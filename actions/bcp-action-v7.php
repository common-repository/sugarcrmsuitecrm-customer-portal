<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! function_exists( 'scp_sign_up_callback' ) ) {
    add_action('admin_post_scp_sign_up', 'scp_sign_up_callback'); // Sign Up      
    add_action('admin_post_nopriv_scp_sign_up', 'scp_sign_up_callback'); // Sign Up
    function scp_sign_up_callback() {
        global $objSCP;
        if ($objSCP != 0) {
            $username_c = sanitize_text_field($_REQUEST['add-signup-username']);
            $password_c = sanitize_text_field($_REQUEST['add-signup-password']);
            $first_name = sanitize_text_field($_REQUEST['add-signup-first-name']);
            $last_name = sanitize_text_field($_REQUEST['add-signup-last-name']);
            $phone_work = sanitize_text_field($_REQUEST['add-signup-work-phone']);
            $phone_mobile = sanitize_text_field($_REQUEST['add-signup-mobile']);
            $phone_fax = sanitize_text_field($_REQUEST['add-signup-fax']);
            $title = sanitize_text_field($_REQUEST['add-signup-title']);
            $email1 = sanitize_email($_REQUEST['add-signup-email-address']);
            $primary_address_street = sanitize_text_field($_REQUEST['add-signup-primary-address']);
            $biztech_redirect_login = get_page_link(get_option('biztech_redirect_login'));

            $username_c = sanitize_user($username_c, true);
            if (isset($username_c) && $username_c != '') {//username is not blank then
                $addSignUp = array(
                    'username_c' => $username_c,
                    'password_c' => $password_c,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'phone_work' => $phone_work,
                    'title' => $title,
                    'phone_mobile' => $phone_mobile,
                    'phone_fax' => $phone_fax,
                    'email1' => $email1,
                    'primary_address_street' => $primary_address_street,
                );
                //check user exists
                $checkUserExists = $objSCP->getUserInformationByUsername($username_c);
                $checkEmailExists = $objSCP->getPortalEmailExists($email1);

                //check username

                $user_id = username_exists($username_c);
                $wp_email_exist = email_exists($email1);

                if (($checkUserExists) && ($checkEmailExists) && ($user_id) && $wp_email_exist) {
                    $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?error=1';
                    $signup_msg = "Username and Email Address already exists.";
                    setcookie('scp_signup_err', $signup_msg, time() + 30, '/');
                    wp_redirect($redirect_url);
                } else if ($checkUserExists || $user_id) {
                    $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?error=1';
                    $signup_msg = "Username already exists.";
                    setcookie('scp_signup_err', $signup_msg, time() + 30, '/');
                    wp_redirect($redirect_url);
                } else if ($checkEmailExists || $wp_email_exist) {
                    $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?error=1';
                    $signup_msg = "Email Address already exists.";
                    setcookie('scp_signup_err', $signup_msg, time() + 30, '/');
                    wp_redirect($redirect_url);
                } else if (!is_email($email1)) {//check proper email validation
                    $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?error=1';
                    $signup_msg = "This email id is invalid because it uses illegal characters. Please enter a valid email id.";
                    setcookie('scp_signup_err', $signup_msg, time() + 30, '/');
                    wp_redirect($redirect_url);
                } else {
                    $isSignUp = $objSCP->set_entry('Contacts', $addSignUp);
                    if ($isSignUp != NULL) {
                        //create user in wp when entry entered in 

                        $new_user_id = wp_insert_user(array(
                            'user_login' => $username_c,
                            'user_pass' => $password_c,
                            'user_email' => $email1,
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                                )
                        );
                        scp_mail_user($username_c, $password_c, $first_name, $last_name, $email1); //Send user mail
                        $signup_msg = "You are successfully sign up.";
                        setcookie('scp_signup_suc', $signup_msg, time() + 30, '/');
                        if (isset($biztech_redirect_login) && !empty($biztech_redirect_login)) {

                            $redirect_url = $biztech_redirect_login . '?signup=true';
                        } else {
                            $redirect_url = home_url() . '/portal-login?signup=true';
                        }
                        wp_redirect($redirect_url);
                    }
                }
            } else {//if username is blank then
                $signup_msg = "This username is invalid because it uses illegal characters. Please enter a valid username.";
                setcookie('scp_signup_err', $signup_msg, time() + 30, '/');
                $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?error=1';
                wp_redirect($redirect_url);
            }
        } else {
            $objSCP = 0;
            $conn_err = "Connection with SugarCRM not successful. Please check SugarCRM Version, URL, Username and Password.";
            setcookie('scp_connection_error', $conn_err, time() + 30, '/');
            $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?conerror=1';
            wp_redirect($redirect_url);
        }
    }
}

