<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! function_exists( 'prefix_admin_scp_update_profile' ) ) {
    add_action('admin_post_scp_update_profile', 'prefix_admin_scp_update_profile');   // Update Profile
    add_action('admin_post_nopriv_scp_update_profile', 'prefix_admin_scp_update_profile');   // Update Profile
    function prefix_admin_scp_update_profile() {
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
            $oldsignuppassword = sanitize_text_field($_REQUEST['old-signup-password']);

            $updateUserInfo = array(
                'id' => $_SESSION['scp_user_id'],
                'password_c' => $password_c,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'phone_work' => $phone_work,
                'phone_mobile' => $phone_mobile,
                'phone_fax' => $phone_fax,
                'title' => $title,
                'email1' => $email1,
                'primary_address_street' => $primary_address_street,
            );

            $isUpdate = $objSCP->set_entry('Contacts', $updateUserInfo);

            if ($isUpdate != NULL) {
                $_SESSION['scp_user_account_name'] = $username_c;            
                $user_arr = get_userdatabylogin($username_c);
                if($oldsignuppassword != $password_c){
                    $user_id = wp_update_user(array(
                        'ID' => $user_arr->ID,
                        'user_pass' => $password_c,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                    ));
                }else{
                   $user_id = wp_update_user(array(
                        'ID' => $user_arr->ID,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                    )); 
                }
                $succmsg = "Your profile updated successfully.";
                setcookie('scp_profile_succ', $succmsg, time() + 3600, '/');
                $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . "?success=1";
                wp_redirect($redirect_url);
            }
        } else {
            $objSCP = 0;
            $conn_err = "Connection with SugarCRM not successful. Please check SugarCRM Version, URL, Username and Password.";
            setcookie('scp_connection_error', $conn_err, time() + 3600, '/');
            $redirect_url = esc_url( $_REQUEST['scp_current_url'] ) . '?conerror=1';
            wp_redirect($redirect_url);
        }
    }
}

