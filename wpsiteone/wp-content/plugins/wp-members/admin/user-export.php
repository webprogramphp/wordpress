<?php
/**
 * WP-Members Export Functions
 *
 * Mananges exporting users to a CSV file.
 *
 * This file is part of the WP-Members plugin by Chad Butler
 * You can find out more about this plugin at https://rocketgeek.com
 * Copyright (c) 2006-2019  Chad Butler
 * WP-Members(tm) is a trademark of butlerblog.com
 *
 * @package WP-Members
 * @author Chad Butler
 * @copyright 2006-2019
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * New export function to export all or selected users
 *
 * @since 2.9.7
 * @since 3.2.0 Updated to use fputcsv.
 * @since 3.2.1 Added user data filters.
 *
 * @param array $args
 * @param array $users
 */
function wpmem_export_users( $args, $users = null ) {

	global $wpmem;

	$today = date( "m-d-y" );

	// Setup defaults.
	$defaults = array(
		'export'         => 'all',
		'filename'       => 'wp-members-user-export-' . $today . '.csv',
		'export_fields'  => wpmem_fields(),
		'exclude_fields' => array( 'password', 'confirm_password', 'confirm_email' ),
		'entity_decode'  => false,
	);

	/**
	 * Filter the default export arguments.
	 *
	 * @since 2.9.7
	 *
	 * @param array $args An array of arguments to merge with defaults. Default null.
	 */
	$args = wp_parse_args( apply_filters( 'wpmem_export_args', $args ), $defaults );
	
	// Prepare fields, add additional "special" fields.
	$export_fields = array(
		'ID'       => __( 'User ID', 'wp-members' ),
		'username' => __( 'Username', 'wp-members' ),
	);
	foreach( $args['export_fields'] as $meta_key => $value ) {
		$export_fields[ $meta_key ] = $value['label'];
	}
	if ( 1 == $wpmem->mod_reg ) {
		$export_fields['active'] = __( 'Activated?', 'wp-members');
	}
	if ( defined( 'WPMEM_EXP_MODULE' ) && 1 == $wpmem->use_exp ) {
		$export_fields['exp_type'] = __( 'Subscription', 'wp-members' );
		$export_fields['expires']  = __( 'Expires', 'wp-members' );
	}
	$export_fields['user_registered'] = __( 'Registered', 'wp-members' );
	$export_fields['wpmem_reg_ip']    = __( 'IP', 'wp-members' );
	
	/**
	 * Filter the export fields.
	 *
	 * @since 3.2.5
	 *
	 * @param array $export_fields
	 */
	$export_fields = apply_filters( 'wpmem_export_fields', $export_fields );

	// Output needs to be buffered, start the buffer.
	ob_start();

	// If exporting all, get all of the users.
	$users = ( 'all' == $args['export'] ) ? get_users( array( 'fields' => 'ID' ) ) : $users;

	// Generate headers and a filename based on date of export.
	header( "Content-Description: File Transfer" );
	header( "Content-type: application/octet-stream" );
	header( "Content-Disposition: attachment; filename=" . $args['filename'] );
	header( "Content-Type: text/csv; charset=" . get_option( 'blog_charset' ), true );

	$handle = fopen( 'php://output', 'w' );
	fputs( $handle, "\xEF\xBB\xBF" ); // UTF-8 BOM

	// Remove excluded fields from $export_fields while setting up $header array.
	$header = array();
	foreach ( $export_fields as $meta => $field ) {
		if ( in_array( $meta, $args['exclude_fields'] ) ) {
			unset( $export_fields[ $meta ] );
		} else {
			$header[ $meta ] = $field;
		}
	}
	
	/**
	 * Filters user export header row before assembly.
	 *
	 * @since 3.2.1
	 *
	 * @param array $header The header column values
	 */
	$header = apply_filters( 'wpmem_user_export_header', $header );

	fputcsv( $handle, $header );

	// Loop through the array of users, assemble csv.
	// $export_fields only includes fields to be exported at this point.
	foreach ( $users as $user ) {

		$user_info = get_userdata( $user );

		$wp_user_fields = [ 'username', 'user_email', 'user_nicename', 'user_url', 'display_name' ];
		foreach ( $export_fields as $meta => $field ) {
			switch ( $meta ) {
				case 'ID':
					$row[ $meta ] = $user_info->ID;
					break;
				case 'username':
					$row[ $meta ] = $user_info->user_login;
					break;
				case 'active':
					$row[ $meta ] = get_user_meta( $user, 'active', 1 ) ? __( 'Yes' ) : __( 'No' );
					break;
				case 'exp_type':
					$row['exp_type'] = get_user_meta( $user, 'exp_type', true );
					break;
				case 'expires':
					$row['expires'] = get_user_meta( $user, 'expires', true );
					break;
				case 'user_registered':
					$row['user_registered'] = $user_info->user_registered;
					break;
				case 'wpmem_reg_ip':
					$row['wpmem_reg_ip'] = get_user_meta( $user, 'wpmem_reg_ip', true );
					break;
				default:
					if ( in_array( $meta, $wp_user_fields ) ) {
						$row[ $meta ] = ( 'username' == $meta ) ? $user_info->user_login : $user_info->{$meta};
					} else {
						$raw_data = get_user_meta( $user, $meta, true );
						$row[ $meta ] = ( $args['entity_decode'] ) ? html_entity_decode( $raw_data ) : $raw_data;
					}
					break;
			}
		}

		/**
		 * Filter the user data before assembly.
		 *
		 * @since 3.2.1
		 *
		 * @param array $row The user data row
		 */
		$row = apply_filters( 'wpmem_user_export_row', $row );

		fputcsv( $handle, $row );

		// Update the user record as being exported.
		if ( 'all' != $args['export'] ) {
			update_user_meta( $user, 'exported', 1 );
		}
	}

	fclose( $handle );
	print( ob_get_clean() );

	exit();
}

// End of file.