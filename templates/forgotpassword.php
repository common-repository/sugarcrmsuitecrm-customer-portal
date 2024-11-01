<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<div class="scp-login-form scp-form scp-form-two-col scp-forgotpas-form">

    <?php
    $current_url = explode('?', esc_url( $_SERVER['REQUEST_URI']), 2);
    $current_url = $current_url[0];
    $biztech_redirect_login = get_page_link(get_option('biztech_redirect_login'));
    if ($biztech_redirect_login != NULL) {
        $redirect_url_login = $biztech_redirect_login;
    } else {
        $redirect_url_login = home_url() . "/portal-login/";
    }
    ?><div class="login-title">
        <h3  class="scp-login-heading"><?php _e("Portal Forgot Password"); ?></h3>
    </div>
     <?php
    $error = sanitize_text_field( $_REQUEST['error'] );
    if ($error != null) {
        if (isset($_COOKIE['scp_frgtpwd']) && $_COOKIE['scp_frgtpwd'] != '') {
            $cookie_err = $_COOKIE['scp_frgtpwd'];
            unset($_COOKIE['scp_frgtpwd']);
            echo "<span class='error'>$cookie_err</span>";
        }
    }
    
    $sucess = sanitize_text_field( $_REQUEST['sucess'] );
    if ($sucess != null) {
        if (isset($_COOKIE['scp_frgtpwd']) && $_COOKIE['scp_frgtpwd'] != '') {
            $cookie_err = $_COOKIE['scp_frgtpwd'];
            unset($_COOKIE['scp_frgtpwd']);
            echo "<span class='success'>$cookie_err</span>";
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
    <form action="<?php echo home_url(); ?>/wp-admin/admin-post.php" method="post" id="commentForm">
        <ul>
            <li class="required">                                          
                <!--                <label>Enter Username :</label>-->
                <span><input class="input-text" type="text" name="forgot-password-username" id="forgot-password-username" required placeholder="Username"> </span>
            </li>
            <li class="required">                                          
                <!--                <label>Enter Email Address :</label>-->
                <span><input class="input-text" type="email" name="forgot-password-email-address" id="forgot-password-email-address" required placeholder="Email Address"> </span>
            </li>
            <li class="scp-send last">
                <input type="hidden" name="action" value="scp_forgot_password">
                <input type="hidden" name="scp_current_url" value="<?php echo $current_url; ?>">
                <span class="desc"><input type="submit" value="Submit" class='scp-Accounts'></span>
                <span class="right">
                    <a href="<?php echo $redirect_url_login; ?>">Back To Login</a>
                </span>
            </li>    
        </ul>   
    </form>
   </div>
<script type="text/javascript">
    jQuery("#commentForm").validate();
    jQuery('.entry-header').addClass("forget-title");
</script>