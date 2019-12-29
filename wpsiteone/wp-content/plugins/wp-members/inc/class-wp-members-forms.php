<?php
/**
 * The WP_Members Forms Class.
 *
 * @package WP-Members
 * @subpackage WP_Members Forms Object Class
 * @since 3.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class WP_Members_Forms {

	/**
	 * Plugin initialization function.
	 *
	 * @since 3.1.0
	 */
	function __construct() {
		
	}
	
	/**
	 * Creates form fields
	 *
	 * Creates various form fields and returns them as a string.
	 *
	 * @since 3.1.0
	 * @since 3.1.1 Added $delimiter.
	 * @since 3.1.2 Changed $valtochk to $compare.
	 * @since 3.1.6 Added $placeholder.
	 * @since 3.1.7 Added number type & $min, $max, $title and $pattern attributes.
	 * @since 3.2.0 Added $id argument.
	 * @since 3.2.4 Added radio group and multiple checkbox individual item labels.
	 *
	 * @global object $wpmem The WP_Members object class.
	 * @param  array  $args {
	 *     @type string  $id
	 *     @type string  $name
	 *     @type string  $type
	 *     @type string  $value
	 *     @type string  $compare
	 *     @type string  $class
	 *     @type boolean $required
	 *     @type string  $delimiter
	 *     @type string  $placeholder
	 *     @type string  $pattern
	 *     @type string  $title
	 *     @type string  $min
	 *     @type string  $max
	 *     @type string  $rows Number of rows for a textarea (default:5).
	 *     @type string  $cols Number of columns for a textarea (default:20).
	 * }
	 * @return string $str The field returned as a string.
	 */
	function create_form_field( $args ) {
		
		global $wpmem;
		
		// Set defaults for most possible $args.
		$id          = ( isset( $args['id'] ) ) ? esc_attr( $args['id'] ) : esc_attr( $args['name'] );
		$name        = esc_attr( $args['name'] );
		$type        = esc_attr( $args['type'] );
		$value       = ( isset( $args['value']       ) ) ? maybe_unserialize( $args['value'] ) : '';
		$compare     = ( isset( $args['compare']     ) ) ? $args['compare']     : '';
		$class       = ( isset( $args['class']       ) ) ? $args['class']       : 'textbox';
		$required    = ( isset( $args['required']    ) ) ? $args['required']    : false;
		$delimiter   = ( isset( $args['delimiter']   ) ) ? $args['delimiter']   : '|';
		$placeholder = ( isset( $args['placeholder'] ) ) ? $args['placeholder'] : false;
		$pattern     = ( isset( $args['pattern']     ) ) ? $args['pattern']     : false;
		$title       = ( isset( $args['title']       ) ) ? $args['title']       : false;
	
		// Handle field creation by type.
		switch ( $type ) { 

		/*
		 * Field types text|url|email|number|date are all handled essentially the 
		 * same. The primary differences are CSS class (with a default fallback
		 * of 'textbox'), how values are escaped, and the application of min|max
		 * values for number fields.
		 */
		case "text":
		case "url":
		case "email":
		case "number":
		case "date":
			$class = ( 'textbox' == $class ) ? "textbox" : $this->sanitize_class( $class );
			switch ( $type ) {
				case 'url':
					$value = esc_url( $value );
					break;
				case 'email':
					$value = esc_attr( wp_unslash( $value ) );
					break;
				default:
					$value = stripslashes( esc_attr( $value ) ); // @todo Could email and default be combined? Both seem to unslash and esc_attr().
					break;
			}
			$required    = ( $required    ) ? ' required' : '';
			$placeholder = ( $placeholder ) ? ' placeholder="' . esc_attr( __( $placeholder, 'wp-members' ) ) . '"' : '';
			$pattern     = ( $pattern     ) ? ' pattern="' . esc_attr( $pattern ) . '"' : '';
			$title       = ( $title       ) ? ' title="' . esc_attr( __( $title, 'wp-members' ) ) . '"' : '';
			$min         = ( isset( $args['min'] ) && $args['min'] != '' ) ? ' min="' . esc_attr( $args['min'] ) . '"' : '';
			$max         = ( isset( $args['max'] ) && $args['max'] != '' ) ? ' max="' . esc_attr( $args['max'] ). '"' : '';
			$str = "<input name=\"$name\" type=\"$type\" id=\"$id\" value=\"$value\" class=\"$class\"$placeholder$title$pattern$min$max" . ( ( $required ) ? " required " : "" ) . " />";
			break;
		
		case "password":
			$class = $this->sanitize_class( $class );
			$placeholder = ( $placeholder ) ? ' placeholder="' . esc_attr( __( $placeholder, 'wp-members' ) ) . '"' : '';
			$pattern     = ( $pattern     ) ? ' pattern="' . esc_attr( $pattern ) . '"' : '';
			$title       = ( $title       ) ? ' title="' . esc_attr( __( $title, 'wp-members' ) ) . '"' : '';
			$str = "<input name=\"$name\" type=\"$type\" id=\"$id\" class=\"$class\"$placeholder$title$pattern" . ( ( $required ) ? " required " : "" ) . " />";
			break;
		
		case "image":
		case "file":
			$class = ( 'textbox' == $class ) ? "file" : $this->sanitize_class( $class );
			$str = "<input name=\"$name\" type=\"file\" id=\"$id\" value=\"$value\" class=\"$class\"" . ( ( $required ) ? " required " : "" ) . " />";
			break;
	
		case "checkbox":
			$class = ( 'textbox' == $class ) ? "checkbox" : $this->sanitize_class( $class );
			$str = "<input name=\"$name\" type=\"$type\" id=\"$id\" value=\"" . esc_attr( $value ) . "\"" . checked( $value, $compare, false ) . ( ( $required ) ? " required " : "" ) . " />";
			break;
	
		case "textarea":
			$value = esc_textarea( stripslashes( $value ) ); // stripslashes( esc_textarea( $value ) );
			$class = ( 'textbox' == $class ) ? "textarea" : $this->sanitize_class( $class );
			$placeholder = ( $placeholder ) ? ' placeholder="' . esc_attr( __( $placeholder, 'wp-members' ) ) . '"' : '';
			$rows  = ( isset( $args['rows'] ) && $args['rows'] ) ? esc_attr( $args['rows'] ) : '5';
			$cols  = ( isset( $args['cols'] ) && $args['cols'] ) ? esc_attr( $args['cols'] ) : '20';
			$str = "<textarea cols=\"$cols\" rows=\"$rows\" name=\"$name\" id=\"$id\" class=\"$class\"$placeholder" . ( ( $required ) ? " required " : "" ) . ">$value</textarea>";
			break;
	
		case "hidden":
			$str = "<input name=\"$name\" type=\"$type\" value=\"" . esc_attr( $value ) . "\" />";
			break;
	
		case "option":
			$str = "<option value=\"" . esc_attr( $value ) . "\" " . selected( $value, $compare, false ) . " >" . __( $name, 'wp-members' ) . "</option>";
			break;
	
		case "select":
		case "multiselect":
		case "membership":
			$class = ( 'textbox' == $class && 'multiselect' != $type ) ? "dropdown"    : $class;
			$class = ( 'textbox' == $class && 'multiselect' == $type ) ? "multiselect" : $class;
			$pname = ( 'multiselect' == $type ) ? $name . "[]" : $name;
			$str = "<select name=\"$pname\" id=\"$id\" class=\"$class\"" . ( ( 'multiselect' == $type ) ? " multiple " : "" ) . ( ( $required ) ? " required " : "" ) . ">\n";
			if ( 'membership' == $type ) {
				$value = array( 'Choose membership|' );
				foreach( $wpmem->membership->products as $membership_key => $membership_value ) {
					$value[] = $membership_value['title'] . '|' . $membership_key;
				}
			}
			foreach ( $value as $option ) {
				$pieces = explode( '|', $option );
				if ( 'multiselect' == $type ) {
					$chk = '';
					$values = ( empty( $compare ) ) ? array() : ( is_array( $compare ) ? $compare : explode( $delimiter, $compare ) );
				} else {
					$chk = $compare;
					$values = array();
				}
				if ( isset( $pieces[1] ) && '' != $pieces[1] ) {
					$chk = ( ( isset( $pieces[2] ) && '' == $compare ) || in_array( $pieces[1], $values ) ) ? $pieces[1] : $chk;
				} else {
					$chk = 'not selected';
				}
				$str = $str . "<option value=\"$pieces[1]\"" . selected( $pieces[1], $chk, false ) . ">" . esc_attr( __( $pieces[0], 'wp-members' ) ) . "</option>\n";
			}
			$str = $str . "</select>";
			break;
			
		case "multicheckbox":
			$class = ( 'textbox' == $class ) ? "checkbox" : $class;
			$str = '';
			$num = 1;
			foreach ( $value as $option ) {
				$pieces = explode( '|', $option );
				$values = ( empty( $compare ) ) ? array() : ( is_array( $compare ) ? $compare : explode( $delimiter, $compare ) );
				$chk = ( isset( $pieces[2] ) && '' == $compare ) ? $pieces[1] : '';
				if ( isset( $pieces[1] ) && '' != $pieces[1] ) {
					$id_value = esc_attr( $id . '[' . $pieces[1] . ']' );
					$label = wpmem_form_label( array( 'meta_key'=>$id_value, 'label'=>esc_html( __( $pieces[0], 'wp-members' ) ), 'type'=>'radio', 'id'=>$id_value ) );
					$str = $str . $this->create_form_field( array(
						'id'      => $id_value,
						'name'    => $name . '[]',
						'type'    => 'checkbox',
						'value'   => $pieces[1],
						'compare' => ( in_array( $pieces[1], $values ) ) ? $pieces[1] : $chk,
					) ) . "&nbsp;" . $label . "<br />\n";
				} else {
					$str = $str . '<span class="div_multicheckbox_separator">' . esc_html( __( $pieces[0], 'wp-members' ) ) . "</span><br />\n";
				}
			}
			break;
			
		case "radio":
			$class = ( 'textbox' == $class ) ? "radio" : $this->sanitize_class( $class );
			$str = '';
			$num = 1;
			foreach ( $value as $option ) {
				$pieces = explode( '|', $option );
				$id_num = $id . '_' . $num;
				if ( isset( $pieces[1] ) && '' != $pieces[1] ) {
					$label = wpmem_form_label( array( 'meta_key'=>esc_attr( $id_num ), 'label'=>esc_html( __( $pieces[0], 'wp-members' ) ), 'type'=>'radio', 'id'=>esc_attr( "label_" . $id_num ) ) );
					$str = $str . "<input type=\"radio\" name=\"$name\" id=\"" . esc_attr( $id_num ) . "\" value=\"" . esc_attr( $pieces[1] ) . '"' . checked( $pieces[1], $compare, false ) . ( ( $required ) ? " required " : " " ) . "> $label<br />\n";
					$num++;
				} else {
					$str = $str . '<span class="div_radio_separator">' . esc_html( __( $pieces[0], 'wp-members' ) ) . "</span><br />\n";
				}
			}
			break;		
	
		} 
	
		return $str;
	} // End create_form_field()
	
	/**
	 * Create form label.
	 *
	 * @since 3.1.7
	 * @since 3.2.4 Added $id
	 *
	 * @param array  $args {
	 *     @type string $meta_key
	 *     @type string $label
	 *     @type string $type
	 *     @type string $id       (optional)
	 *     @type string $class    (optional)
	 *     @type string $required (optional)
	 *     @type string $req_mark (optional)
	 * }
	 * @return string $label
	 */
	function create_form_label( $args ) {
		global $wpmem;
		
		$meta_key   = $args['meta_key'];
		$label      = $args['label'];
		$type       = $args['type'];
		$class      = ( isset( $args['class']    ) ) ? $args['class']    : false;
		$id         = ( isset( $args['id']       ) ) ? $args['id']       : false;
		$required   = ( isset( $args['required'] ) ) ? $args['required'] : false;
		$req_mark   = ( isset( $args['req_mark'] ) ) ? $args['req_mark'] : false;
		
		//$req_mark = ( ! $req_mark ) ? $wpmem->get_text( 'register_req_mark' ) : '*';
		
		if ( ! $class ) {
			$class = ( $type == 'password' || $type == 'email' || $type == 'url' ) ? 'text' : $type;
		}
		
		$id = ( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$label = '<label for="' . esc_attr( $meta_key ) . '"' . $id . ' class="' . $this->sanitize_class( $class ) . '">' . __( $label, 'wp-members' );
		$label = ( $required ) ? $label . $req_mark : $label;
		$label = $label . '</label>';
		
		return $label;
	}
	
	/**
	 * Sanitizes classes passed to the WP-Members form building functions.
	 *
	 * This generally uses just sanitize_html_class() but allows for 
	 * whitespace so multiple classes can be passed (such as "regular-text code").
	 *
	 * @since 3.2.0
	 *
	 * @param	string $class
	 * @return	string sanitized_class
	 */
	function sanitize_class( $class ) {
		// If no whitespace, just return WP sanitized class.
		if ( ! strpos( $class, ' ' ) ) {
			return sanitize_html_class( $class );
		} else {
			// Break string by whitespace, sanitize individual class names.
			$class_array = explode( ' ', $class );
			$len = count( $class_array ); $i = 0;
			$sanitized_class = '';
			foreach ( $class_array as $single_class ) {
				$sanitized_class .= sanitize_html_class( $single_class );
				$sanitized_class .= ( $i == $len - 1 ) ? '' : ' ';
				$i++;
			}
			return $sanitized_class;
		}
	}
	/**
	 * Uploads file from the user.
	 *
	 * @since 3.1.0
	 *
	 * @param  array    $file
	 * @param  int      $user_id
	 * @return int|bool
	 */
	function do_file_upload( $file = array(), $user_id = false ) {
		
		// Filter the upload directory.
		add_filter( 'upload_dir', array( &$this,'file_upload_dir' ) );
		
		// Set up user ID for use in upload process.
		$this->file_user_id = ( $user_id ) ? $user_id : 0;
	
		// Get WordPress file upload processing scripts.
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		
		$file_return = wp_handle_upload( $file, array( 'test_form' => false ) );
	
		if ( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
			return false;
		} else {
	
			$attachment = array(
				'post_mime_type' => $file_return['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_return['file'] ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
				'guid'           => $file_return['url'],
				'post_author'    => ( $user_id ) ? $user_id : '',
			);
	
			$attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );
	
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file_return['file'] );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );
	
			if ( 0 < intval( $attachment_id ) ) {
				// Returns an array with file information.
				return $attachment_id;
			}
		}
	
		return false;
	} // End upload_file()
	
	/**
	 * Sets the file upload directory.
	 *
	 * This is a filter function for upload_dir.
	 *
	 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/upload_dir
	 *
	 * @since 3.1.0
	 *
	 * @param  array $param {
	 *     The directory information for upload.
	 *
	 *     @type string $path
	 *     @type string $url
	 *     @type string $subdir
	 *     @type string $basedir
	 *     @type string $baseurl
	 *     @type string $error
	 * }
	 * @return array $param
	 */
	function file_upload_dir( $param ) {
		$user_id  = ( isset( $this->file_user_id ) ) ? $this->file_user_id : null;
		
		$args = array(
			'user_id'   => $user_id,
			'wpmem_dir' => 'wpmembers/',
			'user_dir'  => 'user_files/' . $user_id,
		);
		/**
		 * Filter the user directory elements.
		 *
		 * @since 3.1.0
		 *
		 * @param array $args
		 */
		$args = apply_filters( 'wpmem_user_upload_dir', $args );

		$param['subdir'] = '/' . $args['wpmem_dir'] . $args['user_dir'];
		$param['path']   = $param['basedir'] . '/' . $args['wpmem_dir'] . $args['user_dir'];
		$param['url']    = $param['baseurl'] . '/' . $args['wpmem_dir'] . $args['user_dir'];
	
		return $param;
	}

	/**
	 * Login Form Builder.
	 *
	 * Builds the form used for login, change password, and reset password.
	 *
	 * @since 2.5.1
	 * @since 3.1.7 Moved to forms object class as login_form().
	 * @since 3.1.7 Added WP action login_form.
	 * @since 3.2.6 Added nonce to the short form.
	 *
	 * @param  string $page 
	 * @param  array  $arr {
	 *     The elements needed to generate the form (login|reset password|forgotten password).
	 *
	 *     @type string $heading     Form heading text.
	 *     @type string $action      The form action (login|pwdchange|pwdreset|getusername).
	 *     @type string $button_text Form submit button text.
	 *     @type array  $inputs {
	 *         The form input values.
	 *
	 *         @type array {
	 *
	 *             @type string $name  The field label.
	 *             @type string $type  Input type.
	 *             @type string $tag   Input tag name.
	 *             @type string $class Input tag class.
	 *             @type string $div   Div wrapper class.
	 *         }
	 *     }
	 *     @type string $redirect_to Optional. URL to redirect to.
	 * }
	 * @return string $form  The HTML for the form as a string.
	 */
	function login_form( $mixed, $arr = array() ) {

		// Handle legacy use.
		if ( is_array( $mixed ) ) {
			$page = $mixed['page'];
			$arr  = $mixed;
		} else {
			$page = $mixed;
		}
		
		
		// Set up redirect_to @todo This could be done in a separate method usable by both login & reg.
		if ( isset( $_REQUEST['redirect_to'] ) ) {
			$redirect_to = $_REQUEST['redirect_to'];
		} else {
			if ( isset( $arr['redirect_to'] ) ) {
				$redirect_to = $arr['redirect_to'];
			} else {
				$redirect_to = ( isset( $_SERVER['REQUEST_URI'] ) ) ? $_SERVER['REQUEST_URI'] : get_permalink();
			}
		}

		global $wpmem;

		// set up default wrappers
		$defaults = array(

			// wrappers
			'heading_before'  => '<legend>',
			'heading_after'   => '</legend>',
			'fieldset_before' => '<fieldset>',
			'fieldset_after'  => '</fieldset>',
			'main_div_before' => '<div id="wpmem_login">',
			'main_div_after'  => '</div>',
			'txt_before'      => '',
			'txt_after'       => '',
			'row_before'      => '',
			'row_after'       => '',
			'buttons_before'  => '<div class="button_div">',
			'buttons_after'   => '</div>',
			'link_before'     => '<div class="link-text">',
			'link_after'      => '</div>',
			'link_span_before' => '<span class="link-text-%s">',
			'link_span_after'  => '</span>',

			// classes & ids
			'form_id'         => 'wpmem_' . $arr['action'] . '_form',
			'form_class'      => 'form',
			'button_id'       => '',
			'button_class'    => 'buttons',

			// other
			'strip_breaks'    => true,
			'wrap_inputs'     => true,
			'remember_check'  => true,
			'n'               => "\n",
			't'               => "\t",
			'redirect_to'     => $redirect_to,
			'login_form_action' => true,

		);

		/**
		 * Filter the default form arguments.
		 *
		 * This filter accepts an array of various elements to replace the form defaults. This
		 * includes default tags, labels, text, and small items including various booleans.
		 *
		 * @since 2.9.0
		 *
		 * @param array                 An array of arguments to merge with defaults. Default null.
		 * @param string $arr['action'] The action being performed by the form. login|pwdreset|pwdchange|getusername.
		 */
		$args = apply_filters( 'wpmem_login_form_args', '', $arr['action'] );

		// Merge $args with defaults.
		$args = wp_parse_args( $args, $defaults );

		// Build the input rows.
		foreach ( $arr['inputs'] as $input ) {
			$label = '<label for="' . esc_attr( $input['tag'] ) . '">' . $input['name'] . '</label>';
			$field = wpmem_create_formfield( $input['tag'], $input['type'], '', '', $input['class'] );
			$field_before = ( $args['wrap_inputs'] ) ? '<div class="' . $this->sanitize_class( $input['div'] ) . '">' : '';
			$field_after  = ( $args['wrap_inputs'] ) ? '</div>' : '';
			$rows[] = array( 
				'row_before'   => $args['row_before'],
				'label'        => $label,
				'field_before' => $field_before,
				'field'        => $field,
				'field_after'  => $field_after,
				'row_after'    => $args['row_after'],
			);
		}

		/**
		 * Filter the array of form rows.
		 *
		 * This filter receives an array of the main rows in the form, each array element being
		 * an array of that particular row's pieces. This allows making changes to individual 
		 * parts of a row without needing to parse through a string of HTML.
		 *
		 * @since 2.9.0
		 * @since 3.2.6 Added $arr parameter so all settings are passed.
		 *
		 * @param array  $rows          An array containing the form rows.
		 * @param string $arr['action'] The action being performed by the form. login|pwdreset|pwdchange|getusername.
		 * @param array  $arr           An array containing all of the form settings.
		 */
		$rows = apply_filters( 'wpmem_login_form_rows', $rows, $arr['action'], $arr );

		// Put the rows from the array into $form.
		$form = '';
		foreach ( $rows as $row_item ) {
			$row  = ( $row_item['row_before']   != '' ) ? $row_item['row_before'] . $args['n'] . $row_item['label'] . $args['n'] : $row_item['label'] . $args['n'];
			$row .= ( $row_item['field_before'] != '' ) ? $row_item['field_before'] . $args['n'] . $args['t'] . $row_item['field'] . $args['n'] . $row_item['field_after'] . $args['n'] : $row_item['field'] . $args['n'];
			$row .= ( $row_item['row_after']    != '' ) ? $row_item['row_after'] . $args['n'] : '';
			$form.= $row;
		}

		// Handle outside elements added to the login form (currently ONLY for login).
		if ( 'login' == $arr['action'] && $args['login_form_action'] ) {
			ob_start();
			/** This action is documented in wp-login.php */
			do_action( 'login_form' );
			$add_to_form = ob_get_contents();
			ob_end_clean();
			$form.= $add_to_form;
		}

		// Build hidden fields, filter, and add to the form.
		$hidden = wpmem_create_formfield( 'redirect_to', 'hidden', esc_url( $args['redirect_to'] ) ) . $args['n'];
		$hidden = $hidden . wpmem_create_formfield( 'a', 'hidden', $arr['action'] ) . $args['n'];
		$hidden = ( $arr['action'] != 'login' ) ? $hidden . wpmem_create_formfield( 'formsubmit', 'hidden', '1' ) : $hidden;

		/**
		 * Filter the hidden field HTML.
		 *
		 * @since 2.9.0
		 *
		 * @param string $hidden        The generated HTML of hidden fields.
		 * @param string $arr['action'] The action being performed by the form. login|pwdreset|pwdchange|getusername.
		 */
		$form = $form . apply_filters( 'wpmem_login_hidden_fields', $hidden, $arr['action'] );

		// Build the buttons, filter, and add to the form.
		if ( $arr['action'] == 'login' ) {
			$args['remember_check'] = ( $args['remember_check'] ) ? $args['t'] . wpmem_create_formfield( 'rememberme', 'checkbox', 'forever' ) . '&nbsp;' . '<label for="rememberme">' . $wpmem->get_text( 'remember_me' ) . '</label>&nbsp;&nbsp;' . $args['n'] : '';
			$buttons =  $args['remember_check'] . $args['t'] . '<input type="submit" name="Submit" value="' . esc_attr( $arr['button_text'] ) . '" class="' . $this->sanitize_class( $args['button_class'] ) . '" />' . $args['n'];
		} else {
			$buttons = '<input type="submit" name="Submit" value="' . esc_attr( $arr['button_text'] ) . '" class="' . $this->sanitize_class( $args['button_class'] ) . '" />' . $args['n'];
		}

		/**
		 * Filter the HTML for form buttons.
		 *
		 * The string includes the buttons, as well as the before/after wrapper elements.
		 *
		 * @since 2.9.0
		 *
		 * @param string $buttons        The generated HTML of the form buttons.
		 * @param string $arr['action']  The action being performed by the form. login|pwdreset|pwdchange|getusername.
		 */
		$form = $form . apply_filters( 'wpmem_login_form_buttons', $args['buttons_before'] . $args['n'] . $buttons . $args['buttons_after'] . $args['n'], $arr['action'] );

		$links_array = array(
			'forgot' => array(
				'tag'  => 'forgot',
				'link' => add_query_arg( 'a', 'pwdreset', $wpmem->user_pages['profile'] ),
				'page' => 'profile',
				'action' => 'login',
			),
			'register' => array(
				'tag'  => 'reg',
				'link' => $wpmem->user_pages['register'],
				'page' => 'register',
				'action' => 'login',
			),
			'username' => array(
				'tag'  => 'username',
				'link' => add_query_arg( 'a', 'getusername', $wpmem->user_pages['profile'] ),
				'page' => 'profile',
				'action' => 'pwdreset',
			),
		);
		foreach ( $links_array as $key => $value ) {
			$tag = $value['tag'];
			if ( ( $wpmem->user_pages[ $value['page'] ] || 'members' == $page ) && $value['action'] == $arr['action'] ) {
				/**
				 * Filters register, forgot password, and forgot username links.
				 *
				 * @since 2.8.0
				 * @since 3.1.7 Combined all to a single process.
				 * @since 3.2.5 Added $tag parameter.
				 *
				 * @param string The raw link.
				 * @param string $tag forgot|reg|pwdreset.
				 */
				$link = apply_filters( "wpmem_{$tag}_link", $value['link'], $tag );
				$str  = $wpmem->get_text( "{$key}_link_before" ) . '<a href="' . esc_url( $link ) . '">' . $wpmem->get_text( "{$key}_link" ) . '</a>';
				$link_str = $args['link_before'];
				$link_str.= ( '' != $args['link_span_before'] ) ? sprintf( $args['link_span_before'], $key ) : '';
				/**
				 * Filters the register, forgot password, and forgot username links HTML.
				 *
				 * @since 2.9.0
				 * @since 3.0.9 Added $link parameter.
				 * @since 3.1.7 Combined all to a single process.
				 * @since 3.2.5 Added $tag parameter.
				 *
				 * @param string $str  The link HTML.
				 * @param string $link The link.
				 * @param string $tag  forgot|reg|pwdreset.
				 */
				$link_str.= apply_filters( "wpmem_{$tag}_link_str", $str, $link, $tag );
				$link_str.= ( '' != $args['link_span_after'] ) ? $args['link_span_after'] : '';
				$link_str.= $args['link_after'] . $args['n'];
				/*
				 * If this is the register link, and the current post type is set to
				 * display the register form, and the current page is not the login
				 * page, then do not add the register link, otherwise add the link.
				 */
				if ( 'register' == $key ) {
					if ( ! isset( $wpmem->user_pages['register'] ) || '' == $wpmem->user_pages['register'] ) {
						$form = $form;
					} else {
						if ( isset( $wpmem->user_pages['login'] ) && '' != $wpmem->user_pages['login'] ) {
							$form = ( 1 == $wpmem->show_reg[ get_post_type( get_the_ID() ) ] && wpmem_current_url( true, false ) != wpmem_login_url() ) ? $form : $form . $link_str;
						} else {
							global $post;
							if ( has_shortcode( $post->post_content, 'wpmem_profile' ) ) {
								$form = $form;
							} else {
								$form = ( 1 == $wpmem->show_reg[ get_post_type( get_the_ID() ) ] && ! has_shortcode( $post->post_content, 'wpmem_form' ) ) ? $form : $form . $link_str;
							}
						}
					}
				} else {
					$form = $form . $link_str;
				}
			}
		}

		// Apply the heading.
		$form = $args['heading_before'] . $arr['heading'] . $args['heading_after'] . $args['n'] . $form;

		// Apply fieldset wrapper.
		$form = $args['fieldset_before'] . $args['n'] . $form . $args['fieldset_after'] . $args['n'];
		
		// Apply nonce.
		$form = wp_nonce_field( 'wpmem_shortform_nonce', '_wpmem_' . $arr['action'] . '_nonce', true, false ) . $args['n'] . $form;

		// Apply form wrapper.
		$form = '<form action="' . esc_url( get_permalink() ) . '" method="POST" id="' . $this->sanitize_class( $args['form_id'] ) . '" class="' . $this->sanitize_class( $args['form_class'] ) . '">' . $args['n'] . $form . '</form>';

		// Apply anchor.
		$form = '<a id="' . esc_attr( $arr['action'] ) . '"></a>' . $args['n'] . $form;

		// Apply main wrapper.
		$form = $args['main_div_before'] . $args['n'] . $form . $args['n'] . $args['main_div_after'];

		// Apply wpmem_txt wrapper.
		$form = $args['txt_before'] . $form . $args['txt_after'];

		// Remove line breaks.
		$form = ( $args['strip_breaks'] ) ? str_replace( array( "\n", "\r", "\t" ), array( '','','' ), $form ) : $form;

		/**
		 * Filter the generated HTML of the entire form.
		 *
		 * @since 2.7.4
		 *
		 * @param string $form          The HTML of the final generated form.
		 * @param string $arr['action'] The action being performed by the form. login|pwdreset|pwdchange|getusername.
		 */
		$form = apply_filters( 'wpmem_login_form', $form, $arr['action'] );

		/**
		 * Filter before the form.
		 *
		 * This rarely used filter allows you to stick any string onto the front of
		 * the generated form.
		 *
		 * @since 2.7.4
		 *
		 * @param string $str           The HTML to add before the form. Default null.
		 * @param string $arr['action'] The action being performed by the form. login|pwdreset|pwdchange|getusername.
		 */
		$form = apply_filters( 'wpmem_login_form_before', '', $arr['action'] ) . $form;

		return $form;
	} // End login_form.

	/**
	 * Registration Form Builder.
	 *
	 * Outputs the form for new user registration and existing user edits.
	 *
	 * @since 2.5.1
	 * @since 3.1.7 Moved to forms object class as register_form().
	 * @since 3.2.5 use_nonce now obsolete (nonce is added automatically).
	 *
	 * @global object $wpmem        The WP_Members object.
	 * @global string $wpmem_regchk Used to determine if the form is in an error state.
	 * @global array  $userdata     Used to get the user's registration data if they are logged in (user profile edit).
	 * @param  mixed  $mixed        (optional) String toggles between new registration ('new') and user profile edit ('edit'), or array containing settings arguments.
	 * @param  string $heading      (optional) The heading text for the form, null (default) for new registration.
	 * @return string $form         The HTML for the entire form as a string.
	 */
	function register_form( $mixed = 'new', $heading = '', $redirect_to = null ) {
		
		// Handle legacy use.
		if ( is_array( $mixed ) ) {
			$id          = ( isset( $mixed['id']          ) ) ? $mixed['id']          : '';
			$tag         = ( isset( $mixed['tag']         ) ) ? $mixed['tag']         : 'new';
			$heading     = ( isset( $mixed['heading']     ) ) ? $mixed['heading']     : '';
			$redirect_to = ( isset( $mixed['redirect_to'] ) ) ? $mixed['redirect_to'] : '';
		} else {
			$tag = $mixed;
		}

		global $wpmem, $wpmem_regchk, $userdata; 

		// Set up default wrappers.
		$defaults = array(

			// Wrappers.
			'heading_before'   => '<legend>',
			'heading_after'    => '</legend>',
			'fieldset_before'  => '<fieldset>',
			'fieldset_after'   => '</fieldset>',
			'main_div_before'  => '<div id="wpmem_reg">',
			'main_div_after'   => '</div>',
			'txt_before'       => '',
			'txt_after'        => '',
			'row_before'       => '',
			'row_after'        => '',
			'buttons_before'   => '<div class="button_div">',
			'buttons_after'    => '</div>',

			// Classes & ids.
			'form_id'          => ( 'new' == $tag ) ? 'wpmem_register_form' : 'wpmem_profile_form',
			'form_class'       => 'form',
			'button_id'        => '',
			'button_class'     => 'buttons',

			// Required field tags and text.
			'req_mark'         => $wpmem->get_text( 'register_req_mark' ),
			'req_label'        => $wpmem->get_text( 'register_required' ),
			'req_label_before' => '<div class="req-text">',
			'req_label_after'  => '</div>',

			// Buttons.
			'show_clear_form'  => false,
			'clear_form'       => $wpmem->get_text( 'register_clear' ),
			'submit_register'  => $wpmem->get_text( 'register_submit' ),
			'submit_update'    => $wpmem->get_text( 'profile_submit' ),

			// Other.
			'post_to'          => get_permalink(),
			'strip_breaks'     => true,
			'wrap_inputs'      => true,
			'n'                => "\n",
			't'                => "\t",

		);

		/**
		 * Filter the default form arguments.
		 *
		 * This filter accepts an array of various elements to replace the form defaults. This
		 * includes default tags, labels, text, and small items including various booleans.
		 *
		 * @since 2.9.0
		 * @since 3.2.5 Added $id
		 *
		 * @param array        An array of arguments to merge with defaults. Default null.
		 * @param string $tag  Toggle new registration or profile update. new|edit.
		 * @param string $id   An id for the form (optional).
		 */
		$args = apply_filters( 'wpmem_register_form_args', '', $tag, $id );

		// Merge $args with defaults.
		$args = wp_parse_args( $args, $defaults );
		
		// Get fields.
		$wpmem_fields = wpmem_fields( $tag );
		
		// Fields to skip for user profile update.

		if ( 'edit' == $tag ) {
			$pass_arr = array( 'username', 'password', 'confirm_password', 'password_confirm' );
			// Skips tos on user edit page, unless they haven't got a value for tos.
			if ( isset( $wpmem_fields['tos'] ) && ( $wpmem_fields['tos']['checked_value'] == get_user_meta( $userdata->ID, 'tos', true ) ) ) { 
				$pass_arr[] = 'tos';
			}
			foreach ( $pass_arr as $pass ) {
				unset( $wpmem_fields[ $pass ] );
			}
		}
		
		/**
		 * Filter the array of form fields.
		 *
		 * The form fields are stored in the WP options table as wpmembers_fields. This
		 * filter can filter that array after the option is retreived before the fields
		 * are parsed. This allows you to change the fields that may be used in the form
		 * on the fly.
		 *
		 * @since 2.9.0
		 * @deprecated 3.1.7 Use wpmem_fields instead.
		 *
		 * @param array        The array of form fields.
		 * @param string $tag  Toggle new registration or profile update. new|edit.
		 */
		$wpmem_fields = apply_filters( 'wpmem_register_fields_arr', $wpmem_fields, $tag );

		$hidden_rows = array();

		// Loop through the remaining fields.
		foreach ( $wpmem_fields as $meta_key => $field ) {

			// Start with a clean row.
			$val = ''; $label = ''; $input = ''; $field_before = ''; $field_after = '';

			// If the field is set to display and we aren't skipping, construct the row.
			if ( $field['register'] ) {
				
				// Handle hidden fields
				if ( 'hidden' == $field['type'] ) {
					$do_row = false;
					$hidden_rows[ $meta_key ] = wpmem_form_field( array( 
						'name'     => $meta_key,
						'type'     => $field['type'],
						'value'    => $field['value'],
						'compare'  => $valtochk,
						'required' => $field['required'],
					) );
				}

				// Label for all but TOS and hidden fields.
				if ( 'tos' != $meta_key && 'hidden' != $field['type'] ) {

					$class = ( $field['type'] == 'password' || $field['type'] == 'email' || $field['type'] == 'url' ) ? 'text' : $field['type'];
					
					$label = wpmem_form_label( array(
						'meta_key' => $meta_key, //( 'username' == $meta_key ) ? 'user_login' : $meta_key, 
						'label'    => __( $field['label'], 'wp-members' ), 
						'type'     => $field['type'], 
						'class'    => $class, 
						'required' => $field['required'], 
						'req_mark' => $args['req_mark'] 
					) );

				} 

				// Gets the field value for edit profile.
				if ( ( 'edit' == $tag ) && ( '' == $wpmem->regchk ) ) {
					switch ( $meta_key ) {
						case( 'description' ):
						case( 'textarea' == $field['type'] ):
							$val = get_user_meta( $userdata->ID, $meta_key, 'true' ); // esc_textarea() is run when field is created.
							break;

						case 'user_email':
						case 'confirm_email':
							$val = sanitize_email( $userdata->user_email );
							break;

						case 'user_url':
							$val = $userdata->user_url; // esc_url() is run when the field is created.
							break;

						case 'display_name':
							$val = sanitize_text_field( $userdata->display_name );
							break; 

						default:
							$val = sanitize_text_field( get_user_meta( $userdata->ID, $meta_key, 'true' ) );
							break;
					}

				} else {
					if ( 'file' == $field['type'] ) {
						$val = ( isset( $_FILES[ $meta_key ]['name'] ) ) ? $_FILES[ $meta_key ]['name'] : '' ;
					} else {
						$val = ( isset( $_POST[ $meta_key ] ) ) ? $_POST[ $meta_key ] : '';
					}
				}

				// Does the tos field.
				if ( 'tos' == $meta_key ) {

					$val = sanitize_text_field( wpmem_get( $meta_key, '' ) ); 

					// Should be checked by default? and only if form hasn't been submitted.
					$val   = ( ! $_POST && $field['checked_default'] ) ? $field['checked_value'] : $val;
					$input = wpmem_form_field( array(
						'name'     => $meta_key, 
						'type'     => $field['type'], 
						'value'    => $field['checked_value'], 
						'compare'  => $val,
						'required' => $field['required'],
					) );
					$input = ( $field['required'] ) ? $input . $args['req_mark'] : $input;

					// Determine if TOS is a WP page or not.
					$tos_content = stripslashes( get_option( 'wpmembers_tos' ) );
					if ( has_shortcode( $tos_content, 'wpmem_tos' ) || has_shortcode( $tos_content, 'wp-members' ) ) {	
						$tos_link_url = do_shortcode( $tos_content );
						$tos_link_tag = '<a href="' . esc_url( $tos_link_url ) . '" target="_blank">';
					} else {
						$tos_link_url = WPMEM_DIR . "/wp-members-tos.php";
						$tos_link_tag = "<a href=\"#\" onClick=\"window.open('" . $tos_link_url . "','mywindow');\">";
					}
					
					/**
					 * Filter the TOS link.
					 *
					 * @since 3.2.6
					 *
					 * @param string $tos_link_tag
					 * @param string $tos_link_url
					 */
					$tos_link_tag = apply_filters( 'wpmem_tos_link_tag', $tos_link_tag, $tos_link_url );

					/**
					 * Filter the TOS link text.
					 *
					 * @since 2.7.5
					 *
					 * @param string       The link text.
					 * @param string $tag  Toggle new registration or profile update. new|edit.
					 */
					$tos_link_text = apply_filters( 'wpmem_tos_link_txt', $wpmem->get_text( 'register_tos' ), $tag );
					
					// If filtered value is not the default label, use that, otherwise use label.
					// @note: if default changes, this check must change.
					if ( __( 'Please indicate that you agree to the %s Terms of Service %s', 'wp-members' ) == $tos_link_text ) {
						if ( __( 'TOS', 'wp-members' ) != $field['label'] && __( 'Terms of Service', 'wp-members' ) != $field['label'] ) {
							$tos_link_text = $field['label'];
						}
					}
					
					// If tos string does not contain link identifiers (%s), wrap the whole string.
					if ( ! strpos( $tos_link_text, '%s' ) ) {
						$tos_link_text = '%s' . $tos_link_text . '%s';
					}
					
					$input .= ' ' . sprintf( $tos_link_text, $tos_link_tag, '</a>' );

					// In previous versions, the div class would end up being the same as the row before.
					$field_before = ( $args['wrap_inputs'] ) ? '<div class="div_text">' : '';
					$field_after  = ( $args['wrap_inputs'] ) ? '</div>' : '';

				} elseif ( 'hidden' != $field['type'] ) {

					// For checkboxes.
					if ( 'checkbox' == $field['type'] ) { 
						$valtochk = $val;
						$val = $field['checked_value']; 
						// if it should it be checked by default (& only if form not submitted), then override above...
						if ( $field['checked_default'] && ( ! $_POST && $tag != 'edit' ) ) { $val = $valtochk = $field['checked_value']; }
					}

					// For dropdown select.
					if ( $field['type'] == 'select' || $field['type'] == 'radio' || $field['type'] == 'multiselect' || $field['type'] == 'multicheckbox' ) {
						$valtochk = $val;
						$val = $field['values'];
					}

					if ( ! isset( $valtochk ) ) { $valtochk = ''; }

					if ( 'edit' == $tag && ( 'file' == $field['type'] || 'image' == $field['type'] ) ) {

						$attachment_url = wp_get_attachment_url( $val );
						$empty_file = '<span class="description">' . __( 'None' ) . '</span>';
						if ( 'file' == $field['type'] ) {
							$input = ( $attachment_url ) ? '<a href="' . esc_url( $attachment_url ) . '">' . get_the_title( $val ) . '</a>' : $empty_file;
						} else {
							$input = ( $attachment_url ) ? '<img src="' . esc_url( $attachment_url ) . '">' : $empty_file;
						}
						$input.= '<br />' . $wpmem->get_text( 'profile_upload' ) . '<br />';
						$input.= wpmem_form_field( array(
							'name'    => $meta_key, 
							'type'    => $field['type'], 
							'value'   => $val, 
							'compare' => $valtochk,
						) );

					} else {

						// For all other input types.
						$formfield_args = array( 
							'name'     => $meta_key, // ( 'username' == $meta_key ) ? 'user_login' : $meta_key,
							'type'     => $field['type'],
							'value'    => $val,
							'compare'  => $valtochk,
							//'class'    => ( $class ) ? $class : 'textbox',
							'required' => $field['required'],
							'placeholder' => ( isset( $field['placeholder'] ) ) ? $field['placeholder'] : '',
							'pattern'     => ( isset( $field['pattern']     ) ) ? $field['pattern']     : false,
							'title'       => ( isset( $field['title']       ) ) ? $field['title']       : false,
							'min'         => ( isset( $field['min']         ) ) ? $field['min']         : false,
							'max'         => ( isset( $field['max']         ) ) ? $field['max']         : false,
							'rows'        => ( isset( $field['rows']        ) ) ? $field['rows']        : false,
							'cols'        => ( isset( $field['cols']        ) ) ? $field['cols']        : false,
						);
						if ( 'multicheckbox' == $field['type'] || 'multiselect' == $field['type'] ) {
							$formfield_args['delimiter'] = $field['delimiter'];
						}
						$input = wpmem_form_field( $formfield_args );

					}

					// Determine input wrappers.
					$field_before = ( $args['wrap_inputs'] ) ? '<div class="div_' . $class . '">' : '';
					$field_after  = ( $args['wrap_inputs'] ) ? '</div>' : '';
				}

			}

			// If the row is set to display, add the row to the form array.
			if ( $field['register'] && 'hidden' != $field['type'] ) {

				$values = '';
				if ( 'multicheckbox' == $field['type'] || 'select' == $field['type'] || 'multiselect' == $field['type'] || 'radio' == $field['type'] ) {
					$values = $val;
					$val = $valtochk;
				}

				$rows[ $meta_key ] = array(
					'meta'         => $meta_key,
					'type'         => $field['type'],
					'value'        => $val,
					'values'       => $values,
					'label_text'   => __( $field['label'], 'wp-members' ),
					'row_before'   => $args['row_before'],
					'label'        => $label,
					'field_before' => $field_before,
					'field'        => $input,
					'field_after'  => $field_after,
					'row_after'    => $args['row_after'],
				);
			}
		}

		// If captcha is Really Simple CAPTCHA.
		if ( $wpmem->captcha == 2 && $tag != 'edit' ) {
			$row = wpmem_build_rs_captcha();
			$rows['captcha'] = array(
				'meta'         => '', 
				'type'         => 'text', 
				'value'        => '',
				'values'       => '',
				'label_text'   => $row['label_text'],
				'row_before'   => $args['row_before'],
				'label'        => $row['label'],
				'field_before' => ( $args['wrap_inputs'] ) ? '<div class="div_text">' : '',
				'field'        => $row['field'],
				'field_after'  => ( $args['wrap_inputs'] ) ? '</div>' : '',
				'row_after'    => $args['row_after'],
			);
		}

		/**
		 * Filter the array of form rows.
		 *
		 * This filter receives an array of the main rows in the form, each array element being
		 * an array of that particular row's pieces. This allows making changes to individual 
		 * parts of a row without needing to parse through a string of HTML.
		 *
		 * @since 2.9.0
		 * @since 3.0.9 Added $rows['label_text'].
		 * @since 3.1.0 Added $rows['key'].
		 * @since 3.1.6 Deprecated $rows['order'].
		 *
		 * @param array  $rows    {
		 *     An array containing the form rows. 
		 *
		 *     @type string order        Field display order. (deprecated as of 3.1.6)
		 *     @type string meta         Field meta tag (not used for display).
		 *     @type string type         Input field type (not used for display).
		 *     @type string value        Input field value (not used for display).
		 *     @type string values       Possible field values (dropdown, multiple select/check, radio).
		 *     @type string label_text   Raw text for the label (not used for display).
		 *     @type string row_before   Opening wrapper tag around the row.
		 *     @type string label        Label tag.
		 *     @type string field_before Opening wrapper tag before the input tag.
		 *     @type string field        The field input tag.
		 *     @type string field_after  Closing wrapper tag around the input tag.
		 *     @type string row_after    Closing wrapper tag around the row.
		 * }
		 * @param string $tag  Toggle new registration or profile update. new|edit.
		 */
		$rows = apply_filters( 'wpmem_register_form_rows', $rows, $tag );
		
		// Make sure all keys are set just in case someone didn't return a proper array through the filter.
		// @todo Merge this with the next foreach loop so we only have to foreach one time.
		$row_keys = array( 'meta', 'type', 'value', 'values', 'label_text', 'row_before', 'label', 'field_before', 'field', 'field_after', 'row_after' );
		foreach ( $rows as $meta_key => $row ) {
			foreach ( $row_keys as $check_key ) {
				$rows[ $meta_key ][ $check_key ] = ( isset( $rows[ $meta_key ][ $check_key ] ) ) ? $rows[ $meta_key ][ $check_key ] : '';
			}
		}

		// Put the rows from the array into $form.
		$form = ''; $enctype = '';
		foreach ( $rows as $row_item ) {
			// Check form to see if we need multipart enctype.
			$enctype = ( $row_item['type'] == 'file' ||  $row_item['type'] == 'image' ) ? "multipart/form-data" : $enctype;
			// Assemble row pieces.
			$row  = ( $row_item['row_before']   != '' ) ? $row_item['row_before'] . $args['n'] . $row_item['label'] . $args['n'] : $row_item['label'] . $args['n'];
			$row .= ( $row_item['field_before'] != '' ) ? $row_item['field_before'] . $args['n'] . $args['t'] . $row_item['field'] . $args['n'] . $row_item['field_after'] . $args['n'] : $row_item['field'] . $args['n'];
			$row .= ( $row_item['row_after']    != '' ) ? $row_item['row_after'] . $args['n'] : '';
			$form.= $row;
		}

		// Do recaptcha if enabled.
		if ( ( $wpmem->captcha == 1 || $wpmem->captcha == 3 ) && $tag != 'edit' ) { // don't show on edit page!

			// Get the captcha options.
			$wpmem_captcha = get_option( 'wpmembers_captcha' ); 

			// Start with a clean row.
			$row = '';
			$row = '<div class="clear"></div>';
			$row.= '<div class="captcha">' . wpmem_inc_recaptcha( $wpmem_captcha['recaptcha'] ) . '</div>';

			// Add the captcha row to the form.
			/**
			 * Filter the HTML for the CAPTCHA row.
			 *
			 * @since 2.9.0
			 *
			 * @param string       The HTML for the entire row (includes HTML tags plus reCAPTCHA).
			 * @param string $tag  Toggle new registration or profile update. new|edit.
			 */
			$form.= apply_filters( 'wpmem_register_captcha_row', $args['row_before'] . $row . $args['row_after'], $tag );
		}

		// Create hidden fields.
		$var         = ( $tag == 'edit' ) ? 'update' : 'register';
		$redirect_to = ( isset( $_REQUEST['redirect_to'] ) ) ? $_REQUEST['redirect_to'] : ( ( $redirect_to ) ? $redirect_to : get_permalink() );
		$hidden_rows['_wpmem_a']        = '<input name="a" type="hidden" value="' . esc_attr( $var ) . '" />';
		$hidden_rows['_wpmem_reg_page'] = '<input name="wpmem_reg_page" type="hidden" value="' . esc_url( get_permalink() ) . '" />';
		if ( $redirect_to != get_permalink() ) {
			$hidden_rows['_wpmem_redirect_to'] = '<input name="redirect_to" type="hidden" value="' . esc_url( $redirect_to ) . '" />';
		}
		
		/**
		 * Filter the hidden form rows.
		 *
		 * @since 3.2.0
		 *
		 * @param array  $hidden_rows
		 * @param string $tag
		 */
		$hidden_rows = apply_filters( 'wpmem_register_hidden_rows', $hidden_rows, $tag );
		
		// Assemble hidden fields HTML.
		$hidden = '';
		foreach ( $hidden_rows as $hidden_row ) {
			$hidden .= $hidden_row . $args['n'];
		}

		/**
		 * Filter the hidden field HTML.
		 *
		 * @since 2.9.0
		 *
		 * @param string $hidden The generated HTML of hidden fields.
		 * @param string $tag    Toggle new registration or profile update. new|edit.
		 */
		$hidden = apply_filters( 'wpmem_register_hidden_fields', $hidden, $tag );

		// Add the hidden fields to the form.
		$form.= $hidden;

		// Create buttons and wrapper.
		$button_text = ( $tag == 'edit' ) ? $args['submit_update'] : $args['submit_register'];
		$button_html = array(
			'reset' =>  ( $args['show_clear_form'] ) ? '<input name="reset" type="reset" value="' . esc_attr( $args['clear_form'] ) . '" class="' . $this->sanitize_class( $args['button_class'] ) . '" /> ' : '',
			'submit' => '<input name="submit" type="submit" value="' . esc_attr( $button_text ) . '" class="' . $this->sanitize_class( $args['button_class'] ) . '" />',
		);
		$buttons = $button_html['reset'] . $args['n'] . $button_html['submit'] . $args['n'];

		/**
		 * Filter the HTML for form buttons.
		 *
		 * The string passed through the filter includes the buttons, as well as the HTML wrapper elements.
		 *
		 * @since 2.9.0
		 * @since 3.2.6 Added $button_html parameter
		 *
		 * @param string $buttons     The generated HTML of the form buttons.
		 * @param string $tag         Toggle new registration or profile update. new|edit.
		 * @param array  $button_html The individual button html.
		 */
		$buttons = apply_filters( 'wpmem_register_form_buttons', $buttons, $tag, $button_html );

		// Add the buttons to the form.
		$form.= $args['buttons_before'] . $args['n'] . $buttons . $args['buttons_after'] . $args['n'];

		// Add the required field notation to the bottom of the form.
		$form.= $args['req_label_before'] . $args['req_label'] . $args['req_label_after'];

		// Apply the heading.
		/**
		 * Filter the registration form heading.
		 *
		 * @since 2.8.2
		 *
		 * @param string $str
		 * @param string $tag Toggle new registration or profile update. new|edit.
		 */
		$heading = ( !$heading ) ? apply_filters( 'wpmem_register_heading', $wpmem->get_text( 'register_heading' ), $tag ) : $heading;
		$form = $args['heading_before'] . $heading . $args['heading_after'] . $args['n'] . $form;

		// Apply fieldset wrapper.
		$form = $args['fieldset_before'] . $args['n'] . $form . $args['n'] . $args['fieldset_after'];

		// Apply attribution if enabled.
		$form = $form . wpmem_inc_attribution();

		// Apply nonce. Nonce uses $tag value of the form processor, NOT the form builder.
		$nonce = ( $tag == 'edit' ) ? 'update' : 'register';
		$form  = wp_nonce_field( 'wpmem_longform_nonce', '_wpmem_' . $nonce . '_nonce', true, false ) . $args['n'] . $form;

		// Apply form wrapper.
		$enctype = ( $enctype == 'multipart/form-data' ) ? ' enctype="multipart/form-data"' : '';
		$form = '<form name="form" method="post"' . $enctype . ' action="' . esc_attr( $args['post_to'] ) . '" id="' . $this->sanitize_class( $args['form_id'] ) . '" class="' . $this->sanitize_class( $args['form_class'] ) . '">' . $args['n'] . $form . $args['n'] . '</form>';

		// Apply anchor.
		$form = '<a id="register"></a>' . $args['n'] . $form;

		// Apply main div wrapper.
		$form = $args['main_div_before'] . $args['n'] . $form . $args['n'] . $args['main_div_after'] . $args['n'];

		// Apply wpmem_txt wrapper.
		$form = $args['txt_before'] . $form . $args['txt_after'];

		// Remove line breaks if enabled for easier filtering later.
		$form = ( $args['strip_breaks'] ) ? $this->strip_breaks( $form, $rows ) : $form; //str_replace( array( "\n", "\r", "\t" ), array( '','','' ), $form ) : $form;

		/**
		 * Filter the generated HTML of the entire form.
		 *
		 * @since 2.7.4
		 *
		 * @param string $form The HTML of the final generated form.
		 * @param string $tag  Toggle new registration or profile update. new|edit.
		 * @param array  $rows   {
		 *     An array containing the form rows. 
		 *
		 *     @type string order        Field display order.
		 *     @type string meta         Field meta tag (not used for display).
		 *     @type string type         Input field type (not used for display).
		 *     @type string value        Input field value (not used for display).
		 *     @type string values       The possible values for the field (dropdown, multiple select/checkbox, radio group).
		 *     @type string label_text   Raw text for the label (not used for display).
		 *     @type string row_before   Opening wrapper tag around the row.
		 *     @type string label        Label tag.
		 *     @type string field_before Opening wrapper tag before the input tag.
		 *     @type string field        The field input tag.
		 *     @type string field_after  Closing wrapper tag around the input tag.
		 *     @type string row_after    Closing wrapper tag around the row.
		 * }
		 * @param string $hidden The HTML string of hidden fields
		 */
		$form = apply_filters( 'wpmem_register_form', $form, $tag, $rows, $hidden );

		/**
		 * Filter before the form.
		 *
		 * This rarely used filter allows you to stick any string onto the front of
		 * the generated form.
		 *
		 * @since 2.7.4
		 *
		 * @param string $str The HTML to add before the form. Default null.
		 * @param string $tag Toggle new registration or profile update. new|edit.
		 */
		$form = apply_filters( 'wpmem_register_form_before', '', $tag ) . $form;

		// Return the generated form.
		return $form;
	} // End register_form().
	
	/**
	 * Strip line breaks from form.
	 *
	 * Function removes line breaks and tabs. Checks for textarea fields
	 * before stripping line breaks.
	 *
	 * @since 3.1.8
	 *
	 * @param  string $form
	 * @param  array  $rows
	 * @return string $form
	 */
	function strip_breaks( $form, $rows ) {
		foreach( $rows as $key => $row ) {
			if ( 'textarea' == $row['type'] ) {
				$textareas[ $key ] = $row['field'];
			}
		}
		$form = str_replace( array( "\n", "\r", "\t" ), array( '','','' ), $form );
		if ( ! empty ( $textareas ) ) {
			foreach ( $textareas as $textarea ) {
				$stripped = str_replace( array( "\n", "\r", "\t" ), array( '','','' ), $textarea );
				$form = str_replace( $stripped, $textarea, $form );
			}
		}
		return $form;
	}
	
	/**
	 * Login Dialog.
	 *
	 * Loads the login form for user login.
	 *
	 * @since 1.8
	 * @since 3.1.4 Global $wpmem_regchk no longer needed.
	 * @since 3.2.0 Moved to forms class, renamed do_login_form().
	 *
	 * @global object $post         The WordPress Post object.
	 * @global object $wpmem        The WP_Members object.
	 * @param  string $page         If the form is being displayed in place of blocked content. Default: page.
	 * @param  string $redirect_to  Redirect URL. Default: null.
	 * @param  string $show         If the form is being displayed in place of blocked content. Default: show.
	 * @return string $str          The generated html for the login form.
	 */
	function do_login_form( $page = "page", $redirect_to = null, $show = 'show' ) {

		global $post, $wpmem;

		$str = '';

		if ( $page == "page" ) {

			 if ( $wpmem->regchk != "success" ) {

				$dialogs = get_option( 'wpmembers_dialogs' );

				// This shown above blocked content.
				$msg = $wpmem->get_text( 'restricted_msg' );
				$msg = ( $dialogs['restricted_msg'] == $msg ) ? $msg : __( stripslashes( $dialogs['restricted_msg'] ), 'wp-members' );
				$str = '<div id="wpmem_restricted_msg"><p>' . $msg . '</p></div>';

				/**
				 * Filter the post restricted message.
				 *
				 * @since 2.7.3
				 * @since 3.2.0 Added raw message string and HTML as separate params.
				 *
				 * @param string $str The post restricted message with HTML.
				 * @param string $msg The raw message string.
				 * @param string      The 'before' HTML wrapper.
				 * @param string      The 'after' HTML wrapper.
				 */
				$str = apply_filters( 'wpmem_restricted_msg', $str, $msg, '<div id="wpmem_restricted_msg"><p>', '</p></div>' );

			} 	
		} 

		// Create the default inputs.
		$default_inputs = array(
			array(
				'name'   => $wpmem->get_text( 'login_username' ), 
				'type'   => 'text', 
				'tag'    => 'log',
				'class'  => 'username',
				'div'    => 'div_text',
			),
			array( 
				'name'   => $wpmem->get_text( 'login_password' ), 
				'type'   => 'password', 
				'tag'    => 'pwd', 
				'class'  => 'password',
				'div'    => 'div_text',
			),
		);

		/**
		 * Filter the array of login form fields.
		 *
		 * @since 2.9.0
		 *
		 * @param array $default_inputs An array matching the elements used by default.
		 */
		$default_inputs = apply_filters( 'wpmem_inc_login_inputs', $default_inputs );

		$defaults = array( 
			'heading'      => $wpmem->get_text( 'login_heading' ), 
			'action'       => 'login', 
			'button_text'  => $wpmem->get_text( 'login_button' ),
			'inputs'       => $default_inputs,
			'redirect_to'  => $redirect_to,
		);	

		/**
		 * Filter the arguments to override login form defaults.
		 *
		 * @since 2.9.0
		 *
		 * @param array $args An array of arguments to use. Default null.
		 */
		$args = apply_filters( 'wpmem_inc_login_args', '' );

		$arr  = wp_parse_args( $args, $defaults );

		$str  = ( $show == 'show' ) ? $str . wpmem_login_form( $page, $arr ) : $str;

		return $str;
	}

	/**
	 * Change Password Dialog.
	 *
	 * Loads the form for changing password.
	 *
	 * @since 2.0.0
	 * @since 3.2.0 Moved to forms class, renamed do_changepassword_form().
	 *
	 * @global object $wpmem The WP_Members object.
	 * @return string $str   The generated html for the change password form.
	 */
	function do_changepassword_form() {

		global $wpmem;

		// create the default inputs
		$default_inputs = array(
			array(
				'name'   => $wpmem->get_text( 'pwdchg_password1' ), 
				'type'   => 'password',
				'tag'    => 'pass1',
				'class'  => 'password',
				'div'    => 'div_text',
			),
			array( 
				'name'   => $wpmem->get_text( 'pwdchg_password2' ), 
				'type'   => 'password', 
				'tag'    => 'pass2',
				'class'  => 'password',
				'div'    => 'div_text',
			),
		);

		/**
		 * Filter the array of change password form fields.
		 *
		 * @since 2.9.0
		 *
		 * @param array $default_inputs An array matching the elements used by default.
		 */	
		$default_inputs = apply_filters( 'wpmem_inc_changepassword_inputs', $default_inputs );

		$defaults = array(
			'heading'      => $wpmem->get_text( 'pwdchg_heading' ), 
			'action'       => 'pwdchange', 
			'button_text'  => $wpmem->get_text( 'pwdchg_button' ), 
			'inputs'       => $default_inputs,
		);

		/**
		 * Filter the arguments to override change password form defaults.
		 *
		 * @since 2.9.0
		 *
		 * @param array $args An array of arguments to use. Default null.
		 */
		$args = apply_filters( 'wpmem_inc_changepassword_args', '' );

		$arr  = wp_parse_args( $args, $defaults );

		$str  = wpmem_login_form( 'page', $arr );

		return $str;
	}
	
	/**
	 * Reset Password Dialog.
	 *
	 * Loads the form for resetting password.
	 *
	 * @since 2.1.0
	 * @since 3.2.0 Moved to forms class, renamed do_resetpassword_form().
	 *
	 * @global object $wpmem The WP_Members object.
	 * @return string $str   The generated html fo the reset password form.
	 */
	function do_resetpassword_form() { 

		global $wpmem;

		// Create the default inputs.
		$default_inputs = array(
			array(
				'name'   => $wpmem->get_text( 'pwdreset_username' ), 
				'type'   => 'text',
				'tag'    => 'user', 
				'class'  => 'username',
				'div'    => 'div_text',
			),
			array( 
				'name'   => $wpmem->get_text( 'pwdreset_email' ), 
				'type'   => 'text', 
				'tag'    => 'email', 
				'class'  => 'text',
				'div'    => 'div_text',
			),
		);

		/**
		 * Filter the array of reset password form fields.
		 *
		 * @since 2.9.0
		 *
		 * @param array $default_inputs An array matching the elements used by default.
		 */	
		$default_inputs = apply_filters( 'wpmem_inc_resetpassword_inputs', $default_inputs );

		$defaults = array(
			'heading'      => $wpmem->get_text( 'pwdreset_heading' ),
			'action'       => 'pwdreset', 
			'button_text'  => $wpmem->get_text( 'pwdreset_button' ), 
			'inputs'       => $default_inputs,
		);

		/**
		 * Filter the arguments to override reset password form defaults.
		 *
		 * @since 2.9.0
		 *
		 * @param array $args An array of arguments to use. Default null.
		 */
		$args = apply_filters( 'wpmem_inc_resetpassword_args', '' );

		$arr  = wp_parse_args( $args, $defaults );

		$str  = wpmem_login_form( 'page', $arr );

		return $str;
	}
	
	/**
	 * Forgot Username Form.
	 *
	 * Loads the form for retrieving a username.
	 *
	 * @since 3.0.8
	 * @since 3.2.0 Moved to forms class, renamed do_forgotusername_form().
	 *
	 * @global object $wpmem The WP_Members object class.
	 * @return string $str   The generated html for the forgot username form.
	 */
	function do_forgotusername_form() {
		
		global $wpmem;
		
		// create the default inputs
		$default_inputs = array(
			array(
				'name'   => $wpmem->get_text( 'username_email' ), 
				'type'   => 'text',
				'tag'    => 'user_email',
				'class'  => 'username',
				'div'    => 'div_text',
			),
		);

		/**
		 * Filter the array of forgot username form fields.
		 *
		 * @since 2.9.0
		 *
		 * @param array $default_inputs An array matching the elements used by default.
		 */	
		$default_inputs = apply_filters( 'wpmem_inc_forgotusername_inputs', $default_inputs );

		$defaults = array(
			'heading'      => $wpmem->get_text( 'username_heading' ), 
			'action'       => 'getusername', 
			'button_text'  => $wpmem->get_text( 'username_button' ),
			'inputs'       => $default_inputs,
		);

		/**
		 * Filter the arguments to override change password form defaults.
		 *
		 * @since 
		 *
		 * @param array $args An array of arguments to use. Default null.
		 */
		$args = apply_filters( 'wpmem_inc_forgotusername_args', '' );

		$arr  = wp_parse_args( $args, $defaults );

		$str  = wpmem_login_form( 'page', $arr );

		return $str;
	}


} // End of WP_Members_Forms class.