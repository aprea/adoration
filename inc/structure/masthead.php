<?php
/**
 * Masthead actions. Responsible for displaying the various masthead elements, e.g. logo, navigation, search form etc.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/* Display the masthead logo text.*/
add_action( 'adoration_masthead', 'adoration_masthead_logo',          50 );

/* Display the masthead tagline. */
add_action( 'adoration_masthead', 'adoration_masthead_tagline',       60 );

/* Display the masthead main navigation. */
add_action( 'adoration_masthead', 'adoration_masthead_navigation',    70 );

/* Display the masthead search form. */
add_action( 'adoration_masthead', 'adoration_masthead_search',        80 );

/**
 * Display the masthead logo text.
 *
 * @since  1.0.0
 */
function adoration_masthead_logo() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_masthead_logo', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	} ?>

	<div class="site-branding">
		<?php if ( is_front_page() ) : ?>
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
		<?php else : ?>
			<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
		<?php endif; ?>
	</div><?php
}

/**
 * Display the masthead tagline.
 *
 * @since  1.0.0
 */
function adoration_masthead_tagline() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_masthead_tagline', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	$tagline = get_bloginfo( 'description', 'display' );
	$tagline = apply_filters( 'adoration_tagline', $tagline );

	if ( empty( $tagline ) ) {
		return;
	} ?>

	<div class="site-tagline"><?php echo esc_html( $tagline ); ?></div><?php
}

/**
 * Display the masthead main navigation.
 *
 * @since  1.0.0
 */
function adoration_masthead_navigation() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_masthead_navigation', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( ! has_nav_menu( 'primary' ) ) {
		return;
	}

	ob_start(); ?>

	<label for="menu-toggle" class="menu-toggle-button"><span></span></label>
	<input type="checkbox" id="menu-toggle" autocomplete="off"><?php

	$menu_toggle = ob_get_clean();

	$items_wrap = '<nav ' . hybrid_get_attr( 'menu', 'primary' ) . '><ul>%3$s</ul></nav>';

	$args = array(
		'theme_location' => 'primary',
		'container'      => '',
		'fallback_cb'    => '',
		'items_wrap'     => $menu_toggle . $items_wrap
	);

	wp_nav_menu( apply_filters( 'adoration_masthead_navigation_menu_args', $args ) );
}

/**
 * Display the masthead search form.
 *
 * @since  1.0.0
 */
function adoration_masthead_search() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_masthead_search', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	ob_start(); ?>

	<div class="search-container masthead-search-wrap">
		<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="screen-reader-text"><?php echo _x( 'Search for:', 'label', 'adoration' ); ?></span>
			<input id="masthead-search" type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search&hellip;', 'placeholder', 'adoration' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'adoration' ); ?>" />
			<label for="masthead-search"></label>
			<input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'adoration' ); ?>" />
		</form>
	</div><?php

	echo ob_get_clean();
}
