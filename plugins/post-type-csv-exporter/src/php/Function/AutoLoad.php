<?php
/**
 * Autoload function files.
 *
 * @file    plugins/post-type-csv-exporter/src/php/Function/AutoLoad.php
 * @package PostTypeCSVExporter
 */

// phpcs:disable WordPress.Files.FileName

// File names in specific load order.
$specific_file_load_order = [];

$function_files = glob( __DIR__ . DIRECTORY_SEPARATOR . '*.php' );

if ( ! empty( $specific_file_load_order ) ) {
	// Load all files that have a order specified.
	foreach ( $specific_file_load_order as $function_file ) {

		$function_file = __DIR__ . DIRECTORY_SEPARATOR . $function_file . '.php';

		if ( file_exists( $function_file ) ) {
			require_once $function_file;
		}
	}
}

// Load all other files that do not have a specific order.
foreach ( $function_files as $function_file ) {

	$base_name = basename( $function_file );

	if ( empty( $specific_file_load_order ) || ! in_array( $base_name, $specific_file_load_order, true ) ) {
		require_once $function_file;
	}
}
