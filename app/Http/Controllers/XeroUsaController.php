<?php

namespace App\Http\Controllers;

require app_path() . '/lib/XeroOAuth.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Invitation;
use Response;
use App\Http\Controllers\AppBaseController;
use App\Traits\MailSetting;
use App\Traits\XeroUSA;

//Xero Usa limitation:
//1st do not sychronize leave application
//2nd only get from xero , but not post to xero


class XeroUsaController extends AppBaseController {

    use XeroUSA;

    public function __construct() {
        $this->middleware('auth');
        $this->signatures['rsa_private_key'] = app_path('certs/privatekey.pem');
        $this->signatures['rsa_public_key'] = app_path('certs/publickey.cer');
    }

    // Test the token is vaild or not
    // Return content if vaild
    // Return false if invaild
    public function tryConnection($org_id) {
        $token = $this->getTokenFromDb($org_id);
        if (isset($token)) {
            $XeroOAuth = $this->getXeroElement($token->token, $token->secret_token);
            $response = $this->getTimeoffType($XeroOAuth);
            return $response;
        }
        return false;
    }

    //CallBack function from Xero
    //Redirect to Synchronize function after save the new token in DB
    public function CallBack(Request $request) {

        if (isset($_REQUEST ['oauth_verifier'])) {
            $XeroOAuth = $this->getXeroElement($_SESSION ['oauth'] ['oauth_token'], $_SESSION ['oauth'] ['oauth_token_secret']);
            $code = $XeroOAuth->request('GET', $XeroOAuth->url('AccessToken', ''), array(
                'oauth_verifier' => $_REQUEST ['oauth_verifier'],
                'oauth_token' => $_REQUEST ['oauth_token']
            ));
            if ($XeroOAuth->response ['code'] == 200) {
                $response = $XeroOAuth->extract_params($XeroOAuth->response ['response']);
                $org_id = \Session::get('XeroOrg');
                $new_token = $this->saveTokenToDb($response, $org_id);

                //save the org name on Xero
                $XeroOAuth = $this->getXeroElement($response['oauth_token'], $response['oauth_token_secret']);
                $response = $XeroOAuth->request('GET', $XeroOAuth->url('Organisation', 'core'), array(), $xml = '', $format = "json");
                $org = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
                $token = \App\Models\AccountingToken::where([
                            'org_str_id' => $org_id,
                            'accsoft_id' => 2
                        ])->first();
                $token->xero_org_name = $org->Organisations[0]->Name;
                $token->save();

                return redirect()->action('XeroUsaController@SynchronizeLT', ['org_id' => $org_id]);
            }
        } else {
            return view('errors.403');
        }
    }

    //Root for Xero Synchronize
    //Check if token in DB or token vaild
    //Redirect to Xero if need new token
    //Redirect to Synchronize if token works
    public function XeroConntect(Request $request) {

        $org_id = \Session::get('XeroOrg');
        //IF find other token , throw errors
        $findToken = \App\Models\AccountingToken::where(['org_str_id' => $org_id, 'accsoft_id' => 1])->first();
        if (isset($findToken)) {
            Flash::error('This organisation has already connected with Xero AU before.');
            return redirect('home');
        }

        //IF not Root org level , return 403 forbidden
        if (\App\Models\OrganisationStructure::isOrgRoot($org_id) && isset($org_id)) {
            $try = $this->tryConnection($org_id);
            if (!$try) {
                $callback = url('/') . '/xerousa/callback';

                $XeroOAuth = new \XeroOAuth(array_merge(array(
                            'application_type' => $this->application_type,
                            'oauth_callback' => $callback,
                            'user_agent' => $this->useragent
                                ), $this->signatures));
                $params = array(
                    'oauth_callback' => $callback
                );
                $response = $XeroOAuth->request('GET', $XeroOAuth->url('RequestToken', ''), $params);
                if ($XeroOAuth->response ['code'] == 200) {
                    $scope = 'payroll.employees,payroll.payitems';
                    $_SESSION ['oauth'] = $XeroOAuth->extract_params($XeroOAuth->response ['response']);
                    $authurl = $XeroOAuth->url("Authorize", '') . "?oauth_token={$_SESSION ['oauth']['oauth_token']}&scope=" . $scope;
                    return redirect($authurl);
                }
            } else {
                return redirect()->action('XeroUsaController@SynchronizeLT', ['org_id' => $org_id]);
            }
        } else {
            return view('errors.403');
        }
    }

