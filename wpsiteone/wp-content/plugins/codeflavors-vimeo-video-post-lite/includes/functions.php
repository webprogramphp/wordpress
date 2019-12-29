<?php

/**
 * Creates from a number of given seconds a readable duration ( HH:MM:SS )
 * @param int $seconds
 */
function cvm_human_time( $seconds ){
	
	$seconds = absint( $seconds );
	
	if( $seconds < 0 ){
		return;
	}
	
	$h = floor( $seconds / 3600 );
	$m = floor( $seconds % 3600 / 60 );
	$s = floor( $seconds %3600 % 60 );
	
	return ( ($h > 0 ? $h . ":" : "").($m > 0 ? ($h > 0 && $m < 10 ? "0" : "") . $m . ":" : "0:") . ($s < 10 ? "0" : "") . $s);
	
}

/**
 * Query Vimeo for single video details
 * @param string $video_id
 * @param string $source
 */
function cvm_query_video( $video_id, $source = 'vimeo' ){

	if ( ! class_exists( 'CVM_Video_Import' ) ) {
		require_once CVM_PATH . 'includes/libs/video-import.class.php';
	}

	$args   = array(
		'feed'        => 'video',
		'query'       => $video_id
	);
	$vimeo  = new CVM_Video_Import( $args );
	$result = $vimeo->get_feed();
	if ( ! $result ) {
		$error = $vimeo->get_errors();
		if ( is_wp_error( $error ) ) {
			return $error;
		}
	}
	return $result;
}

/**
 * @deprecated 
 * @since 1.3
 * @uses cvm_is_video()
 * 
 * Checks that global $post is video post type
 */
function cvm_is_video_post(){
	return cvm_is_video();
}

/**
 * Utility function. Checks if a given or current post is video created by the plugin
 * @param object $post
 */
function cvm_is_video( $post = false ){
    if( !$post ){
	    $post = get_queried_object();
    }
    if( is_numeric( $post ) ){
        get_post( $post );
    }
    if( !$post ){
        return false;
    }

    if( cvm_get_post_type() == $post->post_type ){
        return true;
    }

    if( 'post' == $post->post_type ){
        $is_video = get_post_meta($post->ID, '__cvm_is_video', true);
        if( $is_video ){
            return true;
        }
    }

    return false;
}

function cvm_get_post_type(){
	global $CVM_POST_TYPE;
	return $CVM_POST_TYPE->get_post_type();
}

/**
 * Get plugin custom post type "category" taxonomy
 *
 * @return string
 */

function cvm_get_category(){
    global $CVM_POST_TYPE;
	return $CVM_POST_TYPE->get_post_tax();
}

/**
 * Get plugin custom post type tag taxonomy
 *
 * @return string
 */

function cvm_get_tag(){
    global $CVM_POST_TYPE;
	return $CVM_POST_TYPE->get_tag_tax();
}

/**
 * Adds video player script to page
 */
function cvm_enqueue_player(){

    $suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '.dev' : '';

	wp_enqueue_script(
		'cvm-video-player',
		CVM_URL.'assets/front-end/js/video-player' . $suffix . '.js',
		array('jquery', 'swfobject'),
		'1.0'
	);
	
	wp_enqueue_style(
			'cvm-video-player',
			CVM_URL.'assets/front-end/css/video-player.css'
	);
}

/**
 * Formats the response from the feed for a single entry
 * @param array $entry
 */
function cvm_format_video_entry( $raw_entry ){

	$thumbnails = array();
	if( isset( $raw_entry['pictures']['sizes'] ) ){
		foreach( $raw_entry['pictures']['sizes'] as $thumbnail ){
			$thumbnails[] = $thumbnail['link'];
		}
	}

	$stats = array(
		'comments' 	=> 0,
		'likes' 	=> 0,
		'views' 	=> 0
	);

	if( isset( $raw_entry['metadata']['connections']['comments']['total'] ) ){
		$stats['comments'] = $raw_entry['metadata']['connections']['comments']['total'];
	}

	if( isset( $raw_entry['metadata']['connections']['likes']['total'] ) ){
		$stats['likes'] = $raw_entry['metadata']['connections']['likes']['total'];
	}

	if( isset( $raw_entry['stats']['plays'] ) ){
		$stats['views'] = $raw_entry['stats']['plays'];
	}

	// extract tags
	$tags = array();
	if( isset( $raw_entry['tags'] ) && is_array( $raw_entry['tags'] ) ){
		foreach( $raw_entry['tags'] as $tag ){
			$tags[] = $tag['name'];
		}
	}

	$privacy = false;
	if( isset($raw_entry['privacy']) ){
		if( in_array( $raw_entry['privacy'], array('anybody', 'unlisted', 'disable') ) ){
			$privacy = 'public';
		}else{
			$privacy = 'private';
		}
	}

	$size = array();
	if( isset( $raw_entry['width'] ) && isset( $raw_entry['height'] ) ){
		$w = absint( $raw_entry['width'] );
		$h = absint( $raw_entry['height'] );
		$size = array(
			'width' => $w,
			'height' => $h,
			'ratio' => round(  $w/$h  , 2 )
		);
	}

	$entry = array(
		'video_id'		=> str_replace( '/videos/' , '', $raw_entry['uri'] ),
		'uploader'		=> $raw_entry['user']['name'],
		'uploader_uri'	=> $raw_entry['user']['uri'],
		'published' 	=> $raw_entry['created_time'],
		'updated'		=> isset( $raw_entry['modified_time'] ) ? $raw_entry['modified_time'] : false,
		'title'			=> $raw_entry['name'],
		'description' 	=> $raw_entry['description'],
		'category'		=> false,
		'tags'			=> $tags,
		'duration'		=> $raw_entry['duration'],
		'thumbnails'	=> $thumbnails,
		'stats'			=> $stats,
		'privacy'		=> $privacy,
		'size'			=> $size,
        'tags'          => $tags,
	);

    return $entry;
}

