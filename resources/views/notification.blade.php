 
<div class="col-lg-4">
    <div class="panel panel-default panel-home">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-commenting"></i> Notifications</h3>
        </div>
        <div class="panel-body">
            <?php
            $user = \Auth::user();
            $alerts = [];
            foreach ($user->unreadNotifications as $notification) {
                if ($notification->data['type'] == 'particalLeaves') {
                    $alerts[] = $notification;
                }
            }
            foreach ($alerts as $notification) {
                $url = action('LeaveApplicationController@manageLeave') . '#leaveappid' . $notification->data['leaveapplicationID'];
                $content = 'An partical leave has been approved. You need to create it manually in Xero. It may take up to three hours for this change to be applied.';
                $badge = "alert-danger";
                ?>
                <div class="<?= $badge ?>">
                    <span class="closebtn notificationClose" onclick="this.parentElement.style.display = 'none';" data-href="/setNotificationRead?ID=<?= $notification->id ?>">&times;</span> 
                    <?= '<a class="notificationClose" data-href="/setNotificationRead?ID=' . $notification->id . '" href="' . $url . '">' . $content . '</a>'; ?>
                </div>
                <?php
            }
            foreach ($user->unreadNotifications as $notification) {
                $content = "";
                $badge = "";
                switch ($notification->data['type']) {
                    case 'create':
                        $url = action('LeaveApplicationController@manageLeave') . '#leaveappid' . $notification->data['leaveapplicationID'];
                        $content = $notification->data['from'] . ' just create a new leave application.';
                        $badge = "alert-info";
                        break;
                    case 'approved':
                        $url = action('LeaveApplicationController@myLeaveApplication') . '#leaveappid' . $notification->data['leaveapplicationID'];
                        $content = $notification->data['from'] . ' just ' . $notification->data['type'] . ' your leave application.';
                        $badge = "alert-info";
                        break;
                    case 'rejected':
                        $url = action('LeaveApplicationController@myLeaveApplication') . '#leaveappid' . $notification->data['leaveapplicationID'];
                        $content = $notification->data['from'] . ' just ' . $notification->data['type'] . ' your leave application.';
                        $badge = "alert-info";
                        break;
                    case 'xero':
                        $url = "";
                        $content = \App\User::XeronotificationMessage();
                        $badge = "alert-info";
                        break;
                    case 'comment':
                        $leavepp = isset($notification->data['leaveapplicationID']) ? App\Models\LeaveApplication::find($notification->data['leaveapplicationID']) : null;
                        if (isset($leavepp)) {
                            if (\Auth::user()->id == $leavepp->user_id) {
                                $url = action('LeaveApplicationController@myLeaveApplication') . '#leaveappid' . $notification->data['leaveapplicationID'];
                            } else {
                                $url = action('LeaveApplicationController@manageLeave') . '#leaveappid' . $notification->data['leaveapplicationID'];
                            }
                        } else {
                            $url = "";
                        }
                        $badge = "alert-info";
                        $content = $notification->data['from'] . ' just ' . 'send you a new comment.';
                        break;
                    case 'particalLeaves':
                        continue;
//                        $url = action('LeaveApplicationController@manageLeave') . '#leaveappid' . $notification->data['leaveapplicationID'];
//                        $content = 'An partical leave has been approved. You need to create it manually in Xero';
//                        $badge = "alert-danger";
                        break;
                }
                if ($notification->data['type'] !== 'particalLeaves') {
                    ?>

                    <div class="<?= $badge ?>">
                        <span class="closebtn notificationClose" onclick="this.parentElement.style.display = 'none';" data-href="/setNotificationRead?ID=<?= $notification->id ?>">&times;</span> 
                        <?= '<a class="notificationClose" data-href="/setNotificationRead?ID=' . $notification->id . '" href="' . $url . '">' . $content . '</a>'; ?>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(function () {
        $('.notificationClose').on('click', function () {
            var url = $(this).data('href');

            $.ajax({
                url: url,
                type: "get",
                datatype: "html",
            })
                    .done(function (data) {


                    })
                    .fail(function (jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
        });
    });
</script>
@append
<style>

    /* The alert message box */
    .alert-info {
        padding: 20px;
        background-color: #1858a4;  
        color: white;
        margin-bottom: 15px;
    }
    .alert-danger {
        padding: 20px;
        background-color: #ca1629;
        color: white;
        margin-bottom: 15px;
    }

    /* The close button */
    .closebtn {
        margin-left: 15px;
        color: white;
        font-weight: bold;
        float: right;
        font-size: 22px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
    }

    /* When moving the mouse over the close button */
    .closebtn:hover {
        color: black;
    }
</style>




