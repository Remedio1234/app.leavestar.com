<table class="table table-responsive table-striped" id="accountingTokens-table">
    <thead>
        <th>Org Str Id</th>
        <th>Accsoft Id</th>
        <th>Token</th>
        <th>Secret Token</th>
        <th>Refresh Token</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($accountingTokens as $accountingToken)
        <tr>
            <td>{!! $accountingToken->org_str_id !!}</td>
            <td>{!! $accountingToken->accsoft_id !!}</td>
            <td>{!! $accountingToken->token !!}</td>
            <td>{!! $accountingToken->secret_token !!}</td>
            <td>{!! $accountingToken->refresh_token !!}</td>
            <td>
                {!! Form::open(['route' => ['accountingTokens.destroy', $accountingToken->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('accountingTokens.show', [$accountingToken->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('accountingTokens.edit', [$accountingToken->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>