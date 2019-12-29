<?php
/**
* Customization options
*
*/

function a1_sanitize_checkbox( $checked ) {
  return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

function a1_field_sanitize_input_choice( $input, $setting ) {

  // Ensure input is a slug.
  $input = sanitize_key( $input );

  // Get list of choices from the control associated with the setting.
  $choices = $setting->manager->get_control( $setting->id )->choices;

  // If the input is a valid key, return it; otherwise, return the default.
  return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}
function a1_sanitize_email( $email, $setting ) {
    return ( is_email($email) ? $email : '' );
}

function a1_customize_register( $wp_customize ) {
$a1_options = get_option( 'a1_theme_options' );


  $wp_customize->add_panel(
    'general',
    array(
        'title' => __( 'General', 'a1' ),
        'description' => __('styling options','a1'),
        'priority' => 20, 
    )
  );

  $wp_customize->add_panel(
    'top-header-section',
    array(
        'title' => __( 'Top Header', 'a1' ),
        'description' => __('Top header styling options','a1'),
        'priority' => 20, 
    )
  );
  
   //All our sections, settings, and controls will be added here
  $wp_customize->add_section(
    'TopHeaderSocialLinks',
    array(
      'title' => __('Top Header Social Accounts', 'a1'),
      'priority' => 120,
      'description' => __( 'In first input box, you need to add FONT AWESOME shortcode which you can find ' ,  'a1').'<a target="_blank" href="'.esc_url('https://fortawesome.github.io/Font-Awesome/icons/').'">'.__('here' ,  'a1').'</a>'.__(' and in second input box, you need to add your social media profile URL.', 'a1').'<br />'.__(' Enter the URL of your social accounts. Leave it empty to hide the icon.' ,  'a1'),
      'panel' => 'top-header-section'
    )
  );

$TopHeaderSocialIconDefault = array(
  array('url'=>$a1_options['fburl'],'icon'=>'fa-facebook'),
  array('url'=>$a1_options['twitter'],'icon'=>'fa-twitter'),
  array('url'=>$a1_options['googleplus'],'icon'=>'fa-google-plus'),  
  array('url'=>$a1_options['pinterest'],'icon'=>'fa-pinterest'),  
  );

$TopHeaderSocialIcon = array();
  for($i=1;$i <= 4;$i++):  
    $TopHeaderSocialIcon[] =  array( 'slug'=>sprintf('TopHeaderSocialIcon%d',$i),   
      'default' => $TopHeaderSocialIconDefault[$i-1]['icon'],   
      'label' => esc_html__( 'Social Account ', 'a1') .$i,   
      'priority' => sprintf('%d',$i) );  
  endfor;
  foreach($TopHeaderSocialIcon as $TopHeaderSocialIcons){
    $wp_customize->add_setting(
      $TopHeaderSocialIcons['slug'],
      array( 
       'default' => $TopHeaderSocialIcons['default'],       
        'capability'     => 'edit_theme_options',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );
    $wp_customize->add_control(
      $TopHeaderSocialIcons['slug'],
      array(
        'type'  => 'text',
        'section' => 'TopHeaderSocialLinks',
        'input_attrs' => array( 'placeholder' => esc_attr__('Enter Icon','a1') ),
        'label'      =>   $TopHeaderSocialIcons['label'],
        'priority' => $TopHeaderSocialIcons['priority']
      )
    );
  }
  $TopHeaderSocialIconLink = array();
  for($i=1;$i <= 4;$i++):  
    $TopHeaderSocialIconLink[] =  array( 'slug'=>sprintf('TopHeaderSocialIconLink%d',$i),   
      'default' => $TopHeaderSocialIconDefault[$i-1]['url'],   
      'label' => esc_html__( 'Social Link ', 'a1' ) .$i,
      'priority' => sprintf('%d',$i) );  
  endfor;
  foreach($TopHeaderSocialIconLink as $TopHeaderSocialIconLinks){
    $wp_customize->add_setting(
      $TopHeaderSocialIconLinks['slug'],
      array(
        'default' => $TopHeaderSocialIconLinks['default'],
        'capability'     => 'edit_theme_options',
        'type' => 'theme_mod',
        'sanitize_callback' => 'esc_url_raw',
      )
    );
    $wp_customize->add_control(
      $TopHeaderSocialIconLinks['slug'],
      array(
        'type'  => 'text',
        'section' => 'TopHeaderSocialLinks',
        'priority' => $TopHeaderSocialIconLinks['priority'],
        'input_attrs' => array( 'placeholder' => esc_html__('Enter URL','a1')),
      )
    );
  }
  
  $wp_customize->get_section('title_tagline')->panel = 'general';
  $wp_customize->get_section('static_front_page')->panel = 'general';
  $wp_customize->get_section('header_image')->panel = 'general';
  $wp_customize->get_section('title_tagline')->title = __('Header & Logo','a1');
  
$wp_customize->add_section(
  'headerNlogo',
  array(
    'title' => __('Header & Logo','a1'),
    'panel' => 'general'
  )
);

$wp_customize->add_setting(
  'theme_logo_height',
  array(
    'default' => '',
    'capability'     => 'edit_theme_options',
    'sanitize_callback' => 'absint',
    )
  );
$wp_customize->add_control(
  'theme_logo_height',
  array(
    'section' => 'title_tagline',
    'label'      => __('Enter Logo Size', 'a1'),
    'description' => __("Use if you want to increase or decrease logo size (optional) Don't enter `px` in the string. e.g. 20 (default: 10px)",'a1'),
    'type'       => 'text',
    'priority'    => 50,
    )
  );


/* Basic Settings */
$wp_customize->add_section( 'basic_settings_section' ,
   array(
      'title'       => __( 'Basic Settings', 'a1' ),
      'priority'    => 35,
      'capability'     => 'edit_theme_options', 
     'panel' => 'top-header-section'  
  )
);

$wp_customize->add_setting(
  'theme_email_id',
  array(
    'default' => isset($a1_options['email'])?$a1_options['email']:'',
    'capability'     => 'edit_theme_options',
    'sanitize_callback' => 'sanitize_text_field',
    )
  );
$wp_customize->add_control(
  'theme_email_id',
  array(
    'section' => 'basic_settings_section',
    'label'      => __('Enter Email id', 'a1'),
    'description' => __("Enter e-mail id for your site , you would like to display in the Top Header.",'a1'),
    'type'       => 'text',
    'priority'    => 50,
    )
  );

$wp_customize->add_setting(
  'theme_phone_number',
  array(
    'default' => isset($a1_options['phone'])?$a1_options['phone']:'',
    'capability'     => 'edit_theme_options',
    'sanitize_callback' => 'sanitize_text_field',
    )
  );
$wp_customize->add_control(
  'theme_phone_number',
  array(
    'section' => 'basic_settings_section',
    'label'      => __('Enter Phone Number', 'a1'),
    'description' => __("Enter phone number for your site , you would like to display in the Top Header.",'a1'),
    'type'       => 'text',
    'priority'    => 54,
    )
  );
$wp_customize->add_setting(
  'a1-remove-breadcrumbs',
  array(
    'default' => true,
    'capability'     => 'edit_theme_options',
    'sanitize_callback' => 'a1_sanitize_checkbox',
    )
  );
$wp_customize->add_control(
  'a1-remove-breadcrumbs',
  array(
    'section' => 'basic_settings_section',
    'label'      => __('Check for show the breadcrumbs in site.', 'a1'),    
    'type'       => 'checkbox',
    'priority'    => 54,
    )
  );

$wp_customize->add_setting(
  'a1-remove-top-header',
  array(
    'default' => false,
    'capability'     => 'edit_theme_options',
    'sanitize_callback' => 'a1_sanitize_checkbox',
    )
  );
$wp_customize->add_control(
  'a1-remove-top-header',
  array(
    'section' => 'basic_settings_section',
    'label'      => __('Check this if you want to hide the top header.', 'a1'),    
    'type'       => 'checkbox',
    'priority'    => 54,
    )
  );
$wp_customize->add_setting(
  'a1-fixed-top-menu',
  array(
    'default' => false,
    'capability'     => 'edit_theme_options',
    'sanitize_callback' => 'a1_sanitize_checkbox',
    )
  );
$wp_customize->add_control(
  'a1-fixed-top-menu',
  array(
    'section' => 'basic_settings_section',
    'label'      => __('Check this if you want to have FIXED main menu.', 'a1'),    
    'type'       => 'checkbox',
    'priority'    => 54,
    )
  );

$wp_customize->add_setting(
  'a1_scroll_top_header',
  array(
    'default' => false,
    'capability'     => 'edit_theme_options',
    'sanitize_callback' => 'a1_sanitize_checkbox',
    )
  );
$wp_customize->add_control(
  'a1_scroll_top_header',
  array(
    'section' => 'basic_settings_section',
    'label'      => __('Check this if you want to display top header on scroll.', 'a1'),    
    'type'       => 'checkbox',
    'priority'    => 54,
    )
  );

/*-------------------- Blog Page Option Setting --------------------------*/

/* Front page First section */
$wp_customize->add_section( 'blog_page_metatag_section' ,
   array(
      'title'       => __( 'Blog (Archive) Page Options', 'a1' ),
      'priority'    => 32,
      'capability'     => 'edit_theme_options', 
     'panel' => 'general'  
  )
);
/*a1_homepage_sectionswitch*/
$wp_customize->add_setting( 'a1_blog_page_title',
    array(
        'default' => isset($a1_options['blogtitle'])?$a1_options['blogtitle']:'',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
        'priority' => 20, 
    )
);
$wp_customize->add_control( 'a1_blog_page_title',
    array(
        'section' => 'blog_page_metatag_section',                
        'label'   => __('Blog Title ','a1'),
        'type'    => 'text',
        'input_attrs' => array( 'placeholder' => esc_html__('Blog Title','a1')),
    )
);

$wp_customize->add_setting( 'a1_blog_entry_meta_by',
    array(
        'default' => isset($a1_options['entry-meta-by'])?$a1_options['entry-meta-by']:'',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
        'priority' => 20, 
    )
);
$wp_customize->add_control( 'a1_blog_entry_meta_by',
    array(
        'section' => 'blog_page_metatag_section',                
        'label'   => __('Entry meta by word ','a1'),
        'description' => 'Enter entry meta by word for your site , you would like to replace with current word.',
        'type'    => 'text',
        'input_attrs' => array( 'placeholder' => esc_html__('Entry meta by word','a1')),
    )
);

$wp_customize->add_setting( 'a1_blog_entry_meta_on',
    array(
        'default' => isset($a1_options['entry-meta-on'])?$a1_options['entry-meta-on']:'',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
        'priority' => 20, 
    )
);
$wp_customize->add_control( 'a1_blog_entry_meta_on',
    array(
        'section' => 'blog_page_metatag_section',                
        'label'   => __('Entry meta on word ','a1'),
        'description' => 'Enter entry meta on word for your site , you would like to replace with current word.',
        'type'    => 'text',
        'input_attrs' => array( 'placeholder' => esc_html__('Entry meta on word','a1')),
    )
);




$wp_customize->add_setting(
    'a1_blog_page_single_post',
    array(
        'default' => false,
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'a1_sanitize_checkbox',
    )
);
$wp_customize->add_control(
    'a1_blog_page_single_post',
    array(
        'section' => 'blog_page_metatag_section',
        'label'      => __('Check this to hide meta info on the single blog posts.. ', 'a1'),       
        'type'       => 'checkbox',        
    )
);

$wp_customize->add_setting(
    'a1_blog_page_blog_post',
    array(
        'default' => false,
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'a1_sanitize_checkbox',
    )
);
$wp_customize->add_control(
    'a1_blog_page_blog_post',
    array(
        'section' => 'blog_page_metatag_section',
        'label'      => __('Check this to hide meta info on all archieve pages (author, tag, date etc)..', 'a1'),       
        'type'       => 'checkbox',        
    )
);


/*-------------------- Home Page Option Setting --------------------------*/
$wp_customize->add_panel(
    'frontpage_section',
    array(
        'title' => __( 'Front Page Options', 'a1' ),
        'description' => __('Front Page options','a1'),
        'priority' => 20, 
    )
  );


$wp_customize->add_section( 'frontpage_slider_section' ,
   array(
      'title'       => __( 'Front Page : Banner Slider', 'a1' ),
      'priority'    => 32,
      'capability'     => 'edit_theme_options', 
      'panel' => 'frontpage_section'   
  )
);
/*frontpage_slider_sectionswitch*/
$wp_customize->add_setting(
    'frontpage_slider_sectionswitch',
    array(
        'default' => (!isset($a1_options['remove-slider']))?'1':'2',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'a1_field_sanitize_input_choice',
    )
);
$wp_customize->add_control(
    'frontpage_slider_sectionswitch',
    array(
        'section' => 'frontpage_slider_section',
        'label'      => __('Slider Section', 'a1'),
        'description' => __('Slider Section hide or show .','a1'),
        'type'       => 'select',
        'choices' => array(
          "1"   => esc_html__( "Show", 'a1' ),
          "2"   => esc_html__( "Hide", 'a1' ),      
        ),
    )
);

 for($i=1;$i <= 5;$i++):  

    $wp_customize->add_setting(
        'a1_homepage_sliderimage'.$i.'_image',
        array(
            'default' =>isset($a1_options['slider-img-'.$i])?a1_get_image_id($a1_options['slider-img-'.$i]):'',
            'capability'     => 'edit_theme_options',
            'sanitize_callback' => 'absint',
        )
    );
    $wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, 'a1_homepage_sliderimage'.$i.'_image', array(
        'section'     => 'frontpage_slider_section',
        'label'       => __( 'Upload Slider Image ' ,'a1').$i,
        'flex_width'  => true,
        'flex_height' => true,
        'width'       => 1350,
        'height'      => 550,   
        'default-image' => '',
    ) ) );

    $wp_customize->add_setting( 'a1_homepage_sliderimage'.$i.'_title',
        array(
            'default' => isset($a1_options['slidecaption-'.$i])?$a1_options['slidecaption-'.$i]:'',
            'capability'     => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
            'priority' => 20, 
        )
    );
    $wp_customize->add_control( 'a1_homepage_sliderimage'.$i.'_title',
        array(
            
            'section' => 'frontpage_slider_section',                
            'label'   => __('Enter Slider Title ','a1').$i,
            'type'    => 'text',
            'input_attrs' => array( 'placeholder' => esc_html__('Enter Slider Title','a1')),
        )
    ); 
    $wp_customize->add_setting( 'a1_homepage_sliderimage'.$i.'_content',
        array(
            'default' => isset($a1_options['slidebuttontext-'.$i])?$a1_options['slidebuttontext-'.$i]:'',
            'capability'     => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
            'priority' => 22, 
        )
    );
    $wp_customize->add_control( 'a1_homepage_sliderimage'.$i.'_content',
        array(            
            'section' => 'frontpage_slider_section',                
            'label'   => __('Enter Slider Button Text ','a1').$i,
            'type'    => 'text',
            'input_attrs' => array( 'placeholder' => esc_html__('Enter Slider Button Text','a1')),
        )
    );   
    $wp_customize->add_setting( 'a1_homepage_sliderimage'.$i.'_link',
        array(
            'default' => isset($a1_options['slidebuttonlink-'.$i])?$a1_options['slidebuttonlink-'.$i]:'',
            'capability'     => 'edit_theme_options',
            'sanitize_callback' => 'esc_url_raw',
            'priority' => 20, 
        )
    );
    $wp_customize->add_control( 'a1_homepage_sliderimage'.$i.'_link',
        array(
            'section' => 'frontpage_slider_section',                
            'label'   => __('Enter Slider URL ','a1').$i,
            'type'    => 'text',
            'input_attrs' => array( 'placeholder' => esc_html__('Enter Slider URL','a1')),
        )
    );
 endfor;

//Title Bar
$wp_customize->add_section( 'frontpage_title_bar_section' ,
   array(
      'title'       => __( 'Front Page : Core Features Section', 'a1' ),
      'priority'    => 32,
      'capability'     => 'edit_theme_options', 
      'panel' => 'frontpage_section'   
  )
);


/* Front page First section */
$wp_customize->add_section( 'frontpage_first_section' ,
   array(
      'title'       => __( 'Front Page : First Section', 'a1' ),
      'priority'    => 32,
      'capability'     => 'edit_theme_options', 
      'panel' => 'frontpage_section'   
  )
);
/*a1_homepage_sectionswitch*/
$wp_customize->add_setting(
    'a1_homepage_first_sectionswitch',
    array(
        'default' => (!isset($a1_options['remove-core-features']))?'1':'2',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'a1_field_sanitize_input_choice',
    )
);
$wp_customize->add_control(
    'a1_homepage_first_sectionswitch',
    array(
        'section' => 'frontpage_first_section',
        'label'      => __('Service Section', 'a1'),
        'description' => __('Service Section hide or show .','a1'),
        'type'       => 'select',
        'choices' => array(
          "1"   => esc_html__( "Show", 'a1' ),
          "2"   => esc_html__( "Hide", 'a1' ),      
        ),
    )
);

$wp_customize->add_setting( 'a1_homepage_section_title',
    array(
        'default' => isset($a1_options['coretitle'])?$a1_options['coretitle']:'',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
        'priority' => 20, 
    )
);
$wp_customize->add_control( 'a1_homepage_section_title',
    array(
        'section' => 'frontpage_first_section',                
        'label'   => __('Enter Service Title ','a1'),
        'type'    => 'text',
        'input_attrs' => array( 'placeholder' => esc_html__('Enter Service Title','a1')),
    )
);

$wp_customize->add_setting( 'a1_homepage_section_desc',
    array(  
        'default' => isset($a1_options['corecaption'])?$a1_options['corecaption']:'',    
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'wp_kses_post',
        'priority' => 20, 
    )
);
$wp_customize->add_control( 'a1_homepage_section_desc',
    array(
        'section' => 'frontpage_first_section',                
        'label'   => __('Enter Service Short Description ','a1'),        
        'type'    => 'textarea',
        'input_attrs' => array( 'placeholder' => esc_html__('Enter Service Short Description','a1')),
    )
); 



for($i=1;$i <= 3;$i++):
   
    $wp_customize->add_setting(
        'a1_homepage_first_section'.$i.'_icon',
        array(
            'default' =>isset($a1_options['home-icon-'.$i])?a1_get_image_id($a1_options['home-icon-'.$i]):'',
            'capability'     => 'edit_theme_options',
            'sanitize_callback' => 'absint',
        )
    );
    $wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, 'a1_homepage_first_section'.$i.'_icon', array(
        'section'     => 'frontpage_first_section',
        'label'       => __( 'Upload Icon should be (43px X 28px) ' ,'a1').$i,
        'flex_width'  => true,
        'flex_height' => false,
        'width'       => 43,
        'height'      => 28,   
        'default-image' => '',
    ) ) );


  $wp_customize->add_setting( 'a1_homepage_first_section'.$i.'_title',
      array(
          'default' => isset($a1_options['section-title-'.$i])?$a1_options['section-title-'.$i]:'',
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'sanitize_text_field',
          'priority' => 20, 
      )
  );
  $wp_customize->add_control( 'a1_homepage_first_section'.$i.'_title',
      array(
          'section' => 'frontpage_first_section',                
          'label'   => __('Enter Tab Title ','a1').$i,
          'type'    => 'text',
          'input_attrs' => array( 'placeholder' => esc_html__('Enter title','a1')),
      )
  );

  $wp_customize->add_setting( 'a1_homepage_first_section'.$i.'_desc',
      array( 
          'default' => isset($a1_options['section-content-'.$i])?$a1_options['section-content-'.$i]:'',     
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'wp_kses_post',
          'priority' => 20, 
      )
  );
  $wp_customize->add_control( 'a1_homepage_first_section'.$i.'_desc',
      array(
          'section' => 'frontpage_first_section',                
          'label'   => __('Enter Tab Description ','a1').$i,
          'type'    => 'textarea',
          'input_attrs' => array( 'placeholder' => esc_html__('Enter Description','a1')),
      )
  );

  $wp_customize->add_setting( 'a1_homepage_first_section'.$i.'_link',
      array(
          'default' => isset($a1_options['coresectionlink-' . $i])?$a1_options['coresectionlink-' . $i]:'',
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'esc_url_raw',
          'priority' => 20, 
      )
  );
  $wp_customize->add_control( 'a1_homepage_first_section'.$i.'_link',
      array(
          'section' => 'frontpage_first_section',                
          'label'   => __('Enter Tab Link ','a1').$i,
          'type'    => 'text',
          'input_attrs' => array( 'placeholder' => esc_html__('Enter URL','a1')),
      )
  ); 
