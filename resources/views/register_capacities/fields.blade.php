<!-- Register Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('register_id', 'Register Id:') !!}
    {!! Form::number('register_id', null, ['class' => 'form-control']) !!}
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

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('registerCapacities.index') !!}" class="btn btn-default">Cancel</a>
</div>
