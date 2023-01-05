@extends('layouts.setting_layout')

@section('content')
<section class="content-header">
    <h1>
        Open Hour
    </h1>
</section>
<div class="content">
    @include('adminlte-templates::common.errors')
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                {!! Form::model($openHour, ['route' => ['openHours.update', $openHour->id], 'method' => 'patch','class'=>'form-render']) !!}

                @include('open_hours.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection