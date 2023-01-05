<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserRegister
 * @package App\Models
 * @version June 19, 2017, 4:42 am UTC
 */
class UserRegister extends Model {

    use SoftDeletes;

    public $table = 'user_register';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'org_id',
        'is_admin',
        'token',
        'email',
        'name',
        'address',
        'phone',
        'birthday',
        'xero_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'org_id' => 'integer',
        'is_admin' => 'string',
        'token' => 'string',
        'email' => 'string',
        'name' => 'string',
        'address' => 'string',
        'phone' => 'string',
        'xero_id' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
         
    ];

}
