<?php
/*
 * Header For a1 Theme.
 */
global $a1_options;
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
    <!--<![endif]-->
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">       
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
        <header class="<?php if (get_theme_mod('a1-fixed-top-menu', false)){ ?>header-fixed-color<?php } ?>">
            <?php if (!get_theme_mod('a1-remove-top-header', false)): ?>
            <div class="top-header">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="row">
                                <div class="col-md-8 col-sm-8 col-xs-12 location-part">
                                    
                                    <?php if(get_theme_mod('theme_phone_number',$a1_options['phone'])!='') : ?>
                                        <p><i class="fa fa-phone"></i><?php echo esc_attr(get_theme_mod('theme_phone_number',$a1_options['phone'])); ?></p>
                                    <?php endif;
                                        if (get_theme_mod('theme_email_id',$a1_options['email'])!=''): ?>
                                        <p class="top-header-email"><i class="fa fa-envelope-o"></i><a href="mailto:<?php echo esc_attr($a1_options['email']); ?>"><?php echo esc_attr(get_theme_mod('theme_email_id',$a1_options['email'])); ?></a></p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-12 social-part">
                                    <ul>
                                        <?php $TopHeaderSocialIconDefault = array(
                                              array('url'=>isset($a1_options['fburl'])?$a1_options['fburl']:'#','icon'=>'fa-facebook'),
                                              array('url'=>isset($a1_options['twitter'])?$a1_options['twitter']:'#','icon'=>'fa-twitter'),
                                              array('url'=>isset($a1_options['googleplus'])?$a1_options['googleplus']:'#','icon'=>'fa-google-plus'),  
                                              array('url'=>isset($a1_options['pinterest'])?$a1_options['pinterest']:'#','icon'=>'fa-pinterest'),
                                            );
                                        for($i=1; $i<=4; $i++) :
                                        if(get_theme_mod('TopHeaderSocialIconLink'.$i,$TopHeaderSocialIconDefault[$i-1]['url'])!='' && get_theme_mod('TopHeaderSocialIcon'.$i,$TopHeaderSocialIconDefault[$i-1]['icon'])!=''): ?>
                                           <li><a href="<?php echo esc_url(get_theme_mod('TopHeaderSocialIconLink'.$i,$TopHeaderSocialIconDefault[$i-1]['url'])); ?>" class="icon" title="" target="_blank">
                                                <i class="fa <?php echo esc_attr(get_theme_mod('TopHeaderSocialIcon'.$i,$TopHeaderSocialIconDefault[$i-1]['icon'])); ?>"></i>
                                            </a></li>
                                        <?php endif; endfor; ?>    
                                    </ul>                            
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif;?>

            <!-- Fixed navbar -->
            <nav id="header" class="navbar main-nav bottom-header">
                <div id="header-container" class="container">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="navbar-header-logo main-logo"> 
                                <?php 
                                if(has_custom_logo()){
                                    the_custom_logo();
                                } ?>
                                <?php if(display_header_text()):?>
                                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" id='brand' class="home-link"><h2><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h2><p class="site-description"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p></a>   
                                <?php endif; ?>
                            </div>
                            <div id="mainmenu" classold=="collapse navbar-collapse">
                                <?php
                                    $a1_defaults = array(
                                        'theme_location'  => 'primary',                            
                                        'container'       => 'ul',                            
                                        'echo'            => true,
                                        'menu_class'      => 'navbar-nav',
                                        'depth'           => 0,
                                    );                               
                                    wp_nav_menu($a1_defaults);
                                 ?>
                            </div><!-- /.nav-collapse -->
                        </div>
                    </div>
                </div><!-- /.container -->
            </nav><!-- /.navbar -->
        </header>
        <!--header part end-->