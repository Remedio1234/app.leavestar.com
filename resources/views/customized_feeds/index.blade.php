@extends('organisation_users.user_edit_layout')

@section('render')
<div class="">

    <h1 class="pull-left">Customized Feeds</h1>
    <h1 class="pull-right">
        <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('customizedFeeds.create') !!}">Add New</a>
    </h1>


    <div class="clearfix"></div>

    @include('flash::message')

    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">
            @include('customized_feeds.table')
        </div>
    </div>

</div>    
@endsection

