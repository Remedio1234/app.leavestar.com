@extends('organisation_users.user_edit_layout')

@section('render')
@include('adminlte-templates::common.errors')
@include('flash::message')
<br/>
<div class="box box-primary">

    <div class="box-body">
        <div class="row">
            {!! Form::model($organisationUser, ['action' => ['OrganisationUserController@updateUser' ],'files'=>'true', 'method' => 'patch','id'=>'org_user_update']) !!}

            <!-- Org Str Id Field -->

            <div class="form-group col-sm-12">
                {!! Form::label('profile pic', 'Profile Picture:') !!} {!! Form::file('image', null) !!}
                <?php
                $image = \Auth::user()->profile_pic;
                if (isset($image)) {
                    ?>

                    <div class="user-edit-profile-pic">
                        <img class="profile_pic" style="max-width:300px;" src="<?= url('/') . '/' . $image ?>"/>
                        <a class="btn btn-xs btn-danger" href="/organisationUser/deleteProfile"><span class="glyphicon glyphicon-trash"></span></a>
                    </div>
                    <?php
                }
                ?>
            </div>



            <!-- Org Str Id Field -->
            <div class="form-group col-sm-12">
                {!! Form::label('phone', 'Phone:') !!}
                {!! Form::text('phone'  ,null, ['class' => 'form-control']) !!}
            </div>

            <!-- Org Str Id Field -->
            <div class="form-group col-sm-12">
                {!! Form::label('birthday', 'Birthday:') !!}
                {!! Form::date('birthday'  ,null, ['class' => 'form-control']) !!}
            </div>

            <!-- Org Str Id Field -->
            <div class="form-group col-sm-12">
                {!! Form::label('start_working_date', 'Start Date:') !!}
                {!! Form::date('start_working_date'  ,null, ['class' => 'form-control']) !!}
            </div>



            <div class="form-group col-sm-12 ">
                {!! Form::label('birthdayFeedColor', 'Birthday Feed Color:') !!}
                {!! Form::text('birthdayFeedColor', null, ['class' => 'form-control feedcolor']) !!}
            </div>
            <div class="form-group col-sm-12 ">
                {!! Form::label('birthdayTextColor', 'Birthday Feed Text Color:') !!}
                {!! Form::text('birthdayTextColor', null, ['class' => 'form-control feedcolor']) !!}
            </div>
            <div class="form-group col-sm-12 ">
                {!! Form::label('anniversariesFeedColor', 'Work Anniversaries Feed Color:') !!}
                {!! Form::text('anniversariesFeedColor', null, ['class' => 'form-control feedcolor']) !!}
            </div>

            <div class="form-group col-sm-12 ">
                {!! Form::label('anniversaryTextColor', 'Work Anniversaries Feed Text Color:') !!}
                {!! Form::text('anniversaryTextColor', null, ['class' => 'form-control feedcolor']) !!}
            </div>




            <div class="form-group col-sm-12 ">
                <?php
                $user = \App\User::find(\Auth::user()->id);
                $emailNotification = ($user->receiveEmailNotification == 0) ? false : true;
                ?>
                {!! Form::label('Email Notification', 'Email Notification:'  ) !!}
                {!! Form::checkbox('emailNotification', 'need', $emailNotification ) !!}
                Receive Email Notification as well
            </div>


            <!-- Submit Field -->
            <div class="form-group col-sm-12">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}

            </div>


            {!! Form::close() !!}
        </div>
    </div>
</div>


@section('scripts')

<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
{!! $validator !!}

<script type="text/javascript">

$('.feedcolor').colorpicker();

</script>

@stop
@endsection
