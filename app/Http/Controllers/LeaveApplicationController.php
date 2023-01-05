<?php

namespace App\Http\Controllers;

require app_path() . '/lib/XeroOAuth.php';

use App\Http\Requests\CreateLeaveApplicationRequest;
use App\Http\Requests\UpdateLeaveApplicationRequest;
use App\Repositories\LeaveApplicationRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Traits\MailSetting;
use App\Traits\Xero;
use App\Notifications\ParticalDayNotification;

class LeaveApplicationController extends AppBaseController {

    use MailSetting;

use Xero;

    /** @var  LeaveApplicationRepository */
    private $leaveApplicationRepository;
    private $weekly_token = "iaee7svQSWwEIBPyu4jteigVNi7A2nOfYftcTgDgaYfObHo3ya3GXceTOyVH";
    protected $validationRules = [
        'date_range' => 'required',
        'leave_type_id' => 'required',
        'flexible' => 'required',
        'status' => 'required',
    ];

    private function getMessage1() {
        return "If you are linked with Xero, You can only reject the leave application through Xero and do the Synchronize";
    }

    private function getMessage2() {
        return "You can not update appoved leave.";
    }

    public function __construct(LeaveApplicationRepository $leaveApplicationRepo) {
        $this->signatures['rsa_private_key'] = app_path('certs/privatekey.pem');
        $this->signatures['rsa_public_key'] = app_path('certs/publickey.cer');
        $this->middleware('auth', ['except' => ['crudMatchHalfDayLeave']]);
        $this->middleware('accountCheck', ['except' => ['crudMatchHalfDayLeave']]);
        $this->middleware('accountActiveCheck', ['except' => ['crudMatchHalfDayLeave']]);
        $this->leaveApplicationRepository = $leaveApplicationRepo;
    }

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

