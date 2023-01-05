@extends('layouts.app')

@section('content')
<div class="content">
    <section class="content-header">
        <h1>Weekly Reminder</h1>
        <p>What's Happening This Week</p>
        <p><?= date( 'jS F Y', strtotime('monday this week')). ' - ' .date( 'jS F Y', strtotime('sunday this week')); ?></p>
    </section>

    @include('flash::message')

    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">


            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#WeeklyReminder" aria-controls="WeeklyReminder" role="tab" data-toggle="tab">Weekly Report</a></li>
                <!-- <li role="presentation"><a href="#Upcoming_Leaves" aria-controls="Upcoming_Leaves" role="tab" data-toggle="tab">Leave History</a></li> -->

            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="WeeklyReminder"> 
                    <!-- <span class="pull-left" style="margin-top: 6px;">Sort Date&nbsp;</span> -->
                    <!-- <div class="dropdown  pull-left" id="sortdatedp" cval='0'> -->
                        <!-- <button type="button" class="btn btn-block dropdown-toggle menu-widget-text-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span>Descending</span><span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#">Descending</a></li>
                            <li><a class="dropdown-item" href="#">Ascending</a></li>
                        </ul> -->
                    <!-- </div> -->
                    <div class="dropdown  pull-right">
                        <button type="button" class="btn btn-block dropdown-toggle menu-widget-text-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span><?php
                                switch ($queryType) {
                                    case "leave-starting":
                                        echo "Leave Starting ";
                                        break;
                                    case "leave-ending":
                                        echo "Leave Ending ";
                                        break;
                                    case "work-anniversary":
                                        echo "Work Anniversary ";
                                        break;
                                    case "birthday":
                                        echo "Birthday";
                                        break;
                                    default:
                                        echo "Leave Starting ";
                                        break;
                                }
                                ?>  </span><span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="/weeklyNotifications/manage/?queryType=leave-starting">Leave Starting</a></li>
                            <li><a class="dropdown-item" href="/weeklyNotifications/manage/?queryType=leave-ending">Leave Ending</a></li>
                            <li><a class="dropdown-item" href="/weeklyNotifications/manage/?queryType=work-anniversary">Work Anniversary</a></li>
                            <li><a class="dropdown-item" href="/weeklyNotifications/manage/?queryType=birthday">Birthday</a></li>
                        </ul>
                    </div>
                    <!-- <div class="clearfix"></div> -->
                @if ( $weeklyReports == new stdClass())
                    <div id="emptyWeeklyRecords" class="app-entity leave-panel">No Records to display</div>
                @else
                    <div style="margin-top: 50px;">@include('weekly_report.table')</div>
                @endif
                </div>
                <div role="tabpanel" class="tab-pane" id="Upcoming_Leaves"> ...</div>

            </div>


        </div>
    </div>
</div>


@endsection