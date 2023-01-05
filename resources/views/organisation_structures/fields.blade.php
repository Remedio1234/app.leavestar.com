<!-- Name Field -->
<div class="form-group col-sm-12">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control' ]) !!}
</div>

<!--  Lft Field -->
<!--<div class="form-group col-sm-6">
    {!! Form::label('_lft', ' Lft:') !!}
    {!! Form::number('_lft', null, ['class' => 'form-control']) !!}
</div>

  Rgt Field 
<div class="form-group col-sm-6">
    {!! Form::label('_rgt', ' Rgt:') !!}
    {!! Form::number('_rgt', null, ['class' => 'form-control']) !!}
</div>

 Parent Id Field 
<div class="form-group col-sm-6">
    {!! Form::label('parent_id', 'Parent Id:') !!}
    {!! Form::number('parent_id', null, ['class' => 'form-control']) !!}
</div>-->

<!-- Setting Id Field -->
<!--<div class="form-group col-sm-6">
    {!! Form::label('setting_id', 'Setting Id:') !!}
    {!! Form::number('setting_id', null, ['class' => 'form-control']) !!}
</div>-->

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="<?= URL::previous()  ?>" class="btn btn-default button-close-right">Cancel</a>
</div>
