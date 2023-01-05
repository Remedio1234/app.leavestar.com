<table id="XeroConnection" class="display table table-striped"  cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Organasation</th>
            <th>Xero Connected</th>
            <th>Connected Name</th>
            <th  >Action</th>
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
                <td>{!!  ($organisationUser->xero_id=='')?'No':'Yes' !!}</td>
                <td>{!!  $organisationUser->xero_name !!}</td>
                <td>

                    <a onclick = "return confirm('Are you sure?')" class = 'btn btn-danger btn-xs' href="<?php echo action('OrganisationUserController@removeXerotoken', $organisationUser->id) ?>"><i class="glyphicon glyphicon-trash"></i> Remove Token</a>

                </td>
            </tr>
        <?php } ?>
        @endforeach
    </tbody>
</table>

@section('scripts')
<script>
    $(document).ready(function () {
        $('#XeroConnection').DataTable();
    });
</script>    
@append