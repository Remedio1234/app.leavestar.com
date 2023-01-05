@extends('layouts.app')

@section('content')
<div class="content">
    <section class="content-header">
        <h1>My Leaves</h1>
    </section>

    @include('flash::message')

    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">
            @include('leave_applications.table')

        </div>
    </div>
</div>

@endsection