if ( ! function_exists( 'scp_login_callback' ) ) {
    add_action('admin_post_scp_login', 'scp_login_callback'); // login
    add_action('admin_post_nopriv_scp_login', 'scp_login_callback'); // login
    function scp_login_callback() {

    global $objSCP, $wpdb, $sugar_crm_version;
    if ($objSCP != 0) {
        $scp_username = sanitize_text_field( $_REQUEST['scp_username'] );
        $scp_password = sanitize_text_field( $_REQUEST['scp_password'] );
        $isLogin = $objSCP->PortalLogin($scp_username, $scp_password);

        if (($isLogin->records[0] != NULL) && ($scp_username != NULL) && ($scp_password != NULL)) {
            $_SESSION['scp_user_id'] = $isLogin->records[0]->id;
            $_SESSION['scp_user_account_name'] = $isLogin->records[0]->username_c;

            //get option to redirect to which page after login
            if (get_page_link(get_option('biztech_redirect_manange')) != NULL) {
                $redirect_url = get_page_link(get_option('biztech_redirect_manange'));
            } else {
                $redirect_url = home_url() . "/portal-manage-page/";
            }
            wp_redirect($redirect_url);
            exit();
        } else {
            $ERROR_CODE = (isset($isLogin->error)) ? $isLogin->error : 1;
            $set_global_loginerr = array(
                '1' => 'Invalid Username OR Password.',
                '201' => 'Please contact your Administrator to validate License.',
                '202' => 'There seems some error while validating your license for Customer Portal Pro. Please try again later.'
            );
            setcookie('scp_login_error', $set_global_loginerr[$ERROR_CODE], time() + 30, '/');
            $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?error=1';
            wp_redirect($redirect_url);
        }
    } else {
        $objSCP = 0;
        $conn_err = "Connection with SugarCRM not successful. Please check SugarCRM Version, URL, Username and Password.";
        setcookie('scp_connection_error', $conn_err, time() + 30, '/');
        $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?conerror=1';
        wp_redirect($redirect_url);
    }
}
}

if ( ! function_exists( 'scp_forgot_password_callback' ) ) {
    add_action('admin_post_scp_forgot_password', 'scp_forgot_password_callback');
    add_action('admin_post_nopriv_scp_forgot_password', 'scp_forgot_password_callback');
    function scp_forgot_password_callback() {
        global $objSCP;
        if ($objSCP != 0) {

            $checkUsername = sanitize_text_field($_REQUEST['forgot-password-username']);
            $checkEmialAddress = sanitize_text_field($_REQUEST['forgot-password-email-address']);

            $checkUserExists = $objSCP->getUserInformationByUsername($checkUsername);
            $username = $checkUserExists->records[0]->username_c;
            $emailAddress = $checkUserExists->records[0]->email1;
            $getAdminEmail = get_option('admin_email');
            if (($username == $checkUsername) && ($emailAddress == $checkEmialAddress)) {
                $password = $checkUserExists->records[0]->password_c;
                if (get_option('biztech_scp_name') != NULL) {
                    $headers = "From: " . get_option('biztech_scp_name') . " <$getAdminEmail>' . '\r\n";
                } else {
                    $headers = "From: <$getAdminEmail>' . '\r\n";
                }

                if (get_page_link(get_option('biztech_redirect_login')) != NULL) {
                    $url = get_page_link(get_option('biztech_redirect_login'));
                } else {
                    $url = home_url() . "/portal-login/";
                }
                $isSendEmail = scp_mail_user_forgotpwd($username, $password, $url, $emailAddress); //Send user mail
                $redirect_url = esc_url( $_REQUEST['scp_current_url'] );
                if ($isSendEmail == true) {
                    $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?sucess=1';
                    $disply_msg = "Your password has been sent successfully.";
                    setcookie('scp_frgtpwd', $disply_msg, time() + 30, '/');
                    wp_redirect($redirect_url);
                } else {
                    $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?error=1';
                    $disply_msg = "Failed to send your your password. Please try later or contact the administrator by another method.";
                    setcookie('scp_frgtpwd', $disply_msg, time() + 30, '/');
                    wp_redirect($redirect_url);
                }
            } else if (($username == $checkUsername) && ($emailAddress != $checkEmialAddress)) {
                $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?error=1';
                $disply_msg = "Your email address does not match.";
                setcookie('scp_frgtpwd', $disply_msg, time() + 30, '/');
                wp_redirect($redirect_url);
            } else {
                $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?error=1';
                $disply_msg = "Your username does not exists.";
                setcookie('scp_frgtpwd', $disply_msg, time() + 30, '/');
                wp_redirect($redirect_url);
            }
        } else {
            $objSCP = 0;
            $conn_err = "Connection with SugarCRM not successful. Please check SugarCRM Version, URL, Username and Password.";
            setcookie('scp_connection_error', $conn_err, time() + 30, '/');
            $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?conerror=1';
            wp_redirect($redirect_url);
        }
    }
}