    //Get the Top Tree Node , and the children tree structure
    private function traverse($categories, &$tree, $level) {

        foreach ($categories as $category) {
            array_push($tree, array(
                'id' => $category->id,
                'name' => $category->name,
                'account_id' => $category->account_id,
                'setting_id' => $category->setting_id,
                'children' => [],
            ));
            if ($category->children->count() > 0) {
                $this->traverse($category->children, $tree[count($tree) - 1]['children'], $level + 1);
            }
        }
    }

    //Check if there are unsynchronized record in DB ,which Xero hasn't
    //return true if need synchronized
    public function CheckDbRecord($org_id, $type) {
        if ($type == 'leavetype') {
            $leavetypes = \App\Models\LeaveType::where(['org_id' => $org_id, 'xero_id' => null])->first();
            return (isset($leavetypes)) ? true : false;
        } else {
            $tree = $this->getChildTree($org_id);
            $users = \App\Models\OrganisationUser::where(['xero_id' => ''])->whereIn('org_str_id', $tree)->first();
            return (isset($users)) ? true : false;
        }
    }

    //Create or modify the leave type from Xero to DB
    public function AddNewLeavetypetoDB($leavetypes, $org_id) {
        foreach ($leavetypes as $item) {
            \App\Models\LeaveType::updateOrCreate([
                'org_id' => $org_id,
                'xero_id' => $item->TimeOffTypeID
                    ], [
                'name' => $item->TimeOffType,
                'description' => 'TypeOfUnits:  undefined',
                'ispaidleave' => (($item->TimeOffCategory == 'PAID') ? 0 : 1),
                'isshowonpayslip' => (($item->ShowBalanceToEmployee == true) ? 0 : 1)
            ]);
        }
    }

    //Generate Secure Token
    public function generateToken($length = "32") {
        return \App\Models\SecureToken::getToken($length);
    }

    //Add new user invitation to DB
    public function AddNewUsertoDB($item, $org_id, $id_admin) {
        $XeroOrg = \Session::get('XeroOrg');
        $user_register = \App\Models\UserRegister::updateOrCreate([
                    'org_id' => $org_id,
                    'xero_id' => $item['EmployeeID']
                        ], [
                    'name' => $item['FirstName'] . " " . $item['LastName'],
                    'is_admin' => $id_admin,
                    'email' => (isset($item['Email'])) ? $item['Email'] : "",
                    'phone' => (isset($item['Phone'])) ? $item['Phone'] : "",
                    'birthday' => $item['DateOfBirth'],
                    'token' => $this->generateToken(),
        ]);
        $token = $this->getTokenFromDb($XeroOrg);
        $XeroOAuth = $this->getXeroElement($token->token, $token->secret_token);
        $employee_balance = $this->getSingleEmployee($XeroOAuth, $item['EmployeeID']);
        $root_org = \App\Models\OrganisationStructure::findRootOrg($org_id);

        foreach ($employee_balance as $balance) {

            $leavetype = \App\Models\LeaveType::where(['org_id' => $root_org, 'xero_id' => $balance->TimeOffTypeId])->first();
            if (isset($leavetype)) {
                $register_capacity = \App\Models\RegisterCapacity::create([
                            'register_id' => $user_register->id,
                            'leave_type_id' => $leavetype->id,
                            'capacity' => round(($balance->NumberOfUnits) * 3600)
                ]);
            }
        }

        //Send Email to user
        $current_org = \Session::get('current_org');
        $currentUser = \App\Models\OrganisationUser::where(['org_str_id' => $current_org, 'user_id' => \Auth::user()->id])->first();
        Mail::to($item['Email'])->send(new Invitation($user_register, $currentUser));
    }

    //Do Auto Matching based on the name of leave type
    public function AutoMatchingLeavetype($org_id, $leavetypeXero) {
        foreach ($leavetypeXero as $item) {
            $search = \App\Models\LeaveType::where([
                        'name' => $item->TimeOffType,
                        'xero_id' => null,
                        'org_id' => $org_id
                    ])->first();
            if (isset($search)) {
                $search->xero_id = $item->TimeOffTypeID;
                $search->description = 'TypeOfUnits: undefined';
                $search->ispaidleave = (($item->TimeOffCategory == 'PAID') ? 0 : 1);
                $search->isshowonpayslip = (($item->ShowBalanceToEmployee == true) ? 0 : 1);
                $search->save();
            }
        }
    }

    //Save the Xero leave type into DB
    public function MatchXeroLeavetypeToDB($xerolt, $id) {
        $dblt = \App\Models\LeaveType::find($id);
        $dblt->name = $xerolt->TimeOffType;
        $dblt->xero_id = $xerolt->TimeOffTypeID;
        $dblt->description = 'TypeOfUnits: undefined ';
        $dblt->ispaidleave = (($xerolt->TimeOffCategory == 'PAID') ? 0 : 1);
        $dblt->isshowonpayslip = (($xerolt->ShowBalanceToEmployee == true) ? 0 : 1);
        $dblt->save();
    }

