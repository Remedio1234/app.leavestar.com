@extends('layouts.app')

@section('content')
    <div class="content">
        <section class="content-header">
            <h1>Accounts</h1>
<!--           <a class="btn btn-primary" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('accounts.create') !!}">Add New</a>-->
        </section>
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('accounts.table')
            </div>
        </div>
    </div>
@endsection

