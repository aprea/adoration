<?php if ( is_active_sidebar( 'subsidiary' ) ) : // If the sidebar has widgets. ?>

	<?php do_action( 'adoration_before_subsidiary_sidebar' ); ?>

	<h3 id="sidebar-subsidiary-title" class="screen-reader-text"><?php
		/* Translators: %s is the sidebar name. This is the sidebar title shown to screen readers. */
		printf( _x( '%s Sidebar', 'Sidebar title.', 'adoration' ), hybrid_get_sidebar_name( 'subsidiary' ) );
	?></h3>

	<aside <?php hybrid_attr( 'sidebar', 'subsidiary' ); ?>>

		<?php dynamic_sidebar( 'subsidiary' ); // Displays the subsidiary sidebar. ?>

	</aside><!-- #sidebar-subsidiary -->

	<?php do_action( 'adoration_after_subsidiary_sidebar' ); ?>

<?php endif; // End widgets check. ?>
