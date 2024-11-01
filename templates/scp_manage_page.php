<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$conerror = $_REQUEST['conerror'];
if ($conerror != null) {
    if (isset($_COOKIE['scp_connection_error']) && $_COOKIE['scp_connection_error'] != '') {
        $cookie_err = $_COOKIE['scp_connection_error'];
        unset($_COOKIE['scp_login_error']);
        return "<div class='error settings-error' id='setting-error-settings_updated'> 
            <p><strong>$cookie_err</strong></p>
        </div>";
    }
}
$biztech_redirect_profile = get_page_link(get_option('biztech_redirect_profile'));
if ($biztech_redirect_profile != NULL) {
    $redirect_url = $biztech_redirect_profile;
} else {
    $redirect_url = home_url() . "/portal-profile/";
}

$pagetemplate = get_post_meta(get_the_ID(), '_wp_page_template', true);
$template = str_replace('.php', "", $pagetemplate);
if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
    $list_result_count_all = $objSCP->get_relationships('Contacts', $_SESSION['scp_user_id'], 'accounts', array('id'), '', '', 0);
    $countAcc = count($list_result_count_all->entry_list);
}
if ($sugar_crm_version == 7) {
    $list_result_count_all = $objSCP->getRelationship('Contacts', $_SESSION['scp_user_id'], 'accounts', 'id', array(), '', '', 'date_entered:DESC');
    if (!empty($list_result_count_all->records)) {
        $countAcc = count($list_result_count_all->records);
    } else {
        $countAcc = 0;
    }
}
$theme_name = wp_get_theme(); //20-aug-2016
if ($theme_name == "Twenty Fifteen") {
    $article_class_gen = "article-fifteen-theme";
}
if ($theme_name == "Twenty Sixteen") {
    $article_class_gen = "article-sixteen-theme";
}

if ($template != 'page-crm-standalone') {
    ?>
    <header class="entry-header entry-wrapper">
        <div class="container"> 
            <?php if ($template != 'page-crm-standalone') {
                ?>
                <h1 class="entry-title scp-menu-modules"><?php
                        if (get_option('biztech_scp_name') != NULL) {
                            _e(get_option('biztech_scp_name'));
                        } else {
                            _e("Free Portal");
                        }
                        ?></h1>
                <?php
            } else {
                ?><a href='javascript:void(0)' ><h1 class="entry-title"><?php _e("Free Portal"); ?></h1></a><?php
                    }
                    ?>
            <div class='userinfo'>
                <a href='javascript:void(0)' class="scp-menu-manage"><?php echo $_SESSION['scp_user_account_name']; ?> </a> <ul class="scp-open-manage-menu"><li><a class='fa fa-user' href='<?php echo $redirect_url; ?>'> <?php _e("My Profile"); ?></a></li><li><a class='fa fa-power-off'  href='?logout=true'> <?php _e("Log Out"); ?></a></li></ul>
            </div>
        </div>
    </header>
<?php } ?>
<article class = "page type-page status-publish hentry container">

    <div class="entry-content entry-wrapper scp-wrapper-bg">
        <?php if ($template == 'page-crm-standalone') { ?>
            <span id="toggle"><?php if (get_option('biztech_scp_portal_menu_title') != '') {
            echo get_option('biztech_scp_portal_menu_title');
        } else {
            echo 'My Profile';
        } ?><span class="fa arrow"></span></span>
                    <?php } ?>
        <div class="scp-leftpanel">
                    <?php if ($template == 'page-crm-standalone') { ?>
                <ul class="scp-sidemenu">
                    <li class="scp-menu-profile-sec"> <?php
                        $current_user = wp_get_current_user();

                        if (($current_user instanceof WP_User)) {
                            echo "<div class='scp-user-img'>" . get_avatar($current_user->user_email, 51) . "</div>";
                        }
                        ?><span class="scp-menu-manage username"> <a href='<?php echo $redirect_url; ?>'><?php echo $_SESSION['scp_user_account_name']; ?> </a></span>
                        <a href='<?php echo $redirect_url; ?>' class="fa fa-pencil scp-profile-edit"></a>
                        <ul>
                            <li><a class='fa fa-power-off scp-profile-logout'  href='?logout=true'> <?php _e("Log Out"); ?></a></li>
                        </ul>
                    </li>

                    <li id="Cases_id" class="scp-sidebar-class">
                        <a class="label"> <span class="fa Cases side-icon-wrapper"></span> &nbsp;<span class="menu-text">Cases</span><span class="fa arrow"></span></a>
                        <ul style="display: none;" id="dropdown_Cases_id" class="inner_ul">
                            <li style="list-style: none;" id="edit-Cases"><a href="javascript:void(0)"><span class="fa fa-plus side-icon-wrapper"></span><span>Add</span></a></li>
                            <li style="list-style: none;" id="list-Cases" ><a class='no-toggle' href="javascript:void(0)"><span class="fa fa-list side-icon-wrapper"></span><span>View</span></a></li>
                        </ul>
                    </li>
                </ul>
            <?php } ?>
        </div>
        <div class='scp-entry-header scp-page-list-view'>
