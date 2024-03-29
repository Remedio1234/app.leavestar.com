<!-- Leave Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('leave_id', 'Leave Id:') !!}
    {!! Form::number('leave_id', null, ['class' => 'form-control']) !!}
</div>

 <!-- Content Field -->
<div class="form-group col-sm-6">
    {!! Form::label('content', 'Content:') !!}
    {!! Form::text('content', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('comments.index') !!}" class="btn btn-default">Cancel</a>
</div>
