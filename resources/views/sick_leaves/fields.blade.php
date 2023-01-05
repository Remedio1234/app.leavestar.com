


<!-- Rule Type Field -->
<div class="form-group col-sm-12">
    {!! Form::label('rule_type', 'Rule Type:') !!}
    {!! Form::select('rule_type', array('0' => 'Based on Day', '1' => 'Based on Number of Day in row'),null,['class' => 'form-control']) !!}
</div>

<div class="render-content">


</div>


<!--Hidden Field for Org ID-->
<div class="form-group col-sm-12">
    {!! Form::hidden('org_id',$organisationStructure->id , ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Submit', ['class' => 'btn btn-primary ']) !!}
    <a href="javascript:return false;" data-href="<?= "/sickLeaves/?org_id=" . $organisationStructure->id ?>" class="btn btn-default button-open-right">Back</a>
</div>

<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
{!! $validator !!}


<script>
$(function () {
    var sick_leaves = <?= isset($sick_leaves->value) ? json_encode($sick_leaves->value) : "''" ?>;
    var type = $('#rule_type').val();
    $.ajax({
        url: "/sickLeave/render-partical",
        type: "get",
        datatype: "html",
        data: {type: type, sick_leaves: sick_leaves},
    })
            .done(function (data) {
                $('.render-content').empty().html(data);
            })
            .fail(function (jqXHR, ajaxOptions, thrownError) {

            });

    $('.form-render').ajaxForm({
        beforeSubmit: function (arr, $form, options) {
            if ($(this).find('.has-error').length > 0) {
                return false;
            }
        },
        success: function (data) {
            $('.right-sidebar').html(data);
            //$('<div class="alert alert-success">' + data + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button></div>').insertAfter('.right-sidebar>.content>.box-primary > h1');
        },
    });

    $('#rule_type').on('change', function () {
        var type = $(this).val();

        var sick_leaves = <?= isset($sick_leaves->value) ? json_encode($sick_leaves->value) : "''" ?>;
        $.ajax({
            url: "/sickLeave/render-partical",
            type: "get",
            datatype: "html",
            data: {type: type, sick_leaves: sick_leaves},
        })
                .done(function (data) {
                    $('.render-content').empty().html(data);
                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {

                });

    });

});
</script>