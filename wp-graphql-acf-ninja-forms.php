<?php

/**
 * Plugin Name: Ninja Form Field WPGraphQL Extension
 * Plugin URI: https://mddd.nl
 * Description: Add Ninja form field to WPGraphQL.
 * Author: M.D. Leguijt
 * Author URI: https://mddd.nl
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
  exit;
}

add_filter('wpgraphql_acf_supported_fields', function($supported_fields) {
	$supported_fields[] = 'forms';

	return $supported_fields;
});

add_filter( 'wpgraphql_acf_register_graphql_field', function($field_config, $type_name, $field_name, $config) {
	$acf_field = isset( $config['acf_field'] ) ? $config['acf_field'] : null;
	$acf_type  = isset( $acf_field['type'] ) ? $acf_field['type'] : null;

	if( !$acf_field ) {
		return $field_config;
	} 

	// ignore all other field types
	if( $acf_type !== 'forms' ) {
			return $field_config;
	}

	// define data type
	$field_config['type'] = 'Form';

	// add resolver
	$field_config['resolve'] = function( $root ) use ( $acf_field ) {
		// when field is used in WP_Post and is top-level field (not nested in repeater, flexible content etc.)
		if( $root->ID ) {
			$value = get_field( $acf_field['key'], $root->ID, false );

		// when field is used in WP_Post and is nested in repeater, flexible content etc. ...
		} elseif( array_key_exists( $acf_field['key'], $root ) ) {
			$value = $root[$acf_field['key']];
		} 

		return !empty( $value ) ? $value : null;
	};

	return $field_config;
}, 10, 4 );