<?php
/**
 * The template used for displaying the error content inside of the loop.
 *
 * @package Adoration
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit; ?>

<?php if ( ! is_search() ) : ?>
<header class="entry-header">
	<h1 class="entry-title"><?php _e( 'Nothing Found', 'adoration' ); ?></h1>
</header><!-- .entry-header -->
<?php endif; ?>

<div <?php hybrid_attr( 'entry-content' ); ?>>
	<?php if ( is_search() ) : ?>
		<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'adoration' ); ?></p>
	<?php else : ?>
		<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'adoration' ); ?></p>
	<?php endif; ?>

	<?php get_search_form(); ?>
</div><!-- .entry-content -->
