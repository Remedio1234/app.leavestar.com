<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Flash;

class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
        //app('App\Http\Controllers\WeeklyNotificationsController')->leaveStarting();
    }

    //Get the Top Tree Node , and the children tree structure
    private function traverse($categories, &$tree, $level) {

        foreach ($categories as $category) {
            array_push($tree, array(
                'id' => $category->id,
                'name' => $category->name,
                'account_id' => $category->account_id,
                'setting_id' => $category->setting_id,
                'children' => [],
            ));
            if ($category->children->count() > 0) {
                $this->traverse($category->children, $tree[count($tree) - 1]['children'], $level + 1);
            }
        }
    }

    //get children tree
    private function getChildTree($org_id) {
        $account_id = \App\Models\OrganisationStructure::find($org_id)->account_id;
        $tree = [];
        $nodes = \App\Models\OrganisationStructure::scoped(['account_id', $account_id])->descendantsOf($org_id);
        $this->traverse($nodes, $tree, 1);
        $array = [];
        foreach ($tree as $item) {
            $array[] = $item['id'];
        }
        $array[] = intval($org_id);
        return $array;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function donormalIndex() {

        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, \Session::get('current_org'));
        $org_user = \App\Models\OrganisationUser::where(['user_id' => \Auth::user()->id, 'org_str_id' => \Session::get('current_org')])->first();

        //$org = \App\Models\OrganisationStructure::find($realBoss);
        if ($org_user->is_admin == 'yes') {
            $tree = $this->getChildTree($realBoss);
            $users = \App\Models\OrganisationUser::whereIn('org_str_id', $tree)->select('user_id')->groupBy('user_id')->get()->count();

            $totalLeavesApplied = \App\Models\LeaveApplication::where('status', 0)->whereIn('org_id', $tree)->count();
            $totalApprovedLeaves = \App\Models\LeaveApplication::where('status', 1)->whereDate('end_date', '>', date('Y-m-d'))->whereIn('org_id', $tree)->count();

            $one_month_after = \Carbon\Carbon::now()->addMonth(1);
            $now = \Carbon\Carbon::now();
            $upcomingLeaves = \App\Models\LeaveApplication::where('status', 1)->where('start_date', '<=', $one_month_after)->where('start_date', ">", $now)->whereIn('org_id', $tree)->count();
            //die('ggg');
            return view('home')->with([
                        'userNumber' => $users,
                        'leaveApplied' => $totalLeavesApplied,
                        'leaveApproved' => $totalApprovedLeaves,
                        'upcomingleaves' => $upcomingLeaves
            ]);
        } else {
            return view('home');
        }
    }

    public function index() {
        $current_org = \Session::get('current_org');
        if (!isset($current_org)) {
            if (\auth::user()->id == 1) {
                $users = \App\User::get()->count() - 1;
                $totalorgnazation = \App\Models\OrganisationStructure::get()->count();
                $totalLeavesApplied = \App\Models\LeaveApplication::where('status', 0)->count();
                $totalApprovedLeaves = \App\Models\LeaveApplication::where('status', 1)->whereDate('end_date', '>', date('Y-m-d'))->count();

                return view('adminHome')->with([
                            'userNumber' => $users,
                            'leaveApplied' => $totalLeavesApplied,
                            'leaveApproved' => $totalApprovedLeaves,
                            'orgNumber' => $totalorgnazation
                ]);
            } else {
                $org_id = \App\User::where('id', \Auth::user()->id)->first()->last_visit_org;
                \Session::set('current_org', $org_id);
                return $this->donormalIndex();
            }
        } else {
            return $this->donormalIndex();
        }
    }

    public function changeOrg(Request $request, $org_id) {
        $user_list = \App\Models\OrganisationUser::where(['org_str_id' => $org_id, 'user_id' => \Auth::user()->id])->get();
        if (sizeof($user_list) == 0) {
            return view('errors.403');
        } else {
            $user = \App\User::where('id', \Auth::user()->id)->first();
            $user->last_visit_org = $org_id;
            $user->save();
            $request->session()->put('current_org', $org_id);

            return redirect(\URL::previous());
        }
    }

    public function setNotificationRead(Request $request) {
        $notificationID = $request['ID'];
        $user = \Auth::user();
        $notification = $user->notifications()->where('id', $notificationID)->first();

        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function terminateAccount(Request $request) {
        $user_id = \Auth::user()->id;
        $org_id = \Session::get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel($user_id, $org_id);
        $org_user = \App\Models\OrganisationUser::where([
                    'user_id' => $user_id,
                    'org_str_id' => $realBoss,
                ])->first();
        if (isset($org_user)) {
            $accountId = \App\Models\OrganisationStructure::find($realBoss)->account_id;
            $account = \App\Models\Account::find($accountId);
            $account->status = 0;
            $account->save();
            Flash::error('Your Account ' . $account->name . ' has been terminated.');
            return redirect('/');
        } else {
            return view('errors.403');
        }
    }

    public function endGuide() {
        $user = \App\User::find(\Auth::user()->id);
        $user->tourGuide = 1;
        $user->save();
    }

}
