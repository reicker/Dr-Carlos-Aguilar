<?php
/**
 * The template for the gallery upload modal.
 *
 * @package Listify
 */
?>

<div id="add-photo" class="popup">

	<h2 class="popup-title"><?php _e( 'Upload Images', 'listify' ); ?></h2>

	<div class="content-single-job_listing-upload-area">
		<a href="#" class="upload-images">
			<span class="upload-area">
				<i class="ion-ios7-cloud-upload-outline"></i>
			</span>
			<?php _e( 'Click to upload...', 'listify' ); ?>
		</a>

		<ul id="listify-new-gallery-additions" class="listify-gallery-images">

		</ul>

		<form action="" method="post" class="listify-add-to-gallery">
			<input type="hidden" name="listify_gallery_images" id="listify-new-gallery-images" value="" />
			<input type="submit" name="submit" value="<?php esc_attr_e( 'Add Images to Gallery', 'listify' ); ?>" />
			<input type="hidden" name="post_id" id="post_id" value="<?php echo get_post()->ID; ?>" />
			<input type="hidden" name="redirect" id="gallery-redirect" value="<?php echo esc_url( Listify_WP_Job_Manager_Gallery::url( get_post()->ID ) ); ?>" />
			<?php wp_nonce_field( 'listify_add_to_gallery' ) ?>
		</form>
	</div>

</div>