$(document.body).on('click', '.next', function (e) {
    var myurl = $(this).attr('href');
    var id = $('input[name="field"]:checked').val();
    console.log(id);
    if (id == null) {
        $('.alert').show();
        return false;
    } else {
        $.ajax({
            url: myurl,
            type: "get",
            data: {id: id},
            datatype: "html",
            beforeSend: function () {
                // Handle the beforeSend event

                $(".matching_loading").show();
            },
        })
                .done(function (data) {
                    $(".matching_loading").hide();
                    $(".container1").empty().html(data);
                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
        return false;
    }
});
//$(document.body).on('change', 'input[name="field"]', function (e) {
//    $('.alert').hide();
//});
$(document.body).on('click', '.skip', function (e) {
    var myurl = $(this).attr('href');
    $.ajax({
        url: myurl,
        type: "get",
        datatype: "html",
        beforeSend: function () {
            // Handle the beforeSend event
            $(".matching_loading").show();
        },
    })
            .done(function (data) {
                $(".matching_loading").hide();
                $(".container1").empty().html(data);
            })
            .fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
    return false;
});

$('div.alert').not('.alert-important').delay(3000).fadeOut(350);


$('.invitation').ajaxForm({
    beforeSubmit: function (arr, $form, options) {
        $(".matching_loading").show();
        $(".matching_finish").hide();
    },
    success: function (data) {
        $(".matching_loading").hide();
        $(".matching_finish").show();
    },
});




//$(document.body).on('click', '.invite', function (e) {
//    var myurl = $(this).attr('href');
//    var checkvalue = [];
//    $('input[name="users"]:checked').each(function (i) {
//        checkvalue[i] = $(this).val();
//    });
//    $.ajax({
//        url: myurl,
//        type: "get",
//        data: {result: checkvalue},
//        datatype: "html",
//        beforeSend: function () {
//            // Handle the beforeSend event
//            $(".matching_loading").show();
//        },
//    })
//            .done(function (data) {
//                $(".matching_loading").hide();
//                $(".container1").empty().html(data);
//            })
//            .fail(function (jqXHR, ajaxOptions, thrownError) {
//                alert('No response from server');
//            });
//    return false;
//});