@extends('layouts.app')
@section('content')

<div class="container-fluid">
    <div class="calendar-wrap">
        @include('flash::message')
        <?php
        //$realBoss = App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, \Session::get('current_org'));
        //$org = App\Models\OrganisationStructure::find($realBoss);
        $org_user = \App\Models\OrganisationUser::where(['user_id' => \Auth::user()->id, 'org_str_id' => \Session::get('current_org')])->first();
        if ($org_user->is_admin == 'yes') {
            ?>
            @include('dashboard')
            <?php
        }
        ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="panel panel-default  panel-home">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-calendar"></i> Calendar</h3>
                    </div>
                    <div class="panel-body">
                        <leavestar-calendar></leavestar-calendar> 
                    </div>
                </div>
            </div>
            @include('notification')
        </div>

    </div>
</div>



@endsection
