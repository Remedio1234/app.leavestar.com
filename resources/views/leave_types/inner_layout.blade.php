 
<div class="content"> 

    <div class="box box-primary">
        <h1>
            Settings
             
        </h1>

        <div class="box-body">
            <div class="row">
                <div class="col-md-12">

                    <!-- tabs left -->
                    <div class="tabbable">
                        <ul class="nav nav-pills nav-stacked col-md-3">
                            <li ><a class="button-open-right" href=<?= "/organisationStructures/" . $organisationStructure->id . "/edit" ?> >Basic</a></li>
                            <li class="active"><a class="button-open-right" href=<?= "/leaveTypes/?org_id=" . $organisationStructure->id ?>  >Leave Type</a></li>
                            <li ><a class="button-open-right" href= <?= "/blockDates/?org_id=" . $organisationStructure->id ?> >Block Dates</a></li>
                            <li><a class="button-open-right" href=<?= "/customHolidays/?org_id=" . $organisationStructure->id ?>  >Custom Public Holiday</a></li>
                            <li><a class="button-open-right" href=<?= "/sickLeaves/?org_id=" . $organisationStructure->id ?>  >Sick Leave Setting</a></li>
                        </ul>
                        <div class="col-md-9">
                            <div class="tab-pane active" id="leave_type">

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
                            </div>

                        </div>
                        <!-- /tabs -->
                    </div>

                </div>
            </div>
        </div>
    </div>
</div> 