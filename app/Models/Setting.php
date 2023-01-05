<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Setting
 * @package App\Models
 * @version March 15, 2017, 12:09 am UTC
 */
class Setting extends Model {

    use SoftDeletes;

    public $table = 'setting';

    private static function getMessage1() {
        return "<span class='glyphicon glyphicon-check'></span> There are not too many people on leave during this time.";
    }

    private static function getMessage2($start, $end, $rules, $number) {
//        return "The selected dates may be against the leave date rule. Only " . $rules . " people are allowed to take leave during " . $start . " To " . $end . ". Now there are " . $number;
        return "<span class='glyphicon glyphicon-exclamation-sign'></span> There appears to be too many people on leave at the same time.";
    }

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'logo',
        'data_format',
        'timezone',
        'leave_rules',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'logo' => 'string',
        'timezone' => 'string',
        'leave_rules' => 'integer',
        'data_format' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    public static function DataFormatFrontEnd() {
        $array = [
            '1' => 'Y-MM-DD',
            '2' => 'MM/DD/Y',
            '3' => 'DD/MM/Y',
        ];
        return $array;
    }

    public static function DataFormatBackEnd() {
        $array = [
            '1' => 'Y-m-d',
            '2' => 'm/d/Y',
            '3' => 'd/m/Y',
        ];
        return $array;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * */
    public static function getUctTime($org_id, $dataTime) {
        $setting_id = OrganisationStructure::find($org_id)->setting_id;
        $timezone = Setting::find($setting_id)->timezone;
        $localzone = new \DateTimeZone($timezone);
        $uctzone = new \DateTimeZone('UCT');

        $format = \App\Models\Setting::getBackEndDF($org_id) . ' h:i A';
        $time = \DateTime::createFromFormat($format, $dataTime, $localzone);
        $time->setTimezone($uctzone);
        $time = $time->format('Y-m-d H:i:s');
        return $time;
    }

    public static function getLocalTime($org_id, $dataTime, $orgFormat = true) {

//        if (isset($_SESSION['datetime_settings'][$org_id])) {
//            $format = $_SESSION['datetime_settings'][$org_id]['format'];
//            $timezone = $_SESSION['datetime_settings'][$org_id]['timezone'];
//        } else {
//            $_SESSION['datetime_settings'][$org_id] = array();
//            $setting_id = OrganisationStructure::find($org_id)->setting_id;
//            $timezone = $_SESSION['datetime_settings'][$org_id]['timezone'] = Setting::find($setting_id)->timezone;
//            $format = $_SESSION['datetime_settings'][$org_id]['format'] = \App\Models\Setting::getBackEndDF($org_id) . ' h:i A';
//        }        
        $setting_id = OrganisationStructure::find($org_id)->setting_id;
        $timezone = Setting::find($setting_id)->timezone;
        $format = \App\Models\Setting::getBackEndDF($org_id) . ' h:i A';
        $localzone = new \DateTimeZone($timezone);
        $uctzone = new \DateTimeZone('UCT');
        $time = \DateTime::createFromFormat('Y-m-d H:i:s', $dataTime, $uctzone);
        $time->setTimezone($localzone);
        $time = $time->format($orgFormat ? $format : 'Y-m-d H:i:s');
        return $time;
    }

    public static function getFrontEndDF($org_id) {
        $setting_id = OrganisationStructure::find($org_id)->setting_id;
        $data_format = Setting::find($setting_id)->data_format;
        $array = setting::DataFormatFrontEnd();
        return $array[$data_format];
    }

    public static function getBackEndDF($org_id) {
        $setting_id = OrganisationStructure::find($org_id)->setting_id;
        $data_format = Setting::find($setting_id)->data_format;
        $array = setting::DataFormatBackEnd();
        return $array[$data_format];
    }

    public static function getDataFormat() {
        $array = [
            '1' => 'Y-m-d  Example: ' . Date('Y-m-d'),
            '2' => 'm/d/Y  Example: ' . Date('m/d/Y'),
            '3' => 'd/m/Y  Example: ' . Date('d/m/Y'),
        ];
        return $array;
    }

    public function block_date() {
        return $this->hasMany(\App\Models\BlockDate::class);
    }

    public function custom_holiday() {
        return $this->hasMany(\App\Models\CustomHoliday::class);
    }

    public function sick_leave() {
        return $this->hasMany(\App\Models\SickLeave::class);
    }

    //Get the Top Tree Node , and the children tree structure
    private static function traverse($categories, &$tree, $level) {
        foreach ($categories as $category) {
            array_push($tree, array(
                'id' => $category->id,
                'name' => $category->name,
                'account_id' => $category->account_id,
                'setting_id' => $category->setting_id,
                'children' => [],
            ));
            if ($category->children->count() != 0) {
                Setting::traverse($category->children, $tree[count($tree) - 1]['children'], $level + 1, $category);
            }
        }
    }

    public static function GetDays($sStartDate, $sEndDate) {
        // Firstly, format the provided dates.  
        // This function works best with YYYY-MM-DD  
        // but other date formats will work thanks  
        // to strtotime().  
        $sStartDate = gmdate("Y-m-d H:i:s", strtotime($sStartDate));
        $sEndDate = gmdate("Y-m-d H:i:s", strtotime($sEndDate));

        // Start the variable off with the start date
        $sTempStart = $sStartDate;
        $sTempEnd = gmdate("Y-m-d H:i:s", strtotime("tomorrow -1 second", strtotime($sStartDate)));
        $aDays[] = ['start' => $sTempStart, 'end' => $sTempEnd];
        $sTempStart = gmdate("Y-m-d H:i:s", strtotime("+1 second", strtotime($sTempEnd)));

        while ($sTempStart < $sEndDate) {
            $sTempEnd = gmdate("Y-m-d H:i:s", strtotime("tomorrow -1 second", strtotime($sTempStart)));
            $aDays[] = ['start' => $sTempStart, 'end' => $sTempEnd];
            $sTempStart = gmdate("Y-m-d H:i:s", strtotime("+1 second", strtotime($sTempEnd)));
        }
        // Once the loop has finished, return the  
        // array of days.  
        $aDays[sizeof($aDays) - 1]['end'] = $sEndDate;
        return $aDays;
    }

    private static function getChildTree($org_id) {
        $account_id = OrganisationStructure::find($org_id)->account_id;
        $tree = [];
        $nodes = OrganisationStructure::scoped(['account_id', $account_id])->descendantsOf($org_id);
        Setting::traverse($nodes, $tree, 1);
        $array = [];
        foreach ($tree as $item) {
            $array[] = $item['id'];
        }
        $array[] = intval($org_id);
        return $array;
    }

    public static function checkGeneralLeaveRule($start_date, $end_date, $org_id, $manager = false, $exceptionLeaveID = null) {

        $setting_id = OrganisationStructure::find($org_id)->setting_id;
        $leave_rules = Setting::find($setting_id)->leave_rules;

        $days = Setting::GetDays($start_date, $end_date);

        foreach ($days as $day_p) {
            $numbers_temp = LeaveApplication::where('status', '1')->where('id', '!=', $exceptionLeaveID);

            $number = $numbers_temp->where(function ($query) use ($day_p) {
                                $query->whereBetween('start_date', [$day_p['start'], $day_p['end']])
                                ->orWhere(function ($query) use ($day_p) {
                                    $query->whereBetween('end_date', [$day_p['start'], $day_p['end']])
                                    ->orWhere(function ($query) use ($day_p) {
                                        $query->where('start_date', '<', $day_p['start'])
                                        ->where('end_date', '>', $day_p['end']);
                                    });
                                });
                            })->where('user_id', '!=', \Auth::user()->id)
                            ->where('org_id', $org_id)->count();


            if ($number + 1 > $leave_rules) {
                $array['status'] = "failed";
                $localDateFind1 = Setting::getLocalTime($org_id, $day_p['start'], false);
                $localDateFind2 = Setting::getLocalTime($org_id, $day_p['end'], false);
                $message2 = Setting::getMessage2($localDateFind1, $localDateFind2, $leave_rules, $number);
                $array['message'] = $message2;
                return $array;
            }
        }
        $array['status'] = "success";
        $array['message'] = Setting::getMessage1();
        return $array;
    }

}