endfor;

/* Front page Second section */
$wp_customize->add_section( 'frontpage_second_section' ,
   array(
      'title'       => __( 'Front Page : Second Section', 'a1' ),
      'priority'    => 32,
      'capability'     => 'edit_theme_options', 
      'panel' => 'frontpage_section'   
  )
);

/*a1_homepage_sectionswitch*/
$wp_customize->add_setting(
    'a1_homepage_second_sectionswitch',
    array(
        'default' => (!isset($a1_options['remove-product-description']))?'1':'2',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'a1_field_sanitize_input_choice',
    )
);
$wp_customize->add_control(
    'a1_homepage_second_sectionswitch',
    array(
        'section' => 'frontpage_second_section',
        'label'      => __('Second Section', 'a1'),
        'description' => __('Second Section hide or show .','a1'),
        'type'       => 'select',
        'choices' => array(
          "1"   => esc_html__( "Show", 'a1' ),
          "2"   => esc_html__( "Hide", 'a1' ),      
        ),
    )
);

$wp_customize->add_setting( 'a1_homepage_second_section_title',
      array(
          'default' => isset($a1_options['producttitle'])?$a1_options['producttitle']:'',
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'sanitize_text_field',
          'priority' => 20, 
      )
  );
  $wp_customize->add_control( 'a1_homepage_second_section_title',
      array(
          'section' => 'frontpage_second_section',                
          'label'   => __('Enter Second Section Title ','a1'),
          'type'    => 'text',
          'input_attrs' => array( 'placeholder' => esc_html__('Enter Title','a1')),
      )
  );
  
  $wp_customize->add_setting( 'a1_homepage_second_section_caption',
      array( 
          'default' => isset($a1_options['productcaption'])?$a1_options['productcaption']:'',     
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'wp_kses_post',
          'priority' => 20, 
      )
  );
  $wp_customize->add_control( 'a1_homepage_second_section_caption',
      array(
          'section' => 'frontpage_second_section',                
          'label'   => __('Enter Short Caption','a1'),
          'type'    => 'textarea',
          'input_attrs' => array( 'placeholder' => esc_html__('Enter Caption','a1')),
      )
  );

  $wp_customize->add_setting( 'a1_homepage_second_section_desc',
      array( 
          'default' => isset($a1_options['productcontent'])?$a1_options['productcontent']:'',     
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'wp_kses_post',
          'priority' => 20, 
      )
  );
  $wp_customize->add_control( 'a1_homepage_second_section_desc',
      array(
          'section' => 'frontpage_second_section',                
          'label'   => __('Enter Short Description','a1'),
          'type'    => 'textarea',
          'input_attrs' => array( 'placeholder' => esc_html__('Enter Description','a1')),
      )
  );
  $wp_customize->add_setting( 'a1_homepage_second_section_email',
      array(
          'default' => isset($a1_options['product-form-email'])?$a1_options['product-form-email']:'',
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'a1_sanitize_email',
          'priority' => 20, 
      )
  );
  $wp_customize->add_control( 'a1_homepage_second_section_email',
      array(
          'section' => 'frontpage_second_section',                
          'label'   => __('Enter Second Section Email ','a1'),
          'type'    => 'email',
          'input_attrs' => array( 'placeholder' => esc_html__('Enter Email','a1')),
      )
  );

