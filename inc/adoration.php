<?php
/**
 * Sets up custom filters and actions for the theme. This does things like sets up sidebars, menus, scripts,
 * and lots of other awesome stuff that WordPress themes do.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/* Register custom image sizes. */
add_action( 'init', 'adoration_register_image_sizes', 5 );

/* Register custom menus. */
add_action( 'init', 'adoration_register_menus', 5 );

/* Register sidebars. */
add_action( 'widgets_init', 'adoration_register_sidebars', 5 );

/* Add custom scripts. */
add_action( 'wp_enqueue_scripts', 'adoration_enqueue_scripts' );

/* Register custom styles. */
add_action( 'wp_enqueue_scripts', 'adoration_register_styles', 0 );

/* Displays the breadcrumbs. */
add_action( 'adoration_breadcrumbs', 'adoration_breadcrumbs' );

/* Adds a class to the WordPress SEO breadcrumbs. */
add_filter( 'wpseo_breadcrumb_output_class', 'adoration_breadcrumb_class' );

/* Disables comments on the page template. */
add_filter( 'adoration_pre_comments', 'adoration_disable_comments_on_pages' );

/* Remove Contact Form 7 styles. */
add_filter( 'wpcf7_load_css', '__return_false' );

/* Filter the search form HTML. */
add_filter( 'get_search_form', 'adoration_search_form' );

/* Adds custom attributes to the subsidiary sidebar. */
add_filter( 'hybrid_attr_sidebar', 'adoration_sidebar_subsidiary_class', 10, 2 );

/* Removes the "blog" title on the blog archive. */
add_filter( 'adoration_show_loop_meta', 'adoration_disable_loop_meta_on_blog_archive' );

/* Adds the "highlight" class to the most recent post in the blog archive. */
add_filter( 'post_class', 'adoration_add_highlight_class_to_first_post', 0, 3 );

/* Removes inline styles from tag clouds. */
add_filter( 'wp_generate_tag_cloud', 'adoration_tag_cloud' );

/* Registers Widgets. */
add_action( 'widgets_init', 'adoration_register_widgets' );

/* Adds a class to the previous comment page link. */
add_filter( 'previous_comments_link_attributes', 'adoration_previous_comments_link_class' );

/* Adds a class to the next comment page link. */
add_filter( 'next_comments_link_attributes', 'adoration_next_comments_link_class' );

/* Modify the password form on password protected posts. */
add_filter( 'the_password_form', 'adoration_password_form' );

/* Wraps oembed video elements to provide extra styling options. */
add_filter( 'oembed_dataparse', 'adoration_oembed_dataparse', 10, 3 );

/* Enqueues front-end CSS for the link button color. */
add_action( 'wp_enqueue_scripts', 'adoration_link_button_color_css', 11 );

/* Consider empty post queries as 404s. */
add_filter( 'body_class', 'adoration_empty_queries_as_404' );

/**
 * Registers custom image sizes for the theme.
 *
 * @since  1.0.0
 */
function adoration_register_image_sizes() {

	// Post thumbnails on the main blog index.
	add_image_size( 'adoration-post-thumb', 680, 9999 );

	// Post thumbnails in the recent posts widget.
	add_image_size( 'adoration-widget-thumbnail', 72, 72, true );

	/* The rest of the changes are WC specific, exit early if we're not running WooCommerce. */
	if ( ! is_woocommerce_activated() ) {
		return;
	}

	/* Resizes WooCommerces default shop_thumbnail image size. */
	remove_image_size( 'shop_thumbnail' );

	/* Product thumbnails used in the widget area. */
	add_image_size( 'shop_thumbnail', 72, 72, true );

	/*  Used for product category thumbnails. */
	add_image_size( 'adoration-featured-products-thumb', 510, 510 );

	/* Used on single product pages for the additional image thumbnails. */
	add_image_size( 'adoration-products-small', 74, 74, true );
}

/**
 * Registers nav menu locations.
 *
 * @since  1.0.0
 */
function adoration_register_menus() {
	register_nav_menu( 'primary', _x( 'Primary', 'Nav menu location', 'adoration' ) );
	register_nav_menu( 'social',  _x( 'Social',  'Nav menu location', 'adoration' ) );
	register_nav_menu( 'footer',  _x( 'Footer',  'Nav menu location', 'adoration' ) );
}

