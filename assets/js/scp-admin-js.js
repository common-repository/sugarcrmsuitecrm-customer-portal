jQuery(document).ready(function () {
        
    jQuery('#upload_image_button').click(function () {
        formfield = jQuery('#upload_image').attr('name');
        tb_show('Upload a logo', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });

    window.send_to_editor = function (html) {
        // imgurl = jQuery('img',html).attr('src');//not in 4.4.2 but work in 4.3.2
        //imgurl = jQuery(html).attr('src');
        var imgExists = html.indexOf('<img src="');

        if (imgExists > -1) {
            var i = imgExists + 10;

            html = html.substr(i);
            html = html.substr(0, html.indexOf('"'));
        }
        jQuery('#upload_image').val(html);
        tb_remove();
        jQuery('#wpss_upload_image_thumb').html("<img height='65' src='" + html + "'/>");
    }

    

});