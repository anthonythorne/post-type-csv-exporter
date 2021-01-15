<?php
/**
 * Enables a CSV exporter admin page for a post type.
 *
 * Call on init after the post type has been created, possibly priority of 999.
 *
 * @file    plugins/post-type-csv-exporter/source/php/Library/CSVExporter.php
 * @package PostTypeCSVExporter
 */

// phpcs:disable WordPress.Files.FileName

namespace PostTypeCSVExporter\Library;

use WP_Post_Type;
use WP_Post;

/**
 * Enables a CSV exporter admin page for a post type.
 */
class CSVExporter {

	const ACTION                = 'csv_exporter';
	const SLUG                  = 'csv-exporter';
	const TITLE                 = 'CSV Exporter';
	const ASSETS_FILE_NAME      = 'post-type-csv-exporter';
	const ASSETS_ENQUEUE_HANDLE = 'post-type-csv-exporter';
	const TEMPLATE_SLUG         = 'post-type-csv-exporter';
	const CACHE_KEY_SALT        = 'csv-exporter';
	const CACHE_TTL             = 60 * 60; // Cashed for a hour.

	/**
	 * Contains the $_POST.
	 *
	 * @var null
	 */
	protected $post_variables = null;

	/**
	 * Contains the response.
	 *
	 * @var array
	 */
	protected $response = [
		'data'    => [],
		'success' => true,
		'status'  => '',
		'message' => '',
	];

	/**
	 * The post type.
	 *
	 * @var null|string
	 */
	protected $post_type = '';

	/**
	 * The post type name.
	 *
	 * @var null|string
	 */
	protected $post_type_name = '';

	/**
	 * The export page title.
	 *
	 * @var null|string
	 */
	protected $export_page_title = '';

	/**
	 * The export menu title.
	 *
	 * @var null|string
	 */
	protected $export_menu_title = '';

	/**
	 * The export menu slug.
	 *
	 * @var null|string
	 */
	protected $export_menu_slug = '';

	/**
	 * The export enqueue hook.
	 *
	 * @var null|string
	 */
	protected $export_enqueue_hook = '';

	/**
	 * The parent menu slug
	 *
	 * @var null|string
	 */
	protected $parent_menu_slug = null;

	/**
	 * Initialise params and setup actions and filters.
	 *
	 * @param string $post_type The post type that the export page is being added for.
	 * @param array  $params    TODO.
	 */
	public function __construct( $post_type, $params = [] ) {

		// Bail early, this is only needed within the admin area.
		if ( ! is_admin() ) {
			return;
		}

		$post_type_object = get_post_type_object( $post_type );

		// Bail early if the post type does not exist. Consider a warning/notice under local development.
		if ( empty( $post_type_object ) ) {
			if ( is_local() ) {
				trigger_error( 'The post type does not exist or does not exist yet. Admin CSV page cannot be created', E_USER_NOTICE ); // phpcs:ignore
			}

			return;
		}

		// Set all parameters first before other action and hooks.
		$this->set_parameters( $post_type_object );

		// Add the export endpoint.
		add_action( 'wp_ajax_' . self::ACTION, [ $this, 'ajax_endpoint' ] );

		// Register the enqueues.
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueues' ] );

		// Register the menu item late.
		add_action( 'admin_menu', [ $this, 'register_admin_sub_pages' ], 999 );

	}

	/**
	 * Setup any parameters for latter use.
	 *
	 * @param WP_Post_Type $post_type_object The post type object.
	 */
	protected function set_parameters( $post_type_object ) {

		$this->post_type           = $post_type_object->name;
		$this->post_type_name      = $post_type_object->labels->name;
		$this->export_page_title   = $this->post_type_name . ' ' . self::TITLE;
		$this->export_menu_title   = $this->post_type_name . ' ' . self::TITLE;
		$this->export_menu_slug    = $this->post_type . '-' . self::SLUG;
		$this->export_enqueue_hook = $this->post_type . '_page_' . $this->export_menu_slug;
		$this->parent_menu_slug    = 'edit.php?post_type=' . $this->post_type;
		$this->parent_menu_slug    = 'edit.php?post_type=' . $this->post_type;

	}

