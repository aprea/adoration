<?php
/**
 * subsidiary banner actions.
 *
 * Responsible for displaying the subsidiary banner elements, i.e. subsidiary menu, my account menu, cart, social media icons.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/* Display the subsidiary banner wrap opening tags. */
add_action( 'adoration_subsidiary_banner', 'adoration_subsidiary_wrap_open',            10 );

/* Display either the My Account link or the login/register links. */
add_action( 'adoration_subsidiary_banner', 'adoration_subsidiary_banner_account',       20 );

/* Display the subsidiary banner cart. */
add_action( 'adoration_subsidiary_banner', 'adoration_subsidiary_banner_cart',          30 );

/* Display the subsidiary banner wrap middle tags. */
add_action( 'adoration_subsidiary_banner', 'adoration_subsidiary_wrap_middle',          40 );

/* Display the subsidiary banner social media menu. */
add_action( 'adoration_subsidiary_banner', 'adoration_subsidiary_banner_social_media',  50 );

/* Display the subsidiary banner wrap ending tags. */
add_action( 'adoration_subsidiary_banner', 'adoration_subsidiary_wrap_close',           50 );

/**
 * Display the subsidiary banner wrap opening tags.
 *
 * @since  1.0.0
 */
function adoration_subsidiary_wrap_open() {	?>
	<div class="subsidiary-left-wrap"><?php
}

/**
 * Display either the My Account / Logout links or the Login / Register links.
 *
 * @since  1.0.0
 */
function adoration_subsidiary_banner_account() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_subsidiary_banner_account', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( is_user_logged_in() ) {
		adoration_subsidiary_banner_account_logged_in();
	} else {
		adoration_subsidiary_banner_account_logged_out();
	}
}

/**
 * Display the My Account / Logout links.
 *
 * @since  1.0.0
 */
function adoration_subsidiary_banner_account_logged_in() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_subsidiary_banner_account_logged_in', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	} ?>
	<div class="account">
		<?php if ( apply_filters( 'adoration_show_subsidiary_banner_my_account', true ) ) : ?>
		<div class="my-account-section">
			<a href="<?php echo adoration_my_account(); ?>"></i><?php _ex( 'My Account', 'The customers account overview page.', 'adoration' ) ?></a>
		</div>
		<?php endif; ?>

		<?php if ( apply_filters( 'adoration_show_masthead_logout', false ) ) : ?>
		<div class="my-account-section">
			<a href="<?php echo wp_logout_url( adoration_my_account() ); ?>"><?php _ex( 'Logout', 'Logout of your account.', 'adoration' ) ?></a>
		</div>
		<?php endif; ?>
	</div><?php
}

/**
 * Display the masthead login/register links.
 *
 * @since  1.0.0
 */
function adoration_subsidiary_banner_account_logged_out() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_subsidiary_banner_account_logged_out', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	$users_can_register             = get_option( 'users_can_register' );
	$enable_myaccount_registrations = get_option( 'woocommerce_enable_myaccount_registration' );
	$users_can_register             = ( $users_can_register && 'yes' === $enable_myaccount_registrations && apply_filters( 'adoration_subsidiary_banner_registration', true ) );

	$link_text = _x( 'Login', 'Log into your account.', 'adoration' );
	$link_text = ( $users_can_register ) ? _x( 'Login / Register', 'Log into your account or register for one.', 'adoration' ) : $link_text; ?>

	<div class="account">
		<?php if ( apply_filters( 'adoration_show_subsidiary_banner_login', true ) ) : ?>
		<div class="my-account-section">
			<a href="<?php echo adoration_my_account(); ?>"><?php echo $link_text; ?></a>
		</div>
		<?php endif; ?>
	</div><?php
}

/**
 * Display the subsidiary banner cart.
 *
 * @since  1.0.0
 */
function adoration_subsidiary_banner_cart() {
	global $woocommerce;

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_subsidiary banner_cart', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( ! is_woocommerce_activated() ) {
		return;
	} ?>

	<div class="cart">
		<a href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>">
			<span class="cart-total"><?php echo $woocommerce->cart->get_cart_total(); ?></span>
			<span class="cart-item-quantity"><?php printf( esc_html_x( '%d items', 'Number of cart items, e.g. 5 items' ), $woocommerce->cart->get_cart_contents_count() ); ?></span>
		</a>
		<div class="widget_shopping_cart_content"><?php echo adoration_cart_contents(); ?></div>
	</div><?php
}

/**
 * Display the subsidiary banner wrap middle tags.
 *
 * @since  1.0.0
 */
function adoration_subsidiary_wrap_middle() { ?>
	</div><!-- .subsidiary-left-wrap -->
	<div class="subsidiary-right-wrap"><?php
}

/**
 * Display the subsidiary banner social media menu.
 *
 * @since  1.0.0
 */
function adoration_subsidiary_banner_social_media() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_subsidiary_banner_social_media', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	ob_start(); ?>

	<label for="social-toggle" class="social-toggle-button"><span></span></label>
	<input type="checkbox" id="social-toggle" autocomplete="off"><?php

	$menu_toggle = ob_get_clean();

	$items_wrap = '<nav ' . hybrid_get_attr( 'menu', 'social' ) . '><ul>%3$s</ul></nav>';

	$args = array(
		'theme_location'  => 'social',
		'container'       => '',
		'fallback_cb'     => '',
		'depth'           => 1,
		'items_wrap'      => $menu_toggle . $items_wrap
	);

	wp_nav_menu( apply_filters( 'adoration_subsidiary_banner_social_media_menu_args', $args ) );
}

/**
 * Display the subsidiary banner wrap ending tags.
 *
 * @since  1.0.0
 */
function adoration_subsidiary_wrap_close() { ?>
	</div><!-- .subsidiary-left-wrap --><?php
}
