<table class="table table-responsive table-striped" id="organisationUsers-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Organasation</th>
            <th>Email</th>
            <th>Role</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($organisationUsers as $organisationUser)
        <?php
        $showFlag = false;
        $org = $organisationUser->org_str_id;
        if (App\Models\OrganisationStructure::find($org)->parent_id == null) {
            $user_id = $organisationUser->user_id;
            $searchResult = App\Models\OrganisationUser::whereIn('org_str_id', $tree)->where([
                        'user_id' => $user_id,
                    ])->where('org_str_id', '<>', $org)->first();
            if (!isset($searchResult)) {
                $showFlag = true;
            }
        } else {
            $showFlag = true;
        }
        ?>
        <?php
        if ($showFlag) {
            ?>
            <tr>
                <td>{!! App\User::find($organisationUser->user_id)->name !!}</td>
                <td>{!! App\Models\OrganisationStructure::find($organisationUser->org_str_id)->name !!}</td>
                <td>{!! App\User::find($organisationUser->user_id)->email !!}</td>
                <td>{!!  ($organisationUser->is_admin=='yes')?"Manager":"Normal User"  !!}</td>
                <td>
                    {!! Form::open(['route' => ['organisationUsers.destroy', $organisationUser->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>

                        <a href="{!! route('organisationUsers.edit', [$organisationUser->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
            <?php } ?>
        @endforeach
    </tbody>
</table>

<div class="pagination"> {{ $organisationUsers->links() }} </div>
