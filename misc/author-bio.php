<?php
/**
 * The template for displaying Author bios
 *
 * @package Adoration
 */
?>

<div class="author-info">
	<div class="author-description">
		<h3 class="author-title">
			<?php the_author(); ?>
		</h3>

		<p class="author-bio">
			<?php the_author_meta( 'description' ); ?>
			<a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
				<?php printf( __( 'Posts by %s', 'adoration' ), get_the_author() ); ?>
			</a>
		</p><!-- .author-bio -->
	</div><!-- .author-description -->

	<div class="author-avatar">
		<?php
		$author_bio_avatar_size = apply_filters( 'adoration_author_bio_avatar_size', 230 );
		echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_size );
		?>
	</div><!-- .author-avatar -->
</div><!-- .author-info -->
