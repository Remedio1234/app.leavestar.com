<?php
$weeklist = [
    '1' => 'Monday',
    '2' => 'Tuesday',
    '3' => 'Wednesday',
    '4' => 'Thurday',
    '5' => 'Friday',
    '6' => 'Saturday',
    '7' => 'Sunday',
];
for ($i = 0; $i <= 23; $i++) {
    $hours_list[$i] = $i;
}
for ($i = 0; $i <= 59; $i++) {
    $minutes_list[$i] = $i;
}
?>
<!-- Dayofweek Field -->
<div class="form-group col-sm-12">
    {!! Form::label('dayOfWeek', 'Dayofweek:') !!}
    {!! Form::select('dayOfWeek',$weeklist, null, ['class' => 'form-control']) !!}
</div>

<!-- Start Time Field -->
<div class="form-group col-sm-12">
    {!! Form::label('when', 'When:') !!}
    <p id="datepairExample">
        {!! Form::text('start_time' , null, ['class' => 'form-control time start']) !!}
        To
        {!! Form::text('end_time' , null, ['class' => 'form-control time end']) !!}
    </p>
</div>

<!-- Start Time Field -->
<div class="form-group col-sm-12">
    {!! Form::label('Breaks', 'Breaks Time:') !!}
    <p id="datepairExample2">
        {!! Form::select('breakHours' ,$hours_list, null, ['class' => 'form-control time start']) !!}
        Hours
        {!! Form::select('breakMins', $minutes_list , null, ['class' => 'form-control time end']) !!}
        Minutes
    </p>
</div>

<!--Hidden Field for Org ID-->
<div class="form-group col-sm-12">
    {!! Form::hidden('org_id',$organisationStructure->id , ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="javascript:return false;" data-href="<?= "/openHours/?org_id=" . $organisationStructure->id ?>" class="btn btn-default button-open-right">Back</a>

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


<script>
    $('#datepairExample .time').timepicker({
        'showDuration': true,
        'disableTextInput': true,
        'timeFormat': 'G:i:s',
        'minTime': '12:00:00am',
        'maxTime': '11:30:00pm'
    });

    // $('#datepairExample .date').datepicker({
    //     'format': 'm/d/yyyy',p
    //     'autoclose': true
    // });

    $('#datepairExample').datepair({
        'defaultTimeDelta': null,
    });
</script>