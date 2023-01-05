@extends('layouts.app')

@section('content')   


<section class="content-header">
    <h1>
        User Register
    </h1>
</section>
<div class="content">
   
    @include('flash::message')
    <div class="box box-primary">

        <div class="box-body">
            <div class="row">
                {!! Form::open(['route' => 'userRegisters.store','id'=>'form-invite']) !!}

                @include('user_registers.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>


@endsection