@extends('layouts.setting_layout')

@section('content')
<section class="content-header">
    <h1>
        Custom Holiday
    </h1>
</section>
<div class="content">

    <div class="box box-primary">

        <div class="box-body">
            <div class="row">
                {!! Form::open(['route' => 'customHolidays.store','class'=>'form-render']) !!}

                @include('custom_holidays.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
