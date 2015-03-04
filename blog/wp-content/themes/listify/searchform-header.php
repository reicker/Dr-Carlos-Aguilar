<?php
/**
 * The template for displaying the header search form. Searches listings.
 *
 * @package Listify
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( get_post_type_archive_link(
'job_listing' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php _e( 'Search for:', 'listify' ); ?></span>
		<input type="search" class="search-field" placeholder="Search" value="" name="search_keywords" title="Search for:" />
	</label>
	<button type="submit" class="search-submit"><i class="ion-search-strong"></i></button>
</form>
