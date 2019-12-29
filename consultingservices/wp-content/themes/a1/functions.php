<?php
if (!function_exists('a1_setup')) :
   function a1_setup() {
       global $content_width;
       if (!isset($content_width)) {
           $content_width = 770;
       }
		/*
		 * Make A1 theme available for translation.
		 */
		load_theme_textdomain( 'a1', get_template_directory() . '/languages' );
       // This theme styles the visual editor to resemble the theme style.
       add_editor_style(array('css/editor-style.css', a1_font_url()));
       // Add RSS feed links to <head> for posts and comments.
       add_theme_support('automatic-feed-links');
       add_theme_support( 'title-tag' );
       add_theme_support('post-thumbnails');
       set_post_thumbnail_size(672, 372, true);
       add_image_size('a1-full-width', 1038, 576, true);
		add_image_size('a1-portfolio-image', 320, 260, true);
       // This theme uses wp_nav_menu() in two locations.
       register_nav_menus(array(
           'primary' => __('Header Menu', 'a1'),
           'secondary' => __('Footer Menu', 'a1'),
       ));
  	//custom background
  	add_theme_support( 'custom-background', apply_filters( 'a1_custom_background_args', array(
  			'default-color' => 'f5f5f5',
  		) ) );
    add_theme_support( 'custom-header', apply_filters( 'a1_custom_header_args', array(
        'uploads'       => true,
        'flex-height'   => true,
        'default-text-color' => '#000',
        'header-text' => true,
        'height' => '120',
        'width'  => '1260'
        ) ) );
    add_theme_support( 'custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
        'priority' => 11,     
        'header-text' => array('img-responsive-logo', 'site-description-logo'),
    ) );
       /*
	   * Switch default core markup for search form, comment form, and comments
	   * to output valid HTML5.
	   */
       add_theme_support('html5', array(
           'search-form', 'comment-form', 'comment-list',
       ));
       // Add support for featured content.
       add_theme_support('featured-content', array(
           'featured_content_filter' => 'a1_get_featured_posts',
           'max_posts' => 6,
       ));
	   // This theme uses its own gallery styles.
		add_filter('use_default_gallery_style', '__return_false');   }
endif;
// a1_setup
add_action('after_setup_theme', 'a1_setup');
/*Title*/
function a1_wp_title( $title, $sep ) {
	global $paged, $page;
	if ( is_feed() ) { return $title; } // end if
	// Add the site name.
	$title .= get_bloginfo( 'name' );
	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title = "$title $sep $site_description";
	} // end if
	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 ) {
		$title = sprintf(/* translators: %s is count.*/ __( 'Page %s', 'a1' ), max( $paged, $page ) ) . " $sep $title";
	} // end if
	return $title;
} // end blogim_wp_title
add_filter( 'wp_title', 'a1_wp_title', 10, 2 );

