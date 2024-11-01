<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<div class="scp-login-form scp-form"><div class="login-title">
        <?php if (get_option('biztech_scp_upload_image') != NULL) { ?>
            <img src="<?php echo get_option('biztech_scp_upload_image'); ?>"  width="100"/><?php
        } else {
            echo $defaultImage;
        }
        ?>
        <?php if (get_option('biztech_scp_name') != NULL) {//Added by BC on 22-sep-2015  ?>
            <h3  class="scp-login-heading"><?php echo get_option('biztech_scp_name'); ?></h3>
        <?php } ?>
    </div>
    <?php   
    $signup = sanitize_text_field( $_REQUEST['signup'] );
    if ($signup == 'true') {

        if (isset($_COOKIE['scp_signup_suc']) && $_COOKIE['scp_signup_suc'] != '') {
            $cookie_err = $_COOKIE['scp_signup_suc'];
            unset($_COOKIE['scp_signup_suc']);
            echo "<span class='success login-success'>" . $cookie_err . "</span>";
        }
    }
    
    $error = sanitize_text_field( $_REQUEST['error'] );
    if ($error != null) {
        if (isset($_COOKIE['scp_login_error']) && $_COOKIE['scp_login_error'] != '') {
            $cookie_err = $_COOKIE['scp_login_error'];
            unset($_COOKIE['scp_login_error']);
            echo "<span class='error'>" . $cookie_err . "</span>";
        }
    }
    
    $conerror = sanitize_text_field( $_REQUEST['conerror'] );
    if ($conerror != null) {
        if (isset($_COOKIE['scp_connection_error']) && $_COOKIE['scp_connection_error'] != '') {
            $cookie_err = $_COOKIE['scp_connection_error'];
            unset($_COOKIE['scp_login_error']);
            return "<div class='error settings-error' id='setting-error-settings_updated'> 
            <p><strong>$cookie_err</strong></p>
        </div>";
        }
    }
    ?>
    <form name="scp-login-form" id="scp-login-form" action="<?php echo home_url() ?>/wp-admin/admin-post.php" method="post"> 
        <ul>
            <li class="required">
                <span><input type="text" class="input-text" name="scp_username" id="scp-username" required="" placeholder="Username"></span>
            </li>
            <li class="required">
                <span><input type="password" class="input-text" name="scp_password" id="scp-password" required="" placeholder="* * * * * * * *"></span>
            </li>
            <li class="scp-send  last">
                <input type="hidden" name="action" value="scp_login">
                <?php
//get option to redirect to which page
                $currentURL = explode('?', esc_url( $_SERVER['REQUEST_URI']), 2);
                $currentURL = $currentURL[0];
//get option to redirect to which page for sign up
                if (get_page_link(get_option('biztech_redirect_signup')) != NULL) {
                    $redirectURL_signup = get_page_link(get_option('biztech_redirect_signup'));
                } else {
                    $redirectURL_signup = home_url() . "/portal-sign-up/";
                }
//get option to redirect to which page for forgot pwd
                if (get_page_link(get_option('biztech_redirect_forgotpwd')) != NULL) {
                    $redirectURL_forgot_pwd = get_page_link(get_option('biztech_redirect_forgotpwd'));
                } else {
                    $redirectURL_forgot_pwd = home_url() . "/portal-forgot-password/";
                }
                ?>
                <input type="hidden" name="scp_current_url" value="<?php echo $currentURL; ?>">
                <span><input type="submit" name="scp_login_form_submit" id="scp-login-form-submit" value="LogIn"></span>
                <span class="right">
                    <a href="<?php echo $redirectURL_signup; ?>"> <?php _e('Sign Up Now!'); ?></a>
                    <a href="<?php echo $redirectURL_forgot_pwd; ?>"><?php _e('Forgot Password?'); ?></a>
                </span>
            </li>
        </ul>
    </form>
    </div>
<script type="text/javascript">
    jQuery("#scp-login-form").validate();
    jQuery('.entry-header').addClass("login-title");
</script>