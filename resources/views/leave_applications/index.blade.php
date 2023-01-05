@extends('leave_applications.layout')

@section('content') 
<section class="content-header">
    <h1 class="pull-left">Leave Applications</h1>

</section>
<div class="content">
    <div class="clearfix"></div>

    @include('flash::message')

    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">
            @include('leave_applications.table')
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
                app.$children[0].$refs.calendar.$emit('refetch-events');
            },
        });

    });
</script>


@endsection