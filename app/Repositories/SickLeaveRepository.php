<?php

namespace App\Repositories;

use App\Models\SickLeave;
use App\Models\OrganisationStructure;
use InfyOm\Generator\Common\BaseRepository;

class SickLeaveRepository extends BaseRepository {

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'setting_id',
        'rule_type',
        'value'
    ];

    /**
     * Configure the Model
     * */
    public function model() {
        return SickLeave::class;
    }

    //Check if need to duplicate new setting \
    //@Return $setting /*old setting or new setting */ 
    public function checknew($orgnazation, $setting) {
        if ($orgnazation->setting_new != 1) {
            $new_setting = $setting->replicate();
            $new_setting->push();
            $attach_list = [ 'block_date', 'custom_holiday', 'sick_leave', 'open_hour'];
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
            $orgnazation->setting_id = $new_setting->id;
            $orgnazation->setting_new = 1;
            $orgnazation->save();
            return $new_setting;
        } else {
            return $setting;
        }
    }

    public function create(array $attributes) {

        $org_id = $attributes['org_id'];
        $organisation = OrganisationStructure::where('id', $org_id)->first();
        $setting = \App\Models\Setting::find($organisation->setting_id);

        //If setting is still parent's setting, then duplicate parent's setting first
        //Attach the relationship as well

        $new_setting = $this->checknew($organisation, $setting);
        //add the new record in block date

        if ($attributes['rule_type'] == 1) {
            $sick_leave = SickLeave::create([
                        'setting_id' => $new_setting->id,
                        'rule_type' => $attributes['rule_type'],
                        'value' => $attributes['value']
            ]);
        } else {
            $array = "";
            foreach ($attributes['value'] as $item) {
                $array = $array . $item . ",";
            }
            $sick_leave = SickLeave::create([
                        'setting_id' => $new_setting->id,
                        'rule_type' => $attributes['rule_type'],
                        'value' => $array
            ]);
        }
    }

    public function update(array $attributes, $id) {

        $organisation = OrganisationStructure::where('id', $attributes['org_id'])->first();
        $setting = \App\Models\Setting::find($organisation->setting_id);
        $sick_leaves = SickLeave::find($id);

        //If setting is still parent's setting, then duplicate parent's setting first
        //Attach the relationship as well
        if ($organisation->setting_new != 1) {
            $new_setting = $setting->replicate();
            $new_setting->push();
            $attach_list = [ 'block_date', 'custom_holiday', 'sick_leave', 'open_hour'];
            foreach ($attach_list as $list) {
                if (isset($setting->{$list})) {
                    foreach ($setting->{$list} as $item) {
                        //for each of the child, duplicate it and attach to the parent (setting)
                        $child = $item->replicate();
                        $child->setting()->associate($new_setting);
                        $child->save();
                        if (($list == 'sick_leave') && ($item->id == $id)) {
                            $sick_leaves = $child;
                        }
                    }
                }
            }
            $setting = $new_setting;
            $organisation->setting_id = $new_setting->id;
            $organisation->setting_new = 1;
            $organisation->save();
        }


        if ($attributes['rule_type'] == 1) {
            $result1 = $sick_leaves->update([
                'setting_id' => $setting->id,
                'rule_type' => $attributes['rule_type'],
                'value' => $attributes['value'],
            ]);
        } else {
            $array = "";
            foreach ($attributes['value'] as $item) {
                $array = $array . $item . ",";
            }
            $result1 = $sick_leaves->update([
                'setting_id' => $setting->id,
                'rule_type' => $attributes['rule_type'],
                'value' => $array,
            ]);
        }
    }

    //Delete function is a bit sepecial 
    public function delete($array) {
        $organisation = OrganisationStructure::where('id', $array['org_id'])->first();
        $setting = \App\Models\Setting::find($organisation->setting_id);

        //If setting is still parent's setting, then duplicate parent's setting first
        //Attach the relationship as well
        if ($organisation->setting_new != 1) {
            $new_setting = $setting->replicate();
            $new_setting->push();
            $attach_list = [ 'custom_holiday', 'block_date', 'open_hour'];
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
            //special opration for leave type
            foreach ($setting->sick_leave as $item) {
                //for each of the child, duplicate it and attach to the parent (setting)
                if ($item->id != $array['id']) {
                    $child = $item->replicate();
                    $child->setting()->associate($new_setting);
                    $child->save();
                }
            }
            $organisation->setting_id = $new_setting->id;
            $organisation->setting_new = 1;
            $organisation->save();
        } else {
            SickLeave::find($array['id'])->delete();
        }
    }

}
