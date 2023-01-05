<?php

namespace App\Http\Controllers;

//use App\Http\Controllers\AppBaseController;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;
use App\Http\Controllers\CalendarController;
use App\Models;

use Mail;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class WeeklyNotificationsController extends CalendarController {

    private $weekly_token = "gbkbnaw4o8dbnos744GGJHBOODNE23R9U975CGhdfvHAVooo3";
    private $weekStart;
    private $weekEnd;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        
        //only run on mondays
//        if(date("D") != "Mon"){
//            die();
//        }
        
//        $this->weekStart = strtotime("Last Sunday + 22 hours + 30 minutes");

// original        //$this->weekStart = strtotime("Yesterday + 22 hours + 30 minutes"); 
// original        //$this->weekEnd = strtotime("Last Sunday + 7 days - 1 hour - 30 minutes");
        
        //$this->weekStart = strtotime("Yesterday + 22 hours + 30 minutes"); 
        //$this->weekEnd = strtotime("Last Sunday + 7 days - 1 hour - 30 minutes");
       
        //$this->weekStart = strtotime("Today"); 
        //$this->weekEnd = strtotime("Today + 7 days -1 seconds");
        
       //$this->weekStart = strtotime("28-01-2019 00:00:00"); 
        //$this->weekEnd = strtotime("03-02-2019 22:29:59");
        
        
        //$this->weekStart = strtotime("22-04-2019 00:00:00"); 
        //$this->weekEnd = strtotime("28-04-2019 23:59:59");
        
        //$this->weekStart = strtotime("Last Sunday");
       // $this->weekEnd = strtotime("Next Sunday");
        
//        echo date("Y-m-d H:i:s", $this->weekStart);
//        echo "<br/>";
//        echo date("Y-m-d H:i:s", $this->weekEnd);
//        die();

	$this->weekStart = strtotime("monday this week"); 
        $this->weekEnd = strtotime("sunday this week");
    }

    private function happeningThisWeek($time) {
        $weekStart = $this->weekStart;
        $weekEnd = $this->weekEnd;
        
        //if ($time >= $weekStart && $time <= $weekEnd) {
        if ($time >= $weekStart && $time <= $weekEnd) {
        
        /*if(((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0))) && (date('m',$time)==02) && (date('d',$time)==29)){
        return false;
        
        } else {
            return true;
        }
        */
        return true;
        
        } else {
            return false;
        }
    }

    private function takesUpWeek($start, $end) {
        $weekStart = $this->weekStart;
        $weekEnd = $this->weekEnd;

        if ($start < $weekStart && $end >= $weekEnd) {
            return true;
        } else {
            return false;
        }
    }

    private function buildTable($title, $events) {
        $message = "<table style='border: 1px solid #edeff2; border-collapse: collapse; width: 500px; background: #f2f4f6; color: #2f3133;'><tr><td style='padding: 15px; text-align: center;'><i><b>{$title}</b></i></td></tr></table>";

        $message .= "<table style='border: 1px solid #edeff2; border-collapse: collapse; width: 500px;'>";
        foreach ($events as $event) {
            $message .= "<tr><td style='width: 30%; border: 1px solid #edeff2; padding: 10px; color: #2f3133;'><b>{$event['date']}</b></td><td style='border: 1px solid #edeff2; padding: 10px; color: #73787e;'>{$event['title']}</td></tr>";
            /*if(isset($event['user_id'])){
            $message.="<tr><td>ordg Id</td><td>{$event['user_id']}</td></tr>";
            }*/
        }
        $message .= "</table><table height='20' width='600;'></table>";

        return $message;
    }
    
    private function buildTableAnniversaries($title, $events) {
        $message = "<table style='border: 1px solid #edeff2; border-collapse: collapse; width: 500px; background: #f2f4f6; color: #2f3133;'><tr><td colspan='2' style='padding: 15px; text-align: center;'><i><b>{$title}</b></i></td><td style='padding: 15px; text-align: center;'><i><b>Start Date</b></td></tr></table>";

        $message .= "<table style='border: 1px solid #edeff2; border-collapse: collapse; width: 500px;'>";
        foreach ($events as $event) {
            $message .= "<tr><td style='width: 30%; border: 1px solid #edeff2; padding: 10px; color: #2f3133;'><b>{$event['date']}</b></td><td style='border: 1px solid #edeff2; padding: 10px; color: #73787e;'>{$event['title']}</td><td style='width: 30%; border: 1px solid #edeff2; padding: 10px; color: #2f3133;'><b>{$event['start_date']}</b></td></tr>";
        }
        $message .= "</table><table height='20' width='600;'></table>";

        return $message;
    }
    

    public function send(Request $request, $token) {
        if ($token == $this->weekly_token) {

            //get all the admins
            $admins = Models\OrganisationUser::where(['is_admin' => 'yes'])->get();
//            $admins = Models\OrganisationUser::where(['is_admin' => 'yes', 'user_id' => 10])->get();

            $alreadySent = [];

            //loop through admins
            foreach ($admins as $admin) {
//echo $admin->user_id;
                if (in_array($admin->user_id, $alreadySent)) {
                    continue;
                } else {
                    $alreadySent[] = $admin->user_id;
                }

                $birthdays = [];
                $anniversaries = [];
                $leaveStarting = [];
                $leaveEnding = [];
                $onLeave = [];
                $otherEvents = [];

                //get the user
                $user = \App\User::find($admin->user_id);
//echo '<pre>';print_r($user->email);echo '</pre>';
                //login each admin
                $request->session()->set('current_org', $admin->org_str_id);
                \Auth::login($user, true);

                //get the org
                $org = Models\OrganisationStructure::find($admin->org_str_id);

                if (!$org) { // don't know why the org wouldn't exist?
                    continue;
                }

                $orgName = $org->name;


                //fetch their feed
                $request['leavetype'] = "";
//                $request['org'] = "null";
                $request['employees'] = "null";
                $request['start'] = date("Y-m-d", $this->weekStart);
                $request['end'] = date("Y-m-d", $this->weekEnd);

                ob_start();
                $this->feed($request);
                $feed = \GuzzleHttp\json_decode(ob_get_clean());


               //echo '<pre>';print_r($feed);echo '</pre>';
                foreach ($feed as $event) {

//echo '<pre>'; print_r($event);echo '</pre>'; echo '^&';
                    if (strpos($event->title, 'Birthday') !== false) { // birthday
                        $date = strtotime($event->start);
                        if ($this->happeningThisWeek($date)) {
//                            print_r("<h3>Birthday</h3>");
			//if(date('Y-m-d',strtotime($event->start))==$event->start){
                            $birthdays[] = [
                                'time' => strtotime($event->start),
                                'date' => date("D jS M Y", $date),
                                'title' => $event->title,
                                
                            ];
                           //}
                          //echo '<pre>';  echo date('Y-m-d',strtotime($event->start)); print_r($event);echo '</pre>';
                        }
                    } else if (strpos($event->title, 'Working Anniversary') !== false) { //work anniversary
                        $date = strtotime($event->start);
                        if ($this->happeningThisWeek($date)) {
//                            print_r("<h3>Anniversary</h3>");
                            $anniversaries[] = [
                                'time' => strtotime($event->start),
                                'date' => date("D jS M Y", $date),
                                'title' => $event->title,
                                'start_date' => $event->start_date,
                            ];
//                            print_r($event);
                        }
                    } else {
                        
                        

                        if (isset($event->eventStatus) && $event->eventStatus != 1) { //Only care about approved leave
                            continue;
                        }
                        
                        if ($this->happeningThisWeek(strtotime($event->start))) { // starting this week
//                            print_r("<h3>Starting and ending this week</h3>");
                            
                            $eventStart = strtotime($event->start);
                            $eventEnd = strtotime($event->end);
                            $eventDate = date("D jS M Y", strtotime($event->start));
                            
                            if(($eventEnd - $eventStart) > 86400){ // if the event goes for longer than a day
                                $eventDate .= " - ".date("D jS M Y", strtotime($event->end));
                            }
                            
                            $leaveStarting[] = [
                                'time' => $eventStart,
                                'date' => $eventDate,
                                'title' => $event->title
                            ];
                           //echo '<pre>'; print_r($event);echo '</pre>'; echo '^&';
                        } else if ($this->happeningThisWeek(strtotime($event->end))) { // ending this week
//                            print_r("<h3>Ending this week</h3>");
                            $leaveEnding[] = [
                                'time' => strtotime($event->end),
                                'date' => date("D jS M Y", strtotime($event->end)),
                                'title' => $event->title
                            ];
//                            print_r($event);
                        } else if ($this->takesUpWeek(strtotime($event->start), strtotime($event->end))) { // leave takes up whole week
//                            print_r("<h3>Away this week</h3>");
                            $onLeave[] = [
                                'date' => "All week",
                                'title' => $event->title
                            ];
//                            print_r($event);
                        }
                    }
                }

                /*print_r($birthdays);
                print_r($anniversaries);
                print_r($leaveStarting);
                print_r($leaveEnding);
                print_r($onLeave);
                */
                usort($birthdays, function($a, $b) {
                    return $a['time'] - $b['time'];
                });
                usort($anniversaries, function($a, $b) {
                    return $a['time'] - $b['time'];
                });
                usort($leaveStarting, function($a, $b) {
                    return $a['time'] - $b['time'];
                });
                usort($leaveEnding, function($a, $b) {
                    return $a['time'] - $b['time'];
                });

                $message = "";

                if (!empty($birthdays)) {
                    $message .= $this->buildTable("Birthdays", $birthdays);
                }

                if (!empty($anniversaries)) {
                    $message .= $this->buildTableAnniversaries("Work anniversaries", $anniversaries);
                }

                if (!empty($leaveEnding)) {
                    $message .= $this->buildTable("Starting this week", $leaveEnding);
                }

                if (!empty($leaveStarting)) {
                    $message .= $this->buildTable("Ending this week", $leaveStarting);
                }

                

                if (!empty($onLeave)) {
                    $message .= $this->buildTable("All week", $onLeave);
                }

//                echo $message;

                if ($message == "") {
                    $message = "There is nothing upcoming this week.";
                } else {
                    $message = "Here is what your week looks like: <br/>" . $message;
                }

                //send the email
                //echo $message;
                $user->notify(new \App\Notifications\WeeklyNotification($message));
                //Todo: 
                //logout
            }
        }
    }

