<?php 
/*
 * Tag Template File.
 */
get_header(); ?>
<!--section part start-->
<section class="section-main" style="<?php if (get_theme_mod('a1-fixed-top-menu', false)){ ?>margin-top:93px; <?php } ?>">
  <div class="col-md-12 a1-breadcrumb" >
    <div class="container a1-container">
      <div class="col-md-6 col-sm-6 no-padding-lr left-part">
    <h3><?php esc_html_e('Tag','a1'); echo':'. single_tag_title( '', false ); ?></h3>
      </div>
      <div class="col-md-6 col-sm-6 no-padding-lr right-part">
        <?php if(function_exists('a1_custom_breadcrumbs')) a1_custom_breadcrumbs(); ?>
      </div>
    </div>
  </div>
  <div class="clearfix"></div>
  <div class="container a1-container">
    <div class="row">
      <div class="col-md-8 col-sm-8 blog-article">
        <?php while ( have_posts() ) : the_post(); ?>
        <div class="blog-post"> <a href="<?php echo esc_url( get_permalink() ); ?>" class="blog-title"><?php echo get_the_title();  ?></a>
          <?php if(!get_theme_mod ( 'a1_blog_page_blog_post',false)){ ?>
           <div class="blog-info"> 
            <ul>
              <?php a1_entry_meta(); ?>
            </ul>
          </div>
          <?php } ?>
          <div class="blog-inner"> 
            <?php if(has_post_thumbnail()) :  the_post_thumbnail('full');   endif; ?>
            <div class="blog-content">
              <?php the_excerpt(); ?>
               </div>
          </div>
        </div>
         <?php endwhile; ?>
        <!--Pagination Start-->
        <?php if(function_exists('faster_pagination')) {?>
         <div class="col-md-12 a1-pagination no-padding">
              <?php faster_pagination('','2'); ?>
         </div>
        <?php }else { ?>
        <?php if(get_option('posts_per_page ') < $wp_query->found_posts) { ?>
        <div class="col-md-12 a1-pagination no-padding">
          <ul>
          <li><?php previous_posts_link(); ?></li>
          <li><?php next_posts_link(); ?></li>
        </ul>
        </div>
        <?php } ?>
        <?php } ?>
        <!--Pagination End-->
      </div>
      <!--sidebar start-->
      <?php get_sidebar(); ?>
        </div>
      </div>
  </section>
<!--section part end-->
<?php get_footer(); ?>