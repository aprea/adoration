<?php
/**
 * Recent Comments Widget.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class Adoration_Widget_Recent_Comments extends Adoration_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'widget_adoration_recent_comments';
		$this->widget_description = __( 'Your site&#8217;s most recent comments.', 'adoration' );
		$this->widget_id          = 'adoration_recent_comments';
		$this->widget_name        = __( 'Adoration Recent Comments', 'adoration' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Recent Comments', 'adoration' ),
				'label' => __( 'Title', 'adoration' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of comments to show', 'adoration' )
			)
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
		$pre = apply_filters( 'adoration_pre_recent_comments', false, $args, $instance );

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

		/**
		 * Filter the arguments for the Recent Comments widget.
		 *
		 * @since  1.0.0
		 * @see    WP_Comment_Query::query() for information on accepted arguments.
		 * @param  array  $comment_args An array of arguments used to retrieve the recent comments.
		 */
		$comments = get_comments( apply_filters( 'adoration_widget_comments_args', array(
			'number'      => $number,
			'status'      => 'approve',
			'post_status' => 'publish',
			'post_type'   => 'post',
			'type'        => 'comment',
		) ) );

		if ( empty( $comments ) ) {
			$this->cache_widget( $args, '' );
			return;
		}

		// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
		$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
		_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

		ob_start();

		$this->widget_start( $args, $instance ); ?>

		<ul>
			<?php foreach ( (array) $comments as $comment ) : ?>
				<li>
					<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
						<div class="image-wrap">
							<?php echo get_avatar( $comment, 144 ); ?>
						</div>

						<div class="meta">
							<span class="comment-author link"><?php comment_author(); ?></span>
							<span class="comment-date"><?php comment_date(); ?></span>
							<span class="comment-text"><?php echo $this->comment_text( get_comment_text() ); ?></span>
						</div>
					</a>
				</li>
			<?php endforeach; ?>
		</ul><?php

		$this->widget_end( $args );

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}

	/**
	 * [comment_text description]
	 *
	 * @since   1.0.0
	 * @param   string  $comment_text  Raw comment text.
	 * @return  string                 Formatted comment text;
	 */
	private function comment_text( $comment_text ) {

		// Allow developers to short-circuit this function.
		$pre = apply_filters( 'adoration_pre_recent_comments_comment_text', false, $comment_text );

		if ( false !== $pre ) {
			return $pre;
		}

		$comment_text_length   = apply_filters( 'adoration_recent_comments_comment_text_length', 45 );
		$comment_text_ellipsis = apply_filters( 'adoration_recent_comments_comment_text_ellipsis', '&hellip;' );

		if ( strlen( $comment_text ) <= $comment_text_length ) {
			return esc_html( $comment_text );
		}

		$comment_text = wp_strip_all_tags( $comment_text, true );
		$comment_text = substr( $comment_text, 0, $comment_text_length );

		return esc_html( $comment_text . $comment_text_ellipsis );
	}
}
