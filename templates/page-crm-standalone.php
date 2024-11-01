<?php /**
 * Template Name : CRM Standalone
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 */ ?>
<html lang="en-US">
    <!--<![endif]-->
    <head>
        <title><?php echo get_the_title(); ?> | <?php bloginfo('name'); ?></title>
        <?php
        wp_head();
        //sugar_crm_portal_style_and_script();
        ?>
        
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body  <?php body_class(); ?>>

        <?php
        while (have_posts()) : the_post();
            $theme_name = wp_get_theme();//20-aug-2016
            if ($theme_name == "Twenty Fourteen") {
                $fullwidth_class_gen = "fullwidth-fourteen-theme";
            }
            if ($theme_name == "Twenty Fifteen") {
                $fullwidth_class_gen = "fullwidth-fifteen-theme";
            }
            if ($theme_name == "Twenty Sixteen") {
                $fullwidth_class_gen = "fullwidth-sixteen-theme";
            }
            wp_enqueue_script( 'scp-standalone' );
            ?>             
            <div id="fullwidth-wrapper" class="crm_standalone <?php echo $fullwidth_class_gen; ?>">
                <?php
                // Include the page content.
                the_content();
                ?>
            </div>
            <?php
        endwhile;
        if (isset($_SESSION['scp_user_account_name']) && $_SESSION['scp_user_account_name'] != '') {
            $first_element = reset($_SESSION['module_array']);
        } else {
            $first_element = '';
        }
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var curURL = window.location.href;
                if (curURL.indexOf("?") < 0) {//Updated by BC on 04-jun-2016 for default module list shown dynamically
                    var first_element = 'Cases';
                    var first_element_without_s = 'Case';
                    // scp_common_call("list-" + first_element);
                    $(first_element_without_s + '#_id').addClass('noborder');
                    $('#dropdown_' + first_element + '_id').css('display', 'block');
                    $('#list-' + first_element + '  a').addClass('scp-active-submenu');
                } else {
                    var divId = curURL.split("?");

                    var getdata = divId[1].split("-");
                    var modulename = getdata[1];
                    var view = getdata[0];
                    if (modulename.indexOf("&") >= 0) { // in calnder,after click go to detail page
                        getmodulename = modulename.split("&");
                        modulename = getmodulename[0];
                        if (view == 'detail') {// display selected in module list
                            setview = "list";
                            //modulename1 = modulename.toLowerCase();
                            modulename1 = modulename;
                            $('#' + modulename1 + '_id').addClass('noborder');
                            $('#dropdown_' + modulename1 + '_id').css('display', 'block');
                            $('#edit-' + modulename1 + '  a').addClass('scp-active-submenu');
                            $("#" + modulename1 + "_id a.label").addClass('scp-active-menu');
                        }
                    }
                    if (view == 'list') {// display selected in module list afetr record added
                        //modulename1 = modulename.toLowerCase();
                        modulename1 = modulename;

                        $('#' + modulename1 + '_id').addClass('noborder');
                        $('#dropdown_' + modulename1 + '_id').css('display', 'block');
                        $('#list-' + modulename1 + '  a').addClass('scp-active-submenu');
                        $("#" + modulename1 + "_id a.label").addClass('scp-active-menu');
                    }
                }

                $(".inner_ul> li").click(function ()
                {
                    var divId = $(this).attr('id');
                    $('.no-toggle').removeAttr('style');
                    $('a').removeClass('scp-active-submenu');
                    $('#' + divId + ' a').addClass('scp-active-submenu');
                });

            });
        </script>
        <?php wp_footer(); ?>
    </body></html>