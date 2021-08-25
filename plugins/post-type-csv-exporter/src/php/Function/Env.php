<?php
/**
 * Environment helper functions.
 *
 * @file    plugins/post-type-csv-exporter/src/php/Function/Env.php
 * @package PostTypeCSVExporter
 */

// phpcs:disable WordPress.Files.FileName

namespace PostTypeCSVExporter;

/**
 * Get the environment slug.
 *
 * @return string
 */
function get_environment_type() {

	if ( defined( 'WP_ENV' ) ) {
		return WP_ENV;
	}

	// Default, returns production.
	return function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production';
}

/**
 * Basic helper function to check if this is the production development environment.
 *
 * @return bool
 */
function is_production() {

	if ( defined( 'WP_ENV' ) ) {
		return 'production' === WP_ENV;
	}

	// Default, returns true for production if not set.
	return function_exists( 'wp_get_environment_type' ) ? 'production' === wp_get_environment_type() : true;
}

/**
 * Basic helper function to check if this is the staging development environment.
 *
 * @return bool
 */
function is_staging() {

	if ( defined( 'WP_ENV' ) ) {
		return 'staging' === WP_ENV;
	}

	// Default, returns true for production if not set.
	return function_exists( 'wp_get_environment_type' ) ? 'staging' === wp_get_environment_type() : false;
}

/**
 * Basic helper function to check if this is the local development environment.
 *
 * @return bool
 */
function is_local() {

	if ( defined( 'WP_ENV' ) ) {
		return 'local' === WP_ENV;
	}

	if ( defined( 'WP_LOCAL_DEV' ) ) {
		return WP_LOCAL_DEV;
	}

	// Default, returns true for production if not set.
	return function_exists( 'wp_get_environment_type' ) ? ( 'local' === wp_get_environment_type() || 'development' === wp_get_environment_type() ) : false;
}

/**
 * Basic helper function to get the environment as a string.
 *
 * @return string
 */
function get_environment_slug() {

	if ( is_local() ) {
		return 'local';
	} elseif ( is_staging() ) {
		return 'staging';
	}

	return 'production';
}