	/**
	 * Process the request.
	 */
	public function ajax_endpoint() {

		$post_type = $this->post_variables( 'post_type' );

		// Verify the nonce.
		$nonce_action = $this->build_export_ajax_nonce_key( $post_type );
		check_ajax_referer( $nonce_action, 'wp_nonce' );

		// Build the data for the csv file.
		$csv_headers                        = $this->get_csv_headers();
		$this->response['data']['headers']  = array_values( $csv_headers );
		$this->response['data']['rows']     = $this->get_csv_rows( $csv_headers );
		$this->response['data']['filename'] = $this->get_filename();

		// Return teh response.
		$this->json_response();
	}

	/**
	 * Admin enqueues for the specific page.
	 *
	 * @param string $hook Hook for the current page that is being viewed to conditionally enqueue assets.
	 */
	public function admin_enqueues( $hook ) {

		if ( is_admin() && $this->export_enqueue_hook === $hook ) {

			$templates_path = plugins_url( '/template/', realpath( __DIR__ ) );

			// JS.
			wp_register_script(
				self::ASSETS_ENQUEUE_HANDLE,
				$templates_path . 'js/' . self::ASSETS_FILE_NAME . '.js',
				[ 'jquery' ],
				true,
				true
			);

			$params = [
				'action'     => self::ACTION,
				'ajax_nonce' => wp_create_nonce( $this->build_export_ajax_nonce_key( $this->post_type ) ),
				'post_type'  => $this->post_type,
			];

			wp_localize_script( self::ASSETS_ENQUEUE_HANDLE, 'postTypeCSVExporterConfig', $params );
			wp_enqueue_script( self::ASSETS_ENQUEUE_HANDLE, );

			// CSS.
			wp_enqueue_style( self::ASSETS_ENQUEUE_HANDLE, $templates_path . 'js/' . self::ASSETS_FILE_NAME . '.js', '', true );
		}
	}

	/**
	 * Register sub menu page for the post type export page.
	 */
	public function register_admin_sub_pages() {

		add_submenu_page(
			$this->parent_menu_slug,
			$this->export_page_title,
			$this->export_menu_title,
			'manage_options',
			$this->export_menu_slug,
			[ $this, 'csv_exports_page' ]
		);

	}

	/**
	 * Render the content for the post type export page.
	 */
	public function csv_exports_page() {

		$template_vars = [
			'page_title'     => $this->export_page_title,
			'post_keys'      => $this->get_post_keys( $this->post_type ),
			'post_meta_keys' => $this->get_meta_keys_for_post_type( $this->post_type ),
		];

		AdminCSVExporterView::render_admin_page( self::TEMPLATE_SLUG, '', $template_vars );
	}

	/**
	 * Get an array of WP_Post object keys.
	 *
	 * @param string $post_type The post type to gather meta keys for.
	 *
	 * @return array
	 */
	public function get_post_keys( $post_type ) {
		$blank_post_instance = new \WP_Post( WP_Post::get_instance( 0 ) );

		$post_keys = array_keys( get_object_vars( $blank_post_instance ) );

		// Filter the format.
		$post_keys = $this->create_key_title_pair( $post_keys );

		/**
		 * Filter: post_type_csv_exporter_post_keys_<post_type>
		 * Allows for filtering what post keys are sent to the template for selecting.
		 *
		 * The following is the list of post object keys.
		 * 'ID'
		 * 'post_author'
		 * 'post_date'
		 * 'post_date_gmt'
		 * 'post_content'
		 * 'post_title'
		 * 'post_excerpt'
		 * 'post_status'
		 * 'comment_status'
		 * 'ping_status'
		 * 'post_password'
		 * 'post_name'
		 * 'to_ping'
		 * 'pinged'
		 * 'post_modified'
		 * 'post_modified_gmt'
		 * 'post_content_filtered'
		 * 'post_parent'
		 * 'guid'
		 * 'menu_order'
		 * 'post_type'
		 * 'post_mime_type'
		 * 'comment_count'
		 * 'filter'
		 */
		$post_keys = apply_filters( "post_type_csv_exporter_post_keys_{$post_type}", $post_keys );

		return $post_keys;
	}

