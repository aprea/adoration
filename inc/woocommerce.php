<?php
/**
 * Sets up custom WooCommerce specific filters and actions for the theme.
 *
 * Defines overrides for WooCommerce pluggable functions.
 *
 * Defines WooCommerce helper / wrapper functions.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Exit early if we detect that WooCommerce isn't activated.
if ( ! is_woocommerce_activated() ) {
	return;
}

/* Remove WooCommerce breadcrumbs */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

/* Remove the WooCommerce content wrappers */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper',     10 );
remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );

/* Remove the sidebar from WooCommerce pages */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );

/* Hide the WooCommerce page titles */
add_filter( 'woocommerce_show_page_title', '__return_false' );

/* Remove the default WooCommerce styles */
add_filter( 'woocommerce_enqueue_styles', '__return_false' );

/* Make the store display a maximum of 12 products per page */
add_filter( 'loop_shop_per_page', 'adoration_products_per_page' );

/* Change the size of the review gravatars */
add_filter( 'woocommerce_review_gravatar_size', 'adoration_review_gravatar_size' );

/* Change the size of the main product image on the product template */
add_filter( 'single_product_large_thumbnail_size', 'adoration_single_product_large_thumbnail_size' );

/* Change the size of the product thumbnails the product template */
add_filter( 'single_product_small_thumbnail_size', 'adoration_single_product_small_thumbnail_size' );

/* Changes the JSON returned by the woocommerce_get_refreshed_fragments AJAX call */
add_filter( 'add_to_cart_fragments', 'adoration_add_to_cart_fragments' );

/* Remove the cross-sell section from the cart page */
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

/* Add the cross-sell section back into the cart page */
add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );

/* Limits the cross-sells section to 2 product columns on the cart template. */
add_filter( 'woocommerce_cross_sells_columns', 'adoration_cross_sells_columns_cart' );

/* Limits the cross-sells section to 2 products total on the cart template. */
add_filter( 'woocommerce_cross_sells_total', 'adoration_cross_sells_total' );

/* Modifies the number of related product columns displayed on the single product template */
add_filter( 'woocommerce_output_related_products_args', 'adoration_output_related_products_args' );

/* Modifies the count html inside of the subcategory listing */
add_filter( 'woocommerce_subcategory_count_html', 'adoration_subcategory_count_html', 10, 2 );

/* WooCommerce starting content wrapper - adds the title / description to WC pages */
add_action( 'woocommerce_before_main_content', 'adoration_woocommerce_before_main_content' );

/* WooCommerce ending content wrapper */
add_action( 'woocommerce_after_main_content', 'adoration_woocommerce_after_main_content' );

/* Modifies the default 'shop_catalog' image size */
add_filter( 'woocommerce_get_image_size_shop_catalog', 'adoration_woocommerce_shop_catalog_image_size' );

/* Remove WooCommerce default pagination */
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

/* Replace WooCommerce default pagination with WordPress built-in pagination */
add_action( 'woocommerce_after_shop_loop', 'adoration_woocommerce_pagination', 10 );

/* Removes WooCommerce products from the default search */
add_action( 'pre_get_posts', 'adoration_remove_woocomerce_from_search' );

/* Adds the rating field back into the review form */
add_filter( 'comment_form_field_comment', 'adoration_woocommerce_review_rating_field' );

/* Changes the "Sale!" text to "Sale" */
add_filter( 'woocommerce_sale_flash', 'adoration_sale_flash' );

/* Registers and unregisters WC Widgets */
add_action( 'widgets_init', 'adoration_register_wc_widgets' );

/* Removes the "shop" title on the single product pages. */
add_filter( 'adoration_woocommerce_entry_header', 'adoration_disable_entry_header_on_single_product_pages' );

/* Adds a heading before the payment method list on the checkout page */
add_action( 'woocommerce_review_order_before_payment', 'adoration_woocommerce_checkout_payment_heading' );

/* Removes the redundant headers from the single product tab panels */
add_filter( 'woocommerce_product_additional_information_heading', '__return_false' );
add_filter( 'woocommerce_product_description_heading', '__return_false' );

/* Adds a woocommerce-thankyou class to the body element of the order received page */
add_filter( 'body_class', 'adoration_thankyou_class' );

