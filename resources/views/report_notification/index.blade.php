@extends('organisation_users.user_edit_layout')

@section('render')
<div class="">

    <h1 class="pull-left">Weekly Report Notification</h1>
    <h1 class="pull-right">
        <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('customizedFeeds.create') !!}">Add New</a>
    </h1>


    <div class="clearfix"></div>

    @include('flash::message')

    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">
            @include('report_notification.table')
        </div>
    </div>

</div>    
@endsection

