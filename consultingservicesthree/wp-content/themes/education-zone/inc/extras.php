<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Education_Zone
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
if ( ! function_exists( 'education_zone_body_classes' ) ) :

function education_zone_body_classes( $classes ) {
	global $post;

    $page_class  = education_zone_sidebar_layout_class();
    $ed_banner   = get_theme_mod( 'education_zone_ed_slider_section' );

	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

    if( ! $ed_banner ){
        $classes[] = 'no-banner';
    }
	
    // Adds a class of custom-background-image to sites with a custom background image.
	if ( get_background_image() ) {
		$classes[] = 'custom-background-image';
	}
    
    // Adds a class of custom-background-color to sites with a custom background color.
    if ( get_background_color() != 'ffffff' ) {
		$classes[] = 'custom-background-color';
	}
    
    if( !is_active_sidebar( 'right-sidebar' ) || is_page_template( 'template-home.php' ) || $page_class == 'no-sidebar' ){
        $classes[] = 'full-width';
    }

    if( education_zone_is_woocommerce_activated() && ( is_shop() || is_product_category() || is_product_tag() || 'product' === get_post_type() ) && ! is_active_sidebar( 'shop-sidebar' ) ){
        $classes[] = 'full-width';
    }
 
	return $classes;
}
endif;
add_filter( 'body_class', 'education_zone_body_classes' );

if( ! function_exists( 'education_zone_header' ) ):
/**
 * Page Header 
*/
function education_zone_header(){
    if( ! ( is_front_page() || is_page_template('template-home.php') ) ){
    ?>
    <div class="page-header">
		<div class="container">
	        
            <?php
            /** For Woocommerce */
            if( education_zone_is_woocommerce_activated() && ( is_product_category() || is_product_tag() || is_shop() ) ){
                if( is_shop() ){
                    if( get_option( 'page_on_front' ) == wc_get_page_id( 'shop' ) ) {
                        return;
                    }

                    $_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
                    if( ! $_name ){
                        $product_post_type = get_post_type_object( 'product' );
                        $_name = $product_post_type->labels->singular_name; ?>
                        <h1 class="page-title"><?php echo esc_html( $_name ); ?></h1>
                    <?php 
                    } 

                }elseif( is_product_category() || is_product_tag() ){
                    $current_term = $GLOBALS['wp_query']->get_queried_object(); ?>
                    <h1 class="page-title"><?php echo esc_html( $current_term->name ); ?> </h1>
                <?php 
                } 
            }else{
                if( is_archive() ){ ?> 
                    <h1 class="page-title"> <?php the_archive_title(); ?> </h1>
                <?php 
                }
            } 
            
            if( is_search() ){ 
                global $wp_query;    
                ?>
                <h1 class="page-title"><?php printf( esc_html__( '%1$s Result for "%2$s"', 'education-zone' ), $wp_query->found_posts, get_search_query() ); ?></h1>        		
                <?php                
            }
            
            if( is_home() ){ ?>
                <h1 class="page-title"><?php single_post_title(); ?></h1>
            <?php 
            }
            
            if( is_page() ){
                the_title( '<h1 class="page-title">', '</h1>' );
            }
            
            if( is_404() ){ ?>
                <h1 class="page-title"><?php echo esc_html__( '404 Error - Page not Found', 'education-zone' ); ?></h1>
            <?php                
            }
       	
           do_action( 'education_zone_breadcrumbs' ); ?>
        
		</div>
	</div>
<?php
    }
}
endif;
add_action( 'education_zone_page_header', 'education_zone_header' );
        
