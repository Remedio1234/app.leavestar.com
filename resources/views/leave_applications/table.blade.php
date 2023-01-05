<?php $dummy = 0; ?>
@foreach($leaveApplications as $leaveApplication)

<?php
$rule_array = [];
$datediff = strtotime($leaveApplication->end_date) - strtotime($leaveApplication->start_date);
$hoursLeave = App\Models\LeaveApplication::CalLeaveUnit($leaveApplication->org_id, $leaveApplication->start_date, $leaveApplication->end_date) / 3600;
//$difference = ((round($datediff / (60 * 60 * 24))) == 0) ? round($datediff / (60 * 60 )) : round($datediff / (60 * 60 * 24));
$difference = ceil(($datediff / (60 * 60 * 24 )));

//$mark = ((round($datediff / (60 * 60 * 24))) == 0) ? "Hours" : "Days";
$mark = "Days";

$currentBalanceSearch = App\Models\LeaveCapacity::where([
            'user_id' => $leaveApplication->user_id,
            'org_id' => $leaveApplication->org_id,
            'leave_type_id' => $leaveApplication->leave_type_id,
        ])->first();
$currentBalance = isset($currentBalanceSearch) ? $currentBalanceSearch->capacity : 0;


$statuses = ["PENDING", "APPROVED", "DENIED", "NOT SUBMITTED"];

$startYear = date("Y", strtotime($leaveApplication->start_date));
$endYear = date("Y", strtotime($leaveApplication->end_date));
$sameYear = ($startYear == $endYear) ? true : false;
$formats = ["j M y", "j M y"];
if ($leaveApplication->status == 3) {
    $app_class = "scheduled";
}
if ($leaveApplication->status == 0) {
    $app_class = "pending";
}
if ($leaveApplication->status == 1) {
    $app_class = "approved";
}
if ($leaveApplication->status == 2) {
    $app_class = "rejected";
}
$flexiblity = ($leaveApplication->flexible == 0) ? "Not Flexible" : "Flexible";
$user_name = App\User::find($leaveApplication->user_id)->name;
$org_name = App\Models\OrganisationStructure::find($leaveApplication->org_id)->name;

