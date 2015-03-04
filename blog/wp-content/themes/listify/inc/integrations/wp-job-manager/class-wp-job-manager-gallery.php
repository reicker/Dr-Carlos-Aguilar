<?php

class Listify_WP_Job_Manager_Gallery {

	public static $slug;

	public static $post_id;

	public function __construct() {
		self::$slug = _x( 'gallery', 'gallery endpoint slug', 'listify' );

		/** Frontend */
		add_action( 'init', array( $this, 'add_rewrite_endpoints' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 8 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_localize_scripts' ), 12 );
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );
		add_action( 'wp_ajax_listify_add_to_gallery', array( $this, 'listify_add_to_gallery' ) );

		add_filter( 'attachment_link', array( $this, 'attachment_link' ), 10, 2 );
		add_filter( 'comment_post_redirect', array( $this, 'comment_redirect' ), 10, 2 );

		/** Admin */
		add_action( 'admin_menu', array( $this, 'add_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	public function add_rewrite_endpoints() {
		add_rewrite_endpoint( self::$slug, EP_PERMALINK | EP_PAGES );
	}

	public static function url( $post_id = null ) {
		if ( $post_id ) {
			$base = get_permalink( $post_id );
		} elseif ( isset( self::$post_id ) ) {
			$base = get_permalink( self::$post_id );
		}

		if ( get_option( 'permalink_structure' ) ) {
			$url = $base . trailingslashit( self::$slug );
		} else {
			$url = add_query_arg( 'gallery', true, $base );
		}

		return esc_url( $url );
	}

	public function wp_enqueue_scripts() {
		global $wp_query;

		if ( ! is_singular( 'job_listing' ) || isset( $wp_query->query_vars[ self::$slug ] ) ) {
			return;
		}

		global $post;

		if ( current_user_can( 'upload_files' ) ) {
			wp_enqueue_media( array( 'post' => $post->ID ) );
		}
	}

	public function wp_localize_scripts() {
		wp_localize_script( 'listify', 'listifyListingGallery',
			$this->get_localization()
		);
	}

	public function template_redirect() {
		global $wp_query;

		if ( ! is_singular( 'job_listing' ) ) {
			return;
		}

		if ( isset( $wp_query->query_vars[ self::$slug ] ) ) {
			locate_template( array( 'single-job_listing-gallery.php' ), true );

			exit;
		}
	}

	public function listify_add_to_gallery() {
		check_ajax_referer( 'listify_add_to_gallery', '_nonce' );

		$ids = esc_attr( $_POST[ 'ids' ] );

		if ( ! $ids ) {
			wp_send_json_error();
		}

		$ids = explode( ',', $ids );
		$post_id = absint( $_POST[ 'post_id' ] );

		if ( ! is_array( $ids ) ) {
			wp_send_json_error();
		}

		$current = self::get( $post_id );

		$gallery = array_unique( array_merge( $ids, $current ) );
		$string = implode( ',', $gallery );

		$shortcode = '[gallery ids=' . $string . ']';

    // update the "real" field as well that contains an array of URLS
    $urls = array();

    foreach ( $gallery as $image ) {
      $src = wp_get_attachment_image_src( $image, 'full' );
      $urls[] = $src[0];
    }

    update_post_meta( $post_id, '_gallery_images', $urls );

		self::set( $post_id, $shortcode );

		wp_send_json_success();
	}

	/**
	 *
	 */
	public function attachment_link( $url, $id ) {
		global $wp_query;

		if ( ! is_singular( 'job_listing' ) || isset( $wp_query->query_vars[ 'gallery' ] ) ) {
			return $url;
		}

		$gallery = self::get( get_queried_object_id() );
		$new = self::url() . '#' . $url;

		return $new;
	}

	public function comment_redirect( $url, $comment ) {
		$listing = get_post( $comment->comment_post_ID )->post_parent;

		if ( ! $listing || 'job_listing' != get_post_type( $listing ) ) {
			return $url;
		}

		$gallery = self::get( $listing );
		$url = self::url() . '#' . get_permalink( $comment->comment_post_ID );

		return $url;
	}

	public function admin_enqueue_styles () {
		global $pagenow;

		if ( ! ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) ) ) {
			return;
		}

		wp_enqueue_script( 'timepicker', get_template_directory_uri() . '/inc/integrations/wp-job-manager/js/vendor/jquery.timepicker.min.js', array( 'jquery' ) );

		wp_enqueue_script( 'listify-wp-job-manager-gallery-admin', get_template_directory_uri() . '/inc/integrations/wp-job-manager/js/source/wp-job-manager-gallery-admin.js', array( 'jquery' ) );

		wp_localize_script( 'listify-wp-job-manager-gallery-admin', 'listifyListingGallery',
			$this->get_localization()
		);
	}

	public function add_meta_box() {
		$job_listings = get_post_type_object( 'job_listing' );

		add_meta_box( 'job_listing-gallery', sprintf( __( '%s Gallery', 'listify' ), $job_listings->labels->singular_name ), array( $this, 'meta_box_set_gallery' ), $job_listings->name, 'side' );
	}

	public function meta_box_set_gallery() {
		global $post;

		$gallery = self::get( $post->ID );

		if ( ! $gallery ) {
			$gallery = array();
		}

		$shortcode = '[gallery ids=' . implode( ',', $gallery ) . ']';
		$limit = 99999;
	?>

		<div class="listify-gallery-images-wrapper">
			<?php
				include( locate_template( array( 'content-single-job_listing-gallery-overview.php' ) ) );
			?>

			<input type="hidden" name="listify_gallery_images" id="listify_gallery_images" value="<?php echo esc_attr( $shortcode ); ?>" />

		</div>

		<p class="listify-add-gallery-images hide-if-no-js" style="clear: left;">
			<a href="#" class="manage"><?php _e( 'Manage gallery images', 'listify' ); ?></a> &bull;
			<a href="#" class="remove"><?php _e( 'Clear gallery', 'listify' ); ?></a>
		</p>

	<?php
	}

	public function save_post( $post_id ) {
		global $post;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! is_object( $post ) ) {
			return;
		}

		if ( 'job_listing' != $post->post_type ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}

		$gallery = esc_attr( $_POST[ 'listify_gallery_images' ] );

		if ( ! $gallery ) {
			return;
		}

		self::set( $post->ID, $gallery );
	}