/**
 * Calculate video size ratio based on video size returned by Vimeo
 * @param $video_data - video data retrieved from Vimeo
 *
 * @return bool|float
 */
function cvm_video_size_ratio( $video_data ){
    if( isset( $video_data['size']['ratio'] ) ){
        $ratio = $video_data['size']['ratio'];
    }else{
        $ratio = false;
    }
    return $ratio;
}

/**
 * Utility function, returns plugin default settings
 */
function cvm_plugin_settings_defaults(){
	$defaults = array(
		'public'				=> true, // post type is public or not
		'archives'				=> false,
		'import_title' 			=> true, // import titles on custom posts
		'import_description' 	=> 'post_content', // import descriptions on custom posts
		'import_status'			=> 'draft', // default import status of videos
		'import_privacy'        => 'pending', // private videos will be imported with this status
        'import_date'           => false, // import video publish date
		'import_tags'           => false,
		'max_tags'              => 1,
        'vimeo_consumer_key'	=> '',
		'vimeo_secret_key'		=> '',
		'oauth_token'			=> '',// retrieved from Vimeo
	);
	return $defaults;
}

/**
 * Utility function, returns plugin settings
 */
function cvm_get_settings(){
	$defaults = cvm_plugin_settings_defaults();
	$option = get_option('_cvm_plugin_settings', $defaults);
	
	foreach( $defaults as $k => $v ){
		if( !isset( $option[ $k ] ) ){
			$option[ $k ] = $v;
		}
	}
	
	return $option;
}

/**
 * Utility function, updates plugin settings
 */
function cvm_update_settings(){	
	$defaults = cvm_plugin_settings_defaults();
	foreach( $defaults as $key => $val ){
		if( is_numeric( $val ) ){
			if( isset( $_POST[ $key ] ) ){
				$defaults[ $key ] = (int)$_POST[ $key ];
			}
			continue;
		}
		if( is_bool( $val ) ){
			$defaults[ $key ] = isset( $_POST[ $key ] );
			continue;
		}
		
		if( isset( $_POST[ $key ] ) ){
			$defaults[ $key ] = $_POST[ $key ];
		}
	}
	// current settings
	$plugin_settings = cvm_get_settings();
	// reset oauth if user changes the keys
	if( isset( $_POST['vimeo_consumer_key'] ) && isset( $_POST['vimeo_secret_key'] ) ){
		if( 
			($_POST['vimeo_consumer_key'] != $plugin_settings['vimeo_consumer_key']) || 
			($_POST['vimeo_secret_key'] != $plugin_settings['vimeo_secret_key'] ) 
		){
			$defaults['oauth_token'] = '';
		}		
	}else {
		// if the consumer keys are not sent by POST, set the old values
		$defaults['vimeo_consumer_key'] = $plugin_settings['vimeo_consumer_key'];
		$defaults['vimeo_secret_key']   = $plugin_settings['vimeo_secret_key'];
	}
	
	update_option('_cvm_plugin_settings', $defaults);
}

/**
 * Global player settings defaults.
 */
function cvm_player_settings_defaults(){
	$defaults = array(
		'title'		=> 1,
		'byline' 	=> 1, // show player controls. Values: 0 or 1
		'portrait' 	=> 1, // 0 - always show controls; 1 - hide controls when playing; 2 - hide progress bar when playing
		'color'		=> '', // 0 - fullscreen button hidden; 1 - fullscreen button displayed
		'fullscreen'=> 1,	
	
		'autoplay'	=> 0, // 0 - on load, player won't play video; 1 - on load player plays video automatically
		//'loop'		=> 0, // 0 - video won't start again once finished; 1 - video will play again once finished

		// extra settings
		'aspect_ratio'		=> '16x9',
		'aspect_override'   => true,
		'width'				=> 640,
		'video_position' 	=> 'below-content', // in front-end custom post, where to display the video: above or below post content
		'volume'			=> 25, // video default volume
		'js_embed'        => true,
	);
	return $defaults;
}

/**
 * Get general player settings
 */
function cvm_get_player_settings(){
	$defaults 	= cvm_player_settings_defaults();
	$option 	= get_option('_cvm_player_settings', $defaults);
	
	foreach( $defaults as $k => $v ){
		if( !isset( $option[ $k ] ) ){
			$option[ $k ] = $v;
		}
	}
	
	// various player outputs may set their own player settings. Return those.
	global $CVM_PLAYER_SETTINGS;
	if( $CVM_PLAYER_SETTINGS ){
		foreach( $option as $k => $v ){
			if( isset( $CVM_PLAYER_SETTINGS[$k] ) ){
				$option[$k] = $CVM_PLAYER_SETTINGS[$k];
			}
		}
	}
	
	return $option;
}

