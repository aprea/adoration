<?php
/**
 * Defines various general purpose template tags used in different templates.
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
 * Displays the title on single posts, pages, CPTs. Must be used within the loop.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_title() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_title', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	$title = get_the_title();

	// $title is empty, exit early
	if ( 0 === strlen( $title ) ) {
		return;
	}

	ob_start(); ?>

	<header class="entry-header">
		<?php printf( '<h1 %s>%s</h1>', hybrid_get_attr( 'entry-title' ), $title ); ?>
	</header><?php

	$title = ob_get_clean();

	echo apply_filters( 'adoration_title', $title );
}

/**
 * Displays the comments template if enabled. Must be used within the loop.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_comments() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_comments', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	$comments_enabled = ( comments_open() || '0' != get_comments_number() ) && is_singular();

	if ( apply_filters( 'adoration_comments_enabled', $comments_enabled ) ) {
		comments_template();
	}
}

/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_paging_nav() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_paging_nav', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	} ?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', '_s' ); ?></h1>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', '_s' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', '_s' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}

/**
 *  Display navigation to next/previous post when applicable.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_post_nav() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_post_nav', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	} ?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', '_s' ); ?></h1>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', _x( '<span class="meta-nav">&larr;</span>&nbsp;%title', 'Previous post link', '_s' ) );
				next_post_link(     '<div class="nav-next">%link</div>',     _x( '%title&nbsp;<span class="meta-nav">&rarr;</span>', 'Next post link',     '_s' ) );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}

/**
 * Displays the posted on date.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_posted_on() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_posted_on', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		_x( 'Posted on %s', 'post date', '_s' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	$byline = sprintf(
		_x( 'by %s', 'post author', '_s' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>';
}

/**
 * Prints HTML with meta information for the categories, tags and comments.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_entry_footer() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_entry_footer', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	// Hide category and tag text for pages.
	if ( 'post' == get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( __( ', ', '_s' ) );
		if ( $categories_list && adoration_categorized_blog() ) {
			printf( '<span class="cat-links">' . __( 'Posted in %1$s', '_s' ) . '</span>', $categories_list );
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', __( ', ', '_s' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . __( 'Tagged %1$s', '_s' ) . '</span>', $tags_list );
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( __( 'Leave a comment', '_s' ), __( '1 Comment', '_s' ), __( '% Comments', '_s' ) );
		echo '</span>';
	}

	edit_post_link( __( 'Edit', '_s' ), '<span class="edit-link">', '</span>' );
}

/**
 * Returns true if a blog has more than 1 category.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function adoration_categorized_blog() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_categorized_blog', null );

	if ( null !== $pre ) {
		return $pre;
	}

	if ( false === ( $all_the_cool_cats = get_transient( 'adoration_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'adoration_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so adoration_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so adoration_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in adoration_categorized_blog.
 */
function adoration_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'adoration_categories' );
}
add_action( 'edit_category', 'adoration_category_transient_flusher' );
add_action( 'save_post',     'adoration_category_transient_flusher' );

/**
 * Display Product Categories.
 * Hooked into the `homepage` action in the homepage template.
 *
 * @since  1.0.0
 */
function adoration_homepage_product_categories( $args ) {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_homepage_product_categories', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( ! is_woocommerce_activated() ) {
		return;
	}

	$args = array(
		'number'           => 6,
		'columns'          => 3,
		'child_categories' => 0,
		'orderby'          => 'name',
		'title'            => __( 'Product Categories', 'adoration' ),
	);

	// If using a 1 column layout increase the product columns to fill out the available space.
	if ( adoration_is_1c_wide_layout() ) {
		$args['columns']  = 4;
		$args['number']   = 8;
	}

	$content = adoration_product_categories( $args );

	adoration_homepage_section( $content, $args['title'] );
}

/**
 * Display Recent Products.
 * Hooked into the `homepage` action in the homepage template.
 *
 * @since  1.0.0
 */
function adoration_homepage_recent_products( $args ) {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_homepage_recent_products', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( ! is_woocommerce_activated() ) {
		return;
	}

	$args = array(
		'per_page' => 6,
		'columns'  => 3,
		'title'    => __( 'Recent Products', 'adoration' ),
	);

	// If using a 1 column layout increase the product columns to fill out the available space.
	if ( adoration_is_1c_wide_layout() ) {
		$args['columns']  = 4;
		$args['per_page'] = 8;
	}

	$content = adoration_recent_products( $args );

	adoration_homepage_section( $content, $args['title'] );
}

/**
 * Display Featured Products.
 * Hooked into the `homepage` action in the homepage template.
 *
 * @since  1.0.0
 */