if( !function_exists( 'education_zone_breadcrumbs_cb' ) ):
/**
 * Breadcrumb
*/
function education_zone_breadcrumbs_cb(){
  
    $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
    $delimiter = esc_html( get_theme_mod( 'education_zone_breadcrumb_separator', __( '>', 'education-zone' ) ) ); // delimiter between crumbs
    $home = esc_html( get_theme_mod( 'education_zone_breadcrumb_home_text', __( 'Home', 'education-zone' ) ) ); // text for the 'Home' link
    $showCurrent = get_theme_mod( 'education_zone_ed_current', '1' ); // 1 - show current post/page title in breadcrumbs, 0 - don't show
    $before = '<span class="current">'; // tag before the current crumb
    $after = '</span>'; // tag after the current crumb
    
    global $post;
    $homeLink = esc_url( home_url( ) );
    
    if( get_theme_mod( 'education_zone_ed_breadcrumb' ) ){
        if ( is_front_page() ) {
        
            if ( $showOnHome == 1 ) echo '<div id="crumbs"><a href="' . esc_url( $homeLink ) . '">' . esc_html( $home ) . '</a></div>';
        
        } else {
        
             echo '<div id="crumbs"><a href="' . esc_url( $homeLink ) . '">' . esc_html( $home ) . '</a> <span class="separator">' . esc_html( $delimiter ) . '</span> ';
        
            if ( is_category() ) {
                $thisCat = get_category( get_query_var( 'cat' ), false );
                if ( $thisCat->parent != 0 ) echo get_category_parents( $thisCat->parent, TRUE, ' <span class="separator">' . $delimiter . '</span> ' );
                echo $before .  esc_html( single_cat_title( '', false ) ) . $after;
            
            } elseif( education_zone_is_woocommerce_activated() && ( is_product_category() || is_product_tag() ) ){ //For Woocommerce archive page
        
                $current_term = $GLOBALS['wp_query']->get_queried_object();
                if( is_product_category() ){
                    $ancestors = get_ancestors( $current_term->term_id, 'product_cat' );
                    $ancestors = array_reverse( $ancestors );
                    foreach ( $ancestors as $ancestor ) {
                        $ancestor = get_term( $ancestor, 'product_cat' );    
                        if ( ! is_wp_error( $ancestor ) && $ancestor ) {
                            echo ' <a href="' . esc_url( get_term_link( $ancestor ) ) . '">' . esc_html( $ancestor->name ) . '</a> <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                        }
                    }
                }           
                echo $before . esc_html( $current_term->name ) . $after;
                
            } elseif( education_zone_is_woocommerce_activated() && is_shop() ){ //Shop Archive page
                if ( get_option( 'page_on_front' ) == wc_get_page_id( 'shop' ) ) {
                    return;
                }
                $_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
        
                if ( ! $_name ) {
                    $product_post_type = get_post_type_object( 'product' );
                    $_name = $product_post_type->labels->singular_name;
                }
                echo $before . esc_html( $_name ) . $after;
                
            } elseif ( is_search() ) {
                echo $before . esc_html__( 'Search Results for "', 'education-zone' ) . esc_html( get_search_query() ) . esc_html__( '"', 'education-zone' ) . $after;
            
            } elseif ( is_day() ) {
                echo '<a href="' . esc_url( get_year_link( get_the_time( __( 'Y', 'education-zone' ) ) ) ) . '">' . esc_html( get_the_time( __( 'Y', 'education-zone' ) ) ) . '</a> <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                echo '<a href="' . esc_url( get_month_link( get_the_time( __( 'Y', 'education-zone' ) ), get_the_time( __( 'm', 'education-zone' ) ) ) ) . '">' . esc_html( get_the_time( __( 'F', 'education-zone' ) ) ) . '</a> <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                echo $before . esc_html( get_the_time( __( 'd', 'education-zone' ) ) ) . $after;
            
            } elseif ( is_month() ) {
                echo '<a href="' . esc_url( get_year_link( get_the_time( __( 'Y', 'education-zone' ) ) ) ) . '">' . esc_html( get_the_time( __( 'Y', 'education-zone' ) ) ) . '</a> <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                echo $before . esc_html( get_the_time( __( 'F', 'education-zone' ) ) ) . $after;
            
            } elseif ( is_year() ) {
                echo $before . esc_html( get_the_time( __( 'Y', 'education-zone' ) ) ) . $after;
        
            } elseif ( is_single() && !is_attachment() ) {
                
                if( education_zone_is_woocommerce_activated() && 'product' === get_post_type() ){ //For Woocommerce single product
                    if ( $terms = wc_get_product_terms( $post->ID, 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) ) ) {
                        $main_term = apply_filters( 'woocommerce_breadcrumb_main_term', $terms[0], $terms );
                        $ancestors = get_ancestors( $main_term->term_id, 'product_cat' );
                        $ancestors = array_reverse( $ancestors );
                        foreach ( $ancestors as $ancestor ) {
                            $ancestor = get_term( $ancestor, 'product_cat' );    
                            if ( ! is_wp_error( $ancestor ) && $ancestor ) {
                                echo ' <a href="' . esc_url( get_term_link( $ancestor ) ) . '">' . esc_html( $ancestor->name ) . '</a> <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                            }
                        }
                        echo ' <a href="' . esc_url( get_term_link( $main_term ) ) . '">' . esc_html( $main_term->name ) . '</a> <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                    }
                    
                    echo $before . esc_html( get_the_title() ) . $after;
                
                } elseif ( get_post_type() != 'post' ) {
                    
                    $post_type = get_post_type_object( get_post_type() );
                    
                    if( $post_type->has_archive == true ){
                       
                        // Add support for a non-standard label of 'archive_title' (special use case).
                       $label = !empty( $post_type->labels->archive_title ) ? $post_type->labels->archive_title : $post_type->labels->name;
                       // Core filter hook.
                       $label = apply_filters( 'post_type_archive_title', $label, $post_type->name );
                       printf( '<a href="%s">%s</a>', esc_url( get_post_type_archive_link( $post_type ) ), $label );
                       echo '<span class="separator">' . esc_html( $delimiter ) . '</span> ';
        
                    }
                    if ( $showCurrent == 1 ){ 
                       
                        echo $before . esc_html( get_the_title() ) . $after;
                    }

                } else {
                    $cat = get_the_category(); 
                    if( $cat ){
                        $cat = $cat[0];
                        $cats = get_category_parents( $cat, TRUE, ' <span class="separator">' . esc_html( $delimiter ) . '</span> ' );
                        if ( $showCurrent == 0 ) $cats = preg_replace( "#^(.+)\s$delimiter\s$#", "$1", $cats );
                        echo $cats;
                    }
                    if ( $showCurrent == 1 ) echo $before . esc_html( get_the_title() ) . $after;
                }
            
            } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
                $post_type = get_post_type_object(get_post_type());
                if ( get_query_var('paged') ) {
                    echo '<a href="' . esc_url( get_post_type_archive_link( $post_type->name ) ) . '">' . esc_html( $post_type->label ) . '</a>';
                    if( $showCurrent == 1 ) echo ' <span class="separator">' . esc_html( $delimiter ) . '</span> ' . $before . sprintf( __('Page %s','education-zone'), get_query_var('paged') ) . $after;
                } else {
                    if ( $showCurrent == 1 ) echo $before . esc_html( $post_type->label ) . $after;
                }

            } elseif ( is_attachment() ) {
                $parent = get_post( $post->post_parent );
                $cat = get_the_category( $parent->ID ); 
                if( $cat ){
                    $cat = $cat[0];
                    echo get_category_parents( $cat, TRUE, ' <span class="separator">' . esc_html( $delimiter ) . '</span> ');
                    echo '<a href="' . esc_url( get_permalink( $parent ) ) . '">' . esc_html( $parent->post_title ) . '</a>' . ' <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                }
                if ( $showCurrent == 1 ) echo  $before . esc_html( get_the_title() ) . $after;
            
            } elseif ( is_page() && !$post->post_parent ) {
                if ( $showCurrent == 1 ) echo $before . esc_html( get_the_title() ) . $after;
        
            } elseif ( is_page() && $post->post_parent ) {
                $parent_id  = $post->post_parent;
                $breadcrumbs = array();
                while( $parent_id ){
                    $page = get_post( $parent_id );
                    $breadcrumbs[] = '<a href="' . esc_url( get_permalink( $page->ID ) ) . '">' . esc_html( get_the_title( $page->ID ) ) . '</a>';
                    $parent_id  = $page->post_parent;
                }
                $breadcrumbs = array_reverse( $breadcrumbs );
                for ( $i = 0; $i < count( $breadcrumbs) ; $i++ ){
                    echo $breadcrumbs[$i];
                    if ( $i != count( $breadcrumbs ) - 1 ) echo ' <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                }
                if ( $showCurrent == 1 ) echo ' <span class="separator">' . esc_html( $delimiter ) . '</span> ' . $before . esc_html( get_the_title() ) . $after;
            
            } elseif ( is_tag() ) {
                echo $before . esc_html( single_tag_title( '', false ) ) . $after;
            
            } elseif ( is_author() ) {
                global $author;
                $userdata = get_userdata( $author );
                echo $before . esc_html( $userdata->display_name ) . $after;
            
            } elseif ( is_404() ) {
                echo $before . esc_html__( '404 Error - Page not Found', 'education-zone' ) . $after;
            } elseif( is_home() ){
                echo $before;
                single_post_title();
                echo $after;
            }
        
            echo '</div>';
        
        }
    }
    
} // end education_zone_breadcrumbs()
add_action( 'education_zone_breadcrumbs', 'education_zone_breadcrumbs_cb' );

endif;

/**
 * Callback function for Comment List *
 * 
 * @link https://codex.wordpress.org/Function_Reference/wp_list_comments 
 */
 
 function education_zone_theme_comment($comment, $args, $depth){
	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>
	<<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ) : ?>
	<div id="div-comment-<?php comment_ID() ?>" class="comment-body" itemscope itemtype="http://schema.org/UserComments">
	<?php endif; ?>
	
    <footer class="comment-meta">
    
        <div class="comment-author vcard">
    	<?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
    	<?php printf( __( '<b class="fn" itemprop="creator" itemscope itemtype="http://schema.org/Person">%s</b>', 'education-zone' ), get_comment_author_link() ); ?>
    	</div>
    	<?php if ( $comment->comment_approved == '0' ) : ?>
    		<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'education-zone' ); ?></em>
    		<br />
    	<?php endif; ?>
    
    	<div class="comment-metadata commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>"><time datetime="<?php comment_date(); ?>">
    		<?php
    			
    			echo esc_html( get_comment_date() ); ?></time></a><?php edit_comment_link( __( '(Edit)', 'education-zone' ), '  ', '' );
    		?>
    	</div>
    </footer>
    
    <div class="comment-content"><?php comment_text(); ?></div>

	<div class="reply">
	<?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
	</div>
	<?php if ( 'div' != $args['style'] ) : ?>
	</div>
	<?php endif; 
}

if( ! function_exists( 'education_zone_social_cb' ) ):
/**
 * Callback for Social Links
*/
function education_zone_social_cb(){
    
    $facebook  = get_theme_mod( 'education_zone_facebook' );
    $twitter   = get_theme_mod( 'education_zone_twitter' );
    $pinterest = get_theme_mod( 'education_zone_pinterest' );
    $linkedin  = get_theme_mod( 'education_zone_linkedin' );
    $gplus     = get_theme_mod( 'education_zone_gplus' );
    $instagram = get_theme_mod( 'education_zone_instagram' );
    $youtube   = get_theme_mod( 'education_zone_youtube' );
    $ok        = get_theme_mod( 'education_zone_ok' );
    $vk        = get_theme_mod( 'education_zone_vk' );
    $xing      = get_theme_mod( 'education_zone_xing' );
    
    if( $facebook || $twitter || $pinterest || $linkedin || $gplus || $instagram || $youtube || $ok || $vk || $xing ){
    
    ?>
	<ul class="social-networks">
		<?php if( $facebook ){ ?>
        <li><a href="<?php echo esc_url( $facebook ); ?>" target="_blank" title="<?php esc_attr_e( 'Facebook', 'education-zone' );?>"><i class="fa fa-facebook-square"></i></a></li>
		<?php } if( $twitter ){ ?>
        <li><a href="<?php echo esc_url( $twitter ); ?>" target="_blank" title="<?php esc_attr_e( 'Twitter', 'education-zone' );?>"><i class="fa fa-twitter-square"></i></a></li>
        <?php } if( $pinterest ){ ?>
        <li><a href="<?php echo esc_url( $pinterest ); ?>" target="_blank" title="<?php esc_attr_e( 'Pinterest', 'education-zone' );?>"><i class="fa fa-pinterest-square"></i></a></li>
		<?php } if( $linkedin ){ ?>
        <li><a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" title="<?php esc_attr_e( 'LinkedIn', 'education-zone' );?>"><i class="fa fa-linkedin-square"></i></a></li>
        <?php } if( $gplus ){ ?>
        <li><a href="<?php echo esc_url( $gplus ); ?>" target="_blank" title="<?php esc_attr_e( 'Google Plus', 'education-zone' );?>"><i class="fa fa-google-plus-square"></i></a></li>
        <?php } if( $instagram ){ ?>
        <li><a href="<?php echo esc_url( $instagram ); ?>" target="_blank" title="<?php esc_attr_e( 'Instagram', 'education-zone' );?>"><i class="fa fa-instagram"></i></a></li>
		<?php } if( $youtube ){ ?>
        <li><a href="<?php echo esc_url( $youtube ); ?>" target="_blank" title="<?php esc_attr_e( 'YouTube', 'education-zone' );?>"><i class="fa fa-youtube-square"></i></a></li>
        <?php } if( $ok ){ ?>
        <li><a href="<?php echo esc_url( $ok ); ?>" target="_blank" title="<?php esc_attr_e( 'OK', 'education-zone' );?>"><i class="fa fa-odnoklassniki"></i></a></li>
        <?php } if( $vk ){ ?>
        <li><a href="<?php echo esc_url( $vk ); ?>" target="_blank" title="<?php esc_attr_e( 'VK', 'education-zone' );?>"><i class="fa fa-vk"></i></a></li>
        <?php } if( $xing ){ ?>
        <li><a href="<?php echo esc_url( $xing ); ?>" target="_blank" title="<?php esc_attr_e( 'Xing', 'education-zone' );?>"><i class="fa fa-xing"></i></a></li>
        <?php } ?>
	</ul>
    <?php
    }    
}
endif;
add_action( 'education_zone_social', 'education_zone_social_cb' );

if( ! function_exists( 'education_zone_mobile_header' ) ):
/**
 * Callback for Mobile Header
*/   
function education_zone_mobile_header(){
    $phone      = get_theme_mod( 'education_zone_phone' );
    $email      = get_theme_mod( 'education_zone_email' );
    ?>
    <div class="mobile-header">
            <div class="container">
                <div class="menu-opener">
                    <span></span>
                    <span></span>
                    <span></span>
                </div> <!-- menu-opener ends -->

                <div class="site-branding">
                    <?php 
                        if( function_exists( 'has_custom_logo' ) && has_custom_logo() ){
                            echo '<div class="img-logo">';
                            the_custom_logo();
                            echo '</div><!-- .img-logo -->';
                        } 
                    ?>
                    <div class="text-logo">
                    <?php
                        $site_title =  get_bloginfo( 'name', 'display' );
                        $description = get_bloginfo( 'description', 'display' );

                        if( $site_title ) : ?>
                            <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></p>
                        <?php
                        endif;
                    
                       if ( $description ) : ?>
                           <p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
                        <?php
                       endif; 
                    ?>
                    </div>
                </div> <!-- site-branding ends -->
            </div> <!-- container ends -->

            <div class="mobile-menu">
                <?php get_search_form(); ?>

                <nav class="main-navigation" role="navigation">
                    <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
                </nav><!-- #site-navigation -->
                <?php 
                    if( has_nav_menu( 'secondary' ) ){ ?>
                        <nav class="secondary-nav" role="navigation"> 
                            <?php wp_nav_menu( array( 'theme_location' => 'secondary', 'menu_id' => 'secondary-menu', 'fallback_cb' => false ) ); ?>
                        </nav><!-- #site-navigation -->
                    <?php 
                    }

                    if( $email || $phone ){ ?>
                       <div class="contact-info">
                        <?php 
                            if( $phone ) echo '<a href="tel:'. preg_replace('/\D/', '', $phone) .'" class="tel-link">'. esc_html( $phone ) .'</a>';
                            if( $email ) echo '<a href="mailto:'. sanitize_email( $email ) .'" class="email-link">'. esc_html( $email ) .'</a>';
                        ?>
                        </div> <!-- contact-info ends -->
                    <?php 
                    }
                ?>
            </div>
        </div> <!-- mobile-header ends -->
    <?php
}
endif;

if( ! function_exists( 'education_zone_site_header' ) ):
/**
 * Callback for Site Header
*/   
function education_zone_site_header(){
    $phone      = get_theme_mod( 'education_zone_phone' );
    $email      = get_theme_mod( 'education_zone_email' );
    $menu_label = get_theme_mod('education_zone_top_menu_label', __('Quick Links','education-zone') );
    ?>
    <header id="masthead" class="site-header" role="banner" itemscope itemtype="http://schema.org/WPHeader">
            <?php 
            if( $phone || $email || has_nav_menu( 'secondary' ) ) { ?>
                <div class="header-top">
                  <div class="container">
                    <?php 
                    if( $email || $phone ){ ?>
                        <div class="top-links">
                            <?php 
                            if( $email ){ ?>
                                <span><i class="fa fa-envelope-o"></i><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
                                </span>
                            <?php 
                            } 
                            if( $phone ){ ?>
                                <span><i class="fa fa-phone"></i><a href="tel:<?php echo preg_replace('/\D/', '', $phone); ?>"><?php echo esc_html( $phone ); ?></a>
                                </span>
                            <?php 
                            } ?>
                        </div>
                    <?php 
                    }
                    if( has_nav_menu( 'secondary' ) ){ ?>
                        <nav id="secondary-navigation" class="secondary-nav" role="navigation">     
                            <a href="javascript:void(0);"><?php echo esc_html( $menu_label ); ?></a>                
                            <?php wp_nav_menu( array( 'theme_location' => 'secondary', 'menu_id' => 'secondary-menu', 'fallback_cb' => false ) ); ?>
                        </nav><!-- #site-navigation -->
                    <?php 
                    } ?>
                </div>
            </div>
            <?php 
            } ?>
        
            <div class="header-m">
                <div class="container">
                    <div class="site-branding" itemscope itemtype="http://schema.org/Organization">
                        <?php 
                            if( function_exists( 'has_custom_logo' ) && has_custom_logo() ){
                                the_custom_logo();
                            } 
                        ?>
                        <?php if ( is_front_page() ) : ?>
                            <h1 class="site-title" itemprop="name"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" itemprop="url"><?php bloginfo( 'name' ); ?></a></h1>
                        <?php else : ?>
                            <p class="site-title" itemprop="name"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" itemprop="url"><?php bloginfo( 'name' ); ?></a></p>
                        <?php endif;
                           $description = get_bloginfo( 'description', 'display' );
                           if ( $description || is_customize_preview() ) : ?>
                               <p class="site-description" itemprop="description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
                        <?php
                           endif; 
                        ?>                    
                   </div><!-- .site-branding -->
                   
                    <div class="form-section">
                        <div class="example">                       
                            <?php get_search_form(); ?>
                        </div>
                    </div>
                </div>
            </div>
            
           <div class="header-bottom">
                <div class="container">
                    <nav id="site-navigation" class="main-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">                        
                        <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
                    </nav><!-- #site-navigation -->
                </div>
            </div>

        </header><!-- #masthead -->
    <?php
}
endif;
/**
 * Get home page sections 
*/ 
function education_zone_get_sections(){

    $sections = array(
            'slider-section' => array(
               'id' => 'slider',
               'class' => 'banner'
                ),
            'info-section' => array(
               'id' => 'info',
               'class' => 'information'
                ),
             'welcome-section' => array(
              'id' => 'welcome',
              'class' => 'welcome-note'
              ),
             'courses-section' => array(
              'id' => 'courses',
              'class' => 'featured-courses'
              ),
             'extra-info-section' => array(
              'id' => 'extra_info',
              'class' => 'theme'
              ),
            'choose-section' => array(
              'id' => 'choose',
              'class' => 'choose-us'
              ),
            'testimonial-section' => array(
              'id' => 'testimonials',
              'class' => 'student-stories'
              ),
            'blog-section' => array(
              'id' => 'blog',
              'class' => 'latest-events'
              ),
            'gallery-section'=> array(
              'id' => 'gallery',
              'class' => 'photo-gallery'
              ),
            'search-section' => array(
              'id' => 'search',
              'class' => 'search-section'
              ),
      );
    $enabled_section = array();
    foreach ( $sections as $section ) {
        if ( get_theme_mod( 'education_zone_ed_' . $section['id'] . '_section' ) == 1 ){
            $enabled_section[] = array(
                'id'    => $section['id'],
                'class' => $section['class']
            );
        }
    }
    return $enabled_section;
}

/**
 * Return sidebar layouts for pages
*/
function education_zone_sidebar_layout_class(){
    global $post;
    if( is_page() ){
        $sidebar = get_post_meta( $post->ID, 'education_zone_sidebar_layout', true );
        if( $sidebar ){
            return $sidebar;    
        }else{
            return 'right-sidebar';
        }
    }
}

if ( ! function_exists( 'education_zone_excerpt_more' ) ) :
/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... * 
 */
function education_zone_excerpt_more( $more ) {
	return is_admin() ? $more : ' &hellip; ';
}
add_filter( 'excerpt_more', 'education_zone_excerpt_more' );
endif;

if ( ! function_exists( 'education_zone_excerpt_length' ) && ! is_admin() ) :
/**
 * Changes the default 55 character in excerpt 
*/
function education_zone_excerpt_length( $length ) {
	return is_admin() ? $length : 20;
}
add_filter( 'excerpt_length', 'education_zone_excerpt_length', 999 );
endif;

if( ! function_exists( 'education_zone_change_comment_form_default_fields' ) ) :
/**
 * Change Comment form default fields i.e. author, email & url.
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function education_zone_change_comment_form_default_fields( $fields ){
    
    // get the current commenter if available
    $commenter = wp_get_current_commenter();
 
    // core functionality
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );    
 
    // Change just the author field
    $fields['author'] = '<p class="comment-form-author"><label class="screen-reader-text" for="author">' . esc_html__( 'Name*', 'education-zone' ) . '</label><input id="author" name="author" placeholder="' . esc_attr__( 'Name*', 'education-zone' ) . '" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>';
    
    $fields['email'] = '<p class="comment-form-email"><label class="screen-reader-text" for="email">' . esc_html__( 'Email*', 'education-zone' ) . '</label><input id="email" name="email" placeholder="' . esc_attr__( 'Email*', 'education-zone' ) . '" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>';
    
    $fields['url'] = '<p class="comment-form-url"><label class="screen-reader-text" for="url">' . esc_html__( 'Website', 'education-zone' ) . '</label><input id="url" name="url" placeholder="' . esc_attr__( 'Website', 'education-zone' ) . '" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>'; 
    
    return $fields;
    
}
endif;

add_filter( 'comment_form_default_fields', 'education_zone_change_comment_form_default_fields' );


if( ! function_exists( 'education_zone_change_comment_form_defaults' ) ) :
/**
 * Change Comment Form defaults
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function education_zone_change_comment_form_defaults( $defaults ){
    
    // Change the "cancel" to "I would rather not comment" and use a span instead
    $defaults['comment_field'] = '<p class="comment-form-comment"><label class="screen-reader-text" for="comment">' . esc_html__( 'Comment', 'education-zone' ) . '</label><textarea id="comment" name="comment" placeholder="' . esc_attr__( 'Comment', 'education-zone' ) . '" cols="45" rows="8" aria-required="true"></textarea></p>';
    
    return $defaults;
    
}
endif;

add_filter( 'comment_form_defaults', 'education_zone_change_comment_form_defaults' );

if ( ! function_exists( 'education_zone_get_section_header' ) ) :
/**
 * Returns Section header
*/
function education_zone_get_section_header( $section_title ){
    
        $header_query = new WP_Query( array( 
                
                'p' => $section_title,
                'post_type' => 'page'

                ));
        
        if( $section_title && $header_query->have_posts() ){ 
            while( $header_query->have_posts() ){ $header_query->the_post();
    ?>
                <div class="header-part">
                    <?php 
                        echo '<h2 class="section-title">';
                         the_title();
                         echo '</h2>';
                        the_content(); 
                    ?>
                </div>
    <?php   }
        wp_reset_postdata();
        }   
}
endif;


if( ! function_exists( 'education_zone_single_post_schema' ) ) :
/**
 * Single Post Schema
 *
 * @return string
 */
function education_zone_single_post_schema() {
    if ( is_singular( 'post' ) ) {
        global $post;
        $custom_logo_id = get_theme_mod( 'custom_logo' );

        $site_logo   = wp_get_attachment_image_src( $custom_logo_id , 'education-zone-schema' );
        $images      = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
        $excerpt     = education_zone_escape_text_tags( $post->post_excerpt );
        $content     = $excerpt === "" ? mb_substr( education_zone_escape_text_tags( $post->post_content ), 0, 110 ) : $excerpt;
        $schema_type = ! empty( $custom_logo_id ) && has_post_thumbnail( $post->ID ) ? "BlogPosting" : "Blog";

        $args = array(
            "@context"  => "http://schema.org",
            "@type"     => $schema_type,
            "mainEntityOfPage" => array(
                "@type" => "WebPage",
                "@id"   => get_permalink( $post->ID )
            ),
            "headline"  => get_the_title( $post->ID ),
            "image"     => array(
                "@type"  => "ImageObject",
                "url"    => $images[0],
                "width"  => $images[1],
                "height" => $images[2]
            ),
            "datePublished" => get_the_time( DATE_ISO8601, $post->ID ),
            "dateModified"  => get_post_modified_time(  DATE_ISO8601, __return_false(), $post->ID ),
            "author"        => array(
                "@type"     => "Person",
                "name"      => education_zone_escape_text_tags( get_the_author_meta( 'display_name', $post->post_author ) )
            ),
            "publisher" => array(
                "@type"       => "Organization",
                "name"        => get_bloginfo( 'name' ),
                "description" => get_bloginfo( 'description' ),
                "logo"        => array(
                    "@type"   => "ImageObject",
                    "url"     => $site_logo[0],
                    "width"   => $site_logo[1],
                    "height"  => $site_logo[2]
                )
            ),
            "description" => ( class_exists('WPSEO_Meta') ? WPSEO_Meta::get_value( 'metadesc' ) : $content )
        );

        if ( has_post_thumbnail( $post->ID ) ) :
            $args['image'] = array(
                "@type"  => "ImageObject",
                "url"    => $images[0],
                "width"  => $images[1],
                "height" => $images[2]
            );
        endif;

        if ( ! empty( $custom_logo_id ) ) :
            $args['publisher'] = array(
                "@type"       => "Organization",
                "name"        => get_bloginfo( 'name' ),
                "description" => get_bloginfo( 'description' ),
                "logo"        => array(
                    "@type"   => "ImageObject",
                    "url"     => $site_logo[0],
                    "width"   => $site_logo[1],
                    "height"  => $site_logo[2]
                )
            );
        endif;

        echo '<script type="application/ld+json">' , PHP_EOL;
        if ( version_compare( PHP_VERSION, '5.4.0' , '>=' ) ) {
            echo wp_json_encode( $args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) , PHP_EOL;
        } else {
            echo wp_json_encode( $args ) , PHP_EOL;
        }
        echo '</script>' , PHP_EOL;
    }
}
endif;
add_action( 'wp_head', 'education_zone_single_post_schema' );

if( ! function_exists( 'education_zone_escape_text_tags' ) ) :
/**
 * Remove new line tags from string
 *
 * @param $text
 * @return string
 */
function education_zone_escape_text_tags( $text ) {
    return (string) str_replace( array( "\r", "\n" ), '', strip_tags( $text ) );
}
endif;