/**
 * Update general player settings
 */
function cvm_update_player_settings(){
	$defaults = cvm_player_settings_defaults();
	foreach( $defaults as $key => $val ){
		if( is_numeric( $val ) ){
			if( isset( $_POST[ $key ] ) ){
				$defaults[ $key ] = (int)$_POST[ $key ];
			}else{
				$defaults[ $key ] = 0;
			}
			continue;
		}
		if( is_bool( $val ) ){
			$defaults[ $key ] = isset( $_POST[ $key ] );
			continue;
		}
		
		if( isset( $_POST[ $key ] ) ){
			$defaults[ $key ] = $_POST[ $key ];
		}
	}
	
	update_option('_cvm_player_settings', $defaults);	
}

/**
 * Displays checked argument in checkbox
 * @param bool $val
 * @param bool $echo
 */
function cvm_check( $val, $echo = true ){
	$checked = '';
	if( is_bool($val) && $val ){
		$checked = ' checked="checked"';
	}
	if( $echo ){
		echo $checked;
	}else{
		return $checked;
	}	
}

/**
 * Displays a style="display:hidden;" if passed $val is bool and false
 * @param bool $val
 * @param string $before
 * @param string $after
 * @param bool $echo
 */
function cvm_hide( $val, $compare = false, $before=' style="', $after = '"', $echo = true ){
	$output = '';
	if(  $val == $compare ){
		$output .= $before.'display:none;'.$after;
	}
	if( $echo ){
		echo $output;
	}else{
		return $output;
	}
}

/**
 * Display select box
 * @param array $args - see $defaults in function
 * @param bool $echo
 */
function cvm_select( $args = array(), $echo = true ){
	
	$defaults = array(
		'options' 	=> array(),
		'name'		=> false,
		'id'		=> false,
		'class'		=> '',
		'selected'	=> false,
		'use_keys'	=> true,
		'before'	=> '',
		'after'		=> '',
        'disabled'  => false
	);
	
	$o = wp_parse_args($args, $defaults);
	$disabled = $o['disabled'] ? ' disabled="disabled" ' : '';

	if( !$o['id'] ){
		$output = sprintf( '<select name="%1$s" id="%1$s" class="%2$s" %3$s>', $o['name'], $o['class'], $disabled);
	}else{
		$output = sprintf( '<select name="%1$s" id="%2$s" class="%3$s" %3$s>', $o['name'], $o['id'], $o['class'], $disabled);
	}	
	
	foreach( $o['options'] as $val => $text ){
		$opt = '<option value="%1$s" title="%4$s"%2$s>%3$s</option>';
		
		if( is_array( $text ) ){
			$title = $text['title'];
			$text = $text['text'];			
		}else{
			$title = '';
		}
		
		$value = $o['use_keys'] ? $val : $text;
		$c = $o['use_keys'] ? $val == $o['selected'] : $text == $o['selected'];
		$checked = $c ? ' selected="selected"' : '';		
		$output .= sprintf($opt, $value, $checked, $text, $title);		
	}
	
	$output .= '</select>';
	
	if( $echo ){
		echo $o['before'].$output.$o['after'];
	}
	
	return $o['before'].$output.$o['after'];
}

/**
 * Returns a select box containing the options for aspect ratio
 *
 * @param array $args
 * @param bool $echo
 *
 * @return null/html
 */
function cvm_aspect_ratio_select( $args = array(), $echo = true ) {

	$defaults     = array(
		'name'     => false,
		'id'       => false,
		'class'    => '',
		'selected' => false,
		'before'   => '',
		'after'    => ''
	);
	$o            = wp_parse_args( $args, $defaults );
	$o['options'] = array(
		'4x3'    => '4:3',
		'16x9'   => '16:9',
		'2.35x1' => '2,35:1'
	);

	$select = cvm_select( $o, false );
	if ( $echo ) {
		echo $select;
	} else {
		return $select;
	}
}

/**
 * Returns post video data from meta
 *
 * @param int/post object $post_id
 * @return bool/array
 */
function cvm_get_post_video_data( $post_id ){
    if( !$post_id ){
        return false;
    }
    if( !is_numeric( $post_id ) ){
        $post_id = $post_id->ID;
    }

    $meta_key = '__cvm_video_data';
    return get_post_meta( $post_id, $meta_key, true );
}

/**
 * Calculate player height from given aspect ratio and width
 * @param string $aspect_ratio
 * @param int $width
 */
function cvm_player_height( $aspect_ratio, $width, $ratio = false ){
	$width = absint($width);

	if ( is_numeric( $ratio ) && $ratio > 0 ) {
		return floor( $width / $ratio );
	}

	switch( $aspect_ratio ){
		case '4x3':
			$height = ($width * 3) / 4;
		break;
		case '16x9':
		default:	
			$height = ($width * 9) / 16;
		break;
		case '2.35x1':
			$height = floor( $width / 2.35 );
		break;
	}
	return $height;
}

/**
 * Single post default settings
 */
function cvm_post_settings_defaults(){
	// general player settings
	$plugin_defaults = cvm_get_player_settings();	
	return $plugin_defaults;
}

