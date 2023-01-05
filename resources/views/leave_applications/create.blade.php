@extends('leave_applications.layout')

@section('content')
<h1>Apply for Leave</h1>
<hr/>
{!! Form::open(['route' => 'leaveApplications.store' ,'class'=>'form-render']) !!}

@include('leave_applications.fields')

{!! Form::close() !!}




@endsection