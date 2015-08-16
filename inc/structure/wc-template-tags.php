<?php
/**
 * Defines WooCommerce specific template tags. Replaces functionality found in several WooCommerce shortcodes.
 *
 * The output can generally be filtered using the adoration_pre_* hooks.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * List products in a category.
 *
 * @since   1.0.0
 * @param   array        $args
 * @return  bool|string
 */
function adoration_product_category( $args ) {
	global $woocommerce_loop;

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_adoration_product_category', false, $args );

	if ( false !== $pre ) {
		return $pre;
	}

	$args = wp_parse_args(
		$args,
		array(
			'per_page' => '12',
			'columns'  => '4',
			'orderby'  => 'title',
			'order'    => 'desc',
			'category' => '',  // Slugs
			'operator' => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
		)
	);

	$args = apply_filters( 'adoration_product_category_args', $args );

	extract( $args );

	if ( ! $category ) {
		return false;
	}

	// Default ordering args
	$ordering_args = WC()->query->get_catalog_ordering_args( $orderby, $order );

	$q_args = array(
		'post_type'				=> 'product',
		'post_status' 			=> 'publish',
		'ignore_sticky_posts'	=> 1,
		'orderby' 				=> $ordering_args['orderby'],
		'order' 				=> $ordering_args['order'],
		'posts_per_page' 		=> $per_page,
		'meta_query' 			=> array(
			array(
				'key' 			=> '_visibility',
				'value' 		=> array('catalog', 'visible'),
				'compare' 		=> 'IN'
			)
		),
		'tax_query' 			=> array(
			array(
				'taxonomy' 		=> 'product_cat',
				'terms' 		=> array_map( 'sanitize_title', explode( ',', $category ) ),
				'field' 		=> 'slug',
				'operator' 		=> $operator
			)
		)
	);

	if ( isset( $ordering_args['meta_key'] ) ) {
		$q_args['meta_key'] = $ordering_args['meta_key'];
	}

	$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $q_args, $args ) );

	if ( ! $products->have_posts() ) {
		woocommerce_reset_loop();
		wp_reset_postdata();
		return false;
	}

	$woocommerce_loop['columns'] = $columns;

	ob_start(); ?>

	<?php woocommerce_product_loop_start(); ?>

		<?php while ( $products->have_posts() ) : $products->the_post(); ?>

			<?php wc_get_template_part( 'content', 'product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php woocommerce_product_loop_end();

	woocommerce_reset_loop();
	wp_reset_postdata();

	$return = ob_get_clean();

	// Remove ordering query arguments
	WC()->query->remove_ordering_args();

	return $return;
}

/**
 * List all (or limited) product categories.
 *
 * @since   1.0.0
 * @param   array        $args
 * @return  bool|string
 */
function adoration_product_categories( $args ) {
	global $woocommerce_loop;

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_adoration_product_categories', false, $args );

	if ( false !== $pre ) {
		return $pre;
	}

	$args = wp_parse_args(
		$args,
		array(
			'number'     => null,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'columns' 	 => '4',
			'hide_empty' => 1,
			'parent'     => ''
		)
	);

	$args = apply_filters( 'adoration_product_categories_args', $args );

	extract( $args );

	if ( isset( $args[ 'ids' ] ) ) {
		$ids = explode( ',', $args[ 'ids' ] );
		$ids = array_map( 'trim', $ids );
	} else {
		$ids = array();
	}

	$hide_empty = ( $hide_empty == true || $hide_empty == 1 ) ? 1 : 0;

	// get terms and workaround WP bug with parents/pad counts
	$args = array(
		'orderby'    => $orderby,
		'order'      => $order,
		'hide_empty' => $hide_empty,
		'include'    => $ids,
		'pad_counts' => true,
		'child_of'   => $parent
	);

	$product_categories = get_terms( 'product_cat', $args );

	if ( '' !== $parent ) {
		$product_categories = wp_list_filter( $product_categories, array( 'parent' => $parent ) );
	}

	if ( $hide_empty ) {
		foreach ( $product_categories as $key => $category ) {
			if ( $category->count == 0 ) {
				unset( $product_categories[ $key ] );
			}
		}
	}

	if ( $number ) {
		$product_categories = array_slice( $product_categories, 0, $number );
	}

	if ( empty( $product_categories ) ) {
		return false;
	}

	$woocommerce_loop['columns'] = $columns;

	ob_start();

	// Reset loop/columns globals when starting a new loop
	$woocommerce_loop['loop'] = $woocommerce_loop['column'] = '';

	woocommerce_product_loop_start();

	foreach ( $product_categories as $category ) {
		wc_get_template( 'content-product_cat.php', array( 'category' => $category ) );
	}

	woocommerce_product_loop_end();
	woocommerce_reset_loop();

	return ob_get_clean();
}

