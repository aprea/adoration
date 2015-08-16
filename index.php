<?php
/**
 * The main template file.
 *
 * @package Adoration
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

hybrid_get_header(); // Loads the header.php template. ?>

    <?php do_action( 'adoration_before_article' ); ?>

	<?php if ( ! is_front_page() && ! is_singular() && ! is_404() ) : // If viewing a multi-post page ?>

		<?php locate_template( array( 'misc/loop-meta.php' ), true ); // Loads the misc/loop-meta.php template. ?>

	<?php endif; // End check for multi-post page. ?>

	<?php if ( have_posts() ) : // Checks if any posts were found. ?>

		<?php do_action( 'adoration_before_posts_loop' ); ?>

		<?php while ( have_posts() ) : // Begins the loop through found posts. ?>

			<?php the_post(); // Loads the post data. ?>

			<?php hybrid_get_content_template(); // Loads the content/*.php template. ?>

			<?php adoration_comments(); // Loads the comments.php template. ?>

		<?php endwhile; // End found posts loop. ?>

		<?php do_action( 'adoration_after_posts_loop' ); ?>

		<?php locate_template( array( 'misc/loop-nav.php' ), true ); // Loads the misc/loop-nav.php template. ?>

	<?php else : // If no posts were found. ?>

		<?php locate_template( array( 'content/error.php' ), true ); // Loads the content/error.php template. ?>

	<?php endif; // End check for posts. ?>

<?php hybrid_get_footer(); // Loads the footer.php template. ?>
