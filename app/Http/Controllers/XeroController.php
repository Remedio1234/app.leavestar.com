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
use App\Traits\Xero;
use Illuminate\Support\Facades\DB;

class XeroController extends AppBaseController {

    use Xero;

use MailSetting;

    public function __construct() {
        $this->middleware('auth');
        $this->signatures['rsa_private_key'] = app_path('certs/privatekey.pem');
        $this->signatures['rsa_public_key'] = app_path('certs/publickey.cer');
    }

    //Update the auto leave message
    public function UpdateAutoReply($leave_id) {

        $leaveapp = \App\Models\LeaveApplication::find($leave_id);
        $org_user = \App\Models\OrganisationUser::where([
                    'org_str_id' => $leaveapp->org_id,
                    'user_id' => $leaveapp->user_id
                ])->first();
        $status = $leaveapp->status;
        if ((isset($org_user->email_provider)) && ($org_user->email_provider == 'gmail') && ($leaveapp->autoreplysetting)) {
            $this->postVacationGmail($leaveapp, $org_user, $status);
        }
        if ((isset($org_user->email_provider)) && ($org_user->email_provider == 'outlook') && ($leaveapp->autoreplysetting)) {
            $this->postVacationOutlook($leaveapp, $org_user, $status);
        }
    }

    // Test the token is vaild or not
    // Return content if vaild
    // Return false if invaild
    public function tryConnection($org_id) {
        $token = $this->getTokenFromDb($org_id);
        if (isset($token)) {
            $XeroOAuth = $this->getXeroElement($token->token, $token->secret_token);
            $response = $this->getLeaveType($XeroOAuth);
            return $response;
        }
        return false;
    }

    //CallBack function from Xero
    //Redirect to Synchronize function after save the new token in DB
    public function CallBack(Request $request) {

        if (isset($_REQUEST ['oauth_verifier'])) {
            //$XeroOAuth = $this->getXeroElement($_SESSION ['oauth'] ['oauth_token'], $_SESSION ['oauth'] ['oauth_token_secret']);//jdn
            $XeroOAuth = $this->getXeroElement($request->session()->get('oauth.oauth_token'), $request->session()->get('oauth.oauth_token_secret'));
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
                            'accsoft_id' => 1
                        ])->first();
                $token->xero_org_name = $org->Organisations[0]->Name;
                $token->save();

