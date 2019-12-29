<?php
/*
Plugin Name: Vimeotheque Lite
Plugin URI: https://vimeotheque.com
Description: Previously known as "CodeFlavors Vimeo Video Post Lite", Vimeotheque Lite imports Vimeo videos directly into WordPress and display them as posts or embedded in existing posts and/or pages as single videos or playlists.
Author: CodeFlavors
Version: 1.3.2
Author URI: https://codeflavors.com
Text domain: codeflavors-vimeo-video-post-lite
Domain Path: languages
*/

if( defined( 'CVM_PATH' ) ){
	/**
	 * Display a notice if both Lite and PRO versions are active
	 */
	function cvm_double_ver_notice(){
		$plugin = plugin_basename( __FILE__ );
		$deactivate_url = wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . $plugin, 'deactivate-plugin_' . $plugin );
		?>
	<div class="notice notice-error is-dismissible">
        <p><?php printf( __( 'You have installed <strong>Vimeotheque PRO</strong> by CodeFlavors. You should %sdeactivate Vimeotheque Lite%s.', 'codeflavors-vimeo-video-post-lite' ), '<a href="' . $deactivate_url . '">', '</a>' ); ?></p>
    </div>
		<?php
	}
	add_action( 'admin_notices', 'cvm_double_ver_notice' );
	
	return;
}

define( 'CVM_PATH'		, plugin_dir_path(__FILE__) );
define( 'CVM_URL'		, plugin_dir_url(__FILE__) );
define( 'CVM_VERSION'	, '1.3.2');

include_once CVM_PATH.'includes/functions.php';
include_once CVM_PATH.'includes/shortcodes.php';
include_once CVM_PATH.'includes/libs/rest-api.class.php';
include_once CVM_PATH.'includes/libs/custom-post-type.class.php';
include_once CVM_PATH.'includes/libs/video-import.class.php';

/**
 * Enqueue player script on single custom post page
 */
function cvm_single_video_scripts(){
	
	$settings 	= cvm_get_settings();
	$is_visible = $settings['archives'] ? true : is_single();
	
	if( is_admin() || !$is_visible || !cvm_is_video() ){
		return;
	}
	cvm_enqueue_player();	
}
add_action('wp_print_scripts', 'cvm_single_video_scripts');

/**
 * Filter custom post content
 */
function cvm_single_custom_post_filter( $content ){
	
	$settings 	= cvm_get_settings();
	$is_visible = $settings['archives'] ? true : is_single();
	
	if( is_admin() || !$is_visible || !cvm_is_video() ){
		return $content;
	}
	
	/**
	 * Filter that can prevent video embedding by the plugin. Useful if user wants to implement
	 * his own templates for video post type.
	 * @var bool
	 */
	$allow_embed = apply_filters( 'cvm_automatic_video_embed' , true );
	if( !$allow_embed ){
	    return $content;
	}
	
	global $post;
	// check if post is password protected
	if( post_password_required( $post ) ){
		return $content;
	}
	
	$settings 	= cvm_get_video_settings( $post->ID, true );
	$video 		= get_post_meta($post->ID, '__cvm_video_data', true);
	
	$settings['video_id'] = $video['video_id'];

	if( !$video ){
	    return $content;
    }

	/**
     * Filter that can be used to modify the width of the embed
     * @var int
     */
    $width 	= apply_filters( 'cvm-embed-width', $settings['width'], $video, 'automatic_embed' );
	$height = cvm_player_height( $settings['aspect_ratio'] , $width, $settings['size_ratio'] );

	/**
	 * Filter on video container CSS class to add extra CSS classes
	 * Name: cvm_video_post_css_class
	 * Params: - an empty array
	 * - the post object that will embed the video
	 *
	 * @var string
	 */
	$class     = apply_filters( 'cvm_video_embed_css_class', array(), $post );
	$extra_css = implode( ' ', ( array ) $class );

	$video_data_atts = cvm_data_attributes( $settings );

	// if js embedding not allowed, embed by placing the iframe dirctly into the post content
	$plugin_embed_opt = cvm_get_player_settings();
	$embed_html = '<!--video container-->';
	if ( isset( $plugin_embed_opt['js_embed'] ) && ! $plugin_embed_opt['js_embed'] ) {
		$params     = array(
			'title'      => $settings['title'],
			'byline'     => $settings['byline'],
			'portrait'   => $settings['portrait'],
			'color'      => $settings['color'],
			'fullscreen' => $settings['fullscreen']
		);
		$embed_url  = 'https://player.vimeo.com/video/' . $video['video_id'] . '?' . http_build_query( $params, '', '&' );
		$extra_css  .= ' cvm_simple_embed';
		$embed_html = '<iframe src="' . $embed_url . '" width="100%" height="100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	}

	
	$video_container = '<div class="cvm_single_video_player ' . $extra_css . '" ' . $video_data_atts . ' style="width:' . $width . 'px; height:' . $height . 'px; max-width:100%;">' . $embed_html . '</div>';
	
	if( 'below-content' == $settings['video_position'] ){
		return $content . $video_container;
	}else{
		return $video_container . $content;
	}
}
add_filter('the_content', 'cvm_single_custom_post_filter');

/**
 * Plugin activation; register permalinks for videos
 */
function cvm_activation_hook(){
	global $CVM_POST_TYPE;
	if( !$CVM_POST_TYPE ){
		return;
	}
	// register custom post
	$CVM_POST_TYPE->register_post();
	// create rewrite ( soft )
	flush_rewrite_rules( false );

	$CVM_POST_TYPE->plugin_activation();
}
register_activation_hook( __FILE__, 'cvm_activation_hook');

/**
 * Load plugin textdomain
 */
function cvm_load_textdomain() {
    load_plugin_textdomain( 'codeflavors-vimeo-video-post-lite', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'cvm_load_textdomain' );

/**
 * Ask for user review
 */
function cvm_review_init(){
    global $CVM_POST_TYPE, $post;
    if( ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $CVM_POST_TYPE->get_post_type() ) || ( $post && $post->post_type === $CVM_POST_TYPE->get_post_type() ) ){
	    include_once CVM_PATH . 'includes/libs/review-callout.class.php';
	    $m = "It's great to see that you've been using plugin <strong>Vimeotheque Lite</strong> for a while now. Hopefully you're happy with it! <br>If so, would you consider leaving a positive review? It really helps to support the plugin and helps others to discover it too!";
	    $user = new CVM_User( 'cvm_ignore_notice_nag' );
	    $message = new CVM_Message( $m , 'https://wordpress.org/support/plugin/codeflavors-vimeo-video-post-lite/reviews/#new-post' );
	    new CVM_Review_Callout( 'cvm_plugin_review_callout', $message, $user );
    }
}
add_action( 'admin_init', 'cvm_review_init', 99999 );