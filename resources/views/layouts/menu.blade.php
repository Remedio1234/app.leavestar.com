<?php
$admin_list = [
    [
        'route' => '/accounts',
        'label' => 'Manage Accounts',
    ],
    [
        'route' => '/weeklyNotifications/manage/?queryType=leave-starting',
        'label' => 'Weekly Reminder',
        'id' => 'weekly_reminder',
    ]
];
$manager_list = [
    [
        'route' => '/organisationStructures',
        'label' => 'Organisations Settings',
        'id' => 'org_settings'
    ],
    [
        'route' => '/organisationUsers',
        'label' => 'Staff Management',
        'id' => 'staff_management',
    ],
    [
        'route' => '/leaveApplication/manage?queryType=approved',
        'label' => 'Leave Management',
        'id' => 'leave_management',
    ],
    [
        'route' => '/weeklyNotifications/manage/?queryType=leave-starting',
        'label' => 'Weekly Reminder',
        'id' => 'weekly_reminder',
    ]
];
$normal_list = [
];
$org_id = Session::get('current_org');
$level = App\Models\OrganisationUser::checkLevel(\Auth::user()->id, $org_id);


switch ($level) {
    case 1:
        $list = $admin_list;
        break;
    case 2:
        $list = $manager_list;
        break;
    case 3:
        $list = $manager_list;
        break;
    case 4:
        $list = $normal_list;
        break;
}

if (($level != 1)) {
    ?>

    <li>
        <a id="apply-leave" href="javascript:return false;" data-href="/leaveApplications/create/" class="button-open-right"><span>Apply for leave</span></a> 
    </li>

    <li>
        <?php
        $root_org = \App\Models\OrganisationStructure::findRootOrg(\Session::get('current_org'));
        $leave_types = \App\Models\LeaveType::where('org_id', $root_org)->get();
        if ($leave_types->count() > 0) {
            ?>
            <div class="menu-widget">
                <!--<div class="menu-widget-text-md">Available leave</div>-->

                <div class="btn-group edit-user dropdown-leavetype" style="display: block; width: 100%;">
                    <button type="button" class="btn btn-block dropdown-toggle menu-widget-text-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="current-leave-type">  <?php echo $leave_types[0]->name ?> </span><span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <?php
                        foreach ($leave_types as $type) {
                            $rules = \App\Models\LeaveCapacity::findAccrualRule(\Session::get('current_org'), \Auth::user()->id, $type->id);
                            if ($rules) {
                                ?>
                                <li>
                                    <a class="btn btn-default btn-flat ajaxcall" href="/leaveCapacity/checkcapacity/<?= $type->id ?>"><?= $type->name ?></a>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </div>

                <div class="menu-widget-text-xl capacity">0</div>
                <div class="menu-widget-text-md">Hours</div>
            </div>
        <?php } ?>
    </li>
    <?php
    $next_leave = \App\Models\LeaveApplication::where([
                'user_id' => \Auth::user()->id,
                'org_id' => \Session::get('current_org'),
                'status' => 1
            ])->where('start_date', '>', date('Y-m-d H:i:s'))->orderBy('start_date')->first();

    if (isset($next_leave)) {
        $localtime = \App\Models\Setting::getLocalTime(\Session::get('current_org'), $next_leave->start_date, false);
        $localtimeUpdate = date('Y-m-d\TH:i:s', strtotime($localtime));
        ?>

        <li>
            <div class="menu-widget">
                <div class="menu-widget-text-md">Leave countdown</div>
                <div id="day_left" class="menu-widget-text-xl"></div>
                <div class="menu-widget-text-md">Days</div>
                <div id="time_left" class="menu-widget-text-lg"></div>
                <div class="menu-widget-text-sm">Hr &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Min &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sec</div>
            </div>
        </li>
        @section('scripts')
        <script>
            // Set the date we're counting down to
            var countDownDate = new Date("<?= $localtimeUpdate ?>").getTime();

            // Update the count down every 1 second
            var x = setInterval(function () {

                // Get todays date and time
                var now = new Date().getTime();

                // Find the distance between now an the count down date
                var distance = countDownDate - now;

                // Time calculations for days, hours, minutes and seconds
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Display the result in the element with id="demo"
                $('#day_left').html(days);
                $('#time_left').html(hours + ':' + minutes + ':' + seconds);

                // If the count down is finished, write some text 
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("demo").innerHTML = "EXPIRED";
                }
            }, 1000);
        </script>
        @append
        <?php
    }
}
?>

<?php
foreach ($list as $item) {
    if ((isset($item['id'])) && ($item['id'] == 'org_settings')) {
        $li_id = "org_label";
    } else {
        $li_id = "";
    }
    ?>
    <li class="hidden-xs" id="<?= $li_id ?>">
        <a id="<?= isset($item['id']) ? $item['id'] : "" ?>" href="<?= $item['route'] ?> "><span><?= $item['label'] ?></span></a>
    </li>
    <?php
}
?>

@section('scripts')
<script>
    $(function () {

        function getAvailableLeave(myurl) {
            $.ajax({
                url: myurl,
                type: "get",
                datatype: "html",
            }).done(function (data) {
                $('.dropdown-leavetype').removeClass('open');
                $('.current-leave-type').empty().html(data.leavetype + " ");
                $('.capacity').empty().html(data.capacity);
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        }

        $('.dropdown-leavetype .ajaxcall').on('click', function () {
            var myurl = $(this).attr('href');
            getAvailableLeave(myurl);
            return false;
        });
        getAvailableLeave($(".dropdown-leavetype .ajaxcall").eq(0).attr("href"));
    });
</script>
@append
