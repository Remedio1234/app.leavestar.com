<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLeaveAccrualSettingRequest;
use App\Http\Requests\UpdateLeaveAccrualSettingRequest;
use App\Repositories\LeaveAccrualSettingRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class LeaveAccrualSettingController extends AppBaseController {

    /** @var  LeaveAccrualSettingRepository */
    private $leaveAccrualSettingRepository;
    private $daily_token = "iaee7svQSWwEIBPyu4jteigVNi7A2nOfYftcTgDgaYfObHo3ya3GXceTOyVH";
    protected $validationRules = [
        'seconds' => 'required',
    ];

    public function __construct(LeaveAccrualSettingRepository $leaveAccrualSettingRepo) {
        $this->leaveAccrualSettingRepository = $leaveAccrualSettingRepo;
    }

    /**
     * Display a listing of the LeaveAccrualSetting.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        if (\Request::ajax()) {

            $organisationStructure = \App\Models\OrganisationStructure::find($request['org_id']);

            $leaveAccrualSettings = \App\Models\LeaveAccrualSetting::where('org_id', $organisationStructure->id)->where('user_id', null)->get();

            if (sizeof($leaveAccrualSettings) == 0) {
                $root_org = \App\Models\OrganisationStructure::findRootOrg($organisationStructure->id);
                $leaveAccrualSettings = \App\Models\LeaveAccrualSetting::where('org_id', $root_org)->get();
            }

            return view('leave_accrual_settings.index')
                            ->with(['organisationStructure' => $organisationStructure, 'leaveAccrualSettings' => $leaveAccrualSettings, 'view' => 'leavearrual']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Show the form for creating a new LeaveAccrualSetting.
     *
     * @return Response
     */
    public function create(Request $request) {
        $org_id = $request->session()->get('current_org');
        if (\App\User::checkUserRole((\Auth::user()->id), $org_id)) {
            $validator = \JsValidator::make($this->validationRules);

            $organisationStructure = \App\Models\OrganisationStructure::where('id', $request['org_id'])->first();

            return view('leave_accrual_settings.create')->with(['organisationStructure' => $organisationStructure, 'validator' => $validator, 'view' => 'leavearrual']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Store a newly created LeaveAccrualSetting in storage.
     *
     * @param CreateLeaveAccrualSettingRequest $request
     *
     * @return Response
     */
    public function store(CreateLeaveAccrualSettingRequest $request) {
        if (\Request::ajax()) {
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request['org_id'];
            $input = $request->all();
            $this->leaveAccrualSettingRepository->create($input);

            $organisationStructure = \App\Models\OrganisationStructure::where('id', $org_id)->first();
            $leaveAccrualSettings = \App\Models\LeaveAccrualSetting::where('org_id', $organisationStructure->id)->where('user_id', null)->get();
            $alert = "  Rule saved successfully.";

            return redirect(route('leaveAccrualSettings.index', ['org_id' => $org_id]))->with('status', $alert);
            //return view('leave_accrual_settings.index')->with(['organisationStructure' => $organisationStructure, 'leaveAccrualSettings' => $leaveAccrualSettings, 'validator' => $validator, 'alert' => $alert, 'view' => 'leavearrual']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Display the specified LeaveAccrualSetting.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id) {
        return view('errors.403');
//        $leaveAccrualSetting = $this->leaveAccrualSettingRepository->findWithoutFail($id);
//
//        if (empty($leaveAccrualSetting)) {
//            Flash::error('Leave Accrual Setting not found');
//
//            return redirect(route('leaveAccrualSettings.index'));
//        }
//
//        return view('leave_accrual_settings.show')->with('leaveAccrualSetting', $leaveAccrualSetting);
    }

    /**
     * Show the form for editing the specified LeaveAccrualSetting.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id, Request $request) {
        $org_id = $request['org_id'];
        if (\App\Models\LeaveAccrualSetting::checkBelonging($id, $org_id)) {
            $validator = \JsValidator::make($this->validationRules);
            $leaveAccrualSetting = $this->leaveAccrualSettingRepository->findWithoutFail($id);
            $organisationStructure = \App\Models\OrganisationStructure::where('id', $request['org_id'])->first();

            return view('leave_accrual_settings.edit')->with(['organisationStructure' => $organisationStructure, 'validator' => $validator, 'leaveAccrualSetting' => $leaveAccrualSetting, 'view' => 'leavearrual']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Update the specified LeaveAccrualSetting in storage.
     *
     * @param  int              $id
     * @param UpdateLeaveAccrualSettingRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLeaveAccrualSettingRequest $request) {
        if (\Request::ajax()) {
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request['org_id'];
            $this->leaveAccrualSettingRepository->update($request->all(), $id);

            $organisationStructure = \App\Models\OrganisationStructure::where('id', $org_id)->first();

            $leaveAccrualSettings = \App\Models\LeaveAccrualSetting::where('org_id', $organisationStructure->id)->where('user_id', null)->get();

            $alert = 'Rule updated successfully.';
            return redirect(route('leaveAccrualSettings.index', ['org_id' => $org_id]))->with('status', $alert);
            //return view('leave_accrual_settings.index')->with(['organisationStructure' => $organisationStructure, 'leaveAccrualSettings' => $leaveAccrualSettings, 'validator' => $validator, 'alert' => $alert, 'view' => 'leavearrual']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Remove the specified LeaveAccrualSetting from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id, Request $request) {
        if (\Request::ajax()) {
            //get organisation and validator
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request['org_id'];

            // pass the org_id with a tricky way
            $array['id'] = $id;
            $array['org_id'] = $org_id;
            $this->leaveAccrualSettingRepository->delete($array);

            //get the new setting_id
            $organisationStructure = \App\Models\OrganisationStructure::where('id', $org_id)->first();
            $leaveAccrualSettings = \App\Models\LeaveAccrualSetting::where('org_id', $organisationStructure->id)->where('user_id', null)->get();

            if (sizeof($leaveAccrualSettings) == 0) {
                $root_org = \App\Models\OrganisationStructure::findRootOrg($organisationStructure->id);
                $leaveAccrualSettings = \App\Models\LeaveAccrualSetting::where('org_id', $root_org)->get();
            }

            $alert = 'Rule deleted successfully.';
            return redirect(route('leaveAccrualSettings.index', ['org_id' => $org_id]))->with('status', $alert);
//return view('leave_accrual_settings.index')->with(['organisationStructure' => $organisationStructure, 'leaveAccrualSettings' => $leaveAccrualSettings, 'validator' => $validator, 'alert' => $alert, 'view' => 'leavearrual']);
        } else {
            return view('errors.403');
        }
    }

    public function NewSetting(Request $request) {
        if (\Request::ajax()) {
            $rules = $this->validationRules;
            $rules['balance'] = "required|numeric";
            $validator = \JsValidator::make($rules);
            $org_id = $request['org_id'];
            $user_id = $request['user_id'];
            $leavetype_id = $request['leavetype'];
            return view('leave_accrual_settings.create_partical')->with(['org_id' => $org_id, 'leave_type_id' => $leavetype_id, 'user_id' => $user_id, 'validator' => $validator]);
        } else {
            return view('errors.403');
        }
    }

    public function StoreSetting(Request $request) {
        $org_id = $request->session()->get('current_org');
        \App\Models\LeaveAccrualSetting::updateOrCreate([
            'org_id' => $request['org_id'],
            'user_id' => $request['user_id'],
            'leave_type_id' => $request['leave_type_id'],
                ], [
            'period' => $request['period'],
            'seconds' => $request['seconds'] * 3600,
            'options' => $request['options']
        ]);

        \App\Models\LeaveCapacity::updateOrCreate([
            'user_id' => $request['user_id'],
            'org_id' => $request['org_id'],
            'leave_type_id' => $request['leave_type_id'],
                ], [
            'capacity' => $request['balance'] * 3600,
            'last_update_date' => date("Y-m-d H:i:s", time()),
        ]);
        $message = "Leave Accrual Setting Created Successfully";
        $org_user_id = \App\Models\OrganisationUser::where(['org_str_id' => $request['org_id'], 'user_id' => $request['user_id']])->first()->id;
        return redirect(route('organisationUsers.edit', ['id' => $org_user_id]))->with('status', $message);
    }

    public function EditSetting(Request $request) {
        if (\Request::ajax()) {
            $rules = $this->validationRules;
            $rules['balance'] = "required|numeric";
            $validator = \JsValidator::make($rules);
            $org_id = $request['org_id'];
            $user_id = $request['user_id'];
            $accrual_setting = \App\Models\LeaveAccrualSetting::find($request['accrual_setting']);
            $capacity = \App\Models\LeaveCapacity::where(['leave_type_id' => $accrual_setting->leave_type_id, 'user_id' => $user_id, 'org_id' => $org_id])->first();

            return view('leave_accrual_settings.update_partical')->with(['leaveAccrualSetting' => $accrual_setting, 'capacity' => $capacity, 'org_id' => $org_id, 'user_id' => $user_id, 'validator' => $validator]);
        } else {
            return view('errors.403');
        }
    }

    //On going Accrualling strategy
    public function AccruallingOngoing($leavecapacity, $leave_accrual_setting) {
        $last_update_date = $leavecapacity->last_update_date;
        $current = date("Y-m-d H:i:s", time());
        switch ($leave_accrual_setting->period) {
            case 0:
                $accrualing_per_seconds = ($leave_accrual_setting->seconds / 24 );
                break;
            case 1:
                $accrualing_per_seconds = (($leave_accrual_setting->seconds / 7 / 24) );
                break;
            case 2:
                $accrualing_per_seconds = (($leave_accrual_setting->seconds / 30 / 24) );
                break;
            case 3:
                $accrualing_per_seconds = ($leave_accrual_setting->seconds / 365 / 24 );
                break;
        }

        $seconds_gap = strtotime($current) - strtotime($last_update_date);
        $increasing = $seconds_gap * $accrualing_per_seconds;
        $leavecapacity->capacity = $leavecapacity->capacity * 3600 + $increasing;
        $leavecapacity->last_update_date = date("Y-m-d H:i:s", time());
        $leavecapacity->save();
    }

    //At End of period Accrualling strategy
    public function AccruallingAtEnd($leavecapacity, $leave_accrual_setting) {
        $last_update_date = $leavecapacity->last_update_date;
        $current = date("Y-m-d H:i:s", time());
        switch ($leave_accrual_setting->period) {
            case 0:
                $total_seconds = 3600 * 24;
                break;
            case 1:
                $total_seconds = 3600 * 24 * 7;
                break;
            case 2:
                $total_seconds = 3600 * 24 * 30;
                break;
            case 3:
                $total_seconds = 3600 * 24 * 365;
                break;
        }
        $seconds_gap = strtotime($current) - strtotime($last_update_date);
        if ($seconds_gap >= $total_seconds) {
            $leavecapacity->capacity = $leavecapacity->capacity * 3600 + $leave_accrual_setting->seconds * 3600;
            $leavecapacity->last_update_date = date("Y-m-d H:i:s", strtotime($last_update_date) + $total_seconds);
            $leavecapacity->save();
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

    public function getUserExceptionList($org_id, $leave_type_id) {
        $list = [];
        $exceptions = \App\Models\LeaveAccrualSetting::where([
                    'org_id' => $org_id,
                    'leave_type_id' => $leave_type_id
                ])->where('user_id', '!=', null)->get();
        foreach ($exceptions as $item) {
            $list[] = $item->user_id;
        }
        return $list;
    }

    public function getOrgExceptionList($org_id, $leave_type_id) {
        $list = [];
        $tree = $this->getChildTree($org_id);
        $exceptions = \App\Models\LeaveAccrualSetting::where([
                    'leave_type_id' => $leave_type_id
                ])->where('user_id', '!=', null)->whereIn('org_id', $tree)->get();
        foreach ($exceptions as $item) {
            $list[] = $item->org_id;
        }
        return $list;
    }

    public function SingleUserAcc($user_id, $rules) {
        $leavecapacity = \App\Models\LeaveCapacity::firstOrCreate([
                    'user_id' => $user_id,
                    'org_id' => $rules->org_id,
                    'leave_type_id' => $rules->leave_type_id
                        ], [
                    'capacity' => 0,
                    'last_update_date' => date("Y-m-d H:i:s", time())
        ]);
        if ($rules->options == 0) {
            $this->AccruallingOngoing($leavecapacity, $rules);
        } else {
            $this->AccruallingAtEnd($leavecapacity, $rules);
        }
    }

    //Auto Accrualling Script    
    public function AutoAccrualling($token) {
        if ($token == $this->daily_token) {
            $accrualling_rules = \App\Models\LeaveAccrualSetting::all();
            foreach ($accrualling_rules as $rules) {
                if (isset($rules->user_id)) {
                    //If not set leave capacity for this user with this leave type
                    //Create a new empty capacitu for this user
                    $this->SingleUserAcc($rules->user_id, $rules);
                } else {
                    $rules_org = $rules->org_id;
                    $root_org = \App\Models\OrganisationStructure::findRootOrg($rules_org);
                    if ($rules_org != $root_org) {
                        $user_exception_list = $this->getUserExceptionList($rules->org_id, $rules->leave_type_id);
                        $users = \App\Models\OrganisationUser::where('org_str_id', $rules->org_id)->whereNotIn('user_id', $user_exception_list)->get();
                        foreach ($users as $user) {
                            $this->SingleUserAcc($user->user_id, $rules);
                        }
                    } else {
                        $org_exception_list = $this->getOrgExceptionList($rules->org_id, $rules->leave_type_id);
                        $user_exception_list = $this->getUserExceptionList($rules->org_id, $rules->leave_type_id);
                        $tree = $this->getChildTree($rules->org_id);
                        $users = \App\Models\OrganisationUser::whereIn('org_str_id', $tree)->whereNotIn('org_str_id', $org_exception_list)->whereNotIn('user_id', $user_exception_list)->get();
                        foreach ($users as $user) {
                            $this->SingleUserAcc($user->user_id, $rules);
                        }
                    }
                }
            }
        } else {
            return view('errors.403');
        }
    }

}
