<?php
/**
 * Simple fragment cache using WordPress transients.
 *
 * @file    plugins/post-type-csv-exporter/source/php/Library/FragmentCache.php
 * @package PostTypeCSVExporter
 */

// phpcs:disable WordPress.Files.FileName

namespace PostTypeCSVExporter\Library;

/**
 * Simple fragment cache using WordPress transients.
 */
class FragmentCache {

	const GROUP = 'cache-fragments';

	/**
	 * Cache key.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * Time to loss.
	 *
	 * @var int
	 */
	protected $ttl;

	/**
	 * Constructor.
	 *
	 * @param string $key Cache key.
	 * @param int    $ttl Time to loss, how long hte item will remain cached.
	 */
	public function __construct( $key, $ttl ) {
		$this->key = $key;
		$this->ttl = $ttl;
	}

	/**
	 * Expands on the WordPress set transient function to use the generated key from this class.
	 *
	 * @param mixed $value Fragment cached value to store in cache.
	 */
	public function set_transient( $value ) {
		set_transient( $this->key(), $value, $this->ttl );
	}

	/**
	 * Expands on the WordPress get transient function to use the generated key from this class.
	 *
	 * @return mixed Value of transient.
	 */
	public function get_transient() {
		return get_transient( $this->key() );
	}

	/**
	 * Output the cache content, if it is available.
	 *
	 * @return boolean
	 */
	public function output() {

		$did_output = false;

		$output = $this->get_transient();

		if ( ! empty( $output ) ) { // It was in the cache.

			// phpcs:disable

			echo sprintf(
				'<!-- /fragment output (key=%s, ttl=%ss) -->',
				esc_html( $this->key ),
				esc_html( $this->ttl )
			);

			echo $output;

			echo sprintf(
				'<!-- fragment output (key=%s) -->',
				esc_html( $this->key )
			);

			// phpcs:enable

			$did_output = true;

		} else {

			// phpcs:disable

			echo sprintf(
				'<!-- fragment build (key=%s) -->',
				esc_html( $this->key )
			);

			// phpcs:enable

			// Buffer the output from now onwards until store() is called.
			ob_start();

		}

		return $did_output;

	}

	/**
	 * Store/cache the output.
	 */
	public function store() {

		// Get the buffered output to be cached.
		$output = ob_get_flush(); // Also flushes the buffers.
		$this->set_transient( $output );

		// Output the cached output.
		// phpcs:disable

		echo sprintf(
			'<!-- /fragment build (key=%s, ttl=%ss) -->',
			esc_html( $this->key ),
			esc_html( $this->ttl )
		);

		// phpcs:enable

	}

	/**
	 * Returns the encoded cache key.
	 *
	 * @return string
	 */
	public function key() {

		$key = $this->key;

		// Whether we are on a secure connection.
		$is_https = (
				isset( $_SERVER['HTTPS'] ) &&
				! empty( $_SERVER['HTTPS'] ) &&
				'off' !== $_SERVER['HTTPS']
			) || (
				isset( $_SERVER['SERVER_PORT'] ) &&
				443 === absint( $_SERVER['SERVER_PORT'] )
			);

		// Make unique if is HTTPS v HTTP.
		if ( $is_https ) {
			$key = $key . '_https';
		}

		// Has to an acceptable length.
		return 'frag_' . md5( $key . self::GROUP );

	}

}
