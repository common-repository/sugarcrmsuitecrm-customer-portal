<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! function_exists( 'scp_create_admin_menu' ) ) {
    add_action('admin_menu', 'scp_create_admin_menu');
    function scp_create_admin_menu() {
        //create admin side menu
        add_menu_page('Customer Portal', 'Customer Portal', 'administrator', 'biztech-crm-portal', 'scp_settings_page');
        //call register settings function
        add_action('admin_init', 'register_scp_settings');
        //add submenu for pro version
        add_submenu_page('biztech-crm-portal', __('Premium - Customer Portal'),'<label>'. __('Upgrade Now').'</label> <span class="dashicons dashicons-star-filled" style="color: #d54e21;"></span>', 'manage_options', 'biztech-crm-portal-pro', 'biztech_crm_portal_pro_callback');
    }
}

if ( ! function_exists( 'register_scp_settings' ) ) {
    function register_scp_settings() {

    //register our settings
    register_setting('sugar_crm_portal-settings-group', 'biztech_scp_name');
    register_setting('sugar_crm_portal-settings-group', 'biztech_scp_rest_url');
    register_setting('sugar_crm_portal-settings-group', 'biztech_scp_username');
    register_setting('sugar_crm_portal-settings-group', 'biztech_scp_password');
    register_setting('sugar_crm_portal-settings-group', 'biztech_scp_case_per_page');
    register_setting('sugar_crm_portal-settings-group', 'biztech_scp_sugar_crm_version');
    register_setting('sugar_crm_portal-settings-group', 'biztech_scp_upload_image');
    register_setting('sugar_crm_portal-settings-group', 'biztech_scp_portal_menu_title');
    register_setting('sugar_crm_portal-settings-group', 'biztech_redirect_login');
    register_setting('sugar_crm_portal-settings-group', 'biztech_redirect_signup'); //for redirect page for sign up
    register_setting('sugar_crm_portal-settings-group', 'biztech_redirect_profile'); // for redirect page for profile chnages
    register_setting('sugar_crm_portal-settings-group', 'biztech_redirect_forgotpwd'); //for redirect page for forgot passwords
    register_setting('sugar_crm_portal-settings-group', 'biztech_redirect_manange'); //for redirect page for manage page
}
}

