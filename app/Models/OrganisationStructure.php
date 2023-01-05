<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use App\User;

/**
 * Class OrganisationStructure
 * @package App\Models
 * @version March 15, 2017, 3:44 am UTC
 */
class OrganisationStructure extends Model {

    use NodeTrait;

use SoftDeletes;

    public $table = 'organisation_structure';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'name',
        '_lft',
        '_rgt',
        'parent_id',
        'setting_id',
        'account_id',
        'setting_new',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        '_lft' => 'integer',
        '_rgt' => 'integer',
        'parent_id' => 'integer',
        'setting_id' => 'integer',
        'account_id' => 'integer',
        'setting_new' => 'integer',
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
    public function account() {
        return $this->belongsTo(\App\Models\Account::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * */
    public function setting() {
        return $this->belongsTo(\App\Models\Setting::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * */
    public function accountingTokens() {
        return $this->hasMany(\App\Models\AccountingToken::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * */
    public function blockDates() {
        return $this->hasMany(\App\Models\BlockDate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * */
    public function customHolidays() {
        return $this->hasMany(\App\Models\CustomHoliday::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * */
    public function leaveTypes() {
        return $this->hasMany(\App\Models\LeaveType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * */
    public function organisationUsers() {
        return $this->hasMany(\App\Models\OrganisationUser::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * */
    public function sickLeaves() {
        return $this->hasMany(\App\Models\SickLeave::class);
    }

//    public function seekparent($node) {
//        if ($node->setting_id != null) {
//            return $node->setting_id;
//        } else {
//            $result = OrganisationStructure::ancestorsOf($node->id);
//            foreach ($result as $item) {
//                $single = $item;
//            }
//            $result3 = $this->seekparent($single);
//            return $result3;
//        }
//    }
    //Find the Org's root org
    //return [0] if it's the root itself
    public static function findRootOrg($org_id) {
        $account_id = \App\Models\OrganisationStructure::where('id', $org_id)->first()->account_id;
        $ancestors = \App\Models\OrganisationStructure::scoped(['account_id' => $account_id])->ancestorsOf($org_id);

        if (isset($ancestors[1])) {
            $root_org = ($ancestors[1]->parent_id == null) ? $ancestors[0]->id : $ancestors[1]->id;
        } else {
            if (isset($ancestors[0])) {
                $root_org = $org_id;
            } else {
                $root_org = null;
            }
        }

        return $root_org;
    }

    public static function isOrgRoot($org_id) {
        $parent = OrganisationStructure::find($org_id)->parent_id;
        if (isset($parent)) {
            $result = OrganisationStructure::isAccountRoot($parent);
            return $result;
        } else {
            return false;
        }
    }

    public static function isAccountRoot($org_id) {
        if (OrganisationStructure::find($org_id)->parent_id == null) {
            return true;
        } else {
            return false;
        }
    }

}
