<!-- Logo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('logo', 'Logo:') !!}
    {!! Form::text('logo', null, ['class' => 'form-control']) !!}
</div>

<!-- Timezone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timezone', 'Timezone:') !!}
    {!! Form::text('timezone', null, ['class' => 'form-control']) !!}
</div>

<!-- Leave Rules Field -->
<div class="form-group col-sm-6">
    {!! Form::label('leave_rules', 'Leave Rules:') !!}
    {!! Form::number('leave_rules', null, ['class' => 'form-control']) !!}
</div>

<!-- Leave Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('leave_type', 'Leave Type:') !!}
    {!! Form::number('leave_type', null, ['class' => 'form-control']) !!}
</div>

<!-- Block Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('block_date', 'Block Date:') !!}
    {!! Form::number('block_date', null, ['class' => 'form-control']) !!}
</div>

<!-- Custom Holidays Field -->
<div class="form-group col-sm-6">
    {!! Form::label('custom_holidays', 'Custom Holidays:') !!}
    {!! Form::number('custom_holidays', null, ['class' => 'form-control']) !!}
</div>

<!-- Sick Leave Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sick_leave', 'Sick Leave:') !!}
    {!! Form::number('sick_leave', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('settings.index') !!}" class="btn btn-default">Cancel</a>
</div>
