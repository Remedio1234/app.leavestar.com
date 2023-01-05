@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Accounting Token
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
                   {!! Form::model($accountingToken, ['route' => ['accountingTokens.update', $accountingToken->id], 'method' => 'patch']) !!}

                        @include('accounting_tokens.fields')

                   {!! Form::close() !!}
           </div>
       </div>
   </div>
@endsection