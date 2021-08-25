<?php
/**
 * Parent controller which all controllers should extend.
 *
 * @file    plugins/post-type-csv-exporter/src/php/Library/Controller.php
 * @package PostTypeCSVExporter
 */

// phpcs:disable WordPress.Files.FileName

namespace PostTypeCSVExporter\Library;

var_dump( 'here');

/**
 * Parent controller which all controllers should extend.
 */
abstract class Controller {

	/**
	 * Boot the controller.
	 * This is called as soon as the theme is loaded, in functions.php.
	 *
	 * @return void
	 */
	abstract public function setup();

}
