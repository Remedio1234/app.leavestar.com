@extends('layouts.setting_layout')

@section('content')
<section class="content-header">
    <h1>
        Custom Holiday
    </h1>
</section>
<div class="content">
    @include('adminlte-templates::common.errors')     
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                {!! Form::model($custom_holidays, ['route' => ['customHolidays.update', $custom_holidays->id], 'method' => 'patch','class'=>'form-render']) !!}

                @include('custom_holidays.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection