<?php
/**
 * The Sidebar containing the widget areas for WooCommerce
 *
 * @package Listify
 */

$defaults = array(
	'before_widget' => '<aside class="widget widget-product">',
	'after_widget'  => '</aside>',
	'before_title'  => '<h1 class="widget-title widget-title-product %s">',
	'after_title'   => '</h1>',
	'widget_id'     => ''
);
?>

	<div id="secondary" class="col-xs-12 col-md-4" role="complementary">

		<a href="#" data-toggle="woocommerce-filters" class="js-toggle-area-trigger"><?php _e( 'Toggle Filters', 'listify' ); ?></a>

		<div class="js-toggle-area content-box woocommerce-filters">

			<?php if ( ! dynamic_sidebar( 'widget-area-sidebar-shop' ) ) : ?>

				<?php the_widget( 'WC_Widget_Product_Search', array( 'title' => '' ), $defaults ); ?>

				<?php the_widget( 'WC_Widget_Products', array( 'title' => __( 'Products', 'listify' ), 'show' => '', 'number' => 10, 'orderby' => 'date', 'order' => 'desc', 'hide_free' => 0, 'show_hidden' => 0 ), $defaults ); ?>

			<?php endif; ?>

		</div>

	</div><!-- #secondary -->
