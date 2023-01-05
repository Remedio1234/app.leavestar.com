@foreach($weeklyReports as $weeklyReport)

<?php
    if (!empty($weeklyReport['user_id'])) {
        $user_name = App\User::find($weeklyReport['user_id'])->name;
    } else {
        $user_name = $weeklyReport['name'];
    }
?>
<div id="" class="app-entity leave-panel">
    <div class="">

        <div class="infos col-sm-12">
            <h5 class="status pull-right"></h5>
            <!-- <h4><span class="glyphicon glyphicon-user"></span> user name</h4> -->
            <div class="dates">
                <div style="margin: 20px; font-size: 1.4em; font-weight: 700;"><?= !empty($user_name) ? $user_name : '' ?> <?= !empty($weeklyReport['date']) ? $weeklyReport['date'] : '' ?></div>
            </div>

            <?php if (!empty($weeklyReport['title'])) { ?>
            <table class="table table-condensed table-striped">
                <tr>
                    <th>Type</th>
                    <td class="text-right"><?= !empty($weeklyReport['title']) ? $weeklyReport['title'] : '' ?></td>
                </tr>
            </table>
            <?php } ?>

            <div class="row">
                <div class="col-sm-12">
                    <div class="comment_content">
                    </div>        
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>

@endforeach

@section('scripts')
<script>

</script>  
@append