	/**
	 * Return the post meta keys from a sample.
	 *
	 * @param string $post_type   The post type to gather meta keys for.
	 * @param int    $sample_size The number of posts to use for this sample.
	 *
	 * @return array|mixed|void
	 */
	public function get_meta_keys_for_post_type( $post_type, $sample_size = 50 ) {

		$meta_keys = [];

		// Bail early.
		if ( empty( $this->post_type ) ) {
			return $meta_keys;
		}

		$key = $this->post_type . '-' . self::CACHE_KEY_SALT;

		$cache = new FragmentCache( $key, self::CACHE_TTL );

		$cached_meta_keys = $cache->get_transient();

		// Bail early with the cached meta keys.
		if ( ! empty( $cached_meta_keys ) && is_array( $cached_meta_keys ) ) {
			return $cached_meta_keys;
		}

		$query_args = [
			'post_type' => $post_type,
			'limit'     => $sample_size,
		];

		$posts = get_posts( $query_args );

		foreach ( $posts as $post ) {
			$post_meta_keys = get_post_custom_keys( $post->ID );
			$meta_keys      = array_merge( $meta_keys, $post_meta_keys );
		}

		// Use array_unique to remove duplicate meta_keys that we received from all posts.
		// Use array_values to reset the index of the array.
		$meta_keys = array_values( array_unique( $meta_keys ) );

		// Filter the format.
		$meta_keys = $this->create_key_title_pair( $meta_keys );

		/**
		 * Filter: post_type_csv_exporter_post_meta_keys_<post_type>
		 * Allows for filtering what post meta keys are sent to the template for selecting.
		 */
		$meta_keys = apply_filters( "post_type_csv_exporter_post_meta_keys_{$post_type}", $meta_keys );

		// Cache for use within the time frame.
		$cache->set_transient( $meta_keys );

		return $meta_keys;
	}

	/**
	 * Create an array where the key is teh value, and teh value is readable version of itself.
	 *
	 * @param array $array An array of values to convert.
	 *
	 * @return array
	 */
	protected function create_key_title_pair( $array ) {

		// Bail early.
		if ( ! is_array( $array ) ) {
			return $array;
		}

		$key_title_pair_array = [];

		foreach ( $array as $index => $value ) {
			$key_title_pair_array[ $value ] = ucwords( str_replace( [ '-', '_' ], ' ', $value ) );
		}

		return $key_title_pair_array;
	}

	/**
	 * Build the nonce key.
	 *
	 * @param string $post_type The post type.
	 *
	 * @return string|string[]
	 */
	protected function build_export_ajax_nonce_key( $post_type ) {
		return str_replace( '-', '_', $post_type . '_' . self::SLUG );
	}

	/**
	 * Gather the final csv header keys and titles.
	 * // TODO tags, categories, taxonomies.
	 *
	 * @param string $post_type The post type.
	 *
	 * @return array
	 */
	public function get_csv_headers( $post_type = '' ) {

		if ( empty( $post_type ) ) {
			$post_type = $this->post_variables( 'post_type' );
		}

		// Gather the allowed keys.
		$post_keys      = $this->get_post_keys( $post_type );
		$post_meta_keys = $this->get_meta_keys_for_post_type( $post_type );

		// Sort allowed keys with chosen keys to obtain the csv headers.
		$csv_headers = array_merge( $post_keys, $post_meta_keys );
		foreach ( $csv_headers as $key => $title ) {
			if ( empty( $this->post_variables( $key ) ) ) {
				unset( $csv_headers[ $key ] );
			}
		}

		return $csv_headers;
	}