if ( ! function_exists( 'scp_add_module_callback' ) ) {
    add_action('wp_ajax_scp_add_module', 'scp_add_module_callback');
    add_action('wp_ajax_nopriv_scp_add_module', 'scp_add_module_callback');
    function scp_add_module_callback() {
        global $objSCP, $wpdb, $sugar_crm_version;
        $html = '';
        $sess_id = $objSCP->access_token;
        if ($sess_id != '') {
            $module_name = sanitize_text_field( $_POST['modulename'] );
            $view = sanitize_text_field( $_POST['view'] );
            $current_url = esc_url( $_POST['current_url'] );
            if ($current_url != '' && !empty($current_url)) {
                $current_url = explode('?', $current_url, 2);
                $current_url = $current_url[0];
            }
            include( SCP_TEMPLATE_PATH . 'scp_add_page_v7.php');
            $html .= "</div><script type='text/javascript'>jQuery('#general_form_id').validate();</script>";
            echo $html;
            wp_die();
        } else {
            $objSCP = 0;
            $conn_err = "Connection with SugarCRM not successful. Please check SugarCRM Version, URL, Username and Password.";
            setcookie('scp_connection_error', $conn_err, time() + 30, '/');
            $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?conerror=1';
            wp_redirect($redirect_url);
        }
    }
}

