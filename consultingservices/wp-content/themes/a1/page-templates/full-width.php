<?php 
/*
 * Template Name: Full Width
 */
get_header(); ?>
<!--section part start-->
<section class="section-main" style="<?php if (get_theme_mod('a1-fixed-top-menu', false)){ ?>margin-top:93px; <?php } ?>">
  <div class="col-md-12 a1-breadcrumb">
    <div class="container a1-container">
      <div class="col-md-6 col-sm-6 no-padding-lr left-part">      
      <h3><?php echo get_the_title(); ?></h3>     
      </div>
      <div class="col-md-6 col-sm-6 no-padding-lr right-part">
        <?php if(function_exists('a1_custom_breadcrumbs')) a1_custom_breadcrumbs(); ?>
      </div>
    </div>
  </div>
  <div class="clearfix"></div>
  <div class="container a1-container">
    <div class="row">
      <div class="col-md-12 blog-article">
        <?php while ( have_posts() ) : the_post(); ?>
        <div class="blog-post"> 
          <div class="blog-inner"> 
          <?php $a1_image = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID())); 
              if(!empty($a1_image)) :?>
            <img src="<?php echo esc_url( $a1_image ); ?>" class="img-responsive" alt="<?php echo esc_attr(get_the_title()); ?>">
             <?php endif; ?>
            <div class="blog-content">
              <?php the_content(); ?>
               </div>
          </div>
        </div>
        <div class="col-md-12 a1-post-comment no-padding">
        <?php comments_template( '', true ); ?>
        </div>
         <?php endwhile; ?> 
		</div>
     </div>
    </div>
  </section>
<!--section part end-->
<?php get_footer(); ?>