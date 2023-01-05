<table class="table table-responsive table-striped" id="registerCapacities-table">
    <thead>
        <th>Register Id</th>
        <th>Leave Type Id</th>
        <th>Capacity</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($registerCapacities as $registerCapacity)
        <tr>
            <td>{!! $registerCapacity->register_id !!}</td>
            <td>{!! $registerCapacity->leave_type_id !!}</td>
            <td>{!! $registerCapacity->capacity !!}</td>
            <td>
                {!! Form::open(['route' => ['registerCapacities.destroy', $registerCapacity->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('registerCapacities.show', [$registerCapacity->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('registerCapacities.edit', [$registerCapacity->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>