$role = App\Models\OrganisationUser::where('user_id', \Auth::user()->id)->first()->is_admin;
?>
<div id="<?= "leaveappid" . $leaveApplication->id ?>" class="app-entity <?= "leaveappid" . $leaveApplication->id ?> leave-panel <?php echo $dummy == 0 ? 'dummy':'' ?>">
    <div class="<?= $app_class . " leave_app" ?>">
        <?php /*
          <div class="col-sm-3 days">
          <div class="duration"><?= $difference ?></div>
          <p><?= strtoupper($mark) ?></p>
          </div>
         * 
         */ ?>
        <div class="infos col-sm-12">
            <h5 class="status pull-right"><?= $statuses[$leaveApplication->status] ?></h5>
            <h4><span class="glyphicon glyphicon-user"></span> <?= $user_name ?></h4>
            <div class="dates <?= (($sameYear) ? "same-year" : "") ?>">
                <div class="from">

                    <div class="date"><?= date($formats[($sameYear) ? 0 : 1], strtotime(\App\Models\Setting::getLocalTime($org_id, $leaveApplication->start_date, false))) ?></div>
                    <div class="time"><?= date("h:ia", strtotime(\App\Models\Setting::getLocalTime($org_id, $leaveApplication->start_date, false))) ?></div>
                </div>
                <div class="to">
                    <div class="date"><?= date($formats[($sameYear) ? 0 : 1], strtotime(\App\Models\Setting::getLocalTime($org_id, $leaveApplication->end_date, false))) ?></div>
                    <div class="time"><?= date("h:ia", strtotime(\App\Models\Setting::getLocalTime($org_id, $leaveApplication->end_date, false))) ?></div>
                </div>
                <div class="clearfix"></div>
            </div>

            <table class="table table-condensed table-striped">
                <tr>
                    <th>Duration</th>
                    <td class="text-right"><?= $difference ?> <?= $mark ?> (<?= $hoursLeave ?> Hours)</td>
                </tr>
                <tr>
                    <th>Current Balance</th>
                    <td class="text-right"><?= number_format(($currentBalance), 2, '.', ''); ?> Hours</td>
                </tr>
                <tr>
                    <th>Type</th>
                    <td class="text-right"><?= App\Models\LeaveType::find($leaveApplication->leave_type_id)->name ?></td>
                </tr>
                <tr>
                    <th>Flexibility</th>
                    <td class="text-right"><?= $flexiblity ?></td>
                </tr>
                <tr>
                    <th>Department</th>
                    <td class="text-right"><?= $org_name ?></td>
                </tr>
                <?php if ($view == 'manage') { ?>
                    <tr>
                        <th>Modify</th>
                        <td class="text-right"> <a href="javascript:return false;" data-href="{!! route('leaveApplications.edit', [$leaveApplication->id]) !!}" class='button-open-right modify btn btn-warning'>Change </a></td>
                    </tr>
                    <?php
                }
                $org_user = \App\Models\OrganisationUser::where([
                            'org_str_id' => $leaveApplication->org_id,
                            'user_id' => $leaveApplication->user_id
                        ])->first();
                if (isset($org_user->phone)) {
                    ?>
                    <tr class = "leaveAppContact">
                        <th>Contact</th>
                        <td class = "text-right"><a href="tel:<?= $org_user->phone ?>">Call</a></td>
                    </tr>
                    <?php
                }
                ?>

            </table>
        </div>
    </div>
    <?php
    if ($leaveApplication->rule_check != 1) {

        $manager = ($app_class == "scheduled") ? false : true;
        $size = 0;
        $date_start = App\Models\Setting::getLocalTime($leaveApplication->org_id, $leaveApplication->start_date);
        $date_end = App\Models\Setting::getLocalTime($leaveApplication->org_id, $leaveApplication->end_date);
        $org_id = $leaveApplication->org_id;
        $user_id = $leaveApplication->user_id;
        $leave_type_id = $leaveApplication->leave_type_id;
        $leave_type_name = \App\Models\LeaveType::find($leave_type_id)->name;
        $exceptionId = $leaveApplication->id;
        $rule_array[] = \App\Models\BlockDate::checkBlockDateRule($leaveApplication->start_date, $leaveApplication->end_date, $org_id, $manager, $exceptionId);
        /*if ($app_class != "pending") {
            if ($role != "no" && !empty($role)) {
                $rule_array[] = \App\Models\Setting::checkGeneralLeaveRule($leaveApplication->start_date, $leaveApplication->end_date, $org_id, $manager, $exceptionId);
            }
        }*/
        $rule_array[] = \App\Models\LeaveCapacity::checkCapacityRule($org_id, $user_id, $leave_type_id, $leaveApplication->start_date, $leaveApplication->end_date, $manager, $exceptionId);
        //Only check the rule if leave name contain "sick leave"
        if (strpos($leave_type_name, "sick leave") !== false) {
            $rule_array[] = \App\Models\SickLeave::checkSickLeaveRule($date_start, $date_end, $org_id, $manager);
        }
        $warnings = [];

        foreach ($rule_array as $rule) {
            if ($rule['status'] != 'success') {
                $warnings[] = $rule['message'];
            }
        }

        if ($warnings) {
            ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-warning">
                        <?php
                        echo implode("<br/>", $warnings);
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="comment_content">

            </div>        
        </div>
    </div>


    <?php if ($view != 'manage') { ?>
        <div class="actions col-sm-6 col-sm-offset-3">
            <a href="javascript:return false;" data-href="{!! route('leaveApplications.edit', [$leaveApplication->id]) !!}" class='button-open-right modify btn btn-warning'>Change </a>
            {!! Form::open(['route' => ['leaveApplications.destroy', $leaveApplication->id], 'method' => 'delete']) !!}
            {!! Form::button('Delete', ['type' => 'submit' , 'onclick' => "return confirm('Are you sure?')", 'class' => 'btn btn-danger']) !!}  
            {!! Form::close() !!}
            <?php
            $commentsNumber = App\Models\Comment::where('leave_id', $leaveApplication->id)->count();
            ?>
            <a data-leave_id="<?= $leaveApplication->id ?>" href="<?= "/comments/?leave_id=" . $leaveApplication->id ?>" class="button-dropdown btn btn-default">Comments(<?= $commentsNumber ?>)</a>
        </div>
        <?php
    } else {
        ?>
        <div class="actions col-sm-6 col-sm-offset-3">

            {!! Form::open(['route' => ['leaveApplications.update', $leaveApplication->id], 'method' => 'patch','class'=>'form-render2']) !!}
            <input type="hidden" value="approve" name="actions">

            {!! Form::button(' Approve', ['type' => 'submit' , 'onclick' => "return confirm('Are you sure?')", 'class' => 'btn btn-success']) !!}  
            {!! Form::close() !!}


            {!! Form::open(['route' => ['leaveApplications.update', $leaveApplication->id], 'method' => 'patch','class'=>'form-render2']) !!}
            <input type="hidden" value="reject" name="actions">

            {!! Form::button(' Deny ', ['type' => 'submit' , 'onclick' => "return confirm('Are you sure?')", 'class' => 'btn btn-danger']) !!}  
            {!! Form::close() !!}
            <?php
            $commentsNumber = App\Models\Comment::where('leave_id', $leaveApplication->id)->count();
            ?>
            <a data-leave_id="<?= $leaveApplication->id ?>" href="<?= "/comments/?leave_id=" . $leaveApplication->id ?>" class="button-dropdown btn btn-default">Comments(<?= $commentsNumber ?>)</a>
        </div>

        <?php
    }
    ?>

    <div class="clearfix"></div>
</div>


<?php $dummy = ($dummy + 1); ?>
@endforeach

@section('scripts')
<script>
    $('.button-dropdown').on('click', function () {
        var myurl = $(this).attr('href');
        var id = $(this).data('leave_id');
        $.ajax({
            url: myurl,
            type: "get",
            datatype: "html",
        })
                .done(function (data) {
                    if ($(".leaveappid" + id).find('.comment_content').hasClass("open")) {
                        $(".leaveappid" + id).find('.comment_content').removeClass("open");
                    } else {
                        $(".leaveappid" + id).find('.comment_content').addClass("open");
                        $(".leaveappid" + id).find('.comment_content').empty().html(data);
                    }
                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });

        return false;

    });
    //$(".button-dropdown").trigger("click");
</script>  
@append

