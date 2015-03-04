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
	<div id="secondary" class="widget-area col-md-4 col-sm-5 col-xs-12" role="complementary">
		<?php if ( ! dynamic_sidebar( 'widget-area-sidebar-product' ) ) : ?>

			<?php the_widget( 'WC_Widget_Product_Search', array( 'title' => '' ), $defaults ); ?>

			<?php the_widget( 'WC_Widget_Products', array( 'title' => __( 'Products', 'listify' ), 'show' => '', 'number' => 10, 'orderby' => 'date', 'order' => 'desc', 'hide_free' => 0, 'show_hidden' => 0 ), $defaults ); ?>

		<?php endif; // end sidebar widget area ?>
	</div><!-- #secondary -->