/* End Front page Second section */

/* Front page Third section */
$wp_customize->add_section( 'frontpage_third_section' ,
   array(
      'title'       => __( 'Front Page : Third Section', 'a1' ),
      'priority'    => 32,
      'capability'     => 'edit_theme_options', 
      'panel' => 'frontpage_section'   
  )
);

/*a1_homepage_sectionswitch*/
$wp_customize->add_setting(
    'a1_homepage_third_sectionswitch',
    array(
        'default' => (!isset($a1_options['remove-getin-touch']))?'1':'2',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'a1_field_sanitize_input_choice',
    )
);
$wp_customize->add_control(
    'a1_homepage_third_sectionswitch',
    array(
        'section' => 'frontpage_third_section',
        'label'      => __('Third Section', 'a1'),
        'description' => __('Third Section hide or show .','a1'),
        'type'       => 'select',
        'choices' => array(
          "1"   => esc_html__( "Show", 'a1' ),
          "2"   => esc_html__( "Hide", 'a1' ),      
        ),
    )
);

$wp_customize->add_setting( 'a1_homepage_third_section_title',
      array(
          'default' => isset($a1_options['get-touch-title'])?$a1_options['get-touch-title']:'',
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'sanitize_text_field',
          'priority' => 20, 
      )
  );
  $wp_customize->add_control( 'a1_homepage_third_section_title',
      array(
          'section' => 'frontpage_third_section',                
          'label'   => __('Enter Third Section Title ','a1'),
          'type'    => 'text',
          'input_attrs' => array( 'placeholder' => esc_html__('Enter Title','a1')),
      )
  );
  
  $wp_customize->add_setting( 'a1_homepage_third_section_caption',
      array( 
          'default' => isset($a1_options['get-touch-caption'])?$a1_options['get-touch-caption']:'',     
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'wp_kses_post',
          'priority' => 20, 
      )
  );
  $wp_customize->add_control( 'a1_homepage_third_section_caption',
      array(
          'section' => 'frontpage_third_section',                
          'label'   => __('Enter Short Caption','a1'),
          'type'    => 'textarea',
          'input_attrs' => array( 'placeholder' => esc_html__('Enter Caption','a1')),
      )
  );

  $wp_customize->add_setting(
        'a1_homepage_third_section_image',
        array(
            'default' =>isset($a1_options['get-touch-logo'])?a1_get_image_id($a1_options['get-touch-logo']):'',
            'capability'     => 'edit_theme_options',
            'sanitize_callback' => 'absint',
        )
    );
    $wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, 'a1_homepage_third_section_image', array(
        'section'     => 'frontpage_third_section',
        'label'       => __( 'Upload Third Section Get In Touch Logo ' ,'a1'),
        'flex_width'  => true,
        'flex_height' => true,
        'width'       => 100,
        'height'      => 100,   
        'default-image' => '',
    ) ) );

  $wp_customize->add_setting( 'a1_homepage_third_section_btn_title',
      array(
          'default' => isset($a1_options['contactus-now-text'])?$a1_options['contactus-now-text']:'',
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'sanitize_text_field',
          'priority' => 20, 
      )
  );
  $wp_customize->add_control( 'a1_homepage_third_section_btn_title',
      array(
          'section' => 'frontpage_third_section',                
          'label'   => __('Enter Third Section Button Title ','a1'),
          'type'    => 'text',
          'input_attrs' => array( 'placeholder' => esc_html__('Enter Button Title','a1')),
      )
  );

  $wp_customize->add_setting( 'a1_homepage_third_section_btn_link',
      array(
          'default' => isset($a1_options['get-touch-page'])?$a1_options['get-touch-page']:'',
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'esc_url_raw',
          'priority' => 20, 
      )
  );
  $wp_customize->add_control( 'a1_homepage_third_section_btn_link',
      array(
          'section' => 'frontpage_third_section',                
          'label'   => __('Enter Third Section Button Link','a1'),
          'type'    => 'text',
          'input_attrs' => array( 'placeholder' => esc_html__('Enter Button URL','a1')),
      )
  ); 
  


