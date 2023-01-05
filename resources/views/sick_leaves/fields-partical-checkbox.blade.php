
<?php
$weeklist = [
    '1' => 'Monday',
    '2' => 'Tuesday',
    '3' => 'Wednesday',
    '4' => 'Thurday',
    '5' => 'Friday',
    '6' => 'Saturday',
    '7' => 'Sunday',
];
$array = [];
if (isset($model)) {
    $array = explode(",", $model);
}
?> 



<div class="form-group col-sm-12">
    {!! Form::label('value2', 'Select Days') !!}
    <?php
    foreach ($weeklist as $key => $value) {
        ?>
        <input type="checkbox" name="value[]" value="<?= $key ?>"  <?= in_array($key, $array) ? "checked" : "" ?> > <?= $value ?>
        <?php
    }
    ?>
</div>