<?php
if ($template == 'page-crm-standalone') {
    ?>
                <header class="entry-header entry-wrapper">
                    <div class="container"> 
                        <a href='javascript:void(0)' class='scp-manage-page-header-sec'><?php if (get_option('biztech_scp_name') != NULL) { ?>
                                <span class='fa fa-bars scp-bar-toggle'></span><h1 class="entry-title"><?php echo get_option('biztech_scp_name'); ?></h1>
    <?php } else { ?><span class='fa fa-bars scp-bar-toggle'></span><h1 class="entry-title"><?php _e("Free Portal"); ?></h1><?php } ?></a>
                    </div>
                </header>
<?php } ?>
            <div id="responsedata" class='scp-standalone-content'></div>
            <div id="otherdata" style="display:none;"></div>
        </div>
    </div>
</article>



<?php $id = sanitize_text_field( $_REQUEST['id'] ); ?>
<script type="text/javascript">

    function scp_module_order_by(page, modulename, order_by, order, view, get_current_url) { // for order by
        var searchval = jQuery('#search_name').val();
        var el = jQuery(".scp-entry-header");
        App.blockUI(el);
        var data = {
            'action': 'scp_list_module',
            'view': view,
            'modulename': modulename,
            'page_no': page,
            'current_url': get_current_url,
            'order_by': order_by,
            'order': order,
            'searchval': searchval,
        };
        jQuery.post(ajaxurl, data, function (response) {
            App.unblockUI(el);
            if (jQuery.trim(response) != '-1') {
                jQuery('#responsedata').html(response);
                // jQuery('#succid').hide();
            }
            else
            {
                var redirect_url = $(location).attr('href') + "?conerror=1";
                window.location.href = redirect_url;
            }
        });
    }

    function scp_module_paging(page, modulename, ifsearch, order_by, order, view, get_current_url) { // for paging
        var searchval = jQuery('#search_name').val();
        if (ifsearch == 1 && (searchval.trim() == '' || searchval == null)) {
            alert('Please enter valid data to search');
            return false;
        }

        var el = jQuery(".scp-entry-header");
        App.blockUI(el);
        var data = {
            'action': 'scp_list_module',
            'view': view,
            'modulename': modulename,
            'page_no': page,
            'current_url': get_current_url,
            'order_by': order_by,
            'order': order,
            'searchval': searchval
        };
        jQuery.post(ajaxurl, data, function (response) {
            App.unblockUI(el);
            if (jQuery.trim(response) != '-1') {
                jQuery('#responsedata').html(response);
            }
            else
            {
                var redirect_url = $(location).attr('href') + "?conerror=1";
                window.location.href = redirect_url;
            }
        });
    }

    function scp_clear_search_txtbox(page, modulename, ifsearch, order_by, order, view, get_current_url) { //for clear search text box
        jQuery('#otherdata').hide();
        jQuery(this).parents('form').find('input[type="text"]').val('');
        var el = jQuery(".scp-entry-header");
        App.blockUI(el);
        var data = {
            'action': 'scp_list_module',
            'view': view,
            'modulename': modulename,
            'page_no': page,
            'current_url': get_current_url,
            'order_by': order_by,
            'order': order,
        };
        jQuery.post(ajaxurl, data, function (response) {
            App.unblockUI(el);
            if (jQuery.trim(response) != '-1') {
                jQuery('#responsedata').html(response);
                // jQuery('#succid').hide();
            }
            else
            {
                var redirect_url = $(location).attr('href') + "?conerror=1";
                window.location.href = redirect_url;
            }
        });

    }
    
    jQuery(document).ready(function ($) {

        var curURL = window.location.href;
        //for display default account page
        if (curURL.indexOf("?") < 0) {
            scp_common_call("list-Cases");
        }
        //for display listing page after record insert 
        var param = curURL.split("?");
        if (param[1] != '' && param[1] != null && param[1] != 'undefined') {
            sucmsg = 1;
            scp_common_call(param[1], sucmsg);
        }
        $(".inner_ul> li").click(function ()
        {
            var divId = $(this).attr('id');
            sucmsg = 0;
            scp_common_call(divId, sucmsg);
        });
        function scp_common_call(divId, sucmsg) {
            jQuery('#otherdata').hide();
            var getdata = divId.split("-");
            var view = getdata[0];
            var modulename = getdata[1];
            var get_current_url = "<?php echo esc_url( $_SERVER['REQUEST_URI']); ?>"
            var order_by = '';
            var order = '';
            var el = jQuery(".scp-entry-header");
            if (modulename.indexOf("&") >= 0) { // in calnder,after click go to detail page
                getmodulename = modulename.split("&");
                modulename = getmodulename[0];

                if (divId.indexOf("detail") >= 0) {// display selected in module list
                    setview = "list";
                    modulename1 = modulename;
                }
            }
            if (divId.indexOf("list") >= 0) {// display selected in module list afetr record added
                modulename1 = modulename;
            }
            App.blockUI(el);
            if (view == 'edit') {
                var data = {
                    'action': 'scp_add_module',
                    'view': view,
                    'modulename': modulename,
                    'id': "<?php echo ( $id ? $id : '' );?>",
                    'current_url': get_current_url
                };
            }
            if (view == 'view') {
                if (modulename == 'calendar') { // if calender module then call other page
                    var data = {
                        'action': 'scp_calendar_display',
                    };
                } else {
                    var data = {
                        'action':'scp_view_module',
                        'view': view,
                        'modulename': modulename,
                        'id': "<?php echo ( $id ? $id : '' );?>",
                        'current_url': get_current_url
                    };
                }
            }
            if (view == 'list') {
                var data = {
                    'action': 'scp_list_module',
                    'view': view,
                    'modulename': modulename,
                    'page_no': 0,
                    'current_url': get_current_url,
                    'order_by': order_by,
                    'order': order
                };
            }
            if (view == 'detail') {
                var data = {
                    'action': 'scp_view_module',
                    'view': view,
                    'modulename': modulename,
                    'id': "<?php echo ( $id ? $id : '' );?>",
                    'current_url': get_current_url

                };
            }
            jQuery.post(ajaxurl, data, function (response) {
                App.unblockUI(el);
                if (jQuery.trim(response) != '-1') {
                    jQuery('#responsedata').html(response);
                    if (sucmsg == 1) {
                        jQuery('#succid').show(); //display success msg of updated/added/deleted
                    } else {
                        jQuery('#succid').hide();
                    }
                }
                else
                {
                    var redirect_url = $(location).attr('href') + "?conerror=1";
                    window.location.href = redirect_url;
                }
            });
        }
    });
    
    function scp_module_call_view(modulename, id, view, curURL) { // for view particular record(detail page)
        var el = jQuery(".scp-entry-header");
        App.blockUI(el);
        var data = {
            'action': 'scp_view_module',
            'view': view,
            'modulename': modulename,
            'id': id,
            'current_url': curURL
        };
        jQuery.post(ajaxurl, data, function (response) {
            App.unblockUI(el);
            if (jQuery.trim(response) != '-1') {
                jQuery('#responsedata').html(response);
                jQuery('#succid').hide();

            }
            else
            {
                var redirect_url = $(location).attr('href') + "?conerror=1";
                window.location.href = redirect_url;
            }
        });
    }

    function scp_module_call_add(htmlval, modulename, view, success, deleted, id) { // for add record layout in module
        htmlval = (typeof htmlval === 'undefined') ? 0 : htmlval;
        success = (typeof success === 'undefined') ? null : success;
        deleted = (typeof deleted === 'undefined') ? null : deleted;
        id = (typeof id === 'undefined') ? null : id;
        var get_current_url = "<?php echo esc_url( $_SERVER['REQUEST_URI']); ?>"
        var el = jQuery(".scp-entry-header");
        /* Added for working edit from calendar detail page */
        var split_arry = get_current_url.split('?');
        split_arry[1] = (typeof split_arry[1] === 'undefined') ? null : split_arry[1];
        if (split_arry[1] != null) {
            var splited_url_var = split_arry[1].split('-')[0];
            if (splited_url_var == 'detail') {
                get_current_url = split_arry[0] + "?list-" + modulename;
            }
        }
        App.blockUI(el);
        var data = {
            'action': 'scp_add_module',
            'view': view,
            'modulename': modulename,
            'success': success,
            'deleted': deleted,
            'id': id,
            'current_url': get_current_url

        };
        jQuery.post(ajaxurl, data, function (response) {
            App.unblockUI(el);
            if (jQuery.trim(response) != '-1') {
                if (htmlval == 0) {
                    jQuery('#otherdata').hide();
                    jQuery('#responsedata').html(response);
                    jQuery('#succid').hide();
                }
                else {
                    jQuery('#otherdata').show();
                    jQuery('#otherdata').html(response);
                    jQuery('#succid').hide();
                }
                jQuery("#otherdata").appendTo("#responsedata");
            }
            else
            {
                var redirect_url = $(location).attr('href') + "?conerror=1";
                window.location.href = redirect_url;
            }
        });
    }
</script>