/**
 * Registers sidebars.
 *
 * @since  1.0.0
 */
function adoration_register_sidebars() {

	hybrid_register_sidebar(
		array(
			'id'           => 'primary',
			'name'         => _x( 'Primary', 'Sidebar name', 'adoration' ),
			'description'  => __( 'The main sidebar. It is displayed on either the left or right side of the page based on the chosen layout.', 'adoration' ),
		)
	);

	hybrid_register_sidebar(
		array(
			'id'          => 'subsidiary',
			'name'        => _x( 'Subsidiary', 'Sidebar name', 'adoration' ),
			'description' => __( 'A sidebar located in the footer of the site. Optimized for one, two, or three widgets (and multiples thereof).', 'adoration' ),
		)
	);
}

/**
 * Enqueues scripts.
 *
 * @since  1.0.0
 */
function adoration_enqueue_scripts() {

	$suffix    = hybrid_get_min_suffix();
	$theme_uri = trailingslashit( get_template_directory_uri() );
	$version   = adoration_get_version();

	wp_register_script( 'adoration-skip-link-focus-fix', $theme_uri . "js/skip-link-focus-fix{$suffix}.js", array(), $version, true );
	wp_register_script( 'adoration',                     $theme_uri . "js/adoration{$suffix}.js", array( 'jquery' ), $version, true );

	wp_enqueue_script( 'adoration-skip-link-focus-fix' );
	wp_enqueue_script( 'adoration' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/**
 * Registers custom stylesheets for the front end.
 *
 * @since  1.0.0
 */
function adoration_register_styles() {

	$google_fonts_uri = apply_filters( 'adoration_google_fonts_uri', '//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,900,300italic,400italic,700italic,900italic' );

	wp_register_style( 'adoration-fonts', $google_fonts_uri );
}

/**
 * Displays the breadcrumbs.
 *
 * Attempts to use WordPress SEO's breadcrumbs if available.
 *
 * Falls back to using Hybrid Core's breadcrumbs if WordPress SEO isn't installed or enabled.
 *
 * @since  1.0.0
 */
function adoration_breadcrumbs() {

	$i = 0;

	// Attempt to use WordPress SEO's breadcrumb function
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		$options = get_option( 'wpseo_internallinks' );
		if ( true === $options['breadcrumbs-enable'] ) {
			yoast_breadcrumb();
			return;
		}
	}

	// Fallback to using Hybrid Core's breadcrumbs
	$args = array(
		'show_browse' => false,
		'separator'   => '&rsaquo;',
	);

	echo breadcrumb_trail( $args );
}

/**
 * Adds a class to the WordPress SEO breadcrumb wrapper.
 *
 * @since   1.0.0
 * @return  string
 */
function adoration_breadcrumb_class() {
	return 'breadcrumb-trail';
}

/**
 * Disables comments on the page template.
 *
 * @since   1.0.0
 * @param   boolean|string  $default  Boolean false (the default) or previously filtered value.
 * @return  boolean|string            Boolean false (the default) or previously filtered value.
 */
function adoration_disable_comments_on_pages( $default ) {

	if ( false !== $default || ! is_page() ) {
		return $default;
	}

	return '';
}

/**
 * Returns the HTML used to display the search form.
 *
 * @since   1.0.0
 * @return  string
 */
function adoration_search_form() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_search_form', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	ob_start(); ?>

	<div class="search-container regular-search">
		<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label>
				<span class="screen-reader-text"><?php echo _x( 'Search for:', 'label', 'adoration' ); ?></span>
				<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search&hellip;', 'placeholder', 'adoration' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'adoration' ); ?>" />
			</label>
			<input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'adoration' ); ?>" />
		</form>
	</div><?php

	return ob_get_clean();
}

/**
 * Adds a custom class to the 'subsidiary' sidebar.  This is used to determine the number of columns used to
 * display the sidebar's widgets.  This optimizes for 1, 2, and 3 columns or multiples of those values.
 *
 * Note that we're using the global $sidebars_widgets variable here. This is because core has marked
 * wp_get_sidebars_widgets() as a private function. Therefore, this leaves us with $sidebars_widgets for
 * figuring out the widget count.
 * @link http://codex.wordpress.org/Function_Reference/wp_get_sidebars_widgets
 *
 * @since   1.0.0
 * @param   array   $attr
 * @param   string  $context
 * @return  array
 */