    private function getChildTree($org_id) {
        $account_id = \App\Models\OrganisationStructure::find($org_id)->account_id;
        $tree = [];
        $nodes = \App\Models\OrganisationStructure::scoped(['account_id', $account_id])->descendantsOf($org_id);
        $this->traverse($nodes, $tree, 1);
        $array[0] = $org_id;
        foreach ($tree as $item) {
            $array[] = $item['id'];
        }
        return $array;
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
            if ($category->children->count() != 0) {
                $this->traverse($category->children, $tree[count($tree) - 1]['children'], $level + 1, $category);
            }
        }
    }

    /* Displaying index for leave application for manager of all the staff           
     * * */

    public function manageLeave(Request $request) {
        $type = $request['queryType'];
        $user_id = \Auth::user()->id;
        $org_id = $request->session()->get('current_org');
        $user_org = \App\Models\OrganisationUser::where([
                    'org_str_id' => $org_id,
                    'user_id' => $user_id
                ])->first();

        if ($user_org->is_admin == 'yes') {
            $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
            $searchResult = \App\Models\OrganisationUser::join('organisation_structure', 'organisation_structure.id', '=', 'organisation_user.org_str_id')
                    ->where('organisation_user.user_id', $user_id)
                    ->where('organisation_user.is_admin', 'yes')
                    ->where('organisation_structure.parent_id', null)
                    ->first();
            $tree = $this->getChildTree($realBoss);
            if (\App\Models\OrganisationStructure::findRootOrg($org_id) == $org_id) {
                if (isset($type)) {
                    switch ($type) {
                        case "pending":
                            //$searchArray1 = ['0'];
                            //$searchArray2 = [];
                            //$one_month_after = \Carbon\Carbon::now()->addMonth(1200);
                            $leaveApplication = \App\Models\LeaveApplication::where('status', 0)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
                            break;
                        case "approved":
//                            $searchArray1 = [];
//                            $searchArray2 = ['1'];
                            // $one_month_after = \Carbon\Carbon::now()->addMonth(1200);
                            $leaveApplicationA = \App\Models\LeaveApplication::where('status', 1)->whereDate('end_date', '>', date('Y-m-d'))->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
                            //$leaveApplicationC = \App\Models\LeaveApplication::whereIn('status', [1])->where('need_alert', 1)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
                            //$leaveApplication = $leaveApplicationA->merge($leaveApplicationC)->sortByDesc('updated_at');
                            $leaveApplication = $leaveApplicationA->sortByDesc('updated_at');
                            break;
                        case "upcoming":
//                            $searchArray1 = [];
//                            $searchArray2 = ['1'];                            
                            $one_month_after = \Carbon\Carbon::now()->addMonth(1);
                            $leaveApplicationA = \App\Models\LeaveApplication::where('status', 1)->whereDate('start_date', '>', date('Y-m-d'))->whereDate('start_date', '<=', $one_month_after)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
                            //$leaveApplicationC = \App\Models\LeaveApplication::whereIn('status', [1])->where('need_alert', 1)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
                            //$leaveApplication = $leaveApplicationA->merge($leaveApplicationC)->sortByDesc('updated_at');
                            $leaveApplication = $leaveApplicationA->sortByDesc('updated_at');
                            break;
                    }
                } else {
//                    $searchArray1 = ['0'];
//                    $searchArray2 = ['1', '2'];
//                    $one_month_after = \Carbon\Carbon::now()->addMonth(1200);
                    $leaveApplication = \App\Models\LeaveApplication::whereIn('org_id', $tree)->whereDate('end_date', '>', date('Y-m-d'))->orderBy('updated_at', 'DESC')->get();
                }
            } else {
                $manages = \App\Models\OrganisationUser::where(['org_str_id' => $org_id, 'is_admin' => 'yes'])->get();
                $admin_list = [];
                foreach ($manages as $item) {
                    $admin_list[] = $item->user_id;
                }
                if (isset($type)) {
                    switch ($type) {
                        case "pending":
                            //$searchArray1 = ['0'];
                            //$searchArray2 = [];
                            //$one_month_after = \Carbon\Carbon::now()->addMonth(1200);
                            $leaveApplication = \App\Models\LeaveApplication::where('status', 0)->whereNotIn('user_id', $admin_list)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
                            break;
                        case "approved":
//                            $searchArray1 = [];
//                            $searchArray2 = ['1'];
                            // $one_month_after = \Carbon\Carbon::now()->addMonth(1200);
                            $leaveApplicationA = \App\Models\LeaveApplication::where('status', 1)->whereNotIn('user_id', $admin_list)->whereDate('end_date', '>', date('Y-m-d'))->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
                            //$leaveApplicationC = \App\Models\LeaveApplication::whereIn('status', [1])->whereNotIn('user_id', $admin_list)->where('need_alert', 1)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
                            //$leaveApplication = $leaveApplicationA->merge($leaveApplicationC)->sortByDesc('updated_at');
                            $leaveApplication = $leaveApplicationA->sortByDesc('updated_at');
                            break;
                        case "upcoming":
//                            $searchArray1 = [];
//                            $searchArray2 = ['1'];                            
                            $one_month_after = \Carbon\Carbon::now()->addMonth(1);
                            $leaveApplicationA = \App\Models\LeaveApplication::where('status', 1)->whereNotIn('user_id', $admin_list)->whereDate('start_date', '>', date('Y-m-d'))->whereDate('start_date', '<=', $one_month_after)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
                            //$leaveApplicationC = \App\Models\LeaveApplication::whereIn('status', [1])->whereNotIn('user_id', $admin_list)->where('need_alert', 1)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
                            //$leaveApplication = $leaveApplicationA->merge($leaveApplicationC)->sortByDesc('updated_at');
                            $leaveApplication = $leaveApplicationA->sortByDesc('updated_at');
                            break;
                    }
                } else {
//                    $searchArray1 = ['0'];
//                    $searchArray2 = ['1', '2'];
//                    $one_month_after = \Carbon\Carbon::now()->addMonth(1200);
                    $leaveApplication = \App\Models\LeaveApplication::whereIn('org_id', $tree)->whereDate('end_date', '>', date('Y-m-d'))->whereNotIn('user_id', $admin_list)->orderBy('updated_at', 'DESC')->get();
                }
            }
//            if (\App\Models\OrganisationStructure::findRootOrg($org_id) == $org_id) {
//
//                $leaveApplicationA = \App\Models\LeaveApplication::whereIn('status', $searchArray1)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
//                $leaveApplicationB = \App\Models\LeaveApplication::whereIn('status', $searchArray2)->whereDate('end_date', '>', date('Y-m-d'))->where('start_date', '<=', $one_month_after)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
//                $leaveApplicationC = \App\Models\LeaveApplication::whereIn('status', [1])->where('need_alert', 1)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
//                $leaveApplication = $leaveApplicationA->merge($leaveApplicationB)->merge($leaveApplicationC)->sortByDesc('updated_at');
//                $leaveHistorys = \App\Models\LeaveApplication::whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
//            } else {
//                $manages = \App\Models\OrganisationUser::where(['org_str_id' => $org_id, 'is_admin' => 'yes'])->get();
//                $admin_list = [];
//                foreach ($manages as $item) {
//                    $admin_list[] = $item->user_id;
//                }
//                $leaveApplicationA = \App\Models\LeaveApplication::whereIn('status', $searchArray1)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
//                $leaveApplicationB = \App\Models\LeaveApplication::whereIn('status', $searchArray2)->whereDate('end_date', '>', date('Y-m-d'))->where('start_date', '<=', $one_month_after)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
//                $leaveApplicationC = \App\Models\LeaveApplication::whereIn('status', [1])->where('need_alert', 1)->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
//                $leaveApplication = $leaveApplicationA->merge($leaveApplicationB)->merge($leaveApplicationC)->sortByDesc('updated_at');
//                $leaveHistorys = \App\Models\LeaveApplication::whereIn('org_id', $tree)->whereNotIn('user_id', $admin_list)->orderBy('updated_at', 'DESC')->get();
//            }
            $leaveHistorys = \App\Models\LeaveApplication::whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();

            return view('leave_applications.management')
                            ->with(['leaveApplications' => $leaveApplication, 'view' => 'manage', 'org_id' => $org_id, 'leaveHistorys' => $leaveHistorys, 'queryType' => $type]);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Display a listing of the LeaveApplication.
     *
     * @param Request $request
     * @return Response
     */
    public function myLeaveApplication(Request $request) {
        $user_id = \Auth::user()->id;
        $org_id = $request->session()->get('current_org');
        $leaveApplication = \App\Models\LeaveApplication::where(['user_id' => $user_id, 'org_id' => $org_id])->orderBy('updated_at', 'DESC')->get();

        return view('leave_applications.myLeaves')
                        ->with(['leaveApplications' => $leaveApplication, 'view' => 'index', 'org_id' => $org_id]);
    }

    public function index(Request $request) {


        $user_id = \Auth::user()->id;
        $org_id = $request->session()->get('current_org');
        $leaveApplication = \App\Models\LeaveApplication::where(['user_id' => $user_id, 'org_id' => $org_id])->orderBy('updated_at', 'DESC')->get();

        return view('leave_applications.index')
                        ->with(['leaveApplications' => $leaveApplication, 'view' => 'index', 'org_id' => $org_id]);
    }

    /**
     * Show the form for creating a new LeaveApplication.
     *
     * @return Response
     */
    public function create(Request $request) {
        if (\Request::ajax()) {
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request->session()->get('current_org');

            $root_org = \App\Models\OrganisationStructure::findRootOrg($org_id);
            $leave_type_list = \App\Models\LeaveType::where('org_id', $root_org)->get();
            $list = [];
            foreach ($leave_type_list as $item) {
                $list[$item->id] = $item->name;
            }

            //Decide to show auto reply setting or not
            $org_user = \App\Models\OrganisationUser::where([
                        'org_str_id' => $org_id,
                        'user_id' => \Auth::user()->id
                    ])->first();
            if (isset($org_user->email_provider) && isset($org_user->refresh_token)) {
                $auto_reply = true;
            } else {
                $auto_reply = false;
            }
            return view('leave_applications.create')->with(['org_id' => $org_id, 'type_list' => $list, 'view' => 'create', 'validator' => $validator, 'auto_reply' => $auto_reply]);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Store a newly created LeaveApplication in storage.
     *
     * @param CreateLeaveApplicationRequest $request
     *
     * @return Response
     */
    public function store(CreateLeaveApplicationRequest $request) {
        if (\Request::ajax()) {
            $input = $request->all();
            $org_id = $request->session()->get('current_org');
            $leaveApplication = $this->leaveApplicationRepository->create($input);

            $message = "Leave Application Save Successfully";

            return redirect(route('leaveApplications.index'));
        } else {
            return view('errors.403');
        }
    }

    /**
     * Display the specified LeaveApplication.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id) {
        $leaveApplication = $this->leaveApplicationRepository->findWithoutFail($id);
//echo '<pre>'; print_r($leaveApplication); echo '</pre>';die;
        if (empty($leaveApplication)) {
            Flash::error('Leave Application not found');

            return redirect(route('leaveApplications.index'));
        }

        return view('leave_applications.show')->with('leaveApplication', $leaveApplication);
    }

    /**
     * Show the form for editing the specified LeaveApplication.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id, Request $request) {
        if (\Request::ajax()) {
            $leaveApplication = $this->leaveApplicationRepository->findWithoutFail($id);

            $validator = \JsValidator::make($this->validationRules);
            //$current_org = $request->session()->get('current_org');
            $org_id = $leaveApplication->org_id;

            $root_org = \App\Models\OrganisationStructure::findRootOrg($org_id);
            $leave_type_list = \App\Models\LeaveType::where('org_id', $root_org)->get();

            foreach ($leave_type_list as $item) {
                $list[$item->id] = $item->name;
            }
            //modify date range

            $dates = \App\Models\Setting::getLocalTime($org_id, $leaveApplication->start_date);
            $dates2 = \App\Models\Setting::getLocalTime($org_id, $leaveApplication->end_date);

            $leaveApplication->date_range = $dates . ' - ' . $dates2;


            //Decide to show auto reply setting or not
            $org_user = \App\Models\OrganisationUser::where([
                        'org_str_id' => $org_id,
                        //'user_id' => \Auth::user()->id
                        'user_id' => $leaveApplication->user_id
                    ])->first();
            if (isset($org_user->email_provider) && isset($org_user->refresh_token)) {
                $auto_reply = true;
            } else {
                $auto_reply = false;
            }
            if ($leaveApplication->status != '1') {
                return view('leave_applications.edit')->with([ 'type_list' => $list, 'view' => 'edit', 'validator' => $validator, 'leaveApplication' => $leaveApplication, 'auto_reply' => $auto_reply]);
            } else {
                Flash::error($this->getMessage2());
                return redirect(route('leaveApplications.index'));
            }
        } else {
            return view('errors.403');
        }
    }

    /**
     * Update the specified LeaveApplication in storage.
     *
     * @param  int              $id
     * @param UpdateLeaveApplicationRequest $request
     *
     * @return Response
     */
    public function checkIsFullDay($timestart, $timeend, $leaveApplication) {
        $setting_id = \App\Models\OrganisationStructure::find($leaveApplication->org_id)->setting_id;
        //check start time
        $day1 = date('N', strtotime($timestart));
        $open_hour_setting = \App\Models\OpenHour::where([
                    'dayOfWeek' => $day1,
                    'setting_id' => $setting_id
                ])->first();
        $time1 = date('H:i:s', strtotime($timestart));
        if (($time1 > $open_hour_setting->start_time) && ($time1 < $open_hour_setting->end_time)) {
            return false;
        }
        //check end time
        $day2 = date('N', strtotime($timeend));
        $open_hour_setting = \App\Models\OpenHour::where([
                    'dayOfWeek' => $day2,
                    'setting_id' => $setting_id
                ])->first();
        $time2 = date('H:i:s', strtotime($timeend));
        if (($time2 > $open_hour_setting->start_time) && ($time2 < $open_hour_setting->end_time)) {
            return false;
        }
        return true;
    }

    public function update($id, UpdateLeaveApplicationRequest $request) {
        //if (\Request::ajax()) {
        if (isset($request['actions'])) {

            $leaveApplication = \App\Models\LeaveApplication::find($id);
            // $org_id = $request->session()->get('current_org');
            /* Currently, if org linked with Xero, only support Approve
             * But not support Reject, Reject need to be done through Xero
             * And need to synchronic to get updated
             */
            $root_org = \App\Models\OrganisationStructure::findRootOrg($leaveApplication->org_id);
            //only check Xero AU
            $account_token = \App\Models\AccountingToken::where([
                        'org_str_id' => $root_org,
                        'accsoft_id' => 1
                    ])->first();
            if ((isset($account_token)) && ($account_token->token != null)) {
                if ($request['actions'] == 'approve') {
                    $token = $this->getTokenFromDb($root_org);
                    if (($token == false)) {
                        $message = "Xero Token Expired.Please ReSychonize Xero Again";
                        Flash::error($message);
                        return redirect(action('LeaveApplicationController@manageLeave'));
                    }
                    if ($leaveApplication->status != 1) {
                        $leaveapp = \App\Models\LeaveApplication::find($id);
                        $this->updateOrCreateCapacity($leaveapp);
                    }
                    $leaveApplication->status = 1;
                    $leaveApplication->save();
                    $this->UpdateAutoReply($leaveApplication->id);
                    $message = "Leave Application Changed Successfully";
                    Flash::success($message);
                    $user = \Auth::user();
                    $user->sendLeaveApplicationNotification($leaveApplication, 'approved');

                    $localtimeStart = \App\Models\Setting::getLocalTime($leaveApplication->org_id, $leaveApplication->start_date, false);
                    $localtimeEnd = \App\Models\Setting::getLocalTime($leaveApplication->org_id, $leaveApplication->end_date, false);

                    $checkFullday = $this->checkIsFullDay($localtimeStart, $localtimeEnd, $leaveApplication);

                    if ($checkFullday) {
                        $this->updateLeaveApplicationtoXero($leaveApplication);
                    } else {
                        $message = "This leave application contains partical day leave. Please create this leave in Xero manually.";
                        Flash::error($message);
                        //set notification
                        $current_org = $leaveApplication->org_id;
                        $rootOrg = \App\Models\OrganisationStructure::findRootOrg($current_org);

                        $org_user = \App\Models\OrganisationUser::where([ 'org_str_id' => $rootOrg, 'is_admin' => 'yes'])->get();

                        foreach ($org_user as $manager) {
                            $userTo = \App\User::find($manager->user_id);
                            $notification = new ParticalDayNotification($leaveApplication);
                            $userTo->notify($notification);
                        }
                        //set flag for application

                        $leaveApplication->need_alert = 1;
                        $leaveApplication->save();
                    }
                } elseif (($request['actions'] == 'reject') && ($leaveApplication->status == 0)) {
                    $leaveapp = \App\Models\LeaveApplication::find($id);
                    $user = \Auth::user();
                    $user->sendLeaveApplicationNotification($leaveapp, 'rejected');
                    $leaveApplication->status = 2;
                    $leaveApplication->save();
                    $this->UpdateAutoReply($leaveApplication->id);
                    $message = "Leave Application Changed Successfully";
                    Flash::success($message);
                } else {
                    Flash::error($this->getMessage1());
                }
            } else {
                $status = ($request['actions'] == 'approve') ? '1' : '2';

                //Change the Status from approve to reject
                //Add the capacity back if do so
                if ($request['actions'] == 'reject') {
                    if ($leaveApplication->status == 1) {
                        $leaveapp = \App\Models\LeaveApplication::find($id);
                        $this->addBackCapacity($leaveapp);
                        $user = \Auth::user();
                        $user->sendLeaveApplicationNotification($leaveapp, 'rejected');
                    }
                }

                //Change the Status from reject to approve
                //Create new leave capacity or Update the old capacity
                if ($request['actions'] == 'approve') {
                    if ($leaveApplication->status != 1) {
                        $leaveapp = \App\Models\LeaveApplication::find($id);
                        $this->updateOrCreateCapacity($leaveapp);
                        $user = \Auth::user();
                        $user->sendLeaveApplicationNotification($leaveapp, 'approved');
                    }
                }
                $leaveApplication->status = $status;
                $leaveApplication->save();
                $this->UpdateAutoReply($leaveApplication->id);
                $message = "Leave Application Changed Successfully";
                Flash::success($message);
            }

            return redirect(action('LeaveApplicationController@manageLeave'));
        } else {

            $leaveApplication = $this->leaveApplicationRepository->update($request->all(), $id);

            $message = "Leave Application Save Successfully";
            Flash::success($message);
            if (\Auth::user()->id == $leaveApplication->user_id) {
                return redirect(action('LeaveApplicationController@myLeaveApplication'));
            } else {
                return redirect(action('LeaveApplicationController@manageLeave'));
            }

            //return redirect(route('leaveApplications.index'));
        }
//        } else {
//            return view('errors.403');
//        }
    }

    public function addBackCapacity($leaveapp) {

        $capacity = \App\Models\LeaveCapacity::where([
                    'user_id' => $leaveapp->user_id,
                    'org_id' => $leaveapp->org_id,
                    'leave_type_id' => $leaveapp->leave_type_id
                ])->first();
        if (isset($capacity)) {
            $capacity->capacity = $capacity->capacity * 3600 + \App\Models\LeaveApplication::CalLeaveUnit($leaveapp->org_id, $leaveapp->start_date, $leaveapp->end_date);
            $capacity->save();
        }
    }

    public function updateOrCreateCapacity($leaveapp) {
        $accrual_rule = \App\Models\LeaveCapacity::findAccrualRule($leaveapp->org_id, $leaveapp->user_id, $leaveapp->leave_type_id);
        if ((isset($accrual_rule))) {
            $capacity = \App\Models\LeaveCapacity::where([
                        'user_id' => $leaveapp->user_id,
                        'org_id' => $leaveapp->org_id,
                        'leave_type_id' => $leaveapp->leave_type_id
                    ])->first();

            $leave_unit = \App\Models\LeaveApplication::CalLeaveUnit($leaveapp->org_id, $leaveapp->start_date, $leaveapp->end_date);
            if (isset($capacity)) {
                $capacity->capacity = $capacity->capacity * 3600 - $leave_unit;
                $capacity->save();
            } else {
                \App\Models\LeaveCapacity::create([
                    'user_id' => $leaveapp->user_id,
                    'org_id' => $leaveapp->org_id,
                    'leave_type_id' => $leaveapp->leave_type_id,
                    'capacity' => $leave_unit,
                    'last_update_date' => date('Y-m-d- H:i:s', time())
                ]);
            }
        }
    }

    /**
     * Remove the specified LeaveApplication from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id, Request $request) {

        $leaveapp = \App\Models\LeaveApplication::find($id);
        if (($leaveapp->status == 1) && (strtotime($leaveapp->start_date) < time())) {
            $message = "You cannot delete approved leave if it is already started.";
            Flash::error($message);
            return redirect(action('LeaveApplicationController@myLeaveApplication'));
        }
        if ($leaveapp->status == 1) {
            $this->addBackCapacity($leaveapp);
        }
        $this->leaveApplicationRepository->delete($id);

        $message = "Application Deleted Successfully";
        Flash::success($message);
        return redirect(action('LeaveApplicationController@myLeaveApplication'));
    }

    public function checkApplication(Request $request) {
        if (\Request::ajax()) {
            $date_range = urldecode($request['date_range']);
            $leave_type_id = $request['leave_type_id'];
            $leave_id = $request['leaveAppId'];
            $exceptionId = ($leave_id == '' ) ? null : $leave_id;

            $leaveapplication = \App\Models\LeaveApplication::find($leave_id);

            $leave_type_name = \App\Models\LeaveType::find($request['leave_type_id'])->name;
            $date_start = explode(' - ', $date_range)[0];
            $date_end = explode(' - ', $date_range)[1];
            $org_id = isset($leaveapplication) ? $leaveapplication->org_id : $request->session()->get('current_org');

            $utcDateStart = \App\Models\Setting::getUctTime($org_id, $date_start);
            $utcDateEnd = \App\Models\Setting::getUctTime($org_id, $date_end);
            //Add all the rules here
            //Currently it's the general leave rule and block date rule
            $rule_array[] = \App\Models\BlockDate::checkBlockDateRule($utcDateStart, $utcDateEnd, $org_id, false, $exceptionId);
            $rule_array[] = \App\Models\Setting::checkGeneralLeaveRule($utcDateStart, $utcDateEnd, $org_id, false, $exceptionId);
            $rule_array[] = \App\Models\LeaveCapacity::checkCapacityRule($org_id, \Auth::user()->id, $leave_type_id, $utcDateStart, $utcDateEnd, false, $exceptionId);
            //Only check the rule if leave name contain "sick leave"
            if (strpos($leave_type_name, "sick leave") !== false) {
                $rule_array[] = \App\Models\SickLeave::checkSickLeaveRule($date_start, $date_end, $org_id);
            }
            $output = "<ul>";
            foreach ($rule_array as $item) {
                if ($item['status'] == "failed") {
                    $output = $output . "<li class=" . $item['status'] . ">" . $item['message'] . "</li>";
                }
            }
            $output = $output . "</ul>";
            return $output;
        } else {
            return view('errors.403');
        }
    }

    public function updateLeaveApplicationtoXero($leaveApplication) {

        //If Xero is linked, then push the leave app to Xero
        $root_org = \App\Models\OrganisationStructure::findRootOrg($leaveApplication->org_id);

        $account_token = \App\Models\AccountingToken::where([
                    'org_str_id' => $root_org
                ])->first();
        if (isset($account_token)) {
            $params = [];
            $org_user = \App\Models\OrganisationUser::where([
                        'org_str_id' => $leaveApplication->org_id,
                        'user_id' => $leaveApplication->user_id,
                    ])->first();

            $comment = \App\Models\Comment::where('leave_id', $leaveApplication->id)->first();
            if (isset($comment)) {
                $title = $comment->content;
            } else {
                $userName = \App\User::find($leaveApplication->user_id)->name;
                $leaveName = \App\Models\LeaveType::find($leaveApplication->leave_type_id)->name;
                $title = $userName . '`s ' . $leaveName;
            }
            if ((isset($org_user->xero_id)) && (isset(\App\Models\LeaveType::find($leaveApplication->leave_type_id)->xero_id))) {

                $variable = array(
                    'EmployeeID' => $org_user->xero_id,
                    'LeaveTypeID' => \App\Models\LeaveType::find($leaveApplication->leave_type_id)->xero_id,
                    'Title' => $title,
                    'StartDate' => date('Y-m-d', strtotime(\App\Models\Setting::getLocalTime($root_org, $leaveApplication->start_date, false))),
                    'EndDate' => date('Y-m-d', strtotime(\App\Models\Setting::getLocalTime($root_org, $leaveApplication->end_date, false))),
                );
                if (isset($leaveApplication->xero_id)) {
                    $variable['LeaveApplicationID'] = $leaveApplication->xero_id;
                }
                array_push($params, $variable);
                $xml = \App\Models\Serializer::toxml($params, "LeaveApplications", array("LeaveApplication"));
                $token = $this->getTokenFromDb($root_org);
                $XeroOAuth = $this->getXeroElement($token->token, $token->secret_token);
                $XeroId = $this->setLeaveApplication($XeroOAuth, $xml);
                if (isset($XeroId)) {
                    $leaveApplication->xero_id = $XeroId;
                    $leaveApplication->save();
                } else {
                    $user = \Auth::user();
                    $user->sendXeroNotification($leaveApplication->org_id, 'xero');
                }
            } else {
                $user = \Auth::user();
                $user->sendXeroNotification($leaveApplication->org_id, 'xero');
            }
        }
    }

    public function crudMatchHalfDayLeave() {

        $accountToken = \App\Models\AccountingToken::all();
        foreach ($accountToken as $token) {
            $org_id = $token->org_str_id;
            $tree = $this->getChildTree($org_id);
            // leaves in DB
            $leaveapplicationsDB = \App\Models\LeaveApplication::whereIn('org_id', $tree)->where('need_alert', 1)->get();
            if (sizeof($leaveapplicationsDB) == 0) {
                continue;
            }
            $newtoken = $this->getTokenFromDb($org_id);
            $XeroOAuth = $this->getXeroElement($newtoken->token, $newtoken->secret_token);
            //Leaves in Xero
            $leaves = $this->getLeaves($XeroOAuth, $token->last_check_time);
            foreach ($leaveapplicationsDB as $leaveDB) {
                foreach ($leaves->LeaveApplication as $leave) {
                    //Xero feature                    
                    $startdate = strtotime((string) $leave->StartDate);
                    $enddate = strtotime((string) $leave->EndDate);
                    $leavetypeID = (string) $leave->LeaveTypeID;
                    $employeeId = (string) $leave->EmployeeID;
                    $leaveID = (string) $leave->LeaveApplicationID;
                    //DB feature
                    $leavetypeXeroID = \App\Models\LeaveType::find($leaveDB->leave_type_id)->xero_id;
                    $employeeXeroID = \App\Models\OrganisationUser::where(['user_id' => $leaveDB->user_id, 'org_str_id' => $leaveDB->org_id])->first()->xero_id;
                    $leaveStart = strtotime(\App\Models\Setting::getLocalTime($leaveDB->org_id, $leaveDB->start_date, false));
                    $leaveEnd = strtotime(\App\Models\Setting::getLocalTime($leaveDB->org_id, $leaveDB->end_date, false));

                    if (($employeeXeroID == $employeeId) && ($leavetypeXeroID == $leavetypeID) && (date('Y-m-d', $startdate) == date('Y-m-d', $leaveStart) ) && (date('Y-m-d', $enddate) == date('Y-m-d', $leaveEnd))) {
                        $notifications = \App\Models\Notification::all();
                        foreach ($notifications as $notification) {
                            $data = json_decode($notification->data);
                            $type = $notification->type;
                            if (($type == 'App\Notifications\ParticalDayNotification') && ($data->leaveapplicationID == $leaveDB->id)) {
                                $notification->read_at = date('Y-m-d H:i:s');
                                $notification->save();
                            }
                        }
                        $leaveDB->xero_id = $leaveID;
                        $leaveDB->need_alert = 0;
                        $leaveDB->save();
                        break;
                    }
                }
            }

            $formatted = date("Y-m-d") . "T" . date("H:i:h");
            $token->last_check_time = $formatted;
            $token->save();
        }
    }

}
