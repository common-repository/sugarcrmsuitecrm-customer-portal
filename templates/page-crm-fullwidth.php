<?php
/**
 * Template Name : CRM Fullwidth
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 */
get_header();
scp_style_and_script();
?>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
while (have_posts()) : the_post();
$theme_name = wp_get_theme();//20-aug-2016
    if ($theme_name == "Twenty Fourteen") {
        $fullwidth_class_gen = "fullwidth-fourteen-theme";
    }
    if ($theme_name == "Twenty Fifteen") {
        $fullwidth_class_gen = "fullwidth-fifteen-theme";
    }
    if ($theme_name == "Twenty Sixteen") {
        $fullwidth_class_gen = "fullwidth-sixteen-theme";
    }
    ?>

    <div id="fullwidth-wrapper" class="crm_fullwidth <?php echo $fullwidth_class_gen; ?>">
        <?php
        // Include the page content.
        the_content();
        ?>
    </div>
<?php endwhile; ?>
<?php get_footer(); ?>
