<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LeaveCapacity
 * @package App\Models
 * @version July 5, 2017, 3:48 am UTC
 */
class LeaveCapacity extends Model {

    use SoftDeletes;

    public $table = 'leave_capacity';

    private static function getMessage1($leave_type_id) {
        return "<span class='glyphicon glyphicon-check'></span> It looks like you have enough " . LeaveType::find($leave_type_id)->name . " available.";
    }

    private static function getMessage2($leave_type_id, $need_unit, $unit_available, $manager) {
        $has_hours = round($unit_available / 3600);
        $need_hours = $need_unit / 3600;
        // $has_hours = $has_hours > 0 ? $has_hours : 0;
        return "<span class='glyphicon glyphicon-exclamation-sign'></span> Applicant has {$has_hours} hours of " . LeaveType::find($leave_type_id)->name . " available. {$need_hours} hours has been requested.";
    }

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'user_id',
        'org_id',
        'leave_type_id',
        'capacity',
        'last_update_date'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'org_id' => 'integer',
        'leave_type_id' => 'integer',
        'capacity' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    public function getCapacityAttribute($seconds) {
        $value = $seconds / 3600;
        return $value;
    }

    public static function findAccrualRule($org_id, $user_id, $leave_type_id) {
        $single_accrual = LeaveAccrualSetting::where([
                    'user_id' => $user_id,
                    'org_id' => $org_id,
                    'leave_type_id' => $leave_type_id
                ])->first();
        if (isset($single_accrual)) {
            return $single_accrual;
        } else {
            $org_accrual = LeaveAccrualSetting::where([
                        'org_id' => $org_id,
                        'leave_type_id' => $leave_type_id
                    ])->first();
            if (isset($org_accrual)) {
                return $org_accrual;
            } else {
                $root_org = OrganisationStructure::findRootOrg($org_id);
                $root_accrual = LeaveAccrualSetting::where([
                            'org_id' => $root_org,
                            'leave_type_id' => $leave_type_id
                        ])->first();
                if (isset($root_accrual)) {
                    return $root_accrual;
                } else {
                    return null;
                }
            }
        }
    }

    public static function totalUnitAvailble($org_id, $user_id, $leave_type_id, $accrual_rule, $end_date, $exceptionLeaveID) {
        //Current Capacity
        $capacity = LeaveCapacity::where([
                    'user_id' => $user_id,
                    'org_id' => $org_id,
                    'leave_type_id' => $leave_type_id
                ])->first();
        if (isset($capacity)) {
            $available_now = ($capacity->capacity * 3600);
            $last_update_date = strtotime($capacity->last_update_date);
        } else {
            $available_now = 0;
            $last_update_date = strtotime(date('Y-m-d H:i:s', time()));
        }

        //Accrualling Capacity
        $period = $accrual_rule->period;
        $gap = array(
            '0' => 24 * 3600,
            '1' => 24 * 3600 * 7,
            '2' => 24 * 3600 * 30,
            '3' => 24 * 3600 * 365,
        );
        while ($last_update_date < strtotime($end_date)) {
            if (( $last_update_date + $gap[$period]) < strtotime($end_date)) {
                $last_update_date = $last_update_date + $gap[$period];
                $available_now = $available_now + ($accrual_rule->seconds) * 3600;
            } else {
                $options = $accrual_rule->options;
                if ($options == 1) {
                    break;
                } else {
                    switch ($accrual_rule->period) {
                        case 0:
                            $accrualing_per_seconds = ($accrual_rule->seconds / 24 );
                            break;
                        case 1:
                            $accrualing_per_seconds = (($accrual_rule->seconds / 7 / 24) );
                            break;
                        case 2:
                            $accrualing_per_seconds = (($accrual_rule->seconds / 30 / 24) );
                            break;
                        case 3:
                            $accrualing_per_seconds = ($accrual_rule->seconds / 365 / 24 );
                            break;
                    }
                    $seconds = strtotime($end_date) - $last_update_date;
                    $available_now = $available_now + $seconds * $accrualing_per_seconds;
                    break;
                }
            }
        }

        //Vaild Pending Application
        $vaildApp = LeaveApplication::where([
                    'user_id' => $user_id,
                    'org_id' => $org_id,
                    'status' => 0
                ])->where('id', '!=', $exceptionLeaveID)->get();
        foreach ($vaildApp as $app) {
            $unit = LeaveApplication::CalLeaveUnit($app->org_id, $app->start_date, $app->end_date);
            $available_now = $available_now - $unit;
        }
        //Available Capacity = Current Capacity+ Accrualing - Pending Leave                
        return $available_now;
    }

    public static function checkCapacityRule($org_id, $user_id, $leave_type_id, $start_date, $end_date, $manager = false, $exceptionLeaveID = null) {
        $accrual_rule = LeaveCapacity::findAccrualRule($org_id, $user_id, $leave_type_id);

        if (isset($accrual_rule)) {

            $need_unit = LeaveApplication::CalLeaveUnit($org_id, $start_date, $end_date);
            $unit_available = LeaveCapacity::totalUnitAvailble($org_id, $user_id, $leave_type_id, $accrual_rule, $end_date, $exceptionLeaveID);

            if ($need_unit > $unit_available) {
                $array['status'] = "failed";
                $array['message'] = LeaveCapacity::getMessage2($leave_type_id, $need_unit, $unit_available, $manager);
                return $array;
            } else {
                $array['status'] = "success";
                $array['message'] = LeaveCapacity::getMessage1($leave_type_id);
                return $array;
            }
        } else {
            $array['status'] = "success";
            $array['message'] = LeaveCapacity::getMessage1($leave_type_id);
            return $array;
        }
    }

}
