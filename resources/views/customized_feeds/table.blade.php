<table class="table table-responsive table-striped" id="customizedFeeds-table">
    <thead>

    <th>Feed</th>
    <th>Description</th>
    <th colspan="3">Action</th>
</thead>
<tbody>
    @foreach($customizedFeeds as $customizedFeed)
    <tr>

        <td>{!! substr($customizedFeed->feed, 0, 50).((strlen($customizedFeed->feed) > 50)?"...":"") !!}</td>      
        <td>{!! $customizedFeed->description !!}</td>

        <td>
            {!! Form::open(['route' => ['customizedFeeds.destroy', $customizedFeed->id], 'method' => 'delete']) !!}
            <div class='btn-group'>

                <a href="{!! route('customizedFeeds.edit', [$customizedFeed->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
</tbody>
</table>