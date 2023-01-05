<table class="table table-responsive table-striped" id="sickLeaves-table">
    <thead>

    <th>Rule Type</th>
    <th>Value</th>
    <th colspan="2">Action</th>
</thead> 
<tbody>
    @foreach($sick_leaves as $sickLeave)
    <tr>

        <td>{!! ($sickLeave->rule_type==0)?"Based on Days":"Based on Number" !!}</td>
        <td>{!! $sickLeave->value !!}</td>
        <td>
            {!! Form::open(['route' => ['sickLeaves.destroy', $sickLeave->id], 'method' => 'delete','class'=>'form-render2']) !!}
            <div class='btn-group'>
                {!! Form::hidden('org_id',$organisationStructure->id , ['class' => 'form-control']) !!}

                <a href="javascript:return false;" data-href="{!! route('sickLeaves.edit', ['id'=>$sickLeave->id,'org_id'=>$organisationStructure->id ]) !!}" class='  button-open-right'><i class="glyphicon glyphicon-edit"></i></a>
                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn-hidden ', 'onclick' => "return confirm('Are you sure?')"]) !!} 
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
</tbody>
</table>