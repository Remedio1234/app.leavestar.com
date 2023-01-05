<?php
foreach ($tree as $item) {
    $org_list[$item] = App\Models\OrganisationStructure::find($item)->name;
}
$first_org_id = $tree[0];
?>


<!-- Name Field -->
<div class="form-group col-sm-12">
    {!! Form::label('org_id', 'Invite to:') !!}
    {!! Form::select('org_id',$org_list, null, ['class' => 'form-control']) !!}
</div>

<!-- Email Field -->
<div class="form-group col-sm-12">
    {!! Form::label('email', 'Email:') !!} 
    {!! Form::email('email',  null, ['class' => 'form-control'  ] ) !!}
</div>

<!-- Name Field -->
<div class="form-group col-sm-12">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control','id'=>'name']) !!}
</div>

<!-- Is Admin Field -->
<div class="form-group col-sm-12">
    {!! Form::label('is_admin', 'Is Admin:') !!}
    {!! Form::select('is_admin',array('no' => 'Normal User', 'yes' => 'Manager'), null, ['class' => 'form-control']) !!}
</div>

<!-- Phone Field -->
<div class="form-group col-sm-12">
    {!! Form::label('phone', 'Phone:') !!}
    {!! Form::text('phone', null, ['class' => 'form-control']) !!}
</div>

<!-- Birthday Field -->
<div class="form-group col-sm-12">
    {!! Form::label('birthday', 'Birthday:') !!}
    {!! Form::date('birthday', null, ['class' => 'form-control']) !!}
</div>


<div id="dynamic_field">
    <table class="table table-striped">
        <tr>
            <th>Leave Type</th>
            <th>Balance</th>
        </tr>
        <?php
        $leave_accrual_setting = \App\Models\LeaveAccrualSetting::findSetting($first_org_id);
        foreach ($leave_accrual_setting as $item) {
            ?>
            <tr>
                <td><?= \App\Models\LeaveType::find($item->leave_type_id)->name ?></td>
                <td>
                    {!! Form::text('hours_'.$item->leave_type_id, null, ['class' => 'form-control','placeholder'=>'Hours']) !!}
                </td>
            </tr>

            <?php
        }
        ?>

    </table>
</div>



<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('organisationUsers.index') !!}" class="btn btn-default">Cancel</a>
</div>


@section('scripts')
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
{!! $validator !!}



<script>
$(function () {

    $("#org_id").on('change', function () {
        var myurl = "/userRegister/returnlist";
        var org_id = $(this).val();
        $.ajax({
            url: myurl,
            type: "get",
            datatype: "html",
            data: {org_id: org_id},
        })
                .done(function (data) {
                    $('#dynamic_field').empty().html(data);

                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });


    });


});




</script>
@append

