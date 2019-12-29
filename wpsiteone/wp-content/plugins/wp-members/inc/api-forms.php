<?php
/**
 * WP-Members API Functions
 * 
 * This file is part of the WP-Members plugin by Chad Butler
 * You can find out more about this plugin at https://rocketgeek.com
 * Copyright (c) 2006-2019  Chad Butler
 * WP-Members(tm) is a trademark of butlerblog.com
 *
 * @package WP-Members
 * @subpackage WP-Members API Functions
 * @author Chad Butler 
 * @copyright 2006-2019
 *
 * Functions included:
 *
 * - wpmem_register_form
 * - wpmem_form_field
 * - wpmem_form_label
 * - wpmem_fields
 */

/**
 * Invokes a login form.
 *
 * @since 3.2.0
 *
 * @global object $wpmem
 * @param  array  $args {
 *     Possible arguments for creating the form.
 *
 *     @type string id
 *     @type string tag
 *     @type string form
 *     @type string redirect_to
 * }
 * @return string $html
 */
/*function wpmem_login_form( $args ) {
  global $wpmem;
  return $wpmem->forms->login_form( $args );
}*/

/**
 * Invokes a registration or user profile update form.
 *
 * @since 3.2.0
 *
 * @global object $wpmem
 * @param  array  $args {
 *     Possible arguments for creating the form.
 *
 *     @type string id
 *     @type string tag
 *     @type string form
 *     @type string product
 *     @type string include_fields
 *     @type string exclude_fields
 *     @type string redirect_to
 *     @type string heading
 * }
 * @return string $html
 */
function wpmem_register_form( $args ) {
  global $wpmem;
  return $wpmem->forms->register_form( $args );
}

/**
 * Wrapper for $wpmem->create_form_field().
 *
 * @since 3.1.2
 * @since 3.2.0 Accepts wpmem_create_formfield() arguments.
 *
 * @global object $wpmem    The WP_Members object class.
 * @param string|array  $args {
 *     @type string  $name        (required) The field meta key.
 *     @type string  $type        (required) The field HTML type (url, email, image, file, checkbox, text, textarea, password, hidden, select, multiselect, multicheckbox, radio).
 *     @type string  $value       (optional) The field's value (can be a null value).
 *     @type string  $compare     (optional) Compare value.
 *     @type string  $class       (optional) Class identifier for the field.
 *     @type boolean $required    (optional) If a value is required default: true).
 *     @type string  $delimiter   (optional) The field delimiter (pipe or comma, default: | ).
 *     @type string  $placeholder (optional) Defines the placeholder attribute.
 *     @type string  $pattern     (optional) Adds a regex pattern to the field (HTML5).
 *     @type string  $title       (optional) Defines the title attribute.
 *     @type string  $min         (optional) Adds a min attribute (HTML5).
 *     @type string  $max         (optional) Adds a max attribute (HTML5).
 *     @type string  $rows        (optional) Adds rows attribute to textarea.
 *     @type string  $cols        (optional) Adds cols attribute to textarea.
 * }
 * @param  string $type     The field type.
 * @param  string $value    The default value for the field.
 * @param  string $valtochk Optional for comparing the default value of the field.
 * @param  string $class    Optional for setting a specific CSS class for the field.
 * @return string           The HTML of the form field.
 */
//function wpmem_form_field( $args ) {
function wpmem_form_field( $name, $type=null, $value=null, $valtochk=null, $class='textbox' ) {
	global $wpmem;
	if ( is_array( $name ) ) {
		$args = $name;
	} else {
		$args = array(
			'name'     => $name,
			'type'     => $type,
			'value'    => $value,
			'compare'  => $valtochk,
			'class'    => $class,
		);
	}
	return $wpmem->forms->create_form_field( $args );
}

/**
 * Wrapper for $wpmem->create_form_label().
 *
 * @since 3.1.7
 *
 * @global object $wpmem
 * @param array  $args {
 *     @type string $meta_key
 *     @type string $label
 *     @type string $type
 *     @type string $id         (optional)
 *     @type string $class      (optional)
 *     @type string $required   (optional)
 *     @type string $req_mark   (optional)
 * }
 * @return string The HTML of the form label.
 */
function wpmem_form_label( $args ) {
	global $wpmem;
	return $wpmem->forms->create_form_label( $args );
}

/**
 * Wrapper to get form fields.
 *
 * @since 3.1.1
 * @since 3.1.5 Checks if fields array is set or empty before returning.
 * @since 3.1.7 Added wpmem_form_fields filter.
 *
 * @global object $wpmem  The WP_Members object.
 * @param  string $tag    The action being used (default: null).
 * @param  string $form   The form being generated.
 * @return array  $fields The form fields.
 */
function wpmem_fields( $tag = '', $form = 'default' ) {
	global $wpmem;
	// Load fields if none are loaded.
	if ( ! isset( $wpmem->fields ) || empty( $wpmem->fields ) ) {
		$wpmem->load_fields( $form );
	}
	
	// @todo Review for removal.
	$tag = $wpmem->convert_tag( $tag );
	
	/**
	 * Filters the fields array.
	 *
	 * @since 3.1.7
	 *
	 * @param  array  $wpmem->fields
	 * @param  string $tag (optional)
	 */
	return apply_filters( 'wpmem_fields', $wpmem->fields, $tag );
}