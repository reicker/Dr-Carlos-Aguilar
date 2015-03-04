<?php
/**
 * WooCommerce
 */

class Listify_WooCommerce extends listify_Integration {

	public function __construct() {
		$this->includes = array(
			'class-woocommerce-template.php'
		);

		$this->integration = 'woocommerce';

		parent::__construct();
	}

	public function setup_actions() {
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		// add_filter( 'login_url', array( $this, 'login_url' ), 10, 2 );

		add_filter( 'user_contactmethods', array( $this, 'user_contactmethods' ), 10, 2 );

		add_action( 'woocommerce_edit_account_form', array( $this, 'woocommerce_edit_account_form' ) );
		add_action( 'woocommerce_save_account_details', array( $this, 'woocommerce_save_account_details' ) );
	}

	public function after_setup_theme() {
		add_theme_support( 'woocommerce' );
	}

	public function widgets_init() {
		$widgets = array(
			'job_listing-social-profiles.php'
		);

		foreach ( $widgets as $widget ) {
			include_once( listify_Integration::get_dir() . '/widgets/class-widget-' . $widget );
		}

		register_widget( 'Listify_Widget_Listing_Social_Profiles' );

		register_sidebar( array(
			'name'          => __( 'Product Sidebar', 'listify' ),
			'id'            => 'widget-area-sidebar-product',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h1 class="widget-title %s">',
			'after_title'   => '</h1>',
		) );

		register_sidebar( array(
			'name'          => __( 'Shop Sidebar', 'listify' ),
			'id'            => 'widget-area-sidebar-shop',
			'before_widget' => '<aside id="%1$s" class="widget widget-shop %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h1 class="widget-title %s">',
			'after_title'   => '</h1>',
		) );
	}

	public function login_url( $url, $redirect ) {
		$url = add_query_arg( 'redirect_to', $redirect, get_permalink( wc_get_page_id( 'myaccount' ) ) );

		return $url;
	}

	public function user_contactmethods( $methods, $user ) {
		$methods[ 'twitter' ] = __( 'Twitter URL', 'listify' );
		$methods[ 'facebook' ] = __( 'Facebook URL', 'listify' );
		$methods[ 'googleplus' ] = __( 'Google+ URL', 'listify' );
		$methods[ 'pinterest' ] = __( 'Pinterest URL', 'listify' );
		$methods[ 'linkedin' ] = __( 'LinkedIn URL', 'listify' );
		$methods[ 'github' ] = __( 'GitHub URL', 'listify' );

		return $methods;
	}

	public function woocommerce_edit_account_form() {
		$methods = wp_get_user_contact_methods( get_current_user_id() );

		if ( empty( $methods ) ) {
			return;
		}

		$user = wp_get_current_user();
	?>

		<fieldset>
			<legend><?php _e( 'Biography', 'listify' ); ?></legend>

			<p class="form-row form-row-wide">
				<label for="biography" class="screen-reader-text"><?php _e( 'Biography', 'listify' ); ?></label>
				<textarea class="input-text" name="biography" id="biography"><?php echo esc_textarea( $user->description ); ?></textarea>
			</p>
		</fieldset>

		<fieldset>
			<legend><?php _e( 'Social Profiles', 'listify' ); ?></legend>

			<?php foreach ( $methods as $method => $label ) : ?>
				<p class="form-row form-row-wide">
					<label for="<?php echo esc_attr( $method ); ?>"><?php echo esc_attr( $label ); ?></label>
					<input type="text" class="input-text" name="<?php echo esc_attr( $method ); ?>" id="<?php echo esc_attr( $method ); ?>" value="<?php echo esc_attr( $user->$method ); ?>" />
				</p>
			<?php endforeach; ?>
		</fieldset>

	<?php
	}

	public function woocommerce_save_account_details( $user_id ) {
		$methods = wp_get_user_contact_methods( get_current_user_id() );

		if ( empty( $methods ) ) {
			return;
		}

		foreach ( $methods as $method => $label ) {
			$value = isset( $_POST[ $method ] ) ? esc_url( $_POST[ $method ] ) : null;

			update_user_meta( $user_id, $method, $value );
		}

		if ( isset( $_POST[ 'biography' ] ) ) {
			$biography = esc_textarea( $_POST[ 'biography' ] );

			update_user_meta( $user_id, 'description', $biography );
		}
	}

}

$GLOBALS[ 'listify_woocommerce' ] = new Listify_WooCommerce();
