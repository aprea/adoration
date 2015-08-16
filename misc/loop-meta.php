<?php

if ( false === apply_filters( 'adoration_show_loop_meta', true ) ) {
	return;
} ?>

<div <?php hybrid_attr( 'loop-meta' ); ?>>

	<h1 <?php hybrid_attr( 'loop-title' ); ?>><?php hybrid_loop_title(); ?></h1>

	<?php if ( ! is_paged() && $desc = hybrid_get_loop_description() ) : // Check if we're on page/1. ?>

		<div <?php hybrid_attr( 'loop-description' ); ?>>
			<?php if ( is_author() ) :
				$author_bio_avatar_archive_size = apply_filters( 'adoration_author_bio_archive_avatar_size', 144 );
				echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_archive_size ); ?>
			<?php endif; ?>

			<?php echo $desc; ?>
		</div><!-- .loop-description -->

	<?php endif; // End paged check. ?>

</div><!-- .loop-meta -->
