<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $accountingToken->id !!}</p>
</div>

<!-- Org Str Id Field -->
<div class="form-group">
    {!! Form::label('org_str_id', 'Org Str Id:') !!}
    <p>{!! $accountingToken->org_str_id !!}</p>
</div>

<!-- Accsoft Id Field -->
<div class="form-group">
    {!! Form::label('accsoft_id', 'Accsoft Id:') !!}
    <p>{!! $accountingToken->accsoft_id !!}</p>
</div>

<!-- Token Field -->
<div class="form-group">
    {!! Form::label('token', 'Token:') !!}
    <p>{!! $accountingToken->token !!}</p>
</div>

<!-- Secret Token Field -->
<div class="form-group">
    {!! Form::label('secret_token', 'Secret Token:') !!}
    <p>{!! $accountingToken->secret_token !!}</p>
</div>

<!-- Refresh Token Field -->
<div class="form-group">
    {!! Form::label('refresh_token', 'Refresh Token:') !!}
    <p>{!! $accountingToken->refresh_token !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $accountingToken->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $accountingToken->updated_at !!}</p>
</div>

<!-- Deleted At Field -->
<div class="form-group">
    {!! Form::label('deleted_at', 'Deleted At:') !!}
    <p>{!! $accountingToken->deleted_at !!}</p>
</div>

