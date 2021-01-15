<?php
/**
 * File auto load functions.
 *
 * @file    plugins/post-type-csv-exporter/source/php/Function/AutoLoadFunction.php
 * @package PostTypeCSVExporter
 */

// phpcs:disable WordPress.Files.FileName

namespace PostTypeCSVExporter;

/**
 * Helper funciton to autoload files from a given directory path.
 *
 * @param string $dir_path The path to the directory that needs its files auto loaded.
 */
function autoload_directory_files( $dir_path ) {
	$function_files = glob( rtrim( $dir_path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . '*.php' );

	// Load all other files that do not have a specific order.
	foreach ( $function_files as $function_file ) {
		if ( file_exists( $function_file ) ) {
			require_once $function_file;
		}
	}
}