/**
 * Set thumbnail as featured image for a given post ID
 *
 * @param int $post_id - id of post
 * @param bool $refresh - false: search attachments and if image found, return it; true: refresh from Vimeo
 *
 * @return array - post_id: id of post having attached image
 *                 - attachment_id: id of media image
 */
function cvm_import_featured_image( $post_id, $refresh = false ) {

	if ( ! $post_id ) {
		return false;
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return false;
	}

	$video_meta = cvm_get_post_video_data( $post_id );

	if ( ! $video_meta ) {
		return false;
	}

	if ( ! $refresh ) {
		// check if thumbnail was already imported
		$attachment = get_posts( array(
			'post_type'  => 'attachment',
			'meta_key'   => 'video_thumbnail',
			'meta_value' => $video_meta['video_id']
		) );
		// if thumbnail exists, return it
		if ( $attachment ) {
			// set image as featured for current post
			set_post_thumbnail( $post_id, $attachment[0]->ID );

			return array(
				'post_id'       => $post_id,
				'attachment_id' => $attachment[0]->ID
			);
		}
	}

	$image_url = end( $video_meta['thumbnails'] );
	$result    = cvm_add_featured_image( $post_id, $image_url, $video_meta['video_id'] );

	return $result;
}

/**
 * Sets the given image URL as featured image on a given post_id
 *
 * @param int $post_id - ID of post to set featured image on
 * @param string $image_url - url for image
 * @param string $video_id - Vimeo video ID
 */
function cvm_add_featured_image( $post_id, $image_url, $video_id ) {
	// get the thumbnail
	$response = wp_remote_get(
		$image_url,
		array(
			'sslverify' => false,
			/**
			 * Request timeout filter
			 * @var int
			 */
			'timeout'   => apply_filters( 'cvm_image_request_timeout', 15 )
		)
	);

	if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	$image_contents = $response['body'];
	$image_type     = wp_remote_retrieve_header( $response, 'content-type' );
	// Translate MIME type into an extension
	if ( $image_type == 'image/jpeg' ) {
		$image_extension = '.jpg';
	} elseif ( $image_type == 'image/png' ) {
		$image_extension = '.png';
	}

	// Construct a file name using post slug and extension
	$fname        = urldecode( basename( get_permalink( $post_id ) ) );
	$new_filename = preg_replace( '/[^A-Za-z0-9\-]/', '', $fname ) . '-vimeo-thumbnail' . $image_extension;

	// Save the image bits using the new filename
	$upload = wp_upload_bits( $new_filename, null, $image_contents );
	if ( $upload['error'] ) {
		return false;
	}

	$image_url = $upload['url'];
	$filename  = $upload['file'];

	$wp_filetype = wp_check_filetype( basename( $filename ), null );
	$attachment  = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title'     => get_the_title( $post_id ) . ' - Vimeo thumbnail',
		'post_content'   => '',
		'post_status'    => 'inherit',
		'guid'           => $image_url
	);
	$attach_id   = wp_insert_attachment( $attachment, $filename, $post_id );
	// you must first include the image.php file
	// for the function wp_generate_attachment_metadata() to work
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	// Add field to mark image as a video thumbnail
	update_post_meta( $attach_id, 'video_thumbnail', $video_id );

	// set image as featured for current post
	update_post_meta( $post_id, '_thumbnail_id', $attach_id );

	/**
	 * Trigger action on plugin import
	 */
	do_action( 'cvm_import_video_thumbnail', $attach_id, $video_id, $post_id );

	return array(
		'post_id'       => $post_id,
		'attachment_id' => $attach_id
	);
}

/**
 * Returns playback settings set on a video post
 */
function cvm_get_video_settings( $post_id = false, $output = false ){
	global $CVM_POST_TYPE;
	if( !$post_id ){
		global $post;
		if( !$post || $CVM_POST_TYPE->get_post_type() !== $post->post_type ){
			return false;
		}
		$post_id = $post->ID;		
	}else{
		$post = get_post( $post_id );
		if( $CVM_POST_TYPE->get_post_type() !== $post->post_type ){
			return false;
		}
	}
	// stores default value as set in plugin settings
	$plugin_settings = cvm_post_settings_defaults();

	$defaults = cvm_post_settings_defaults();
	$option = get_post_meta( $post_id, '__cvm_playback_settings', true );
	if( !is_array( $option ) ){
	    $option = array();
    }

	if( !$option ){
		$option = $defaults;
	}else{
		foreach( $defaults as $k => $v ){
			if( !isset( $option[ $k ] ) ){
				$option[ $k ] = $v;
			}
		}
    }

	// the options that should be preserved from main plugin settings
	$get_from_main_settings = array( 'aspect_override' );

	// overwrite defaults with post options
	foreach ( $plugin_settings as $k => $v ) {
		if ( in_array( $k, $get_from_main_settings ) || ! isset( $option[ $k ] ) ) {
			$option[ $k ] = $v;
		}
	}

	if ( $output ) {
		foreach ( $option as $k => $v ) {
			if ( is_bool( $v ) ) {
				$option[ $k ] = absint( $v );
			}
		}
	}

	if ( isset( $option['aspect_override'] ) && $option['aspect_override'] ) {
		$video_data = get_post_meta($post->ID, '__cvm_video_data', true);
		$option['size_ratio'] = cvm_video_size_ratio( $video_data );
	}else{
		$option['size_ratio'] = false;
    }

	return $option;	
}

