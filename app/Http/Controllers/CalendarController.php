<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calendar;
use App\Models\ICS;

class CalendarController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
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
            if ($category->children->count() != 0) {
                $this->traverse($category->children, $tree[count($tree) - 1]['children'], $level + 1, $category);
            }
        }
    }

    public function getChildTree($org_id) {
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

    public function CHolidayCondensed($org_id) {
        $color_customHoliday = "#ca1629";
        $setting_id = \App\Models\OrganisationStructure::find($org_id)->setting_id;
        $custom_holiday = \App\Models\CustomHoliday::where('setting_id', $setting_id)->get();
        $condensedEvents = [];
        foreach ($custom_holiday as $item) {
            $condensedEvents[] = [
                'title' => $item->name,
                'start' => date("Y-m-d H:i:s", strtotime(\App\Models\Setting::getLocalTime($org_id, $item->start_date, false))),
                'end' => date("Y-m-d H:i:s", strtotime(\App\Models\Setting::getLocalTime($org_id, $item->end_date, false))),
                'color' => $color_customHoliday,
                "textColor" => "#FFF",
                "editable" => false,
                    //"rendering" => 'background',
            ];
        }
        return $condensedEvents;
    }

    public function birthdayCondensed($startDate, $endDate, $color = '#50cf30') {
        if ((date('Y', strtotime($startDate))) == (date('Y', strtotime($endDate)))) {
            $year = date('Y', strtotime($startDate));
        } else {
            $weekAfter = strtotime('+7 days', strtotime($startDate));
            $year = date('Y', $weekAfter);
        }


        $currentUser = \App\Models\OrganisationUser::where([
                    'user_id' => \Auth::user()->id,
                    'org_str_id' => \Session::get('current_org'),
                ])->first();
        
        $root_org = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, \Session::get('current_org'));
        $orgTree = $this->getChildTree($root_org);
        $users = \App\Models\OrganisationUser::whereIn('org_str_id', $orgTree)->get();
        
        $root_org = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, \Session::get('current_org'));
        $orgTree = $this->getChildTree($root_org);
        $users = \App\Models\OrganisationUser::whereIn('org_str_id', $orgTree)->get();
        $condensedEvents = [];
        $user_check = [];
        foreach ($users as $user) {
            if (!isset($user_check[$user->user_id])) {
                $user_check[$user->user_id] = false;
                $condensedEvents[] = [
                    'title' => \App\User::find($user->user_id)->name . '`s Birthday',
                    'start' => date("Y-m-d", strtotime($year . '-' . date("m-d", strtotime($user->birthday)))),
                    'end' => date("Y-m-d", strtotime($year . '-' . date("m-d", strtotime($user->birthday)))),
                    'color' => isset($currentUser->birthdayFeedColor) ? $currentUser->birthdayFeedColor : $color,
                    "textColor" => isset($currentUser->birthdayTextColor) ? $currentUser->birthdayTextColor : "black",
                    "editable" => false,
                        //"rendering" => 'background',
                ];
            }
        }
        return $condensedEvents;
    }

    public function workAniversaryCondensed($startDate, $endDate, $color = '#23c3db') {
        if ((date('Y', strtotime($startDate))) == (date('Y', strtotime($endDate)))) {
            $year = date('Y', strtotime($startDate));
        } else {
            $weekAfter = strtotime('+7 days', strtotime($startDate));
            $year = date('Y', $weekAfter);
        }
        $currentUser = \App\Models\OrganisationUser::where([
                    'user_id' => \Auth::user()->id,
                    'org_str_id' => \Session::get('current_org'),
                ])->first();
        $root_org = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, \Session::get('current_org'));
        $orgTree = $this->getChildTree($root_org);
        $users = \App\Models\OrganisationUser::whereIn('org_str_id', $orgTree)->get();
        $condensedEvents = [];
        foreach ($users as $user) {

            if (isset($user->start_working_date)) {
                $condensedEvents[] = [
                    'title' => \App\User::find($user->user_id)->name . '`s Working Anniversary',
                    'start' => $year . '-' . date("m-d", strtotime($user->start_working_date)),
                    'end' => $year . '-' . date("m-d", strtotime($user->start_working_date)),
                    'color' => isset($currentUser->anniversariesFeedColor) ? $currentUser->anniversariesFeedColor : $color,
                    "textColor" => isset($currentUser->anniversaryTextColor) ? $currentUser->anniversaryTextColor : "black",
                    "editable" => false,
                    "start_date" => date("Y", strtotime($user->start_working_date)),
                        //"rendering" => 'background',
                ];
            }
        }
        return $condensedEvents;
    }

    public function appCondensed($events, $org_id) {
        $user_id = \Auth::user()->id;
        $condensedEvents = [];
        foreach ($events as $event) {
            switch ($event->status) {
                case 0:
                    $color = '#ed9200';
                    break;
                case 1:
                    $color = '#15b75e';
                    break;
                case 2:
                    $color = '#ca1629';
                    break;
                case 3:
                    $color = '#ca1629';
                    break;
            }
            $leave_type = \App\Models\LeaveType::find($event->leave_type_id)->name;
            $user_name = \App\User::find($event->user_id)->name;
            $title = ($event->user_id == $user_id) ? ("My " . $leave_type) : ($user_name . "'s " . $leave_type);
            $is_admin = \App\Models\OrganisationUser::where([
                        'org_str_id' => $org_id,
                        'user_id' => $user_id
                    ])->first()->is_admin;
            $urlManager = action('LeaveApplicationController@manageLeave') . '#leaveappid' . $event->id;
            $ajaxClass = false;
            if (($is_admin == 'yes') && ($event->user_id != $user_id)) {
                $url = $urlManager;
            }
            if ($event->user_id == $user_id) {
                $url = action('LeaveApplicationController@myLeaveApplication') . '#leaveappid' . $event->id;
                $ajaxClass = true;
            }
            if (($is_admin == 'no') && ($event->user_id != $user_id)) {
                $url = "";
            }

            $condensedEvents[] = [
                'title' => $title,
                'start' => date("Y-m-d H:i:s", strtotime(\App\Models\Setting::getLocalTime($org_id, $event->start_date, false))),
                'end' => date("Y-m-d H:i:s", strtotime(\App\Models\Setting::getLocalTime($org_id, $event->end_date, false))),
                'url' => $url,
                'color' => $color,
                "textColor" => "black",
                "leaveType" => $leave_type,
                "eventStatus" => $event->status,
                "editable" => false,
                    //"rendering" => 'background',
            ];
        }
        return $condensedEvents;
    }

    public function feed(Request $request) {
        //Get the holiday feed first
        $events = Calendar::holidayFeed();

        $leave_q = $request['leavetype'];
	$org_q=array();
	$employee_q=array();
	
	//safety if null the array is arr() empty ,jd
	if($request['org']!='null'){
		$org_q = json_decode($request['org']);
	}        
	if($request['employees']!='null'){
        	$employee_q = json_decode($request['employees']);
	}
	
        //Get the leave application feed
        //Merge the staff's leave first for Manger
        //Then Merge user's own leave application
        $user_id = \Auth::user()->id;
        $org_id = $request->session()->get('current_org');

        //Pre-filter procedure
        $Pre_leaveapplication = \App\Models\LeaveApplication::where('status', '!=', 2);
        if ($leave_q !== "") {
            $Pre_leaveapplication = $Pre_leaveapplication->where('leave_type_id', $leave_q);
        }
        if (sizeof((array)$org_q) > 0) { 
            $Pre_leaveapplication = $Pre_leaveapplication->whereIn('org_id', $org_q);
        }
        if (sizeof($employee_q) > 0) {
            $Pre_leaveapplication = $Pre_leaveapplication->whereIn('user_id', $employee_q);
        }

        $event_merge = [];
        if (\App\Models\OrganisationUser::where(['org_str_id' => $org_id, 'user_id' => $user_id])->first()->is_admin == 'yes') {
            //$realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
            //just get all leave from account level

            $account_id = \App\Models\OrganisationStructure::where(['id' => $org_id])->first()->account_id;
            $realboss = \App\Models\OrganisationStructure::where(['account_id' => $account_id, 'parent_id' => null])->first()->id;

            $tree = $this->getChildTree($realboss);
            $leave_application = $Pre_leaveapplication->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
            $event_merge = array_merge($events, $this->appCondensed($leave_application, $org_id));
        } else {

            $account_id = \App\Models\OrganisationStructure::where(['id' => $org_id])->first()->account_id;
            $realboss = \App\Models\OrganisationStructure::where(['account_id' => $account_id, 'parent_id' => null])->first()->id;

            $tree = $this->getChildTree($realboss);
            $leave_application_my = $Pre_leaveapplication->whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();


            //$leave_application_my = $Pre_leaveapplication->where(['user_id' => $user_id, 'org_id' => $org_id])->get();
            $event_merge = array_merge($event_merge, $this->appCondensed($leave_application_my, $org_id));
        }
        //Merge Block Dates and Custom Holidays 
        //And brithday
        $event_merge = array_merge($event_merge, $this->CHolidayCondensed($org_id));

        $event_merge = array_merge($event_merge, Calendar::customCalendars());

        $event_merge = array_merge($event_merge, $this->birthdayCondensed($request['start'], $request['end']));
        $event_merge = array_merge($event_merge, $this->workAniversaryCondensed($request['start'], $request['end']));

        echo json_encode($event_merge);
    }

    public function leaveType(Request $request) {
        $requestOrg = json_decode($request['org_id']);
        if (($requestOrg) !== null) {
            $org_id = $requestOrg;
            $root_org = \App\Models\OrganisationStructure::findRootOrg($org_id);
            $leavetype = \App\Models\LeaveType::where('org_id', $org_id)->get();
            if (sizeof($leavetype) == 0) {
                $leavetype = \App\Models\LeaveType::where('org_id', $root_org)->get();
            }
        } else {
            $org_id = $request->session()->get('current_org');
            $root_org = \App\Models\OrganisationStructure::findRootOrg($org_id);
            $leavetype = \App\Models\LeaveType::where('org_id', $root_org)->get();
        }


        $array = [];
        foreach ($leavetype as $type) {
            $array[] = [
                'label' => $type->name,
                'value' => $type->id
            ];
        }
        echo json_encode($array);
    }

    public function Department(Request $request) {
        $org_id = $request->session()->get('current_org');
        $accountId = \App\Models\OrganisationStructure::find($org_id)->account_id;
        $realBoss = \App\Models\OrganisationStructure::where(['account_id' => $accountId, 'parent_id' => null])->first();
        $tree = $this->getChildTree($realBoss->id);
        $array = [];
        foreach ($tree as $item) {
            $org = \App\Models\OrganisationStructure::find($item);
            $array[] = [
                'label' => $org->name,
                'value' => $org->id
            ];
        }
        echo json_encode($array);
    }

    public function Employee(Request $request) {

        $org_id = $request->session()->get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
        $tree = $this->getChildTree($realBoss);
        $array = [];
        $users = \App\Models\OrganisationUser::whereIn('org_str_id', $tree)->get();
        foreach ($users as $item) {
            $array[] = [
                'label' => \App\User::find($item->user_id)->name,
                'value' => $item->user_id
            ];
        }
        echo json_encode($array);
    }

    public function DownloadFeed() {
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename=LeaveStar.ics');

        $org_id = \Session::get('current_org');
        $tree = $this->getChildTree($org_id);
        $leave_application = \App\Models\LeaveApplication::whereIn('org_id', $tree)->orderBy('updated_at', 'DESC')->get();
        $leaveArray = [];
        foreach ($leave_application as $application) {
            $leave_type = \App\Models\LeaveType::find($application->leave_type_id)->name;
            $user_name = \App\User::find($application->user_id)->name;
            $title = ($application->user_id == \Auth::user()->id) ? ("My " . $leave_type) : ($user_name . "'s " . $leave_type);
            $leaveArray[] = array(
                'description' => $title,
                'dtstart' => date('Y-m-d H:i:A', strtotime($application->start_date)),
                'dtend' => date('Y-m-d H:i:A', strtotime($application->end_date)),
                'summary' => $title,
            );
        }
        $ics = new ICS($leaveArray);

        echo $ics->to_string();
    }

}
