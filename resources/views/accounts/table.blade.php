<table class="table table-responsive table-striped" id="accounts-table">
    <thead>
    <th>ID</th>
    <th>Name</th>

    <th colspan="3">Action</th>
</thead>
<tbody>
    @foreach($accounts as $account)
    <tr>
        <td>{!! $account->id !!}</td>
        <td>{!! $account->name !!}</td>

        <td>
            {!! Form::open(['route' => ['accounts.destroy', $account->id], 'method' => 'delete']) !!}
            <div class='btn-group'>
                <a href="{!! route('accounts.show', [$account->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                <a href="{!! route('accounts.edit', [$account->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
</tbody>
</table>


<div class="pagination"> {{ $accounts->links() }} </div>