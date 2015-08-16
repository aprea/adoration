<?php
/**
 * "To have faith is to trust yourself to the water. When you swim you don't grab hold of the water,
 * because if you do you will sink and drown. Instead you relax, and float." ~ Alan Watts
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package    Adoration
 * @subpackage Functions
 * @version    1.0.0
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

$template_directory = trailingslashit( get_template_directory() );

/* Load function files */
require_once $template_directory . 'inc/functions.php';

/* Load additional files */
require_once $template_directory . 'inc/abstracts/abstract-adoration-widget.php';
require_once $template_directory . 'inc/structure/comments.php';
require_once $template_directory . 'inc/structure/hooks.php';
require_once $template_directory . 'inc/structure/masthead.php';
require_once $template_directory . 'inc/structure/social-media-menu.php';
require_once $template_directory . 'inc/structure/subsidiary-banner.php';
require_once $template_directory . 'inc/structure/template-tags.php';
require_once $template_directory . 'inc/structure/wc-template-tags.php';
require_once $template_directory . 'inc/adoration.php';
require_once $template_directory . 'inc/class-adoration-wc-widget-recent-reviews.php';
require_once $template_directory . 'inc/class-adoration-widget-recent-posts.php';
require_once $template_directory . 'inc/class-adoration-widget-recent-comments.php';
require_once $template_directory . 'inc/customizer.php';
require_once $template_directory . 'inc/woocommerce.php';

/* Load the Hybrid Core framework and launch it. */
require_once $template_directory . 'library/hybrid.php';
new Hybrid();

/* Set up the theme early. */
add_action( 'after_setup_theme', 'adoration_setup', 5 );

/**
 * The theme setup function. This function sets up support for various WordPress and framework functionality.
 *
 * @since  1.0.0
 */
function adoration_setup() {

	/* Theme layouts. */
	add_theme_support(
		'theme-layouts',
		array(
			'1c'        => __( '1 Column Wide',                'adoration' ),
			'1c-narrow' => __( '1 Column Narrow',              'adoration' ),
			'2c-l'      => __( '2 Columns: Content / Sidebar', 'adoration' ),
			'2c-r'      => __( '2 Columns: Sidebar / Content', 'adoration' ),
		),
		array( 'default' => is_rtl() ? '2c-r' :'2c-l' )
	);

	/* Load stylesheets. */
	add_theme_support(
		'hybrid-core-styles',
		array( 'adoration-fonts', 'adoration-font-awesome', 'parent', 'style' )
	);

	/* Enable custom template hierarchy. */
	add_theme_support( 'hybrid-core-template-hierarchy' );

	/* The best thumbnail/image script ever. */
	add_theme_support( 'get-the-image' );

	/* Breadcrumbs. Yay! */
	add_theme_support( 'breadcrumb-trail' );

	/* Pagination. */
	add_theme_support( 'loop-pagination' );

	/* Better captions for themes to style. */
	add_theme_support( 'cleaner-caption' );

	/* Automatically add feed links to <head>. */
	add_theme_support( 'automatic-feed-links' );

	/* Whistles plugin. */
	add_theme_support( 'whistles', array( 'styles' => true ) );

	/* Declare support for WooCommerce */
	add_theme_support( 'woocommerce' );

	/* Let WordPress handle the rendering of the <title> tag */
	add_theme_support( 'title-tag' );

	/* Prevent hybrid core from outputting the <title> tag. */
	remove_action( 'wp_head', 'hybrid_doctitle', 0 );

	/* Declare support for the custom background feature */
	$color_scheme  = adoration_get_color_scheme();
	$default_color = trim( $color_scheme[0], '#' );

	add_theme_support( 'custom-background', apply_filters( 'adoration_custom_background_args', array(
		'default-color'      => $default_color,
		'default-attachment' => 'fixed',
	) ) );

	/* Editor styles. */
	add_editor_style( adoration_get_editor_styles() );

	/* Handle content width for embeds and images. */
	// Note: this is the largest size based on the theme's various layouts.
	hybrid_set_content_width( 1136 );
}