if ( ! function_exists( 'scp_list_module_callback' ) ) {
    add_action('wp_ajax_scp_list_module', 'scp_list_module_callback');
    add_action('wp_ajax_nopriv_scp_list_module', 'scp_list_module_callback');
    function scp_list_module_callback() {
        global $objSCP, $wpdb, $sugar_crm_version;
        $where_con = array();
        $search_name = $deleted = '';
        $sess_id = $objSCP->access_token;
        if ($sess_id != '') {
            $result_timezone = '';
            $module_name = sanitize_text_field( $_POST['modulename'] );
            $view = sanitize_text_field( $_POST['view'] );
            $limit = get_option('biztech_scp_case_per_page');
            $page_no = sanitize_text_field( $_POST['page_no'] );
            $offset = ($page_no * $limit) - $limit;
            $current_url = esc_url( $_POST['current_url'] );
            if ($current_url != '' && !empty($current_url)) {
                $current_url = explode('?', $current_url, 2);
                $current_url = $current_url[0];
            }
            $order_by = trim( sanitize_text_field( $_POST['order_by'] ) );
            $order = trim(sanitize_text_field( $_POST['order'] ));
            $searchval = sanitize_text_field( $_POST['searchval'] );
            if ($searchval)
                $search_name = trim($searchval);
            if (isset($search_name) && !empty($search_name)) {
                $where_con = array(
                    "name" => array(
                        '$contains' => $search_name,
                    ),
                );
            }
            if (isset($order_by) == true && !empty($order_by) && isset($order) == true && !empty($order)) {
                $order_by_query = "$order_by:$order";
            } else {
                $order_by_query = "date_entered:DESC";
            }
            $n_array_str = implode(',', $n_array);

            $list_result_count_all = $objSCP->getRelationship('Contacts', $_SESSION['scp_user_id'], strtolower($module_name), 'id', $where_con, '', '', $order_by_query);
            $list_result = $objSCP->getRelationship('Contacts', $_SESSION['scp_user_id'], strtolower($module_name), 'id,name,case_number,date_entered,priority,status', $where_con, $limit, $offset, $order_by_query);

            $cnt = count($list_result->records);
            $countCases = count($list_result_count_all->records);
            $html = "";
            $html .= "<form action = '" . home_url() . "/wp-admin/admin-post.php' method = 'post' enctype = 'multipart/form-data' id = 'actionform'>
                <input type = 'hidden' name = 'action' value = 'scp_module_call_add'>
                <input type = 'hidden' name = 'scp_current_url' value = '" . $current_url . "'>
                <input type = 'hidden' name = 'id' value = '' id = 'scp_id'>
                <input type = 'hidden' name = 'delete' value = '1'>
                <input type = 'hidden' name = 'module_name' value = '" . $module_name . "'>
                </form>";

            $html .="<div class='scp-action-header'><div class='scp-action-header-left'><h3 class='fa " . $module_name . " side-icon-wrapper scp-$module_name-font scp-default-font'>" . strtoupper($module_name) . "</h3>
            <a  class='scp-$module_name scp-default-bg-btn' href='javascript:void(0);' onclick='scp_module_call_add(0,\"$module_name\",\"edit\",\"\",\"\",\"\");'><span class='fa fa-plus side-icon-wrapper'></span><span>ADD " . strtoupper($module_name) . "</span></a>
            </div>";
            //for Serach
            $html .= "<form method = 'post' enctype = 'multipart/form-data' id = 'actionform_search' onsubmit = \"return false;\" class='actionform_search'>
                        <div class='search-box'>
                <input type='text' name='search_name' value='" . $search_name . "' id='search_name' placeholder='Search $module_name' class='search-input'/>
                <a href='javascript:;' onclick='scp_module_paging(0,\"$module_name\",1,\"\",\"\",\"$view\",\"$current_url\")' id='search_btn_id' class='hover active scp-button search-btn'><i class='fa fa-search' aria-hidden='true'></i>SEARCH</a>
                    </div>
                <a id='clear_btn_id' onclick='scp_clear_search_txtbox(0,\"$module_name\",\"\",\"\",\"\",\"$view\",\"$current_url\");' href='javascript:;'><i class='fa fa-remove'></i>CLEAR</a>
                </form>
                <script>
                jQuery('#search_name').keypress(function(event){

                var keycode = (event.keyCode ? event.keyCode : event.which);
                if(keycode == '13'){
                        scp_module_paging(0,\"$module_name\",1,\"\",\"\",\"$view\",\"$current_url\");	
                }
                event.stopPropagation();
                });
                </script>";
            $html .= "</div>";
            if ($cnt > 0) {
                $html .= "<div class='scp-page-action-title'>View $module_name </div>";
            }
            $get_param = $_SERVER['QUERY_STRING'];
            if (isset($get_param) && !empty($get_param) || $current_url != '') {
                if (isset($_COOKIE['scp_add_record']) && $_COOKIE['scp_add_record'] != '' && !isset($_COOKIE['scp_add_record_fail']) && $_COOKIE['scp_add_record_fail'] == '') {
                    $cookie_err = $_COOKIE['scp_add_record'];
                    unset($_COOKIE['scp_add_record']);
                    $html .= "<span class='success' id='succid'>" . $cookie_err . "</span>";
                }
                if (isset($_COOKIE['scp_add_record_fail']) && $_COOKIE['scp_add_record_fail'] != '') {//for cases per user are not more than 5 error message set
                    $html .= "<span class='messages error dsp_error_msg' id='fail_error'>" . $_COOKIE['scp_add_record_fail'] . "</span>";
                }
            }
            $html .= "<div class='scp-table-responsive'>";
            if ($cnt > 0) {
                $html .= "<table id='example' class='display scp-table scp-table-striped scp-table-bordered scp-table-hover' cellspacing='0' width='100%'>";
                $name_arry = array();
                $module_without_s = strtolower(rtrim($module_name, 's'));
                include( SCP_TEMPLATE_PATH . 'scp_list_page_v7.php');
                $html .="</table>";
                $html .="</div>";
                $pagination_url = "?";
                $view1 = 'list';
                $html .= scp_pagination($countCases, $limit, $page_no, $pagination_url, $module_name, $order_by, $order, $view1, $current_url);
            } else {
                $html .= "<strong>No Record(s) Found.</strong>
                </div>";
            }
            echo $html;
            wp_die();
        } else {
            $objSCP = 0;
            $conn_err = "Connection with SugarCRM not successful. Please check SugarCRM Version, URL, Username and Password.";
            setcookie('scp_connection_error', $conn_err, time() + 30, '/');
            echo "-1";
            wp_die();
        }
    }
}

if ( ! function_exists( 'scp_view_module_callback' ) ) {
    add_action('wp_ajax_scp_view_module', 'scp_view_module_callback');
    add_action('wp_ajax_nopriv_scp_view_module', 'scp_view_module_callback');
    function scp_view_module_callback() {
        global $objSCP, $wpdb, $sugar_crm_version;
        $sess_id = $objSCP->access_token;
        if ($sess_id != '') {
            $module_name = sanitize_text_field( $_POST['modulename'] );
            $view = sanitize_text_field( $_POST['view'] );
            $id = sanitize_text_field( $_POST['id'] );
            $current_url = esc_url( $_POST['current_url'] );
            if ($current_url != '' && !empty($current_url)) {
                $current_url = explode('?', $current_url, 2);
                $current_url = $current_url[0];
            }
            $html = '';
            $record_detail = $objSCP->getRecordDetail($module_name, $id);
            $name = $record_detail->name;
            include( SCP_TEMPLATE_PATH . 'scp_view_page_v7.php');
            $html .= "</div>";
            echo $html;
            wp_die();
        } else {
            $objSCP = 0;
            $conn_err = "Connection with SugarCRM not successful. Please check SugarCRM Version, URL, Username and Password.";
            setcookie('scp_connection_error', $conn_err, time() + 30, '/');
            echo "-1";
            wp_die();
        }
    }
}

if ( ! function_exists( 'scp_add_moduledata_call_callback' ) ) {
    add_action('wp_ajax_scp_add_moduledata_call', 'scp_add_moduledata_call_callback');
    add_action('wp_ajax_nopriv_scp_add_moduledata_call', 'scp_add_moduledata_call_callback');
    add_action('admin_post_scp_add_moduledata_call', 'scp_add_moduledata_call_callback');
    add_action('admin_post_nopriv_scp_add_moduledata_call', 'scp_add_moduledata_call_callback');
    function scp_add_moduledata_call_callback() {

        global $objSCP, $wpdb, $sugar_crm_version;
        $current_url = esc_url( $_POST['current_url'] );

        $sess_id = $objSCP->access_token;
        if ($sess_id != '') {
            if ($current_url != '' && !empty($current_url)) {
                $current_url = explode('?', $current_url, 2);
                $current_url = $current_url[0];
                $id = sanitize_text_field($_REQUEST['id']);
            }
            $logged_user_id = $_SESSION['scp_user_id'];
            $module_name = sanitize_text_field($_REQUEST['module_name']);
            $view = sanitize_text_field($_REQUEST['view']);

            //get name array
            $pass_name_arry = array(
                'name' => sanitize_text_field($_REQUEST['add-name']),
                'description' => sanitize_text_field($_REQUEST['add-description']),
                'status' => sanitize_text_field( $_REQUEST['add-status'] ),
                'priority' => sanitize_text_field( $_REQUEST['add-priority'] ),
                'type' => sanitize_text_field( $_REQUEST['add-type'] ),
                'resolution' => sanitize_text_field($_REQUEST['add-resolution'])
            );
            $new_id = $objSCP->set_entry($module_name, $pass_name_arry);

            $rel_id = $objSCP->set_relationship('Contacts', $logged_user_id, strtolower($module_name), $new_id);
            $view1 = 'list';
            $redirect_url = $current_url . '?' . $view1 . '-' . $module_name . '';
            $conn_err = $module_name . " Added Successfully";
            setcookie('scp_add_record', $conn_err, time() + 30, '/');
            wp_redirect($redirect_url);
        } else {
            $objSCP = 0;
            $conn_err = "Connection with SugarCRM not successful. Please check SugarCRM Version, URL, Username and Password.";
            setcookie('scp_connection_error', $conn_err, time() + 30, '/');
            $redirect_url = $current_url . '?conerror=1';
            wp_redirect($redirect_url);
        }
    }
}