/* Adds an extra column to the product grid for wide layouts */
add_filter( 'loop_shop_columns', 'adoration_loop_shop_columns' );

/**
 * Make the store display a maximum of 12 products per page.
 *
 * @since   1.0.0
 * @return  int
 */
function adoration_products_per_page() {
	return 12;
}

/**
 * Change the size of the review gravatars.
 *
 * @since   1.0.0
 * @return  int
 */
function adoration_review_gravatar_size() {
	return 144;
}

/**
 * Change the size of the main product image on the product template.
 *
 * @since   1.0.0
 * @return  string
 */
function adoration_single_product_large_thumbnail_size() {
	return 'full';
}

/**
 * Change the size of the product thumbnails the product template.
 *
 * @since   1.0.0
 * @return  string
 */
function adoration_single_product_small_thumbnail_size() {
	return 'adoration-products-small';
}

/**
 * Changes the JSON returned by the woocommerce_get_refreshed_fragments AJAX call.
 *
 * @since   1.0.0
 * @param   array  $fragments
 * @return  array
 */
function adoration_add_to_cart_fragments( $fragments ) {
	global $woocommerce;

	$fragments['#subsidiary-banner .cart-total .amount']       = $woocommerce->cart->get_cart_total();
	$fragments['#subsidiary-banner .cart .cart-item-quantity'] = '<span class="cart-item-quantity">' . sprintf( esc_html_x( '%d items', 'Number of cart items, e.g. 5 items' ), $woocommerce->cart->get_cart_contents_count() ) . '</span>';

	return $fragments;
}

/**
 * Returns the output of woocommerce_mini_cart().
 *
 * @since   1.0.0
 * @return  string
 */
function adoration_cart_contents() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_cart_contents', false );

	if ( false !== $pre ) {
		return $pre;
	}

	ob_start();
	woocommerce_mini_cart(); // Display mini cart.
	return apply_filters( 'adoration_cart_contents', ob_get_clean() );
}

/**
 * Returns the permalink to the My Account template.
 *
 * @since   1.0.0
 * @return  string
 */
function adoration_my_account() {

	$my_account_permalink = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );

	return apply_filters( 'adoration_my_account_permalink', $my_account_permalink );
}

/**
 * Limits the cross-sells section to 2 product columns on the cart template.
 *
 * @since   1.0.0
 * @return  int
 */
function adoration_cross_sells_columns_cart() {

	if ( ! is_cart() ) {
		return;
	}

	return apply_filters( 'adoration_cross_sells_columns_cart', 2 );
}

/**
 * Limits the cross-sells section to 2 products total on the cart template.
 *
 * @since   1.0.0
 * @return  int
 */
function adoration_cross_sells_total() {
	return apply_filters( 'adoration_cross_sells_total', 2 );
}

