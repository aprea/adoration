<?php
/**
 * Recent Posts Widget.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class Adoration_Widget_Recent_Posts extends Adoration_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'widget_adoration_recent_entries';
		$this->widget_description = __( 'Your site&#8217;s most recent Posts.', 'adoration' );
		$this->widget_id          = 'adoration_recent_posts';
		$this->widget_name        = __( 'Adoration Recent Posts', 'adoration' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Recent Posts', 'adoration' ),
				'label' => __( 'Title', 'adoration' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of posts to show', 'adoration' )
			),
			'show_date' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Display post date', 'woocommerce' )
			),
		);

		parent::__construct();
	}

	/**
	 * Widget function.
	 *
	 * @since  1.0.0
	 * @see    WP_Widget
	 * @param  array  $args
	 * @param  array  $instance
	 */
	 public function widget( $args, $instance ) {
		global $comments, $comment;

		// Allow developers to short-circuit this function.
		$pre = apply_filters( 'adoration_pre_recent_posts', false, $args, $instance );

		if ( false !== $pre ) {
			echo $pre;
			return;
		}

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;

		if ( ! $number ) {
			$number = 5;
		}

		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		/**
		 * Filter the arguments for the Recent Posts widget.
		 *
		 * @since  1.0.0
		 * @see    WP_Query::get_posts()
		 * @param  array  $args  An array of arguments used to retrieve the recent posts.
		 */
		$r = new WP_Query( apply_filters( 'adoration_widget_posts_args', array(
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true
		) ) );

		if ( ! $r->have_posts() ) {
			$this->cache_widget( $args, '' );
			return;
		}

		ob_start();

		$this->widget_start( $args, $instance ); ?>

		<ul>
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li>
				<a href="<?php the_permalink(); ?>">
					<div class="image-wrap">
					<?php get_the_image(
						array(
							'size'         => 'adoration-widget-thumbnail',
							'link_to_post' => false,
							'attachment'   => false,
						) );
					?>
					</div>

					<div class="meta">
						<?php if ( $show_date ) : ?>
							<span class="post-date"><?php echo get_the_date(); ?></span>
						<?php endif; ?>
						<span class="post-title link"><?php get_the_title() ? the_title() : the_ID(); ?></span>
					</div>
				</a>
			</li>
		<?php endwhile; ?>
		</ul>
		<?php echo $args['after_widget'];

		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		$this->widget_end( $args );

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}
