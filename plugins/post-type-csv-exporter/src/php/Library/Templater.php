<?php
/**
 * Templating system which works with the existing WordPress theme hierarchy.
 *
 * @file    plugins/post-type-csv-exporter/src/php/Library/Templater.php
 * @package PostTypeCSVExporter
 */

// phpcs:disable WordPress.Files.FileName

namespace PostTypeCSVExporter\Library;

/**
 * A simple pure PHP templating utility.
 * Simply executes a PHP file (the template) with a number of predefined variables.
 */
class Templater {

	/**
	 * The slug of the template file.
	 *
	 * @var mixed|string
	 */
	private $slug;

	/**
	 * The default template directory.
	 *
	 * @var mixed|string
	 */
	private $dir;

	/**
	 * The subdirectory.
	 *
	 * @var mixed|string
	 */
	private $subdir;

	/**
	 * The template variables.
	 *
	 * @var mixed|string
	 */
	private $vars;

	/**
	 * Setup and add any args.
	 *
	 * @param array $args Bulk parameters to pass to the template.
	 */
	public function __construct( $args ) {

		// Ensure that slug and dir are present.
		assert( ! empty( $args['slug'] ) );
		assert( ! empty( $args['dir'] ) );

		$this->slug   = isset( $args['slug'] ) ? $args['slug'] : '';
		$this->dir    = isset( $args['dir'] ) ? $args['dir'] : '';
		$this->subdir = isset( $args['subdir'] ) ? $args['subdir'] : '';
		$this->vars   = isset( $args['vars'] ) ? $args['vars'] : [];

		// Add in the template slug.
		$this->vars['template_slug'] = $this->slug;
	}

	/**
	 * Magic setter method.
	 *
	 * @param string $var_name The variable name being set.
	 * @param mixed  $value    The value being stored against teh variable name.
	 */
	public function __set( $var_name, $value ) {
		assert( ! empty( $var_name ) );

		$this->vars[ $var_name ] = $value;
	}

	/**
	 * Magic getter method.
	 *
	 * @param string $var_name The variable name being set.
	 *
	 * @return mixed|null
	 */
	public function __get( $var_name ) {
		assert( ! empty( $var_name ) );

		return isset( $this->vars[ $var_name ] ) ? $this->vars[ $var_name ] : null;
	}

	/**
	 * Renders the template.
	 *
	 * @param bool $output Whether to output the rendered template, otherwise return it as a string.
	 *
	 * @return string Returns empty string when outputs rendered.
	 */
	public function render( $output = true ) {

		// Render and return empty string.
		if ( $output ) {
			$this->renderOutput();
			return '';
		}

		// Store and return output.
		ob_start();
		$this->renderOutput();
		return ob_get_clean();
	}

	/** Renders the template and outputs the result */
	private function renderOutput() {

		$name = $this->slug;

		// Handle whether a sub directory has been added
		if ( ! empty( $this->subdir ) ) {
			$themeView = get_stylesheet_directory() . "/$this->subdir/$name.php";
		} else {
			$themeView = get_stylesheet_directory() . "/$name.php";
		}

		if ( file_exists( $themeView ) ) { // use the theme file preferentially.

			$this->renderFile( $themeView );
		} else {

			if ( ! empty( $this->subdir ) ) {
				$dir = "$this->dir/template/$this->subdir";
			} else {
				$dir = "$this->dir/template";
			}

			$this->renderFile( "$dir/$name.php" );
		}
	}

	/**
	 * Renders the template file.
	 *
	 * @param string $templateFile The template file and path.
	 */
	private function renderFile( $templateFile ) {

		assert( ! empty( $templateFile ) );

		// Missing template is a fatal error.
		if ( ! file_exists( $templateFile ) ) {
			wp_die( "Template does not exist; $templateFile" );
		}

		// Extract the given vars into the current scope.
		extract( $this->vars ); // Extract for the local scope.
		$GLOBALS['template_vars'] = $this->vars; // Import as array for the global scope.

		// Include and execute the template file.
		include $templateFile;
	}

}