    //Save the Xero leave type into DB
    public function MatchXeroUserToDB($xerouser, $id) {

        $user = \App\Models\OrganisationUser::find($id);

        $user->xero_id = $xerouser['EmployeeID'];
        $user->xero_name = $xerouser['FirstName'] . ' ' . $xerouser['LastName'];
        $user->birthday = $xerouser['DateOfBirth'];
        $user->start_working_date = isset($xerouser['StartDate']) ? $xerouser['StartDate'] : null;
        $user->phone = (isset($xerouser['Phone'])) ? $xerouser['Phone'] : "";
        $user->save();
    }

    //Main Synchronize function for leave type
    //If need matching procedure, redirect to matching page
    //If no need to matching , create new leavetype in DB
    public function SynchronizeLT($org_id, Request $request) {
        $XeroOrg = \Session::get('XeroOrg');
        if (\App\Models\OrganisationStructure::findRootOrg($org_id) == $XeroOrg) {
            $_SESSION['iscomplete'] = 0;
            $token = $this->getTokenFromDb($org_id);
            $Xero = $this->getXeroElement($token->token, $token->secret_token);


            $leavetypesXero = $this->getTimeoffType($Xero);
            //If token expired, force to home page with error message
            if (!$leavetypesXero) {
                Flash::error('Xero Connect Expired.');
                return redirect('home');
            }
            //Do the Auto Matching first before anything else
            $this->AutoMatchingLeavetype($org_id, array_values($leavetypesXero));
            foreach ($leavetypesXero as $key => $item) {
                $search = \App\Models\LeaveType::where(['org_id' => $org_id, 'xero_id' => $item->TimeOffTypeID])->first();
                if (isset($search)) {
                    unset($leavetypesXero[$key]);
                }
            }
            $_SESSION['XeroLeaveTypes'] = array_values($leavetypesXero);
            if ($this->CheckDbRecord($org_id, 'leavetype')) {
                //If xero item all used up, upload everything left to Xero from DB , then proceed to User Matching
                if (sizeof($_SESSION['XeroLeaveTypes']) == 0) {

                    return redirect()->action('XeroUsaController@SynchronizeUser', ['org_id' => $org_id, 'view' => 'full']);
                } else {
                    $leavetypesDB = \App\Models\LeaveType::where(['org_id' => $org_id, 'xero_id' => null])->get()->toArray();
                    $_SESSION['DBLeaveTypes'] = $leavetypesDB;
                    $_SESSION['step'] = 0;
                    return view('xero_usa.index')->with('type', 'leavetype');
                }
            } else {
                $this->AddNewLeavetypetoDB($leavetypesXero, $org_id);
                return redirect()->action('XeroUsaController@SynchronizeUser', ['org_id' => $org_id, 'view' => 'full']);
            }
        } else {
            return view('errors.403');
        }
    }

    //Receive Ajax call from frontend
    //Matching the xero item to Db
    //Output the next one
    //If all items matched, save all left over into DB or Xero
    public function MatchingLeaveType(Request $request) {
        if (\Request::ajax()) {
            $id = $request['id'];
            $org_id = \Session::get('XeroOrg');
            $SelectedLeavetype = $_SESSION['XeroLeaveTypes'][$id];
            $DBLeavetype = $_SESSION['DBLeaveTypes'][$_SESSION['step']];
            $this->MatchXeroLeavetypeToDB($SelectedLeavetype, $DBLeavetype['id']);

            unset($_SESSION['XeroLeaveTypes'][$id]);
            $_SESSION['XeroLeaveTypes'] = array_values($_SESSION['XeroLeaveTypes']);
            unset($_SESSION['DBLeaveTypes'][$_SESSION['step']]);
            $_SESSION['DBLeaveTypes'] = array_values($_SESSION['DBLeaveTypes']);

            //Proceed to the next one
            if ((sizeof($_SESSION['XeroLeaveTypes']) > 0) && (sizeof($_SESSION['DBLeaveTypes']) > 0)) {
                $_SESSION['step'] = 0;
                return view('xero_usa.index-partical')->with('type', 'leavetype');
            }

            //If xero item all used up, upload everything left to Xero from DB , then proceed to User Matching
            if (sizeof($_SESSION['XeroLeaveTypes']) == 0) {
                //$this->AddNewLeavetypesToXero($_SESSION['DBLeaveTypes'], $org_id);
                return redirect()->action('XeroUsaController@SynchronizeUser', ['org_id' => $org_id, 'view' => 'partical']);
            }

            //If DB item all used up, download everything left to Xero to DB , then proceed to User Matching
            if (sizeof($_SESSION['DBLeaveTypes']) == 0) {
                $this->AddNewLeavetypetoDB($_SESSION['XeroLeaveTypes'], $org_id);
                return redirect()->action('XeroUsaController@SynchronizeUser', ['org_id' => $org_id, 'view' => 'partical']);
            }
        } else {
            return view('errors.403');
        }
    }

