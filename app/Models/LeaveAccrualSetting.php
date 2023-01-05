<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LeaveAccrualSetting
 * @package App\Models
 * @version July 4, 2017, 5:21 am UTC
 */
class LeaveAccrualSetting extends Model {

    use SoftDeletes;

    public $table = 'leave_accrual_setting';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'org_id',
        'user_id',
        'leave_type_id',
        'period',
        'seconds',
        'options',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'org_id' => 'integer',
        'user_id' => 'integer',
        'leave_type_id' => 'integer',
        'period' => 'integer',
        'options' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    public static function checkBelonging($id, $org_id) {
        $leave_arrual_setting = LeaveAccrualSetting::where([
                    'id' => $id,
                    'org_id' => $org_id,
                    'user_id' => null
                ])->first();
        if (isset($leave_arrual_setting)) {
            return true;
        }
        $root_org = OrganisationStructure::findRootOrg($org_id);
        $leave_arrual_setting2 = LeaveAccrualSetting::where([
                    'id' => $id,
                    'org_id' => $root_org,
                    'user_id' => null
                ])->first();
        if (isset($leave_arrual_setting2)) {
            return true;
        }
        return false;
    }

    public function getSecondsAttribute($seconds) {
        $value = $seconds / 3600;
        return $value;
    }

    //This is on org level
    public static function findSetting($org_id) {
        $leave_accrual_settings = LeaveAccrualSetting::where(['org_id' => $org_id, 'user_id' => null])->first();
        if (isset($leave_accrual_settings)) {
            return LeaveAccrualSetting::where(['org_id' => $org_id, 'user_id' => null])->get();
        } else {
            $root_org = OrganisationStructure::findRootOrg($org_id);

            return LeaveAccrualSetting::where(['org_id' => $root_org, 'user_id' => null])->get();
        }
    }

    //This is on user level
    public static function findSettingClient($org_id, $leavetype_id) {
        $leave_accrual_settings = LeaveAccrualSetting::where(['org_id' => $org_id, 'leave_type_id' => $leavetype_id])->first();
        if (isset($leave_accrual_settings)) {

            return $leave_accrual_settings;
        } else {
            $root_org = OrganisationStructure::findRootOrg($org_id);

            return LeaveAccrualSetting::where(['org_id' => $root_org, 'leave_type_id' => $leavetype_id])->first();
        }
    }

    public static function getDetails($leave_accrual_settings) {
        $period[0] = 'Day';
        $period[1] = 'Week';
        $period[2] = 'Month';
        $period[3] = 'Year';

        $options[0] = "Accrual On Going";
        $options[1] = "Accrual at the end of selected period";

        $details = $leave_accrual_settings->seconds . " Hours Per " . $period[$leave_accrual_settings->period] . "<br> Method:" . $options[$leave_accrual_settings->options] . ". ";
        return $details;
    }

}
