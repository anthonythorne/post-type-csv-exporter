<?php
/**
 * Environment helper functions.
 *
 * @file    plugins/post-type-csv-exporter/source/php/Function/Env.php
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
	return 'production' === get_environment_type();
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

	if ( 'staging' == get_environment_type() ) {
		return true;
	}

	return ( strpos( get_site_url(), 'stage' ) !== false );
}

/**
 * Basic helper function to check if this is the uat development environment.
 *
 * @return bool
 */
function is_uat() {

	if ( defined( 'WP_ENV' ) ) {
		return 'uat' === WP_ENV;
	}

	if ( 'uat' == get_environment_type() ) {
		return true;
	}

	return ( strpos( get_site_url(), 'uat' ) !== false );
}

/**
 * Basic helper function to check if this is the dev development environment.
 *
 * @return bool
 */
function is_dev() {

	if ( defined( 'WP_ENV' ) ) {
		return 'dev' === WP_ENV;
	}

	if ( 'development' == get_environment_type() ) {
		return true;
	}

	return ( strpos( get_site_url(), 'dev' ) !== false );
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

	if ( 'local' == get_environment_type() ) {
		return true;
	}

	return ( strpos( get_site_url(), '.local' ) !== false );
}

/**
 * Basic helper function to get the environment as a string.
 *
 * @return string
 */
function get_environment_slug() {

	if ( is_local() ) {
		return 'local';
	} elseif ( is_dev() ) {
		return 'dev';
	} elseif ( is_uat() ) {
		return 'uat';
	} elseif ( is_staging() ) {
		return 'staging';
	} elseif ( strpos( get_site_url(), 'new.indexexchange.com' ) !== false ) {
		return 'new';
	}

	return 'production';
}
