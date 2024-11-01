//click on menus
jQuery(document).ready(function () {
    if (jQuery('#toggle').is(":visible")) {//default close on standalone
            jQuery(".scp-leftpanel").toggle();
            jQuery(this).toggleClass('toggle-icon-wrapper');
        }
    jQuery(".scp-bar-toggle").click(function () {
        jQuery("body").toggleClass("toggled-on", 500);
        jQuery(".container").toggleClass("menu-show", 500);

    });

});