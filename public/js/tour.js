$(function () {
    window.tour = new Tour({
        steps: [
            {
                path: "/home",
                element: ".content-wrapper.main",
                title: "Calendar Yo",
                placement: "left",
                content: "Check it out bro",
                backdrop: true,
            },
            {
                element: ".main-sidebar",
                title: "Sidebar",
                placement: "right",
                content: "Settings 'n shit",
                backdrop: true,
            },
            {
                path: "/organisationStructures",
                element: ".content-wrapper.main",
                title: "Organisation settings",
                placement: "left",
                content: "Settings 'n shit",
                backdrop: true,
                onNext: function () {
//                    console.log($(this));
//                    console.log("cool");
//                    this.pause();
//                    alert("TEST");
//                    $("#demo > ol.sortable > li:first-child() > .menuDiv > .menuEdit a:nth-child(3)").trigger("click");
                    
                }
            },
            {
                path: "/organisationStructures",
                orphan: true,
                title: "orphan",
                content: "test",
            },
        ]
    }).start(true);
});