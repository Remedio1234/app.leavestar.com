<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrganisationUser
 * @package App\Models
 * @version March 15, 2017, 4:25 am UTC
 */
class OrganisationUser extends Model {

    use SoftDeletes;

    public $table = 'organisation_user';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'org_str_id',
        'user_id',
        'xero_id',
        'is_admin',
        'phone',
        'address',
        'birthday',
        'refresh_token',
        'email_provider',
        'xero_name',
        'start_working_date',
        'birthdayFeedColor',
        'anniversariesFeedColor',
        'birthdayTextColor',
        'anniversaryTextColor'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'org_str_id' => 'integer',
        'user_id' => 'integer'
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
    public function organisationStructure() {
        return $this->belongsTo(\App\Models\OrganisationStructure::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * */
    public function user() {
        return $this->belongsTo(\App\User::class);
    }

    //Check user level
    //@return 1---admin , 2--all level manager , 3-- normal
    public static function checkLevel($user_id, $org_id) {
        if ($user_id == 1) {
            return "1";
        } else {
            if (OrganisationUser::where(['org_str_id' => $org_id, 'user_id' => $user_id])->first()->is_admin == 'yes') {
                if (OrganisationStructure::find($org_id)->parent_id == null) {
                    return "2";
                } else {
                    return "3";
                }
            } else {
                return "4";
            }
        }
    }

    public static function getAccountLevel($user_id, $org_id) {
        $searchResult = \App\Models\OrganisationUser::join('organisation_structure', 'organisation_structure.id', '=', 'organisation_user.org_str_id')
                ->where('organisation_user.user_id', $user_id)
                ->where('organisation_user.is_admin', 'yes')
                ->where('organisation_structure.parent_id', null)
                ->first();
        if (isset($searchResult)) {
            return $searchResult->org_str_id;
        } else {
            return $org_id;
        }
    }

}
