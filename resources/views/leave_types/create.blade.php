@extends('layouts.setting_layout')

@section('content')
<section class="content-header">
    <h1>
        Leave Type
    </h1>
</section>
<div class="content">

    <div class="box box-primary">

        <div class="box-body">
            <div class="row">
                {!! Form::open(['route' => 'leaveTypes.store','class'=>'form-render']) !!}

                @include('leave_types.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
