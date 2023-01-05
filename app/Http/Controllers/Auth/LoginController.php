<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Config;
use DB;

class LoginController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles authenticating users for the application and
      | redirecting them to your home screen. The controller uses a trait
      | to conveniently provide its functionality to your applications.
      |
     */

use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function login(Request $request) {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
      
        $credentials = $this->credentials($request);

        if ($this->guard()->attempt($credentials, $request->has('remember'))) {

            $last_org = \Auth::user()->last_visit_org;
            if ($last_org != null) {

                $request->session()->put('current_org', $last_org);
            } else {
                $first_org_user = \App\Models\OrganisationUser::where('user_id', \Auth::user()->id)->first();

                if ((isset($first_org_user)) || (\Auth::user()->id == 1)) {
                    $first_org_by_user = isset($first_org_user->org_str_id) ? $first_org_user->org_str_id : null;
                    $user = \App\User::where('id', \Auth::user()->id)->first();
                    $user->last_visit_org = $first_org_by_user;
                    $user->save();
                    $request->session()->put('current_org', $first_org_by_user);
                } else {
                    \Auth::logout();

                    return redirect(route('login'))->with('info', 'Your Account has not attached to any Organasation.Please contact our support team for more details.');
                }
            }

            $data = DB::select('SELECT leave_capacity.id AS ids, leave_capacity.capacity AS capacity, leave_capacity.status_update_date AS status_update_date FROM leave_capacity LEFT JOIN leave_type ON leave_type.id = leave_capacity.leave_type_id WHERE leave_capacity.user_id = :user_id AND leave_capacity.org_id = :org_id AND leave_type.name = :name LIMIT 1', ['user_id' => \Auth::user()->id, 'org_id' => 74, 'name' => 'Annual Leave']);

            if (!collect([$data])->isEmpty()) {

              $status_update_date = (int) date('m', strtotime($data[0]->status_update_date));
              $current_month = (int) date('m');
              $current_date = (int) date('d');
              $first_date = (int) date('d',strtotime('first day of this month'));
              $last_date = (int) date('d',strtotime('last day of this month'));

              // within the month
              if ($first_date < $current_date && $last_date > $current_date) {

                // if current month greater than status update date add 22500 seconds to capicty field
                if ($current_month > $status_update_date) {

                  // hour | seconds | total seconds
                  // 6.25 * 3600 = 22,500
                  $capacity = $data[0]->capacity + 22500;

                  DB::table('leave_capacity')->where('id', $data[0]->ids)->update(['capacity' => $capacity, 'status_update_date' => date('Y-m-d- H:i:s', time())]);
                }
              }

            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

}
