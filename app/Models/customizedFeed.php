<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class customizedFeed
 * @package App\Models
 * @version July 10, 2017, 3:00 am UTC
 */
class customizedFeed extends Model {

    use SoftDeletes;

    public $table = 'customized_feed';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'org_id',
        'user_id',
        'feed',
        'description',
        'feedcolor',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'org_id' => 'integer',
        'user_id' => 'integer',
        'feed' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    public static function getColorFeedList() {
        $colorArray = [
            '0' => 'red',
            '1' => 'blue',
        ];
        return $colorArray;
    }

    public static function getFeedColor($colorId) {
        $colorArray = customizedFeed::getColorFeedList();
        return $colorArray[$colorId];
    }

}
