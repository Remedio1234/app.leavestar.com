<?php

namespace App\Repositories;

use App\Models\RegisterCapacity;
use InfyOm\Generator\Common\BaseRepository;

class RegisterCapacityRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'register_id',
        'leave_type_id',
        'capacity'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RegisterCapacity::class;
    }
}
