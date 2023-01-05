<table class="table table-responsive" id="accountingSoftwares-table">
    <thead>
        <th>Name</th>
        <th>Version</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($accountingSoftwares as $accountingSoftware)
        <tr>
            <td>{!! $accountingSoftware->name !!}</td>
            <td>{!! $accountingSoftware->version !!}</td>
            <td>
                {!! Form::open(['route' => ['accountingSoftwares.destroy', $accountingSoftware->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('accountingSoftwares.show', [$accountingSoftware->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('accountingSoftwares.edit', [$accountingSoftware->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>