/**
 * Display recent products.
 *
 * @since   1.0.0
 * @param   array        $args
 * @return  bool|string
 */
function adoration_recent_products( $args ) {
	global $woocommerce_loop;

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_adoration_recent_products', false, $args );

	if ( false !== $pre ) {
		return $pre;
	}

	$args = wp_parse_args(
		$args,
		array(
			'per_page' 	=> '12',
			'columns' 	=> '4',
			'orderby' 	=> 'date',
			'order' 	=> 'desc'
		)
	);

	$args = apply_filters( 'adoration_recent_products_args', $args );

	extract( $args );

	$meta_query = WC()->query->get_meta_query();

	$q_args = array(
		'post_type'				=> 'product',
		'post_status'			=> 'publish',
		'ignore_sticky_posts'	=> 1,
		'posts_per_page' 		=> $per_page,
		'orderby' 				=> $orderby,
		'order' 				=> $order,
		'meta_query' 			=> $meta_query
	);

	$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $q_args, $args ) );

	if ( ! $products->have_posts() ) {
		wp_reset_postdata();
		return false;
	}

	$woocommerce_loop['columns'] = $columns;

	ob_start(); ?>

	<?php woocommerce_product_loop_start(); ?>

		<?php while ( $products->have_posts() ) : $products->the_post(); ?>

			<?php wc_get_template_part( 'content', 'product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php woocommerce_product_loop_end();

	wp_reset_postdata();

	return ob_get_clean();
}

/**
 * List all products on sale.
 *
 * @since   1.0.0
 * @param   array        $args
 * @return  bool|string
 */
function adoration_sale_products( $args ) {
	global $woocommerce_loop;

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_adoration_sale_products', false, $args );

	if ( false !== $pre ) {
		return $pre;
	}

	$args = wp_parse_args(
		$args,
		array(
			'per_page' => '12',
			'columns'  => '4',
			'orderby'  => 'title',
			'order'    => 'asc'
		)
	);

	$args = apply_filters( 'adoration_sale_products_args', $args );

	extract( $args );

	// Get products on sale
	$product_ids_on_sale = wc_get_product_ids_on_sale();

	$meta_query   = array();
	$meta_query[] = WC()->query->visibility_meta_query();
	$meta_query[] = WC()->query->stock_status_meta_query();
	$meta_query   = array_filter( $meta_query );

	$q_args = array(
		'posts_per_page'	=> $per_page,
		'orderby' 			=> $orderby,
		'order' 			=> $order,
		'no_found_rows' 	=> 1,
		'post_status' 		=> 'publish',
		'post_type' 		=> 'product',
		'meta_query' 		=> $meta_query,
		'post__in'			=> array_merge( array( 0 ), $product_ids_on_sale )
	);

	$products = new WP_Query( apply_filters( 'adoration_shortcode_products_query', $q_args, $args ) );

	if ( ! $products->have_posts() ) {
		wp_reset_postdata();
		return false;
	}

	$woocommerce_loop['columns'] = $columns;

	ob_start(); ?>

	<?php woocommerce_product_loop_start(); ?>

		<?php while ( $products->have_posts() ) : $products->the_post(); ?>

			<?php wc_get_template_part( 'content', 'product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php woocommerce_product_loop_end();

	wp_reset_postdata();

	return ob_get_clean();
}

/**
 * List best selling products on sale.
 *
 * @since   1.0.0
 * @param   array        $args
 * @return  bool|string
 */
function adoration_best_selling_products( $args ) {
	global $woocommerce_loop;

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_adoration_best_selling_products', false, $args );

	if ( false !== $pre ) {
		return $pre;
	}

	$args = wp_parse_args(
		$args,
		array(
			'per_page' => '12',
			'columns'  => '4'
		)
	);

	$args = apply_filters( 'adoration_best_selling_products_args', $args );

	extract( $args );

	$q_args = array(
		'post_type' 			=> 'product',
		'post_status' 			=> 'publish',
		'ignore_sticky_posts'   => 1,
		'posts_per_page'		=> $per_page,
		'meta_key' 		 		=> 'total_sales',
		'orderby' 		 		=> 'meta_value_num',
		'meta_query' 			=> array(
			array(
				'key' 		=> '_visibility',
				'value' 	=> array( 'catalog', 'visible' ),
				'compare' 	=> 'IN'
			)
		)
	);

	$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $q_args, $args ) );

	if ( ! $products->have_posts() ) {
		wp_reset_postdata();
		return false;
	}

	$woocommerce_loop['columns'] = $columns;

	ob_start(); ?>

	<?php woocommerce_product_loop_start(); ?>

		<?php while ( $products->have_posts() ) : $products->the_post(); ?>

			<?php wc_get_template_part( 'content', 'product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php woocommerce_product_loop_end();

	wp_reset_postdata();

	return ob_get_clean();
}

