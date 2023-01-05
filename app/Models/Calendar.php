<?php

namespace App\Models;

use App\Models\ICal;
//use ICal\ICal;

class Calendar {

    public function __construct() {
        date_default_timezone_set("UTC");
    }

    public static $colors = ["salmon", "skyblue", "aquamarine", "darkorange"];
    public static $feeds = [
        "US Holidays" => "https://www.calendarlabs.com/templates/ical/US-Holidays.ics",
        "UK Holidays" => "https://www.calendarlabs.com/templates/ical/UK-Holidays.ics",
        "Argentina Holidays" => "https://www.calendarlabs.com/templates/ical/Argentina-Holidays.ics",
        "Australia Holidays" => "https://www.calendarlabs.com/templates/ical/Australia-Holidays.ics",
        "Belgium Holidays" => "https://www.calendarlabs.com/templates/ical/Belgium-Holidays.ics",
        "Brazil Holidays" => "https://www.calendarlabs.com/templates/ical/Brazil-Holidays.ics",
        "Canada Holidays" => "https://www.calendarlabs.com/templates/ical/Canada-Holidays.ics",
        "China Holidays" => "https://www.calendarlabs.com/templates/ical/China-Holidays.ics",
        "Colombia Holidays" => "https://www.calendarlabs.com/templates/ical/Colombia-Holidays.ics",
        "Denmark Holidays" => "https://www.calendarlabs.com/templates/ical/Denmark-Holidays.ics",
        "Egypt Holidays" => "https://www.calendarlabs.com/templates/ical/Egypt-Holidays.ics",
        "France Holidays" => "https://www.calendarlabs.com/templates/ical/France-Holidays.ics",
        "Germany Holidays" => "https://www.calendarlabs.com/templates/ical/Germany-Holidays.ics",
        "Greece Holidays" => "https://www.calendarlabs.com/templates/ical/Greece-Holidays.ics",
        "HongKong Holidays" => "https://www.calendarlabs.com/templates/ical/HongKong-Holidays.ics",
        "India Holidays" => "https://www.calendarlabs.com/templates/ical/India-Holidays.ics",
        "Indonesia Holidays" => "https://www.calendarlabs.com/templates/ical/Indonesia-Holidays.ics",
        "Ireland Holidays" => "https://www.calendarlabs.com/templates/ical/Ireland-Holidays.ics",
        "Italy Holidays" => "https://www.calendarlabs.com/templates/ical/Italy-Holidays.ics",
        "Japan Holidays" => "https://www.calendarlabs.com/templates/ical/Japan-Holidays.ics",
        "Kenya Holidays" => "https://www.calendarlabs.com/templates/ical/Kenya-Holidays.ics",
        "Malaysia Holidays" => "https://www.calendarlabs.com/templates/ical/Malaysia-Holidays.ics",
        "Mauritius Holidays" => "https://www.calendarlabs.com/templates/ical/Mauritius-Holidays.ics",
        "Mexico Holidays" => "https://www.calendarlabs.com/templates/ical/Mexico-Holidays.ics",
        "NewZealand Holidays" => "https://www.calendarlabs.com/templates/ical/NewZealand-Holidays.ics",
        "Norway Holidays" => "https://www.calendarlabs.com/templates/ical/Norway-Holidays.ics",
        "Philippines Holidays" => "https://www.calendarlabs.com/templates/ical/Philippines-Holidays.ics",
        "Romania Holidays" => "https://www.calendarlabs.com/templates/ical/Romania-Holidays.ics",
        "Singapore Holidays" => "https://www.calendarlabs.com/templates/ical/Singapore-Holidays.ics",
        "SouthAfrica Holidays" => "https://www.calendarlabs.com/templates/ical/SouthAfrica-Holidays.ics",
        "SouthKorea Holidays" => "https://www.calendarlabs.com/templates/ical/SouthKorea-Holidays.ics",
        "Spain Holidays" => "https://www.calendarlabs.com/templates/ical/Spain-Holidays.ics",
        "Sweden Holidays" => "https://www.calendarlabs.com/templates/ical/Sweden-Holidays.ics",
        "Thailand Holidays" => "https://www.calendarlabs.com/templates/ical/Thailand-Holidays.ics",
        "Turkey Holidays" => "https://www.calendarlabs.com/templates/ical/Turkey-Holidays.ics",
        "UAE Holidays" => "https://www.calendarlabs.com/templates/ical/UAE-Holidays.ics",
        "Vietnam Holidays" => "https://www.calendarlabs.com/templates/ical/Vietnam-Holidays.ics",
        "International Holidays" => "https://www.calendarlabs.com/templates/ical/International-Holidays.ics",
        "Buddhist Holidays" => "https://www.calendarlabs.com/templates/ical/Buddhist-Holidays.ics",
        "Christian Holidays" => "https://www.calendarlabs.com/templates/ical/Christian-Holidays.ics",
        "Hindu Holidays" => "https://www.calendarlabs.com/templates/ical/Hindu-Holidays.ics",
        "Islam Holidays" => "https://www.calendarlabs.com/templates/ical/Islam-Holidays.ics",
        "Jewish Holidays" => "https://www.calendarlabs.com/templates/ical/Jewish-Holidays.ics",
        "Sikh Holidays" => "https://www.calendarlabs.com/templates/ical/Sikh-Holidays.ics",
    ];

