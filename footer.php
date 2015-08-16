<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Adoration
 */
?>

				</main><!-- #content -->

				<?php hybrid_get_sidebar( 'primary' ); // Loads the sidebar/primary.php template. ?>

			</div><!-- .content-wrap -->

		</div><!-- .wrap -->

	</div><!-- #.site-content -->

	<?php do_action( 'adoration_after_site_content' ); ?>

	<footer <?php hybrid_attr( 'footer' ); ?>>

		<div class="footer-sidebar">
			<?php hybrid_get_sidebar( 'subsidiary' ); // Loads the sidebar/subsidiary.php template. ?>
		</div>

		<div class="footer-end">
			<div class="credit">
				<?php printf(
					/* Translators: 1 is current year, 2 is site name/link, 3 is WordPress name/link, and 4 is theme name/link. */
					__( 'Copyright &#169; %1$s %2$s. Powered by %3$s and %4$s.', 'adoration' ),
					date_i18n( 'Y' ),
					hybrid_get_site_link(),
					hybrid_get_wp_link(),
					hybrid_get_theme_link()
				); ?>
			</div><!-- .credit -->

			<?php
			$args = array(
				'theme_location'  => 'footer',
				'fallback_cb'     => false,
				'container'       => 'nav',
				'container_class' => 'footer-nav',
				'depth'           => 1,
			);

			wp_nav_menu( apply_filters( 'adoration_footer_nav_menu_args', $args ) ); ?>
		</div>

	</footer><!-- #footer -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
