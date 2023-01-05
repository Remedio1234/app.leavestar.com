@extends('layouts.app')

@section('content')
<div class="content">
    <section class="content-header">
        <h1>Organisation Structures</h1>
    </section>
    <div class="clearfix"></div>

    @include('flash::message')

    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">
            @include('organisation_structures.table')
        </div>
    </div>
</div>
@endsection

