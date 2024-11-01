<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! function_exists( 'scp_sign_up' ) ) {
    add_shortcode('bcp-sign-up', 'scp_sign_up');
    function scp_sign_up() {
        //20-aug-2016
        global $objSCP, $sugar_crm_version;
        if($objSCP == NULL){
            return "<div class='error settings-error error-line error-msg' id='setting-error-settings_updated'> 
                <strong>Please Contact Your Administrator.</strong>
            </div>";
        }
        if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
            $sess_id = $objSCP->session_id;
        }
        if ($sugar_crm_version == 7) {
            $sess_id = $objSCP->access_token;
        }
        if ($sess_id != '') {
            ob_start();
            if (empty($_SESSION['scp_user_id'])) {
                include( SCP_TEMPLATE_PATH . 'signup.php');
                return ob_get_clean();
            } else {
                if (isset($_COOKIE['scp_connection_error']) && $_COOKIE['scp_connection_error'] != '') {
                    $cookie_err = $_COOKIE['scp_connection_error'];
                    unset($_COOKIE['scp_login_error']);
                    return "<div class='error settings-error error-line error-msg' id='setting-error-settings_updated'> 
                <strong>$cookie_err</strong>
            </div>";
                }
            }
        } else {
            if (isset($_COOKIE['scp_connection_error']) && $_COOKIE['scp_connection_error'] != '') {
                $cookie_err = $_COOKIE['scp_connection_error'];
                unset($_COOKIE['scp_login_error']);
                return "<div class='error settings-error error-line error-msg' id='setting-error-settings_updated'> 
                <strong>$cookie_err</strong>
            </div>";
            }
        }
    }
}

if ( ! function_exists( 'scp_login_shortcode' ) ) {
    add_shortcode('bcp-login', 'scp_login_shortcode');
    function scp_login_shortcode() {
        //20-aug-2016
        global $objSCP, $sugar_crm_version;
        if($objSCP == NULL){
            return "<div class='error settings-error error-line error-msg' id='setting-error-settings_updated'> 
                <strong>Please Contact Your Administrator.</strong>
            </div>";
        }
        if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
            $sess_id = $objSCP->session_id;
        }
        if ($sugar_crm_version == 7) {
            $sess_id = $objSCP->access_token;
        }
        if ($sess_id != '') {
            ob_start();
            if (empty($_SESSION['scp_user_id'])) {
                include( SCP_TEMPLATE_PATH . 'login.php');
                return ob_get_clean();
            } else {
                if (isset($_COOKIE['scp_connection_error']) && $_COOKIE['scp_connection_error'] != '') {
                    $cookie_err = $_COOKIE['scp_connection_error'];
                    unset($_COOKIE['scp_login_error']);
                    return "<div class='error settings-error error-line error-msg' id='setting-error-settings_updated'> 
                <strong>$cookie_err</strong>
            </div>";
                }
            }
        } else {
            if (isset($_COOKIE['scp_connection_error']) && $_COOKIE['scp_connection_error'] != '') {
                $cookie_err = $_COOKIE['scp_connection_error'];
                unset($_COOKIE['scp_login_error']);
                return "<div class='error settings-error error-line error-msg' id='setting-error-settings_updated'> 
                <strong>$cookie_err</strong>
            </div>";
            }
        }
    }
}

if ( ! function_exists( 'scp_profile_shortcode' ) ) {
    add_shortcode('bcp-profile', 'scp_profile_shortcode');
    function scp_profile_shortcode() {

        global $objSCP, $sugar_crm_version, $getContactInfo;
        if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
            $sess_id = $objSCP->session_id;
        }
        if ($sugar_crm_version == 7) {
            $sess_id = $objSCP->access_token;
        }
        if ($sess_id != '') {
            if (isset($_SESSION['scp_user_id']) == true) { // check for login or not
                if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
                    $getContactInfo = $objSCP->getPortalUserInformation($_SESSION['scp_user_id'])->entry_list[0]->name_value_list;
                } else {
                    $getContactInfo = $objSCP->getPortalUserInformation($_SESSION['scp_user_id']);
                }
                ob_start();
                include( SCP_TEMPLATE_PATH . 'profile.php');
                return ob_get_clean();
            }
        } else {
            if (isset($_COOKIE['scp_connection_error']) && $_COOKIE['scp_connection_error'] != '') {
                $cookie_err = $_COOKIE['scp_connection_error'];
                unset($_COOKIE['scp_login_error']);
                return "<div class='error settings-error error-line error-msg' id='setting-error-settings_updated'> 
                <strong>$cookie_err</strong>
            </div>";
            }
        }
    }
}

if ( ! function_exists( 'scp_forgot_password' ) ) {
    add_shortcode('bcp-forgot-password', 'scp_forgot_password');
    function scp_forgot_password() {

        global $objSCP, $sugar_crm_version;
        if($objSCP == NULL){
            return "<div class='error settings-error error-line error-msg' id='setting-error-settings_updated'> 
                <strong>Please Contact Your Administrator.</strong>
            </div>";
        }
        if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
            $sess_id = $objSCP->session_id;
        }
        if ($sugar_crm_version == 7) {
            $sess_id = $objSCP->access_token;
        }
        if ($sess_id != '') {
            ob_start();
            if (empty($_SESSION['scp_user_id'])) {
                include( SCP_TEMPLATE_PATH . 'forgotpassword.php');
                return ob_get_clean();
            } else {
                if (isset($_COOKIE['scp_connection_error']) && $_COOKIE['scp_connection_error'] != '') {
                    $cookie_err = $_COOKIE['scp_connection_error'];
                    unset($_COOKIE['scp_login_error']);
                    return "<div class='error settings-error error-line error-msg' id='setting-error-settings_updated'> 
                <strong>$cookie_err</strong>
            </div>";
                }
            }
        } else {
            if (isset($_COOKIE['scp_connection_error']) && $_COOKIE['scp_connection_error'] != '') {
                $cookie_err = $_COOKIE['scp_connection_error'];
                unset($_COOKIE['scp_login_error']);
                return "<div class='error settings-error error-line error-msg' id='setting-error-settings_updated'> 
                <strong>$cookie_err</strong>
            </div>";
            }
        }
    }
}

if ( ! function_exists( 'scp_manage_page_shortcode' ) ) {
    add_shortcode('bcp-manage-page', 'scp_manage_page_shortcode');
    function scp_manage_page_shortcode() {

        //20-aug-2016
        global $objSCP, $sugar_crm_version;
        if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
            $sess_id = $objSCP->session_id;
        }
        if ($sugar_crm_version == 7) {
            $sess_id = $objSCP->access_token;
        }
        if ($sess_id != '') {
            include( SCP_TEMPLATE_PATH . 'scp_manage_page.php');
            return ob_get_clean();
        } else {
            if (isset($_COOKIE['scp_connection_error']) && $_COOKIE['scp_connection_error'] != '') {
                $cookie_err = $_COOKIE['scp_connection_error'];
                unset($_COOKIE['scp_login_error']);
                return "<div class='error settings-error error-line error-msg' id='setting-error-settings_updated'> 
                <strong>$cookie_err</strong>
            </div>";
            }
        }
    }
}