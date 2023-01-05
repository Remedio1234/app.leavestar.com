<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $leaveApplication->id !!}</p>
</div>

<!-- User Id Field -->
<div class="form-group">
    {!! Form::label('user_id', 'User Id:') !!}
    <p>{!! $leaveApplication->user_id !!}</p>
</div>

<!-- Start Date Field -->
<div class="form-group">
    {!! Form::label('start_date', 'Start Date:') !!}
    <p>{!! $leaveApplication->start_date !!}</p>
</div>

<!-- End Date Field -->
<div class="form-group">
    {!! Form::label('end_date', 'End Date:') !!}
    <p>{!! $leaveApplication->end_date !!}</p>
</div>

<!-- Leave Type Id Field -->
<div class="form-group">
    {!! Form::label('leave_type_id', 'Leave Type Id:') !!}
    <p>{!! $leaveApplication->leave_type_id !!}</p>
</div>

<!-- Flexible Field -->
<div class="form-group">
    {!! Form::label('flexible', 'Flexible:') !!}
    <p>{!! $leaveApplication->flexible !!}</p>
</div>

<!-- Status Field -->
<div class="form-group">
    {!! Form::label('status', 'Status:') !!}
    <p>{!! $leaveApplication->status !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $leaveApplication->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $leaveApplication->updated_at !!}</p>
</div>

<!-- Deleted At Field -->
<div class="form-group">
    {!! Form::label('deleted_at', 'Deleted At:') !!}
    <p>{!! $leaveApplication->deleted_at !!}</p>
</div>