	public static function get( $post_id ) {
    	self::$post_id = $post_id;

		$gallery = get_post_meta( $post_id, '_gallery', true );

    	if ( ! $gallery ) {
      		return;
    	}

    	if ( is_string( $gallery ) ) { 
      		$gallery = self::parse_shortcode( $gallery );
    	}

    	return $gallery;  
	}

	public static function set( $post_id, $gallery ) {
		return update_post_meta( $post_id, '_gallery', $gallery );
	}

	private static function parse_shortcode( $shortcode ) {
		$pattern = get_shortcode_regex();
		preg_match( "/$pattern/s", $shortcode, $match );
		$atts = shortcode_parse_atts( $match[3] );

		if ( isset( $atts['ids'] ) ) {
			$shortcode = explode( ',', $atts['ids'] );
		} else {
			$shortcode = array();
		}

		return $shortcode;
	}

	private function get_localization() {
		return array(
			'canUpload'         => current_user_can( 'upload_files' ),
			'gallery_title' 	=> __( 'Add Images to Gallery', 'listify' ),
			'gallery_button' 	=> __( 'Add to gallery', 'listify' ),
			'delete_image'		=> __( 'Delete image', 'listify' ),
			'default_title' 	=> __( 'Upload', 'listify' ),
			'default_button' 	=> __( 'Select this', 'listify' ),
		);
	}

}
