<?php
/**
 * Review Comments Template
 *
 * This template was overridden to change several HTML elements and attributes.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) ); ?>

<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

	<article id="comment-<?php comment_ID(); ?>" class="comment-container">

		<header class="comment-meta">
			<span class="avatar-wrap">
				<?php echo get_avatar( $comment, apply_filters( 'woocommerce_review_gravatar_size', '60' ), '', get_comment_author_email( $comment->comment_ID ) );
				if ( get_option( 'woocommerce_review_rating_verification_label' ) === 'yes' && wc_customer_bought_product( $comment->comment_author_email, $comment->user_id, $comment->comment_post_ID ) ) {
					echo '<span class="verified" title="' . __( 'Verified Owner', 'adoration' ) . '">' . __( 'Verified Owner', 'adoration' ) . '</span>';
				} ?>
			</span>

			<cite <?php hybrid_attr( 'comment-author' ); ?>><?php comment_author(); ?></cite>

			<?php if ( $rating && get_option( 'woocommerce_enable_review_rating' ) == 'yes' ) : ?>
				<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( __( 'Rated %d out of 5', 'adoration' ), $rating ) ?>">
					<span style="width:<?php echo ( $rating / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'adoration' ); ?></span>
				</div>
			<?php endif; ?>
		</header>

		<?php if ( '0' == $comment->comment_approved ) : ?>
			<p class="comment-awaiting-moderation"><?php _e( 'Your review is awaiting moderation.', 'adoration' ); ?></p>
		<?php endif; ?>

		<div itemprop="description" class="description comment-content">
			<?php comment_text(); ?>
		</div><!-- .comment-content -->

		<a <?php hybrid_attr( 'comment-permalink' ); ?>><time <?php hybrid_attr( 'comment-published' ); ?>><?php printf( __( '%s ago', 'adoration' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ); ?></time></a>

	</article><?php
