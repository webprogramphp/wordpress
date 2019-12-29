<?php
/*
 * Enqueue scripts and styles for the front end.
 */
function a1_scripts() {
    global $a1_options;
    wp_enqueue_style('a1-googlefonts-lato', a1_font_url(), array(), null);
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.css', array());
    wp_enqueue_style('fontaewsome', get_template_directory_uri() . '/css/font-awesome.css', array());
    wp_enqueue_style('a1-default-css', get_template_directory_uri() . '/css/default.css', array());
    wp_enqueue_style('a1-media-css', get_template_directory_uri() . '/css/media.css', array());

    wp_enqueue_style('style', get_stylesheet_uri());
   
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.js', array('jquery'));
    wp_enqueue_script('a1-default', get_template_directory_uri() . '/js/default.js', array('jquery'));
    wp_enqueue_script('a1-custom-menu', get_template_directory_uri() . '/js/custom-menu.js', array('jquery'));
    
    if (get_theme_mod ( 'a1_scroll_top_header',false)):
        wp_localize_script( 'a1-default', 'scroll_top_header', 'yes' );
    else:
        wp_localize_script( 'a1-default', 'scroll_top_header', 'no' );
    endif;
    wp_enqueue_script('owl-carousel', get_template_directory_uri() . '/js/owl.carousel.js', array('jquery'));
    if (is_singular())
        wp_enqueue_script("comment-reply");

    a1_custom_css();
}
add_action('wp_enqueue_scripts', 'a1_scripts');
?>