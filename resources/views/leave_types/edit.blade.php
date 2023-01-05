@extends('layouts.setting_layout')

@section('content')
<section class="content-header">
    <h1>
        Leave Type
    </h1>
</section>
<div class="content">
    @include('adminlte-templates::common.errors')
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                {!! Form::model($leaveType, ['route' => ['leaveTypes.update', $leaveType->id], 'method' => 'patch','class'=>'form-render']) !!}

                @include('leave_types.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection