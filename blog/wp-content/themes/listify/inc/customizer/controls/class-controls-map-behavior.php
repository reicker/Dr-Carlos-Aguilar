<?php

class Listify_Customizer_Controls_Map_Behavior extends Listify_Customizer_Controls {

	public function __construct() {
		$this->section = 'map-behavior';

		parent::__construct();

		add_action( 'customize_register', array( $this, 'add_controls' ), 30 );
		add_action( 'customize_register', array( $this, 'set_controls' ), 35 );
	}

	public function add_controls( $wp_customize ) {
		$this->controls = array(
			'map-behavior-clusters' => array(
				'label' => __( 'Use Clusters', 'listify' ),
				'type' => 'checkbox',
			),
			'map-behavior-grid-size' => array(
				'label' => __( 'Cluster Grid Size (px)', 'listify' )
			),
			'map-behavior-autofit' => array(
				'label' => __( 'Autofit on load', 'listify' ),
				'type' => 'checkbox'
			),
			'map-behavior-center' => array(
				'label' => __( 'Default Center Coordinate', 'listify' )
			),
			'map-behavior-zoom' => array(
				'label' => __( 'Default Zoom Level', 'listify' ),
				'type' => 'select',
				'choices' => array( '1' => '1', '2' => '2', '3' => '3', '4' =>
				'4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9',
				'10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' =>
				'14', '15' => '15', '16' => '16', '17' => '17', '18' => '18' ),
			),
			'map-behavior-max-zoom' => array(
				'label' => __( 'Max Zoom Level', 'listify' ),
				'type' => 'select',
				'choices' => array( '1' => '1', '2' => '2', '3' => '3', '4' =>
				'4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9',
				'10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' =>
				'14', '15' => '15', '16' => '16', '17' => '17', '18' => '18' ),
			),
			'map-behavior-search-min' => array(
				'label' => __( 'Search Radius Min', 'listify' ),
			),
			'map-behavior-search-max' => array(
				'label' => __( 'Search Radius Max', 'listify' )
			)
		);

		return $wp_customize;
	}

	public function get_icon_list() {
		global $iconlist;

		nclude_once( get_template_directory() . '/inc/icon-list.php' );

		$list = array();

		$list[ '' ] = __( 'Default', 'listify' );

		foreach ( $iconlist as $meh => $icon ) {
			$list[$icon->name] = $icon->name;
		}

		return $list;
	}
}

new Listify_Customizer_Controls_Map_Behavior();
