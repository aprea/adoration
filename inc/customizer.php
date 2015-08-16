<?php
/**
 * Handles the theme's theme customizer functionality.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/* Theme Customizer setup. */
add_action( 'customize_register', 'adoration_customize_register', 11 );

/* Binds JS listener to make Customizer color_scheme control. */
add_action( 'customize_controls_enqueue_scripts', 'adoration_customize_control_js' );

/* Load customizer JavaScript files. */
add_action( 'customize_preview_init', 'adoration_enqueue_customizer_scripts' );

/* Output an Underscore template for generating CSS for the color scheme. */
add_action( 'customize_controls_print_footer_scripts', 'adoration_color_scheme_css_template' );

/**
 * Sets up the theme customizer sections, controls, and settings.
 *
 * @since  1.0.0
 * @param  object  $wp_customize
 */
function adoration_customize_register( $wp_customize ) {

	$color_scheme = adoration_get_color_scheme();

	/* Enable live preview for WordPress theme features. */
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	// Add color scheme setting and control.
	$wp_customize->add_setting( 'color_scheme', array(
		'default'           => 'default',
		'sanitize_callback' => 'adoration_sanitize_color_scheme',
		'transport'         => 'postMessage',
	) );

	// Add custom link and button color setting and control.
	$wp_customize->add_setting( 'link_button_color', array(
		'default'           => $color_scheme[1],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_button_color', array(
		'label'       => __( 'Link and Button Color', 'adoration' ),
		'description' => __( 'Applied to text links and buttons throughout the site.', 'adoration' ),
		'section'     => 'colors',
	) ) );

	// Remove the core header textcolor control, as it shares the sidebar text color.
	$wp_customize->remove_control( 'header_textcolor' );
}

/**
 * Loads theme customizer JavaScript.
 *
 * @since  1.0.0
 */
function adoration_enqueue_customizer_scripts() {

	$suffix    = hybrid_get_min_suffix();
	$theme_uri = trailingslashit( get_template_directory_uri() );
	$version   = adoration_get_version();

	wp_enqueue_script( 'adoration-customize', $theme_uri . "js/customize{$suffix}.js", array( 'jquery' ), $version, true );
}

/**
 * Binds JS listener to make Customizer color_scheme control.
 *
 * Passes color scheme data as colorScheme global.
 *
 * @since  1.0.0
 */
function adoration_customize_control_js() {

	$suffix    = hybrid_get_min_suffix();
	$theme_uri = trailingslashit( get_template_directory_uri() );
	$version   = adoration_get_version();

	wp_enqueue_script(  'color-scheme-control', $theme_uri . "/js/color-scheme-control{$suffix}.js", array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), $version, true );
	wp_localize_script( 'color-scheme-control', 'colorScheme', adoration_get_color_schemes() );
}

/**
 * Register color schemes. Can be filtered with adoration_color_schemes.
 *
 * The order of colors in a colors array:
 * 1. Main Background Color.
 * 2. Main Link / Button Color.
 *
 * @since   1.0.0
 * @return  array  An associative array of color scheme options.
 */
function adoration_get_color_schemes() {
	return apply_filters( 'adoration_color_schemes', array(
		'default' => array(
			'label'  => esc_html_x( 'Default', 'Color scheme label.', 'adoration' ),
			'colors' => array(
				'#fdfdfd',
				'#3498db',
			),
		),
	) );
}

/**
 * Returns an array of the default color scheme hex values.
 *
 * @since   1.0.0
 * @return  array
 */
function adoration_get_color_scheme() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_get_color_scheme', false );

	if ( false !== $pre ) {
		return $pre;

	}

	$color_schemes = adoration_get_color_schemes();

	return $color_schemes['default']['colors'];
}

/**
 * Returns CSS for the color schemes.
 *
 * @since   1.0.0
 * @param   array   $colors  Color scheme colors.
 * @return  string           Color scheme CSS.
 */
