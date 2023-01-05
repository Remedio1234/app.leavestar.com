<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AccountingToken
 * @package App\Models
 * @version June 14, 2017, 1:13 am UTC
 */
class AccountingToken extends Model {

    use SoftDeletes;

    public $table = 'accounting_token';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'org_str_id',
        'accsoft_id',
        'token',
        'secret_token',
        'refresh_token',
        'xero_org_name',
        'last_check_time',
        'earingrate_id',
        'calendar_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'org_str_id' => 'integer',
        'accsoft_id' => 'integer',
        'token' => 'string',
        'secret_token' => 'string',
        'refresh_token' => 'string'
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
    public function accountingSoftware() {
        return $this->belongsTo(\App\Models\AccountingSoftware::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * */
    public function organisationStructure() {
        return $this->belongsTo(\App\Models\OrganisationStructure::class);
    }

}
