<?php
/**
 * Home: Search Listings
 *
 * @since Listify 1.0.0
 */
class Listify_Widget_Search_Listings extends Listify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $listify_facetwp;

		$this->widget_description = __( 'Display a search form to search listings', 'listify' );
		$this->widget_id          = 'listify_widget_search_listings';
		$this->widget_name        = __( 'Listify - Page: Search Listings', 'listify' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'listify' )
			),
			'description' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Description:', 'listify' )
			)
		);

		if ( listify_has_integration( 'facetwp' ) ) {
			$this->settings[ 'facets' ] = array(
				'type'  => 'text',
				'std'   => $listify_facetwp->get_facets( 'flat' ),
				'label' => __( 'Facets:', 'listify' )
			);
		}

		parent::__construct();

		add_filter( 'facetwp_load_assets', '__return_true' );
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) )
			return;

		extract( $args );

		$title = apply_filters( 'widget_title', $instance[ 'title' ], $instance, $this->id_base );
		$description = isset( $instance[ 'description' ] ) ? esc_attr( $instance[ 'description' ] ) : false;

		$after_title = '<h2 class="home-widget-description">' . $description . '</h2>' . $after_title;

		ob_start();

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

		if ( listify_has_integration( 'facetwp' ) ) {
			global $listify_facetwp;

			$facets = isset( $instance[ 'facets' ] ) ? array_map( 'trim', explode( ',', $instance[ 'facets' ] ) ) : $listify_facetwp->get_facets();

			// limit to 3
			$facets = array_splice( $facets, 0, 3 );

			if ( 3 == count( $facets ) ) {
				$columns = array( 4, 3, 3 );
			} elseif ( 2 == count( $facets ) ) {
				$columns = array( 5, 5 );
			} else {
				$columns = array( 10 );
			}

			$count = 0;
		?>
			<div class="job_search_form">
				<div class="row">
					<?php foreach ( $facets as $facet ) : ?>
						<div class="search_<?php echo $facet; ?> col-xs-12
						col-sm-4 col-md-<?php echo $columns[$count]; ?>">
							<?php echo do_shortcode( '[facetwp facet="' . $facet . '"]' ); ?>
						</div>
					<?php endforeach; ?>

					<div class="col-xs-12 col-sm-12 col-md-2">
						<input type="submit" value="<?php _e( 'Search', 'listify' ); ?>" onclick="FWP.refresh()" />
					</div>
		  		</div>

				<div style="display: none;">
					<?php echo do_shortcode( '[facetwp template="listings"]' ); ?>
				</div>

			</div>

			<script>
			(function($) {
			    $(function() {
			        FWP.auto_refresh = false;
			    });

			    $(document).on( 'facetwp-refresh', function() {
			        if ( FWP.loaded ) {
			            FWP.set_hash();

			            window.location.href = '<?php echo get_post_type_archive_link( 'job_listing' ); ?>' + window.location.hash;
			        }
			    });
			})(jQuery);
			</script>
		<?php
		} else {
			global $listify_job_manager;

			add_action( 'job_manager_job_filters_before', array( $listify_job_manager->template, 'temp_remove_ajax_filters' ) );

			do_action( 'listify_output_results' );

			add_action( 'job_manager_job_filters_after', array( $listify_job_manager->template, 'temp_add_ajax_filters' ) );
		}

		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );

		$this->cache_widget( $args, $content );
	}
}
