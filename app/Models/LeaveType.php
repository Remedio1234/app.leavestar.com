<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LeaveType
 * @package App\Models
 * @version April 24, 2017, 3:41 am UTC
 */
class LeaveType extends Model {

    use SoftDeletes;

    public $table = 'leave_type';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'org_id',
        'xero_id',
        'ispaidleave',
        'name',
        'description',
        'isshowonpayslip'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * */
    public function leaveApplications() {
        return $this->hasMany(\App\Models\LeaveApplication::class);
    }

}