function adoration_sidebar_subsidiary_class( $attr, $context ) {

	if ( 'subsidiary' === $context ) {
		global $sidebars_widgets;

		if ( is_array( $sidebars_widgets ) && !empty( $sidebars_widgets[ $context ] ) ) {

			$count = count( $sidebars_widgets[ $context ] );

			if ( 1 === $count ) {
				$attr['class'] .= ' columns-1';
			} else if ( ! ( $count % 3 ) || $count % 2 ) {
				$attr['class'] .= ' columns-3';
			} else if ( ! ( $count % 2 ) ) {
				$attr['class'] .= ' columns-2';
			}
		}
	}

	return $attr;
}

/**
 * Removes the "blog" title on the blog archive.
 *
 * @since   1.0.0
 * @param   boolean  $show  Whether the loop meta is being displayed.
 * @return  boolean
 */
function adoration_disable_loop_meta_on_blog_archive( $show ) {

	if ( false === is_home() ) {
		return $show;
	}

	return false;
}

/**
 * Adds the "highlight" class to the most recent post in the blog archive.
 *
 * @since   1.0.0
 * @param   array         $classes
 * @param   string|array  $class
 * @param   int           $post_id
 * @return  array
 */
function adoration_add_highlight_class_to_first_post( $classes, $class, $post_id ) {
	global $wp_query;

	if ( ! is_home() || 0 !== get_query_var( 'paged' ) || empty( $wp_query->posts ) ) {
		return $classes;
	}

	if ( $wp_query->posts[0]->ID !== $post_id ) {
		return $classes;
	}

	$classes[] = 'highlight';

	return $classes;
}

/**
 * Removes inline styles from tag clouds.
 *
 * @since  1.0.0
 * @param  string  $tag_string  Tag cloud.
 * @return string               Tag cloud without inline styles.
 */
function adoration_tag_cloud( $tag_string ) {
	return preg_replace( "/style='font-size:.+pt;'/", '', $tag_string );
}

/**
 * Replaces the widget's display callback with the Dynamic Sidebar Params display callback, storing the original callback for use later.
 *
 * The $sidebar_params array is not modified; it is only used to get the current widget ID.
 *
 * @since   1.0.0
 * @param   array  $sidebar_params  The sidebar parameters
 * @return  array                   The sidebar parameters
 */
function adoration_widget_output_filters_dynamic_sidebar_params( $sidebar_params ) {

	if ( is_admin() ) {
		return $sidebar_params;
	}

	global $wp_registered_widgets;

	$widget_id = $sidebar_params[0]['widget_id'];

	$wp_registered_widgets[ $widget_id ]['original_callback'] = $wp_registered_widgets[ $widget_id ]['callback'];
	$wp_registered_widgets[ $widget_id ]['callback'] = 'adoration_widget_output_filters_display_widget';

	return $sidebar_params;
}

/**
 * Callback function to display the widget's original callback function output, with filtering.
 *
 * @since  1.0.0
 */
function adoration_widget_output_filters_display_widget() {
	global $wp_registered_widgets;

	$original_callback_params = func_get_args();
	$widget_id = $original_callback_params[0]['widget_id'];

	$original_callback = $wp_registered_widgets[ $widget_id ]['original_callback'];
	$wp_registered_widgets[ $widget_id ]['callback'] = $original_callback;

	$widget_id_base = $wp_registered_widgets[ $widget_id ]['callback'][0]->id_base;

	if ( ! is_callable( $original_callback ) ) {
		return;
	}

	ob_start();
	call_user_func_array( $original_callback, $original_callback_params );
	$widget_output = ob_get_clean();

	echo apply_filters( 'adoration_widget_output', $widget_output, $widget_id_base, $widget_id );
}

/**
 * Registers Widgets.
 *
 * @since  1.0.0
 */
function adoration_register_widgets() {
	register_widget( 'Adoration_Widget_Recent_Posts' );
	register_widget( 'Adoration_Widget_Recent_Comments' );
}

/**
 * Adds a class to the previous comment page link.
 *
 * @since   1.0.0
 * @param   string  $attributes  Existing previous comment link attributes.
 * @return  string               Existing previous comment link attributes with a class attribute appended.
 */
