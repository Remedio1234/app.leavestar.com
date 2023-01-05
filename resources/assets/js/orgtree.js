$().ready(function() {



    var ns = $('ol.sortable').nestedSortable({
        forcePlaceholderSize: true,
        protectRoot: true,
        handle: 'div',
        helper: 'clone',
        items: 'li',
        opacity: .6,
        placeholder: 'placeholder',
        revert: 250,
        tabSize: 25,
        tolerance: 'pointer',
        toleranceElement: '> div',
        maxLevels: 10,
        isTree: true,
        expandOnHover: 700,
        startCollapsed: false,
        change: function() {

        }
    });

    // Button click will make ajax call
    // Render partical page without refreshing the page
    $(document.body).on('click', '.button-open-right', function(e) {
        //var myurl = $(this).attr('href');
        var myurl = $(this).attr('data-href');
        $.ajax({
                url: myurl,
                type: "get",
                datatype: "html",
                beforeSend: function() {
                    if ($(".wrapper").hasClass("open-right-sidebar")) {
                        $('#ajax-loading-inner').show();
                    } else {
                        $('#ajax-loading-fullscreen').show();
                    }
                }
            })
            .done(function(data) {
                //If Render Include sidebar , means it's a redirect
                var checkString = 'class="sidebar"';
                if (data.indexOf(checkString) !== -1) {
                    window.location.href = myurl;
                } else {
                    $(".right-sidebar").empty().html(data);
                    if ($(".wrapper").hasClass("open-right-sidebar")) {
                        $('#ajax-loading-inner').hide();
                    } else {
                        $('#ajax-loading-fullscreen').hide();
                    }
                    $(".wrapper").addClass("open-right-sidebar");
                }



            })
            .fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        return false;
    });

    $(document.body).on('click', '.button-close-right', function(e) {
        $('#ajax-loading-inner').show();
        $(".wrapper").removeClass("open-right-sidebar");
        setTimeout(function() {
            $('#ajax-loading-inner').hide();
            $(".right-sidebar").html("");
        }, 500);
    });



});
