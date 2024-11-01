<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<header class="entry-header entry-wrapper sign_up_header">
    <div class="container"> 
        <h1 class="entry-title"><?php _e('Portal Sign Up'); ?></h1>
   
    <?php
    $biztech_redirect_login = get_page_link(get_option('biztech_redirect_login'));
    if ($biztech_redirect_login != NULL) {
        $redirect_url_login = $biztech_redirect_login;
    } else {
        $redirect_url_login = home_url() . "/portal-login/";
    }
    ?>
    <div class='userinfo'>
        <a class='fa fa-user scp-Accounts' href='<?php echo $redirect_url_login; ?>'> <?php _e("Login"); ?></a>
    </div> </div>
        
</header>
<div class="scp-form scp-form-two-col">

    <?php
    $current_url = explode('?', esc_url( $_SERVER['REQUEST_URI']), 2);
    $current_url = $current_url[0];
    ?>

    <div class='scp-form scp-form-two-col'>
        <form method="post" action="<?php echo home_url(); ?>/wp-admin/admin-post.php" name="signup_form" id="signup_form">
            <div class="scp-form-container container sign-up-container">
                <?php
                $error = sanitize_text_field( $_REQUEST['error'] );
                if ($error != null) {
                    if (isset($_COOKIE['scp_signup_err']) && $_COOKIE['scp_signup_err'] != '') {
                        $cookie_err = $_COOKIE['scp_signup_err'];
                        unset($_COOKIE['scp_signup_err']);
                        echo "<span class='error error-line error-msg settings-error'>" . $cookie_err . "</span>";
                    }
                }

                $conerror = sanitize_text_field( $_REQUEST['conerror'] );
                if ($conerror != null) {
                    if (isset($_COOKIE['scp_connection_error']) && $_COOKIE['scp_connection_error'] != '') {
                        $cookie_err = $_COOKIE['scp_connection_error'];
                        unset($_COOKIE['scp_login_error']);
                        return "<div class='error error-line error-msg settings-error' id='setting-error-settings_updated'> 
            <p><strong>$cookie_err</strong></p>
        </div>";
                    }
                }
                ?>
                <div class="scp-col-full">
                    <div class="scp-col-6">
                        <div class="scp-form-group required">
                            <label>Username</label>
                            <input class='input-text scp-form-control' type='text' name='add-signup-username' id='add-signup-username'  required/>
                        </div>
                    </div>
                    <div class="scp-col-6">
                        <div class="scp-form-group required">
                            <label>Password</label>
                            <input class='input-text scp-form-control' type='password' name='add-signup-password' id='add-signup-password' required/>
                        </div>
                    </div>
                </div>
                <div class="scp-col-full">
                    <div class="scp-col-6">
                        <div class="scp-form-group">
                            <label>First Name :</label>
                            <input class='input-text scp-form-control' type='text' name='add-signup-first-name' id='add-signup-first-name' />
                        </div>
                    </div>
                    <div class="scp-col-6">
                        <div class="scp-form-group required">
                            <label>Last Name</label>
                            <input class='input-text scp-form-control' type='text' name='add-signup-last-name' id='add-signup-last-name' required/>
                        </div>
                    </div>
                </div>
                <div class="scp-col-full">
                    <div class="scp-col-6">
                        <div class="scp-form-group">
                            <label>Work Phone :</label>
                            <input class='input-text scp-form-control' type='text' name='add-signup-work-phone' id='add-signup-work-phone' />
                        </div>
                    </div>
                    <div class="scp-col-6">
                        <div class="scp-form-group">
                            <label>Mobile :</label>
                            <input class='input-text scp-form-control' type='text' name='add-signup-mobile' id='add-signup-mobile' />
                        </div>
                    </div>
                </div>
                <div class="scp-col-full">
                    <div class="scp-col-6">
                        <div class="scp-form-group">
                            <label>Fax:</label>
                            <input class='input-text scp-form-control' type='text' name='add-signup-fax' id='add-signup-fax' />
                        </div>
                    </div>
                    <div class="scp-col-6">
                        <div class="scp-form-group">
                            <label>Title :</label>
                            <input class='input-text scp-form-control' type='text' name='add-signup-title' id='add-signup-title' />
                        </div>
                    </div>
                </div>
                <div class="scp-col-full">
                    <div class="scp-col-6">
                        <div class="scp-form-group required">
                            <label>Email Address</label>
                            <input class='input-text scp-form-control' type='email' name='add-signup-email-address' id='add-signup-email-address' required/>
                        </div>
                    </div>
                    <div class="scp-col-6">
                        <div class="scp-form-group">
                            <label>Primary Address :</label>
                            <textarea class='input-text scp-form-control' id='add-signup-primary-address' name='add-signup-primary-address'> </textarea>
                        </div>
                    </div>
                </div>
                <div class='scp-send scp-form-actions'>
                    <input type='hidden' name='action' value='scp_sign_up'>
                    <input type='hidden' name='scp_current_url' value='<?php echo $current_url; ?>'>
                    <span class='desc'><input type='submit' name='add-sign-up' value='Sign Up' class='scp-Accounts'/></span>
                </div>  
            </div>



        </form>

    </div>
</div>
<script type="text/javascript">
    jQuery("#signup_form").validate();
</script>