                return redirect()->action('XeroController@SynchronizeLT', ['org_id' => $org_id]);
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
        $findToken = \App\Models\AccountingToken::where(['org_str_id' => $org_id, 'accsoft_id' => 2])->first();
        if (isset($findToken)) {
            Flash::error('This organisation has already connected with Xero USA before.');
            return redirect('home');
        }
        //IF not Root org level , return 403 forbidden
        if (\App\Models\OrganisationStructure::isOrgRoot($org_id) && isset($org_id)) {
            $try = $this->tryConnection($org_id);
            if (!$try) {
                $callback = url('/') . '/xero/callback';

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
                    $scope = 'payroll.employees,payroll.payitems,payroll.leaveapplications,payroll.payrollcalendars';
                    //$_SESSION ['oauth'] = $XeroOAuth->extract_params($XeroOAuth->response ['response']);
                    $request->session()->put('oauth', $XeroOAuth->extract_params($XeroOAuth->response ['response']));//jdn
                    //$authurl = $XeroOAuth->url("Authorize", '') . "?oauth_token={$_SESSION ['oauth']['oauth_token']}&scope=" . $scope;
                    $authurl = $XeroOAuth->url("Authorize", '') . "?oauth_token={$request->session()->get('oauth.oauth_token')}&scope=" . $scope;//jdn
                    return redirect($authurl);
                }
            } else {
                return redirect()->action('XeroController@SynchronizeLT', ['org_id' => $org_id]);
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
                'xero_id' => $item->LeaveTypeID
                    ], [
                'name' => $item->Name,
                'description' => 'TypeOfUnits: ' . $item->TypeOfUnits,
                'ispaidleave' => (($item->IsPaidLeave == true) ? 0 : 1),
                'isshowonpayslip' => (($item->ShowOnPayslip == true) ? 0 : 1)
            ]);
        }
    }

    //Add the left over Leave Type in DB to Xero
    public function AddNewLeavetypesToXero($leavetypes, $org_id) {
        $token = $this->getTokenFromDb($org_id);
        $XeroOAuth = $this->getXeroElement($token->token, $token->secret_token);
        $params = [];
        foreach ($leavetypes as $item) {
            array_push($params, array(
                'Name' => $item['name'],
                'TypeOfUnits' => 'Hours',
                'IsPaidLeave' => ($item['ispaidleave'] == 0) ? "true" : "false",
                'ShowOnPayslip' => "true"
            ));
        }
        $Xero = $this->getXeroElement($token->token, $token->secret_token);
        $leavetypesXero = $this->getLeaveType($Xero);
        foreach ($leavetypesXero as $item) {
            $array = [
                'LeaveTypeID' => $item->LeaveTypeID,
                'Name' => $item->Name,
                'TypeOfUnits' => $item->TypeOfUnits,
                //'NormalEntitlement' => $item->NormalEntitlement,
                'IsPaidLeave' => ($item->IsPaidLeave == true) ? "true" : "false",
                'ShowOnPayslip' => ($item->ShowOnPayslip == true) ? "true" : "false",
            ];
            if (isset($item->LeaveLoadingRate)) {
                $array['LeaveLoadingRate'] = $item->LeaveLoadingRate;
            }
            if (isset($item->NormalEntitlement)) {
                $array['NormalEntitlement'] = $item->NormalEntitlement;
            }
            array_push($params, $array);
        }

        $this->setLeaveType($XeroOAuth, $params, $org_id);
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
            $leavetype = \App\Models\LeaveType::where(['org_id' => $root_org, 'xero_id' => $balance->LeaveTypeID])->first();
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

    //Add the left over User in DB to Xero
    public function AddNewUsersToXero($users, $org_id) {
        $token = $this->getTokenFromDb($org_id);
        $XeroOAuth = $this->getXeroElement($token->token, $token->secret_token);
        $params = [];
        $earningrate = \App\Models\AccountingToken::where('org_str_id', $org_id)->first()->earingrate_id;
        $calendar = \App\Models\AccountingToken::where('org_str_id', $org_id)->first()->calendar_id;
        foreach ($users as $item) {
            if ((!isset($item['skipsychronize'])) || ($item['skipsychronize'] == 'false')) {
                $name = explode(" ", \App\User::find($item['user_id'])->name);
                $date = date_create($item['birthday']);
                array_push($params, array(
                    'FirstName' => $name[0],
                    'LastName' => array_pop($name),
                    'DateOfBirth' => date_format($date, 'Y-m-d'),
                    'HomeAddress' => array(
                        'AddressLine1' => 'Street Address 1',
                        'City' => 'Brisbane',
                        'Region' => 'QLD',
                        'PostalCode' => '1234'
                    ),
                    'Email' => \App\User::find($item['user_id'])->email,
                    'IsAuthorisedToApproveLeave' => true,
                    'Status' => 'ACTIVE',
                    'PayrollCalendarID' => $calendar,
                    'OrdinaryEarningsRateID' => $earningrate,
                ));
            }
        }

        $this->setUser($XeroOAuth, $params, $org_id);
    }

    //Do Auto Matching based on the name of leave type
    public function AutoMatchingLeavetype($org_id, $leavetypeXero) {
        foreach ($leavetypeXero as $item) {
            $search = \App\Models\LeaveType::where([
                        'name' => $item->Name,
                        'xero_id' => null,
                        'org_id' => $org_id
                    ])->first();
            if (isset($search)) {
                $search->xero_id = $item->LeaveTypeID;
                $search->description = 'TypeOfUnits: ' . $item->TypeOfUnits;
                $search->ispaidleave = (($item->IsPaidLeave == true) ? 0 : 1);
                $search->isshowonpayslip = (($item->ShowOnPayslip == true) ? 0 : 1);
                $search->save();
            }
        }
    }

    //Save the Xero leave type into DB
    public function MatchXeroLeavetypeToDB($xerolt, $id) {
        $dblt = \App\Models\LeaveType::find($id);
        $dblt->name = $xerolt->Name;
        $dblt->xero_id = $xerolt->LeaveTypeID;
        $dblt->description = 'TypeOfUnits: ' . $xerolt->TypeOfUnits;
        $dblt->ispaidleave = (($xerolt->IsPaidLeave == true) ? 0 : 1);
        $dblt->isshowonpayslip = (($xerolt->ShowOnPayslip == true) ? 0 : 1);
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
            //$_SESSION['iscomplete'] = 0;
            $request->session()->put('iscomplete',0); //jdn
            $token = $this->getTokenFromDb($org_id);
            $Xero = $this->getXeroElement($token->token, $token->secret_token);
            $calendar_id = $this->getCalendarID($Xero);
            $earning_rate_id = $this->getEaringRates($Xero);
            if (!($calendar_id && $earning_rate_id)) {
                Flash::error('Payroll Calendar or Ordinary Earnings Rate setting hasn`t been set correctly in Xero');
                return redirect('home');
            }
            $acc_token = \App\Models\AccountingToken::where('org_str_id', $org_id)->first();
            $acc_token->calendar_id = $calendar_id;
            $acc_token->earingrate_id = $earning_rate_id;
            $acc_token->save();

            $leavetypesXero = $this->getLeaveType($Xero);
            //If token expired, force to home page with error message
            if (!$leavetypesXero) {
                Flash::error('Xero Connect Expired.');
                return redirect('home');
            }
            //Do the Auto Matching first before anything else
            $this->AutoMatchingLeavetype($org_id, array_values($leavetypesXero));
            foreach ($leavetypesXero as $key => $item) {
                $search = \App\Models\LeaveType::where(['org_id' => $org_id, 'xero_id' => $item->LeaveTypeID])->first();
                if (isset($search)) {
                    unset($leavetypesXero[$key]);
                }
            }
            //$_SESSION['XeroLeaveTypes'] = array_values($leavetypesXero);
            $request->session()->put('XeroLeaveTypes',array_values($leavetypesXero)); //jdn
            if ($this->CheckDbRecord($org_id, 'leavetype')) {
                //If xero item all used up, upload everything left to Xero from DB , then proceed to User Matching
                //if (sizeof($_SESSION['XeroLeaveTypes']) == 0) {
                if (sizeof($request->session()->get('XeroLeaveTypes')) == 0) { //jdn
                    $leaveinDB = \App\Models\LeaveType::where(['org_id' => $org_id, 'xero_id' => null])->get();
                    $this->AddNewLeavetypesToXero($leaveinDB, $org_id);
                    return redirect()->action('XeroController@SynchronizeUser', ['org_id' => $org_id, 'view' => 'full']);
                } else {
                    $leavetypesDB = \App\Models\LeaveType::where(['org_id' => $org_id, 'xero_id' => null])->get()->toArray();
                    //$_SESSION['DBLeaveTypes'] = $leavetypesDB;
                    $request->session()->put('DBLeaveTypes',$leavetypesDB); //jdn
                    //$_SESSION['step'] = 0;
                    $request->session()->put('step',0); //jdn
                    //return view('xero.index')->with('type', 'leavetype');
                    return view('xero.index')->with(['type'=>'leavetype','XeroLeaveTypes'=>$request->session()->get('XeroLeaveTypes'),'iscomplete'=>$request->session()->get('iscomplete'),'DBUsers'=>$request->session()->get('DBUsers'),'XeroUsers'=>$request->session()->get('XeroUsers'),'iscomplete'=>$request->session()->get('iscomplete'),'step'=>$request->session()->get('step')]); //jdn
                }
            } else {
                $this->AddNewLeavetypetoDB($leavetypesXero, $org_id);
                return redirect()->action('XeroController@SynchronizeUser', ['org_id' => $org_id, 'view' => 'full']);
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
            //$SelectedLeavetype = $_SESSION['XeroLeaveTypes'][$id];
            $SelectedLeavetype = $request->session()->get('XeroLeaveTypes.'.$id); //jdn
            //$DBLeavetype = $_SESSION['DBLeaveTypes'][$_SESSION['step']];
            $DBLeavetype = $request->session()->get('DBLeaveTypes.'.$request->session()->get('step')); //jdn
            $this->MatchXeroLeavetypeToDB($SelectedLeavetype, $DBLeavetype['id']);

            //unset($_SESSION['XeroLeaveTypes'][$id]);
            $request->session()->forget('XeroLeaveTypes.'.$id); //jdn
            //$_SESSION['XeroLeaveTypes'] = array_values($_SESSION['XeroLeaveTypes']);
            $request->session()->put('XeroLeaveTypes',array_values($request->session()->get('XeroLeaveTypes'))); //jdn
            //unset($_SESSION['DBLeaveTypes'][$_SESSION['step']]);
            $request->session()->forget('DBLeaveTypes.'.$request->session()->get('step')); //jdn

            //$_SESSION['DBLeaveTypes'] = array_values($_SESSION['DBLeaveTypes']);
            $request->session()->put('DBLeaveTypes',array_values($request->session()->get('DBLeaveTypes'))); //jdn
            //Proceed to the next one
            //if ((sizeof($_SESSION['XeroLeaveTypes']) > 0) && (sizeof($_SESSION['DBLeaveTypes']) > 0)) {
            if ((sizeof($request->session()->get('XeroLeaveTypes')) > 0) && (sizeof($request->session()->get('DBLeaveTypes')) > 0)) { //jdn
                //$_SESSION['step'] = 0;
                $request->session()->put('step',0); //jdn
                return view('xero.index-partical')->with('type', 'leavetype');
            }

            //If xero item all used up, upload everything left to Xero from DB , then proceed to User Matching
            if (sizeof($request->session()->get('XeroLeaveTypes')) == 0) {
                $this->AddNewLeavetypesToXero($request->session()->get('DBLeaveTypes'), $org_id);
                return redirect()->action('XeroController@SynchronizeUser', ['org_id' => $org_id, 'view' => 'partical']);
            }

            //If DB item all used up, download everything left to Xero to DB , then proceed to User Matching
            if (sizeof($request->session()->get('DBLeaveTypes')) == 0) {
                //$this->AddNewLeavetypetoDB($_SESSION['XeroLeaveTypes'], $org_id);
                $this->AddNewLeavetypetoDB($request->session()->get('XeroLeaveTypes'), $org_id); //jdn
                return redirect()->action('XeroController@SynchronizeUser', ['org_id' => $org_id, 'view' => 'partical']);
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
            //$this->AddNewLeavetypetoDB($_SESSION['XeroLeaveTypes'], $org_id);
            $this->AddNewLeavetypetoDB($request->session()->get('XeroLeaveTypes'), $org_id); //jdn
            //$this->AddNewLeavetypesToXero($_SESSION['DBLeaveTypes'], $org_id);
            $this->AddNewLeavetypesToXero($request->session()->get('DBLeaveTypes'), $org_id); //jdn
            //unset($_SESSION['XeroLeaveTypes']); 
            $request->session()->forget('XeroLeaveTypes'); //jdn
            //unset($_SESSION['DBLeaveTypes']);
            $request->session()->forget('DBLeaveTypes'); //jdn
            return redirect()->action('XeroController@SynchronizeUser', ['org_id' => $org_id, 'view' => 'full']);
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
            //$_SESSION['iscomplete'] = 0;
            $request->session()->put('iscomplete', 0); //jdn
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

            //$_SESSION['XeroUsers'] = $array['Employee'];
            //echo 'probs';
            $request->session()->put('XeroUsers', $array['Employee']); //jdn

            if ($this->CheckDbRecord($org_id, 'users')) {
                //Search for all the child tree
                //If xero item all used up, upload everything left to Xero from DB , then proceed to User Matching
                //if (sizeof($_SESSION['XeroUsers']) == 0) {
                if (sizeof($request->session()->get('XeroUsers')) == 0) { //jdn
                    $tree = $this->getChildTree($org_id);
                    $UserinDB = \App\Models\OrganisationUser::where(['xero_id' => ''])->whereIn('org_str_id', $tree)->get()->toArray();
                    $this->AddNewUsersToXero($UserinDB, $org_id);
                    $this->SynchronizeLeaveApp($org_id);
                    Flash::success('Synchronize Complete.');
                    return redirect('home');
                } else {
                    $usersDB = \App\Models\OrganisationUser::where(['xero_id' => ''])->whereIn('org_str_id', $tree)->get()->toArray();

                    //$_SESSION['DBUsers'] = $usersDB;
                    $request->session()->put('DBUsers',$usersDB); //jdn
                    //$_SESSION['step'] = 0;
                    $request->session()->put('step',0); //jdn
                    if ($request['view'] == "full") {
                        //return view('xero.index')->with('type', 'users');
                        return view('xero.index')->with(['type'=>'users','XeroLeaveTypes'=>$request->session()->get('XeroLeaveTypes'),'iscomplete'=>$request->session()->get('iscomplete'),'DBUsers'=>$request->session()->get('DBUsers'),'XeroUsers'=>$request->session()->get('XeroUsers'),'iscomplete'=>$request->session()->get('iscomplete'),'step'=>$request->session()->get('step')]); //jdn
                    } else {
                        return view('xero.index-partical')->with('type', 'users');
                    }
                }
            } else {

                //Go to Page2 
                if ($request['view'] == "full") {
                    $this->SynchronizeLeaveApp($org_id);
                    return view('xero.invitation')->with(['org_id' => $org_id, 'tree' => $tree]);
                } else {
                    $this->SynchronizeLeaveApp($org_id);
                    return view('xero.invitation-partical')->with(['org_id' => $org_id, 'tree' => $tree]);
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
            
            //$SelectedUser = $_SESSION['XeroUsers'][$id];
            $SelectedUser = $request->session()->get('XeroUsers.id'); //jdn
            //echo '>>>'; print_r($request->session()->get('XeroUsers.id'));
            //$DBUser = $_SESSION['DBUsers'][$_SESSION['step']];
            $DBUser = $request->session()->get('DBUsers.'.$request->session()->get('step'));
            
            $this->MatchXeroUserToDB($SelectedUser, $DBUser['id']);

            //unset($_SESSION['XeroUsers'][$id]);
            $request->session()->forget('XeroUsers.'.$id); //jdn
            //$_SESSION['XeroUsers'] = array_values($_SESSION['XeroUsers']);
            $request->session()->put('XeroUsers', array_values($request->session()->get('XeroUsers'))); //jdn
            //unset($_SESSION['DBUsers'][$_SESSION['step']]);
            $request->session()->forget('DBUsers.'.$request->session()->get('step')); //jdn
            //$_SESSION['DBUsers'] = array_values($_SESSION['DBUsers']);
            $request->session()->put('DBUsers', array_values($request->session()->get('DBUsers'))); //jdn 
            
            //Proceed to the next one
            //if ((sizeof($_SESSION['XeroUsers']) > 0) && (sizeof($_SESSION['DBUsers']) > 0)) {
            if ((sizeof($request->session()->get('XeroUsers')) > 0) && (sizeof($request->session()->get('DBUsers')) > 0)) { //jdn
                //$_SESSION['step'] = 0;
                $request->session()->put('step',0);//jdn
                //return view('xero.index')->with('type', 'users');
                return view('xero.index')->with(['type'=>'users','XeroLeaveTypes'=>$request->session()->get('XeroLeaveTypes'),'iscomplete'=>$request->session()->get('iscomplete'),'DBUsers'=>$request->session()->get('DBUsers'),'XeroUsers'=>$request->session()->get('XeroUsers'),'iscomplete'=>$request->session()->get('iscomplete'),'step'=>$request->session()->get('step')]); //jdn
            }

            //If xero item all used up, upload everything left to Xero from DB , then proceed to User Matching
            if (sizeof($request->session()->get('XeroUsers')) == 0) {
                //$this->AddNewUsersToXero($_SESSION['DBUsers'], $org_id);
                $this->AddNewUsersToXero($request->session()->get('DBUsers'), $org_id); //jdn
                $this->SynchronizeLeaveApp($org_id);
                Flash::success('Synchronize Complete.');
                //return redirect('home');
            }

            //If DB item all used up, download everything left to Xero to DB , then proceed to User Matching
            //if (sizeof($_SESSION['DBUsers']) == 0) {
            if (sizeof($request->session()->get('DBUsers')) == 0) { //jdn
                $tree = $this->getChildTree($org_id);
                $this->SynchronizeLeaveApp($org_id);
                return view('xero.invitation')->with(['org_id' => $org_id, 'tree' => $tree]);
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
        //if ((\App\Models\OrganisationStructure::findRootOrg($org_id) == $org_id) && (isset($_SESSION['DBUsers']))) {
        if ((\App\Models\OrganisationStructure::findRootOrg($org_id) == $org_id) && ($request->session()->get('DBUsers') !== null)) { //jdn            
            $tree = $this->getChildTree($org_id);
            //$this->AddNewUsersToXero($_SESSION['DBUsers'], $org_id);
            $this->AddNewUsersToXero($request->session()->get('DBUsers'), $org_id); //jdn
            //unset($_SESSION['DBUsers']);
            $request->session()->forget('DBUsers'); //jdn
            $this->SynchronizeLeaveApp($org_id);
            return view('xero.invitation')->with(['org_id' => $org_id, 'tree' => $tree]);
        } else {
            return view('errors.403');
        }
    }

    //Receive Ajax call from frontend
    //Output the next item in DB to frontend
    public function SkipOne(Request $request) {
        if (\Request::ajax()) {
            $from = $request['from'];
            $skipsychronize = $request['skipsychronize'];

            if ($from == 'leavetype') {
                //$length = sizeof($_SESSION['DBLeaveTypes']);
                $length = sizeof($request->session()->get('DBLeaveTypes')); //jdn
            } else {
                //$_SESSION['DBUsers'][$_SESSION['step']]['skipsychronize'] = $skipsychronize;
                $request->session()->put('DBUsers.'.$request->session()->get('step').'.skipsychronize', $skipsychronize); //jdn
                //$length = sizeof($_SESSION['DBUsers']);
                $length = sizeof($request->session()->get('DBUsers'));
            }
            /*if (($_SESSION['step'] + 1) >= $length) {
                $_SESSION['step'] = 0;
                $_SESSION['iscomplete'] = 1;
            } else {
                $_SESSION['step'] = $_SESSION['step'] + 1;
            }*/
            //jdn
            if (($request->session()->get('step') + 1) >= $length) {
                $request->session()->put('step',0);
                $request->session()->put('iscomplete',1);
            } else {
                $request->session()->put('step',$request->session()->get('step') + 1);
            }

            return view('xero.index-partical')->with('type', $from);
        } else {
            return view('errors.403');
        }
    }

    //Invite procedure 
    public function Invite(Request $request) {

        $org_id = \Session::get('XeroOrg');
        if (\App\Models\OrganisationStructure::findRootOrg($org_id) == $org_id) {
            //$this->SynchronizeLeaveApp($org_id);
            if (isset($request['users']) && ($request['users'] != null)) {
                foreach ($request['users'] as $item) {
                    //$users = $_SESSION['XeroUsers'][$item];
                    $users = $request->session()->get('XeroUsers.'.$item); //jdn
                    $selected_org = $request['org'][$item];
                    $is_admin = $request['role'][$item];
                    $this->AddNewUsertoDB($users, $selected_org, $is_admin);
                }
            }
            Flash::success('Synchronize Complete.');
            //return redirect('home');
        } else {
            return view('errors.403');
        }
    }

    //Final step to synchronize the leave application
    public function SynchronizeLeaveApp($org_id) {
        $XeroOrg = \Session::get('XeroOrg');

        if (\App\Models\OrganisationStructure::findRootOrg($org_id) == $XeroOrg) {
            $tree = $this->getChildTree($org_id);
//            $vaild_leaveapp = \App\Models\LeaveApplication::where([
//                        'status' => '1',
//                        'xero_id' => 0
//                    ])->whereIn('org_id', $tree)->get();
            
            $vaild_leaveapp = \App\Models\LeaveApplication::where('xero_id', '0')->Orwhere('xero_id', null)->where('status', '1')->whereIn('org_id', $tree)->get();
            $token = $this->getTokenFromDb($org_id);
            $XeroOAuth = $this->getXeroElement($token->token, $token->secret_token);

            //Create new application from Db to Xero
            foreach ($vaild_leaveapp as $item) {
                $params = [];
                $user_id = $item->user_id;
                $org_id = $item->org_id;
                $xero_id = $item->xero_id;
                $org_user = \App\Models\OrganisationUser::where(['org_str_id' => $org_id, 'user_id' => $user_id])->first();
                $comment = \App\Models\Comment::where('leave_id', $item->id)->first();
                if (isset($comment)) {
                    $title = $comment->content;
                } else {
                    $userName = \App\User::find($user_id)->name;
                    $leaveName = \App\Models\LeaveType::find($item->leave_type_id)->name;
                    $title = $userName . '`s ' . $leaveName;
                }

                $variable = array(
                    'EmployeeID' => $org_user->xero_id,
                    'LeaveTypeID' => \App\Models\LeaveType::find($item->leave_type_id)->xero_id,
                    'Title' => $title,
                    'StartDate' => date('Y-m-d', strtotime(\App\Models\Setting::getLocalTime($XeroOrg, $item->start_date, false))),
                    'EndDate' => date('Y-m-d', strtotime(\App\Models\Setting::getLocalTime($XeroOrg, $item->end_date, false))),
                );
                if (isset($xero_id) && ($xero_id != null) && ($xero_id != '0')) {
                    $variable['LeaveApplicationID'] = $xero_id;
                }
                array_push($params, $variable);
                $xml = \App\Models\Serializer::toxml($params, "LeaveApplications", array("LeaveApplication"));
                $return_id = $this->setLeaveApplication($XeroOAuth, $xml);

                $item->xero_id = $return_id;
                $item->save();
            }
            //Check if there are any delete leave on Xero 
            $leaves = $this->getLeaves($XeroOAuth);
            if (!$leaves) {
                Flash::error('Xero Connect Expired.');
                return redirect('home');
            }
            $array = [];
            foreach ($leaves->LeaveApplication as $item) {
                $array[] = (string) $item->LeaveApplicationID;
            }
            $find_leaves = \App\Models\LeaveApplication::where(['status' => '1'])->where('xero_id', '!=', '0')->whereNotNull('xero_id')->whereNotIn('xero_id', $array)->whereIn('org_id', $tree)->get();
            foreach ($find_leaves as $item) {
                $item->status = '2';
                $item->xero_id = null;
                //$this->UpdateAutoReply($item->id);
                $item->save();

                $this->addBackCapacity($item);
            }
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
        if (isset($capacity)) {
            $capacity->capacity = $capacity->capacity + \App\Models\LeaveApplication::CalLeaveUnit($leaveapp->org_id, $leaveapp->start_date, $leaveapp->end_date);
            $capacity->save();
        }
    }

    public function index(Request $request) {
        $check = false;
        $org_id = $request['org_id'];
        $current_org = $request->session()->get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $current_org);
        if (($org_id == $current_org) && (\App\Models\OrganisationStructure::isOrgRoot($org_id))) {
            $check = true;
        }
        if (($org_id != $current_org) && (\App\Models\OrganisationStructure::find($org_id)->parent_id == $realBoss)) {
            $check = true;
        }

        if ($check) {
            \Session::set('XeroOrg', $org_id);
            $orgnazation = \App\Models\OrganisationStructure::find($org_id);
            return view('xero.connection')->with(['view' => 'xeroconnection', 'organisationStructure' => $orgnazation]);
        } else {
            return view('errors.403');
        }
    }

    public function Disconnect(Request $request) {
        $token_id = $request['id'];
        $acctoken = \App\Models\AccountingToken::where([
                    'id' => $token_id,
                ])->first();

        $token_org = $acctoken->org_str_id;

        if (isset($acctoken)) {
            $orgnazation = \App\Models\OrganisationStructure::find($token_org);
            $acctoken->delete();
            return view('xero.connection')->with(['view' => 'xeroconnection', 'organisationStructure' => $orgnazation]);
        } else {
            return view('errors.403');
        }
    }
    public function test(Request $request){
        return view('xero.index')->with(['type'=>'users','XeroLeaveTypes'=>$request->session()->get('XeroLeaveTypes'),'iscomplete'=>$request->session()->get('iscomplete'),'DBUsers'=>$request->session()->get('DBUsers'),'XeroUsers'=>$request->session()->get('XeroUsers'),'iscomplete'=>$request->session()->get('iscomplete'),'step'=>$request->session()->get('step')]); //jdn
    }
    

}
