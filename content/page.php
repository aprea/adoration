<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Adoration
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;
?>

<article <?php hybrid_attr( 'post' ); ?>>

	<?php adoration_title(); ?>

	<div <?php hybrid_attr( 'entry-content' ); ?>>
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'adoration' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->
</article><!-- #post-## -->
