<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BlockDate
 * @package App\Models
 * @version April 25, 2017, 11:54 pm UTC
 */
class BlockDate extends Model {

    use SoftDeletes;

    private static function getMessage1() {
        return "";
//        return "<span class='glyphicon glyphicon-check'></span> Block Date check successful.";
    }

    private static function getMessage2($capacity, $number, $item, $manager) {
        $org_id = \Session::get('current_org');
        $start = Setting::getLocalTime($org_id, $item->start_date);
        $end = Setting::getLocalTime($org_id, $item->end_date);
        if ($manager) {
            return "<span class='glyphicon glyphicon-exclamation-sign'></span> The requested leave overlaps blocked out dates between " . $start . " and " . $end . ".";
        } else {
            return "<span class='glyphicon glyphicon-exclamation-sign'></span> There is limited leave availability between " . $start . " and " . $end . ".";
        }
    }

    public $table = 'block_date';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'setting_id',
        'start_date',
        'end_date',
        'limits',
        'description',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'setting_id' => 'integer',
        'limits' => 'integer',
        'description' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * */
    public function setting() {
        return $this->belongsTo(\App\Models\Setting::class);
    }

    public static function dateCrossCheck($d1_begin, $d1_end, $d2_begin, $d2_end) {
        if ((strtotime($d1_end) < strtotime($d2_begin)) || ( strtotime($d1_begin) > strtotime($d2_end) )) {
            return false;
        } else {
            return true;
        }
    }

    //Get the Top Tree Node , and the children tree structure
    private static function traverse($categories, &$tree, $level) {
        foreach ($categories as $category) {
            array_push($tree, array(
                'id' => $category->id,
                'name' => $category->name,
                'account_id' => $category->account_id,
                'setting_id' => $category->setting_id,
                'children' => [],
            ));
            if ($category->children->count() != 0) {
                BlockDate::traverse($category->children, $tree[count($tree) - 1]['children'], $level + 1, $category);
            }
        }
    }

    private static function getChildTree($org_id) {
        $account_id = OrganisationStructure::find($org_id)->account_id;
        $tree = [];
        $nodes = OrganisationStructure::scoped(['account_id', $account_id])->descendantsOf($org_id);
        BlockDate::traverse($nodes, $tree, 1);
        $array = [];
        foreach ($tree as $item) {
            $array[] = $item['id'];
        }
        $array[] = intval($org_id);
        return $array;
    }

    public static function checkBlockDateRule($start_date, $end_date, $org_id, $manager = false, $exceptionLeaveID = null) {
        $setting_id = OrganisationStructure::find($org_id)->setting_id;
        $block_dates = BlockDate::where('setting_id', $setting_id)->get();
        $tree = BlockDate::getChildTree($org_id);
        foreach ($block_dates as $item) {
            if (BlockDate::dateCrossCheck($start_date, $end_date, $item->start_date, $item->end_date)) {
                $capacity = $item->limits;
                //if (isset($exceptionLeaveID)) {
                $numbers_temp = LeaveApplication::where(['status' => '1'])->where('id', '!=', $exceptionLeaveID);
                //} else {
                //   $numbers_temp = LeaveApplication::where('status', '1');
                // }
                $number = $numbers_temp->where(function ($query) use ($item) {
                                    $query->whereBetween('start_date', [$item->start_date, $item->end_date])
                                    ->orWhere(function ($query) use ($item) {
                                        $query->whereBetween('end_date', [$item->start_date, $item->end_date]);
                                    });
                                })->where('user_id', '!=', \Auth::user()->id)
                                ->whereIn('org_id', $tree)->get()->count();

                if ($capacity < ($number + 1)) {
                    $array['status'] = "failed";
                    $message2 = BlockDate::getMessage2($capacity, $number, $item, $manager);
                    $array['message'] = $message2;
                    return $array;
                }
            }
        }
        $array['status'] = "success";
        $array['message'] = BlockDate::getMessage1();
        return $array;
    }

    public static function checkBelonging($id, $org_id) {
        $current_org = \Session::get('current_org');
        $setting_id = BlockDate::find($id)->setting_id;
        $org = OrganisationStructure::where(['id' => $org_id, 'setting_id' => $setting_id])->first();
        $result = \App\User::checkUserRole((\Auth::user()->id), $current_org);
        return (isset($org) && $result);
    }

}
