$(function () {
    window.tour = new Tour({
        steps: [
            // Manager special step
            {
                path: '/home',
                element: ".main-sidebar",
                title: "Welcome to LeaveStar",
                placement: "right",
                content: 'This tutorial will guide you through features in LeaveStar. When you click "organisation setting" on the left sidebar, you will be redirected to the page for organisation setting. You can create new organisation level, update or delete the current levels here. ',
                backdrop: true,
                onNext: function (tour) {
                    $('#demo_index1').trigger("click");
                },
            },
            {
                path: '/organisationStructures',
                element: ".content-wrapper.main",
                title: "Organisation Setting",
                placement: "left",
                content: 'When you click "organisation setting" on the left sidebar, you will be redirected to the page for organisation setting. You can create new organisation level, update or delete the current levels here. <br><br>'
                        + 'To create the highest level of your organisation structure, click "create". Enter the name and press "save". <br><br>'
                        + 'To create additional levels, click "create". Enter the name and press "save".  There is no limit to the number of levels you can create. <br><br>'
                        + 'To rearrange your organisation structure, just drag the level to where you want to go: up a level or down a level. Simply select the level you want to move and without letting go of the mouse move it up and down. To finish moving the level, let go of the mouse and it will drop into position. <br><br>'
                        + 'To remove the level, click “delete” button.<br><br>'
                        + 'Click "next" to set up your branch setting.',
                backdrop: true,
                onNext: function (tour) {
                    $('#demo_index1').trigger("click");
                },
            },
            {
                element: ".sidebar-content",
                title: "Branch Setting",
                placement: "left",
                content: "Update all fields. Click 'submit'",
                backdrop: true,
                backdropContainer: ".right-sidebar",
                onPrev: function (tour) {
                    $('.button-close-right').trigger("click");
                },
                onNext: function (tour) {
                    $('#leavetype_setting').trigger("click");
                },
            },
            {
                element: ".sidebar-content",
                title: "Leave Type Setting",
                delay: 1,
                placement: "left",
                content: "If you have this information already set up on Xero, click 'Xero settings' to connected to your Xero account. " + "<br><br>" +
                        "If you do not use Xero, click 'Add new' to create a new leave type. Fill in the information then click 'Submit'." + "<br><br>" +
                        "If you add leave type and you use Xero, this leave type will be added to Xero." + "<br><br>" +
                        "You can update and delete leave types.",
                backdrop: true,
                backdropContainer: ".right-sidebar",
                onPrev: function (tour) {
                    $('#basic_setting').trigger("click");
                },
                onNext: function (tour) {
                    $('#leaveaccural_setting').trigger("click");
                },
            },
            {
                element: ".sidebar-content",
                title: "Leave Accrual Setting",
                placement: "left",
                content: " Click 'Add new' to create new accrual settings. Fill in the information then click 'Submit'.",
                backdrop: true,
                backdropContainer: ".right-sidebar",
                onPrev: function (tour) {
                    $('#leavetype_setting').click();
                },
                onNext: function (tour) {
                    $('#openhours_setting').click();
                },
            },
            {
                element: ".sidebar-content",
                title: "Business Hour Setting",
                placement: "left",
                content: "Click 'Add new' to create working hours. Fill in the information then click 'Submit'." + "<br><br>" +
                        "Business hours are used to calculate number of leave hours.",
                backdrop: true,
                backdropContainer: ".right-sidebar",
                onPrev: function (tour) {
                    $('#leaveaccural_setting').click();
                },
                onNext: function (tour) {
                    $('#blockdates_setting').click();
                },
            },
            {
                element: ".sidebar-content",
                title: "Block Dates Setting",
                placement: "left",
                content: "Click 'Add new' to create block dates. Fill in the information then click 'Submit'." + "<br><br>" +
                        "Block dates are dates people cannot take a leave. These dates will be shown in calendar. ",
                backdrop: true,
                backdropContainer: ".right-sidebar",
                onPrev: function (tour) {
                    $('#openhours_setting').click();
                },
                onNext: function (tour) {
                    $('#holidays_setting').click();
                },
            },
            {
                element: ".sidebar-content",
                title: "Custom Holidays Setting",
                placement: "left",
                content: "Click 'Add new' to create holidays. Fill in the information then click 'Submit'." + "<br><br>" +
                        "Custom holidays are mandatory holidays e.g. Christmas closure. ",
                backdrop: true,
                backdropContainer: ".right-sidebar",
                onPrev: function (tour) {
                    $('#blockdates_setting').click();
                },
                onNext: function (tour) {
                    $('#sickleaves_setting').click();
                },
            },
            {
                element: ".sidebar-content",
                title: "Sick Leave Setting",
                placement: "left",
                content: "Click 'Add new' to create sick leave settings. Fill in the information then click 'Submit'." + "<br><br>" +
                        "You can create rules based on days, which based on days they are away. " + "<br><br>" +
                        "Or you can create rules based on number of consecutive days taken.",
                backdrop: true,
                backdropContainer: ".right-sidebar",
                onPrev: function (tour) {
                    $('#holidays_setting').click();
                },
                onNext: function (tour) {
                    $('.button-close-right').click();
                },
            },
            {
                element: ".content-wrapper.main",
                title: "Organisation Setting",
                placement: "left",
                content: "If you have mutiple organisations, you need to process all the previous settings to all the organisations if necessary. ",
                backdrop: true,
            },
            {
                path: '/organisationUsers',
                element: ".content-wrapper.main",
                title: "Staff Management Setting",
                placement: "left",
                content: "If you don’t use Xero, you need to invite your staff to join LeaveStar." + "<br><br>" +
                        "To invite new staff into LeaveStar, click 'Invite'. Select the relevant organisation if you get more than one, fill in the other fields and click “save”." + "<br><br>" +
                        "This will generate an email to the user containing a link to login.",
                backdrop: true,
            },
            {
                path: '/leaveApplication/manage',
                element: ".content-wrapper.main",
                title: "Leave Management Setting",
                placement: "left",
                content: " This screen will populate with leave applications. You can click the dropdown box on top right and filter different leave applications." + "<br><br>" +
                        "To approve, click the 'Approve' button. " + "<br><br>" +
                        "To deny, click the 'Deny' button. " + "<br><br>" +
                        "To change, click the 'Change' button. Make changes and press 'Save'. And user will receive notification of changes." + "<br><br>" +
                        "To comment, click the 'Comment' button. Type in comments and press 'Add'",
                backdrop: true,
            },
            //Normal User guide
            {
                element: "#usernameDropdown",
                title: "User Setting",
                placement: "right",
                content: "There is a dropdown menu for user setting after you click here.",
                backdrop: true,
                backdropContainer: "#sidebar-wrapper",
            },
            {
                path: '/organisationUser/editUser',
                element: ".content-wrapper.main",
                title: "User Basic Setting",
                placement: "left",
                content: "Update user settings as required.",
                backdrop: true,
            },
            {
                path: '/organisationUser/editEmail',
                element: ".content-wrapper.main",
                title: "User Email Setting",
                placement: "left",
                content: "To set up out of office reply, click 'Link email' and select either 'Gmail' or 'Outlook'.",
                backdrop: true,
            },
            {
                path: '/customizedFeeds',
                element: ".content-wrapper.main",
                title: "User Calander Feeds Setting",
                placement: "left",
                content: "To set up customized calendar feeds on your calendar page, click 'Add new'." + "<br><br>" +
                        "<a href='http://leavestar.com/how-to-add-a-google-calendar-to-your-calendar-feeds/' target='_blank'>Click here to learn more about calendar feeds</a>" + "<br><br>" +
                        "You can add multiple calendar feeds. ",
                backdrop: true,
            },
            {
                element: ".main-sidebar",
                title: "LeaveStar Sidebar",
                placement: "right",
                content: "To apply for leave, click 'Apply for leave'. You will receive notification when your leave is approved." + "<br><br>" +
                        "Once approved, a countdown will appear." + "<br><br>" +
                        "Total leave available is displayed. " + "<br><br>" +
                        "To return to dashboard, click LeaveStar icon top left of the screen.",
                backdrop: true,
            },
            {
                path: "/home",
                element: ".content-wrapper.main",
                title: "LeaveStar Dashboard",
                placement: "left",
                content: " From the calendar, you can filter by departments, employees and leave types. You can also view the calendar by month, week and day." + "<br><br>" +
                        "Within the calendar, click any leave applications to view details.",
                backdrop: true,
            },
        ],
        onEnd: function (tour) {
            $.ajax({
                url: "/home/end-guide",
            }).done(function () {
                window.location.replace("/");
            });
        },
    }).start(true);
});