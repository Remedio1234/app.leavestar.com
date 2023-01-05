@extends('leave_applications.layout')

@section('content')
<section class="content-header">
    <h1>
        Change Application
    </h1>
</section>
<div class="content">
    @include('adminlte-templates::common.errors')
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                {!! Form::model($leaveApplication, ['route' => ['leaveApplications.update', $leaveApplication->id], 'method' => 'patch' ]) !!}

                @include('leave_applications.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection