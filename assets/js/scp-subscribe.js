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
            submit.removeAttr("disabled").removeClass('disabled');
            preloader.fadeOut();
            message.fadeIn().html( data );            
        });
        return false;
    });

     // For equal-height

         
        $(window).load(function () {
            resetHeight1();
        });
        $(window).resize(function () {
            resetHeight1();
        });
        function resetHeight1() {
            var maxHeight = 0;
            $(".scp-upgrade-page .blog-content").height("auto").each(function () {
                maxHeight = $(this).height() > maxHeight ? $(this).height() : maxHeight;
            }).height(maxHeight);
        }
});