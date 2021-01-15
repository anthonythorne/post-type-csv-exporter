<?php
/**
 * Controls functionality of the GPT script.
 *
 * @file    plugins/post-type-csv-exporter/source/php/Controller/AdminSettingsController.php
 * @package PostTypeCSVExporter
 */

// phpcs:disable WordPress.Files.FileName

namespace PostTypeCSVExporter\Controller;

use PostTypeCSVExporter\Library\Controller;

/**
 * Controls functionality of the Admin Page.
 */
class AdminSettingsController extends Controller {

	/**
	 * Register all actions, filters etc.
	 */
	public function setup() {
		/*
		 * TODO
		 * Add a settings page that manages the Post Types that the CSV page is added for.
		 * This page will also list the allowed export fields for the given post type such
		 * as post fields, meta fields, categories, tags, taxonomies.
		 */
	}

}