function adoration_get_color_scheme_css( $colors ) {

	$colors = wp_parse_args( $colors, array(
		'background_color'  => '',
		'link_button_color' => '',
	) );

	$css = <<<CSS
	/* Color Scheme */
	button:hover,
	input[type="button"]:hover,
	input[type="reset"]:hover,
	input[type="submit"]:hover,
	.button:hover,
	a.button:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	a {
		color: {$colors['link_button_color']};
	}

	a:visited {
		color: {$colors['link_button_color']};
	}

	a:hover, a:focus, a:active {
		color: #000;
	}

	#menu-primary > ul > li > a:hover {
		color: {$colors['link_button_color']};
	}

	.breadcrumb-trail a:hover, .breadcrumb-trail a:visited:hover {
		color: {$colors['link_button_color']};
	}

	.sidebar a {
		color: {$colors['link_button_color']};
	}

	.sidebar a:hover, .sidebar a:focus {
		color: {$colors['link_button_color']};
		border-bottom: 1px solid {$colors['link_button_color']};
	}

	.sidebar ul li a:hover, .sidebar ul li a:focus {
		color: {$colors['link_button_color']};
	}

	.sidebar .widget_price_filter .price_slider_amount .button:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	.sidebar .widget_adoration_recent_entries a:focus .link {
		color: {$colors['link_button_color']};
		border-bottom: 1px solid {$colors['link_button_color']};
	}

	.sidebar .widget_top_rated_products a:hover .product-title, .sidebar .widget_top_rated_products a:focus .product-title,
	.sidebar .widget_recent_reviews a:hover .product-title,
	.sidebar .widget_recent_reviews a:focus .product-title,
	.sidebar .widget_recently_viewed_products a:hover .product-title,
	.sidebar .widget_recently_viewed_products a:focus .product-title,
	.sidebar .widget_products a:hover .product-title,
	.sidebar .widget_products a:focus .product-title,
	.sidebar .widget_shopping_cart a:hover .product-title,
	.sidebar .widget_shopping_cart a:focus .product-title {
		color: {$colors['link_button_color']};
		border-bottom: 1px solid {$colors['link_button_color']};
	}

	.sidebar .widget_shopping_cart a.remove:hover {
		background-color: {$colors['link_button_color']};
	}

	.sidebar .widget_shopping_cart .buttons a:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	.sidebar .tagcloud a:hover, .sidebar .tagcloud a:focus {
		background-color: {$colors['link_button_color']};
	}

	.sidebar .tagcloud a:hover:after, .sidebar .tagcloud a:focus:after {
		border-left: 15px solid {$colors['link_button_color']};
	}

	.sidebar select:focus, .sidebar select:hover {
		border-color: {$colors['link_button_color']};
	}

	.sidebar #wp-calendar tr th a:hover, .sidebar #wp-calendar tr th a:focus, .sidebar #wp-calendar tr td a:hover, .sidebar #wp-calendar tr td a:focus {
		color: {$colors['link_button_color']};
	}

	.footer-sidebar .sidebar ul li a:hover, .footer-sidebar .sidebar ul li a:focus {
		color: {$colors['link_button_color']};
	}

	.footer-sidebar .sidebar .widget_top_rated_products a:hover .product-title, .footer-sidebar .sidebar .widget_top_rated_products a:hover .post-title, .footer-sidebar .sidebar .widget_top_rated_products a:hover .comment-author, .footer-sidebar .sidebar .widget_top_rated_products a:focus .product-title, .footer-sidebar .sidebar .widget_top_rated_products a:focus .post-title, .footer-sidebar .sidebar .widget_top_rated_products a:focus .comment-author,
	.footer-sidebar .sidebar .widget_recent_reviews a:hover .product-title,
	.footer-sidebar .sidebar .widget_recent_reviews a:hover .post-title,
	.footer-sidebar .sidebar .widget_recent_reviews a:hover .comment-author,
	.footer-sidebar .sidebar .widget_recent_reviews a:focus .product-title,
	.footer-sidebar .sidebar .widget_recent_reviews a:focus .post-title,
	.footer-sidebar .sidebar .widget_recent_reviews a:focus .comment-author,
	.footer-sidebar .sidebar .widget_recently_viewed_products a:hover .product-title,
	.footer-sidebar .sidebar .widget_recently_viewed_products a:hover .post-title,
	.footer-sidebar .sidebar .widget_recently_viewed_products a:hover .comment-author,
	.footer-sidebar .sidebar .widget_recently_viewed_products a:focus .product-title,
	.footer-sidebar .sidebar .widget_recently_viewed_products a:focus .post-title,
	.footer-sidebar .sidebar .widget_recently_viewed_products a:focus .comment-author,
	.footer-sidebar .sidebar .widget_products a:hover .product-title,
	.footer-sidebar .sidebar .widget_products a:hover .post-title,
	.footer-sidebar .sidebar .widget_products a:hover .comment-author,
	.footer-sidebar .sidebar .widget_products a:focus .product-title,
	.footer-sidebar .sidebar .widget_products a:focus .post-title,
	.footer-sidebar .sidebar .widget_products a:focus .comment-author,
	.footer-sidebar .sidebar .widget_shopping_cart a:hover .product-title,
	.footer-sidebar .sidebar .widget_shopping_cart a:hover .post-title,
	.footer-sidebar .sidebar .widget_shopping_cart a:hover .comment-author,
	.footer-sidebar .sidebar .widget_shopping_cart a:focus .product-title,
	.footer-sidebar .sidebar .widget_shopping_cart a:focus .post-title,
	.footer-sidebar .sidebar .widget_shopping_cart a:focus .comment-author,
	.footer-sidebar .sidebar .widget_adoration_recent_comments a:hover .product-title,
	.footer-sidebar .sidebar .widget_adoration_recent_comments a:hover .post-title,
	.footer-sidebar .sidebar .widget_adoration_recent_comments a:hover .comment-author,
	.footer-sidebar .sidebar .widget_adoration_recent_comments a:focus .product-title,
	.footer-sidebar .sidebar .widget_adoration_recent_comments a:focus .post-title,
	.footer-sidebar .sidebar .widget_adoration_recent_comments a:focus .comment-author,
	.footer-sidebar .sidebar .widget_adoration_recent_entries a:hover .product-title,
	.footer-sidebar .sidebar .widget_adoration_recent_entries a:hover .post-title,
	.footer-sidebar .sidebar .widget_adoration_recent_entries a:hover .comment-author,
	.footer-sidebar .sidebar .widget_adoration_recent_entries a:focus .product-title,
	.footer-sidebar .sidebar .widget_adoration_recent_entries a:focus .post-title,
	.footer-sidebar .sidebar .widget_adoration_recent_entries a:focus .comment-author {
		color: {$colors['link_button_color']};
		border-bottom: 1px solid {$colors['link_button_color']};
	}

	.footer-sidebar .sidebar .tagcloud a:hover, .footer-sidebar .sidebar .tagcloud a:focus {
		background-color: {$colors['link_button_color']};
	}

	.footer-sidebar .sidebar .tagcloud a:hover:after, .footer-sidebar .sidebar .tagcloud a:focus:after {
		border-left: 15px solid {$colors['link_button_color']};
	}

	.footer-sidebar .sidebar input[type="text"]:focus,
	.footer-sidebar .sidebar input[type="email"]:focus,
	.footer-sidebar .sidebar input[type="url"]:focus,
	.footer-sidebar .sidebar input[type="password"]:focus,
	.footer-sidebar .sidebar input[type="search"]:focus,
	.footer-sidebar .sidebar input[type="number"]:focus,
	.footer-sidebar .sidebar select:focus,
	.footer-sidebar .sidebar textarea:focus {
		border: 1px solid {$colors['link_button_color']};
	}

	.footer-sidebar .sidebar .widget_shopping_cart a.remove:hover {
		background-color: {$colors['link_button_color']};
	}

	.footer-sidebar .sidebar .widget_shopping_cart .buttons a:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	.footer-sidebar .sidebar .widget_shopping_cart .buttons a:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	.footer-sidebar .sidebar .widget_price_filter .price_slider_amount .button:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	.footer-sidebar .sidebar .widget_price_filter .price_slider_amount .button:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	.footer-sidebar .sidebar #wp-calendar tr th a:hover, .footer-sidebar .sidebar #wp-calendar tr th a:focus, .footer-sidebar .sidebar #wp-calendar tr td a:hover, .footer-sidebar .sidebar #wp-calendar tr td a:focus {
		color: {$colors['link_button_color']};
	}

	body.singular-post .post-password-form input[type=submit]:hover, body.singular-page:not(.woocommerce-page):not(.home) .post-password-form input[type=submit]:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.singular-post .post-password-form input[type=submit]:hover, body.singular-page:not(.woocommerce-page):not(.home) .post-password-form input[type=submit]:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	#comments li.comment article .comment-author a:hover, #comments li.pingback article .comment-author a:hover, #reviews li.comment article .comment-author a:hover, #reviews li.pingback article .comment-author a:hover {
		color: {$colors['link_button_color']};
		border-bottom: 1px solid {$colors['link_button_color']};
	}

	#comments li.comment article a.comment-reply-link:hover, #comments li.pingback article a.comment-reply-link:hover, #reviews li.comment article a.comment-reply-link:hover, #reviews li.pingback article a.comment-reply-link:hover {
		color: {$colors['link_button_color']};
		border-color: {$colors['link_button_color']};
	}

	#comments .must-log-in, #reviews .must-log-in {
		background-color: {$colors['link_button_color']};
	}

	#respond form p.form-submit input[type=submit]:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	#respond form p.form-submit input[type=submit]:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	#respond .comment-reply-title small a:hover {
		color: {$colors['link_button_color']};
		border-color: {$colors['link_button_color']};
	}

	.comment-nav-wrap nav.comments-nav a:hover {
		color: {$colors['link_button_color']};
	}

	.entry-byline a:hover, .entry-byline a:focus, .entry-byline a:visited:hover, .entry-byline a:visited:focus {
		color: {$colors['link_button_color']};
	}

	body.blog h2.entry-title a:hover, body.blog h2.entry-title a:focus, body.archive h2.entry-title a:hover, body.archive h2.entry-title a:focus, body.search h2.entry-title a:hover, body.search h2.entry-title a:focus {
		color: {$colors['link_button_color']};
	}

	body.blog article.highlight .entry-byline a:hover, body.blog article.highlight .entry-byline a:focus, body.archive article.highlight .entry-byline a:hover, body.archive article.highlight .entry-byline a:focus, body.search article.highlight .entry-byline a:hover, body.search article.highlight .entry-byline a:focus {
		color: {$colors['link_button_color']};
	}

	body.blog article.highlight h2.entry-title a:hover, body.blog article.highlight h2.entry-title a:focus, body.archive article.highlight h2.entry-title a:hover, body.archive article.highlight h2.entry-title a:focus, body.search article.highlight h2.entry-title a:hover, body.search article.highlight h2.entry-title a:focus {
		color: {$colors['link_button_color']};
	}

	nav.pagination a:hover, nav.pagination a:focus {
		color: {$colors['link_button_color']};
	}

	.regular-search input[type="search"]:focus, .regular-search input[type="search"]:hover, .widget_product_search input[type="search"]:focus, .widget_product_search input[type="search"]:hover {
		border-color: {$colors['link_button_color']};
	}

	#subsidiary-banner .cart .widget_shopping_cart_content a:hover .product-title, #subsidiary-banner .cart .widget_shopping_cart_content a:focus .product-title {
		color: {$colors['link_button_color']};
		border-bottom: 1px solid {$colors['link_button_color']};
	}

	#subsidiary-banner .cart .widget_shopping_cart_content a.remove:hover {
		background-color: {$colors['link_button_color']};
	}

	#subsidiary-banner .cart .widget_shopping_cart_content .buttons a:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	#subsidiary-banner .cart .widget_shopping_cart_content .buttons a:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	#header .site-branding .site-title a:hover {
		color: {$colors['link_button_color']};
	}

	#footer .footer-end a:hover {
		color: {$colors['link_button_color']};
	}

	ul.products a:first-child:hover h3 {
		color: {$colors['link_button_color']} !important;
	}

	ul.products a.added_to_cart:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	ul.products a.added_to_cart:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.woocommerce.archive form.woocommerce-ordering select:focus, body.woocommerce.archive form.woocommerce-ordering select:hover {
		border-color: {$colors['link_button_color']};
	}

	.addresses .edit:hover {
		color: {$colors['link_button_color']};
	}

	.addresses .edit:hover:before {
		border: 2px solid {$colors['link_button_color']};
	}

	body.woocommerce-cart #content table.shop_table.cart > tbody > tr > td a:hover {
		color: {$colors['link_button_color']};
		border-bottom: 1px solid {$colors['link_button_color']};
	}

	body.woocommerce-cart #content table.shop_table.cart .product-remove a:hover {
		background-color: {$colors['link_button_color']};
	}

	body.woocommerce-cart #content table.shop_table.cart .coupon input[type=submit]:hover, body.woocommerce-cart #content table.shop_table.cart .coupon + input[type=submit]:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.woocommerce-cart #content table.shop_table.cart .coupon + input[type=submit]:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.woocommerce-cart #content .cart-collaterals table .shipping-calculator-button:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.woocommerce-cart #content .cart-collaterals table button[type=submit]:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.woocommerce-cart #content .cart-collaterals .wc-proceed-to-checkout a:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.woocommerce-checkout #content #payment #place_order:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.woocommerce-checkout #content .checkout_coupon input[type=submit]:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.woocommerce-checkout #content form.login input[type=submit]:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.woocommerce-account #content table.shop_table.my_account_orders > tbody > tr > td a:hover {
		color: {$colors['link_button_color']};
		border-bottom: 1px solid {$colors['link_button_color']};
	}

	body.woocommerce-account #content table.shop_table.my_account_orders .order-actions a:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.woocommerce-account #content form input[type=submit]:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.singular-product #content div.entry-summary .woocommerce-product-rating a:hover {
		color: {$colors['link_button_color']};
	}

	body.singular-product #content table.variations select:focus, body.singular-product #content table.variations select:hover {
		border-color: {$colors['link_button_color']};
	}

	body.singular-product #content button.button:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.singular-product #content button.button:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	body.singular-product #content .woocommerce-verification-required {
		background-color: {$colors['link_button_color']};
	}

	.wpcf7 input[type=submit]:hover {
		border: 2px solid {$colors['link_button_color']};
		background-color: {$colors['link_button_color']};
	}

	header.hero-slider .slides .slides-inner > .slide .inner-col .text .button-group a.default:focus, header.hero-slider .slides .slides-inner > .slide .inner-col .text .button-group a.default:hover, header.hero-slider .slides .slides-inner > .slide .inner-col .text .button-group a.secondary:focus, header.hero-slider .slides .slides-inner > .slide .inner-col .text .button-group a.secondary:hover {
		background-color: {$colors['link_button_color']};
		border-color: {$colors['link_button_color']};
	}

	.sidebar .widget_adoration_recent_comments a:hover .link, .sidebar .widget_adoration_recent_comments a:focus .link,
	.sidebar .widget_adoration_recent_entries a:hover .link,
	.sidebar .widget_adoration_recent_entries a:focus .link {
		color: {$colors['background_color']};
		border-bottom: 1px solid {$colors['background_color']};
	}

	/* Background Color */
	body {
		background-color: {$colors['background_color']};
	}
CSS;

	return $css;
}

/**
 * Output an Underscore template for generating CSS for the color scheme.
 *
 * The template generates the css dynamically for instant display in the Customizer preview.
 *
 * @since  1.0.0
 */
function adoration_color_scheme_css_template() {
	$colors = array(
		'background_color'  => '{{ data.background_color }}',
		'link_button_color' => '{{ data.link_button_color }}',
	);
	?>
	<script type="text/html" id="tmpl-adoration-color-scheme">
		<?php echo adoration_get_color_scheme_css( $colors ); ?>
	</script>
	<?php
}
