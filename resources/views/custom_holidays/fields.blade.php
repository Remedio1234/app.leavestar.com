<!-- Date Field -->
<div class="form-group col-sm-12">
    {!! Form::label('When', 'When:') !!}
    {!! Form::text('date_range', null, ['class' => 'form-control']) !!}
</div>

<!--
<div class="form-group col-sm-12">
    {!! Form::label('date', 'Date:') !!}
    {!! Form::date('date', null, ['class' => 'form-control']) !!}
</div>-->

<!-- Name Field -->
<div class="form-group col-sm-12">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2]) !!}
</div>

<!--Hidden Field for Org ID-->
<div class="form-group col-sm-12">
    {!! Form::hidden('org_id',$organisationStructure->id , ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Submit', ['class' => 'btn btn-primary ']) !!}
    <a href="javascript:return false;" data-href="<?= "/customHolidays/?org_id=" . $organisationStructure->id ?>" class="btn btn-default button-open-right">Back</a>
</div>

<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
{!! $validator !!}


<script type="text/javascript">
$(function () {
    $('input[name="date_range"]').daterangepicker({
        locale: {
            format: '<?= App\Models\Setting::getFrontEndDF(Session::get('current_org')) ?>'
        }
    });
});
</script>

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