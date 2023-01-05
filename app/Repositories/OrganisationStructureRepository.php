<?php

namespace App\Repositories;

use App\Models\OrganisationStructure;
use InfyOm\Generator\Common\BaseRepository;

class OrganisationStructureRepository extends BaseRepository {

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        '_lft',
        '_rgt',
        'parent_id',
        'setting_id'
    ];

    /**
     * Configure the Model
     * */
    public function model() {
        return OrganisationStructure::class;
    }

    public function update(array $attributes, $id) {

        $organisation = OrganisationStructure::where('id', $id)->first();
        $setting = \App\Models\Setting::find($organisation->setting_id);

        //If setting is still parent's setting, then duplicate parent's setting first
        //Attach the relationship as well
        if ($organisation->setting_new != 1) {
            $new_setting = $setting->replicate();
            $new_setting->push();
            $attach_list = ['leave_type', 'block_date', 'custom_holiday', 'sick_leave'];
            foreach ($attach_list as $list) {
                if (isset($setting->{$list})) {
                    foreach ($setting->{$list} as $item) {
                        //for each of the child, duplicate it and attach to the parent (setting)
                        $child = $item->replicate();
                        $child->setting()->associate($new_setting);
                        $child->save();
                    }
                }
            }
            $setting = $new_setting;
            $organisation->setting_id = $new_setting->id;
            $organisation->setting_new = 1;
            $organisation->save();
        }
        //update the fields in setting
        $result1 = $setting->update([
            'leave_rules' => $attributes['setting']['leave_rules'],
            'timezone' => $attributes['setting']['timezone'],
            'data_format' => $attributes['setting']['data_format'],
        ]);
        //update the name field 
        $result2 = $organisation->update([
            'name' => $attributes['name'],
        ]);
    }

}
