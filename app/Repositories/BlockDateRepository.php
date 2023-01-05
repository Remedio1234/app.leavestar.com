<?php

namespace App\Repositories;

use App\Models\BlockDate;
use App\Models\OrganisationStructure;
use InfyOm\Generator\Common\BaseRepository;

class BlockDateRepository extends BaseRepository {

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'setting_id',
        'start_date',
        'end_date',
        'limits'
    ];

    /**
     * Configure the Model
     * */
    public function model() {
        return BlockDate::class;
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

        $organisation = OrganisationStructure::find($org_id);
        $setting = \App\Models\Setting::find($organisation->setting_id);


        //If setting is still parent's setting, then duplicate parent's setting first
        //Attach the relationship as well

        $new_setting = $this->checknew($organisation, $setting);

        //add the new record in block date
        $dates = explode(' - ', $attributes['date_range']);
        $current_org = \Session::get('current_org');
        $start_date = \App\Models\Setting::getUctTime($current_org, $dates[0]);
        $end_date = \App\Models\Setting::getUctTime($current_org, $dates[1]);

        $limits = $attributes['limits'];
        $des = $attributes['description'];
        $block_date = new BlockDate;
        $block_date->setting_id = $new_setting->id;
        $block_date->start_date = $start_date;
        $block_date->end_date = $end_date;
        $block_date->limits = $limits;
        $block_date->description = $des;
        $block_date->save();
    }

    public function update(array $attributes, $id) {

        $organisation = OrganisationStructure::find($attributes['org_id']);
        $setting = \App\Models\Setting::find($organisation->setting_id);
        $block_date = BlockDate::find($id);

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
                        if (($list == 'block_date') && ($item->id == $id)) {
                            $block_date = $child;
                        }
                    }
                }
            }
            $setting = $new_setting;
            $organisation->setting_id = $new_setting->id;
            $organisation->setting_new = 1;
            $organisation->save();
        }
        $org_id = \Session::get('current_org');
        $dates = explode(' - ', $attributes['date_range']);
        $start_date = \App\Models\Setting::getUctTime($org_id, $dates[0]);
        $end_date = \App\Models\Setting::getUctTime($org_id, $dates[1]);
        $result1 = $block_date->update([
            'setting_id' => $setting->id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'limits' => $attributes['limits'],
            'description' => $attributes['description'],
        ]);
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
            $attach_list = [ 'custom_holiday', 'sick_leave', 'open_hour'];
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
            foreach ($setting->block_date as $item) {
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
            BlockDate::find($array['id'])->delete();
        }
    }

}
