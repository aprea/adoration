<?php
/**
 * General purpose helper functions.
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
 * Returns this theme's version as a string.
 *
 * @since   1.0.0
 * @return  string
 */
function adoration_get_version() {
	static $version = null;

	if ( null === $version ) {
		$theme   = wp_get_theme();
		$version = $theme->exists() ? $theme->get( 'Version' ) : '1.0.0';
	}

	return apply_filters( 'adoration_version', $version );
}

/**
 * Determines wether or not the current page is using a 1 column layout.
 *
 * @since  1.0.0
 */
function adoration_is_1c_layout() {
	return ( in_array( get_theme_mod( 'theme_layout' ), array( '1c', '1c-narrow' ) ) );
}

/**
 * Determines wether or not the current page is using a 1 column wide layout.
 *
 * @since  1.0.0
 */
function adoration_is_1c_wide_layout() {
	return ( '1c' === get_theme_mod( 'theme_layout' ) );
}

/**
 * Determines wether or not the current page is using a 1 column narrow layout.
 *
 * @since  1.0.0
 */
function adoration_is_1c_narrow_layout() {
	return ( '1c-narrow' === get_theme_mod( 'theme_layout' ) );
}


if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	/**
	 * Query WooCommerce activation.
	 *
	 * @since   1.0.0
	 * @return  boolean
	 */
	function is_woocommerce_activated() {

		if ( class_exists( 'woocommerce' ) ) {
			return true;
		}

		return false;
	}
}

/**
 * Returns an array of image dimensions for a given image size string.
 *
 * @since   1.0.0
 * @param   string  $thumb_size
 * @return  array
 */
function adoration_get_thumbnail_dimensions( $thumb_size ) {

	$image_sizes = adoration_get_all_image_sizes();

	if ( isset( $image_sizes[ $thumb_size ] ) ) {
		$dimensions = $image_sizes[ $thumb_size ];
	} else {
		$dimensions = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1
		);
	}

	return apply_filters( 'adoration_thumbnail_dimensions', $dimensions );
}

/**
 * Get all the registered image sizes along with their dimensions
 *
 * @since   1.0.0
 * @return  array  $image_sizes  The image sizes
 */
function adoration_get_all_image_sizes() {
	global $_wp_additional_image_sizes;

	$default_image_sizes = array( 'thumbnail', 'medium', 'large' );

	$image_sizes = array();

	foreach ( $default_image_sizes as $size ) {
		$image_sizes[$size]['width']  = intval( get_option( "{$size}_size_w") );
		$image_sizes[$size]['height'] = intval( get_option( "{$size}_size_h") );
		$image_sizes[$size]['crop']   = get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
	}

	if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
		$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
	}

	return $image_sizes;
}

/**
 * Convert HEX to RGB.
 *
 * @since   1.0.0
 * @param   string  $color  The original color, in 3- or 6-digit hexadecimal form.
 * @return  array   Array   Array containing RGB (red, green, and blue) values for the given HEX code, empty array otherwise.
 *
 */
function adoration_hex2rgb( $color ) {

	$color = trim( $color, '#' );

	if ( strlen( $color ) == 3 ) {
		$r = hexdec( substr( $color, 0, 1 ).substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ).substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ).substr( $color, 2, 1 ) );
	} else if ( strlen( $color ) == 6 ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
	} else {
		return array();
	}

	return array( 'red' => $r, 'green' => $g, 'blue' => $b );
}

/**
 * Callback function for adding editor styles.  Use along with the add_editor_style() function.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function adoration_get_editor_styles() {

	/* Set up an array for the styles. */
	$editor_styles = array();

	/* Add the theme's editor styles. */
	$editor_styles[] = trailingslashit( get_template_directory_uri() ) . 'css/editor-style.css';

	/* If a child theme, add its editor styles. Note: WP checks whether the file exists before using it. */
	if ( is_child_theme() && file_exists( trailingslashit( get_stylesheet_directory() ) . 'css/editor-style.css' ) ) {
		$editor_styles[] = trailingslashit( get_stylesheet_directory_uri() ) . 'css/editor-style.css';
	}

	/* Add the locale stylesheet. */
	$editor_styles[] = get_locale_stylesheet_uri();

	/* Uses Ajax to display custom theme styles added via the Theme Mods API. */
	$editor_styles[] = add_query_arg( 'action', 'adoration_editor_styles', admin_url( 'admin-ajax.php' ) );

	/* Return the styles. */
	return $editor_styles;
}
