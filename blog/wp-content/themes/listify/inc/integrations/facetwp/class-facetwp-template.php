<?php

class Listify_FacetWP_Template extends listify_FacetWP {

	public function __construct() {
		global $listify_job_manager;

		// Global template override
		add_filter( 'template_include', array( $this, 'template_include' ) );

		// Archive Listings
		remove_all_actions( 'listify_output_results' );
		add_action( 'listify_output_results', array( $this, 'output_results' ), 20 );
		add_action( 'listify_output_results', array( $this, 'output_filters' ) );

		add_action( 'archive_job_listing_layout_before', array( $this, 'archive_job_listing_layout_before' ) );

		if ( 'side' == $this->position() ) {
			add_action( 'listify_sidebar_archive_job_listing_after', array( $this, 'output_filters' ) );
		} else {
			add_action( 'listify_output_results', array( $this, 'output_filters' ), 10 );
		}
	}

	public function position() {
		global $listify_job_manager;

		$position = listify_theme_mod( 'listing-archive-facetwp-position' );

		// Force if the map is already on the side
		if ( ( 'side' == $listify_job_manager->map->position() && $listify_job_manager->map->display() ) ||
		listify_is_widgetized_page() ) {
			$position = 'top';
		}

		return $position;
	}

	public function template_include( $template ) {
		$path = 'inc/integrations/facetwp/templates';

		if ( is_post_type_archive( 'job_listing' ) ) {
			$new_template = locate_template( array( $path . '/archive-job_listing.php' ) );

			if ( '' != $new_template ) {
				return $new_template;
			}

			return $template;
		}

		return $template;
	}

	public function after_setup_theme() {

	}

	public function output_results() {
		do_action( 'listify_facetwp_sort' );

		echo '<div class="job_listings"><ul class="job_listings">';
			echo do_shortcode( '[facetwp template="listings"]' );
		echo '</ul></div>';

		echo do_shortcode( '[facetwp pager="true"]' );
	}

	public function archive_job_listing_layout_before() {
		echo do_shortcode( '[facetwp sort="true"]' );
	}

	public function output_filters() {
		global $listify_facetwp;

    	if ( did_action( 'listify_output_results' ) && 'side' ==
    	$this->position() )	{
			return;
		}

		if ( 'side' == $this->position() ) {
			$after = $before = '';
		} else {
			$before = '<div class="row">';
			$after = '</div>';
		}

		$facets = $listify_facetwp->get_facets();

		$count        = count( $facets );
		$count        = floor( 12 / ( $count == 0 ? 1 : $count ) );
		$columns      = 'col-lg-' . $count . ' col-md-6 col-sm-12';
		$class        = '';

		if ( 'side' == $this->position() ) {
			$columns = null;
			$class   = 'widget';
		}

		echo '<a href="#" data-toggle=".job_filters" class="js-toggle-area-trigger">' . __( 'Toggle Filters', 'listify' ) . '</a>';

		echo '<div class="job_filters content-box ' . $this->position() . '">';

		echo $before;

			echo $this->output_facets();

		echo $after;

		echo '</div>';
	}

	public function output_facets() {
		global $listify_facetwp;

		$facets  = $listify_facetwp->get_facets();
		$facetwp = FacetWP_Helper::instance();

		$count   = count( $facets );
		$count   = floor( 12 / ( $count == 0 ? 1 : $count ) );
		$columns = 'col-sm-12 col-md-' . $count . '';
		$class   = '';

		if ( 'side' == $this->position() ) {
			$columns = null;
			$class   = 'widget';
		}

		$output = array();

		foreach ( $facets as $facet ) {
			$facet_obj = $facetwp->get_facet_by_name( $facet );
			$title = $facet_obj[ 'label' ];
			$facet = '[facetwp facet="' . $facet . '"]';

			$output[] = '<aside class="' . $class . ' widget-job_listing-archive ' . $columns . '"><h2 class="widget-title">' . esc_attr( $title ) . '</h2>' . do_shortcode( $facet ) . '</aside>';
		}

		return implode( '', $output );
	}

}
