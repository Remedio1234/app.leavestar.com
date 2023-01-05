<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LeaveApplication
 * @package App\Models
 * @version June 1, 2017, 12:45 am UTC
 */
class LeaveApplication extends Model {

    use SoftDeletes;

    public $table = 'leave_application';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'user_id',
        'org_id',
        'start_date',
        'end_date',
        'leave_type_id',
        'flexible',
        'xero_id',
        'autoreplysetting',
        'autoreplymessage',
        'rule_check',
        'need_alert',
        'notificationID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'leave_type_id' => 'integer',
        'flexible' => 'integer',
        'status' => 'string'
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
    public function leaveType() {
        return $this->belongsTo(\App\Models\LeaveType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * */
    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * */
    public function comments() {
        return $this->hasMany(\App\Models\Comment::class);
    }

    private static function isHoliday($date, $setting_id) {
        $dayofweek = date('N', strtotime($date));
        $open_hour_setting = OpenHour::where([
                    'dayOfWeek' => $dayofweek,
                    'setting_id' => $setting_id
                ])->first();
        $public_holiday = CustomHoliday::where([
                    'setting_id' => $setting_id,
                ])->where('start_date', '<=', date('Y-m-d H:i:s', strtotime($date)))->where('end_date', '>=', date('Y-m-d H:i:s', strtotime($date)))->first();
        if (isset($open_hour_setting) && !isset($public_holiday)) {
            return false;
        } else {
            return true;
        }
    }

    public static function CalLeaveUnit($org_id, $start_date, $end_time) {
        $unit = 0;

        $setting_id = OrganisationStructure::find($org_id)->setting_id;
        $Starttime = Setting::getLocalTime($org_id, $start_date, false);
        $Endtime = Setting::getLocalTime($org_id, $end_time, false);

        //IF it's few hours leave , start day with the same date of end day
        if (date('d M', strtotime($Starttime)) == date('d M', strtotime($Endtime))) {
            $dayofweek = date('N', strtotime($Starttime));
            $open_hour_setting = OpenHour::where([
                        'dayOfWeek' => $dayofweek,
                        'setting_id' => $setting_id
                    ])->first();
            if (!LeaveApplication::isHoliday($Starttime, $setting_id)) {
                $time = date('H:i:s', strtotime($Starttime));
                $time2 = date('H:i:s', strtotime($Endtime));
                if ((strtotime($time)) <= (strtotime($open_hour_setting->start_time))) {
                    if ((strtotime($time2)) <= (strtotime($open_hour_setting->start_time))) {
                        $unit = $unit + 0;
                    }
                    if (((strtotime($time2)) > (strtotime($open_hour_setting->start_time))) && ((strtotime($time2)) < (strtotime($open_hour_setting->end_time)))) {
                        $unit = $unit + (strtotime($time2) - strtotime($open_hour_setting->start_time) );
                    }
                    if ((strtotime($time2)) >= (strtotime($open_hour_setting->end_time))) {
                        $unit = $unit + ($open_hour_setting->numOfHours * 3600 - (($open_hour_setting->breakHours) * 60 + $open_hour_setting->breakMins) * 60);
                    }
                }
                if (((strtotime($time)) > (strtotime($open_hour_setting->start_time))) && ((strtotime($time)) < (strtotime($open_hour_setting->end_time)))) {
                    if (((strtotime($time2)) > (strtotime($open_hour_setting->start_time))) && ((strtotime($time2)) < (strtotime($open_hour_setting->end_time)))) {
                        $unit = $unit + (strtotime($time2) - strtotime($time));
                    }
                    if ((strtotime($time2)) >= (strtotime($open_hour_setting->end_time))) {
                        $unit = $unit + (strtotime($open_hour_setting->end_time) - strtotime($time));
                    }
                }
                if ((strtotime($time)) >= (strtotime($open_hour_setting->end_time))) {
                    $unit = $unit + 0;
                }
            }
            return $unit;
        }

        //Deal with start date
        $dayofweek = date('N', strtotime($Starttime));
        $open_hour_setting = OpenHour::where([
                    'dayOfWeek' => $dayofweek,
                    'setting_id' => $setting_id
                ])->first();

        if (!LeaveApplication::isHoliday($Starttime, $setting_id)) {
            $time = date('H:i:s', strtotime($Starttime));
            if ((strtotime($time)) <= (strtotime($open_hour_setting->start_time))) {
                $unit = $unit + ($open_hour_setting->numOfHours * 3600 - (($open_hour_setting->breakHours) * 60 + $open_hour_setting->breakMins) * 60);
            }
            if (((strtotime($time)) > (strtotime($open_hour_setting->start_time))) && ((strtotime($time)) < (strtotime($open_hour_setting->end_time)))) {
                $unit = $unit + (strtotime($time) - strtotime($open_hour_setting->start_time) );
            }
            if ((strtotime($time)) >= (strtotime($open_hour_setting->end_time))) {
                $unit = $unit + 0;
            }
        }


        //Deal with all the day in middels
        $nextday = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime('+1 day', strtotime($Starttime)))));

        while (date('Y-m-d', strtotime($nextday)) < date('Y-m-d', strtotime($Endtime))) {
            $dayofweek = date('N', strtotime($nextday));
            $open_hour_setting = OpenHour::where([
                        'dayOfWeek' => $dayofweek,
                        'setting_id' => $setting_id
                    ])->first();
            if (!LeaveApplication::isHoliday($nextday, $setting_id)) {
                $unit = $unit + ($open_hour_setting->numOfHours * 3600 - (($open_hour_setting->breakHours) * 60 + $open_hour_setting->breakMins) * 60);
            }

            $nextday = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($nextday)));
        }

        //Deal with last day

        if (date('Y-m-d', strtotime($Starttime)) != date('Y-m-d', strtotime($Endtime))) {
            $dayofweek = date('N', strtotime($Endtime));
            $open_hour_setting = OpenHour::where([
                        'dayOfWeek' => $dayofweek,
                        'setting_id' => $setting_id
                    ])->first();
            if (!LeaveApplication::isHoliday($Endtime, $setting_id)) {
                $time = date('H:i:s', strtotime($Endtime));

                if ((strtotime($time)) <= (strtotime($open_hour_setting->start_time))) {
                    $unit = $unit + 0;
                }
                if (((strtotime($time)) > (strtotime($open_hour_setting->start_time))) && ((strtotime($time)) < (strtotime($open_hour_setting->end_time)))) {
                    $unit = $unit + (strtotime($time) - strtotime($open_hour_setting->start_time) );
                }
                if ((strtotime($time)) >= (strtotime($open_hour_setting->end_time))) {
                    $unit = $unit + ($open_hour_setting->numOfHours * 3600 - (($open_hour_setting->breakHours) * 60 + $open_hour_setting->breakMins) * 60);
                }
            }
        }
        return $unit;
    }

}
