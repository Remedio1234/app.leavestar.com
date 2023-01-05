@extends('layouts.setting_layout')

@section('content')
<section class="content-header">
    <h1 class="pull-left">Business Hours</h1>
    <h1 class="pull-right">

        <a class="btn btn-primary pull-right button-open-right" style="margin-top: -10px;margin-bottom: 5px" href="javascript:return false;" data-href="<?= "/openHours/create?org_id=" . $organisationStructure->id ?>">Add New</a>
    </h1>
</section>
<div class="content">
    <div class="clearfix"></div>

    @include('flash::message')

    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">
            @include('open_hours.table')
        </div>
    </div>
</div>


<script>
    $(function () {

        $('.form-render2').ajaxForm({
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

@endsection

