@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Register Capacity
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($registerCapacity, ['route' => ['registerCapacities.update', $registerCapacity->id], 'method' => 'patch']) !!}

                        @include('register_capacities.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection