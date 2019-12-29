<?php
//form dropdown section
/* Start of Section */
$wp_customize->add_section( 'gf_stla_form_id_dropdown_fields' , array(
    'title' => 'Dropdown Fields',
    'panel' => 'gf_stla_panel',
  ) );
//label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][max-width-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Width' ),
      'section' => 'gf_stla_form_id_dropdown_fields',
      'settings' => array(),
    )
  )

);
/* for pc */
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][max-width]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][max-width]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 400px'
  )
  )
  )
);

/*for tablet */
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][max-width-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][max-width-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 400px '
  )
  )
  )
);
/*for mobile */
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][max-width-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][max-width-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 400px'
  )
  )
  )
);


/* Start of Section */
// Height label.
$wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][height-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Height' ),
      'section' => 'gf_stla_form_id_dropdown_fields',
      'settings' => array(),
    )
  )

);

  /* for pc */
  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][height]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][height]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 50px '
  )
  )
  )
);
  /* for tablet */
  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][height-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][height-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 50px '
  )
  )
  )
);
   /* for mobile */
  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][height-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][height-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 50px '
  )
  )
  )
);

// font style buttons
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][font-style]' , array(
	'default'     => '',
	'transport'   => 'postMessage',
	'type' => 'option'
) );

$wp_customize->add_control( new Stla_Font_Style_Option ( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][font-style]', array(
	'label'	      =>  'Font Style',
	'section'     => 'gf_stla_form_id_dropdown_fields',
	'type'        => 'font_style',
	'choices'     => $font_style_choices,
) ) );

/* Start of Section */
//label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][font-size-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Font Size' ),
      'section' => 'gf_stla_form_id_dropdown_fields',
      'settings' => array(),
    )
  )

);

/* for pc */
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][font-size]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][font-size]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);

/* for tablet */
     $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][font-size-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][font-size-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);

/* for mobile */
     $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][font-size-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][font-size-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);
// not needed there because it's not work for dropdonw field.
/* Start of Section */
 //Line height label
//  $wp_customize->add_control(
//   new WP_Customize_Label_Only(
//     $wp_customize, // WP_Customize_Manager
//     'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][line-height-label-only]', // Setting id
//     array( // Args, including any custom ones.
//       'label' => __( 'Line Height' ),
//       'section' => 'gf_stla_form_id_dropdown_fields',
//       'settings' => array(),
//     )
//   )

// );
//  /* for pc*/
//  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][line-height]' , array(
//       'default'     => '',
//       'transport'   => 'refresh',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][line-height]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
//     'label' => __( '' ),
//    'input_attrs' => array(
//     'placeholder' => 'Ex: 40px'
//   )
//   )
//   )
// );
//   /* for_tablet*/
//    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][line-height-tab]' , array(
//       'default'     => '',
//       'transport'   => 'refresh',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][line-height-tab]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
//     'label' => __( '' ),
//    'input_attrs' => array(
//     'placeholder' => 'Ex: 40px'
//   )
//   )
//   )
// );

  
//   /* for mobile*/
//    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][line-height-phone]' , array(
//       'default'     => '',
//       'transport'   => 'refresh',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][line-height-phone]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
//     'label' => __( '' ),
//    'input_attrs' => array(
//     'placeholder' => 'Ex: 40px'
//   )
//   )
//   )
// );

/* Start of Section */
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][font-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][font-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Font Color' ),
      'section' => 'gf_stla_form_id_dropdown_fields',
    )
  )
);

//Label
$wp_customize->add_control(
	new WP_Customize_Label_Only(
	  $wp_customize, // WP_Customize_Manager
	  'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][border-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Border' ),
		'section' => 'gf_stla_form_id_dropdown_fields',
		'settings' => array(),
	  )
	)
  );

  /* Start of Section */
  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][border-size]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[dropdown-fields][border-size]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
    'label' => __( 'Size' ),
   'input_attrs' => array(
    'placeholder' => 'Example: 4px or 10%'
  )
  )
);

/* Start of Section */
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][border-type]' , array(
      'default'     => 'solid',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[dropdown-fields][border-type]',   array(
    'type' => 'select',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
    'label' => __( 'Type' ),
    'choices'        => $border_types,
  )
);

/* Start of Section */
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][border-radius]' , array(
  'default'     => '',
  'transport'   => 'postMessage',
  'type' => 'option'
) );

$wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[dropdown-fields][border-radius]',   array(
'type' => 'text',
'priority' => 10, // Within the section.
'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
'label' => __( 'Radius' ),
'input_attrs' => array(
'placeholder' => 'Example: 4px or 10%'
)
)
);

/* Start of Section */
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][border-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][border-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Border Color' ),
      'section' => 'gf_stla_form_id_dropdown_fields',
    )
  )
);



 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][background-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][background-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Background Color' ),
      'section' => 'gf_stla_form_id_dropdown_fields',
    )
  )
);


//   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][margin]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[dropdown-fields][margin]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
//     'label' => __( 'Margin' ),
//    'input_attrs' => array(
//     'placeholder' => 'Example: 5px 10px 5px 10px'
//   )
//   )
// );

//    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][padding]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[dropdown-fields][padding]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_dropdown_fields', // Required, core or custom.
//     'label' => __( 'Padding' ),
//    'input_attrs' => array(
//     'placeholder' => 'Example: 5px 10px 5px 10px'
//   )
//   )
// );

// Start of Section
//Label
$wp_customize->add_control(
	new WP_Customize_Label_Only(
	  $wp_customize, // WP_Customize_Manager
	  'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][margin-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Margin' ),
		'section' => 'gf_stla_form_id_dropdown_fields',
		'settings' => array(),
	  )
	)
  );

  stla_margin_padding_controls( $wp_customize, $current_form_id, 'gf_stla_form_id_dropdown_fields', 'dropdown-fields', 'margin' );

// Start of Section
//Label
$wp_customize->add_control(
	new WP_Customize_Label_Only(
	  $wp_customize, // WP_Customize_Manager
	  'gf_stla_form_id_'.$current_form_id.'[dropdown-fields][padding-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Padding' ),
		'section' => 'gf_stla_form_id_dropdown_fields',
		'settings' => array(),
	  )
	)
  );
  stla_margin_padding_controls( $wp_customize, $current_form_id, 'gf_stla_form_id_dropdown_fields', 'dropdown-fields', 'padding' );