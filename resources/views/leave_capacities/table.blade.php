<table class="table table-responsive table-striped" id="leaveCapacities-table">
    <thead>
        <th>User Id</th>
        <th>Org Id</th>
        <th>Leave Type Id</th>
        <th>Capacity</th>
        <th>Last Update Date</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($leaveCapacities as $leaveCapacity)
        <tr>
            <td>{!! $leaveCapacity->user_id !!}</td>
            <td>{!! $leaveCapacity->org_id !!}</td>
            <td>{!! $leaveCapacity->leave_type_id !!}</td>
            <td>{!! $leaveCapacity->capacity !!}</td>
            <td>{!! $leaveCapacity->last_update_date !!}</td>
            <td>
                {!! Form::open(['route' => ['leaveCapacities.destroy', $leaveCapacity->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('leaveCapacities.show', [$leaveCapacity->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('leaveCapacities.edit', [$leaveCapacity->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>