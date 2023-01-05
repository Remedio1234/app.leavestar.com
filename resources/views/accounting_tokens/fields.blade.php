<!-- Org Str Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('org_str_id', 'Org Str Id:') !!}
    {!! Form::number('org_str_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Accsoft Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accsoft_id', 'Accsoft Id:') !!}
    {!! Form::number('accsoft_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Token Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('token', 'Token:') !!}
    {!! Form::textarea('token', null, ['class' => 'form-control']) !!}
</div>

<!-- Secret Token Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('secret_token', 'Secret Token:') !!}
    {!! Form::textarea('secret_token', null, ['class' => 'form-control']) !!}
</div>

<!-- Refresh Token Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('refresh_token', 'Refresh Token:') !!}
    {!! Form::textarea('refresh_token', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('accountingTokens.index') !!}" class="btn btn-default">Cancel</a>
</div>