function adoration_previous_comments_link_class( $attributes ) {
	return trim( $attributes . ' class="comments-previous-page"' );
}

/**
 * Adds a class to the next comment page link.
 *
 * @since   1.0.0
 * @param   string  $attributes  Existing next comment link attributes.
 * @return  string               Existing next comment link attributes with a class attribute appended.
 */
function adoration_next_comments_link_class( $attributes ) {
	return trim( $attributes . ' class="comments-next-page"' );
}

/**
 * Modify the password form on password protected posts.
 *
 * @since   1.0.0
 * @return  string  The password form HTML output.
 */
function adoration_password_form() {

	ob_start(); ?>

	<form action="<?php echo esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); ?>" class="post-password-form" method="post">
		<p><?php _e( 'This content is password protected. To view it please enter your password below:', 'adoration' ); ?></p>
		<p class="post-password-wrap">
			<input name="post_password" type="password" size="20" class="post-password" placeholder="<?php esc_attr_e( 'Post password here...', 'adoration' ); ?>" />
			<input type="submit" name="Submit" value="<?php esc_attr_e( 'Submit', 'adoration' ); ?>" />
		</p>
	</form><?php

	return ob_get_clean();
}

/**
 * Wraps oembed video elements to provide extra styling options.
 *
 * @since   1.0.0
 * @param   string  $return  The returned oEmbed HTML.
 * @param   object  $data    A data object result from an oEmbed provider.
 * @param   string  $url     The URL of the content to be embedded.
 * @return  string           The returned oEmbed HTML.
 */
function adoration_oembed_dataparse( $return, $data, $url ) {

	if ( 'video' !== $data->type ) {
		return $return;
	}

	return sprintf( '<div class="oembed-video-wrap">%s</div>', $return );
}

/**
 * Enqueues front-end CSS for the link button color.
 *
 * @since  1.0.0
 */
