@extends('layouts.setting_layout')

@section('content')
<section class="content-header">
    <h1>
        Block Date
    </h1>
</section>
<div class="content">
    @include('adminlte-templates::common.errors') 
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                {!! Form::model($block_dates, ['route' => ['blockDates.update', $block_dates->id], 'method' => 'patch','class'=>'form-render']) !!}

                @include('block_dates.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection