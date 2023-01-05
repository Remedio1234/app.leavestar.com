<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SickLeave
 * @package App\Models
 * @version May 31, 2017, 2:43 am UTC
 */
class SickLeave extends Model {

    use SoftDeletes;

    public $table = 'sick_leave';

    private static function getMessage1() {
        return "Sick Leave check successful.";
    }

    private static function getMessage2() {
        return "<span class='glyphicon glyphicon-exclamation-sign'></span> Medical Certificate required.";
    }

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'setting_id',
        'rule_type',
        'value'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'setting_id' => 'integer',
        'rule_type' => 'integer',
        'value' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * */
    public function setting() {
        return $this->belongsTo(\App\Models\Setting::class);
    }

    public static function checkBelonging($id, $org_id) {
        $current_org = \Session::get('current_org');
        $setting_id = SickLeave::find($id)->setting_id;
        $org = OrganisationStructure::where(['id' => $org_id, 'setting_id' => $setting_id])->first();
        $result = \App\User::checkUserRole((\Auth::user()->id), $current_org);
        return (isset($org) && $result);
    }

    public static function checkSickLeaveRule($date_start, $date_end, $org_id, $manager = false) {
        $setting_id = OrganisationStructure::find($org_id)->setting_id;
        $sickleave_settings = SickLeave::where('setting_id', $setting_id)->get();
        $need_provide = false;
        foreach ($sickleave_settings as $item) {
            //Number of day in row
            if ($item->rule_type == 1) {
                $start = strtotime($date_start);
                $end = strtotime($date_end);
                $datediff = floor(($end - $start) / (60 * 60 * 24));
                if ($datediff >= $item->value) {
                    $need_provide = true;
                }
            }
            //Days
            if ($item->rule_type == 0) {
                $array = explode(",", $item->value);
                $temp_start = strtotime($date_start);
                $temp_end = strtotime($date_end);
                while ($temp_start < $temp_end) {
                    $dateofweek = date('N', $temp_start);
                    if (in_array($dateofweek, $array)) {
                        $need_provide = true;
                        break;
                    }
                    $temp_start = strtotime('+1 day', $temp_start);
                }
            }
            if ($need_provide == true) {
                break;
            }
        }
        if ($need_provide == false) {
            $array['status'] = "success";
            $array['message'] = SickLeave::getMessage1();
            return $array;
        } else {
            $array['status'] = "failed";
            $message2 = SickLeave::getMessage2();
            $array['message'] = $message2;
            return $array;
        }
    }

}
