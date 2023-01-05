<div class="container1 container-fluid">
    <div class="row">
        <div class="right_from_xero col-md-12" style=" max-height: 700px; overflow: auto;">
            <h1>Select the people you want to invite:</h1> 
            {!! Form::open(['action' => 'XeroUsaController@Invite','method'=>'post','class'=>'invitation']) !!} 
            <!--<label class="label label-primary"><input type="checkbox" value="1" id="select-all-invite"/> Select All</label>-->
            <button type="button" class="btn btn-default" id="select-all-invite"><span class="glyphicon glyphicon-unchecked"></span> Select All</button>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Invite</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>User level</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <?php
                foreach ($tree as $item) {
                    $org_list[$item] = App\Models\OrganisationStructure::find($item)->name;
                }

                $label_class = [
                    "invited" => "success",
                    "not_invited" => "warning",
                ];

                foreach ($_SESSION ['XeroUsers'] as $key => $item) {
                    $missing_email = (!isset($item['Email'])) ? true : false;
//                    $disable = (isset($item['Email'])) ? "" : "Disabled";
                    $userregisted = \App\Models\UserRegister::where([
                                'xero_id' => $item['EmployeeID']
                            ])->whereIn('org_id', $tree)->first();
                    $invited_class = (isset($userregisted)) ? "invited" : "not_invited";
                    $disable = (  $missing_email) ? true : false;

                   // $org = ($userregisted) ? \App\Models\OrganisationStructure::find($userregisted->org_id)->name : "";
                    ?>
                    <tr>
                        <td>
                            <input class="not_invited<?php // /echo $invited_class     ?>" type="checkbox" name="users[]" value="<?= $key ?>"   <?php  echo ($disable) ? "disabled" : ""     ?>  >      
                        </td>
                        <td>
                            <?= $item['FirstName'] . " " . $item['LastName'] ?> <?= ($missing_email) ? "<span class='label label-danger'>(Email not set in Xero)</label>" : "" ?>
                        </td>
                        <td>
                            <?php if (!$disable) { ?> 
                                {!! Form::select('org['.$key.']', $org_list,null,['class' => 'form-control']) !!}
                                <?php
                            } else if ($userregisted) {
                                ?>    
                                {!! Form::select('org['.$key.']', $org_list,null,['class' => 'form-control']) !!}
                                <?php
//echo $org;
                            }
                            ?>
                        </td>
                        <td>
                            <?php if (!$disable) { ?> 
                                {!! Form::select('role['.$key.']', array('no' => 'Normal User', 'yes' => 'Manager'),null,['class' => 'form-control']) !!}
                                <?php
                            } else if ($userregisted) {
                                ?>
                                {!! Form::select('role['.$key.']', array('no' => 'Normal User', 'yes' => 'Manager'),null,['class' => 'form-control']) !!}
                                <?php
                                //echo ($userregisted->is_admin == "yes") ? "Manager" : "Normal User";
                            }
                            ?>
                        </td>
                        <td>
                            <span class="label label-<?= $label_class[$invited_class] ?> <?= $invited_class ?>" style="display: block; text-align: center; padding: 6px;"><?= ucwords(str_replace("_", " ", $invited_class)) ?></span>  
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>



            <?php
//var_dump($_SESSION ['XeroUsers']);
            ?>

        </div>
    </div>       
    <input  type="submit" class="btn btn-primary "  >
    {!! Form::close() !!}
</div>

<div class="matching_loading" style="width:100%; height:100%; position:fixed; top:0px;left:0px; background-color:rgba(85, 126, 167, 0.54);z-index:1000;text-align: center;
     padding-top: 400px;
     font-size: 100px; 
     display:none;">
    loading...
</div>

<div class="matching_finish" style="width:100%; height:100%; position:fixed; top:0px;left:0px; background-color:rgba(85, 126, 167, 0.54);z-index:1000;text-align: center;
     padding-top: 400px;
     font-size: 100px; 
     display:none;">
    Matching Success! 
    <a href="/" class="btn">Go Back</a>
</div>


<script>
    $(function () {

        $("#select-all-invite").click(function () {

            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
                $(this).find("span").removeClass("glyphicon-check").addClass("glyphicon-unchecked");
                $("input.not_invited").trigger("click");
            } else {
                $(this).addClass("active");
                $(this).find("span").addClass("glyphicon-check").removeClass("glyphicon-unchecked");
                $("input.not_invited:not(:checked)").trigger("click");
            }
        });

        $("input.not_invited").click(function () {
            if (!$(this).is(":checked") && $("#select-all-invite").hasClass("active")) {
                $("#select-all-invite").removeClass("active");
                $("#select-all-invite").find("span").removeClass("glyphicon-check").addClass("glyphicon-unchecked");
            }
        });
    });
</script>


