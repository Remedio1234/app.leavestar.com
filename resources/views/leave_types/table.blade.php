<table class="table table-responsive table-striped" id="leaveTypes-table">
    <thead>

    <th>Name</th>
    <th>Description</th>
    <th colspan="3">Action</th>
</thead>
<tbody>
    @foreach($leave_type as $leaveType)
    <tr>

        <td>{!! $leaveType->name !!}</td>
        <td>{!! $leaveType->description !!}</td>
        <td>
            {!! Form::open(['route' => ['leaveTypes.destroy', $leaveType->id], 'method' => 'delete', 'class'=>'form-render2']) !!}
            <div class='btn-group'>
                {!! Form::hidden('org_id',$organisationStructure->id , ['class' => 'form-control']) !!}
                <a href="javascript:return false;" data-href="{!! route('leaveTypes.edit', [  'id'=>$leaveType->id,'org_id'=>$organisationStructure->id  ]) !!}" class='button-open-right'><i class="glyphicon glyphicon-edit"></i></a>
                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn-hidden', 'onclick' => "return confirm('All the related resoureces will be deleted(Leave Applications with this leave type etc.).Are you sure you want to continue? ')"]) !!}
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
</tbody>
</table>