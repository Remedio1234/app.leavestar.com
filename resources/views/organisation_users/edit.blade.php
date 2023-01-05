@extends('layouts.app')

@section('content')
<section class="content-header">
    <h1>
        Organisation User
    </h1>
</section>
<div class="content">
    @include('adminlte-templates::common.errors')
    @include('flash::message')
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                {!! Form::model($organisationUser, ['route' => ['organisationUsers.update', $organisationUser->id], 'method' => 'patch']) !!}

                @include('organisation_users.fields')

                {!! Form::close() !!}
                <table class="table table-striped">
                    <tr>
                        <th>Leave Type</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    $root_org = \App\Models\OrganisationStructure::findRootOrg($organisationUser->org_str_id);
                    $leave_types = \App\Models\LeaveType::where('org_id', $root_org)->get();


                    foreach ($leave_types as $item) {
                        $try = \App\Models\LeaveAccrualSetting::where(['user_id' => $organisationUser->user_id, 'org_id' => $organisationUser->org_str_id, 'leave_type_id' => $item->id])->first();
                        if (isset($try)) {
                            $leave_accrual_settings = $try;
                        } else {
                            $leave_accrual_settings = \App\Models\LeaveAccrualSetting::findSettingClient($organisationUser->org_str_id, $item->id);
                        }

                        if (isset($leave_accrual_settings)) {
                            $capacity = App\Models\LeaveCapacity::where(['leave_type_id' => $item->id, 'user_id' => $organisationUser->user_id, 'org_id' => $organisationUser->org_str_id])->first();
                            $left = "";
                            if (isset($capacity)) {
                                $left = " Now " . $capacity->capacity . " Hours Balance Left.";
                            } else {
                                $left = "No Balance has been setting for this user.";
                            }
                            $details = \App\Models\LeaveAccrualSetting::getDetails($leave_accrual_settings) . '<br>' . $left;
                            ?>
                            <tr>
                                <td><?= $item->name ?></td>
                                <td><?= $details ?></td>
                                <td>

                                    <a href="/leaveAccrualSetting/edit?accrual_setting=<?= $leave_accrual_settings->id ?>" class="btn btn-default btn-xs open-right"><i class="glyphicon glyphicon-edit"></i></a>

                                </td>
                            </tr>
                            <?php
                        } else {
                            ?>
                            <tr>
                                <td><?= $item->name ?></td>
                                <td>Not Setting</td>
                                <td>
                                    <a href="/leaveAccrualSetting/new?leavetype=<?= $item->id ?>" class="btn btn-default btn-xs open-right"><i class="glyphicon glyphicon-edit"></i></a>

                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>
@section('scripts')
<script>
    $(document.body).on('click', '.open-right', function (e) {
        var myurl = $(this).attr('href');
        var user_id =<?= $organisationUser->user_id ?>;
        var org_id =<?= $organisationUser->org_str_id ?>;
        $.ajax({
            url: myurl,
            type: "get",
            datatype: "html",
            data: {user_id: user_id, org_id: org_id},
            beforeSend: function () {
                if ($(".wrapper").hasClass("open-right-sidebar")) {
                    $('#ajax-loading-inner').show();
                } else {
                    $('#ajax-loading-fullscreen').show();
                }
            }
        })
                .done(function (data) {
                    $(".right-sidebar").empty().html(data);

                    if ($(".wrapper").hasClass("open-right-sidebar")) {
                        $('#ajax-loading-inner').hide();
                    } else {
                        $('#ajax-loading-fullscreen').hide();
                    }
                    $(".wrapper").addClass("open-right-sidebar");

                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
        return false;
    });
</script>
@append
@endsection