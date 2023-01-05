 


<!-- Org Str Id Field -->
<div class="form-group col-sm-12">
    {!! Form::label('org_str_id', 'Change Organisation:') !!}
    {!! Form::select('org_str_id',$org_list ,null, ['class' => 'form-control']) !!}
</div>

<!-- User Id Field -->
<div class="form-group col-sm-12">
    {!! Form::label('is_admin', 'Role:') !!}
    {!! Form::select('is_admin',array('no' => 'Normal User', 'yes' => 'Manager') ,null, ['class' => 'form-control']) !!}
</div>


<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('organisationUsers.index') !!}" class="btn btn-default">Cancel</a>
</div>