/**
 * List top rated products on sale.
 *
 * @since   1.0.0
 * @param   array        $atts
 * @return  bool|string
 */
function adoration_top_rated_products( $args ) {
	global $woocommerce_loop;

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_top_rated_products', false, $args );

	if ( false !== $pre ) {
		return $pre;
	}

	$args = wp_parse_args(
		$args,
		array(
			'per_page' => '12',
			'columns'  => '4',
			'orderby'  => 'title',
			'order'    => 'asc'
		)
	);

	$args = apply_filters( 'adoration_top_rated_products_args', $args );

	extract( $args );

	$q_args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'orderby'             => $orderby,
		'order'               => $order,
		'posts_per_page'      => $per_page,
		'meta_query'          => array(
			array(
				'key'         => '_visibility',
				'value'       => array('catalog', 'visible'),
				'compare'     => 'IN'
			)
		)
	);

	add_filter( 'posts_clauses', 'adoration_order_by_rating_post_clauses' );

	$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $q_args, $args ) );

	remove_filter( 'posts_clauses', 'adoration_order_by_rating_post_clauses' );

	if ( ! $products->have_posts() ) {
		wp_reset_postdata();
		return false;
	}

	$woocommerce_loop['columns'] = $columns;

	ob_start(); ?>

	<?php woocommerce_product_loop_start(); ?>

		<?php while ( $products->have_posts() ) : $products->the_post(); ?>

			<?php wc_get_template_part( 'content', 'product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php woocommerce_product_loop_end();

	wp_reset_postdata();

	return ob_get_clean();
}

/**
 * Output featured products.
 *
 * @since   1.0.0
 * @param   array        $atts
 * @return  bool|string
 */
function adoration_featured_products( $args ) {
	global $woocommerce_loop;

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_featured_products', false, $args );

	if ( false !== $pre ) {
		return $pre;
	}

	$args = wp_parse_args(
		$args,
		array(
				'per_page' => '12',
				'columns'  => '4',
				'orderby'  => 'date',
				'order'    => 'desc'
		)
	);

	$args = apply_filters( 'adoration_featured_products_args', $args );

	extract( $args );

	$q_args = array(
		'post_type'				=> 'product',
		'post_status' 			=> 'publish',
		'ignore_sticky_posts'	=> 1,
		'posts_per_page' 		=> $per_page,
		'orderby' 				=> $orderby,
		'order' 				=> $order,
		'meta_query'			=> array(
			array(
				'key' 		=> '_visibility',
				'value' 	=> array('catalog', 'visible'),
				'compare'	=> 'IN'
			),
			array(
				'key' 		=> '_featured',
				'value' 	=> 'yes'
			)
		)
	);

	$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $q_args, $args ) );

	if ( ! $products->have_posts() ) {
		wp_reset_postdata();
		return false;
	}

	$woocommerce_loop['columns'] = $columns;

	ob_start(); ?>

	<?php woocommerce_product_loop_start(); ?>

		<?php while ( $products->have_posts() ) : $products->the_post(); ?>

			<?php wc_get_template_part( 'content', 'product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php woocommerce_product_loop_end();

	wp_reset_postdata();

	return ob_get_clean();
}

/**
 * Modifies the given SQL queries to order the products by highest -> lowest DESC rating.
 *
 * @since   1.0.0
 * @param   array  $args
 * @return  array
 */
function adoration_order_by_rating_post_clauses( $args ) {
	global $wpdb;

	$args['where'] .= " AND $wpdb->commentmeta.meta_key = 'rating' ";

	$args['join'] .= "
		LEFT JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
		LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
	";

	$args['orderby'] = "$wpdb->commentmeta.meta_value DESC";

	$args['groupby'] = "$wpdb->posts.ID";

	return $args;
}
