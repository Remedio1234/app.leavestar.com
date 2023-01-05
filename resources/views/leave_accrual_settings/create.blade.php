@extends('layouts.setting_layout')

@section('content')
<section class="content-header">
    <h1>
        Leave Accrual Setting
    </h1>
</section>
<div class="content">
    @include('adminlte-templates::common.errors')
    <div class="box box-primary">

        <div class="box-body">
            <div class="row">
                {!! Form::open(['route' => 'leaveAccrualSettings.store','class'=>'form-render']) !!}

                @include('leave_accrual_settings.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