function adoration_homepage_featured_products( $args ) {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_homepage_featured_products', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( ! is_woocommerce_activated() ) {
		return;
	}

	$args = array(
		'per_page' => 6,
		'columns'  => 3,
		'title'    => __( 'Featured Products', 'adoration' ),
	);

	// If using a 1 column layout increase the product columns to fill out the available space.
	if ( adoration_is_1c_wide_layout() ) {
		$args['columns']  = 4;
		$args['per_page'] = 8;
	}

	$content = adoration_featured_products( $args );

	adoration_homepage_section( $content, $args['title'] );
}

/**
 * Display Popular Products.
 * Hooked into the `homepage` action in the homepage template.
 *
 * @since  1.0.0
 */
function adoration_homepage_popular_products( $args ) {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_homepage_popular_products', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( ! is_woocommerce_activated() ) {
		return;
	}

	$args = array(
		'per_page' => 6,
		'columns'  => 3,
		'title'    => __( 'Popular Products', 'adoration' ),
	);

	// If using a 1 column layout increase the product columns to fill out the available space.
	if ( adoration_is_1c_wide_layout() ) {
		$args['columns']  = 4;
		$args['per_page'] = 8;
	}

	$content = adoration_best_selling_products( $args );

	adoration_homepage_section( $content, $args['title'] );
}

/**
 * Display Top Rated Products.
 * Hooked into the `homepage` action in the homepage template.
 *
 * @since  1.0.0
 */
function adoration_homepage_top_rated_products( $args ) {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_homepage_top_rated_products', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( ! is_woocommerce_activated() ) {
		return;
	}

	$args = array(
		'per_page' => 6,
		'columns'  => 3,
		'title'    => __( 'Top Rated Products', 'adoration' ),
	);

	// If using a 1 column layout increase the product columns to fill out the available space.
	if ( adoration_is_1c_wide_layout() ) {
		$args['columns']  = 4;
		$args['per_page'] = 8;
	}

	$content = adoration_top_rated_products( $args );

	adoration_homepage_section( $content, $args['title'] );
}

/**
 * Display On Sale Products.
 * Hooked into the `homepage` action in the homepage template.
 *
 * @since  1.0.0
 */
function adoration_homepage_on_sale_products( $args ) {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_homepage_on_sale_products', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( ! is_woocommerce_activated() ) {
		return;
	}

	$args = array(
		'per_page' => 6,
		'columns'  => 3,
		'title'    => __( 'On Sale', 'adoration' ),
	);

	// If using a 1 column layout increase the product columns to fill out the available space.
	if ( adoration_is_1c_wide_layout() ) {
		$args['columns']  = 4;
		$args['per_page'] = 8;
	}

	$content = adoration_sale_products( $args );

	adoration_homepage_section( $content, $args['title'] );
}

/**
 * Display homepage content.
 * Hooked into the `homepage` action in the homepage template.
 *
 * @since  1.0.0
 */
function adoration_homepage_content() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_homepage_content', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	while ( have_posts() ) : the_post();

		the_content();

	endwhile; // end of the loop.
}

/**
 * Displays the various different homepage sections with a title if applicable.
 *
 * @since  1.0.0
 * @param  string  $content  Section content.
 * @param  string  $title    Section title.
 */
function adoration_homepage_section( $content, $title ) {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_homepage_section', false, $content, $title );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( empty( $content ) ) {
		return;
	} ?>

	<section class="adoration-product-section">
		<?php if ( ! empty( $title ) ) : ?>
		<h2 class="section-title"><?php echo $title; ?></h2>
		<?php endif; ?>

		<?php echo $content; ?>
	</section><?php
}

/**
 * Start wrapper for the entry byline.
 *
 * @since  1.0.0
 */
function adoration_entry_byline_content_wrapper() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_entry_byline_content_wrapper', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	} ?>

	<div class="entry-byline"><?php
}

/**
 * Display the author post link for the current post.
 *
 * @since  1.0.0
 */
function adoration_entry_byline_author() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_entry_byline_author', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	} ?>

	<section class="byline-author">
		<span <?php hybrid_attr( 'entry-author' ); ?>>
			<?php the_author_posts_link(); ?>
		</span>
	</section><?php
}

/**
 * Display the human readable/traditional date for the current post.
 *
 * @since  1.0.0
 */
function adoration_entry_byline_date() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_entry_byline_date', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	$human_readable_template = _x( '%s ago', 'e.g. 3 days ago', 'adoration' );
	$human_readable          = sprintf( $human_readable_template, human_time_diff( get_the_time( 'U' ) ) ); ?>

	<section class="byline-date">
		<time <?php hybrid_attr( 'entry-published' ); ?> data-display-toggle="<?php echo get_the_date(); ?>">
			<?php echo $human_readable; ?>
		</time>
	</section><?php
}

