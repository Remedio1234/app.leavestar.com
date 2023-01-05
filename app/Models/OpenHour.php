<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OpenHour
 * @package App\Models
 * @version June 7, 2017, 12:12 am UTC
 */
class OpenHour extends Model {

    use SoftDeletes;

    public $table = 'open_hour';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'setting_id',
        'dayOfWeek',
        'start_time',
        'end_time',
        'numOfHours',
        'breakHours',
        'breakMins'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'setting_id' => 'integer',
        'dayOfWeek' => 'integer',
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

    public static function create_time_range($start, $end, $interval = '30 mins', $format = '12') {
        $startTime = strtotime($start);
        $endTime = strtotime($end);
        $returnTimeFormat = ($format == '12') ? 'g:i:s A' : 'G:i:s';

        $current = time();
        $addTime = strtotime('+' . $interval, $current);
        $diff = $addTime - $current;

        $times = array();
        while ($startTime < $endTime) {
            $times[date($returnTimeFormat, $startTime)] = date($returnTimeFormat, $startTime);
            $startTime += $diff;
        }
        $times[date($returnTimeFormat, $startTime)] = date($returnTimeFormat, $startTime);
        return $times;
    }

    public static function checkBelonging($id, $org_id) {
        $current_org = \Session::get('current_org');
        $setting_id = OpenHour::find($id)->setting_id;
        $org = OrganisationStructure::where(['id' => $org_id, 'setting_id' => $setting_id])->first();
        $result = \App\User::checkUserRole((\Auth::user()->id), $current_org);
        return (isset($org) && $result);
    }

}
