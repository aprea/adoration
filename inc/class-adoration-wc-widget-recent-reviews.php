<?php
/**
 * Recent Reviews Widget.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Exit early if we detect that WooCommerce isn't activated.
if ( ! is_woocommerce_activated() ) {
	return;
}

class Adoration_WC_Widget_Recent_Reviews extends WC_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_recent_reviews';
		$this->widget_description = __( 'Display a list of your most recent reviews on your site.', 'adoration' );
		$this->widget_id          = 'woocommerce_recent_reviews_adoration';
		$this->widget_name        = __( 'WooCommerce Recent Reviews', 'adoration' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Recent Reviews', 'adoration' ),
				'label' => __( 'Title', 'adoration' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 10,
				'label' => __( 'Number of reviews to show', 'adoration' )
			)
		);

		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	 public function widget( $args, $instance ) {
		global $comments, $comment;

		// Allow developers to short-circuit this function.
		$pre = apply_filters( 'adoration_pre_recent_reviews', false, $args, $instance );

		if ( false !== $pre ) {
			echo $pre;
			return;
		}

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		$number   = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];
		$comments = get_comments( array( 'number' => $number, 'status' => 'approve', 'post_status' => 'publish', 'post_type' => 'product' ) );

		if ( $comments ) {
			$this->widget_start( $args, $instance );

			echo '<ul class="product_list_widget">';

			foreach ( (array) $comments as $comment ) {

				$_product = wc_get_product( $comment->comment_post_ID );

				$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );

				$rating_html = $_product->get_rating_html( $rating );

				echo '<li><a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">';

				echo $_product->get_image();

				echo '<div class="product-meta">';

				echo '<span class="product-title">' . $_product->get_title() . '</span>';

				echo $rating_html;

				printf( '<span class="reviewer">' . _x( 'by %1$s', 'by comment author', 'adoration' ) . '</span>', get_comment_author() );

				echo '</div></a></li>';
			}

			echo '</ul>';

			$this->widget_end( $args );
		}

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}
