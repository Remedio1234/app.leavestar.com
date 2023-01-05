<?php

namespace App\Repositories;

use App\Models\LeaveCapacity;
use InfyOm\Generator\Common\BaseRepository;

class LeaveCapacityRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'org_id',
        'leave_type_id',
        'capacity',
        'last_update_date'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LeaveCapacity::class;
    }
}
