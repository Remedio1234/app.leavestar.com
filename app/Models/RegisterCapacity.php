<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RegisterCapacity
 * @package App\Models
 * @version July 5, 2017, 11:55 pm UTC
 */
class RegisterCapacity extends Model
{
    use SoftDeletes;

    public $table = 'register_capacity';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'register_id',
        'leave_type_id',
        'capacity'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'register_id' => 'integer',
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

    
}
