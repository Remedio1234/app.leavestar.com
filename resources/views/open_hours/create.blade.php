@extends('layouts.setting_layout')

@section('content')
<section class="content-header">
    <h1>
        Business Hours
    </h1>
    <p>Business hours will be used to calculate leave.</p>
</section>
<div class="content">
    <div class="box box-primary">
        <hr/>
        <div class="box-body">
            <div class="row">
                {!! Form::open(['route' => 'openHours.store' ,'class'=>'form-render'])!!}

                <?php
                for ($i = 0; $i <= 23; $i++) {
                    $hours_list[$i] = $i;
                }
                for ($i = 0; $i <= 59; $i++) {
                    $minutes_list[$i] = $i;
                }

                $open_hours = App\Models\OpenHour::where('setting_id', $organisationStructure->setting_id)->get();
                $set = [];
                $weeklist = [
                    '1' => 'Monday',
                    '2' => 'Tuesday',
                    '3' => 'Wednesday',
                    '4' => 'Thurday',
                    '5' => 'Friday',
                    '6' => 'Saturday',
                    '7' => 'Sunday',
                ];
                foreach ($open_hours as $item) {
                    if (array_key_exists($item->dayOfWeek, $weeklist)) {
                        unset($weeklist[$item->dayOfWeek]);
                    }
                }
                ?>

                <!-- Start Time Field -->
                <div class="form-group col-sm-12" id="datepairExample">
                    <div class="row form-group">
                        <div class="col-sm-4 text-right">
                            {!! Form::label('start_time', 'Start Time:') !!}    
                        </div>
                        <div class="col-sm-8">
                            {!! Form::text('start_time' , null, ['class' => 'form-control time start']) !!}
                        </div>    
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-4 text-right">
                            {!! Form::label('end_time', 'Finish Time:') !!}
                        </div>
                        <div class="col-sm-8">
                            {!! Form::text('end_time' , null, ['class' => 'form-control time end']) !!}
                        </div>
                    </div>
                </div>


                <!-- Start Time Field -->
                <div class="form-group col-sm-12">
                    <div id="datepairExample2" class="row">
                        <div class="col-sm-4 text-right">
                            {!! Form::label('Breaks', 'What is the total length of breaks included during these business hours?') !!}
                            <p><small>(Breaks include any unpaid time during business hours and will not be included when calculating leave)</small></p>
                        </div>
                        <div class="col-sm-4">
                            {!! Form::label('breakHours', 'Hours') !!}
                            {!! Form::select('breakHours' ,$hours_list, null, ['class' => 'form-control time start']) !!}
                        </div>
                        <div class="col-sm-4">
                            {!! Form::label('breakMins', 'Minutes') !!}
                            {!! Form::select('breakMins', $minutes_list , null, ['class' => 'form-control time end']) !!}
                        </div>
                    </div>
                </div>

                <!-- Dayofweek Field -->
                <div class="form-group col-sm-12">
                    <div class="row">
                        <div class="col-sm-4 text-right">
                            {!! Form::label('dayOfWeek', 'Select the days of the week this setting applies to') !!}                            
                        </div>
                        <div class="col-sm-8">
                            <?php foreach ($weeklist as $key => $value) { ?>
                                <label><input  name="dayOfWeek[]" type="checkbox" value="<?= $key ?>"/> <?= $value ?></label><br/>
                            <?php } ?>
                        </div>
                    </div>
                </div>


                <!--Hidden Field for Org ID-->
                <div class="form-group col-sm-12">
                    {!! Form::hidden('org_id',$organisationStructure->id , ['class' => 'form-control']) !!}
                </div>


                <!-- Submit Field -->
                <div class="form-group col-sm-8 col-sm-offset-4">
                    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                    <a href="javascript:return false;" data-href="<?= "/openHours/?org_id=" . $organisationStructure->id ?>" class="btn btn-default button-open-right">Back</a>
                </div>

                <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
                {!! $validator !!}

                {!! Form::close() !!}



            </div>
        </div>
    </div>
</div>

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
        'showDuration': false,
        'disableTextInput': true,
        'timeFormat': 'G:i:s',
        //      'timeFormat': 'h:ia',
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
@endsection