function adoration_link_button_color_css() {

	$color_scheme      = adoration_get_color_scheme();
	$default_color     = $color_scheme[1];
	$link_button_color = get_theme_mod( 'link_button_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $link_button_color === $default_color ) {
		return;
	}

	$css = '
		/* Color Scheme */
		button:hover,
		input[type="button"]:hover,
		input[type="reset"]:hover,
		input[type="submit"]:hover,
		.button:hover,
		a.button:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		a {
			color: %1$s;
		}

		a:visited {
			color: %1$s;
		}

		a:hover, a:focus, a:active {
			color: black;
		}

		#menu-primary > ul > li > a:hover {
			color: %1$s;
		}

		.breadcrumb-trail a:hover, .breadcrumb-trail a:visited:hover {
			color: %1$s;
		}

		.sidebar a {
			color: %1$s;
		}

		.sidebar a:hover, .sidebar a:focus {
			color: %1$s;
			border-bottom: 1px solid %1$s;
		}

		.sidebar ul li a:hover, .sidebar ul li a:focus {
			color: %1$s;
		}

		.sidebar .widget_price_filter .price_slider_amount .button:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		.sidebar .widget_adoration_recent_entries a:focus .link {
			color: %1$s;
			border-bottom: 1px solid %1$s;
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
			color: %1$s;
			border-bottom: 1px solid %1$s;
		}

		.sidebar .widget_shopping_cart a.remove:hover {
			background-color: %1$s;
		}

		.sidebar .widget_shopping_cart .buttons a:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		.sidebar .tagcloud a:hover, .sidebar .tagcloud a:focus {
			background-color: %1$s;
		}

		.sidebar .tagcloud a:hover:after, .sidebar .tagcloud a:focus:after {
			border-left: 15px solid %1$s;
		}

		.sidebar select:focus, .sidebar select:hover {
			border-color: %1$s;
		}

		.sidebar #wp-calendar tr th a:hover, .sidebar #wp-calendar tr th a:focus, .sidebar #wp-calendar tr td a:hover, .sidebar #wp-calendar tr td a:focus {
			color: %1$s;
		}

		.footer-sidebar .sidebar ul li a:hover, .footer-sidebar .sidebar ul li a:focus {
			color: %1$s;
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
			color: %1$s;
			border-bottom: 1px solid %1$s;
		}

		.footer-sidebar .sidebar .tagcloud a:hover, .footer-sidebar .sidebar .tagcloud a:focus {
			background-color: %1$s;
		}

		.footer-sidebar .sidebar .tagcloud a:hover:after, .footer-sidebar .sidebar .tagcloud a:focus:after {
			border-left: 15px solid %1$s;
		}

		.footer-sidebar .sidebar input[type="text"]:focus,
		.footer-sidebar .sidebar input[type="email"]:focus,
		.footer-sidebar .sidebar input[type="url"]:focus,
		.footer-sidebar .sidebar input[type="password"]:focus,
		.footer-sidebar .sidebar input[type="search"]:focus,
		.footer-sidebar .sidebar input[type="number"]:focus,
		.footer-sidebar .sidebar select:focus,
		.footer-sidebar .sidebar textarea:focus {
			border: 1px solid %1$s;
		}

		.footer-sidebar .sidebar .widget_shopping_cart a.remove:hover {
			background-color: %1$s;
		}

		.footer-sidebar .sidebar .widget_shopping_cart .buttons a:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		.footer-sidebar .sidebar .widget_shopping_cart .buttons a:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		.footer-sidebar .sidebar .widget_price_filter .price_slider_amount .button:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		.footer-sidebar .sidebar .widget_price_filter .price_slider_amount .button:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		.footer-sidebar .sidebar #wp-calendar tr th a:hover, .footer-sidebar .sidebar #wp-calendar tr th a:focus, .footer-sidebar .sidebar #wp-calendar tr td a:hover, .footer-sidebar .sidebar #wp-calendar tr td a:focus {
			color: %1$s;
		}

		body.singular-post .post-password-form input[type=submit]:hover, body.singular-page:not(.woocommerce-page):not(.home) .post-password-form input[type=submit]:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.singular-post .post-password-form input[type=submit]:hover, body.singular-page:not(.woocommerce-page):not(.home) .post-password-form input[type=submit]:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		#comments li.comment article .comment-author a:hover, #comments li.pingback article .comment-author a:hover, #reviews li.comment article .comment-author a:hover, #reviews li.pingback article .comment-author a:hover {
			color: %1$s;
			border-bottom: 1px solid %1$s;
		}

		#comments li.comment article a.comment-reply-link:hover, #comments li.pingback article a.comment-reply-link:hover, #reviews li.comment article a.comment-reply-link:hover, #reviews li.pingback article a.comment-reply-link:hover {
			color: %1$s;
			border-color: %1$s;
		}

		#comments .must-log-in, #reviews .must-log-in {
			background-color: %1$s;
		}

		#respond form p.form-submit input[type=submit]:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		#respond form p.form-submit input[type=submit]:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		#respond .comment-reply-title small a:hover {
			color: %1$s;
			border-color: %1$s;
		}

		.comment-nav-wrap nav.comments-nav a:hover {
			color: %1$s;
		}

		.entry-byline a:hover, .entry-byline a:focus, .entry-byline a:visited:hover, .entry-byline a:visited:focus {
			color: %1$s;
		}

		body.blog h2.entry-title a:hover, body.blog h2.entry-title a:focus, body.archive h2.entry-title a:hover, body.archive h2.entry-title a:focus, body.search h2.entry-title a:hover, body.search h2.entry-title a:focus {
			color: %1$s;
		}

		body.blog article.highlight .entry-byline a:hover, body.blog article.highlight .entry-byline a:focus, body.archive article.highlight .entry-byline a:hover, body.archive article.highlight .entry-byline a:focus, body.search article.highlight .entry-byline a:hover, body.search article.highlight .entry-byline a:focus {
			color: %1$s;
		}

		body.blog article.highlight h2.entry-title a:hover, body.blog article.highlight h2.entry-title a:focus, body.archive article.highlight h2.entry-title a:hover, body.archive article.highlight h2.entry-title a:focus, body.search article.highlight h2.entry-title a:hover, body.search article.highlight h2.entry-title a:focus {
			color: %1$s;
		}

		nav.pagination a:hover, nav.pagination a:focus {
			color: %1$s;
		}

		.regular-search input[type="search"]:focus, .regular-search input[type="search"]:hover, .widget_product_search input[type="search"]:focus, .widget_product_search input[type="search"]:hover {
			border-color: %1$s;
		}

		#subsidiary-banner .cart .widget_shopping_cart_content a:hover .product-title, #subsidiary-banner .cart .widget_shopping_cart_content a:focus .product-title {
			color: %1$s;
			border-bottom: 1px solid %1$s;
		}

		#subsidiary-banner .cart .widget_shopping_cart_content a.remove:hover {
			background-color: %1$s;
		}

		#subsidiary-banner .cart .widget_shopping_cart_content .buttons a:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		#subsidiary-banner .cart .widget_shopping_cart_content .buttons a:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		#header .site-branding .site-title a:hover {
			color: %1$s;
		}

		#footer .footer-end a:hover {
			color: %1$s;
		}

		ul.products a:first-child:hover h3 {
			color: %1$s !important;
		}

		ul.products a.added_to_cart:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		ul.products a.added_to_cart:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.woocommerce.archive form.woocommerce-ordering select:focus, body.woocommerce.archive form.woocommerce-ordering select:hover {
			border-color: %1$s;
		}

		.addresses .edit:hover {
			color: %1$s;
		}

		.addresses .edit:hover:before {
			border: 2px solid %1$s;
		}

		body.woocommerce-cart #content table.shop_table.cart > tbody > tr > td a:hover {
			color: %1$s;
			border-bottom: 1px solid %1$s;
		}

		body.woocommerce-cart #content table.shop_table.cart .product-remove a:hover {
			background-color: %1$s;
		}

		body.woocommerce-cart #content table.shop_table.cart .coupon input[type=submit]:hover, body.woocommerce-cart #content table.shop_table.cart .coupon + input[type=submit]:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.woocommerce-cart #content table.shop_table.cart .coupon + input[type=submit]:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.woocommerce-cart #content .cart-collaterals table .shipping-calculator-button:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.woocommerce-cart #content .cart-collaterals table button[type=submit]:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.woocommerce-cart #content .cart-collaterals .wc-proceed-to-checkout a:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.woocommerce-checkout #content #payment #place_order:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.woocommerce-checkout #content .checkout_coupon input[type=submit]:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.woocommerce-checkout #content form.login input[type=submit]:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.woocommerce-account #content table.shop_table.my_account_orders > tbody > tr > td a:hover {
			color: %1$s;
			border-bottom: 1px solid %1$s;
		}

		body.woocommerce-account #content table.shop_table.my_account_orders .order-actions a:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.woocommerce-account #content form input[type=submit]:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.singular-product #content div.entry-summary .woocommerce-product-rating a:hover {
			color: %1$s;
		}

		body.singular-product #content table.variations select:focus, body.singular-product #content table.variations select:hover {
			border-color: %1$s;
		}

		body.singular-product #content button.button:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.singular-product #content button.button:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		body.singular-product #content .woocommerce-verification-required {
			background-color: %1$s;
		}

		.wpcf7 input[type=submit]:hover {
			border: 2px solid %1$s;
			background-color: %1$s;
		}

		header.hero-slider .slides .slides-inner > .slide .inner-col .text .button-group a.default:focus, header.hero-slider .slides .slides-inner > .slide .inner-col .text .button-group a.default:hover, header.hero-slider .slides .slides-inner > .slide .inner-col .text .button-group a.secondary:focus, header.hero-slider .slides .slides-inner > .slide .inner-col .text .button-group a.secondary:hover {
			background-color: %1$s;
			border-color: %1$s;
		}

		.sidebar .widget_adoration_recent_comments a:hover .link, .sidebar .widget_adoration_recent_comments a:focus .link,
		.sidebar .widget_adoration_recent_entries a:hover .link,
		.sidebar .widget_adoration_recent_entries a:focus .link {
			color: %1$s;
			border-bottom: 1px solid %1$s;
		}
	';

	wp_add_inline_style( 'style', sprintf( $css, $link_button_color ) );
}

/**
 * Consider empty post queries as 404s.
 *
 * @param   array  $classes  Array of body classes.
 * @return  array            Array of body classes.
 * @since   1.0.0
 */
function adoration_empty_queries_as_404( $classes ) {
	global $wp_query;

	if ( ( ! is_archive() && ! is_home() ) || $wp_query->have_posts() ) {
		return $classes;
	}

	$classes[] = 'error-404';

	return $classes;
}
