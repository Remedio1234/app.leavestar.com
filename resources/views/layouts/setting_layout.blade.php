<?php
$class_basic = ($view == 'basic') ? "active" : "normal";
$class_leavetype = ($view == 'leavetype') ? "active" : "normal";
$class_leavearrual = ($view == 'leavearrual') ? "active" : "normal";
$class_openhour = ($view == 'openhour') ? "active" : "normal";
$class_blockdate = ($view == 'blockdate') ? "active" : "normal";
$class_customholiday = ($view == 'custom_holiday') ? "active" : "normal";
$class_sickleave = ($view == 'sickleave') ? "active" : "normal";
$class_xero = ($view == 'xeroconnection') ? "active" : "normal";
?>
<div class="content"> 

    <div class="box box-primary">

        <div class="box-body">

            <!-- tabs left -->

            <ul class="nav nav-pills nav-stacked col-md-3">
                <li><a class='  button-close-right' role='button'>&laquo; Back</a></li>
                <!-- <li class=<?= $class_basic ?>><a id="basic_setting" class="button-open-right" href="javascript:return false;" data-href=<?= "/organisationStructures/" . $organisationStructure->id . "/edit" ?> >Branch Settings</a></li>
                <li class=<?= $class_leavetype ?> ><a id="leavetype_setting" class="button-open-right" href="javascript:return false;" data-href=<?= "/leaveTypes/?org_id=" . $organisationStructure->id ?>  >Leave Types</a></li>
                <li class=<?= $class_leavearrual ?> ><a id="leaveaccural_setting" class="button-open-right" href="javascript:return false;" data-href=<?= "/leaveAccrualSettings/?org_id=" . $organisationStructure->id ?>  >Leave Accrual Settings</a></li>
                <li class=<?= $class_openhour ?>><a id="openhours_setting" class="button-open-right" href="javascript:return false;" data-href=<?= "/openHours/?org_id=" . $organisationStructure->id ?>   >Business Hours</a></li>
                <li class=<?= $class_blockdate ?>><a id="blockdates_setting" class="button-open-right" href="javascript:return false;" data-href=<?= "/blockDates/?org_id=" . $organisationStructure->id ?> >Block Dates</a></li> -->
                <li class=<?= $class_customholiday ?>><a id="holidays_setting" class="button-open-right"  href="javascript:return false;" data-href=<?= "/customHolidays/?org_id=" . $organisationStructure->id ?>  >Custom Holidays</a></li>
                <!-- <li class=<?= $class_sickleave ?>><a id="sickleaves_setting" class="button-open-right" href="javascript:return false;" data-href=<?= "/sickLeaves/?org_id=" . $organisationStructure->id ?>   >Sick Leave Settings</a></li>
                <?php if (\App\Models\OrganisationStructure::isOrgRoot($organisationStructure->id)) { ?> 
                    <li class=<?= $class_xero ?>><a id="xero_setting" class="button-open-right" href="javascript:return false;" data-href=<?= "/xero/?org_id=" . $organisationStructure->id ?> >Xero Settings</a></li>
                <?php } ?> -->
                <!--   <li><a href="/xero/connect">Sycronize with Xero</a></li>-->
            </ul>
            <div class="col-md-9 sidebar-content">
                <div class="tab-pane active" id="block_date">

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


                <!-- /tabs -->
            </div>

        </div>
    </div>
</div> 