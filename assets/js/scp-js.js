jQuery(document).ready(function() {
        //jQuery('#demo').DataTable();
} );


jQuery('.my-color-field').wpColorPicker();

jQuery( document ).ready( function( $ ) {
    $(".wp-color-result").off("click");
    $( '.wp-picker-container' ).on( 'click', function () {        
        location.href = 'admin.php?page=biztech-crm-portal-pro';
    });

    $('select.biztech_scp_pro').click(function () {
        this.blur();
    });

    $('.biztech_scp_pro').on( 'mousedown', function () {
        location.href = 'admin.php?page=biztech-crm-portal-pro';
    });
});

jQuery(document).ready(function($) {

    // for user registration form
    $("form#subscribe_form").submit(function(){


        var submit = $("#subscribe_form #subscribe"),
        preloader = $("#subscribe_form #preloader"),
        message	= $("#subscribe_form #message"),
        contents = {
            action: 'user_subscription',
            subscribe_mail: this.subscribe_mail.value
        };
        // disable button onsubmit to avoid double submision
        submit.attr("disabled", "disabled").addClass('disabled');
        message.fadeOut();
        preloader.fadeIn();

        $.post( this.ajax_theme.value, contents, function( data ){
            setTimeout(function () {
                submit.removeAttr("disabled").removeClass('disabled');
                preloader.fadeOut();
                message.fadeIn().html( data );
            },1000);
        });
        return false;
    });
});