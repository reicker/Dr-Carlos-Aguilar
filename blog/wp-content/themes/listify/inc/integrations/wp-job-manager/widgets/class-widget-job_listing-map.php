<?php
/**
 * Job Listing: Map
 *
 * @since Listify 1.0.0
 */
class Listify_Widget_Listing_Map extends Listify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_description = __( 'Display the listing location and contact details.', 'listify' );
		$this->widget_id          = 'listify_widget_panel_listing_map';
		$this->widget_name        = __( 'Listify - Listing: Map & Contact Details', 'listify' );
		$this->settings           = array(
			'map' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Display map', 'listify' )
			),
			'address' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Display address', 'listify' )
			),
			'phone' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Display phone number', 'listify' )
			),
			'web' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Display website', 'listify' )
			)
		);
		parent::__construct();
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

		global $job_manager, $post, $listify_job_manager;

		if ( '' == $post->geolocation_lat ) {
			return;
		}

		$listify_job_manager->map->enqueue_scripts(true);

		extract( $args );

		$icon = isset( $instance[ 'icon' ] ) ? $instance[ 'icon' ] : null;
		$fields = array( 'map', 'address', 'phone', 'web' );

		foreach ( $fields as $field ) {
			$$field = isset( $instance[ $field ] ) && 1 == $instance[ $field ] ? true : false;
		}

		if ( $icon ) {
			$before_title = sprintf( $before_title, 'ion-' . $icon );
		}

		ob_start();

		echo $before_widget;
		?>

		<div itemscope itemtype="http://schema.org/LocalBusiness" class="row">
			<?php if ( $map ) : ?>
				<div class="col-md-6 col-sm-12">
					<div id="listing-contact-map"></div>

					<script>
						var map,
						    latLng;
						function initialize() {
							latLng = new google.maps.LatLng('<?php echo $post->geolocation_lat; ?>', '<?php echo $post->geolocation_long; ?>');

							var isDraggable = window.innerWidth > 480 ? true : false;

							var mapOptions = {
								zoom: 15,
								center: latLng,
								scrollwheel: false,
								draggable: isDraggable
							};

							map = new google.maps.Map(
								document.getElementById('listing-contact-map'),
								mapOptions
							);

							var marker = new RichMarker({
								position: latLng,
								flat: true,
								meta: this.data,
								draggable: false,
								content:
								'<div class="map-marker type-' + jQuery( '.single_job_listing' ).data( 'term' ) + '">' +
									'<i class="' + jQuery( '.single_job_listing' ).data( 'icon' ) + '"></i>' +
								'</div>'
							});

							marker.setMap( map );
						}

						google.maps.event.addDomListener(window, 'load', initialize);
					</script>
				</div>
			<?php endif; ?>

			<div class="col-md-<?php echo $map ? 6 : 12; ?> col-sm-12">
				<div class="listing-contact-overview">
					<div class="listing-contact-overview-inner">
					<?php
						if ( $address ) :
							$listify_job_manager->template->the_location_formatted();
						endif;

						if ( $phone ) :
							$listify_job_manager->template->the_phone();
						endif;

						if ( $web ) :
							$listify_job_manager->template->the_url();
						endif;
					?>
					</div>
				</div>
			</div>
		</div>

		<?php
		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );

		add_filter( 'listify_page_needs_map', '__return_false' );

		$this->cache_widget( $args, $content );
	}
}
