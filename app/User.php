<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\LeaveApplication;
use App\Notifications\ResetPassword as ResetPasswordNotification;

/**
 * App\User
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
 * @method static \Illuminate\Database\Query\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'last_visit_org', 'profile_pic', 'receiveEmailNotification', 'tourGuide'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    public static function checkUserRole($user_id, $org_id) {
        $org_user = Models\OrganisationUser::where(['user_id' => $user_id, 'org_str_id' => $org_id])->first();

        return ($org_user->is_admin == 'yes');
    }

    public function getProfileImg() {
        return \Storage::get($this->profile_pic);
    }

    public function sendLeaveApplicationNotification($application, $type = 'create') {

        $current_org = \Session::get('current_org');

        if ($type == 'create') {
            $parent_org = Models\OrganisationStructure::find($current_org)->parent_id;
            $org_user = Models\OrganisationUser::where([ 'org_str_id' => $parent_org, 'is_admin' => 'yes'])->orWhere(['org_str_id' => $current_org, 'is_admin' => 'yes'])->get();

            foreach ($org_user as $manager) {
                $userTo = \App\User::find($manager->user_id);
                $userTo->notify(new \App\Notifications\LeaveApplicationNotification($application, $type, $this->id, $userTo));
            }
        } else {
            $userTo = \App\User::find($application->user_id);
            $userTo->notify(new \App\Notifications\LeaveApplicationNotification($application, $type, $this->id, $userTo));
        }
    }

    public function sendCommentNotification($comment) {

        $currentUser = \Auth::user()->id;

        $leaveApplication = Models\LeaveApplication::find($comment->leave_id);

        $userOfLeave = $leaveApplication->user_id;

        if ($currentUser == $userOfLeave) {
            $current_org = \Session::get('current_org');
            $parent_org = Models\OrganisationStructure::find($current_org)->parent_id;
            $org_user = Models\OrganisationUser::where([ 'org_str_id' => $parent_org, 'is_admin' => 'yes'])->orWhere(['org_str_id' => $current_org, 'is_admin' => 'yes'])->get();
            $emails = [];
            foreach ($org_user as $manager) {
                $userTo = \App\User::find($manager->user_id);
                if(!in_array($userTo->email, $emails)){
                    $userTo->notify(new Notifications\CommentsNotification($leaveApplication, $currentUser, 'comment'));
                    $emails[] = $userTo->email;
                }
            }
        } else {
            $userTo = \App\User::find($userOfLeave);
            $userTo->notify(new Notifications\CommentsNotification($leaveApplication, $currentUser, 'comment'));
        }
    }

    public static function XeronotificationMessage() {

        return 'Xero need to be synchronised again.';
    }

    public function sendXeroNotification($org_id, $type) {
        $root_org = \App\Models\OrganisationStructure::findRootOrg($org_id);
        $managers = \App\Models\OrganisationUser::where([
                    'is_admin' => 'yes',
                    'org_str_id' => $root_org
                ])->get();
        foreach ($managers as $people) {
            $user = \App\User::find($people->user_id);
            $user->notify(new \App\Notifications\XeroNotification($type));
        }
    }

    public function sendPasswordResetNotification($token) {
        // Your your own implementation.
        $this->notify(new ResetPasswordNotification($token));
    }

}
