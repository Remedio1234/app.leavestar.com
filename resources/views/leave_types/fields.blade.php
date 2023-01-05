<?php
$list = [
    '0' => 'YES',
    '1' => 'NO'
];
?>


<div class="form-group col-sm-12">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-12">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-12">
    {!! Form::label('ispaidleave', 'Paid Leave?:') !!}
    {!! Form::select('ispaidleave',$list ,null, ['class' => 'form-control']) !!}
</div>


<!--Hidden Field for Org ID-->
<div class="form-group col-sm-12">
    {!! Form::hidden('org_id',$organisationStructure->id , ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Submit', ['class' => 'btn btn-primary ']) !!}
    <a href="javascript:return false;" data-href="<?= "/leaveTypes/?org_id=" . $organisationStructure->id ?>" class="btn btn-default button-open-right">Back</a>
</div>


<!-- Laravel Javascript Validation -->

<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
{!! $validator !!}


<script>
$(function () {
    $('.form-render').ajaxForm({
        beforeSubmit: function (arr, $form, options) {
            if ($(this).find('.has-error').length > 0) {
                return false;
            }
        },
        success: function (data) {
            $('.right-sidebar').html(data);
            //$('<div class="alert alert-success">' + data + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button></div>').insertAfter('.right-sidebar>.content>.box-primary > h1');
        },
    });

});
</script>