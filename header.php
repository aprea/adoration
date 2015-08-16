<?php
/**
 * The header for our theme.
 *
 * @package Adoration
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
<?php wp_head(); // Hook required for scripts, styles, and other <head> items. ?>
</head>

<body <?php hybrid_attr( 'body' ); ?>>

<div id="page" class="hfeed site">

	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'adoration' ); ?></a>

	<?php if ( ! apply_filters( 'adoration_disable_subsidiary_banner', false ) ) : ?>
		<div id="subsidiary-banner" class="subsidiary-banner">
			<div class="subsidiary-banner-wrap">
				<?php do_action( 'adoration_subsidiary_banner' ); ?>
			</div>
		</div>
	<?php endif; ?>

	<header <?php hybrid_attr( 'header' ); ?>>
		<div class="primary-header-outer-wrap">
			<div class="primary-header-inner-wrap">
				<?php do_action( 'adoration_masthead' ); ?>
			</div>
		</div>
	</header><!-- #masthead -->

	<?php do_action( 'adoration_before_site_content' ); ?>

	<div class="site-content">

		<div class="wrap">

			<?php do_action( 'adoration_breadcrumbs' ); ?>

			<div class="content-wrap">

				<main <?php hybrid_attr( 'content' ); ?>>
