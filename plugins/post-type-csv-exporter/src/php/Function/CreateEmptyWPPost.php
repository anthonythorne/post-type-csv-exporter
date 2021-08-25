<?php
/**
 * Creates an empty WP post object.
 *
 * @file    plugins/post-type-csv-exporter/src/php/Function/CreateEmptyWPPost.php
 * @package PostTypeCSVExporter
 */

// phpcs:disable WordPress.Files.FileName

namespace PostTypeCSVExporter;

/**
 * Creates an empty WP post object.
 *
 * @param int $post_id A fake post ID.
 *
 * @return \WP_Post
 */
function create_empty_wp_post( $post_id = - 99 ) {

	$post                 = new \stdClass();
	$post->ID             = $post_id;
	$post->post_author    = 1;
	$post->post_date      = current_time( 'mysql' );
	$post->post_date_gmt  = current_time( 'mysql', 1 );
	$post->post_title     = 'Sed in ultrices odio, vel porttitor nunc.';
	$post->post_content   = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
	$post->post_status    = 'publish';
	$post->comment_status = 'closed';
	$post->ping_status    = 'closed';
	$post->post_name      = 'fake-page-' . rand( 1, 99999 ); // Append random number to avoid clash.
	$post->post_type      = 'post';
	$post->filter         = 'raw'; // Leave in  place, this is important.

	return new \WP_Post( $post );
}
