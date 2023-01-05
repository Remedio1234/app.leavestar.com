<table class="table table-responsive table-striped" id="leaveAccrualSettings-table">
    <thead>

    <th>Leave Type </th>
    <th>Detail</th>
    <th colspan="3">Action</th>
</thead>
<tbody>
    @foreach($leaveAccrualSettings as $leaveAccrualSetting)
    <tr>

        <td><?php
            $leave_id = $leaveAccrualSetting->leave_type_id;
            $leavetype = \App\Models\LeaveType::find($leave_id);
            echo $leavetype->name;
            ?></td>
        <td>
            <?= \App\Models\LeaveAccrualSetting::getDetails($leaveAccrualSetting) ?>
        </td>

        <td>
            {!! Form::open(['route' => ['leaveAccrualSettings.destroy', $leaveAccrualSetting->id], 'method' => 'delete','class'=>'form-render2']) !!}
            <div class='btn-group'>
                {!! Form::hidden('org_id',$organisationStructure->id , ['class' => 'form-control']) !!}
                <a href="javascript:return false;" data-href="{!! route('leaveAccrualSettings.edit', ['id'=>$leaveAccrualSetting->id,'org_id'=>$organisationStructure->id]) !!}" class='button-open-right'><i class="glyphicon glyphicon-edit"></i></a>
                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn-hidden ', 'onclick' => "return confirm('Are you sure?')"]) !!}
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
</tbody>
</table>