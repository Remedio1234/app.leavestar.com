<table class="table table-responsive table-striped" id="userRegisters-table">
    <thead>
    <th>Invite To</th>
    <th>Role</th>
    <th>Email</th>
    <th>Name</th>                
    <th>Birthday</th>
    <th>From</th>
    <th colspan="1">Action</th>
</thead>
<?php
?>
<tbody>
    @foreach($userRegisters as $userRegister)
    <tr>
        <td>{!! \App\Models\OrganisationStructure::find($userRegister->org_id)->name  !!}</td>
        <td>{!! ($userRegister->is_admin=="yes")?"Manager":"Normal User" !!}</td> 
        <td>{!! $userRegister->email !!}</td>
        <td>{!! $userRegister->name !!}</td>
        <td>{!! $userRegister->birthday !!}</td>
        <td>{!! ($userRegister->xero_id==null)?"Manually Invited":"Xero Import" !!}</td>
        <td>
            {!! Form::open(['route' => ['userRegisters.destroy', $userRegister->id], 'method' => 'delete']) !!}
            <div class='btn-group'>
               
                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
</tbody>
</table>

<div class="pagination"> {{ $userRegisters->links() }} </div>