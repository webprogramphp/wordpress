<?php 
/*
 * Template Name: Home Page 
 */
get_header(); 
$a1_options = get_option( 'a1_theme_options' ); ?>
<section class="section-main" style="<?php if (get_theme_mod('a1-fixed-top-menu', false)){ ?>margin-top:93px; <?php } ?>">
<!--home banner-->
<?php if(get_theme_mod ( 'frontpage_slider_sectionswitch',(!isset($a1_options['remove-slider']))?'1':'2')==1) {  $a1_loop_active = 0; ?>
<div class="col-md-12 home-banner">
	<div id="myCarousel" class="carousel slide">
	   <div class="carousel-inner">
	   <?php  for($a1_i=1; $a1_i <= 5 ;$a1_i++ ): 
	   	$sliderimage_image = get_theme_mod ( 'a1_homepage_sliderimage'.$a1_i.'_image',isset($a1_options['slider-img-'.$a1_i])?a1_get_image_id($a1_options['slider-img-'.$a1_i]):'');

		$sliderimage_title = get_theme_mod ( 'a1_homepage_sliderimage'.$a1_i.'_title',$a1_options['slidecaption-'.$a1_i]);
		$sliderimage_content = get_theme_mod ( 'a1_homepage_sliderimage'.$a1_i.'_content',$a1_options['slidebuttontext-'.$a1_i]);		
		$sliderimage_link  = get_theme_mod ( 'a1_homepage_sliderimage'.$a1_i.'_link',$a1_options['slidebuttonlink-'.$a1_i] );
	    if($sliderimage_image!=''){		
		    $a1_loop_active++;
			$sliderimage_image_url = wp_get_attachment_image_src($sliderimage_image,'full');	?>

		  <div class="item <?php if($a1_loop_active==1){ echo'active'; } ?>">
			 <img src="<?php echo esc_url($sliderimage_image_url[0]); ?>" width="1350" height="539" alt="<?php esc_attr_e('First slide','a1') ?>">
			 <div class="col-md-offset-6 col-md-5 banner-text">
			 <?php if($sliderimage_title!='') { ?>
				 <h1><?php echo esc_attr($sliderimage_title); ?></h1>
				 <div class="circle-border"><span class="fa fa-circle"></span></div>    
			 <?php }
			  if($sliderimage_content!='' || $sliderimage_link!='') { ?><a href="<?php echo esc_url($sliderimage_link); ?>"><?php echo esc_attr($sliderimage_content); ?></a><?php } ?>
			 </div>
		  </div>
	   <?php }  endfor; ?>   
	   </div>
	   <!-- Carousel nav -->
	   <?php  if($a1_loop_active >= 2): ?>
		   <a class="carousel-control left nav-left" href="#myCarousel" data-slide="prev"></a>
		   <a class="carousel-control right nav-right" href="#myCarousel" data-slide="next"></a>
	   <?php  endif; ?>
	</div> 
</div>
<?php } ?>
<!--home banner end-->
<!--feature part-->
<?php if(get_theme_mod ( 'a1_homepage_first_sectionswitch',(!isset($a1_options['remove-core-features']))?'1':'2')==1) { ?>
<div class="col-md-12 home-feature">
	<div class="container a1-container">
		<?php if(get_theme_mod ( 'a1_homepage_section_title',$a1_options['coretitle'])!='' || get_theme_mod ( 'a1_homepage_section_desc',$a1_options['corecaption']) !='') { ?>
                        <div class="col-md-8 col-md-offset-2 feature-top">
				<?php if(get_theme_mod ( 'a1_homepage_section_title',$a1_options['coretitle'])!='' ) { ?>
				<h2 class="home-heading"><?php echo esc_attr(get_theme_mod ( 'a1_homepage_section_title',$a1_options['coretitle'])); ?></h2>
				<div class="circle-border"><span class="fa fa-circle"></span></div>
				<?php } if(get_theme_mod ( 'a1_homepage_section_desc',$a1_options['corecaption']) !='') { echo '<p>'.esc_attr(get_theme_mod ( 'a1_homepage_section_desc',$a1_options['corecaption'])).'</p>'; } ?>
			</div>
		<?php } ?>
		<div class="col-md-12 no-padding-lr">
			<div class="row feature-row1">
			<?php for($a1_section_i=1; $a1_section_i <=3 ;$a1_section_i++ ): ?>
                <?php $core_link = (get_theme_mod ( 'a1_homepage_first_section'.$a1_section_i.'_link',$a1_options['coresectionlink-'.$a1_section_i])!='')?get_theme_mod ( 'a1_homepage_first_section'.$a1_section_i.'_link',$a1_options['coresectionlink-'.$a1_section_i]):''; 
                	if(get_theme_mod ( 'a1_homepage_first_section'.$a1_section_i.'_icon',isset($a1_options['home-icon-'.$a1_section_i])?a1_get_image_id($a1_options['home-icon-'.$a1_section_i]):'')!='' && (get_theme_mod ( 'a1_homepage_first_section'.$a1_section_i.'_title',$a1_options['section-title-'.$a1_section_i])!='' || get_theme_mod ( 'a1_homepage_first_section'.$a1_section_i.'_desc',$a1_options['section-content-'.$a1_section_i])!='' )):
                	if(!empty($core_link)){ ?>
                        <a href="<?php echo esc_url($core_link); ?>">
                     <?php } ?>
				<div class="col-md-4 col-sm-4 feature-box">
					<?php if(get_theme_mod ( 'a1_homepage_first_section'.$a1_section_i.'_icon',isset($a1_options['home-icon-'.$a1_section_i])?a1_get_image_id($a1_options['home-icon-'.$a1_section_i]):'')!='') {
						$sliderimage_image_url = wp_get_attachment_image_src(get_theme_mod ( 'a1_homepage_first_section'.$a1_section_i.'_icon',isset($a1_options['home-icon-'.$a1_section_i])?a1_get_image_id($a1_options['home-icon-'.$a1_section_i]):''),'full'); ?>
						<span><img src="<?php echo esc_url($sliderimage_image_url[0]); ?>" width="40" height="25" /></span>
					<?php }
					 if(get_theme_mod ( 'a1_homepage_first_section'.$a1_section_i.'_title',$a1_options['section-title-'.$a1_section_i])!='') { echo '<h5>'.esc_attr(get_theme_mod ( 'a1_homepage_first_section'.$a1_section_i.'_title',$a1_options['section-title-'.$a1_section_i])).'</h5>'; }
					 if(get_theme_mod ( 'a1_homepage_first_section'.$a1_section_i.'_desc',$a1_options['section-content-'.$a1_section_i])!='') { echo '<p>'.esc_attr(get_theme_mod ( 'a1_homepage_first_section'.$a1_section_i.'_desc',$a1_options['section-content-'.$a1_section_i])).'</p>'; } ?>
				</div>
                              <?php if(!empty($core_link)){ ?>
                                </a>
                    <?php } ?>
				 <?php endif; endfor; ?>    
			</div>
		</div>    
	</div>
</div>
<?php } ?>
<!--feature part end--> 
<!--product description start-->
<?php if(get_theme_mod ( 'a1_homepage_second_sectionswitch',(!isset($a1_options['remove-product-description']))?'1':'2')==1) { ?>
<div class="col-md-12 home-product">
	<div class="container a1-container">
	<?php if( get_theme_mod ('a1_homepage_second_section_title',$a1_options['producttitle'])!='' || get_theme_mod ( 'a1_homepage_second_section_caption',$a1_options['productcaption'])!='') { ?>	
		<div class="col-md-8 col-md-offset-2 feature-top">
			<?php if(get_theme_mod ('a1_homepage_second_section_title',$a1_options['producttitle'])!='') { ?>
			<h2 class="home-heading"><?php echo esc_attr(get_theme_mod ('a1_homepage_second_section_title',$a1_options['producttitle'])); ?></h2>
			<div class="circle-border"><span class="fa fa-circle"></span></div>
			<?php } if(get_theme_mod ( 'a1_homepage_second_section_caption',$a1_options['productcaption'])!='') { echo '<p>'.esc_attr(get_theme_mod ( 'a1_homepage_second_section_caption',$a1_options['productcaption'])).'</p>'; } ?>
		</div>
	<?php } ?>
		<div class="row">
		<?php if(get_theme_mod ( 'a1_homepage_second_section_desc',$a1_options['productcontent'])!='') { echo '<div class="col-md-7 col-sm-7 description-left">'.esc_html(get_theme_mod ( 'a1_homepage_second_section_desc',$a1_options['productcontent'])).'</div>'; } 

			if(get_theme_mod ( 'a1_homepage_second_section_email',$a1_options['product-form-email'])!='') { ?>
			<div class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 description-right">
				<form method="post" name="contact_form" id="contact_form">
					<input id="a1_name" type="text" name="a1_name" placeholder="<?php esc_attr_e('Enter your name','a1'); ?>">
					<input id="a1_email" type="email" name="a1_email" placeholder="<?php esc_attr_e('Enter your email','a1'); ?>">
					<input id="a1_phone" type="text" name="a1_phone" placeholder="<?php esc_attr_e('Enter your phone','a1'); ?>">
					<textarea id="a1_message" name="a1_message" placeholder="<?php esc_attr_e('Enter your message','a1'); ?>"></textarea>
					<input type="submit" name="a1_submit" id="a1_submit" value="<?php esc_attr_e('send','a1'); ?>">
				</form>
			<?php }  ?>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<!--product end -->
<!--get touch start--> 
<?php if( get_theme_mod ( 'a1_homepage_third_sectionswitch',(!isset($a1_options['remove-getin-touch']))?'1':'2')==1) { ?>
<div class="col-md-12 home-contact">
	<div class="container a1-container a1-get-touch-home"> 
		<?php if(get_theme_mod ( 'a1_homepage_third_section_title',$a1_options['get-touch-title'])!='') { echo '<h2>'.esc_attr(get_theme_mod ( 'a1_homepage_third_section_title',$a1_options['get-touch-title'])).'</h2>'; } ?>
		<?php if(get_theme_mod ( 'a1_homepage_third_section_caption',$a1_options['get-touch-caption'])!='') { echo '<p>'.esc_attr(get_theme_mod ( 'a1_homepage_third_section_caption',$a1_options['get-touch-caption'])).'</p>'; } ?>
		<?php if(get_theme_mod ( 'a1_homepage_third_section_image',isset($a1_options['get-touch-logo'])?a1_get_image_id($a1_options['get-touch-logo']):'')!='') { 
			$sliderimage_image_url = wp_get_attachment_image_src(get_theme_mod ( 'a1_homepage_third_section_image',isset($a1_options['get-touch-logo'])?a1_get_image_id($a1_options['get-touch-logo']):''),'full'); ?>
			<div class="logo-contact">
				<img src="<?php echo esc_url($sliderimage_image_url[0]); ?>" alt="">
			</div>
		<?php } ?> 
		<?php if(get_theme_mod ('a1_homepage_third_section_btn_title',$a1_options['contactus-now-text'])!='' ) {
$data = '<a class="contact-button" href="'.esc_url(get_theme_mod ('a1_homepage_third_section_btn_link',$a1_options['get-touch-page'])).'">'.esc_attr(get_theme_mod ('a1_homepage_third_section_btn_title',$a1_options['contactus-now-text'])).'</a>'; echo wp_kses_post(apply_filters("the_content",$data)); } ?>
	</div>
</div>
<?php } ?>
<!--get touch end-->
</section>
<?php get_footer(); ?>
