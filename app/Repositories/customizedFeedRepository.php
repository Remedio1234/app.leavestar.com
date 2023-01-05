<?php

namespace App\Repositories;

use App\Models\customizedFeed;
use InfyOm\Generator\Common\BaseRepository;

class customizedFeedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'org_id',
        'user_id',
        'feed'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return customizedFeed::class;
    }
}
