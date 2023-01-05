
<table id="userCapacity" class="display table table-striped" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Organisation</th>
            <th>Leave Type</th>
            <th>Capacity( Hours )</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $randomOrgId = \Session::get('current_org');
        $root_org = \App\Models\OrganisationStructure::findRootOrg($randomOrgId);
        $leaveTypes = \App\Models\LeaveType::where('org_id', $root_org)->get();
        ?>
        @foreach ($organisationUsers as $organisationUser)
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
        if ($showFlag) {
            ?>
            @foreach ($leaveTypes as $leaveType) 

            <tr>
                <?php
                $capacity = \App\Models\LeaveCapacity::where([
                            'leave_type_id' => $leaveType->id,
                            'user_id' => $organisationUser->user_id,
                            'org_id' => $organisationUser->org_str_id])
                        ->first();
                if (isset($capacity)) {
                    $unit = $capacity->capacity;
                } else {
                    $unit = 0;
                }
                ?>
                <td><?= \App\User::find($organisationUser->user_id)->name ?></td>
                <td><?= \App\Models\OrganisationStructure::find($organisationUser->org_str_id)->name ?></td>

                <td><?= $leaveType->name ?></td>
                <td><?= $unit ?></td>
            </tr>
            @endforeach
            <?php } ?>
        @endforeach
    </tbody>
</table>

@section('scripts')
<script>
    $(document).ready(function () {
        $('#userCapacity').DataTable();
    });
</script>    
@append
