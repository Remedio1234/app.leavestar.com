<?php
$query_org = $organisationStructure->id;
$root_org = \App\Models\OrganisationStructure::findRootOrg($query_org);
$leave_types = \App\Models\LeaveType::where([
            'org_id' => $root_org
        ])->get();
$leave_list = [];
foreach ($leave_types as $item) {
    $leave_list[$item->id] = $item->name;
}

$period[0] = 'Day';
$period[1] = 'Week';
$period[2] = 'Month';
$period[3] = 'Year';

$options[0] = "Accrual On Going";
$options[1] = "Accrual at the end of selected period";
?> 

<!-- Leave Type Id Field -->
<div class="form-group col-sm-12">
    {!! Form::label('leave_type_id', 'Leave Type:') !!}
    {!! Form::select('leave_type_id',$leave_list, null, ['class' => 'form-control']) !!}
</div>

<!-- Period Field -->
<div class="form-group col-sm-12">
    {!! Form::label('period', 'Period:') !!}
    {!! Form::select('period', $period , null, ['class' => 'form-control']) !!}
</div>

<!-- Seconds Field -->
<div class="form-group col-sm-12">
    {!! Form::label('seconds', 'Hours:') !!}
    {!! Form::number('seconds', null, ['class' => 'form-control']) !!}
</div>

<!--Hidden Field for Org ID-->
<div class="form-group col-sm-12">
    {!! Form::hidden('org_id',$organisationStructure->id , ['class' => 'form-control']) !!}
</div>

<!-- Options Field -->
<div class="form-group col-sm-12">
    {!! Form::label('options', 'Options:') !!}
    {!! Form::select('options',$options, null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}

    <a href="javascript:return false;" data-href="<?= "/leaveAccrualSettings/?org_id=" . $organisationStructure->id ?>" class="btn btn-default button-open-right">Cancel</a>
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
            //$('<div class="alert alert-success">' + data + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button></div>').insertAfter('.right-sidebar>.content>.box-primary > h1');
        },
    });

});
</script>