/**
 * Display the comments popup link for the current post.
 *
 * @since  1.0.0
 */
function adoration_entry_byline_comments() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_entry_byline_comments', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( is_single() ) {
		ob_start();
		comments_popup_link();
		$comments = ob_get_clean();
	} else {
		$comments = get_comments_number();
	} ?>

	<section class="byline-comments">
		<?php echo $comments; ?>
	</section><?php
}

/**
 * Display the entry views for the current post.
 * Only applicable when the Entry Views plugin by Justin Tadlock is installed and activated.
 *
 * @since  1.0.0
 */
function adoration_entry_byline_entry_views() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_entry_byline_entry_views', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( ! function_exists( 'ev_post_views' ) )	{
		return;
	} ?>

	<section class="byline-entry-views">
		<?php ev_post_views(); ?>
	</section><?php
}

/**
 * End wrapper for the entry byline.
 *
 * @since  1.0.0
 */
function adoration_entry_byline_content_wrapper_end() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_entry_byline_content_wrapper_end', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	} ?>

	</div><!-- .entry-byline --><?php
}

/**
 * Singular post template.
 *
 * @since  1.0.0
 */
function adoration_singular_content() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_singular_content', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	} ?>

	<article <?php hybrid_attr( 'post' ); ?>>

		<header class="entry-header">
			<h1 <?php hybrid_attr( 'entry-title' ); ?>><?php single_post_title(); ?></h1>
			<?php do_action( 'adoration_entry_byline' ); ?>
		</header><!-- .entry-header -->

		<?php get_the_image(
			array(
				'size'         => 'full',
				'link_to_post' => false,
				'before'       => '<div class="featured-image">',
				'after'        => '</div>',
				'attachment'   => false,
			) );
		?>

		<div <?php hybrid_attr( 'entry-content' ); ?>>
			<?php the_content(); ?>
			<?php wp_link_pages(); ?>
		</div><!-- .entry-content -->

		<footer class="entry-footer">
			<?php if ( has_tag() || has_category() ) : ?>
				<div class="entry-terms-wrap">
					<?php hybrid_post_terms( array( 'taxonomy' => 'category', 'text' => __( '<span class="screen-reader-text">Categorized</span> %s', 'adoration' ) ) ); ?>
					<?php hybrid_post_terms( array( 'taxonomy' => 'post_tag', 'text' => __( '<span class="screen-reader-text">Tagged</span> %s', 'adoration' ) ) ); ?>
				</div>
			<?php endif; ?>

			<?php
				// Author bio.
				if ( is_single() && get_the_author_meta( 'description' ) ) :
					get_template_part( 'misc/author-bio' );
				endif;
			?>
		</footer><!-- .entry-footer -->

	</article><!-- .entry --><?php
}

/**
 * Featured image callback when all other methods fail to retrieve an appropriate image.
 *
 * @since  1.0.0
 */
function adoration_no_featured_image() {
	global $wp_query;

	$current_post  = $wp_query->current_post;
	$featured_post = ( 0 === $current_post && ! is_paged() && is_home() );

	if ( $featured_post ) {
		return;
	} ?>

	<div class="entry-image">
		<a href="<?php the_permalink(); ?>" class="featured-image placeholder" rel="bookmark" itemprop="url"><?php the_title(); ?></a>
	</div>
	<?php
}

/**
 * Posts archive template.
 *
 * @since  1.0.0
 */
function adoration_archive_content() {
	//return;
	global $wp_query;

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_archive_content', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	$current_post  = $wp_query->current_post;
	$featured_post = ( 0 === $current_post && ! is_paged() && is_home() );
	$thumb_size    = ( $featured_post ) ? 'full' : 'adoration-post-thumb'; ?>

	<?php if ( $featured_post ) : ?>
	<div class="featured-post-wrap">
	<?php endif; ?>

	<article <?php hybrid_attr( 'post' ); ?>>
		<?php get_the_image(
			array(
				'size'         => $thumb_size,
				'link_to_post' => false,
				'before'       => '<div class="entry-image"><a href="' . get_permalink() . '" class="featured-image" rel="bookmark" itemprop="url">',
				'after'        => '</a></div>',
				'attachment'   => false,
				'callback'     => 'adoration_no_featured_image',
			) );
		?>

		<div class="entry-content">
			<?php the_title( '<header class="entry-header"><h2 ' . hybrid_get_attr( 'entry-title' ) . '><a href="' . get_permalink() . '" rel="bookmark" itemprop="url">', '</a></h2></header>' ); ?>
			<?php do_action( 'adoration_entry_byline' ); ?>

			<div <?php hybrid_attr( 'entry-summary' ); ?>>
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->
		</div>
	</article>

	<?php if ( $featured_post ) : ?>
	</div>
	<?php endif;
}
