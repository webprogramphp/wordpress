<?php
 
function stla_margin_padding_controls( $wp_customize, $current_form_id, $section, $setting_type, $setting_name){
	

	$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'['.$setting_type.']['.$setting_name.'-top]' , array(
		'default'     => '',
		'transport'   => 'postMessage',
		'type' => 'option'
	) );
	
	$wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'['.$setting_type.']['.$setting_name.'-top]',   array(
	  'type' => 'text',
	  'priority' => 10, // Within the section.
	  'section' => $section, // Required, core or custom.
	  'label' => __( 'Top' )
	)
	);

	$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'['.$setting_type.']['.$setting_name.'-bottom]' , array(
		'default'     => '',
		'transport'   => 'postMessage',
		'type' => 'option'
	) );
	
	$wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'['.$setting_type.']['.$setting_name.'-bottom]',   array(
	  'type' => 'text',
	  'priority' => 10, // Within the section.
	  'section' => $section, // Required, core or custom.
	  'label' => __( 'Bottom' )
	)
	);

	$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'['.$setting_type.']['.$setting_name.'-left]' , array(
		'default'     => '',
		'transport'   => 'postMessage',
		'type' => 'option'
	) );
	
	$wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'['.$setting_type.']['.$setting_name.'-left]',   array(
	  'type' => 'text',
	  'priority' => 10, // Within the section.
	  'section' => $section, // Required, core or custom.
	  'label' => __( 'Left' )
	)
	);

	$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'['.$setting_type.']['.$setting_name.'-right]' , array(
		'default'     => '',
		'transport'   => 'postMessage',
		'type' => 'option'
	) );
	
	$wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'['.$setting_type.']['.$setting_name.'-right]',   array(
	  'type' => 'text',
	  'priority' => 10, // Within the section.
	  'section' => $section, // Required, core or custom.
	  'label' => __( 'Right' )
	)
	);
	

	
}