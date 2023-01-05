$(function () {
    window.tour = new Tour({
        steps: [
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