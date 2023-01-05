@extends('layouts.setting_layout')

@section('content')
<section class="content-header">
    <h1>
        Sick Leave
    </h1>
</section>
<div class="content">
    @include('adminlte-templates::common.errors')
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                {!! Form::model($sick_leaves, ['route' => ['sickLeaves.update', $sick_leaves->id], 'method' => 'patch','class'=>'form-render']) !!}

                @include('sick_leaves.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection