<?php

class Listify_WP_Job_Manager_Customizer {
	
	public function __construct() {
		add_filter( 'listify_customizer_panels', array( $this, 'panels' ) );
	}

	public function panels( $panels ) {
		global $listify_strings;

		$panels[ 'listings' ] = array(
			'title' => $listify_strings->label( 'plural' ),
			'sections' => array( 
				'labels' => array(
					'title' => __( 'Labels & Behavior', 'listify' ),
				),
				'listing-archive' => array(
					'title' => __( 'Archive Page', 'listify' )
				),
				'marker-appearance' => array(
					'title' => __( 'Marker Appearance', 'listify' )
				),
				'map-behavior' => array(
					'title' => __( 'Map Behavior', 'listify' )
				)
			)
		);

		return $panels;
	}

}
