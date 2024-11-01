<?php
/**
 * Plugin Name: SugarCRM/SuiteCRM Customer Portal
 * Description: Reduce operational costs and improve customer satisfaction by empowering customers to get online support and get their complains and queries addressed through Customer Portal V2 that integrates the user friendly front end interface of Wordpress and the back end data sourcing from SugarCRM.
 * Author: biztechc
 * Author URI: https://www.appjetty.com/
 * Version: 2.1.1
 * License: GPLv2
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define( 'SCP_PLUGIN_ABSPATH', plugin_dir_path( __FILE__ ) );
define( 'SCP_PLUGIN_URL', plugins_url( '', __FILE__ ) );

define( 'SCP_IMAGES_URL', SCP_PLUGIN_URL . '/assets/images/' );
define( 'SCP_TEMPLATE_PATH', SCP_PLUGIN_ABSPATH . 'templates/' );

global $sugar_crm_version;

$sugar_crm_version = get_option( 'biztech_scp_sugar_crm_version' );
if ( $sugar_crm_version == 7 ) {
    include( SCP_PLUGIN_ABSPATH . 'class/scp-class-7.php' );
    include( SCP_PLUGIN_ABSPATH . 'actions/bcp-action-v7.php' );
} else {
    include( SCP_PLUGIN_ABSPATH . 'class/scp-class-6.php' );
    include( SCP_PLUGIN_ABSPATH . 'actions/bcp-action-v6.php' );
}

include( SCP_PLUGIN_ABSPATH . 'templates/scp-class-page-template.php' );
include( SCP_PLUGIN_ABSPATH . 'actions/scp-common-action.php' );
include( SCP_PLUGIN_ABSPATH . 'admin/settings.php' );
include( SCP_PLUGIN_ABSPATH . 'shortcodes/bcp-shortcodes.php' );

if ( ! function_exists( 'scp_create_page' ) ) {
    register_activation_hook( __FILE__, 'scp_create_page' );
    function scp_create_page() {
        
        global $wpdb, $user_ID;

        $page_array = array(
            'bcp-sign-up'           => 'Portal Sign Up',
            'bcp-login'             => 'Portal Login',
            'bcp-profile'           => 'Portal Profile',
            'bcp-forgot-password'   => 'Portal Forgot Password',
            'bcp-manage-page'       => 'Portal Manage Page',
        );

        foreach ( $page_array as $sc => $page_name ) {
            $page = get_page_by_title( $page_name );
            if ( empty( $page ) ) {
                $new_post = array(
                    'post_title'    => $page_name,
                    'post_content'  => '[' . $sc . ']',
                    'post_status'   => 'publish',
                    'post_date'     => date('Y-m-d H:i:s'),
                    'post_type'     => 'page',
                    'post_author'   => $user_ID
                );
                $post_id = wp_insert_post( $new_post );
                update_post_meta( $post_id, '_wp_page_template', 'page-crm-standalone.php' );
            }
        }
    }
}

if ( ! function_exists( 'scp_connection' ) ) {
    add_action( 'init', 'scp_connection' );
    function scp_connection() {
        
        global $sugar_crm_version, $objSCP;

        $check_var = scp_is_curl();
        if ( ( $check_var == 'yes' ) ) {
            if ( class_exists( 'SugarRestApiCall' ) ) {
                $scp_sugar_rest_url = get_option( 'biztech_scp_rest_url' );
                $scp_sugar_username = get_option( 'biztech_scp_username' );
                $scp_sugar_password = get_option( 'biztech_scp_password' );
                if ( isset( $scp_sugar_rest_url ) && ! empty( $scp_sugar_rest_url ) && isset( $scp_sugar_username ) && ! empty( $scp_sugar_username ) && isset( $scp_sugar_password ) && ! empty( $scp_sugar_password ) ) {
                    $objSCP = new SugarRestApiCall( $scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password );                    
                    if ( ( ( $sugar_crm_version == 6 || $sugar_crm_version == 5 ) && $objSCP->session_id != '' ) || ( $sugar_crm_version == 7 && $objSCP->access_token != '' ) ) {
                        return $objSCP;
                    } else {
                        $objSCP = 0;
                        $conn_err = "Connection with SugarCRM not successful. Please check SugarCRM Version, URL, Username and Password.";
                        setcookie( 'scp_connection_error', $conn_err, time() + 3600, '/' );
                        return 0;
                    }
                }
            } else {
                $objSCP = 0;
                $conn_err = "Connection with SugarCRM not successful. Please check SugarCRM Version, URL, Username and Password.";
                setcookie( 'scp_connection_error', $conn_err, time() + 3600, '/' );
                return 0;
            }
        } else {
            $objSCP = 0;   
            setcookie( 'scp_connection_error', $check_var, time() + 3600, '/' );
            return 0;
        }
    }
}

if ( ! function_exists( 'scp_check_login' ) ) {
    add_action( 'wp', 'scp_check_login', 99 );
    function scp_check_login() {
        if ( !is_admin() ) {
            if ( isset( $_SESSION['scp_user_id'] ) != true ) {
                global $post;
                $curent_page = $post->ID;
                if ( get_option( 'biztech_redirect_profile' ) != NULL ) {
                    $profile_page = get_option( 'biztech_redirect_profile' );
                } else {
                    $page = get_page_by_path( 'bcp-profile' );
                    $profile_page = $page->ID;
                }
                
                if ( get_option( 'biztech_redirect_manange' ) != NULL ) {
                    $manage_page = get_option( 'biztech_redirect_manange' );
                } else {
                    $page = get_page_by_path( 'bcp-manage-page' );
                    $manage_page = $page->ID;
                }
                
                if ( $profile_page == $curent_page || $manage_page == $curent_page ) {
                    $biztech_redirect_login = get_page_link( get_option( 'biztech_redirect_login' ) );
                    if ( isset( $biztech_redirect_login ) && ! empty( $biztech_redirect_login ) ) {
                        $redirect_url = $biztech_redirect_login;
                    } else {
                        $redirect_url = home_url() . '/portal-login';
                    }

                    ob_clean();
                    wp_redirect( $redirect_url );
                    die();
                }
            } else {
                global $post;
                $curent_page = $post->ID;
                if ( get_option( 'biztech_redirect_login' ) != NULL) {
                    $login = get_option( 'biztech_redirect_login' );
                } else {
                    $page = get_page_by_path( 'bcp-login' );
                    $login = $page->ID;
                }
                
                if ( get_option( 'biztech_redirect_forgotpwd' ) != NULL ) {
                    $forgotpwd = get_option( 'biztech_redirect_forgotpwd' );
                } else {
                    $page = get_page_by_path( 'bcp-forgot-password' );
                    $forgotpwd = $page->ID;
                }
                
                if ( get_option( 'biztech_redirect_signup' ) != NULL ) {
                    $signup = get_option( 'biztech_redirect_signup' );
                } else {
                    $dash_page = get_page_by_path( 'scp_sign_up' );
                    $signup = $dash_page->ID;
                }
                
                if ( ! empty( $_SESSION['scp_user_id'] ) ) {
                    if ( $login == $curent_page || $forgotpwd == $curent_page || $signup == $curent_page ) {
                        $biztech_redirect_manange = get_page_link( get_option( 'biztech_redirect_manange' ) );
                        if ( isset( $biztech_redirect_manange ) && ! empty( $biztech_redirect_manange ) ) {
                            $redirect_url = $biztech_redirect_manange;
                        } else {
                            $redirect_url = home_url() . '/portal-manage-page';
                        }

                        ob_clean();
                        wp_redirect( $redirect_url );
                        die();
                    }
                }
            }
        }
    }
}

if ( ! function_exists( 'scp_uninstall' ) ) {
    register_uninstall_hook(__FILE__, 'scp_uninstall');
    function scp_uninstall() {
        
        global $wpdb;
        
        delete_option( 'biztech_scp_name' );
        delete_option( 'biztech_scp_rest_url' );
        delete_option( 'biztech_scp_username' );
        delete_option( 'biztech_scp_password' );
        delete_option( 'biztech_scp_case_per_page' );
        delete_option( 'biztech_scp_upload_image' );
        delete_option( 'biztech_scp_portal_menu_title' );
        delete_option( 'biztech_scp_sugar_crm_version' );
        delete_option( 'biztech_redirect_login' );
        delete_option( 'biztech_redirect_signup' );
        delete_option( 'biztech_redirect_profile' );
        delete_option( 'biztech_redirect_forgotpwd' );
        delete_option( 'biztech_redirect_manange' );
        delete_option( 'biztech_cases_per_user' );

        $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_name IN ( 'portal-sign-up', 'portal-login','portal-profile','portal-forgot-password','portal-manage-page' ) AND post_type = 'page'" );
    }
}

if ( ! function_exists( 'scp_style_and_script' ) ) {
    add_action( 'wp_enqueue_scripts', 'scp_style_and_script', 15 );
    function scp_style_and_script() {
        
        wp_enqueue_style( 'font-awesome.min', SCP_PLUGIN_URL.'/assets/css/font-awesome.min.css' );
        //wp_enqueue_style( 'jquery-ui-timepicker-addon', SCP_PLUGIN_URL.'/assets/css/jquery-ui.min.css' );
        wp_enqueue_style( 'scp-style', SCP_PLUGIN_URL.'/assets/css/scp-style.css' );

        wp_enqueue_script( 'bootstrap.min', SCP_PLUGIN_URL.'/assets/js/bootstrap.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'jquery.cookie.min', SCP_PLUGIN_URL.'/assets/js/jquery.cookie.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'index', SCP_PLUGIN_URL.'/assets/js/index.js', array( 'jquery' ) );
        wp_enqueue_script( 'app', SCP_PLUGIN_URL.'/assets/js/app.js', array( 'jquery' ) );
        wp_enqueue_script( 'jquery.blockui.min', SCP_PLUGIN_URL.'/assets/js/jquery.blockui.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'jquery.validate', SCP_PLUGIN_URL.'/assets/js/jquery.validate/jquery.validate.js', array( 'jquery' ) );
        wp_enqueue_script( 'scp-manage-page-js', SCP_PLUGIN_URL.'/assets/js/scp-manage-page-js.js', array( 'jquery' ) );
        
        wp_register_script( 'scp-standalone', SCP_PLUGIN_URL . '/assets/js/standalone.js', array( 'jquery' ) );
    }
}

if ( ! function_exists( 'scp_modify_jquery' ) ) {
    add_action( 'wp_enqueue_scripts', 'scp_modify_jquery' );
    function scp_modify_jquery() {
        
        global $post;
        
        $profile_page = $manage_page = $forgotpwd = $signup = $login = '';
        $curent_page = $post->ID;
        if ( ! empty( $curent_page ) ) {

            if ( get_option( 'biztech_redirect_profile' ) != NULL ) {
                $profile_page = get_option( 'biztech_redirect_profile' );
            }
            
            if ( get_option( 'biztech_redirect_manange' ) != NULL ) {
                $manage_page = get_option( 'biztech_redirect_manange' );
            }
            
            if ( get_option( 'biztech_redirect_forgotpwd' ) != NULL ) {
                $forgotpwd = get_option( 'biztech_redirect_forgotpwd' );
            }
            
            if ( get_option( 'biztech_redirect_signup' ) != NULL ) {
                $signup = get_option( 'biztech_redirect_signup' );
            }
            
            if ( get_option( 'biztech_redirect_login' ) != NULL ) {
                $login = get_option( 'biztech_redirect_login' );
            }
        }
    }    
}

if ( ! function_exists( 'scp_js_variables' ) ) {
    add_action('wp_head', 'scp_js_variables');
    function scp_js_variables() {
        
        $get_current_url = explode( '?', esc_url( $_SERVER['REQUEST_URI']), 2 );
        $current_url = $get_current_url[0];
        $get_page_parameter = '';
        if ( ! isset( $get_current_url[1] ) ) {
            $get_current_url[1] = null;
        }
        
        if ( $get_current_url[1] ) {
            $get_page_parameter = explode( '&', $get_current_url[1]);
            $get_page_parameter = $get_page_parameter[0];
        }
        
        if ( $get_page_parameter != NULL ) {
            $current_url .= "?" . $get_page_parameter;
        } else {
            $current_url .= "?scp-page=list-cases";
        }
        $scp_page = sanitize_text_field( $_REQUEST['scp-page'] );
        ?>
        <script type="text/javascript">
            var ajaxurl = '<?php echo admin_url( "admin-ajax.php" ); ?>';
            var image_url = '<?php echo SCP_IMAGES_URL; ?>';
            var pathId = '<?php echo ( $scp_page ? $scp_page : '' ); ?>';
        </script>
        <?php
    }    
}

if ( ! function_exists( 'scp_admin_scripts' ) ) {
    $scp_page = sanitize_text_field( $_GET['page'] );
    if ( $scp_page == 'biztech-crm-portal' ) {
        add_action('admin_print_scripts', 'scp_admin_scripts' );
        function scp_admin_scripts() {
            
            wp_enqueue_script( 'media-upload' );
            wp_enqueue_script( 'thickbox' );
            wp_register_script( 'my-upload', SCP_PLUGIN_URL.'/assets/js/scp-admin-js.js', array( 'jquery', 'media-upload', 'thickbox' ) );
            wp_enqueue_script( 'my-upload' );
        }
    }
}

if ( ! function_exists( 'scp_admin_styles' ) ) {
    $scp_page = sanitize_text_field( $_GET['page'] );
    if ( $scp_page == 'biztech-crm-portal' ) {
        add_action( 'admin_print_styles', 'scp_admin_styles' );
        function scp_admin_styles() {
            
            wp_enqueue_style( 'thickbox' );
        }
    }
}

if ( ! function_exists( 'scp_do_output_buffer' ) ) {
    add_action( 'init', 'scp_do_output_buffer' );
    function scp_do_output_buffer() {
        
        ob_start();
    }
}

if ( ! function_exists( 'scp_theme_custom_upload_mimes' ) ) {
    add_filter( 'upload_mimes', 'scp_theme_custom_upload_mimes' );
    function scp_theme_custom_upload_mimes($existing_mimes) {

        $allowed_mime_types = array( 
            'jpg|jpeg|jpe'  => 'image/jpeg', 
            'gif'           => 'image/gif', 
            'png'           => 'image/png', 
            'bmp'           => 'image/bmp', 
            'tif|tiff'      => 'image/tiff', 
            'ico'           => 'image/x-icon',
        );

        $arr = array_diff( $existing_mimes, $allowed_mime_types );
        foreach ( $arr as $ar_key => $ar_val ) {
            unset( $arr[$ar_key] );
        }
        
        return $allowed_mime_types;
    }
}

if ( ! function_exists( 'scp_is_curl' ) ) {
    function scp_is_curl() {
        
        $conn_err = 1;
        if ( ! ( version_compare( phpversion(), '5.0.0', '>=' ) ) ) {
            $conn_err = 'Plugin requires minimum PHP 5.0.0 to function properly. Please upgrade PHP or deactivate Plugin';
            return $conn_err;
        }
        
        if ( ! function_exists( 'curl_version' ) ) {
            $conn_err = 'Please enable PHP CURL extension to make this plugin work.';
            return $conn_err;
        }
        
        if ( ! function_exists( 'json_decode' ) ) {
            $conn_err = 'Please enable PHP JSON extension to make this plugin work.';
            return $conn_err;
        }
        
        if ( $conn_err == 1 ) {
            return 'yes';
        }
    }
}

if ( ! function_exists( 'scp_mail_user' ) ) {
    function scp_mail_user( $username_c, $password_c, $first_name, $last_name, $email ) {
    
        $biztech_redirect_login = get_page_link( get_option( 'biztech_redirect_login' ) );
        $url = $biztech_redirect_login;
        $admin_email = get_option( 'admin_email' );

        $user_info = get_user_by( 'email', $admin_email );
        $admin_username = $user_info->user_login;
    
        if ( $first_name != '' ) {
            $name = $first_name . " " . $last_name;
        } else {
            $name = $last_name;
        }
        $to = $email;

        if ( get_option('biztech_scp_mail_subject' ) != NULL) {
            $subject = get_option( 'biztech_scp_mail_subject' );
        } else {
            $subject = get_bloginfo('name') . ' Account Details';
        }

        if ( get_option('biztech_scp_mail_body') != NULL ) {
            $body = get_option('biztech_scp_mail_body');
            $body = 'Dear ' . $name . ',' . "<br> <br>" . get_option('biztech_scp_mail_body') . "<br> <br>" . 'Username: ' . $username_c . "<br> " . 'Password: ' . $password_c . "<br> <br>" . 'Log in at: ' . $url . ' to get started';
        } else {
            $body = 'Dear ' . $name . ',' . "<br> <br>" . "Thank you for creating an account with us. Now you can manage your different modules." . "<br> <br>" . 'Username: ' . $username_c . "<br> " . 'Password: ' . $password_c . "<br> <br>" . 'Log in at: ' . $url . ' to get started';
        }
        
        if (get_option( 'biztech_scp_upload_image') != NULL ) {
            $crm_logo = "<img src='" . get_option('biztech_scp_upload_image') . "' title='" . get_bloginfo('name') . "' alt='" . get_bloginfo('name') . "' style='border:none' width='100'>";
        } else {
            $crm_logo = get_bloginfo('name');
        }
        
        $body_full = "<table width='100%' cellspacing='0' cellpadding='0' border='0' style='background:#e4e4e4; padding:30px 0;'>
            <tbody>
              <tr>
                <td><center>
                      <table width='800' cellspacing='0' cellpadding='0' border='0' style='font-family: arial,sans-serif; font-size:14px; border-top:4px solid #35b0bf; background:#fff; line-height: 22px;'>
                          <tr>
                              <td style='padding: 30px 40px 0;'>
                                  <table width='100%' cellspacing='0' cellpadding='0' border='0' style='border-bottom:2px solid #ddd;'>
                                      <tr>
                                          <td style='padding-bottom:20px;'>
                                              <a href='#'>
                                                  $crm_logo
                                              </a>
                                          </td>
                                          <td align='right' valign='top' style='padding-bottom:20px;'>
                                             <h2 style='margin:0; padding:0; color:#35b0bf; font-size:22px;'>" . get_option('biztech_scp_name') . "</h2> 
                                          </td>
                                      </tr>
                                  </table>
                              </td>

                          </tr>
                          <tr>
                              <td style='padding:30px 40px 0; color: #484848;' align='left' valign='top'>
                                   <p>" . $body . "</p>
                                  <p>Regards, <br/>
                                  " . get_bloginfo('name') . "</p>
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
                                              <a target='_blank' style='color: #35b0bf;text-decoration: none; font-size:14px;' href='" . get_site_url() . "'>" . get_site_url() . "</a>
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
        add_filter( 'wp_mail_content_type', 'scp_wpdocs_set_html_mail_content_type' );
        wp_mail( $to, $subject, $body_full );
        remove_filter( 'wp_mail_content_type', 'scp_wpdocs_set_html_mail_content_type' );
    }
}

if ( ! function_exists( 'scp_mail_user_forgotpwd' ) ) {
    function scp_mail_user_forgotpwd( $username, $password, $url, $email ) {
 
        $biztech_redirect_login = get_option( 'biztech_redirect_login' );
        $url = $biztech_redirect_login;
        $admin_email = get_option( 'admin_email' );

        $user_info = get_user_by( 'email', $admin_email );
        $admin_username = $user_info->user_login;

        if ( $first_name != '' ) {
            $name = $first_name . " " . $last_name;
        } else {
            $name = $last_name;
        }
        
        $to = $email;
        if ( get_option( 'biztech_scp_upload_image' ) != NULL ) {
            $crm_logo = "<img src='" . get_option('biztech_scp_upload_image') . "' title='" . get_bloginfo('name') . "' alt='" . get_bloginfo('name') . "' style='border:none' width='100'>";
        } else {
            $crm_logo = get_bloginfo('name');
        }
        $body = 'Dear ' . $username . ',' . "<br> <br> Your " . get_bloginfo('name') . " account details is as below : " . "<br> <br>" . 'Username: ' . $username . "<br>" . 'Password: ' . $password;
        $subject = get_bloginfo('name') . ': Password Recover';

        $body_full = "<table width='100%' cellspacing='0' cellpadding='0' border='0' style='background:#e4e4e4; padding:30px 0;'>
            <tbody>
              <tr>
                <td><center>
                      <table width='800' cellspacing='0' cellpadding='0' border='0' style='font-family: arial,sans-serif; font-size:14px; border-top:4px solid #35b0bf; background:#fff; line-height: 22px;'>
                          <tr>
                              <td style='padding: 30px 40px 0;'>
                                  <table width='100%' cellspacing='0' cellpadding='0' border='0' style='border-bottom:2px solid #ddd;'>
                                      <tr>
                                          <td style='padding-bottom:20px;'>
                                              <a href='#'>
                                                  $crm_logo
                                              </a>
                                          </td>
                                          <td align='right' valign='top' style='padding-bottom:20px;'>
                                             <h2 style='margin:0; padding:0; color:#35b0bf; font-size:22px;'>" . get_option('biztech_scp_name') . "</h2> 
                                          </td>
                                      </tr>
                                  </table>
                              </td>

                          </tr>
                          <tr>
                              <td style='padding:30px 40px 0; color: #484848;' align='left' valign='top'>
                                   <p>" . $body . "</p>
                                  <p>Regards, <br/>
                                  " . get_bloginfo('name') . "</p>
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
                                              <a target='_blank' style='color: #35b0bf;text-decoration: none; font-size:14px;' href='" . get_site_url() . "'>" . get_site_url() . "</a>
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

        add_filter( 'wp_mail_content_type', 'scp_wpdocs_set_html_mail_content_type' );
        $return_var = wp_mail( $to, $subject, $body_full );
        remove_filter( 'wp_mail_content_type', 'scp_wpdocs_set_html_mail_content_type' );
        
        return $return_var;
    }
}

if ( ! function_exists( 'scp_wpdocs_set_html_mail_content_type' ) ) {
    function scp_wpdocs_set_html_mail_content_type() {
        
        return 'text/html';
    }
}