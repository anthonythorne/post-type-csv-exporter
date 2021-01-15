<?php
/**
 * Loads the viewable admin page for a the post type exporter.
 *
 * @file    plugins/post-type-csv-exporter/source/php/View/admin-csv-exporter-view.php
 * @package PostTypeCSVExporter
 */

namespace PostTypeCSVExporter\View;

use PostTypeCSVExporter\Library\Templater;
use PostTypeCSVExporter\Library\Config;
use PostTypeCSVExporter\Library\View;

/**
 * Loads the viewable admin page for a the post type exporter.
 */
class AdminCSVExporterView extends View {

	/**
	 * Renders admin page.
	 *
	 * @param string $slug          The template slug.
	 * @param string $sub_dir       Optional. The sub dir slug.
	 * @param array  $template_args Args passed to the template.
	 */
	public static function render_admin_page( $slug, $sub_dir = '', $template_args = [] ) {

		if ( empty( $slug ) ) {
			return;
		}

		$config = Config::get( 'core' );

		$templater = new Templater(
			[
				'slug'   => $slug,
				'subdir' => $sub_dir,
				'dir'    => $config->app( 'directory' ),
				'vars'   => $template_args,
			]
		);

		$templater->render();
	}

}
