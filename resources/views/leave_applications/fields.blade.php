<div class="form-horizontal">
    <div class="form-group">
        {!! Form::label('When', 'When would you like to take leave?',['class' => 'col-md-3 control-label']) !!}
        <div class="col-md-9">
            {!! Form::text('date_range', null, ['class' => 'form-control ajax-check' ]) !!}
            <div id="ruleWarnings" Style="display:none">

            </div>
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('leave_type_id', 'What type of leave would you like to take?',['class' => 'col-md-3 control-label']) !!}   
        <div class="col-md-9">
            <?php
            $first = true;
            foreach ($type_list as $key => $val) {
                ?>
                <div>
                    <label class="radio-inline">
                        {!! Form::radio('leave_type_id',$key,$first,['id' => 'leave_type_id_'.$key, 'class' => 'ajax-check'] ) !!} <?php echo $val ?>
                    </label>    
                </div>
                <?php
                $first = false;
            }
            ?>
            <?php /* {!! Form::select('leave_type_id', $type_list , null,['class' => 'form-control ajax-check']) !!} */ ?>
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('flexible', 'How flexible are you about these dates?',['class' => 'col-md-3 control-label']) !!}
        <div class="col-md-9">
            <div>
                <label class="radio-inline">
                    {!! Form::radio('flexible','0',true ) !!} Not Flexible
                </label>    
            </div>
            <div>
                <label class="radio-inline">
                    {!! Form::radio('flexible','1',false ) !!} Flexible    
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('status', 'Do you want to submit your leave application?',['class' => 'col-md-3 control-label']) !!}
        <div class="col-md-9">
            <div>
                <label class="radio-inline">
                    {!! Form::radio('status','0',true ) !!} Yes
                </label>    
            </div>
            <div>
                <label class="radio-inline">
                    {!! Form::radio('status','3',false ) !!} No (This application will be saved but not submitted)    
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('comment', 'Comments (optional):',['class' => 'col-md-3 control-label']) !!}
        <div class="col-md-9">
            {!! Form::textarea('comment',null, ['class' => 'form-control','rows' => 4]) !!}
        </div>
    </div>


    <?php if ($auto_reply) { ?>

        <div class="form-group">
            {!! Form::label('Auto Reply', 'Enable out of office reply',['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                <?php if (isset($leaveApplication->autoreplysetting) && ($leaveApplication->autoreplysetting == 1)) { ?>
                    {!! Form::checkbox('autoreplysetting','yes',true ) !!}
                <?php } else {
                    ?>
                    {!! Form::checkbox('autoreplysetting','yes',false ) !!}
                <?php } ?>
            </div>
        </div>

        <div class="form-group autoreplymessage">
            {!! Form::label('autoreplymessage', 'Out of office reply:',['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                {!! Form::textarea('autoreplymessage', null, ['class' => 'form-control','rows' => 4,'placeholder'=>'I will be out of office. I will get back to you as soon as possible.']) !!}
            </div>
        </div>
        <script>
            $(function () {
                if ($("input[name='autoreplysetting']").is(':checked')) {
                    $(".autoreplymessage").show();
                } else {
                    $(".autoreplymessage").hide();
                }

                $("input[name='autoreplysetting']").on('change', function () {
                    if ($(this).is(':checked')) {
                        $(".autoreplymessage").show();
                    } else {
                        $(".autoreplymessage").hide();
                    }
                });

            });
        </script>
    <?php } ?>

    <!-- Submit Field -->
    <div class="form-group">
        <div class="col-md-9 col-md-offset-3">
            {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}         
            <a href="javascript:return false;" data-href= "/leaveApplications/" class="btn btn-default button-open-right">Back</a>
        </div>
    </div>
</div>
<!-- Date Range -->


<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
{!! $validator !!}

<?php
$setting_id = App\Models\OrganisationStructure::find(isset($leaveApplication->org_id) ? $leaveApplication->org_id : \Session::get('current_org'))->setting_id;
$customize_holiday = App\Models\CustomHoliday::where('setting_id', $setting_id)->get();
$holidayArray = [];
foreach ($customize_holiday as $holiday) {
    $days = floor((strtotime($holiday->end_date) - strtotime($holiday->start_date)) / (60 * 60 * 24));

    for ($i = 0; $i <= $days; $i++) {
        $start = date("Y-m-d", strtotime($holiday->start_date . ' +' . $i . ' day'));
        $holidayArray[] = $start;
    }
}
$block_days = \App\Models\BlockDate::where('setting_id', $setting_id)->get();
$blockArray = [];
foreach ($block_days as $dates) {
    $days = floor((strtotime($dates->end_date) - strtotime($dates->start_date)) / (60 * 60 * 24));

    for ($i = 0; $i <= $days; $i++) {
        $start = date("Y-m-d", strtotime($dates->start_date . ' +' . $i . ' day'));
        $blockArray[] = $start;
    }
}
?>
<?php
$businessDays = App\Models\OpenHour::where('setting_id', $setting_id)->get();
$businessArray = [];
foreach ($businessDays as $dates) {
    $businessArray[] = ($dates->dayOfWeek == '7') ? "0" : $dates->dayOfWeek;
}
?>
<script type="text/javascript">
        $(function () {
            $('input[name="date_range"]').daterangepicker({
                timePicker: true,
                timePicker24Hour: false,
                timePickerIncrement: 5,
                locale: {
                    format: '<?= App\Models\Setting::getFrontEndDF(isset($leaveApplication->org_id) ? $leaveApplication->org_id : \Session::get('current_org') ) . ' h:mm A' ?>'
                },
                isCustomDate: function (date) {
                    var string = date.format('YYYY-MM-DD');
                    var from = string.split("-");
                    var f = new Date(from[0], from[1] - 1, from[2]);

                    var isWeekend = jQuery.inArray(f.getDay(),<?= json_encode($businessArray) ?>);

                    var holidays =<?= json_encode($holidayArray) ?>;
                    var blockdays =<?= json_encode($blockArray) ?>;
                    if (!(isWeekend >= 0)) {
                        return 'style_weekend';
                    }
                    if ($.inArray(string, holidays) > -1) {
                        return 'style_holiday';
                    }
                    if ($.inArray(string, blockdays) > -1) {
                        return 'style_block';
                    }


                }
            });
        });</script>


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
                app.$children[0].$refs.calendar.$emit('refetch-events');
            },
        });
    });</script>

<script>
    $(function () {
        $(".ajax-check").on('change', function () {
//            var leave_type_id = $("#leave_type_id").val();
            var leave_type_id = $("input:radio[name='leave_type_id']:checked").val();
            var date_range = $("input[name='date_range']").val();
            var LeaveID = '<?php echo (isset($leaveApplication)) ? $leaveApplication->id : "" ?>';
            $('#ruleWarnings').show();
            $.ajax({
                url: '/leaveApplication/check-application/',
                type: "get",
                data: {date_range: date_range, leave_type_id: leave_type_id, leaveAppId: LeaveID},
                datatype: "html",
            })
                    .done(function (data) {
                        $('#ruleWarnings').empty().html(data);
                    })
                    .fail(function (jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });


        });

    });
</script>