    //If item in DB could find coresponding item in Xero do this Action
    //Upload all the left over item in DB to Xero
    //Download all the left over item in Xero if have any
    public function LTcomplete(Request $request) {

        $org_id = \Session::get('XeroOrg');
        if (\App\Models\OrganisationStructure::findRootOrg($org_id) == $org_id) {
            $this->AddNewLeavetypetoDB($_SESSION['XeroLeaveTypes'], $org_id);
            //$this->AddNewLeavetypesToXero($_SESSION['DBLeaveTypes'], $org_id);
            unset($_SESSION['XeroLeaveTypes']);
            unset($_SESSION['DBLeaveTypes']);
            return redirect()->action('XeroUsaController@SynchronizeUser', ['org_id' => $org_id, 'view' => 'full']);
        } else {
            return view('errors.403');
        }
    }

    //Help Class to get the child tree
    //Including the root itself
    //return the $tree
    public function getChildTree($org_id) {
        $account_id = \App\Models\OrganisationStructure::find($org_id)->account_id;
        $tree = [];
        $nodes = \App\Models\OrganisationStructure::scoped(['account_id', $account_id])->descendantsOf($org_id);
        $this->traverse($nodes, $tree, 1);
        $array = [];
        foreach ($tree as $item) {
            $array[] = $item['id'];
        }
        $array[] = intval($org_id);
        return $array;
    }

    //Main Synchronize function for user
    //If need matching procedure, redirect to matching page
    //If no need to matching , redirect to invitation page
    public function SynchronizeUser($org_id, Request $request) {
        $XeroOrg = \Session::get('XeroOrg');
        if (\App\Models\OrganisationStructure::findRootOrg($org_id) == $XeroOrg) {
            $_SESSION['iscomplete'] = 0;
            $token = $this->getTokenFromDb($org_id);
            $Xero = $this->getXeroElement($token->token, $token->secret_token);
            $UsersXero = $this->getEmployees($Xero);
            //If token expired, force to home page with error message
            if (!$UsersXero) {
                Flash::error('Xero Connect Expired.');
                return redirect('home');
            }

            //Transfer the Simple Xml object into Array
            $json = json_encode($UsersXero);
            $array = json_decode($json, TRUE);

            //get the child tree
            $tree = $this->getChildTree($org_id);

            foreach ($array['Employee'] as $key => $item) {

                $search = \App\Models\OrganisationUser::where('xero_id', $item['EmployeeID'])->whereIn('org_str_id', $tree)->first();
                if (isset($search)) {
                    unset($array['Employee'][$key]);
                }
            }

            $_SESSION['XeroUsers'] = $array['Employee'];

            if ($this->CheckDbRecord($org_id, 'users')) {
                //Search for all the child tree
                //If xero item all used up, upload everything left to Xero from DB , then proceed to User Matching
                if (sizeof($_SESSION['XeroUsers']) == 0) {
                    $tree = $this->getChildTree($org_id);
                    $UserinDB = \App\Models\OrganisationUser::where(['xero_id' => ''])->whereIn('org_str_id', $tree)->get()->toArray();
                    //$this->AddNewUsersToXero($UserinDB, $org_id);
                    //$this->SynchronizeLeaveApp($org_id);
                    Flash::success('Synchronize Complete.');
                    return redirect('home');
                } else {
                    $usersDB = \App\Models\OrganisationUser::where(['xero_id' => ''])->whereIn('org_str_id', $tree)->get()->toArray();

                    $_SESSION['DBUsers'] = $usersDB;
                    $_SESSION['step'] = 0;

                    if ($request['view'] == "full") {
                        return view('xero_usa.index')->with('type', 'users');
                    } else {
                        return view('xero_usa.index-partical')->with('type', 'users');
                    }
                }
            } else {

                //Go to Page2 
                if ($request['view'] == "full") {
                    return view('xero_usa.invitation')->with(['org_id' => $org_id, 'tree' => $tree]);
                } else {
                    return view('xero_usa.invitation-partical')->with(['org_id' => $org_id, 'tree' => $tree]);
                }
            }
        } else {
            return view('errors.403');
        }
    }