/**
 * Utility function, updates video settings
 */
function cvm_update_video_settings( $post_id ){
	
	if( !$post_id ){
		return false;
	}
	
	global $CVM_POST_TYPE;
	$post = get_post( $post_id );
	if( $CVM_POST_TYPE->get_post_type() !== $post->post_type ){
		return false;
	}
	
	$defaults = cvm_post_settings_defaults();
	foreach( $defaults as $key => $val ){
		if( is_numeric( $val ) ){
			if( isset( $_POST[ $key ] ) ){
				$defaults[ $key ] = (int)$_POST[ $key ];
			}else{
				$defaults[ $key ] = 0;
			}
			continue;
		}
		if( is_bool( $val ) ){
			$defaults[ $key ] = isset( $_POST[ $key ] );
			continue;
		}
		
		if( isset( $_POST[ $key ] ) ){
			$defaults[ $key ] = $_POST[ $key ];
		}
	}
	
	update_post_meta($post_id, '__cvm_playback_settings', $defaults);	
}


/**
 * Register widgets.
 */
function cvm_load_widgets() {
	// check if posts are public
	$options = cvm_get_settings();
	if( !isset( $options['public'] ) || !$options['public'] ){
		return;
	}
		
	include CVM_PATH.'includes/libs/video-widgets.class.php';
	register_widget( 'CVM_Latest_Videos_Widget' );
	register_widget( 'CVM_Video_Categories_Widget' );
	
}
add_action( 'widgets_init', 'cvm_load_widgets' );

/**
 * TinyMce
 */
function cvm_tinymce_buttons(){
	// Don't bother doing this stuff if the current user lacks permissions
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;
 	
	// Don't load unless is post editing (includes post, page and any custom posts set)
	$screen = get_current_screen();
	global $CVM_POST_TYPE;
	if( !$screen || 'post' != $screen->base || $CVM_POST_TYPE->get_post_type() == $screen->post_type ){
		return;
	}  
	
	// Add only in Rich Editor mode
	if ( get_user_option('rich_editing') == 'true') {
   		
		wp_enqueue_script(array(
			'jquery-ui-dialog'
		));
			
		wp_enqueue_style(array(
			'wp-jquery-ui-dialog'
		));
   	
	    add_filter('mce_external_plugins', 'cvm_tinymce_plugin');
	    add_filter('mce_buttons', 'cvm_register_buttons');
   }	
}

function cvm_register_buttons($buttons) {	
	array_push($buttons, 'separator', 'cvm_shortcode');
	return $buttons;
}

// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function cvm_tinymce_plugin($plugin_array) {
	$plugin_array['cvm_shortcode'] = CVM_URL.'assets/back-end/js/tinymce/shortcode.js';
	return $plugin_array;
}

add_action('admin_head', 'cvm_tinymce_buttons');

function cvm_load_post_edit_styling(){
	global $post, $CVM_POST_TYPE;

	if( $post && $CVM_POST_TYPE->get_post_type() === $post->post_type ){
		// load only if not Gutenberg
	    if( !$CVM_POST_TYPE->is_gutenberg() ) {
			// video thumbnail functionality
			wp_enqueue_script( 'cvm-video-thumbnail', CVM_URL . 'assets/back-end/js/video-thumbnail.js', array(
				'jquery'
			), '1.0' );

			wp_localize_script( 'cvm-video-thumbnail', 'cvm_thumb_message', array(
				'loading'       => __( '... importing thumbnail', 'codeflavors-vimeo-video-post-lite' ),
				'still_loading' => __( '... hold on, still loading', 'codeflavors-vimeo-video-post-lite' )
			) );
		}
		wp_enqueue_style( 'cvm-video-thumbnail', CVM_URL . 'assets/back-end/css/video-thumbnail.css', false, '1.0' );
    }

	if( !$post || $CVM_POST_TYPE->get_post_type() == $post->post_type ){
		return;
	}
	
	wp_enqueue_style(
		'cvm-shortcode-modal',
		CVM_URL.'assets/back-end/css/shortcode-modal.css',
		false,
		'1.0'
	);
	
	wp_enqueue_script(
		'cvm-shortcode-modal',
		CVM_URL.'assets/back-end/js/shortcode-modal.js',
		false,
		'1.0'
	);
	
	wp_enqueue_script(
		'cvm-video-edit',
		CVM_URL.'assets/back-end/js/video-edit.js',
		array('jquery'),
		'1.0'
	);
	
	
	$messages = array(
		'playlist_title' => __('Videos in playlist', 'codeflavors-vimeo-video-post-lite'),
		'no_videos'		 => __('No videos selected.<br />To create a playlist check some videos from the list on the right.', 'codeflavors-vimeo-video-post-lite'),
		'deleteItem'	 => __('Delete from playlist', 'codeflavors-vimeo-video-post-lite'),
		'insert_playlist'=> __('Add shortcode into post', 'codeflavors-vimeo-video-post-lite')
	);
	
	wp_localize_script('cvm-shortcode-modal', 'CVM_SHORTCODE_MODAL', $messages);
}
add_action('admin_print_styles-post.php', 'cvm_load_post_edit_styling');
add_action('admin_print_styles-post-new.php', 'cvm_load_post_edit_styling');

/**
 * Modal window post edit shortcode output
 */
