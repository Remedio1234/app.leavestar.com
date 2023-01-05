@extends('layouts.setting_layout')

@section('content')

<?php
$zones = timezone_identifiers_list();
$data_format = App\Models\Setting::getDataFormat();
foreach ($zones as $zone) {
    $zonelist[$zone] = $zone;
}
?>

<section class="content-header">
    <h1 class="pull-left">Branch Settings</h1>

</section>
<!-- Name Field -->
{!! Form::model($organisationStructure, ['id'=>'setting_home','class'=>'form-render','route' => ['organisationStructures.update', $organisationStructure->id], 'method' => 'patch']) !!}
<div class="form-group col-sm-12">
    {!! Form::label('name', 'Branch Name') !!}
    {!! Form::text('name', null, ['class' => 'form-control' ]) !!}
</div>


<div class="form-group col-sm-12">
    {!! Form::label('setting[leave_rules]', 'Number of people allowed to take leave on the same day') !!}
    {!! Form::text('setting[leave_rules]', null, ['class' => 'form-control' ]) !!}
</div>

<div class="form-group col-sm-12">
    {!! Form::label('setting[timezone]', 'Timezone') !!}
    {!! Form::select('setting[timezone]', $zonelist ,null, ['class' => 'form-control' ]) !!}
</div>

<div class="form-group col-sm-12">
    {!! Form::label('setting[data_format]', 'Date Format') !!}
    {!! Form::select('setting[data_format]', $data_format ,null, ['class' => 'form-control' ]) !!}
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
    {!! Form::submit('Submit', ['class' => 'btn btn-primary ']) !!}

</div>

{!! Form::close() !!}
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
            var div_name = "menuEdit<?= $organisationStructure->id ?>";
            $('#' + div_name + ">span").html($('#name').val());
            $('.right-sidebar').html(data);
        },
    });
});
</script>


@endsection