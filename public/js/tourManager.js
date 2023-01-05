$(function () {
    window.tour = new Tour({
        steps: [
            {
                path: "/home",
                element: ".content-wrapper.main",
                title: "Welcome to LeaveStar",
                placement: "left",
                content: "This tutorial will guide you through features in LeaveStar. This is the main dashboard which contains the calendar and notification section. As a manager, you could see the status of your company in the dashboard section as well. ",
                backdrop: true,
            },
            {
                element: ".main-sidebar",
                title: "LeaveStar Sidebar",
                placement: "right",
                content: "You can moniter your leave capacity here on the sidebar. A leave countdown will be shown if any of your leave has been approved. As a manager, you could also manage your staff and their leave applications. You could change your organisations' setting here as well. ",
                backdrop: true,
                onNext: function (tour) {
                    $('#apply-leave').trigger("click");
                },
            },
            {
                element: ".sidebar-content",
                title: "Leave Application",
                placement: "left",
                content: 'By click "Apply for Leave" on the sidebar,you will be redirected to leave application page. ',
                backdrop: true,
                backdropContainer: ".right-sidebar",
                onPrev: function (tour) {
                    $('.button-close-right').click();
                },
                onNext: function (tour) {
                    $('.button-close-right').click();

                },
            },
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
                content: "This is the page for user's basic setting. You can do settings like feed color ,text color or your profile picture here.",
                backdrop: true,
            },
            {
                path: '/organisationUser/editEmail',
                element: ".content-wrapper.main",
                title: "User Email Setting",
                placement: "left",
                content: "This is the page for user's email setting. You could link either a gmail or a microsoft office email account here. You can set auto reply message when applying for a leave after you link an email address here.",
                backdrop: true,
            },
            {
                path: '/customizedFeeds',
                element: ".content-wrapper.main",
                title: "User Calander Feed Setting",
                placement: "left",
                content: "This is the page where user can add their customized calendar feeds. Currently, only ics feed is supported by LeaveStar.",
                backdrop: true,
            },
            // Manager special step
            {
                path: '/organisationStructures',
                element: ".content-wrapper.main",
                title: "Organisation Setting",
                placement: "left",
                content: "By click 'organisation setting' on the left sidebar, you will be redirected to the page for organisation setting. You can create new organisation node, update or delete the current nodes here.",
                backdrop: true,
            },
            {
                path: '/organisationUsers',
                element: ".content-wrapper.main",
                title: "Staff Management Setting",
                placement: "left",
                content: "By click 'staff management' on the left sidebar, you will be redirected to the page for staff management. You can manage your staff's leave balance, reassign staff or invite new staff here.",
                backdrop: true,
            },
            {
                path: '/leaveApplication/manage',
                element: ".content-wrapper.main",
                title: "Leave Management Setting",
                placement: "left",
                content: "By click 'leave management' on the left sidebar, you will be redirected to the page for leave management. You can manage your staff's leaves and search leave history here.",
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