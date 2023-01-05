<?php

namespace App\Repositories;

use App\Models\UserRegister;
use InfyOm\Generator\Common\BaseRepository;

class UserRegisterRepository extends BaseRepository {

    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Configure the Model
     * */
    public function model() {
        return UserRegister::class;
    }

    //Generate Secure Token
    public function generateToken($length = "32") {
        return \App\Models\SecureToken::getToken($length);
    }

    public function create(array $attributes) {

        $user_register = UserRegister::create([
                    'org_id' => $attributes['org_id'],
                    'is_admin' => $attributes['is_admin'],
                    'email' => $attributes['email'],
                    'name' => $attributes['name'],
                    'birthday' => $attributes['birthday'],
                    'token' => $this->generateToken()
        ]);
        foreach ($attributes as $key => $value) {

            if (strpos($key, 'hours') !== false) {
                $array = explode('_', $key);
                $leavetype_id = $array[1];


                $register_capacity = \App\Models\RegisterCapacity::create([
                            'register_id' => $user_register->id,
                            'leave_type_id' => $leavetype_id,
                            'capacity' => $value * 3600
                ]);
            }
        }

        return $user_register;
    }

}