//Footer Section
$wp_customize->add_section( 'footerCopyright' , array(
    'title'       => __( 'Footer', 'a1' ),
    'priority'    => 100,
    'capability'     => 'edit_theme_options',
  ) );

$wp_customize->add_setting(
    'footer-content',
    array(
        'default' => $a1_options['footer-content'],
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'wp_kses_post',
        'priority' => 21, 
    )
);
$wp_customize->add_control(
    'footer-content',
    array(
        'section' => 'footerCopyright',                
        'label'   => __('Enter Footer Content Text','a1'),
        'description'   => __('Enter content for your site , you would like to display in the Footer.','a1'),        
        'type'    => 'textarea',
    )
);

$wp_customize->add_setting(
    'footertext',
    array(
        'default' => $a1_options['footertext'],
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'wp_kses_post',
        'priority' => 20, 
    )
);
$wp_customize->add_control(
    'footertext',
    array(
        'section' => 'footerCopyright',                
        'label'   => __('Enter Copyright Text','a1'),
        'description'   => __('Copyright text for the footer of your website.','a1'),        
        'type'    => 'textarea',
    )
);



// Text Panel Starts Here 

}
add_action( 'customize_register', 'a1_customize_register' );

function a1_custom_css(){
  wp_enqueue_style('a1-style',get_stylesheet_uri(),array(),NULL);  
  $custom_css='';
  
  $theme_logo_height = (get_theme_mod('theme_logo_height'))?(get_theme_mod('theme_logo_height')):50;
  $custom_css.= ".logo-fixed img{ max-height: ".esc_attr($theme_logo_height)."px;   }";

  wp_add_inline_style( 'a1-style', $custom_css ); 
}