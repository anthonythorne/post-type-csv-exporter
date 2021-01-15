<?php
/**
 * Finds the asset file path.
 *
 * @file    plugins/post-type-csv-exporter/source/php/Function/FindAssetFilePath.php
 * @package PostTypeCSVExporter
 */

// phpcs:disable WordPress.Files.FileName

namespace PostTypeCSVExporter;

/**
 * Basic helper to find the file path dependent on environment.
 *
 * @param string $template_directory The template file path. e.g get_template_directory().
 * @param string $relative_path      The path to the file.
 * @param string $file_name          The file name.
 * @param string $file_ext           The file extension, 'js' or 'css'.
 *
 * @return string
 */
function find_asset_file_path( $template_directory, $relative_path, $file_name, $file_ext ) {

	$relative_path_file_name = trim( $relative_path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . trim( $file_name, DIRECTORY_SEPARATOR );

	$minified = $template_directory . DIRECTORY_SEPARATOR . $relative_path_file_name . '.min.' . $file_ext;
	$standard = $template_directory . DIRECTORY_SEPARATOR . $relative_path_file_name . '.' . $file_ext;

	$file_path = '';

	if ( is_local() && file_exists( $standard ) ) {
		$file_path = DIRECTORY_SEPARATOR . $relative_path_file_name . '.' . $file_ext;
	} elseif ( file_exists( $minified ) ) {
		$file_path = DIRECTORY_SEPARATOR . $relative_path_file_name . '.min.' . $file_ext;
	} elseif ( file_exists( $standard ) ) {
		$file_path = DIRECTORY_SEPARATOR . $relative_path_file_name . '.' . $file_ext;
	}

	return $file_path;
}
