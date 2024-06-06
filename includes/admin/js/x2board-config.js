jQuery(document).ready((function($) {
    $((function() {
        $("#post-body-content").tabs({
            create: function(event, ui) {
                $(ui.tab.find("a")).addClass("nav-tab-active")
            },
            activate: function(event, ui) {
                $(ui.oldTab.find("a")).removeClass("nav-tab-active"), $(ui.newTab.find("a")).addClass("nav-tab-active")
            }
        })
    }))
}));