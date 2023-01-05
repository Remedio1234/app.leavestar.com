<?php

namespace App\Repositories;

use App\Models\accountingSoftware;
use InfyOm\Generator\Common\BaseRepository;

class accountingSoftwareRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'version'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return accountingSoftware::class;
    }
}