if ( ! function_exists( 'woocommerce_upsell_display' ) ) {
	/**
	 * Overrides the WooCommerce pluggable function woocommerce_upsell_display().
	 *
	 * Modifies the number of upsell product columns displayed on the cart template.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function woocommerce_upsell_display( $posts_per_page = '-1', $columns = 2, $orderby = 'rand' ) {

		// Allow developers to short-circuit this function.
		$pre = apply_filters( 'adoration_woocommerce_upsell_display', false );

		if ( false !== $pre ) {
			echo $pre;
			return;
		}

		$columns = 3;
		$posts_per_page = $columns;

		// Show additional columns on wider layouts.
		if ( adoration_is_1c_wide_layout() ) {
			$columns = 4;
			$posts_per_page = $columns;
		}

		$args = array(
			'posts_per_page' => $posts_per_page,
			'orderby'        => apply_filters( 'woocommerce_upsells_orderby', $orderby ),
			'columns'        => $columns
		);

		$args = apply_filters( 'adoration_woocommerce_upsell_display_args', $args );

		wc_get_template( 'single-product/up-sells.php', $args );
	}
}

/**
 * Modifies the number of related product items & columns displayed on the single product template.
 *
 * @since   1.0.0
 * @param   array  $args
 * @return  array
 */
function adoration_output_related_products_args( $args ) {

	$columns = 3;
	$posts   = $columns;

	// Show additional columns on wider layouts.
	if ( adoration_is_1c_wide_layout() ) {
		$columns = 4;
		$posts   = $columns;
	}

	$args['columns']        = $columns;
	$args['posts_per_page'] = $posts;

	return apply_filters( 'adoration_output_related_products_args', $args );
}

if ( ! function_exists( 'woocommerce_subcategory_thumbnail' ) ) {
	/**
	 * Show subcategory thumbnails.
	 *
	 * @since   1.0.0
	 * @param   mixed  $category
	 * @return  void
	 */
	function woocommerce_subcategory_thumbnail( $category ) {

		// Allow developers to short-circuit this function.
		$pre = apply_filters( 'adoration_woocommerce_subcategory_thumbnail', false );

		if ( false !== $pre ) {
			echo $pre;
			return;
		}

		$thumb_size   = apply_filters( 'adoration_subcategory_thumbnail_size', 'adoration-featured-products-thumb' );
		$dimensions   = adoration_get_thumbnail_dimensions( $thumb_size );
		$thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, $thumb_size );
			$image = $image[0];
		} else {
			$args = array(
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => 1,
				'orderby'             => 'date',
				'order'               => 'desc',
				'meta_query'          => array(
					array(
						'key'     => '_visibility',
						'value'   => array('catalog', 'visible'),
						'compare' => 'IN'
					),
					array(
						'key'     => '_thumbnail_id',
						'compare' => 'EXISTS'
					)
				),
				'tax_query' => array(
					array(
						'taxonomy' => 'product_cat',
						'terms'    => $category->term_id,
					),
				),
			);

			$products = new WP_Query( apply_filters( 'adoration_subcategory_thumbnail', $args, $category ) );

			if ( $products->have_posts() ) {
				$product = $products->posts[0];
				$thumbnail_id = get_post_meta( $product->ID, '_thumbnail_id', true );
				$image = wp_get_attachment_image_src( $thumbnail_id, $thumb_size );
				$image = $image[0];
			} else {
				$image = wc_placeholder_img_src();
			}

			wp_reset_postdata();
		}

		if ( ! $image ) {
			return;
		}

		// Prevent esc_url from breaking spaces in urls for image embeds
		// Ref: http://core.trac.wordpress.org/ticket/23605
		$image = str_replace( ' ', '%20', $image );

		echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />';
	}
}

/**
 * Modifies the count html inside of the subcategory listing.
 *
 * @since   1.0.0
 * @param   string  $default
 * @param   object  $category
 * @return  string
 */
function adoration_subcategory_count_html( $default, $category ) {

	$template      = _x( '%s products', 'Number of products in a category e.g. 10 products', 'adoration' );
	$category_html = sprintf( $template, $category->count );

	return '<br /><mark class="count">' . $category_html . '</mark>';
}

/**
 * WooCommerce starting content wrapper - adds the title / description to WC pages.
 *
 * @since  1.0.0
 */
function adoration_woocommerce_before_main_content() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_woocommerce_before_main_content', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( true === apply_filters( 'adoration_woocommerce_entry_header', true ) ) : ?>
	<header class="entry-header">
		<h1 class="entry-title"><?php woocommerce_page_title(); ?></h1>
		<?php adoration_woocommerce_archive_description(); ?>
	</header>
	<?php endif; ?>

	<div class="entry-content"><?php
}

/**
 * WooCommerce ending content wrapper.
 *
 * @since   1.0.0
 * @return  void
 */
function adoration_woocommerce_after_main_content() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_woocommerce_after_main_content', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	} ?>

	</div><?php
}

/**
 * Displays the WooCommerce archive description. Prevents archive description duplication.
 *
 * @since  1.0.0
 */
function adoration_woocommerce_archive_description() {

	// Display the archive description.
	do_action( 'woocommerce_archive_description' );

	// Prevent archive description duplication.
	remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
	remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
}

/**
 * Modifies the default 'shop_catalog' image size.
 *
 * @since   1.0.0
 * @return  array
 */
function adoration_woocommerce_shop_catalog_image_size() {

	$size = array(
		'width'  => '510',
		'height' => '510',
		'crop'   => 1
	);

	return $size;
}

/**
 * Replace WooCommerce default pagination with WordPress built-in pagination
 *
 * @since   1.0.0
 * @return  void
 */
