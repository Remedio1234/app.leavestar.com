<?php

namespace App\Repositories;

use App\Models\OrganisationUser;
use InfyOm\Generator\Common\BaseRepository;

class OrganisationUserRepository extends BaseRepository {

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'org_str_id',
        'user_id'
    ];

    /**
     * Configure the Model
     * */
    public function model() {
        return OrganisationUser::class;
    }

    //Four table need to be changed accordingly
    public function update(array $attributes, $id) {
        $org_user = OrganisationUser::find($id);
        $user_id = $org_user->user_id;
        $old_org_id = $org_user->org_str_id;

        $needCreate = true;
        if (\App\Models\OrganisationStructure::find($old_org_id)->parent_id == null) {
            $searchResult = \App\Models\OrganisationUser::whereIn('org_str_id', $attributes['tree'])->where([
                        'user_id' => $user_id,
                    ])->where('org_str_id', '<>', $old_org_id)->first();
            if (isset($searchResult)) {
                $needCreate = false;
            }
        } else {
            $needCreate = false;
        }

        if ($needCreate) {
            //Create a new org_user 
            $new_org_user = $org_user->replicate();
            $new_org_user->org_str_id = $attributes['org_str_id'];
            $new_org_user->is_admin = $attributes['is_admin'];
            $new_org_user->save();

            //User ->last_visit_org
            $user = \App\User::find($user_id);
            $user->last_visit_org = $attributes['org_str_id'];
            $user->save();
        } else {
            //Update the org_user
            //User ->last_visit_org
            $user = \App\User::find($user_id);
            $user->last_visit_org = $attributes['org_str_id'];
            $user->save();

            //Leave capacity
            \App\Models\LeaveCapacity::where([
                'user_id' => $user_id,
                'org_id' => $old_org_id
            ])->update([
                'org_id' => $attributes['org_str_id']
            ]);

            //Leave application
            \App\Models\LeaveApplication::where([
                'user_id' => $user_id,
                'org_id' => $old_org_id
            ])->update([
                'org_id' => $attributes['org_str_id']
            ]);

            //leave accrualing setting
            \App\Models\LeaveAccrualSetting::where([
                'user_id' => $user_id,
                'org_id' => $old_org_id
            ])->update([
                'org_id' => $attributes['org_str_id']
            ]);

            //Finally org_user table
            $org_user->org_str_id = $attributes['org_str_id'];
            $org_user->is_admin = $attributes['is_admin'];
            $org_user->save();
        }



        if ($user_id == \Auth::user()->id) {
            \Session::set('current_org', $attributes['org_str_id']);
        }
    }

    public function delete($id) {
        $org_user = OrganisationUser::find($id);
        //User ->last_visit_org
        $user = \App\User::find($org_user->user_id);
        $user->last_visit_org = null;
        $user->save();
        //Leave capacity
        $leave_capacity = \App\Models\LeaveCapacity::where([
                    'user_id' => $org_user->user_id,
                    'org_id' => $org_user->org_str_id,
                ])->delete();
        //Leave application
        $leave_application = \App\Models\LeaveApplication::where([
                    'user_id' => $org_user->user_id,
                    'org_id' => $org_user->org_str_id,
                ])->delete();
        //leave accrualing setting
        $leave_acc = \App\Models\LeaveAccrualSetting::where([
                    'user_id' => $org_user->user_id,
                    'org_id' => $org_user->org_str_id,
                ])->delete();

        //Finally org_user table
        $org_user->delete();
    }

}
