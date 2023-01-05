@extends('layouts.app')

@section('content')

<?php
if ($type == 'leavetype') {
    ?>

    <div class="container1">
        <div class="content">
            <div class="container-fluid"> 
                <h1>
                    Synchronise Leave Types
                </h1>
                <hr>

                <div class="row">
                    <div class="left_from_db col-md-6" style=" max-height: 700px;  ">
                        <div class="alert alert-warning alert-important">
                            <span class="glyphicon glyphicon-alert"></span> The following leave type requires synchronisation with Xero.
                        </div>
                        <div class="tag label label-info"><span><?= $DBLeaveTypes[$step]['name'] ?></span></div>
                        <div>
                            <?php
//                            var_dump($_SESSION ['DBLeaveTypes'][$_SESSION ['step']]);
                            ?>
                        </div>
                    </div>
                    <div class="right_from_xero col-md-6" style=" max-height: 700px; overflow: auto;">
                        <h1>Xero</h1> 
                        <?php
                        foreach ($XeroLeaveTypes as $key => $item) {
                            ?>
                            <input type="radio" name="field" value="<?= $key ?>"    ><?= $item->Name ?><br> 
                            <?php
                        }

                        if (empty($XeroLeaveTypes)) {
                            ?>
                            <p><em>There are no leave types in Xero to be synched.<br/>Any additional Leave Star leave types will be created in Xero.</em></p>
                            <?php
                        } else {
//                            var_dump($_SESSION ['XeroLeaveTypes']);
                        }
                        ?>

                        <div class="alert" style="display:none;">
                            <p>You have to select a leave type before you confirm</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <hr/>
        <div class="button" style="text-align: right; padding:40px;">
            <a href="/xero/matching-leavetype" class="btn btn-primary next"  >Confirm and Next</a>
            <a href="/xero/skip?from=leavetype" class="btn skip"  >Skip</a>
            <?php
            if ((isset($iscomplete)) && ($iscomplete == 1)) {
                ?>
                <a href="/xero/ltcomplete" class="btn"  >Complete</a> 
                <?php
            }
            ?>

        </div>
    </div>
    <div class="matching_loading" style="width:100%; height:100%; position:fixed; top:0px;left:0px; background-color:rgba(85, 126, 167, 0.54);z-index:1000;text-align: center;
         padding-top: 400px;
         font-size: 100px; 
         display:none;">
        loading...
    </div>

<?php } else {
    ?>
    <div class="container1">
        <div class="content">
            <div class="container-fluid">
                <h1>
                    Synchronise users
                </h1>
                <hr>
                <div class="row">
                    <div class="left_from_db col-md-6" style=" max-height: 700px; overflow: auto;">
                        <div class="alert alert-warning alert-important">
                            <span class="glyphicon glyphicon-alert"></span> The following user requires synchronisation with Xero.
                        </div>
                        <div>
                            <table class="table table-striped">
                                <tr>
                                    <td>
                                        <?php
                                        echo \App\User::find($DBUsers[$step]['user_id'])->name;
                                        ;
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                       <input type="checkbox" id="skipsychronize" value="true" <?= (isset($DBUsers[$step]['skipsychronize']) && ($DBUsers[$step]['skipsychronize'] == true)) ? "checked" : "" ?>> Do not sychronize this user to Xero.
                                    </td>
                                </tr>
                            </table>
                            <?php
//                            var_dump($_SESSION ['DBUsers'][$_SESSION ['step']]);
                            ?>
                        </div>
                    </div>
                    <div class="right_from_xero col-md-6" style=" max-height: 700px; overflow: auto;">
                        <h1>Users in Xero</h1> 
                        <?php
                        foreach ($XeroUsers as $key => $item) {
                            ?>
                            <label>
                                <input type="radio" name="field" value="<?= $key ?>"    ><?= " " . $item['FirstName'] . " " . $item['LastName'] ?> 
                            </label><br>
                            <?php
                        }
                        ?>

                        <div class="alert" style="display:none;">
                            <p>You have to select a user before you confirm</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>       
        <hr>
        <div class="button" style="text-align: right; padding:40px;">
            <a href="/xero/matching-user" class="btn btn-primary next"  >Confirm</a>
            <a href="/xero/skip?from=users" class="btn skip"  >Skip</a>
            <?php
            if ((isset($iscomplete)) && ($iscomplete == 1)) {
                ?>
                <a href="/xero/usercomplete" class="btn"  >Complete</a> 
                <?php
            }
            ?>

        </div>
    </div>
    <div class="matching_loading" style="width:100%; height:100%; position:fixed; top:0px;left:0px; background-color:rgba(85, 126, 167, 0.54);z-index:1000;text-align: center;
         padding-top: 400px;
         font-size: 100px; 
         display:none;">
        loading...
    </div>

<?php }
?>


@endsection
