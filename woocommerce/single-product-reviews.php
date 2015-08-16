<?php
/**
 * Display single product reviews (comments)
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

global $product;

if ( ! comments_open() ) {
	return;
} ?>

<div id="reviews">
	<div id="comments">

		<?php if ( have_comments() ) : ?>

			<ol class="commentlist">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
			</ol>

			<?php adoration_comments_nav(); ?>

		<?php else : ?>

			<p class="woocommerce-noreviews"><?php _e( 'There are no reviews yet.', 'adoration' ); ?></p>

		<?php endif; ?>
	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->id ) ) : ?>

		<div id="review_form_wrapper">
			<div id="review_form">
				<?php
					$commenter = wp_get_current_commenter();

					$comment_form = array(
						'title_reply'          => __( 'Add a review', 'adoration' ),
						'title_reply_to'       => __( 'Leave a Reply to %s', 'adoration' ),
						'comment_notes_before' => '',
						'comment_notes_after'  => '',
						'fields'               => array(
							'author' => '<input id="author" class="comment-form-author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" placeholder="' . _x( 'Name *', 'Form placeholder.', 'adoration' ) . '" aria-required="true" />',
							'email'  => '<input id="email" class="comment-form-email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_url'] ) . '" placeholder="' . _x( 'Email *', 'Form placeholder.', 'adoration' ) . '" aria-required="true" />',
						),
						'label_submit'  => __( 'Submit', 'adoration' ),
						'logged_in_as'  => '',
						'comment_field' => ''
					);

					$comment_form['comment_field'] .= '<textarea id="comment" class="comment-form-comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="' . _x( 'Your review...', 'Comment textarea placeholder.', 'adoration' ) . '"></textarea>';

					comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		</div>

	<?php else : ?>

		<p class="woocommerce-verification-required"><?php _e( 'Only logged in customers who have purchased this product may leave a review.', 'adoration' ); ?></p>

	<?php endif; ?>

	<div class="clear"></div>
</div>