if ( ! function_exists( 'scp_settings_page' ) ) {
    function scp_settings_page() {
    $check_var = scp_is_curl();
    if (($check_var == 'yes')) {//Added by BC on 06-jun-2016 for CURL CHECKING
        // Admin side page options
        ?>
                <div class='wrap'>
            <h2>SugarCRM/SuiteCRM Customer Portal Settings</h2>
            <div class="biztech-left-sidebar upgrade">
                <form method='post' action='options.php'>
                    <?php settings_fields('sugar_crm_portal-settings-group'); ?>
                    <?php do_settings_sections('sugar_crm_portal-settings-group'); ?>
                    <table class='form-table'>
                        <tr valign='top' class="hide_class">
                            <th scope='row'>Portal Name</th>
                            <td><input type='text'  class='regular-text' id="txtPortalName" value="<?php echo htmlentities(get_option('biztech_scp_name')); ?>" name='biztech_scp_name'></td>
                        </tr>
                        <tr>
                            <?php
                            $sugarCrmVersion = array(
                                '' => 'Select Version',
                                '7' => 'SugarCRM 7',
                                '6' => 'SugarCRM 6',
                                '5' => 'SuiteCRM',
                            );
                            ?>
                            <th scope="row">Version *</th>
                            <td>
                                <select id="biztech_scp_sugar_crm_version" name="biztech_scp_sugar_crm_version">
                                    <?php
                                    $sel = '';
                                    foreach ($sugarCrmVersion as $key => $velue) {

                                        if (get_option('biztech_scp_sugar_crm_version') == $key) {
                                            $sel = 'selected="selected"';
                                        }
                                        ?>
                                        <option value="<?php echo $key; ?>" <?php echo $sel; ?>><?php echo $velue; ?></option>
                                        <?php
                                        $sel = "";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr valign='top'>
                            <th scope='row'>REST URL *</th>
                            <td><input type='text'  class='regular-text' value="<?php echo get_option('biztech_scp_rest_url'); ?>" name='biztech_scp_rest_url' id="sugar_crm_url">
                            </td>
                        </tr>

                        <tr valign='top'>
                            <th scope='row'>Username *</th>
                            <td><input type='text' value="<?php echo get_option('biztech_scp_username'); ?>" name='biztech_scp_username' id="sugar_username"></td>
                        </tr>

                        <tr valign='top'>
                            <th scope='row'>Password *</th>
                            <td><input type='password' value="<?php echo get_option('biztech_scp_password'); ?>" name='biztech_scp_password' id="sugar_password"></td>
                        </tr>

                        <tr valign='top' class="hide_class">
                            <th scope='row'>Records Per Page</th>
                            <td><input type="number" class="small-text" value="<?php
                                if (get_option('biztech_scp_case_per_page') != NULL) {
                                    echo get_option('biztech_scp_case_per_page');
                                } else {
                                    echo "5";
                                }
                                ?>" min="1" step="1" name="biztech_scp_case_per_page"></td>
                        </tr>
                        <!--//Added by BC on 10-jun-2015-->
                        <tr valign="top" class="hide_class">
                            <th scope="row">Portal Logo</th>
                            <td>
                                <label for="upload_image">
                                    <div id="wpss_upload_image_thumb" class="wpss-file">
                                        <?php if (get_option('biztech_scp_upload_image') != NULL) { ?>
                                            <img src="<?php echo get_option('biztech_scp_upload_image'); ?>"  width="65"/><?php
                                        } else {
                                            echo $defaultImage;
                                        }
                                        ?>
                                    </div>
                                    <input id="upload_image" type="text" size="36" name="biztech_scp_upload_image" value="<?php
                                    if (get_option('biztech_scp_upload_image') != NULL) {
                                        echo get_option('biztech_scp_upload_image');
                                    } else {
                                        echo "";
                                    }
                                    ?>" />
                                    <input id="upload_image_button" type="button" value="Upload Image" />
                                    <input id="remove_button" type="button" value="Remove" onclick="clear_image()"/>
                                    <br />Enter an URL or upload an image for the portal logo.
                                    <script type="text/javascript">
                                        function clear_image() {
                                            jQuery("#upload_image").val('');
                                            jQuery('#wpss_upload_image_thumb img').hide();
                                        }
                                    </script>
                                </label>
                            </td>
                        </tr>
                        <tr valign='top' class="hide_class">
                            <th scope='row'>Mobile Portal Menu Title</th>
                            <td><input type="text" value="<?php
                                if (get_option('biztech_scp_portal_menu_title') != NULL) {
                                    echo get_option('biztech_scp_portal_menu_title');
                                } else {
                                    echo "Portal Menu";
                                }
                                ?>" name="biztech_scp_portal_menu_title" /></td>
                        </tr>

                        <!--Added By BC on 19-feb-016 to provide option for redirect page fot sign up-->
                        <tr valign='top' class="hide_class">
                            <th scope='row'>Sign up Page</th>
                            <td>
                                <select name = "biztech_redirect_signup" id="biztech_redirect_signup">
                                    <?php
                                    $pages = get_pages();
                                    $page = get_page_by_path('portal-sign-up');
                                    $signup_page = $page->ID;
                                    foreach ($pages as $pagg) {
                                        $option = '<option value="' . $pagg->ID . '" ';
                                        if (get_option('biztech_redirect_signup') == $pagg->ID) {
                                            $option .='selected=selected';
                                        } else if ((get_option('biztech_redirect_signup') == NULL ) && $pagg->ID == $signup_page) {
                                            $option .='selected=selected';
                                        }
                                        $option .= '>';
                                        $option .= $pagg->post_title;

                                        $option .= '</option>';
                                        echo $option;
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <!--Added By BC on 16-feb-016 to provide option for redirect page for login-->
                        <tr valign='top' class="hide_class">
                            <th scope='row'>Login Page</th>
                            <td><select name = "biztech_redirect_login" id="biztech_redirect_login">
                                    <!-- <option value = ""><?php echo attribute_escape(__('Select page'));
                                    ?></option> -->
                                    <?php
                                    $page = get_page_by_path('portal-login');
                                    $login_page = $page->ID;
                                    $pages = get_pages();
                                    foreach ($pages as $pagg) {
                                        $option = '<option value="' . ($pagg->ID) . '" ';
                                        if (get_option('biztech_redirect_login') == ($pagg->ID)) {
                                            $option .='selected=selected';
                                        } else if ((get_option('biztech_redirect_login') == NULL ) && $pagg->ID == $login_page) {
                                            $option .='selected=selected';
                                        }
                                        $option .= '>';
                                        $option .= $pagg->post_title;

                                        $option .= '</option>';
                                        echo $option;
                                    }
                                    ?>
                                </select></td>
                        </tr>

                        <!--Added By BC on 19-feb-016 to provide option for redirect page for changing profile-->
                        <tr valign='top' class="hide_class">
                            <th scope='row'>Profile Page</th>
                            <td><select name = "biztech_redirect_profile" id="biztech_redirect_profile">
                                    <!-- <option value = ""><?php echo attribute_escape(__('Select page'));
                                    ?></option> -->
                                    <?php
                                    $pages = get_pages();
                                    $page = get_page_by_path('portal-profile');
                                    $profile_page = $page->ID;
                                    foreach ($pages as $pagg) {
                                        $option = '<option value="' . ($pagg->ID) . '" ';
                                        if (get_option('biztech_redirect_profile') == ($pagg->ID)) {
                                            $option .='selected=selected';
                                        } else if ((get_option('biztech_redirect_profile') == NULL ) && $pagg->ID == $profile_page) {
                                            $option .='selected=selected';
                                        }
                                        $option .= '>';
                                        $option .= $pagg->post_title;

                                        $option .= '</option>';
                                        echo $option;
                                    }
                                    ?>
                                </select></td>
                        </tr>
                        <!--Added By BC on 16-feb-016 to provide option for redirect page for forgot password-->
                        <tr valign='top' class="hide_class">
                            <th scope='row'>Forgot Password Page</th>
                            <td><select name = "biztech_redirect_forgotpwd" id="biztech_redirect_forgotpwd">
                                    <!-- <option value = ""><?php echo attribute_escape(__('Select page'));
                                    ?></option> -->
                                    <?php
                                    $pages = get_pages();
                                    $page = get_page_by_path('portal-forgot-password');
                                    $forgot_page = $page->ID;
                                    foreach ($pages as $pagg) {
                                        $option = '<option value="' . ($pagg->ID) . '" ';
                                        if ((get_option('biztech_redirect_forgotpwd') != NULL) && (get_option('biztech_redirect_forgotpwd') == ($pagg->ID))) {
                                            $option .='selected=selected';
                                        } else if ((get_option('biztech_redirect_forgotpwd') == NULL ) && $pagg->ID == $forgot_page) {
                                            $option .='selected=selected';
                                        }
                                        $option .= '>';
                                        $option .= $pagg->post_title;

                                        $option .= '</option>';
                                        echo $option;
                                    }
                                    ?>
                                </select></td>
                        </tr>
                        <!--Added By BC on 16-feb-016 to provide option for Manage Page -->
                        <tr valign='top' class="hide_class">
                            <th scope='row'>Manage Page</th>
                            <td><select name = "biztech_redirect_manange" id="biztech_redirect_manange">
                                    <?php
                                    $pages = get_pages();
                                    $page = get_page_by_path('portal-manage-page');
                                    $manage_page = $page->ID;
                                    foreach ($pages as $pagg) {
                                        $option = '<option value="' . ($pagg->ID) . '" ';
                                        if (get_option('biztech_redirect_manange') == ($pagg->ID)) {
                                            $option .='selected=selected';
                                        } else if ((get_option('biztech_redirect_manange') == NULL ) && $pagg->ID == $manage_page) {
                                            $option .='selected=selected';
                                        }
                                        $option .= '>';
                                        $option .= $pagg->post_title;

                                        $option .= '</option>';
                                        echo $option;
                                    }
                                    ?>
                                </select></td>
                        </tr>
                    </table>

                    <?php
                        wp_enqueue_style('wp-color-picker');
                        wp_enqueue_script('my-script-handle', SCP_PLUGIN_URL.'/assets/js/scp-js.js', array('wp-color-picker'), false, true);
                        wp_enqueue_style('subscription-handle-css', SCP_PLUGIN_URL.'/assets/css/scp-upgrade-style.css');
                    ?>
                    <div class="biztech_scp_pro_feature hide_class">
                        <hr /><h2>The below features are available for Pro Version</h2>
                        <table class='form-table'>
                            <!-- Pro Version Features -->
                            <tr valign='top'>
                                <th scope='row'>Theme Color</th>
                                <td>
                                    <input type="text" value="#ddd" class="my-color-field"/>
                                    <br />Clear theme color to restore default</td>
                            </tr>
                            <tr valign='top'>
                                <th scope='row'>Calendar Calls Color</th>
                                <td><input type="text" value="#ddd" class="my-color-field" /></td>
                            </tr>
                            <tr valign='top'>
                                <th scope='row'>Calendar Meetings Color</th>
                                <td><input type="text" value="#ddd" class="my-color-field" /></td>
                            </tr>
                            <tr valign='top'>
                                <th scope='row'>Calendar Tasks Color</th>
                                <td><input type="text" value="#ddd" class="my-color-field" /></td>
                            </tr>
                            <tr valign='top'>
                                <th scope='row'>Custom Css</th>
                                <td>
                                    <textarea cols="50" rows="10" class="biztech_scp_pro" onfocus="this.blur();"></textarea><br/>
                                    <?php _e('Leave blank to restore to default', 'sugar-portal'); ?>
                                </td>
                            </tr>
                            <tr valign='top'>
                                <th scope='row'>Portal User Registration Mail Subject</th>
                                <td><input type="text" class="regular-text biztech_scp_pro"  onfocus="this.blur();" />
                                    <br />Leave blank to take default mail subject
                                </td>
                            </tr>
                            <tr valign='top'>
                                <th scope='row'>Portal User Registration Mail General Message</th>
                                <td>
                                    <textarea cols="50" rows="10" class="biztech_scp_pro" onfocus="this.blur();"></textarea>
                                </td>
                            </tr>
                            <tr valign='top'>
                                <th scope="row"><?php _e('Recent Activities On Dashboard', 'sugar-portal'); ?></th>
                                <td>
                                    <select class="biztech_scp_pro" multiple="multiple" size="4">
                                        <?php
                                        $modules_list_ary_all = array('Knowledge Base', 'Contracts', 'Invoices', 'Quotes', 'Accounts', 'Calls', 'Cases', 'Documents', 'Leads', 'Meetings', 'Notes', 'Opportunities', 'Tasks');
                                        foreach ($modules_list_ary_all as $key => $value) {
                                            $html1 .= "<option value='" . $key . "'";
                                            $html1 .= ">" . $value . "</option>";
                                        }
                                        echo $html1;
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign='top'>
                                <th scope='row'><label>Single Sign-In</label></th>
                                <td><span class="biztech_scp_pro_checkbox biztech_scp_pro"><label></label></span><input type='checkbox' class=""><?php _e("This feature will allow admin to set single sign-in for wordpress dashboard and portal."); ?></td>
                            </tr>
                            <tr valign='top'>
                                <th scope='row'>Portal Template</th>
                                <td><span class="stopdropdown biztech_scp_pro"><select>
                                            <option value="1">CRM Standalone Page</option>
                                            <option value="0">CRM Full Width Page</option>
                                        </select></span</td>
                            </tr>
                        </table>
                    </div>
                    <?php submit_button(); ?>
                </form>
            </div>
            <div class="biztech-right-sidebar upgrade">
                <div class="biztech_scp_pro_feature pro_sticky">
                    <a href="<?php echo (get_option('biztech_scp_sugar_crm_version')) ? (get_option('biztech_scp_sugar_crm_version')==5) ? "https://www.appjetty.com/suitecrm-wordpress-customer-portal.htm" : "https://www.appjetty.com/sugarcrm-wordpress-customer-portal.htm" : "https://www.appjetty.com/suitecrm-wordpress-customer-portal.htm" ; ?>?utm_source=CustomerPortal&utm_medium=AdminPanel&utm_campaign=WordpressFree" target="_blank">
                        <image src="<?php echo SCP_IMAGES_URL; ?>sticky_img.jpg" />
                    </a>
                </div>
            </div>            
        </div>

<?php
            $scp_sugar_rest_url = get_option('biztech_scp_rest_url');
            $scp_sugar_username = get_option('biztech_scp_username');
            $scp_sugar_password = get_option('biztech_scp_password');

            if (class_exists('SugarRestApiCall')) {
                $objSCP = new SugarRestApiCall($scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password);
                if (((get_option('biztech_scp_sugar_crm_version') == 6 || get_option('biztech_scp_sugar_crm_version') == 5) && $objSCP->session_id != '') || (get_option('biztech_scp_sugar_crm_version') == 7 && $objSCP->access_token != '')) {//Added by BC on 02-sep-2015
                    ?>
                    <div class='updated settings-error' id='setting-error-settings_updated'>
                        <p><strong>Connection successful.</strong></p>
                    </div>
                    <script type="text/javascript">
                        jQuery(".hide_class").show();</script>
                    <?php
                } else {
                    ?>
                    <div class='error settings-error' id='setting-error-settings_updated'>
                        <p><strong>Connection not successful. Please check SugarCRM Version, URL, Username and Password.</strong></p>
                    </div>
                    <script type="text/javascript">
                        jQuery(".hide_class").hide();</script>
                    <?php
                }
            } else {
                ?>
                <div class='error settings-error' id='setting-error-settings_updated'>
                    <p><strong>Connection not successful. Please check SugarCRM Version, URL, Username and Password.</strong></p>
                </div>
                <script type="text/javascript">
                    jQuery(".hide_class").hide();
                </script>
                <?php
            }

    } else {//else CURL CHECKING
        ?>
        <div class='error settings-error' id='setting-error-settings_updated'>
            <p><strong><?php echo $check_var; ?></strong></p>
        </div>
        <?php
    }
}
}

if ( ! function_exists( 'scp_start_session' ) ) {
    add_action('init', 'scp_start_session', 1);
    function scp_start_session() {
        
        if( ! session_id() ) {
            session_start();
        }
    }
}

if ( ! function_exists( 'sugar_crm_portal_logout' ) ) {
    $logout = sanitize_text_field( $_REQUEST['logout'] );
    if ($logout) {

        function sugar_crm_portal_logout() {
            unset($_SESSION['scp_user_id']);
            unset($_SESSION['scp_user_account_name']);
            $redirect_url = explode('?', esc_url( $_SERVER['REQUEST_URI']), 2);
            $redirect_url = $redirect_url[0];
            wp_redirect($redirect_url);
            exit;
        }

        add_action('init', 'sugar_crm_portal_logout', 1);
    }
}

if ( ! function_exists( 'scp_deleteDirectory' ) ) {
    function scp_deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!scp_deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
}

if ( ! function_exists( 'biztech_crm_portal_pro_callback' ) ) {
    function biztech_crm_portal_pro_callback(){
        
        wp_enqueue_script('subscription-handle',SCP_PLUGIN_URL.'/assets/js/scp-subscribe.js', array(), false, true);
        wp_enqueue_style('subscription-handle-css', SCP_PLUGIN_URL.'/assets/css/scp-upgrade-style.css' );
        $current_sugar_version = get_option('biztech_scp_sugar_crm_version');
        if($current_sugar_version == 5 ){
            $version_name = 'SuitCRM';

            $wordress_module['mobile_app']['url'] = 'https://www.appjetty.com/suitecrm-customer-app.htm';
            $wordress_module['mobile_app']['img'] = 'suite-mob-app.png';

            $wordress_module['employee_portal']['url'] = 'https://www.appjetty.com/suitecrm-employee-app.htm';
            $wordress_module['employee_portal']['img'] = 'suite-employee-portal.png';
        }
        else{
            $version_name = 'SugarCRM';

            $wordress_module['mobile_app']['url'] = 'https://www.appjetty.com/sugarcrm-customer-app.htm';
            $wordress_module['mobile_app']['img'] = 'sugar-mob-app.png';

            $wordress_module['employee_portal']['url'] = 'https://www.appjetty.com/sugarcrm-employee-app.htm';
            $wordress_module['employee_portal']['img'] = 'sugar-employee-portal.png';
        }
    ?>

        <div class="scp-upgrade-page">
            <div class="top-heading">
                <h2>Upgrade to Customer Portal Pro and get an enterprise grade self service customer portal!</h2>
                </div>
            <div class="scp-upgrade-page-left-col">
                <form method="post" id="subscribe_form">
                    <div id="message" style="display:none; margin: 0 0 15px;"></div>
                    <input type="hidden" name="ajax_theme" value="<?php echo admin_url( 'admin-ajax.php' );?>">
                    <label>Subscribe for more info: </label>
                    <div class="scp-upgrade-sub">
                        <input type="email" name="subscribe_mail" />
                        <input class="button button-primary" value="Subscribe" type="submit">
                    </div>
                    <img style="display:none;" src="<?php echo  SCP_IMAGES_URL; ?>ajax-loading.gif" id="preloader" alt="Preloader" /><br>
                </form>
                <ul>
                    <li>Fully control module access to customers</li>
                    <li>Fully configurable module layouts</li>
                    <li>Customizable portal frontend framework</li>
                    <li>Attractive dashboard with icon based shortcuts</li>
                    <li>Customer can update CRM records from portal</li>
                    <li>Activity calendar view</li>
                    <li>Import / export portal users</li>
                    <li>Third party chat plugin support</li>
                </ul>
            </div>
            <div class="scp-upgrade-page-right-col">
                <div class="scp-transaction" style="display:none;">
                    <h2>Portal Transaction Add-On</h2>
                    <ul>
                        <li>Allows customer to pay invoices within portal</li>
                        <li>Currently available only for SuiteCRM Portal</li>
                        <li>Works only when WooCommerce is installed</li>
                    </ul>
                    <button>Buy Now</button>
                </div>
            </div>

            <?php $url = (get_option('biztech_scp_sugar_crm_version')) ? (get_option('biztech_scp_sugar_crm_version')==5) ? "https://www.appjetty.com/suitecrm-wordpress-customer-portal.htm" : "https://www.appjetty.com/sugarcrm-wordpress-customer-portal.htm" : "https://www.appjetty.com/suitecrm-wordpress-customer-portal.htm" ; ?>

            <button class="large-btn" onclick="location.href = '<?php echo $url; ?>?utm_source=CustomerPortal&utm_medium=AdminPanel&utm_campaign=WordpressFree';">Buy <?php echo $version_name; ?> Customer Portal Pro</button>
            <i>Comes with 15 days money back guarantee, 3 months FREE support & 6 months FREE upgrades</i>

            <h2 class="top-space">Other Products you might be interested in</h2>
            <div class="other-plug-container">

                <div class="other-plug-blog">
                    <div class="img-blog">
                        <img src="<?php echo SCP_IMAGES_URL.$wordress_module['mobile_app']['img']; ?>">
                    </div>
                    <div class="blog-content">
                        <h3><?php echo $version_name; ?> Mobile App</h3>
                        <ul>
                            <li>Integrate all or selected SugarCRM modules</li>
                            <li>Role based accessibility to users</li>
                            <li>Mobile app compatible with iOS &amp; Android</li>
                        </ul>
                    </div>
                    <div class="btn-section">
                        <button onclick="location.href = '<?php echo $wordress_module['mobile_app']['url']; ?>?utm_source=CustomerPortal&utm_medium=AdminPanel&utm_campaign=WordpressFree';">Buy Now</button>
                        <a target="_blank" href="<?php echo $wordress_module['mobile_app']['url']; ?>?utm_source=CustomerPortal&utm_medium=AdminPanel&utm_campaign=WordpressFree">More information</a>
                    </div>
                </div>

                <div class="other-plug-blog">
                    <div class="img-blog">
                        <img src="<?php echo SCP_IMAGES_URL.$wordress_module['employee_portal']['img']; ?>">
                    </div>
                    <div class="blog-content">
                        <h3><?php echo $version_name; ?> Employee Portal</h3>
                        <ul>
                            <li>Make CRM available to employees</li>
                            <li>Customizable module layouts &amp; frontend design</li>
                            <li>User friendly navigation &amp; design</li>
                        </ul>
                    </div>
                    <div class="btn-section">
                        <button onclick="location.href = '<?php echo $wordress_module['employee_portal']['url']; ?>?utm_source=CustomerPortal&utm_medium=AdminPanel&utm_campaign=WordpressFree';">Buy Now</button>
                        <a target="_blank" href="<?php echo $wordress_module['employee_portal']['url']; ?>?utm_source=CustomerPortal&utm_medium=AdminPanel&utm_campaign=WordpressFree">More information</a>
                    </div>
                </div>

                <div class="other-plug-blog">
                    <div class="img-blog">
                        <img src="<?php echo SCP_IMAGES_URL; ?>show_all_reviews_store-logo.png">
                    </div>
                    <div class="blog-content">
                        <h3>WooCommerce Show All Reviews</h3>
                        <ul>
                            <li>Display all WooCommerce reviews in a single page</li>
                            <li>Display reviews on any page with shortcode</li>
                            <li>Exclude unwanted reviews for any product</li>
                        </ul>
                    </div>
                    <div class="btn-section">
                        <button onclick="location.href = 'https://www.appjetty.com/woocommerce-show-all-reviews.htm?utm_source=CustomerPortal&utm_medium=AdminPanel&utm_campaign=WordpressFree';">Buy Now</button>
                        <a target="_blank" href="https://www.appjetty.com/woocommerce-show-all-reviews.htm?utm_source=CustomerPortal&utm_medium=AdminPanel&utm_campaign=WordpressFree">More information</a>
                    </div>
                </div>

            </div>
        </div>


    <?php
    }
}

if ( ! function_exists( 'user_subscription' ) ) {
    add_action( 'wp_ajax_nopriv_user_subscription', 'user_subscription' );
    add_action( 'wp_ajax_user_subscription', 'user_subscription' );
    function user_subscription(){
        
        $subscribe_mail = sanitize_text_field( $_POST['subscribe_mail'] );
        if(is_email($subscribe_mail)){
            if(get_option( 'biztech_subscribe_mailid' ) == $subscribe_mail){
                echo '<div class="notice notice-warning"><p>You have already subscribed.</p></div>';
            }
            else{
                $to = 'sales@appjetty.com';
                $subject = 'Subscription Upgrade to Customer Portal Pro';
                update_option( 'biztech_subscribe_mailid' , trim($subscribe_mail));
                $body = subscrinbe_mail_body();
                add_filter('wp_mail_content_type', 'scp_wpdocs_set_html_mail_content_type');
                if(wp_mail($to, $subject, $body)){                    
                    echo '<div class="notice notice-success"><p>You have successfully subscribed.</p></div>';
                }
                else{
                    echo '<div class="notice notice-error"><p>There is error while subscribing, please try again.</p></div>';
                }
                remove_filter('wp_mail_content_type', 'scp_wpdocs_set_html_mail_content_type');

            }
        }
        else{
            echo '<div class="notice notice-error"><p>Please enter valid mail-id.</p></div>';
        }
            
        wp_die();
    }   
}

if ( ! function_exists( 'subscrinbe_mail_body' ) ) {
    function subscrinbe_mail_body(){
        $body_full = "<table width='100%' cellspacing='0' cellpadding='0' border='0' style='background:#e4e4e4; padding:30px 0;'>
      <tbody>
        <tr>
          <td><center>
                <table width='800' cellspacing='0' cellpadding='0' border='0' style='font-family: arial,sans-serif; font-size:14px; border-top:4px solid #35b0bf; background:#fff; line-height: 22px;'>
                    <tr>
                        <td style='padding:30px 40px 0; color: #484848;' align='left' valign='top'>
                             <p>Hello Sales,</p>
                             <p>One Of the client request to Upgrade to Customer Portal Pro From Free Wordpress Portal, Client's all details are following.</p>
                             <p><b>Email id:</b> ".get_option( 'biztech_subscribe_mailid' )."</p>
                             <p><b>Website Name:</b> ".get_bloginfo( 'name' )."</p>
                             <p><b>Website URL:</b> <a href='".get_site_url()."/'>".get_site_url()."/</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width='100%' cellspacing='0' cellpadding='0' border='0' style='background:#f7f7f7; padding:15px 40px;'>
                                <tr>
                                    <td align='left' valign='middle'>
                                        <a target='_blank' style='color: #35b0bf;text-decoration: none; font-size:14px;' href='https://www.biztechcs.com/'>https://www.biztechcs.com/</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </center></td>
        </tr>
    </tbody>
    </table>";
        return $body_full;
    }
}