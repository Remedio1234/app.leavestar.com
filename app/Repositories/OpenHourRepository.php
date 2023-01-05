<?php

namespace App\Repositories;

use App\Models\OpenHour;
use App\Models\OrganisationStructure;
use InfyOm\Generator\Common\BaseRepository;

class OpenHourRepository extends BaseRepository {

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'setting_id',
        'dayOfWeek',
        'start_time',
        'end_time',
        'numOfHour'
    ];

    /**
     * Configure the Model
     * */
    public function model() {
        return OpenHour::class;
    }

    //Check if need to duplicate new setting \
    //@Return $setting /*old setting or new setting */ 
    public function checknew($orgnazation, $setting) {
        if ($orgnazation->setting_new != 1) {
            $new_setting = $setting->replicate();
            $new_setting->push();
            $attach_list = ['leave_type', 'block_date', 'custom_holiday', 'sick_leave', 'open_hour'];
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

        $dayofweek = $attributes['dayOfWeek'];
        $start = $attributes['start_time'];
        $end = $attributes['end_time'];
        $break_hours = $attributes['breakHours'];
        $break_mins = $attributes['breakMins'];
        foreach ($dayofweek as $day) {
            $open_hour = OpenHour::create([
                        'setting_id' => $new_setting->id,
                        'dayOfWeek' => $day,
                        'start_time' => $start,
                        'end_time' => $end,
                        'breakHours' => $break_hours,
                        'breakMins' => $break_mins,
                        'numOfHours' => round((strtotime($attributes['end_time']) - strtotime($attributes['start_time'])) / 3600, 2),
            ]);
        }
    }

    public function update(array $attributes, $id) {

        $organisation = OrganisationStructure::where('id', $attributes['org_id'])->first();
        $setting = \App\Models\Setting::find($organisation->setting_id);
        $openhours = OpenHour::find($id);
       
        //If setting is still parent's setting, then duplicate parent's setting first
        //Attach the relationship as well
        if ($organisation->setting_new != 1) {
            $new_setting = $setting->replicate();
            $new_setting->push();
            $attach_list = ['leave_type', 'block_date', 'custom_holiday', 'sick_leave', 'open_hour'];
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

        $result1 = $openhours->update([
            'setting_id' => $setting->id,
            'dayOfWeek' => $attributes['dayOfWeek'],
            'start_time' => $attributes['start_time'],
            'end_time' => $attributes['end_time'],
            'breakHours' => $attributes['breakHours'],
            'breakMins' => $attributes['breakMins'],
            'numOfHours' => round((strtotime($attributes['end_time']) - strtotime($attributes['start_time'])) / 3600, 2),
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
            $attach_list = ['leave_type', 'custom_holiday', 'block_date', 'sick_leave'];
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
            foreach ($setting->open_hour as $item) {
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
            OpenHour::find($array['id'])->delete();
        }
    }

}
