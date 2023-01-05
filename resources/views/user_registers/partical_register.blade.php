<table class="table table-striped">
    <tr>
        <th>Leave Type</th>
        <th>Balance</th>
    </tr>
    <?php
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

{!! $validator !!}