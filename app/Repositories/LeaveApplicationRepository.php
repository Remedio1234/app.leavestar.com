<?php

namespace App\Repositories;

use App\Models\LeaveApplication;
use InfyOm\Generator\Common\BaseRepository;

class LeaveApplicationRepository extends BaseRepository {

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'start_date',
        'end_date',
        'leave_type_id',
        'flexible',
        'status',
        'data_range',
    ];

    /**
     * Configure the Model
     * */
    public function model() {
        return LeaveApplication::class;
    }

    public function saveAttributes(array $attributes, $leave_application, $org_id) {
        $user_id = isset($leave_application->user_id) ? $leave_application->user_id : \Auth::user()->id;
        $dates = explode(' - ', $attributes['date_range']);
        $leave_type_id = $attributes['leave_type_id'];
        $flexible = $attributes['flexible'];
        //0 for pending , 1 for approved
        // -1 for scheduled leave, not show for manager  
        $status = $attributes['status'];
        $autoreply = isset($attributes['autoreplysetting']) ? "1" : "0";
        $autoreplymessage = ($autoreply == 0) ? "" : $attributes['autoreplymessage'];

        $leave_application->user_id = $user_id;
        $leave_application->org_id = $org_id;
        $leave_application->start_date = \App\Models\Setting::getUctTime($org_id, $dates[0]);
        $leave_application->end_date = \App\Models\Setting::getUctTime($org_id, $dates[1]);
        $leave_application->leave_type_id = $leave_type_id;
        $leave_application->flexible = $flexible;
        $leave_application->status = $status;
        $leave_application->autoreplysetting = $autoreply;
        $leave_application->autoreplymessage = $autoreplymessage;
        $leave_application->save();

        if ($attributes['comment'] != null) {
            $comment = new \App\Models\Comment;
            $comment->sender_id = $user_id;
            $comment->leave_id = $leave_application->id;
            $comment->content = $attributes['comment'];
            $comment->save();
        }
        return $leave_application;
    }

    public function createCapacity(array $attributes) {
        $user_id = \Auth::user()->id;
        $org_id = \Session::get('current_org');
        $accrual_rule = \App\Models\LeaveCapacity::findAccrualRule($org_id, $user_id, $attributes['leave_type_id']);
        if ((isset($accrual_rule)) && ($attributes['status'] == 0)) {
            $capacity = \App\Models\LeaveCapacity::where([
                        'user_id' => $user_id,
                        'org_id' => $org_id,
                        'leave_type_id' => $attributes['leave_type_id']
                    ])->first();
            $dates = explode(' - ', $attributes['date_range']);


            $leave_unit = LeaveApplication::CalLeaveUnit($org_id, \App\Models\Setting::getUctTime($org_id, $dates[0]), \App\Models\Setting::getUctTime($org_id, $dates[1]));

            if (isset($capacity)) {
                $capacity->capacity = $capacity->capacity * 3600 - $leave_unit;
                $capacity->save();
            } else {
                \App\Models\LeaveCapacity::create([
                    'user_id' => $user_id,
                    'org_id' => $org_id,
                    'leave_type_id' => $attributes['leave_type_id'],
                    'capacity' => $leave_unit,
                    'last_update_date' => date('Y-m-d- H:i:s', time())
                ]);
            }
        }
    }

    public function updateCapacity(array $attributes, $leaveapp) {
        $user_id = \Auth::user()->id;
        $org_id = $leaveapp->org_id;
        $accrual_rule = \App\Models\LeaveCapacity::findAccrualRule($org_id, $user_id, $attributes['leave_type_id']);

        if ((isset($accrual_rule)) && ($attributes['status'] == 0) && ($leaveapp->status == 3)) {

            $capacity = \App\Models\LeaveCapacity::where([
                        'user_id' => $user_id,
                        'org_id' => $org_id,
                        'leave_type_id' => $attributes['leave_type_id']
                    ])->first();
            $dates = explode(' - ', $attributes['date_range']);
            $leave_unit = LeaveApplication::CalLeaveUnit($org_id, \App\Models\Setting::getUctTime($org_id, $dates[0]), \App\Models\Setting::getUctTime($org_id, $dates[1]));
            if (isset($capacity)) {
                $capacity->capacity = $capacity->capacity * 3600 - $leave_unit;
                $capacity->save();
            } else {
                \App\Models\LeaveCapacity::create([
                    'user_id' => $user_id,
                    'org_id' => $org_id,
                    'leave_type_id' => $attributes['leave_type_id'],
                    'capacity' => $leave_unit,
                    'last_update_date' => date('Y-m-d- H:i:s', time())
                ]);
            }
        } else if ((isset($accrual_rule)) && ($attributes['status'] == 3) && ($leaveapp->status == 0)) {

            $capacity = \App\Models\LeaveCapacity::where([
                        'user_id' => $user_id,
                        'org_id' => $org_id,
                        'leave_type_id' => $attributes['leave_type_id']
                    ])->first();
            $dates = explode(' - ', $attributes['date_range']);
            $leave_unit = LeaveApplication::CalLeaveUnit($org_id, \App\Models\Setting::getUctTime($org_id, $dates[0]), \App\Models\Setting::getUctTime($org_id, $dates[1]));
            $capacity->capacity = $capacity->capacity * 3600 + $leave_unit;
            $capacity->save();
        }
    }

    public function checkRules(array $attributes, $org_id, $exceptionLeaveID = null) {
        $date_range = urldecode($attributes['date_range']);
        $date_start = explode(' - ', $date_range)[0];
        $date_end = explode(' - ', $date_range)[1];
        $utcDateStart = \App\Models\Setting::getUctTime($org_id, $date_start);
        $utcDateEnd = \App\Models\Setting::getUctTime($org_id, $date_end);
        $leave_type_id = $attributes['leave_type_id'];
        $leave_type_name = \App\Models\LeaveType::find($attributes['leave_type_id'])->name;

        $rule_array[] = \App\Models\BlockDate::checkBlockDateRule($utcDateStart, $utcDateEnd, $org_id, false, $exceptionLeaveID);
        /*$rule_array[] = \App\Models\Setting::checkGeneralLeaveRule($utcDateStart, $utcDateEnd, $org_id, false, $exceptionLeaveID);*/
        $rule_array[] = \App\Models\LeaveCapacity::checkCapacityRule($org_id, \Auth::user()->id, $leave_type_id, $utcDateStart, $utcDateEnd, false, $exceptionLeaveID);
        //Only check the rule if leave name contain "sick leave"
        if (strpos($leave_type_name, "sick leave") !== false) {
            $rule_array[] = \App\Models\SickLeave::checkSickLeaveRule($date_start, $date_end, $org_id);
        }
        $check = true;

        foreach ($rule_array as $value) {
            if (($value['status'] != 'success')) {
                $check = false;
                break;
            }
        }

        return $check;
    }

    public function create(array $attributes) {

        $leave_app = new LeaveApplication;
        $org_id = \Session::get('current_org');
        $newLeaveApplication = $this->saveAttributes($attributes, $leave_app, $org_id);

        //If accrual rule isset , then reduce the leave capacity             
        if ($this->checkRules($attributes, $org_id, $newLeaveApplication->id) && $attributes['status'] != '3') {
            $leave_app->rule_check = 1;
            $leave_app->save();
        }
        $user = \Auth::user();
        $user->sendLeaveApplicationNotification($newLeaveApplication);
        return $newLeaveApplication;
        //$this->createCapacity($attributes);
    }

    public function update(array $attributes, $id) {

        $leave_app = LeaveApplication::find($id);
        if ((isset($attributes['status'])) && ($attributes['status'] == '3')) {
            $leave_app->rule_check = 0;
            $leave_app->save();
        }
        if ($this->checkRules($attributes, $leave_app->org_id, $id)) {
            if ((isset($attributes['status'])) && ($attributes['status'] != '3')) {
                $leave_app->rule_check = 1;
                $leave_app->save();
            }
        } else {
            $leave_app->rule_check = 0;
            $leave_app->save();
        }

        $this->updateCapacity($attributes, $leave_app);
        $this->saveAttributes($attributes, $leave_app, $leave_app->org_id);
        return $leave_app;
    }

}
