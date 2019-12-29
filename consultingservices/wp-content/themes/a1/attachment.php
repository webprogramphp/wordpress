<?php 
/*
 * Attachment Template File.
 */
get_header(); ?>
<!--section part start-->
<section class="section-main" style="<?php if (get_theme_mod('a1-fixed-top-menu', false)){ ?>margin-top:93px; <?php } ?>">
  <div class="col-md-12 a1-breadcrumb" >
    <div class="container a1-container">
      <div class="col-md-6 col-sm-6 no-padding-lr left-part">
        <h3><?php echo get_the_title() ?></h3>
      </div>
    </div>
  </div>
  <div class="container a1-container">
    <div class="row">
      <div class="col-md-8 blog-article">
       <?php while ( have_posts() ) : the_post(); ?>  
         <div class="blog-post single-blog"> <?php the_title(); ?>
          <?php if(!get_theme_mod ( 'a1_blog_page_blog_post',false)){ ?>
           <div class="blog-info"> 
            <ul>
              <?php a1_entry_meta(); ?>
            </ul>
          </div>
          <?php } ?>
          <div class="blog-inner"> 
             <?php if(has_post_thumbnail()) :
                  the_post_thumbnail('large');
              endif; ?>
            <div class="blog-content">
             <?php the_content(); ?>
            </div>
          </div>
        </div>
         <?php endwhile; ?>
         <div class="col-md-12 a1-post-comment no-padding">
         <?php comments_template('', true); ?>
        </div>    
      </div>
      <?php get_sidebar(); ?>
    </div>
  </div>
</section>
<!--section part end-->
<?php get_footer(); ?>