if ( ! function_exists( 'scp_pagination' ) ) {
    function scp_pagination($total_record, $per_page = 10, $page = 1, $url = '?', $module_name, $order_by, $order, $view, $current_url) {
    $total = $total_record;
    $adjacents = "2";

    $prevlabel = "&lsaquo;";
    $nextlabel = "&rsaquo;";

    $page = ($page == 0 ? 1 : $page);
    $start = ($page - 1) * $per_page;

    $prev = $page - 1;
    $next = $page + 1;

    $lastpage = ceil($total / $per_page);

    $lpm1 = $lastpage - 1; // //last page minus 1

    $pagination = "";
    if ($lastpage > 1) {
        $pagination .= "<ul class='pagination'>";
        //$pagination .= "<li class='page_info'>Page {$page} of {$lastpage}</li>";

        if ($page > 1) {
            if (($order_by != NULL) && ($order != NULL)) {
                $pagination.= "<li class='page-prev'><a href='javascript:void(0);' onclick='scp_module_paging($prev,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>{$prevlabel}</a></li>";
            } else {
                $pagination.= "<li class='page-prev'><a href='javascript:void(0);' onclick='scp_module_paging($prev,\"" . $module_name . "\",\"\",\"\",\"\",\"$view\",\"$current_url\");'>{$prevlabel}</a></li>";
            }
        }

        if ($lastpage < 7 + ($adjacents * 2)) {
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page) {
                    $pagination.= "<li><a class='current'>{$counter}</a></li>";
                } else {
                    if (($order_by != NULL) && ($order != NULL)) {
                        $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($counter,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>{$counter}</a></li>";
                    } else {
                        $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($counter,\"" . $module_name . "\",\"\",\"\",\"\",\"$view\",\"$current_url\");'>{$counter}</a></li>";
                    }
                }
            }
        } elseif ($lastpage > 5 + ($adjacents * 2)) {

            if ($page < 1 + ($adjacents * 2)) {

                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page) {
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    } else {
                        if (($order_by != NULL) && ($order != NULL)) {
                            $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($counter,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>{$counter}</a></li>";
                        } else {
                            $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($counter,\"" . $module_name . "\",\"\",\"\",\"\",\"$view\",\"$current_url\");'>{$counter}</a></li>";
                        }
                    }
                }

                if (($order_by != NULL) && ($order != NULL)) {
                    $pagination.= "<li class='dot'>...</li>";
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($lpm1,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>{$lpm1}</a></li>";
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($lastpage,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>{$lastpage}</a></li>";
                } else {
                    $pagination.= "<li class='dot'>...</li>";
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($lpm1,\"" . $module_name . "\",'\"\",\"\",\"\",\"$view\",\"$current_url\");'>{$lpm1}</a></li>";
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($lastpage,\"" . $module_name . "\",\"\",\"\",\"\",\"$view\",\"$current_url\");'>{$lastpage}</a></li>";
                }
            } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {

                if (($order_by != NULL) && ($order != NULL)) {
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging(1,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>1</a></li>";
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging(2,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>2</a></li>";
                    $pagination.= "<li class='dot'>...</li>";
                } else {
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging(1,\"" . $module_name . "\",\"\",\"\",\"\",\"$view\",\"$current_url\");'>1</a></li>";
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging(2,\"" . $module_name . "\",\"\",\"\",\"\",\"$view\",\"$current_url\");'>2</a></li>";
                    $pagination.= "<li class='dot'>...</li>";
                }

                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page) {
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    } else {
                        if (($order_by != NULL) && ($order != NULL)) {
                            $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($counter,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>{$counter}</a></li>";
                        } else {
                            $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($counter,\"" . $module_name . "\",\"\",\"\",\"\",\"$view\",\"$current_url\");'>{$counter}</a></li>";
                        }
                    }
                }

                if (($order_by != NULL) && ($order != NULL)) {
                    $pagination.= "<li class='dot'>..</li>";
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($lpm1,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>{$lpm1}</a></li>";
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($lastpage,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>{$lastpage}</a></li>";
                } else {
                    $pagination.= "<li class='dot'>..</li>";
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($lpm1,\"" . $module_name . "\",\"\",\"\",\"\",\"$view\",\"$current_url\");'>{$lpm1}</a></li>";
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($lastpage,\"" . $module_name . "\",\"\",\"\",\"\",\"$view\",\"$current_url\");'>{$lastpage}</a></li>";
                }
            } else {

                if (($order_by != NULL) && ($order != NULL)) {
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging(1,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>1</a></li>";
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging(2,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>2</a></li>";
                    $pagination.= "<li class='dot'>..</li>";
                } else {
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging(1,\"" . $module_name . "\",\"\",\"\",\"\",\"$view\",\"$current_url\");'>1</a></li>";
                    $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging(2,\"" . $module_name . "\",\"\",\"\",\"\",\"$view\",\"$current_url\");'>2</a></li>";
                    $pagination.= "<li class='dot'>..</li>";
                }
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page) {
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    } else {
                        if (($order_by != NULL) && ($order != NULL)) {
                            $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($counter,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>{$counter}</a></li>";
                        } else {
                            $pagination.= "<li><a href='javascript:void(0);' onclick='scp_module_paging($counter,\"" . $module_name . "\",\"\",\"\",\"\",\"$view\",\"$current_url\");'>{$counter}</a></li>";
                        }
                    }
                }
            }
        }

        if ($page < $counter - 1) {

            if (($order_by != NULL) && ($order != NULL)) {
                $pagination.= "<li class='page-next'><a href='javascript:void(0);' onclick='scp_module_paging($next,\"" . $module_name . "\",0,\"$order_by\",\"$order\",\"$view\",\"$current_url\");'>{$nextlabel}</a></li>";
            } else {
                $pagination.= "<li class='page-next'><a href='javascript:void(0);' onclick='scp_module_paging($next,\"" . $module_name . "\",\"\",\"\",\"\",\"$view\",\"$current_url\");'>{$nextlabel}</a></li>";
            }
        }

        $pagination.= "</ul>";
    }

    return $pagination;
}
}

if ( ! function_exists( 'scp_password_reset' ) ) {
    add_action('after_password_reset', 'scp_password_reset', 1, 2);
    function scp_password_reset($user, $new_pass) {
        // Do something before password reset.
        global $objSCP,$sugar_crm_version;
        $pass1 = sanitize_text_field( $_REQUEST['pass1'] );
        if ($pass1 != null) {
            $user_ID = $user->ID;
            $user_email = $user->user_email;
            $user_name = $user->user_login;
            $new_pass_post = $pass1;

            $isLogin = $objSCP->PortalLogin($user_name, $user_email, 1);
            if($sugar_crm_version == 6 || $sugar_crm_version == 5){
                $Record_obj = $isLogin->entry_list[0];
            }
            if($sugar_crm_version == 7){
               $Record_obj = $isLogin->records[0];
            }
            if ($Record_obj != NULL) {
                $scp_user_id = $Record_obj->id;
                $updateUserInfo = array(
                    'id' => $scp_user_id,
                    'password_c' => $new_pass_post,
                );

                $isUpdate = $objSCP->set_entry('Contacts', $updateUserInfo);
            }
        }
    }
}

if ( ! function_exists( 'scp_profile_update' ) ) {
    add_action('profile_update', 'scp_profile_update', 99, 2);
    function scp_profile_update($user_id, $old_user_data) {
        // Do something
        global $objSCP,$sugar_crm_version,$wpdb;
        //if (isset($_REQUEST['pass1']) && !empty($_REQUEST['pass1'])) {
        $pass1 = sanitize_text_field( $_REQUEST['pass1'] );
        $user = get_userdata($user_id);
        $user_first_name = $user->first_name;
        $user_last_name = $user->last_name;
        $new_user_email = $user->user_email;
        $new_pass = $pass1;
        $user_name = $user->user_login;
        //$new_user_email=$_REQUEST['email'];
        $old_user_email = $old_user_data->user_email;

            /* Check email exist in wp and portal */
        //check portal mail
        $checkEmailExists = $objSCP->getPortalEmailExists($new_user_email,$user_name);

        if ($checkEmailExists){
            $wpdb->query("update ".$wpdb->prefix."users set user_email='".$old_user_email."' where ID='".$user_id."'");
            //wp_redirect(home_url()."/wp-admin/profile.php?email_error=1");
            if(current_user_can('administrator')){
                $append_url = "&email_error=1";
            }else{
                $append_url = "?email_error=1";
            }
            $redirct = $_SERVER['HTTP_REFERER'].$append_url;
            wp_redirect($redirct);
            die();
        } else {
            $isLogin = $objSCP->PortalLogin($user_name, $old_user_email, 1);
            if($sugar_crm_version == 6 || $sugar_crm_version == 5){
                $Record_obj = $isLogin->entry_list[0];
            }
            if($sugar_crm_version == 7){
               $Record_obj = $isLogin->records[0];
            }
            if ($Record_obj != NULL) {
                $scp_user_id = $Record_obj->id;
                $updateUserInfo = array(
                    'id' => $scp_user_id,
                    'first_name' => $user_first_name,
                    'last_name' => $user_last_name,
                    'email1' => $new_user_email,
                );
                if (isset($new_pass) && !empty($new_pass)) {
                    $updateUserInfo['password_c'] = $new_pass;
                }
                $isUpdate = $objSCP->set_entry('Contacts', $updateUserInfo);
            }
        }
        //}
    }
}

if ( ! function_exists( 'scp_admin_notice__success' ) ) {
    
    $email_error = sanitize_text_field( $_REQUEST['email_error'] );
    if( $email_error != null )
    {
       add_action( 'admin_notices', 'scp_admin_notice__success' );
    }
    function scp_admin_notice__success() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('<strong>ERROR</strong>: This email is not updated as it is already registered in CRM, please choose another one.'); ?></p>
        </div>
        <?php
    }
}