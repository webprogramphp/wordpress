<?php
/*
 * A1  Breadcrumbs
*/
global $a1_options;
if(get_theme_mod('a1-remove-breadcrumbs',true)) {
	function a1_custom_breadcrumbs() {
	  $a1_showonhome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show 	 
	  $a1_showcurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show 
	 
	  global $post;
	  
	  if (is_home() || is_front_page()) {
	    if ($a1_showonhome == 1) echo '<ol class="breadcrumb"><li class="active"><a href="' . esc_url(home_url()) . '">' . esc_html__('Home','a1') . '</a></li></ol>';
	  } else {
	    echo '<ol class="breadcrumb"><li class="active"><a href="' . esc_url(home_url()) . '">' . esc_html__('Home','a1') . '</a> ';
	    if ( is_category() ) {
	      $a1_thisCat = get_category(get_query_var('cat'), false);
	      if ($a1_thisCat->parent != 0) echo get_category_parents($a1_thisCat->parent, TRUE, ' ');
	      esc_html_e('Archive by category' , 'a1');  echo esc_html(single_cat_title('', false)) ;
	    } elseif ( is_search() ) {
	      esc_html_e('Search results for','a1'); echo esc_html(get_search_query());
	    } elseif ( is_day() ) {
	      echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a> ';
	      echo '<a href="' . esc_url(get_month_link(get_the_time('Y'),get_the_time('m'))) . '">' . esc_html(get_the_time('F')) . '</a> ';
	      echo esc_html(get_the_time('d')) ;
	    } elseif ( is_month() ) {
	      echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a> ';
	      echo  esc_html(get_the_time('F')) ;
	    } elseif ( is_year() ) {
	      echo  esc_html(get_the_time('Y')) ;
	    } elseif ( is_single() && !is_attachment() ) {
	      if ( get_post_type() != 'post' ) {
		$a1_post_type = get_post_type_object(get_post_type());
		$a1_slug = $a1_post_type->rewrite;
		echo '<a href="' . esc_url(home_url('/' . $a1_slug['slug'] . '/')). '">' . esc_html($a1_post_type->labels->singular_name) . '</a>';
		if ($a1_showcurrent == 1) echo  esc_html(get_the_title()) ;
	      } else {
		$a1_cat = get_the_category(); $a1_cat = $a1_cat[0];
		$a1_cats = get_category_parents($a1_cat, TRUE, ' ');
		if ($a1_showcurrent == 0) $a1_cats = 
		preg_replace("#^(.+)\s\s$#", "$1",$a1_cats);
		echo $a1_cats;
		if ($a1_showcurrent == 1) echo  esc_html(get_the_title()) ;
	      }
	    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
	      $a1_post_type = get_post_type_object(get_post_type());
	      echo  $a1_post_type->labels->singular_name ;
	    } elseif ( is_attachment() ) {
	      $a1_parent = get_post($post->post_parent);
	      $a1_cat = get_the_category($a1_parent->ID); $a1_cat = $a1_cat[0];
	      echo get_category_parents($a1_cat, TRUE, ' ');
	      echo '<a href="' . esc_url(get_permalink($a1_parent)) . '">' . esc_html($a1_parent->post_title) . '</a>';
	      if ($a1_showcurrent == 1) echo esc_html(get_the_title()) ;
	    } elseif ( is_page() && !$post->post_parent ) {
	      if ($a1_showcurrent == 1) echo esc_html(get_the_title()) ;
	    } elseif ( is_page() && $post->post_parent ) {
	      $a1_parent_id  = $post->post_parent;
	      $a1_breadcrumbs = array();
	      while ($a1_parent_id) {
		$a1_page = get_page($a1_parent_id);
		$a1_breadcrumbs[] = '<a href="' . esc_url(get_permalink($a1_page->ID)) . '">' . esc_html(get_the_title($a1_page->ID)) . '</a>';
		$a1_parent_id  = $a1_page->post_parent;
	      }
	      $a1_breadcrumbs = array_reverse($a1_breadcrumbs);
	      for ($a1_i = 0; $a1_i < count($a1_breadcrumbs); $a1_i++) {
		echo $a1_breadcrumbs[$a1_i];
		if ($a1_i != count($a1_breadcrumbs)-1) echo ' ';
	      }
	      if ($a1_showcurrent == 1) echo esc_html(get_the_title()) ;
	    } elseif ( is_tag() ) {
	      echo  esc_html__('Posts tagged','a1'); echo esc_html(single_tag_title('', false)) . '"';
	    } elseif ( is_author() ) {
	       global $author;
	      $a1_userdata = get_userdata($author);
	      echo esc_html__('Articles posted by','a1'); echo esc_html($a1_userdata->display_name) ;
	    } elseif ( is_404() ) {
	      echo esc_html__('Error 404','a1'); 
	    }
	    if ( get_query_var('paged') ) {
	      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
	      echo esc_html__('Page','a1') . ' ' . esc_html(get_query_var('paged'));
	      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
	    }
	    echo '</li></ol>';
	  }
	} // end a1_custom_breadcrumbs()
}