function cvm_post_edit_modal(){
	global $post, $CVM_POST_TYPE;
	if( !$post || $CVM_POST_TYPE->get_post_type() == $post->post_type ){
		return;
	}
	
	$options = cvm_get_player_settings();
	
	?>
	<div id="CVMVideo_Modal_Window" style="display:none;">	
		<div class="wrap">
			<div id="cvm-playlist-items">
				<div class="inside">
					<h2><?php _e('Playlist settings', 'codeflavors-vimeo-video-post-lite');?></h2>
					<div id="cvm-playlist-settings" class="cvm-player-settings-options">
						<table>
							<tr>
								<th><label for="cvm_playlist_theme"><?php _e('Theme', 'codeflavors-vimeo-video-post-lite');?>:</label></th>
								<td>
								<?php
								$playlist_themes = cvm_playlist_themes();
								cvm_select(array(
									'name' 		=> 'cvm_playlist_theme',
									'id' 		=> 'cvm_playlist_theme',
									'options' 	=> $playlist_themes
								));
								?>
								</td>
							</tr>
							<tr>
								<th><label for="cvm_aspect_ratio"><?php _e('Aspect', 'codeflavors-vimeo-video-post-lite');?>:</label></th>
								<td>
								<?php 
									$args = array(
										'options' 	=> array(
											'4x3' 	=> '4x3',
											'16x9' 	=> '16x9',
                                            '2.35x1' => '2.35x1'
										),
										'name' 		=> 'aspect_ratio',
										'id'		=> 'aspect_ratio',
										'class'		=> 'cvm_aspect_ratio'
									);
									cvm_select( $args );
								?>
								</td>
							</tr>
							
							<tr>
								<th><label for="width"><?php _e('Width', 'codeflavors-vimeo-video-post-lite');?>:</label></th>
								<td>
									<input type="text" class="cvm_width" name="width" id="width" value="<?php echo $options['width'];?>" size="2" />px
									| <?php _e('Height', 'codeflavors-vimeo-video-post-lite');?> : <span class="cvm_height" id="cvm_calc_height"><?php echo cvm_player_height( $options['aspect_ratio'], $options['width'] );?></span>px
								</td>
							</tr>
							
							<tr>
								<th><label for="volume"><?php _e('Volume', 'codeflavors-vimeo-video-post-lite');?></label>:</th>
								<td>
									<input type="text" name="volume" id="volume" value="<?php echo $options['volume'];?>" size="1" maxlength="3" />
									<label for="volume"><span class="description"><?php _e('number between 0 (mute) and 100 (max)', 'codeflavors-vimeo-video-post-lite');?></span></label>
								</td>
							</tr>
						</table>
						<input type="button" id="cvm-insert-playlist-shortcode" class="button primary" value="<?php _e('Insert playlist', 'codeflavors-vimeo-video-post-lite');?>" />						
					</div>
					
					<input type="hidden" name="cvm_selected_items"  value="" />
					<h2><?php _e('Videos in playlist', 'codeflavors-vimeo-video-post-lite');?></h2>
					
					<div id="cvm-list-items">
						<em><?php _e('No video selected', 'codeflavors-vimeo-video-post-lite');?><br /><?php _e('To create a playlist check some videos from the list on the right.', 'codeflavors-vimeo-video-post-lite');?></em>
					</div>
				</div>	
			</div>
			<div id="cvm-display-videos">
				<iframe src="edit.php?post_type=<?php echo $CVM_POST_TYPE->get_post_type();?>&page=cvm_videos" frameborder="0" width="100%" height="100%"></iframe>
			</div>
		</div>	
	</div>
	<?php	
}

add_action('admin_footer', 'cvm_post_edit_modal');

/**
 * Available player themes
 */
function cvm_playlist_themes(){
	return array(
		'default' 	=> __('Default theme', 'codeflavors-vimeo-video-post-lite'),
		'carousel' 	=> __('Carousel navigation (PRO)', 'codeflavors-vimeo-video-post-lite')
	);
}

/**
 * Enqueue some functionality scripts on widgets page
 */
function cvm_widgets_scripts(){
	$plugin_settings = cvm_get_settings();
	if( isset( $plugin_settings['public'] ) && !$plugin_settings['public'] ){
		return;
	}
}
add_action('admin_print_scripts-widgets.php', 'cvm_widgets_scripts');
/**
 * TEMPLATING
 */

/**
 * Outputs default player data
 */
function cvm_output_player_data( $echo = true ){
	$player = cvm_get_player_settings();
	$attributes = cvm_data_attributes( $player, $echo );	
	return $attributes;
}

/**
 * Output video parameters as data-* attributes
 * @param array $array - key=>value pairs
 * @param bool $echo
 */
function cvm_data_attributes( $attributes, $echo = false ){
    $result = array();
    // these variables are not needed by js and will be skipped
    $exclude = array('video_position', 'aspect_override');
    // loop attributes
    foreach( $attributes as $key=>$value ){
        // skip values from $exclude
        if( in_array( $key, $exclude ) ){
            continue;
        }
        $result[] = sprintf( 'data-%s="%s"', $key, $value );
    }
    if( $echo ){
        echo implode(' ', $result);
    }else{
        return implode(' ', $result);
    }
}

/**
 * Outputs the default player size
 * @param string $before
 * @param string $after
 * @param bool $echo
 */