    //Receive Ajax call from frontend
    //Matching the xero item to Db
    //Output the next one
    //If all items matched, save all left over into DB or Xero
    public function MatchingUser(Request $request) {
        if (\Request::ajax()) {
            $id = $request['id'];

            $org_id = \Session::get('XeroOrg');
            $SelectedUser = $_SESSION['XeroUsers'][$id];
            $DBUser = $_SESSION['DBUsers'][$_SESSION['step']];

            $this->MatchXeroUserToDB($SelectedUser, $DBUser['id']);

            unset($_SESSION['XeroUsers'][$id]);
            $_SESSION['XeroUsers'] = array_values($_SESSION['XeroUsers']);
            unset($_SESSION['DBUsers'][$_SESSION['step']]);
            $_SESSION['DBUsers'] = array_values($_SESSION['DBUsers']);

            //Proceed to the next one
            if ((sizeof($_SESSION['XeroUsers']) > 0) && (sizeof($_SESSION['DBUsers']) > 0)) {
                $_SESSION['step'] = 0;
                return view('xero_usa.index')->with('type', 'users');
            }

            //If xero item all used up, upload everything left to Xero from DB , then proceed to User Matching
            if (sizeof($_SESSION['XeroUsers']) == 0) {
                //$this->AddNewUsersToXero($_SESSION['DBUsers'], $org_id);
                //$this->SynchronizeLeaveApp($org_id);
                Flash::success('Synchronize Complete.');
                return redirect('home');
            }

            //If DB item all used up, download everything left to Xero to DB , then proceed to User Matching
            if (sizeof($_SESSION['DBUsers']) == 0) {
                $tree = $this->getChildTree($org_id);
                return view('xero_usa.invitation')->with(['org_id' => $org_id, 'tree' => $tree]);
            }
        } else {
            return view('errors.403');
        }
    }

    //If item in DB could find coresponding item in Xero do this Action
    //Upload all the left over item in DB to Xero
    //Download all the left over item in Xero if have any
    public function Usercomplete(Request $request) {

        $org_id = \Session::get('XeroOrg');
        if ((\App\Models\OrganisationStructure::findRootOrg($org_id) == $org_id) && (isset($_SESSION['DBUsers']))) {
            $tree = $this->getChildTree($org_id);
            //$this->AddNewUsersToXero($_SESSION['DBUsers'], $org_id);
            unset($_SESSION['DBUsers']);
            return view('xero_usa.invitation')->with(['org_id' => $org_id, 'tree' => $tree]);
        } else {
            return view('errors.403');
        }
    }

    //Receive Ajax call from frontend
    //Output the next item in DB to frontend
    public function SkipOne(Request $request) {
        if (\Request::ajax()) {
            $from = $request['from'];
            if ($from == 'leavetype') {
                $length = sizeof($_SESSION['DBLeaveTypes']);
            } else {
                $length = sizeof($_SESSION['DBUsers']);
            }
            if (($_SESSION['step'] + 1) >= $length) {
                $_SESSION['step'] = 0;
                $_SESSION['iscomplete'] = 1;
            } else {
                $_SESSION['step'] = $_SESSION['step'] + 1;
            }
            return view('xero_usa.index-partical')->with('type', $from);
        } else {
            return view('errors.403');
        }
    }

    //Invite procedure 
    public function Invite(Request $request) {

        $org_id = \Session::get('XeroOrg');
        if (\App\Models\OrganisationStructure::findRootOrg($org_id) == $org_id) {
            if (isset($request['users']) && ($request['users'] != null)) {
                foreach ($request['users'] as $item) {
                    $users = $_SESSION['XeroUsers'][$item];
                    $selected_org = $request['org'][$item];
                    $is_admin = $request['role'][$item];
                    $this->AddNewUsertoDB($users, $selected_org, $is_admin);
                }
            }
            Flash::success('Synchronize Complete.');
            //return redirect('home');
            //$this->SynchronizeLeaveApp($org_id);
        } else {
            return view('errors.403');
        }
    }

    public function addBackCapacity($leaveapp) {
        $capacity = \App\Models\LeaveCapacity::where([
                    'user_id' => $leaveapp->user_id,
                    'org_id' => $leaveapp->org_id,
                    'leave_type_id' => $leaveapp->leave_type_id
                ])->first();
        $capacity->capacity = $capacity->capacity + \App\Models\LeaveApplication::CalLeaveUnit($leaveapp->org_id, $leaveapp->start_date, $leaveapp->end_date);
        $capacity->save();
    }

}
