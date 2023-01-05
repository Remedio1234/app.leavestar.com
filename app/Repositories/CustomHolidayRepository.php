<?php

namespace App\Repositories;

use App\Models\CustomHoliday;
use App\Models\OrganisationStructure;
use InfyOm\Generator\Common\BaseRepository;

class CustomHolidayRepository extends BaseRepository {

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'setting_id',
        'start_date',
        'end_date',
        'name',
        'description'
    ];

    /**
     * Configure the Model
     * */
    public function model() {
        return CustomHoliday::class;
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

//        //add the new record in block date
        $dates = explode(' - ', $attributes['date_range']);
//      $current_org = \Session::get('current_org');
//        $start_date = \App\Models\Setting::getUctTime($current_org, $dates[0]);
//        $end_date = \App\Models\Setting::getUctTime($current_org, $dates[1]);
        $current_org = \Session::get('current_org');
        $settingId = OrganisationStructure::find($current_org)->setting_id;
        $data_format = \App\Models\Setting::find($settingId)->data_format;
        if ($data_format == 3) {
            $data0 = str_replace('/', '-', $dates[0]);
            $data1 = str_replace('/', '-', $dates[1]);
        } else {
            $data0 = $dates[0];
            $data1 = $dates[1];
        }
        $name = $attributes['name'];
        $des = $attributes['description'];
        $custom_holiday = new CustomHoliday;
        $custom_holiday->setting_id = $new_setting->id;
        $custom_holiday->start_date = date('Y-m-d', strtotime($data0));
        $custom_holiday->end_date = date('Y-m-d H:i:s', strtotime($data1 . ' 23:59:59'));
        $custom_holiday->name = $name;
        $custom_holiday->description = $des;
        $custom_holiday->save();
    }

    public function update(array $attributes, $id) {

        $organisation = OrganisationStructure::where('id', $attributes['org_id'])->first();
        $setting = \App\Models\Setting::find($organisation->setting_id);
        $custom_holiday = CustomHoliday::find($id);

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
                        if (($list == 'custom_holiday') && ($item->id == $id)) {
                            $custom_holiday = $child;
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
        // $start_date = \App\Models\Setting::getUctTime($org_id, $dates[0]);
        // $end_date = \App\Models\Setting::getUctTime($org_id, $dates[1]);
        $current_org = \Session::get('current_org');
        $settingId = OrganisationStructure::find($current_org)->setting_id;
        $data_format = \App\Models\Setting::find($settingId)->data_format;
        if ($data_format == 3) {
            $data0 = str_replace('/', '-', $dates[0]);
            $data1 = str_replace('/', '-', $dates[1]);
        } else {
            $data0 = $dates[0];
            $data1 = $dates[1];
        }

        $result1 = $custom_holiday->update([
            'setting_id' => $setting->id,
            'start_date' => date('Y-m-d', strtotime($data0)),
            'end_date' => date('Y-m-d H:i:s', strtotime($data1 . ' 23:59:59')),
            'name' => $attributes['name'],
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
            $attach_list = [ 'block_date', 'sick_leave', 'open_hour'];
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
            foreach ($setting->custom_holiday as $item) {
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
            CustomHoliday::find($array['id'])->delete();
        }
    }

}
