<?php
/**
 * FacetWP
 */

class Listify_FacetWP extends listify_Integration {

	public $facets;
	public $template;

	public function __construct() {
		$this->includes = array(
			'class-facetwp-template.php',
			'class-facetwp-proximity.php'
		);

		$this->integration = 'facetwp';

		parent::__construct();
	}

	public function setup_actions() {
		add_action( 'init', array( $this, 'init' ), 0 );
		
		add_filter( 'listify_pre_controls_listing-archive', array( $this, 'add_customizer_controls' ), 10, 3 );
		add_filter( 'listify_theme_mod_defaults', array( $this, 'add_customizer_defaults' ) );
	}

	public function init() {
		$this->template = new Listify_FacetWP_Template;

		add_filter( 'facetwp_query_args', array( $this, 'facetwp_query_args' ), 10, 2 );
		add_filter( 'facetwp_template_html', array( $this, 'facetwp_template_html' ), 10, 2 );
	}

	public function facetwp_template_html( $output, $class ) {
		if ( 'listings' != $class->template[ 'name' ] ) {
			return $output;
		}

		$query = new WP_Query( $class->query_args );

		ob_start();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				get_template_part( 'content', 'job_listing' );
			}
		} else {
			echo '<li class="col-xs-12">';
			get_template_part( 'content', 'none' );
			echo '</li>';
		}

		$output = ob_get_clean();

		return $output;
	}

	public function facetwp_query_args( $query_args, $facet ) {
		if ( 'listings' != $facet->template[ 'name' ] ) {
			return $query_args;
		}

		if ( '' == $query_args ) {
			$query_args = array();
		}

		$defaults = array(
			'post_type' => 'job_listing',
			's' => isset( $facet->http_params[ 'get' ][ 's' ] ) ? $facet->http_params[ 'get' ][ 's' ] : ''
		);

		$query_args = wp_parse_args( $query_args, $defaults );

		return $query_args;
	}

	public function add_customizer_controls( $controls, $section, $wp_customize ) {
		$controls[ 'listing-archive-facetwp-position' ] = array(
			'label' => __( 'FacetWP Filter Position', 'listify' ),
			'type' => 'select',
			'choices' => array(
				'side' => __( 'Side', 'listify' ),
				'top' => __( 'Top', 'listify' )
			)
		);

		$controls[ 'listing-archive-facetwp-defaults' ] = array(
			'label' => __( 'FacetWP Filters', 'listify' ),
			'description' => __( 'A comma (,) separated list of FacetWP facet slugs. The order they
			appear here will be the order they appear on your website.' )
		);

		return $controls;
	}

	public function add_customizer_defaults( $defaults ) {
		$defaults[ 'listing-archive-facetwp-position' ] = 'side';
		$defaults[ 'listing-archive-facetwp-defaults' ] = 'keyword, location, category';

		return $defaults;
	}

	public function get_facets( $flat = false ) {
		$facets = listify_theme_mod( 'listing-archive-facetwp-defaults' );

		if ( $flat ) {
			return $facets;
		}

		$facets = array_map( 'trim', explode( ',', $facets ) );

		return $facets;
	}

}

$GLOBALS[ 'listify_facetwp' ] = new Listify_FacetWP();
