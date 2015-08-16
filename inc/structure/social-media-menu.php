<?php
/**
 * Sets up custom filters and actions for the social menu.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/* Adds rel="nofollow" to links in the social menu. */
add_filter( 'nav_menu_link_attributes', 'adoration_nofollow_social_menu_items', 10, 3 );

/* Adds a link to the site's main RSS feed to the social menu. */
add_filter( 'wp_nav_menu_items', 'adoration_add_rss_to_social_menu', 10, 2 );

/**
 * Adds rel="nofollow" to links in the social menu.
 *
 * @since   1.0.0
 * @param   array   $atts  The HTML attributes applied to the menu item's <a>, empty strings are ignored.
 * @param   object  $item  The current menu item.
 * @param   array   $args  An array of wp_nav_menu() arguments.
 * @return  array
 */
function adoration_nofollow_social_menu_items( $atts, $item, $args ) {

	if ( 'social' !== $args->theme_location ) {
		return $atts;
	}

	$atts['rel'] = ( ! empty( $atts['rel'] ) ) ? $atts['rel'] . ' nofollow' : 'nofollow';

	return $atts;
}

/**
 * Adds a link to the site's main RSS feed to the social menu.
 *
 * @since   1.0.0
 * @param   string  $items  The HTML list content for the menu items.
 * @param   object  $args   An object containing wp_nav_menu() arguments.
 * @return  string
 */
function adoration_add_rss_to_social_menu( $items, $args ) {

	if ( 'social' !== $args->theme_location ) {
		return $items;
	}

	$link  = apply_filters( 'adoration_feed_url',   get_bloginfo_rss( 'rss2_url' ) );
	$text  = apply_filters( 'adoration_feed_text',  __( 'Subscribe to our Feed', 'adoration' ) );
	$title = apply_filters( 'adoration_feed_title', __( 'Subscribe to our Feed', 'adoration' ) );

	ob_start(); ?>

	<li class="menu-item">
		<a href="<?php echo esc_url( $link ); ?>" title="<?php echo esc_attr( $title ); ?>" class="feed">
			<?php echo esc_html( $text ); ?>
		</a>
	</li><?php

	$feed_menu_item = ob_get_clean();

	return $items . $feed_menu_item;
}
