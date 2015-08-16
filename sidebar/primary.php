<?php if ( adoration_is_1c_layout() && ! is_customize_preview() ) { return; } // Do no show the sidebar on 1 col layouts ?>

<?php do_action( 'adoration_before_primary_sidebar' ); ?>

<h3 id="sidebar-primary-title" class="screen-reader-text"><?php
	/* Translators: %s is the sidebar name. This is the sidebar title shown to screen readers. */
	printf( _x( '%s Sidebar', 'Sidebar title.', 'adoration' ), hybrid_get_sidebar_name( 'primary' ) );
?></h3>

<aside <?php hybrid_attr( 'sidebar', 'primary' ); ?>>

	<?php do_action( 'adoration_before_primary_sidebar_widgets' ); ?>

	<?php if ( is_active_sidebar( 'primary' ) ) : // If the sidebar has widgets. ?>

		<?php dynamic_sidebar( 'primary' ); // Displays the primary sidebar. ?>

	<?php else : // If the sidebar has no widgets. ?>

		<?php the_widget(
			'WP_Widget_Text',
			array(
				'title'  => __( 'Example Widget', 'adoration' ),
				/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
				'text'   => sprintf( __( 'This is an example widget to show how the Primary sidebar looks by default. You can add custom widgets from the %swidgets screen%s in the admin.', 'adoration' ), current_user_can( 'edit_theme_options' ) ? '<a href="' . admin_url( 'widgets.php' ) . '">' : '', current_user_can( 'edit_theme_options' ) ? '</a>' : '' ),
				'filter' => true,
			),
			array(
				'before_widget' => '<section class="widget widget_text">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>'
			)
		); ?>

	<?php endif; // End widgets check. ?>

	<?php do_action( 'adoration_after_primary_sidebar_widgets' ); ?>

</aside><!-- #sidebar-primary -->

<?php do_action( 'adoration_after_primary_sidebar' ); ?>
