<?php

namespace App\Repositories;

use App\Models\LeaveAccrualSetting;
use InfyOm\Generator\Common\BaseRepository;

class LeaveAccrualSettingRepository extends BaseRepository {

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'org_id',
        'user_id',
        'leave_type_id',
        'period',
        'seconds',
        'options'
    ];

    /**
     * Configure the Model
     * */
    public function model() {
        return LeaveAccrualSetting::class;
    }

    public function create(array $attributes) {
        $org_id = $attributes['org_id'];
        $leaveaccrual = LeaveAccrualSetting::where([
                    'org_id' => $org_id
                ])->first();
        if (isset($leaveaccrual) || (\App\Models\OrganisationStructure::findRootOrg($org_id) == $org_id)) {
            LeaveAccrualSetting::create([
                'org_id' => $org_id,
                'leave_type_id' => $attributes['leave_type_id'],
                'period' => $attributes['period'],
                'seconds' => $attributes['seconds'] * 3600,
                'options' => $attributes['options'],
            ]);
        } else {
            $root_org = \App\Models\OrganisationStructure::findRootOrg($org_id);
            $leaveaccrual_root = LeaveAccrualSetting::where([
                        'org_id' => $root_org
                    ])->get();
            foreach ($leaveaccrual_root as $item) {
                $new = $item->replicate();
                $new->org_id = $org_id;
                if ($new->leave_type_id == $attributes['leave_type_id']) {
                    $new->period = $attributes['period'];
                    $new->seconds = $attributes['seconds'] * 3600;
                    $new->options = $attributes['options'];
                }
                $new->push();
            }
            LeaveAccrualSetting::updateOrCreate([
                'org_id' => $org_id,
                'leave_type_id' => $attributes['leave_type_id'],
                    ], [
                'period' => $attributes['period'],
                'seconds' => $attributes['seconds'] * 3600,
                'options' => $attributes['options'],
            ]);
        }
    }

    public function update(array $attributes, $id) {

        $find_leavearrual_setting = LeaveAccrualSetting::where([
                    'id' => $id,
                    'org_id' => $attributes['org_id']
                ])->first();
        if (isset($find_leavearrual_setting)) {
            LeaveAccrualSetting::updateOrCreate([
                'id' => $id,
                'org_id' => $attributes['org_id']
                    ], [
                'leave_type_id' => $attributes['leave_type_id'],
                'period' => $attributes['period'],
                'seconds' => $attributes['seconds'] * 3600,
                'options' => $attributes['options'],
            ]);
        } else {
            $root_org = \App\Models\OrganisationStructure::findRootOrg($attributes['org_id']);
            $leaveaccrual_root = LeaveAccrualSetting::where([
                        'org_id' => $root_org
                    ])->get();
            foreach ($leaveaccrual_root as $item) {
                $new = $item->replicate();
                $new->org_id = $attributes['org_id'];
                if ($new->leave_type_id == $attributes['leave_type_id']) {
                    $new->period = $attributes['period'];
                    $new->seconds = $attributes['seconds'] * 3600;
                    $new->options = $attributes['options'];
                }
                $new->push();
            }
            LeaveAccrualSetting::updateOrCreate([
                'org_id' => $attributes['org_id'],
                'leave_type_id' => $attributes['leave_type_id'],
                    ], [
                'period' => $attributes['period'],
                'seconds' => $attributes['seconds'] * 3600,
                'options' => $attributes['options'],
            ]);
        }
    }

    //Delete function is a bit sepecial 
    public function delete($array) {
        $find_leavearrual_setting = LeaveAccrualSetting::where([
                    'id' => $array['id'],
                    'org_id' => $array['org_id'],
                    'user_id' => null
                ])->first();
        if (isset($find_leavearrual_setting)) {
            LeaveAccrualSetting::find($array['id'])->delete();
        } else {
            $root_org = \App\Models\OrganisationStructure::findRootOrg($array['org_id']);
            $leaveaccrual_root = LeaveAccrualSetting::where([
                        'org_id' => $root_org,
                        'user_id' => null
                    ])->get();
            foreach ($leaveaccrual_root as $item) {

                if ($item->id != $array['id']) {
                    $new = $item->replicate();
                    $new->org_id = $array['org_id'];
                    $new->push();
                }
            }
        }
    }

}
