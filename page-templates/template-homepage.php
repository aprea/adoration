<?php
/**
 * The template for displaying the homepage.
 *
 * This page template will display any functions hooked into the `homepage` action.
 * By default this includes a variety of product displays and the page content itself. To change the order or toggle these components
 * use the Homepage Control plugin.
 * https://wordpress.org/plugins/homepage-control/
 *
 * Template name: Homepage
 *
 * @package adoration
 */

get_header();

/**
 * @hooked adoration_homepage_heading   - 10
 */
do_action( 'adoration_before_homepage_content' );

/**
 * @hooked adoration_page_content       - 10
 * @hooked adoration_product_categories - 20
 * @hooked adoration_recent_products    - 30
 * @hooked adoration_featured_products  - 40
 * @hooked adoration_popular_products   - 50
 * @hooked adoration_on_sale_products   - 60
 */
do_action( 'homepage' );

do_action( 'adoration_after_homepage_content' );

get_footer();
