<?php
// $class_create = ($view == 'create') ? "active" : "normal";
// $class_index = ($view == 'index') ? "active" : "normal";
//$class_manage = ($view == 'manage') ? "active" : "normal";
?>

<div class="content"> 

    <div class="box box-primary">

        <div class="box-body">


            <ul class="nav nav-pills nav-stacked col-md-3">
                <li><a class='  button-close-right' role='button'>close&times;</a></li>
                <li ><a class="button-open-right" href="javascript:return false;" data-href="/leaveApplications/create/"> Apply for Leave</a></li>
                <li ><a class="button-open-right" href="javascript:return false;" data-href="/leaveApplications" >Application History</a></li>

            </ul>

            <div class="col-md-9 sidebar-content">
                <div class="tab-pane active">

                    <?php if (isset($alert)) {
                        ?>
                        <div class="alert alert-success">
                            <?= $alert ?>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button>
                        </div>
                        <?php
                    }
                    ?>
                    @yield('content')
                    @yield('scripts')
                </div>
            </div>

        </div>
    </div>
</div>  