<table class="table table-responsive table-striped" id="blockDates-table">
    <thead>

    <th>Start Date</th>
    <th>End Date</th>
    <th>Description</th>
    <th>Limits</th>
    <th colspan="2">Action</th>
</thead>
<tbody>
    @foreach($block_dates as $blockDate)
    <tr>

        <td>{!! \App\Models\Setting::getLocalTime(Session::get('current_org'), $blockDate->start_date)  !!}</td>
        <td>{!! \App\Models\Setting::getLocalTime(Session::get('current_org'), $blockDate->end_date) !!}</td>
        <td>{!! $blockDate->description !!}</td>
        <td>{!! $blockDate->limits !!}</td>
        <td>
            {!! Form::open(['route' => ['blockDates.destroy', $blockDate->id], 'method' => 'Delete','class'=>'form-render2']) !!}
            <div class='btn-group'>
                {!! Form::hidden('org_id',$organisationStructure->id , ['class' => 'form-control']) !!}
                <a href="javascript:return false;" data-href="{!! route('blockDates.edit', ['id'=>$blockDate->id,'org_id'=>$organisationStructure->id ]) !!}" class='button-open-right'><i class="glyphicon glyphicon-edit"></i></a>
                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn-hidden ', 'onclick' => "return confirm('Are you sure?')"]) !!}
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
</tbody>
</table>