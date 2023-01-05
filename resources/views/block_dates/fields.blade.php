<!-- Date Range -->
<div class="form-group col-sm-12">
    {!! Form::label('When', 'When:') !!}
    {!! Form::text('date_range', null, ['class' => 'form-control']) !!}
</div>

<!--<div class="form-group col-sm-12">
    {!! Form::label('start_date', 'Start Date:') !!}
    {!! Form::date('start_date', null,  ['class' => 'form-control ']) !!}

</div>

<div class="form-group col-sm-12">
    {!! Form::label('end_date', 'End Date:') !!}
    {!! Form::date('end_date', null,  ['class' => 'form-control ']) !!}
</div>-->

<div class="form-group col-sm-12">
    {!! Form::label('limit', 'Limits:') !!}
    {!! Form::text('limits', null, ['class' => 'form-control']) !!}
</div>


<div class="form-group col-sm-12">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => 'form-control']) !!}
</div>


<!--Hidden Field for Org ID-->
<div class="form-group col-sm-12">
    {!! Form::hidden('org_id', $organisationStructure->id , ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Submit', ['class' => 'btn btn-primary ']) !!}
    <a href="javascript:return false;" data-href="<?= "/blockDates/?org_id=" . $organisationStructure->id ?>" class="btn btn-default button-open-right">Back</a>

</div>


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

        },
    });

});
</script>

<script type="text/javascript">
    $(function () {
        $('input[name="date_range"]').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 30,
            locale: {
                format: '<?= App\Models\Setting::getFrontEndDF(Session::get('current_org')) . ' h:mm A' ?>'
            }
        });
    });
</script>