    public static function holidayFeed() {

        //just get the australian feed for now
        $feeds = [];

        $events = [];
        $c = 0;
        foreach ($feeds as $key) {

            $month = date("m");
            $year = date("Y");
            //check for a cached version
            $cachedPath = storage_path("app/" . implode("-", [strtolower(str_replace(" ", '-', $key)), $year, $month]) . ".ics");

            //if there is no cached file, we need to download one
            if (!file_exists($cachedPath)) {
                $content = file_get_contents(self::$feeds[$key]);
                file_put_contents($cachedPath, $content);

                //check if the previous month's file exists, if so delete it
                $month = (string) str_pad((($month - 1 < 1) ? 12 : $month - 1), 2, "0", STR_PAD_LEFT);
                $year = ($month == 12) ? $year - 1 : $year;
                $prevPath = storage_path("app/" . implode("-", [strtolower(str_replace(" ", '-', $key)), $year, $month]) . ".ics");
                if (file_exists($prevPath) && !is_dir($prevPath)) {
                    unlink($prevPath);
                }
            }
    //echo $cachedPath.'<br>'; 
            $ICal = new ICal($cachedPath);
            $events = array_merge($events, $ICal->eventsCondensed(self::$colors[$c]));
            $c++;
        }

        return $events;
    }

    public static function customCalendars() {
        $feeds = customizedFeed::where([
                    'user_id' => \Auth::user()->id,
                    'org_id' => \Session::get('current_org')
                ])->get();


//      $feeds = ["https://outlook.office365.com/owa/calendar/30a6910139a74a368bb72a43eb8cb1ec@hia.edu.au/dd2777741cba40279f650ab87f7dc9518501517833162546411/calendar.ics"];
        $events = [];
        foreach ($feeds as $feed) {

            $url = $feed->feed;
            try {
                $cachedPath = storage_path("app/" . md5($url) . ".ics");
                if (file_exists($cachedPath) && filemtime($cachedPath) < strtotime("- 30 minutes")) {
                    unlink($cachedPath);
                }
                if (!file_exists($cachedPath)) {
                    $content = file_get_contents($url);
                    file_put_contents($cachedPath, $content);
                }
                $ical = new ICal($cachedPath, array(
                    'defaultTimeZone' => 'UTC',
                    'skipRecurrence' => false, // Default value
                ));
                $colorCode = $feed->feedcolor;
                $event = Calendar::eventsCondensed($ical, $colorCode);
            } catch (\Exception $e) {
                $event = [];
            }
            $events = array_merge($events, $event);
        }

        return $events;
    }

    public static function eventsCondensed($ical, $color = "white") {
        $events = $ical->events();
        $condensedEvents = [];

        foreach ($events as $event) {

            $condensedEvents[] = [
                'title' => $event->summary,
                'start' => date("Y-m-d H:i:s", $ical->iCalDateToUnixTimestamp($event->dtstart)),
                'end' => date("Y-m-d H:i:s", $ical->iCalDateToUnixTimestamp($event->dtend)),
                'color' => $color,
                "textColor" => "black",
                "editable" => false,
                    //"rendering" => 'background',
            ];
        }

        return $condensedEvents;
    }

    public static function leaveFeed() {
        
    }

    public static function combinedFeed() {
        
    }

}
