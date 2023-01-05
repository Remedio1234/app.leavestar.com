
<table id="leaveHistoryTable" class="display table table-bordered table-striped" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Department</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Leave Type</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($leaveHistorys as $leaveHistory)
        <?php
        $now = strtotime(date('Y-m-d H:i:s'));
        if ($now < strtotime($leaveHistory->start_date)) {
            $status = "Up Coming";
        }
        if (($now >= strtotime($leaveHistory->start_date)) && ($now <= strtotime($leaveHistory->end_date))) {
            $status = "On going";
        }
        if ($now > strtotime($leaveHistory->end_date)) {
            $status = "Finished";
        }
        ?>
        <tr>
            <td><?= \App\User::find($leaveHistory->user_id)->name ?></td>
            <td><?= \App\Models\OrganisationStructure::find($leaveHistory->org_id)->name ?></td>
            <td><?= $leaveHistory->start_date ?></td>
            <td><?= $leaveHistory->end_date ?></td>
            <td><?= \App\Models\LeaveType::find($leaveHistory->leave_type_id)->name ?></td>
            <td><?= $status ?></td>        
        </tr>
        @endforeach
    </tbody>
</table>

@section('scripts')
<script>
    $(document).ready(function () {
        $('#leaveHistoryTable').DataTable({
            initComplete: function () {
                this.api().columns(['5']).every(function () {
                    var column = this;
                    var select = $('<select style="margin-left: 10px;"><option value=""></option></select>')
                            .appendTo($(column.header()))
                            .on('change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                        );

                                column
                                        .search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                            });

                    column.data().unique().sort().each(function (d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>')
                    });
                });
            }
        });
    });
</script>    
@append
