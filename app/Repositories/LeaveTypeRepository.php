<?php

namespace App\Repositories;

use App\Models\LeaveType;
use App\Models\OrganisationStructure;
use InfyOm\Generator\Common\BaseRepository;

class LeaveTypeRepository extends BaseRepository {

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'setting_id',
        'name',
        'description'
    ];

    /**
     * Configure the Model
     * */
    public function model() {
        return LeaveType::class;
    }

    public function create(array $attributes) {
        $org_id = $attributes['org_id'];
        $root_org = OrganisationStructure::findRootOrg($org_id);
        //add the new record in leave type 
        $name = $attributes['name'];
        $des = $attributes['description'];
        $ispaid = $attributes['ispaidleave'];
        $leave_type = new LeaveType;
        $leave_type->org_id = $root_org;
        $leave_type->name = $name;
        $leave_type->description = $des;
        $leave_type->ispaidleave = $ispaid;
        $leave_type->save();
    }

    //Delete function is a bit sepecial 
    public function delete($array) {
        LeaveType::find($array['id'])->delete();

        //\App\Models\LeaveApplication::where('leave_type_id', $array['id'])->delete();
        \App\Models\LeaveAccrualSetting::where('leave_type_id', $array['id'])->delete();
        \App\Models\LeaveCapacity::where('leave_type_id', $array['id'])->delete();
        \App\Models\LeaveCapacity::where('leave_type_id', $array['id'])->delete();
    }

}