	/**
	 * Build the CSV rows.
	 *
	 * @param array $csv_headers An array of headers to use as the row keys.
	 *
	 * @return array
	 */
	public function get_csv_rows( $csv_headers ) {

		$post_type   = $this->post_variables( 'post_type' );
		$date_after  = $this->post_variables( 'date_after' );
		$date_before = $this->post_variables( 'date_before' );

		$wp_query_params = [
			'post_type'      => $post_type,
			'posts_per_page' => - 1,
		];

		if ( ! empty( $date_after ) && ! empty( $date_before ) ) {
			$wp_query_params['date_query'] = [
				[
					'after'     => $date_after,
					'before'    => $date_before,
					'inclusive' => true,
				],
			];
		}

		$this->response['data']['wp_query'] = $wp_query_params;

		$posts = get_posts( $wp_query_params );

		$rows = [];

		if ( empty( $posts ) ) {
			$this->response['message'] = 'There were no posts found.';
			$this->response['success'] = false;
			return $rows;
		}

		// Loop over each row for each post.
		$index = 0;
		foreach ( $posts as $post ) {

			$post_meta_keys = get_post_custom_keys( $post->ID );
			$rows[ $index ] = [];

			// Loop over the chosen headers.
			foreach ( $csv_headers as $key => $title ) {

				// Add the post keys.
				if ( isset( $post->{$key} ) ) {
					$rows[ $index ][] = $post->{$key};
				}

				// Add the meta keys.
				if ( isset( $post_meta_keys[ $key ] ) ) {
					$rows[ $index ][] = $post_meta_keys[ $key ];
				}
			}

			$index++;
		}

		return $rows;
	}

	/**
	 * Get the $_POST variables.
	 *
	 * @param null|string $key     Key used to target value in $_POST.
	 * @param string      $default $default if key not found.
	 *
	 * @return null|string|array
	 */
	protected function post_variables( $key = null, $default = '' ) {

		if ( null === $this->post_variables ) {
			$this->post_variables                     = $_POST; // phpcs:ignore
			$this->response['data']['post_variables'] = $this->post_variables;
		}

		if ( null !== $key && ! empty( $this->post_variables ) ) {
			$return = isset( $this->post_variables[ $key ] ) ? $this->post_variables[ $key ] : $default;
		} else {
			$return = $this->post_variables;
		}

		return $return;
	}

	/**
	 * Returns the response.
	 *
	 * @return array
	 */
	protected function get_response() {
		return $this->response;
	}

	/**
	 * Serves the built response given as a JSON response and dies.
	 */
	public function json_response() {
		header( 'Content-Type: application/json' );
		die( wp_json_encode( $this->get_response() ) );
	}

	/**
	 * Get the file name for the export.
	 *
	 * @param string $post_type   Optional. The post type to use within the filename.
	 * @param string $date_after  Optional. The date posts are queried "after".
	 * @param string $date_before Optional. The date posts are queried "before".
	 *
	 * @return string
	 */
	protected function get_filename( $post_type = '', $date_after = '', $date_before = '' ) {

		if ( empty( $post_type ) ) {
			$post_type = $this->post_variables( 'post_type' );
		}

		if ( empty( $date_after ) ) {
			$date_after = $this->post_variables( 'date_after' );
		}

		if ( empty( $date_before ) ) {
			$date_before = $this->post_variables( 'date_before' );
		}

		// Start to build the filename string.
		$file_name = $post_type;

		if ( ! empty( $date_after ) && ! empty( $date_before ) ) {
			$file_name .= "__{$date_after}-{$date_before}";
		}

		$file_name .= '__' . gmdate( 'm-d-Y_hia' ) . '.csv';

		return $file_name;
	}


}