// new meow
    public function leaveStarting($queryType) {
        $userArr = array();
        $userIdsArr;
        $u = 0;
        $leaveStarting = array();

        $leaveApplications = Models\LeaveApplication::select(DB::raw('leave_application.id, leave_application.user_id, leave_application.start_date, leave_application.end_date, leave_type.name as title'))->leftJoin('leave_type', 'leave_type.id', '=', 'leave_application.leave_type_id')->where('leave_application.status','=','1')->get();

        foreach ($leaveApplications as $leaveApplication) {
            if ($this->happeningThisWeek(strtotime($leaveApplication->start_date))) {
                $eventStart = strtotime($leaveApplication->start_date);
                $eventEnd = strtotime($leaveApplication->end_date);
                $eventDate = date("jS F", strtotime($leaveApplication->start_date));
                $title = $leaveApplication->title;
                // $name = $user->name;
                
                if(($eventEnd - $eventStart) > 86400){ // if the event goes for longer than a day
                    $eventDate .= " - ".date("jS F Y", strtotime($leaveApplication->end_date));
                }
                
                $leaveStarting[] = [
                    'id' => $leaveApplication->id,
                    'user_id' => $leaveApplication->user_id,
                    'time' => $eventStart,
                    'date' => $eventDate,
                    'title' => $title
                ];
            }
        }

        $object = (object) $leaveStarting;
        return array('weeklyReports' => $object, 'queryType' => $queryType);
    }

    public function leaveEnding($queryType) {
        $userArr = array();
        $userIdsArr;
        $u = 0;
        $leaveEnding = array();

        $leaveApplications = Models\LeaveApplication::select(DB::raw('leave_application.id, leave_application.user_id, leave_application.start_date, leave_application.end_date, leave_type.name as title'))->leftJoin('leave_type', 'leave_type.id', '=', 'leave_application.leave_type_id')->where('leave_application.status','=','1')->get();

        foreach ($leaveApplications as $leaveApplication) {
            if ($this->happeningThisWeek(strtotime($leaveApplication->end_date))) {
                $eventStart = strtotime($leaveApplication->end_date);
                // $eventEnd = strtotime($leaveApplication->end_date);
                $eventDate = 'Ends On '. date("jS F Y", strtotime($leaveApplication->end_date));
                $title = $leaveApplication->title;
                // 'name' => ''

                $leaveEnding[] = [
                    'user_id' => $leaveApplication->user_id,
                    'id' => $leaveApplication->id,
                    'time' => $eventStart,
                    'date' => $eventDate,
                    'title' => $title
                ];
            }
        }

        $object = (object) $leaveEnding;
        return array('weeklyReports' => $object, 'queryType' => $queryType);
    }

    public function workingAnniversary($queryType) {
        $userArr = array();
        // $userIdsArr;
        // $u = 0;
        $anniversaries = array();
        $users=\App\Models\OrganisationUser::select(DB::raw('organisation_user.id, organisation_user.start_working_date, max(organisation_user.org_str_id) as org_str_id,max(organisation_user.user_id)as user_id,max(users.email)as email,max(users.name) as name'))->Join('users', 'users.id', '=', 'organisation_user.user_id')
                            ->where(['organisation_user.is_admin' => 'no'])
                            ->groupBy('organisation_user.user_id')
                            ->get();

        foreach ($users as $user) {
            // $userArr[$u] = $user->user_id;
            // $u++;
            // $date = strtotime($user->start_working_date);
            $d = date_parse_from_format("Y-m-d", $user->start_working_date);
            if (!empty($user->start_working_date)) {
                // $dateNow = date("Y"). '-' . $d["month"] . '-' .$d["day"] . ' ' . $d["hour"] . ':' . $d["minute"] . ':' .$d["second"];
                $dateNow = date("Y"). '-' . $d["month"] . '-' .$d["day"] . ' 00:00:00';
                $date = strtotime($dateNow);
                if ($this->happeningThisWeek($date)) {
                    $anniversaries[] = [
                        'time' => strtotime($user->start_working_date),
                        'date' => 'Work anniversary '. date("jS F Y", $date),
                        'name' => $user->name
                    ];    
                }
            }
        }

        $object = (object) $anniversaries;
        return array('weeklyReports' => $object, 'queryType' => $queryType);
    }

    public function employeeBirthday($queryType) {
        $userArr = array();
        // $userIdsArr;
        // $u = 0;
        $anniversaries = array();
        $users=\App\Models\OrganisationUser::select(DB::raw('organisation_user.id, organisation_user.birthday, max(organisation_user.org_str_id) as org_str_id,max(organisation_user.user_id)as user_id,max(users.email)as email,max(users.name) as name'))->Join('users', 'users.id', '=', 'organisation_user.user_id')
                            ->where(['organisation_user.is_admin' => 'no'])
                            ->groupBy('organisation_user.user_id')
                            ->get();

        foreach ($users as $user) {
            // $userArr[$u] = $user->user_id;
            // $u++;
            // $date = strtotime($user->birthday);
            $d = date_parse_from_format("Y-m-d", $user->birthday);
            if (!empty($user->birthday)) {
                // $dateNow = date("Y"). '-' . $d["month"] . '-' .$d["day"] . ' ' . $d["hour"] . ':' . $d["minute"] . ':' .$d["second"];
                $dateNow = date("Y"). '-' . $d["month"] . '-' .$d["day"] . ' 00:00:00';
                $date = strtotime($dateNow);
                if ($this->happeningThisWeek($date)) {
                    $anniversaries[] = [
                        'time' => strtotime($user->birthday),
                        'date' => 'Birthday '. date("jS F Y", $date),
                        'name' => $user->name
                    ];    
                }
            }
        }

        $object = (object) $anniversaries;
        return array('weeklyReports' => $object, 'queryType' => $queryType);
    }

    public function isWeeklyReport(Request $request) {

        $result = "";

        if ( $request['queryType'] == 'leave-starting' ) {
            $result = $this->leaveStarting($request['queryType']);
        }

        if ( $request['queryType'] == 'leave-ending' ) {
            $result = $this->leaveEnding($request['queryType']);
        }

        if ( $request['queryType'] == 'work-anniversary' ) {
            $result = $this->workingAnniversary($request['queryType']);
        }

        if ( $request['queryType'] == 'birthday' ) {
            $result = $this->employeeBirthday($request['queryType']);
        }

        if ( empty($request['queryType']) ) {
            return view('errors.403');
        }

        // print_r($result);
        // exit();
        return view('weekly_report.management')->with($result);
   }
}