function adoration_woocommerce_pagination() {

	the_posts_pagination(
		array(
			'prev_text'          => __( 'Previous page', 'adoration' ),
			'next_text'          => __( 'Next page', 'adoration' ),
			'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'adoration' ) . ' </span>',
		)
	);
}

/**
 * Removes WooCommerce products from the default search.
 *
 * @since   1.0.0
 * @return  void
 */
function adoration_remove_woocomerce_from_search() {
	global $wp_post_types;

	if ( ! post_type_exists( 'product' ) || ! is_search() ) {
		return;
	}

	$wp_post_types['product']->exclude_from_search = true;
}

/**
 * Adds the rating field back into the review form.
 *
 * @since  1.0.0
 */
function adoration_woocommerce_review_rating_field( $comment_field ) {

	if ( ! is_product() ) {
		return $comment_field;
	}

	if ( ! get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
		return $comment_field;
	}

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_woocommerce_review_rating_field', false );

	if ( false !== $pre ) {
		return $pre . $comment_field;
	}

	ob_start();	?>

	<p class="comment-form-rating">
		<label for="rating"><?php _e( 'Your rating', 'adoration' ); ?></label>
		<select name="rating" id="rating">
			<option value=""><?php _ex( 'Rate&hellip;', 'Review rating.', 'adoration' ); ?></option>
			<option value="5"><?php _ex( 'Perfect', 'Review rating.', 'adoration' ); ?></option>
			<option value="4"><?php _ex( 'Good', 'Review rating.', 'adoration' ); ?></option>
			<option value="3"><?php _ex( 'Average', 'Review rating.', 'adoration' ); ?></option>
			<option value="2"><?php _ex( 'Not that bad', 'Review rating.', 'adoration' ); ?></option>
			<option value="1"><?php _ex( 'Very Poor', 'Review rating.', 'adoration' ); ?></option>
		</select>
	</p><?php

	return ob_get_clean() . $comment_field;
}

/**
 * Changes the "Sale!" text to "Sale".
 *
 * @since  1.0.0
 */
function adoration_sale_flash() {
	return '<span class="onsale">' . __( 'Sale', 'adoration' ) . '</span>';
}

/**
 * Registers and unregisters WC Widgets.
 *
 * @since  1.0.0
 */
function adoration_register_wc_widgets() {

	// The Recent Reviews widget isn't filterable so we have to unregister it and roll our own unfortunately.
	unregister_widget( 'WC_Widget_Recent_Reviews' );

	// Register our own Recent Reviews widget.
	register_widget( 'Adoration_WC_Widget_Recent_Reviews' );
}

/**
 * Removes the "shop" entry header on the single product pages.
 *
 * @since   1.0.0
 * @param   boolean  $show  Whether the entry header is being displayed.
 * @return  boolean
 */
function adoration_disable_entry_header_on_single_product_pages( $show ) {

	if ( false === is_product() ) {
		return $show;
	}

	return false;
}

/**
 * Adds a heading before the payment method list on the checkout page.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_woocommerce_checkout_payment_heading() {

	/*
	 * Do not execute when performing AJAX requests.
	 * Causes duplicate output issues, see: https://github.com/woothemes/woocommerce/issues/7226
	 */
	if ( is_ajax() ) {
		return;
	}

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_woocommerce_checkout_payment_heading', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	} ?>

	<h3 class="payment-methods-heading"><?php _e( 'Payment Methods', 'adoration' ); ?></h3><?php
}

/**
 * Adds a woocommerce-thankyou class to the body element of the order received page.
 *
 * @since   1.0.0
 * @param   array  $classes  CSS classes applied to the body element.
 * @return  array            CSS classes applied to the body element.
 */
function adoration_thankyou_class( $classes ) {
	global $wp;

	if ( ! isset( $wp->query_vars['order-received'] ) ) {
		return $classes;
	}

	$classes[] = 'woocommerce-thankyou';

	return $classes;
}

/**
 * Adds an extra column to the product grid for wide layouts.
 *
 * @since   1.0.0
 * @param   int    Shop columns
 * @return  int    Shop columns
 */
function adoration_loop_shop_columns( $columns ) {

	if ( adoration_is_1c_wide_layout() ) {
		return 4;
	}

	return $columns;
}
