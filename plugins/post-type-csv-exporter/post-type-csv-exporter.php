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

define( 'POST_TYPE_CSV_EXPORTER_VERSION', '1' );

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

add_action( 'plugins_loaded', 'post_type_csv_exporter_setup' );
/**
 * Setup the plugin controllers.
 */
function post_type_csv_exporter_setup() {
	// All of the theme controller instances.
	$controllers = [
		new \PostTypeCSVExporter\Controller\AdminSettingsController(),
	];

	// Boot each of the theme controller instances.
	foreach ( $controllers as $controller ) {
		$controller->setup();
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
