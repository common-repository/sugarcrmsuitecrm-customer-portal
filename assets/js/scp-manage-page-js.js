//mange page script
jQuery(document).ready(function () {

    //show / hide side bar menu
    jQuery("#toggle").click(function () {
        jQuery(".scp-leftpanel").toggle();
        jQuery(this).toggleClass('toggle-icon-wrapper');
    });

    jQuery('.scp-menu-manage').click(function () {
        jQuery('.scp-open-manage-menu').slideToggle();
        jQuery(this).toggleClass('menu-active');
    });
    jQuery('.scp-menu-profile').click(function () {
        jQuery('.scp-open-profile-menu').slideToggle();
        jQuery(this).toggleClass('menu-active');
    });
    jQuery('.scp-menu-modules').click(function () {
        jQuery('.scp-open-modules-menu').slideToggle();
        jQuery(this).toggleClass('menu-active');
    });

    jQuery(window).click(function (e) {
        var scp_menu_manage = jQuery('.scp-menu-manage');
        var scp_menu_modules = jQuery('.scp-menu-modules');
        var scp_menu_profile = jQuery('.scp-menu-profile');

        if (!scp_menu_manage.is(e.target) // if the target of the click isn't the container...
                && scp_menu_manage.has(e.target).length === 0) // ... nor a descendant of the container
        {
            if (jQuery('.scp-open-manage-menu').is(":visible")) {
                jQuery('.scp-open-manage-menu').hide();
                jQuery(scp_menu_manage).toggleClass('menu-active');
            }
        }
        if (!scp_menu_modules.is(e.target) // if the target of the click isn't the container...
                && scp_menu_modules.has(e.target).length === 0) // ... nor a descendant of the container
        {
            if (jQuery(window).width() > 1024) {
                if (jQuery('.scp-open-modules-menu').is(":visible")) {
                    jQuery('.scp-open-modules-menu').hide();
                }
            }
        }

        if (!scp_menu_profile.is(e.target) // if the target of the click isn't the container...
                && scp_menu_profile.has(e.target).length === 0) // ... nor a descendant of the container
        {
            if (jQuery('.scp-open-profile-menu').is(":visible")) {
                jQuery('.scp-open-profile-menu').hide();
                jQuery(scp_menu_profile).toggleClass('menu-active');
            }
        }
    });

    jQuery(window).click(function (e) {
        if (jQuery(window).width() < 1025) {

            jQuery('.scp-sidebar-class').click(function () {


                var getid = jQuery(this).attr('id');
                jQuery(".inner_ul").not("#dropdown_" + getid).slideUp();
                jQuery('.scp-active-menu').not("#" + getid + " a.label").removeClass('scp-active-menu').addClass('scp-sidemenu');
                jQuery('#dropdown_' + getid).show();
                jQuery('.scp-menu-modules').removeClass('menu-active');
                jQuery('#dropdown_' + getid).click(function () {
                    jQuery('.scp-open-modules-menu').hide();
                });
                jQuery('#dashboard_id a').addClass('scp-active-menu');
                jQuery("#" + getid + " a.label").addClass('scp-active-menu');
            });
             jQuery(".inner_ul> li").click(function ()
                {
                    var divId = jQuery(this).attr('id');
                    jQuery('.no-toggle').removeAttr('style');
                    jQuery('a').removeClass('scp-active-submenu');
                    jQuery('#' + divId + ' a').addClass('scp-active-submenu');
                });
        }
    });

});

jQuery(document).ajaxComplete(function () {
    if (!jQuery('#otherdata').is(':empty'))
    {
        jQuery("#responsedata").after('<div id="otherdata" style="display:none;"></div>');
    }
});