function cvm_output_player_size( $before = ' style="', $after='"', $echo = true ){
	$player = cvm_get_player_settings();
	$height = cvm_player_height($player['aspect_ratio'], $player['width']);
	$output = 'width:'.$player['width'].'px; height:'.$height.'px;';
	if( $echo ){
		echo $before.$output.$after;
	}
	
	return $before.$output.$after;
}

/**
 * Output width according to player
 * @param string $before
 * @param string $after
 * @param bool $echo
 */
function cvm_output_width( $before = ' style="', $after='"', $echo = true ){
	$player = cvm_get_player_settings();
	if( $echo ){
		echo $before.'width: '.$player['width'].'px; '.$after;
	}
	return $before.'width: '.$player['width'].'px; '.$after;
}

/**
 * Output video thumbnail
 * @param string $before
 * @param string $after
 * @param bool $echo
 */
function cvm_output_thumbnail( $before = '', $after = '', $echo = true ){
	global $cvm_video;
	$output = '';
	
	if( isset( $cvm_video['video_data']['thumbnails'][0] ) ){
		$img_url = $cvm_video['video_data']['thumbnails'][0];
		if( is_ssl() ){
			$img_url = str_replace( 'http://' , 'https://', $img_url );
		}
		
		$output = sprintf( '<img src="%s" alt="" />', $img_url );
	}
	if( $echo ){
		echo $before.$output.$after;
	}
	
	return $before.$output.$after;
}

/**
 * Output video title
 * @param string $before
 * @param string $after
 * @param bool $echo
 */
function cvm_output_title( $include_duration = true,  $before = '', $after = '', $echo = true  ){
	global $cvm_video;
	$output = '';
	if( isset( $cvm_video['title'] ) ){
		$output = $cvm_video['title'];
	}
	
	if( $include_duration ){
		$output .= ' <span class="duration">['.cvm_human_time( $cvm_video['video_data']['duration'] ).']</span>';
	}
	
	if( $echo ){
		echo $before.$output.$after;
	}
	return $before.$output.$after;
}

/**
 * Outputs video data
 * @param string $before
 * @param string $after
 * @param bool $echo
 */
function cvm_output_video_data( $before = " ", $after="", $echo = true ){
	global $cvm_video;
	if( !$cvm_video ){
		_doing_it_wrong(__METHOD__, __('You should use this into a foreach() loop. Correct usage is: <br />foreach( $videos as $cvm_video ){ '.__METHOD__.'(); } '), '3.0');
		return false;
	}
	$video_settings = cvm_get_video_settings( $cvm_video['ID'] );	
	
	$video_id 		= $cvm_video['video_data']['video_id'];
	$data = array(
		'video_id' 	 => $video_id,
		'autoplay' 	 => $video_settings['autoplay'],
		'volume'  	 => $video_settings['volume'],
	);
	
	$output = cvm_data_attributes($data);
	if( $echo ){
		echo $before.$output.$after;
	}
	
	return $before.$output.$after;
}

function cvm_video_post_permalink( $echo  = true ){
	global $cvm_video;
	
	$pl = get_permalink( $cvm_video['ID'] );
	
	if( $echo ){
		echo $pl;
	}
	
	return $pl;
	
}

/**
 * Themes compatibility layer
 */

/**
 * Supported themes
 */
function cvm_supported_themes(){	
	$themes = array(
		'detube' => array(
			'theme_name' => 'DeTube'		
		),
		'Avada' => array(
			'theme_name' => 'Avada'
		)
	);
	return $themes;
}

/**
 * Check if theme is supported by the plugin.
 * Returns false or an array containing a mapping for custom post fields to store information on
 */
function cvm_check_theme_support(){
	
	$template 	= get_template();
	$themes 	= cvm_supported_themes();
	
	if( !array_key_exists($template, $themes) ){
		return false;
	}
	
	return $themes[$template];		
}

/**
 * Returns contextual help content from file
 * @param string $file - partial file name
 */
function cvm_get_contextual_help( $file ){
	if( !$file ){
		return false;
	}	
	$file_path = CVM_PATH. 'views/help/' . $file.'.html.php';
	if( is_file($file_path) ){
		ob_start();
		include( $file_path );
		$help_contents = ob_get_contents();
		ob_end_clean();		
		return $help_contents;
	}else{
		return false;
	}
}

/**
 * Returns video URL for a given video ID
 * @param string $video_id
 */
function cvm_video_url( $video_id ){
	return sprintf('https://vimeo.com/%s', $video_id);
}

/**
 * Returns embed code for a given video ID
 * @param string $video_id
 */
function cvm_video_embed( $video_id ){
	$options = cvm_get_player_settings();
	return sprintf( '<iframe src="https://player.vimeo.com/video/%s?title=%d&amp;byline=%d&amp;portrait=%d&amp;color=%s" width="%d" height="%d" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>',
		$video_id,
		$options['title'],
		$options['byline'],
		$options['portrait'],
		$options['color'],
		$options['width'],
		cvm_player_height($options['aspect_ratio'], $options['width'])
	);
}

/**
 * Outputs the HTML for embedding videos on single posts.
 *
 * @return string
 */
