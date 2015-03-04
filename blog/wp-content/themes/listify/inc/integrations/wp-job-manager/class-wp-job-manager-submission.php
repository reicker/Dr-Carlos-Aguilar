<?php

class Listify_WP_Job_Manager_Submission extends Listify_Integration {

	public function __construct() {
		$this->includes = array();
		$this->integration = 'wp-job-manager';

		parent::__construct();
	}

	public function setup_actions() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		if ( 1 != listify_theme_mod( 'custom-submission' ) ) {
			return;
		}

		global $listify_job_manager;

		$listify_job_manager->business_hours = new Listify_WP_Job_Manager_Business_Hours;

		add_filter( 'submit_job_form_fields', array( $this, 'remove_company' ) );
		add_filter( 'submit_job_form_fields', array( $this, 'job_category' ) );
		add_filter( 'submit_job_form_fields', array( $this, 'job_tag' ) );
		add_filter( 'submit_job_form_fields', array( $this, 'contact' ) );
		add_filter( 'submit_job_form_fields', array( $this, 'featured_image' ) );
		add_filter( 'submit_job_form_fields', array( $this, 'gallery_images' ) );

		add_filter( 'submit_job_form_fields', array( $this, 'phone' ) );
		add_filter( 'job_manager_job_listing_data_fields', array( $this, 'admin_phone' ) );

		add_filter( 'job_manager_job_listing_data_fields', array( $this, 'admin_remove_company' ) );

		add_filter( 'submit_job_form_fields_get_job_data', array( $this, 'get_job_data' ), 10, 2 );

		add_action( 'job_manager_update_job_data', array( $this, 'save_featured_image' ), 10, 2 );
		add_action( 'job_manager_update_job_data', array( $this, 'save_gallery_images' ), 10, 2 );

		add_filter( 'the_company_logo', array( $this, 'the_company_logo' ), 10, 2 );

		add_filter( 'submit_job_form_save_job_data', array( $this, 'enable_comments' ), 10, 5 );
	}

	public function remove_company( $fields ) {
		unset( $fields[ 'company' ][ 'company_name' ] );
		unset( $fields[ 'company' ][ 'company_tagline' ] );
		unset( $fields[ 'company' ][ 'company_twitter' ] );
		unset( $fields[ 'company' ][ 'company_logo' ] );

		return $fields;
	}

	public function admin_remove_company( $fields ) {
		unset( $fields[ '_company_name' ] );
		unset( $fields[ '_company_tagline' ] );
		unset( $fields[ '_company_twitter' ] );
		unset( $fields[ '_company_logo' ] );
		unset( $fields[ '_filled' ] );

		return $fields;
	}

	public function job_category( $fields ) {
		$fields[ 'job' ][ 'job_category' ][ 'type' ] = 'term-select';

		return $fields;
	}

	public function contact( $fields ) {
		$fields[ 'job' ][ 'application' ][ 'priority' ] = 2.5;

		return $fields;
	}

	public function job_tag( $fields ) {
		if ( ! isset( $fields[ 'job' ][ 'job_tags' ] ) ) {
			return $fields;
		}

		$fields[ 'job' ][ 'job_tags' ][ 'type' ] = 'term-multiselect';
		$fields[ 'job' ][ 'job_tags' ][ 'taxonomy' ] = 'job_listing_tag';

		return $fields;
	}

	public function phone( $fields ) {
		$fields[ 'company' ][ 'phone' ] = array(
				'label' => __( 'Phone Number', 'listify' ),
				'type' => 'text',
				'placeholder' => '',
				'required' => false,
				'priority' => 2.5,
				);

		return $fields;
	}

	public function admin_phone( $fields ) {
		$field = array(
				'_phone' => array(
					'label' => __( 'Company phone', 'listify' ),
					'placeholder' => ''
					)
				);

		return array_slice( $fields, 0, 4, true ) + $field + array_slice( $fields, 4, null, true );
	}

	public function featured_image( $fields ) {
		$fields[ 'job' ][ 'featured_image' ] = array(
				'label'       => __( 'Cover Image', 'listify' ),
				'type'        => 'file',
				'required'    => false,
				'placeholder' => '',
				'priority'    => 4.99,
				'allowed_mime_types' => array(
					'jpg' => 'image/jpeg',
					'gif' => 'image/gif',
					'png' => 'image/png'
					)
				);

		return $fields;
	}

	public function gallery_images( $fields ) {
		$fields[ 'job' ][ 'gallery_images' ] = array(
				'label'       => __( 'Gallery Images', 'listify' ),
				'type'        => 'file',
				'multiple'    => true,
				'required'    => false,
				'placeholder' => '',
				'priority'    => 4.999,
				'allowed_mime_types' => array(
					'jpg' => 'image/jpeg',
					'gif' => 'image/gif',
					'png' => 'image/png'
					)
				);

		return $fields;
	}

	public function get_job_data( $fields, $job ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $job->ID ),
				'full' );

		if ( isset( $image[0] ) ) {
			$fields[ 'job' ][ 'featured_image' ][ 'value' ] = esc_url( $image[0] );
		}

		return $fields;
	}

	public function save_featured_image( $job_id, $values ) {
		$attachment_url = $values[ 'job' ][ 'featured_image' ];
		$attach_id = listify_get_attachment_id_by_url( $attachment_url );

		if ( $attach_id != get_post_thumbnail_id( $job_id ) ) {
			set_post_thumbnail( $job_id, $attach_id );
		} elseif( '' == $attachment_url && has_post_thumbnail( $job_id ) ) {
			delete_post_thumbnail( $job_id );
		}
	}

	public function save_gallery_images( $job_id, $values ) {
		$images = $values[ 'job' ][ 'gallery_images' ];

		if ( ! isset( $images ) || empty( $images ) ) {
			return;
		}

		$gallery = array();

		foreach ( $images as $image ) {
			$gallery[] = listify_get_attachment_id_by_url( $image );
		}

		$gallery = implode( ',', $gallery );

		$shortcode = '[gallery ids=' . $gallery . ']';

		update_post_meta( $job_id, '_gallery', $shortcode );    
	}

	public function the_company_logo( $logo, $post ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );

		if ( ! $post ) {
			return $logo;
		}

		$logo = $image[0];

		return $logo;
	}

	public function enable_comments( $args, $post_title, $post_content, $status, $values ) {
		$args[ 'comment_status' ] = 'open';

		return $args;
	}
}