/** Register Lato Google font for a1.*/
function a1_font_url() {
   $a1_font_url = '';
   if ('off' !== _x('on', 'Open Sans font: on or off', 'a1')) {
       $a1_font_url = add_query_arg('family', urlencode('Open Sans:300,400,700,900,300italic,400italic,700italic'), "//fonts.googleapis.com/css");
   }
   return $a1_font_url;
}
// thumbnail list
function a1_thumbnail_image($content) {
   if (has_post_thumbnail())
       return the_post_thumbnail('thumbnail');
}
/*
* Set Theme Option variable as a global
*/
$a1_options = get_option('a1_theme_options');
global $a1_options;
/** Register widget areas.*/
function a1_widgets_init() {
   register_sidebar(array(
       'name' => __('Primary Sidebar', 'a1'),
       'id' => 'sidebar-1',
       'description' => __('Main sidebar that appears on the right.', 'a1'),
       'before_widget' => '<aside id="%1$s" class="widget %2$s">',
       'after_widget' => '</aside>',
       'before_title' => '<h3 class="sidebar-title">',
       'after_title' => '</h3>',
   ));
   register_sidebar(array(
       'name' => __('Footer Area One', 'a1'),
       'id' => 'footer-1',
       'description' => __('Footer Area One that appears on the footer.', 'a1'),
       'before_widget' => '<aside id="%1$s" class="widget %2$s">',
       'after_widget' => '</aside>',
       'before_title' => '<h3 class="widget-title">',
       'after_title' => '</h3>',
   ));
   register_sidebar(array(
       'name' => __('Footer Area Two', 'a1'),
       'id' => 'footer-2',
       'description' => __('Footer Area Two that appears on the footer.', 'a1'),
       'before_widget' => '<aside id="%1$s" class="widget %2$s">',
       'after_widget' => '</aside>',
       'before_title' => '<h3 class="widget-title">',
       'after_title' => '</h3>',
   ));
   register_sidebar(array(
       'name' => __('Footer Area Three', 'a1'),
       'id' => 'footer-3',
       'description' => __('Footer Area Three that appears on the footer.', 'a1'),
       'before_widget' => '<aside id="%1$s" class="widget %2$s">',
       'after_widget' => '</aside>',
       'before_title' => '<h3 class="widget-title">',
       'after_title' => '</h3>',
   ));
   register_sidebar(array(
       'name' => __('Footer Area Four', 'a1'),
       'id' => 'footer-4',
       'description' => __('Footer Area Four that appears on the footer.', 'a1'),
       'before_widget' => '<aside id="%1$s" class="widget %2$s">',
       'after_widget' => '</aside>',
       'before_title' => '<h3 class="widget-title">',
       'after_title' => '</h3>',
   ));
}
add_action('widgets_init', 'a1_widgets_init');
/**Remove ? from JS and CSS**/
function remove_cssjs_ver( $src ) {
    if( strpos( $src, '?ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'remove_cssjs_ver', 10, 2 );
add_filter( 'script_loader_src', 'remove_cssjs_ver', 10, 2 );
/* Add default menu style if menu is not set from the backend.*/
function a1_add_menuid($page_markup) {
   preg_match('/^<div class=\"([a-z0-9-_]+)\">/i', $page_markup, $a1_matches);
   $a1_divclass = '';
   if (!empty($a1_matches)) {
       $a1_divclass = $a1_matches[1];
   }
   $a1_toreplace = array('<div class="' . $a1_divclass . ' pull-right-res">', '</div>');
   $a1_replace = array('<div class="navbar-collapse collapse ">', '</div>');
   $a1_new_markup = str_replace($a1_toreplace, $a1_replace, $page_markup);
   $a1_new_markup = preg_replace('/<ul/', '<ul class="a1-menu"', $a1_new_markup);
   return $a1_new_markup;
}
add_filter('wp_page_menu', 'a1_add_menuid');
function a1_excerpt_more() {
   return '...</p><a href="' . esc_url(get_permalink()) . '" class="read-button">'.esc_html__('Read more','a1').'</a>';
}
add_filter("excerpt_more", "a1_excerpt_more");
if (!function_exists('a1_entry_meta')) :
/**
* Set up post entry meta.
* Meta information for current post: categories, tags, permalink, author, and date.
**/
   function a1_entry_meta() {
		$a1_options = get_option( 'a1_theme_options' );

		if(get_theme_mod ( 'a1_blog_entry_meta_by',$a1_options['entry-meta-by']) !='' ) { $a1_by_text = get_theme_mod ( 'a1_blog_entry_meta_by',$a1_options['entry-meta-by']); } else { $a1_by_text = __('by','a1'); }
		
		if(get_theme_mod ( 'a1_blog_entry_meta_on',$a1_options['entry-meta-on'])!='' ) { $a1_on_text = get_theme_mod ( 'a1_blog_entry_meta_on',$a1_options['entry-meta-on']); } else { $a1_on_text = __('On','a1'); }
		
		if(get_theme_mod ( 'a1_blog_entry_meta_tags',$a1_options['entry-meta-tags'])!='') { $a1_tags_text = get_theme_mod ( 'a1_blog_entry_meta_tags',$a1_options['entry-meta-tags']); } else { $a1_tags_text = __('Tags','a1'); }

       $a1_date = sprintf('<li>'.$a1_on_text.' <a href="%1$s" title="%2$s"><time datetime="%3$s">%4$s</time></a></li>', esc_url(get_day_link(get_post_time('Y'), get_post_time('m'), get_post_time('j'))), esc_attr(get_the_time()), esc_attr(get_the_date('c')), esc_html(get_the_date('M d,Y'))       );
       $a1_author = sprintf('<li>'.$a1_by_text.': <a href="%1$s" title="%2$s" >%3$s</a></li>', esc_url(get_author_posts_url(get_the_author_meta('ID'))), esc_attr(ucwords(get_the_author())), esc_attr(ucwords(get_the_author())));     

       $a1_tag_list = sprintf('%1$s</li>', get_the_tag_list( '<li>'.$a1_tags_text.': ', ' , '));	   

       printf('%1$s %2$s', $a1_author, $a1_date);
   }
endif;
//fetch title
function a1_title() {
   if (is_category() || is_single()) {
       if (is_category())
           the_category();
       if (is_single())
           the_title();
   }
   elseif (is_page())
       the_title();
   elseif (is_search())
       echo the_search_query();
}

add_filter('template_redirect','a1_frontpage_contact_form_submit');
function a1_frontpage_contact_form_submit(){  
  $a1_options = get_option( 'a1_theme_options' );
  if(isset($_POST['a1_submit']) ) {  
    $a1_product_email = sanitize_email(get_theme_mod ( 'a1_homepage_second_section_email',$a1_options['product-form-email']));    
    $to = $a1_product_email;
    $subject = 'Your Subject';
    $headers = "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $headers .= 'To: "'.$a1_product_email.'"';
    $message = '<table style="width:100%;border:1px solid #ddd;">';
    if(isset($_POST['a1_name'])):
      $message .= '<tr><td style="width:150px;border:1px solid #ddd;">Name :</td><td style="border:1px solid #ddd;"">'.sanitize_text_field( wp_unslash($_POST['a1_name'])).'</td></tr>';
    endif;
    if(isset($_POST['a1_email'])):
     $message .= ' <tr><td style="width:150px;border:1px solid #ddd;">E-mail :</td><td style="border:1px solid #ddd;"">'.sanitize_email( wp_unslash($_POST['a1_email'])).'</td></tr>';
    endif;
    if(isset($_POST['a1_phone'])):
      $message .= '<tr><td style="width:150px;border:1px solid #ddd;">Phone :</td><td style="border:1px solid #ddd;"">'.sanitize_text_field( wp_unslash($_POST['a1_phone'])).'</td></tr>';
    endif;
    if(isset($_POST['a1_message'])):
      $message .= '<tr><td style="width:150px;border:1px solid #ddd;">Message :</td><td style="border:1px solid #ddd;"">'.wp_kses_post( wp_unslash($_POST['a1_message'])).'</td></tr>';
    endif;
    $message .='</table>';
    $mail_sent = wp_mail( $to, $subject, $message, $headers );
  }

}

add_action( 'admin_menu', 'a1_admin_menu');
function a1_admin_menu( ) {
    add_theme_page( __('Pro Feature','a1'), __('A1 Pro','a1'), 'manage_options', 'a1-pro-buynow', 'a1_buy_now', 300 );   
}
function a1_buy_now(){ ?>
<div class="a1_pro_version">
  <a href="<?php echo esc_url('https://fasterthemes.com/wordpress-themes/a1pro/'); ?>" target="_blank">
    
    <img src ="<?php echo esc_url(get_template_directory_uri()); ?>/images/a1_pro_features.png" width="70%" height="auto" />

  </a>
</div>
<?php
}

add_filter('get_custom_logo','a1_change_logo_class');
function a1_change_logo_class($html)
{
  $html = str_replace('class="custom-logo"', 'class="img-responsive logo-fixed"', $html);
  $html = str_replace('width=', 'original-width=', $html);
  $html = str_replace('height=', 'original-height=', $html);
  $html = str_replace('class="custom-logo-link"', 'class="img-responsive logo-fixed"', $html);
  return $html;
}
// retrieves the attachment ID from the file URL
function a1_get_image_id($image_url) {
    global $wpdb;
    $legal_attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )); 
        return (empty($legal_attachment))?0:$legal_attachment[0]; 
}

/***** Theme function files ******/
require get_template_directory() . "/inc/customizer.php";
require get_template_directory() . "/inc/enqueue_script.php";
require get_template_directory() . "/inc/breadcrumbs.php";
