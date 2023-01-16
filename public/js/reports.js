$(function () {
    if(window.location.pathname == '/reports'){
        var date_from = $('input[name="date_from"]').val()
        var date_to = $('input[name="date_to"]').val()
        getAttendanceData(date_from, date_to);

        function getAttendanceData(d_from, d_to){
            $.ajax({
                url: "/reports/attendance?from="+d_from+'&to='+d_to,
                type: "GET",
                dataType : "html",
                success: function( data ) {
                    $("#attendance_table").find('tbody').html(data)
                },
                error: function( xhr, status ) {
                    alert( "Sorry, there was a problem!" );
                }
            });
        }

        $(document).on('click', '#btnExport', function(e){
            e.preventDefault();
            $("#attendance_table").table2excel({
                filename: "Attendance.xls"
            });
        });

        $('input[name="date_from"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2023,
            maxYear: parseInt(moment().format('YYYY'),10)
        }, function(start, end, label) {
            date_from = ''
            date_from = start.format('MM/D/YYYY')
            getAttendanceData(date_from, date_to);
            $("#txt_from").text(start.format('D/MM/YYYY'))
        });
        $('input[name="date_to"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2023,
            maxYear: parseInt(moment().format('YYYY'),10)
        }, function(start, end, label) {
            date_to = ''
            date_to = start.format('MM/D/YYYY')
            $("#txt_to").text(start.format('D/MM/YYYY'))
            getAttendanceData(date_from, date_to);
        });
    }
});