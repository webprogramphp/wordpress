<?php 
/*
* WooCommerce page template
*/
get_header(); ?>
<section class="section-main" style="<?php if (get_theme_mod('a1-fixed-top-menu', false)){ ?>margin-top:93px; <?php } ?>">
  <div class="col-md-12 a1-breadcrumb">
    <div class="container a1-container">
      <div class="col-md-6 col-sm-6 no-padding-lr left-part">     
		<h3><?php the_title(); ?></h3>    
      </div>
      <div class="col-md-6 col-sm-6 no-padding-lr right-part">
        <?php if(function_exists('a1_custom_breadcrumbs')) a1_custom_breadcrumbs(); ?>
      </div>
    </div>
  </div>
  <div class="clearfix"></div>
  <div class="container a1-container">
    <div class="row">
      <div class="col-md-8 blog-article">
        <?php woocommerce_content(); ?>
      </div>
      <!--sidebar start-->
      <?php get_sidebar(); ?>
        </div>
      </div>
  </section>
<?php get_footer(); ?>