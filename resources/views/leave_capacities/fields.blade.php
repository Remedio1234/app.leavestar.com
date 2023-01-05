<!-- User Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('user_id', 'User Id:') !!}
    {!! Form::number('user_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Org Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('org_id', 'Org Id:') !!}
    {!! Form::number('org_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Leave Type Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('leave_type_id', 'Leave Type Id:') !!}
    {!! Form::number('leave_type_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Capacity Field -->
<div class="form-group col-sm-6">
    {!! Form::label('capacity', 'Capacity:') !!}
    {!! Form::number('capacity', null, ['class' => 'form-control']) !!}
</div>

<!-- Last Update Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('last_update_date', 'Last Update Date:') !!}
    {!! Form::date('last_update_date', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('leaveCapacities.index') !!}" class="btn btn-default">Cancel</a>
</div>
