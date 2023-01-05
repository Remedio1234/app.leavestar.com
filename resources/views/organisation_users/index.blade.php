@extends('layouts.app')

@section('content')

<div class="content">
    <section class="content-header">
        <h1 class="pull-left">Organisation Users</h1>
        <h1 class="pull-right">
            <!--           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('organisationUsers.create') !!}">Invite</a>-->
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('userRegisters.create') !!}">Invite</a>
        </h1>
    </section>
    <div class="clearfix"></div>

    @include('flash::message')

    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">


            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#current_user" aria-controls="current_user" role="tab" data-toggle="tab">Current Users</a></li>
                <li role="presentation"><a href="#user_capacity" aria-controls="user_capacity" role="tab" data-toggle="tab">User Capacity List</a></li>
                <li role="presentation"><a href="#invited_user" aria-controls="invited_user" role="tab" data-toggle="tab">Invitation List</a></li>
                <li role="presentation"><a href="#xero_list" aria-controls="xero_list" role="tab" data-toggle="tab">Xero Connection List</a></li>

            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="current_user"> @include('organisation_users.table')</div>
                <div role="tabpanel" class="tab-pane" id="user_capacity"> @include('organisation_users.userCapacityTable')</div>
                <div role="tabpanel" class="tab-pane" id="invited_user"> @include('user_registers.table')</div>
                <div role="tabpanel" class="tab-pane" id="xero_list"> @include('organisation_users.xeroListTable')</div>
            </div>
        </div>
    </div>
</div>
<!--<script>
    $(function () {

        $('#someTab').tab('show')

    });

</script>-->
@endsection

