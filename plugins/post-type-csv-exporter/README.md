# USE

## BASIC
1. Copy this plugins directory into your WordPress install
1. Use a class or function to add the settings page.
   simple, replacing <post_type> with your post type;
```php
add_action( 'init', 'add_csv_exporter_admin_page', 100 );
function add_csv_exporter_admin_page() {
    if ( class_exists( '\\PostTypeCSVExporter\\Library\\CSVExporter' ) ) {
        new \PostTypeCSVExporter\Library\CSVExporter( '<post_type>' );
    }
}
```

## ADVANCED
Adding custom columns and data related to your post type.
```php

add_filter( 'post_type_csv_exporter_post_keys_<post_type>', 'csv_exporter_post_keys' );
/**
 * Filter the post keys for selection on the export page.
 *
 * @param array $post_keys The post keys as an array.
 *
 * @return mixed
 */
function csv_exporter_post_keys( $post_keys ) {

    // Remove meta keys that we know are not required.
    unset( $post_keys['post_date_gmt'] );
    unset( $post_keys['post_content'] );
    unset( $post_keys['post_excerpt'] );
    unset( $post_keys['comment_status'] );
    unset( $post_keys['ping_status'] );
    unset( $post_keys['post_password'] );
    unset( $post_keys['post_name'] );
    unset( $post_keys['to_ping'] );
    unset( $post_keys['pinged'] );
    unset( $post_keys['post_modified_gmt'] );
    unset( $post_keys['post_content_filtered'] );
    unset( $post_keys['post_parent'] );
    unset( $post_keys['guid'] );
    unset( $post_keys['menu_order'] );
    unset( $post_keys['post_type'] );
    unset( $post_keys['post_mime_type'] );
    unset( $post_keys['comment_count'] );
    unset( $post_keys['filter'] );

    return $post_keys;
}

add_filter( 'post_type_csv_exporter_post_meta_keys_<post_type>','csv_exporter_post_meta_keys' );
/**
 * Filter the post meta keys for selection on the export page.
 *
 * @param array $post_meta_keys The post meta keys as an array.
 *
 * @return mixed
 */
 function csv_exporter_post_meta_keys( $post_meta_keys ) {

    // Remove post meta keys that we know are not required.
    unset( $post_meta_keys['blank_page_template_enabled'] );
    unset( $post_meta_keys['blank_page_template_tracking_code'] );
    unset( $post_meta_keys['disable_feature_image'] );
    unset( $post_meta_keys['exclude_from_partner_feed'] );
    unset( $post_meta_keys['_edit_lock'] );

    return $post_meta_keys;
}

add_filter( 'post_type_csv_exporter_post_extra_keys_and_callbacks_<post_type>', 'csv_exporter_post_extra_keys_and_callbacks'  );
/**
 * Add additional keys for the post type exporter.
 * The values are gathered with callbacks.
 *
 * @param array $post_extra_keys An array of extra keys.
 *
 * @return array
 */
function csv_exporter_post_extra_keys_and_callbacks( $post_extra_keys ) {

    $post_extra_keys = [
        'product_type' => 'get_product_type_callback',
    ];

    return array_merge( $post_extra_keys, $post_extra_keys );
}

function get_product_type_callback( $post_id ) {
    /**
     * This is just an example.
     * With the $post_id if it had the product id stored as meta we could get the product
     * then get the product type from that products meta, returning that as the value.
     */
}
```