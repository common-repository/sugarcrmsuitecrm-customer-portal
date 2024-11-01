<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

global $getContactInfo;
    if (get_page_link(get_option('biztech_redirect_manange')) != NULL) {
        $redirectURL_manage = get_page_link(get_option('biztech_redirect_manange'));
    } else {
        $redirectURL_manage = home_url() . "/portal-manage-page/";
    }
?>
<header class="entry-header entry-wrapper">
    <div class="container">
        <h1 class="entry-title"><?php _e('My Profile'); ?></h1>

        <div class='userinfo'>
            <a href='javascript:void(0)' class="scp-menu-profile"><?php echo $_SESSION['scp_user_account_name']; ?> </a> <ul class="scp-open-profile-menu"><li><a class='fa fa-bars side-icon-wrapper'  href='<?php echo $redirectURL_manage; ?>'> <?php _e("Manage Page"); ?></a></li><li><a class='fa fa-power-off'  href='?logout=true'> <?php _e("Log Out"); ?></a></li></ul>
        </div>
    </div>
</header>
<div class="entry-content">
    <?php
    
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
    
    if (isset($_COOKIE['scp_connection_login']) && $_COOKIE['scp_connection_login'] != '') {
        $cookie_err = $_COOKIE['scp_connection_login'];
        unset($_COOKIE['scp_login_error']);
        wp_redirect(home_url());
    }
    

    $current_url = explode('?', esc_url( $_SERVER['REQUEST_URI']), 2);
    $current_url = $current_url[0];
    ?>
    <div class='scp-form scp-form-two-col'>
        <form method="post" action="<?php echo home_url(); ?>/wp-admin/admin-post.php" name="signup_form" id="signup_form">
            <div class="scp-form-container container sign-up-container">
                <?php
                $sucess = sanitize_text_field( $_REQUEST['success'] ); 
                if ($sucess != null) {
                    if (isset($_COOKIE['scp_profile_succ']) && $_COOKIE['scp_profile_succ'] != '') {
                        $cookie_err = $_COOKIE['scp_profile_succ'];
                        echo "<span class='success'>" . $cookie_err . "</span>";
                        unset($_COOKIE['scp_profile_succ']);
                    }
                }
                ?>
                <div class="scp-col-full">
                    <div class="scp-col-6">
                        <div class="scp-form-group required">                                          
                            <label class="remove_m">Username</label>
                            <input class='input-text scp-form-control disable' type='text' name='add-signup-username' id='add-signup-username' value="<?php
                            if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
                                echo $getContactInfo->username_c->value;
                            } else {
                                echo $getContactInfo->username_c;
                            }
                            ?>" readonly="readonly" disable="true"/>
                        </div>
                    </div>
                    <div class="scp-col-6">
                        <div class="scp-form-group required">
                            <label>Password</label>
                            <input class='input-text scp-form-control' type='password' name='add-signup-password' id='add-signup-password' value="<?php
                            if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
                                echo $getContactInfo->password_c->value;
                            } else {
                                echo $getContactInfo->password_c;
                            }
                            ?>" required/>
                        </div>
                    </div>
                </div>
                <div class="scp-col-full">
                    <div class="scp-col-6">
                        <div class="scp-form-group">                                          
                            <label>First Name :</label>
                            <input class='input-text scp-form-control' type='text' name='add-signup-first-name' id='add-signup-first-name' value="<?php
                            if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
                                echo $getContactInfo->first_name->value;
                            } else {
                                echo $getContactInfo->first_name;
                            }
                            ?>" />
                        </div>
                    </div>
                    <div class="scp-col-6">
                        <div class="scp-form-group required">                                          
                            <label>Last Name</label>
                            <input class='input-text scp-form-control' type='text' name='add-signup-last-name' id='add-signup-last-name' value="<?php
                            if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
                                echo $getContactInfo->last_name->value;
                            } else {
                                echo $getContactInfo->last_name;
                            }
                            ?>" required />
                        </div>
                    </div>
                </div>
                <div class="scp-col-full">
                    <div class="scp-col-6">
                        <div class="scp-form-group">                                          
                            <label>Work Phone :</label>
                            <input class='input-text scp-form-control' type='text' name='add-signup-work-phone' id='add-signup-work-phone' value="<?php
                            if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
                                echo $getContactInfo->phone_work->value;
                            } else {
                                echo $getContactInfo->phone_work;
                            }
                            ?>" />
                        </div>
                    </div>
                    <div class="scp-col-6">
                        <div class="scp-form-group">                                          
                            <label>Mobile :</label>
                            <input class='input-text scp-form-control' type='text' name='add-signup-mobile' id='add-signup-mobile' value="<?php
                            if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
                                echo $getContactInfo->phone_mobile->value;
                            } else {
                                echo $getContactInfo->phone_mobile;
                            }
                            ?>" />
                        </div>
                    </div>
                </div>
                <div class="scp-col-full">
                    <div class="scp-col-6">
                        <div class="scp-form-group">                                          
                            <label>Fax:</label>
                            <input class='input-text scp-form-control' type='text' name='add-signup-fax' id='add-signup-fax' value="<?php
                            if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
                                echo $getContactInfo->phone_fax->value;
                            } else {
                                echo $getContactInfo->phone_fax;
                            }
                            ?>" />
                        </div>
                    </div>
                    <div class="scp-col-6">
                        <div class="scp-form-group">                                          
                            <label>Title :</label>
                            <input class='input-text scp-form-control' type='text' name='add-signup-title' id='add-signup-title' value="<?php
                            if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
                                echo $getContactInfo->title->value;
                            } else {
                                echo $getContactInfo->title;
                            }
                            ?>" />
                        </div>
                    </div>
                </div>
                <div class="scp-col-full">
                    <div class="scp-col-6">
                        <div class="scp-form-group required">                                          
                            <label class="remove_m">Email Address</label>
                            <input class='input-text scp-form-control disable' type='email' name='add-signup-email-address' id='add-signup-email-address' value="<?php
                            if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
                                echo $getContactInfo->email1->value;
                            } else {
                                echo $getContactInfo->email1;
                            }
                            ?>"  readonly="readonly" disable="true"/>
                            <span class="error_signup" id='email_error'></span>
                        </div>
                    </div>
                    <div class="scp-col-6">
                        <div class="scp-form-group">                                          
                            <label>Primary Address :</label>
                            <textarea class='input-text scp-form-control' id='add-signup-primary-address' name='add-signup-primary-address'> <?php
                                if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
                                    echo $getContactInfo->primary_address_street->value;
                                } else {
                                    echo $getContactInfo->primary_address_street;
                                }
                                ?> </textarea>
                        </div>
                    </div>
                </div>
                
            <?php
            $current_url = explode('?', esc_url( $_SERVER['REQUEST_URI']), 2);
            $current_url = $current_url[0];
            ?>
            <div class='scp-form-actions'>
                <input type='hidden' name='action' value='scp_update_profile'>
                <input type='hidden' name='scp_current_url' value='<?php echo $current_url; ?>'>
                <input type='hidden' name='old-signup-password' id='old-signup-password' value="<?php
                            if ($sugar_crm_version == 6 || $sugar_crm_version == 5) {
                                echo $getContactInfo->password_c->value;
                            } else {
                                echo $getContactInfo->password_c;
                            }
                            ?>"/>
                <span class='desc'><input type='submit' name='update-profile' value='Save' class='hover active scp-button scp-Accounts' />&nbsp&nbsp<input type='button' value='Cancel' class='hover active scp-button' onclick="history.go(-1);return true;"/></span>
            </div>
                </div>
            </form>
</div>                           
<script type="text/javascript">
    jQuery("#signup_form").validate();
</script>