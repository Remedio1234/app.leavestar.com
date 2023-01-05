@extends('layouts.app')

@section('content')
<section class="content-header">
    <h1>
        User Register
    </h1>
</section>
<div class="content">
    @include('adminlte-templates::common.errors')
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                {!! Form::model($userRegister, ['route' => ['userRegisters.update', $userRegister->id], 'method' => 'patch']) !!}

                @include('user_registers.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection