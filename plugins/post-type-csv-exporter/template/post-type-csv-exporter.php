<?php
/**
 * Contains the template for the post type exporter admin page.
 *
 * @file    plugins/post-type-csv-exporter/template/post-type-csv-exporter.php
 * @package PostTypeCSVExporter
 *
 * Variable Type Definition.
 *
 * @var string $page_title     Page Title
 * @var array  $post_keys      An array of WP_Post object keys.
 * @var array  $post_meta_keys An array of post meta keys for this post type.
 */

?>
<div class="wrap">

	<h1 class="wp-header-inline"><?php echo esc_html( $page_title ) ?></h1>

	<div id="page-message"></div>

	<form id="post-type-csv-exporter-form" method="post" name="post-type-csv-exporter-form" action="#">

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">

				<div id="post-body-content" class="postbox-container">
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">

						<div class="postbox csv-exporter-postbox">
							<h2 class="hndle">Export Date Options</h2>
							<div class="inside">
								<div class="form-field">
									<div class="form-field--label">
										<label for="date_after">Date From</label>
									</div>
									<div class="form-field--input">
										<input id="date_after" name="date_after" class="csv-export-date-picker">
									</div>
								</div>
								<div class="form-field">
									<div class="form-field--label">
										<label for="date_before">Date End</label>
									</div>
									<div class="form-field--input">
										<input id="date_before" name="date_before" class="csv-export-date-picker">
									</div>
								</div>
							</div>
						</div>

						<div class="postbox csv-exporter-postbox">
							<h2 class="hndle">Export Post keys</h2>
							<div class="inside">
								<?php foreach ( $post_keys as $post_key => $post_key_title ) : ?>
									<div class="form-field">
										<div class="form-field--input-label">
											<label for="<?php echo esc_attr( $post_key ); ?>">
												<input type="checkbox"
													id="<?php echo esc_attr( $post_key ); ?>"
													name="<?php echo esc_attr( $post_key ); ?>">
												<?php echo esc_html( $post_key_title ); ?>
											</label>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>

						<div class="postbox csv-exporter-postbox">
							<h2 class="hndle">Export Post keys</h2>
							<div class="inside">
								<?php foreach ( $post_meta_keys as $post_meta_key => $post_meta_key_title ) : ?>
									<div class="form-field">
										<div class="form-field--input-label">
											<label for="<?php echo esc_attr( $post_meta_key ); ?>">
												<input type="checkbox"
													id="<?php echo esc_attr( $post_meta_key ); ?>"
													name="<?php echo esc_attr( $post_meta_key ); ?>">
												<?php echo esc_html( $post_meta_key_title ); ?>
											</label>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>

				<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables ui-sortable">
						<div id="submitdiv" class="postbox">
							<h2><span>Request</span></h2>
							<div class="inside">
								<div class="submitbox" id="submitpost">
									<div id="major-publishing-actions">
										<div id="publishing-action">
											<span class="spinner"></span>
											<button type="submit" class="button button-primary button-large" id="post-type-csv-exporter-submit">Export To CSV</button>
										</div>
										<div class="clear"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
			<br class="clear">
		</div>

	</form>
</div>
