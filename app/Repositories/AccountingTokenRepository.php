<?php

namespace App\Repositories;

use App\Models\AccountingToken;
use InfyOm\Generator\Common\BaseRepository;

class AccountingTokenRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'org_str_id',
        'accsoft_id',
        'token',
        'secret_token',
        'refresh_token'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AccountingToken::class;
    }
}
