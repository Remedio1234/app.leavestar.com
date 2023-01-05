<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CustomHoliday
 * @package App\Models
 * @version May 31, 2017, 2:43 am UTC
 */
class CustomHoliday extends Model {

    use SoftDeletes;

    public $table = 'custom_holidays';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'setting_id',
        'start_date',
        'end_date',
        'name',
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'setting_id' => 'integer',
        'name' => 'string',
        'description' => 'string'
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
        $setting_id = CustomHoliday::find($id)->setting_id;
        $org = OrganisationStructure::where(['id' => $org_id, 'setting_id' => $setting_id])->first();
        $result = \App\User::checkUserRole((\Auth::user()->id), $current_org);
        return (isset($org) && $result);
    }

}
