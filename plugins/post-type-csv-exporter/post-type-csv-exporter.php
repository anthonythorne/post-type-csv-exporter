<?php
/**
 * Plugin Name: Post Type CSV Exporter
 * Plugin URI: https://github.com/anthonythorne33/post-type-csv-exporter
 * Description: Basic CSV Exporter for Posts and Custom Post Types. Allows for exporting Post data, meta data, custom hooks.
 * Version: 1.1
 * Author: Anthony Thorne @ The Code Company
 * Author URI: https://thecode.co
 */

if ( ! defined( 'POST_TYPE_CSV_EXPORTER_BASENAME' ) ) {
	define( 'POST_TYPE_CSV_EXPORTER_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'POST_TYPE_CSV_EXPORTER_URL' ) ) {
	define( 'POST_TYPE_CSV_EXPORTER_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'POST_TYPE_CSV_EXPORTER_DIR_PATH' ) ) {
	define( 'POST_TYPE_CSV_EXPORTER_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

define( 'POST_TYPE_CSV_EXPORTER_VERSION', '1.1' );

add_action( 'plugins_loaded', 'post_type_csv_exporter_setup' );

/**
 * Setup the plugin controllers.
 */
function post_type_csv_exporter_setup() {
	/**
	 * Custom files driven by composer.
	 */
	$autoload_file = dirname( __FILE__ ) . '/vendor/autoload.php';

	if ( file_exists( $autoload_file ) ) {

		require_once $autoload_file;

		// All of the controller instances.
		$controllers = [];

		if ( ! $controllers ) {
			return;
		}

		// Boot each of the controller instances.
		foreach ( $controllers as $controller ) {
			$controller->setup();
		}
	} elseif ( is_local() ) {
		// Trigger an error for local environment only.
		trigger_error( 'Run composer for the ' . dirname( __FILE__ ) . ' plugin.', E_USER_ERROR ); // phpcs:ignore
	}

}

// TODO not required at this stage.
// register_activation_hook( POST_TYPE_CSV_EXPORTER_BASENAME, 'post_type_csv_exporter_activate' );
/**
 * Clear the settings when we activate the plugin.
 */
function post_type_csv_exporter_activate() {
}
// TODO not required at this stage.
// register_deactivation_hook( POST_TYPE_CSV_EXPORTER_BASENAME, 'post_type_csv_exporter_deactivate' );
/**
 * Clear the settings when we deactivate the plugin.
 */
function post_type_csv_exporter_deactivate() {
}