function cvm_video_embed_html(){

    global $post;
    if( !$post ){
        return;
    }

    $settings	= cvm_get_video_settings( $post->ID, true );
    $video		= cvm_get_post_video_data( $post->ID );

    if( !$video ){
        return;
    }

    $settings['video_id'] = $video['video_id'];

    // set ratio data from video details retrieved from Vimeo
    if( isset( $video['size']['ratio'] ) && ( isset( $settings['aspect_override'] ) && $settings['aspect_override'] ) ){
        $settings['size_ratio'] = $video['size']['ratio'];
    }else{
        $settings['size_ratio'] = false;
    }

    /**
     * Filter that can be used to modify the width of the embed
     * @var int
     */
    $width 	= apply_filters( 'cvm-embed-width', $settings['width'], $video, 'manual_embed' );
    $height = cvm_player_height( $settings['aspect_ratio'] , $width, $settings['size_ratio']);

    /**
     * Filter on video container CSS class to add extra CSS classes
     *
     * Name: cvm_video_post_css_class
     * Params: 	- an empty array
     * 			- the post object that will embed the video
     *
     * @var string
    */
    $class = apply_filters('cvm_video_post_css_class', array(), $post);
    $extra_css = implode(' ', (array)$class);

    $video_container = '<div class="cvm_single_video_player ' . $extra_css . '" ' . cvm_data_attributes( $settings ) . ' style="width:' . $width . 'px; height:' . $height.'px; max-width:100%;"><!--video container--></div>';

    // add player script
    cvm_enqueue_player();

    echo $video_container;
    return $video_container;
}

/**
 * Outputs any errors issued by Vimeo when importing videos
 * @param bool $echo
 * @param string $before
 * @param string $after
 */
function cvm_import_errors( $error, $echo = true, $before = '<div class="error"><p>', $after = '</p></div>' ){
	if( !is_wp_error( $error ) ){
		return;
	}
	
	// wp error message
	$code = 'cvm_wp_error';
	$message = $error->get_error_message( $code );
	if( $message ){		
		$output = __('WordPress encountered and error while trying to query Vimeo:', 'codeflavors-vimeo-video-post-lite'). '<br />' . '<strong>'.$message.'</strong></p>';		
		if( $echo ){
			echo $before.$output.$after;
		}		
		return $before.$output.$after;
	}
	
	// vimeo api errors
	$code = 'cvm_vimeo_query_error';	
	$message 	= $error->get_error_message( $code );
	$data		= $error->get_error_data( $code );
	
	$output = '<strong>'.$message.'</strong></p>';
	$output.= sprintf( __('Vimeo error code: %s (<em>%s</em>) - <strong>%s</strong>', 'codeflavors-vimeo-video-post-lite'), $data['code'], $data['msg'], $data['expl'] );
	
	if( 401 == $data['code'] ){
		$url = menu_page_url('cvm_settings', false).'#vimeo_consumer_key';
		$link = sprintf('<a href="%s">%s</a>', $url, __('Settings page', 'codeflavors-vimeo-video-post-lite'));		
		$output.= '<br /><br />' . sprintf(__('Please visit %s and enter your consumer and secret keys.', 'codeflavors-vimeo-video-post-lite'), $link);
	}
	
	if( $echo ){
		echo $before.$output.$after;
	}
	
	return $before.$output.$after;	
}

/**
 * Display update notices on plugin pages.
 */
function cvm_admin_messages(){
	global $CVM_POST_TYPE;
	if( !isset( $_GET['post_type'] ) || $CVM_POST_TYPE->get_post_type() != $_GET['post_type'] ){
		return;
	}
	
	$messages 	= array();
	$o 			= cvm_get_settings();
	
	if( empty( $o['vimeo_consumer_key'] ) || empty( $o['vimeo_secret_key'] ) ){
		$messages[] = 'In order to be able to bulk import videos using Vimeo videos plugin, you must register on <a href="https://developer.vimeo.com/apps/new">Vimeo App page</a>.<br />
					   Please note that you must have a valid Vimeo account and also you must be logged into Vimeo before being able to register your app.<br />
					   After you registered your app, please visit <a href="'.menu_page_url('cvm_settings', false).'#cvm_vimeo_keys">Settings page</a> and enter your Vimeo consumer and secret keys.';
	}
	
	if( $messages ){
		echo '<div class="update-nag"><span>'.implode('</span><hr /><span>', $messages).'</span></div>';
	}
}
add_action('all_admin_notices', 'cvm_admin_messages');

/**
 * @param $path
 * @param string $medium
 *
 * @return string
 */
function cvm_link( $path, $medium = 'doc_link' ) {
	$base = 'https://vimeotheque.com/';
	$vars = array(
		'utm_source'   => 'plugin',
		'utm_medium'   => $medium,
		'utm_campaign' => 'vimeo-lite-plugin'
	);
	$q    = http_build_query( $vars );

	return trailingslashit( $base . $path ) . '?' . $q;
}

function cvm_docs_link( $path ) {
	return cvm_link( 'documentation/' . trailingslashit( $path ), 'doc_link' );
}

/**
 * A simple debug function. Doesn't do anything special, only triggers an
 * action that passes the information along the way.
 * For actual debug messages, extra functions that process and hook to this action
 * are needed.
 */
function _cvm_debug_message( $message, $separator = "\n", $data = false ){
	/**
	 * Fires a debug message action
	 */
	do_action( 'cvm_debug_message', $message, $separator, $data );
}