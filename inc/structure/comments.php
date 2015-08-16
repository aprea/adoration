<?php
/**
 * Defines various template tags used for comments.
 *
 * The output can generally be filtered using the adoration_pre_* hooks.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Start wrapper for the comments section.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_before_comments_wrapper() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_before_comments_wrapper', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	} ?>

	<div id="comments" class="comments-area"><?php
}

/**
 * Display the comments title.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_comments_title() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_comments_title', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	} ?>

	<h3 id="comments-number"><?php comments_number(); ?></h3><?php
}

/**
 * Display the comments.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_comments_list() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_comments_list', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	$args = array(
		'style'        => 'ol',
		'short_ping'   => true,
		'avatar_size'  => 144,
		'callback'     => 'hybrid_comments_callback',
		'end-callback' => 'hybrid_comments_end_callback'
	);

	$comment_list_args = apply_filters( 'adoration_comments_list_args', $args ); ?>

	<ol class="comment-list">
		<?php wp_list_comments( $args ); ?>
	</ol><!-- .comment-list --><?php
}

/**
 * Display navigation to next/previous comments when applicable.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_comments_nav() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_comment_nav', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( get_option( 'page_comments' ) && 1 < get_comment_pages_count() ) : // Check for paged comments.

	$current = get_query_var( 'cpage' ) ? absint( get_query_var( 'cpage' ) ) : 1;
	$max     = absint( get_comment_pages_count() );

	$class = 'comment-nav-wrap';

	if ( 1 === $current || $current === $max ) {
		$class .= ' single-button';
	} ?>

	<div class="<?php echo $class; ?>">

		<span class="page-numbers"><?php
			/* Translators: Comments page numbers. 1 is current page and 2 is total pages. */
			printf( __( 'Page %1$s of %2$s', 'adoration' ), $current, $max );
		?></span>

		<nav class="comments-nav" role="navigation" aria-labelledby="comments-nav-title">

			<h3 id="comments-nav-title" class="screen-reader-text"><?php _e( 'Comments Navigation', 'adoration' ); ?></h3>

			<?php previous_comments_link( _x( 'Older comments', 'comments navigation', 'adoration' ) ); ?>

			<?php next_comments_link( _x( 'Newer comments', 'comments navigation', 'adoration' ) ); ?>

		</nav><!-- .comments-nav -->

	</div>

	<?php
	endif; // End check for paged comments.
}

/**
 * Displays a note specifying that the comments are closed if all the following are true:
 * - Comments are closed
 * - Post has comments
 * - Post type supports comments
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_after_comments_closed() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_comments_closed', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	if ( comments_open() ) {
		return;
	}

	if ( ! get_comments_number() ) {
		return;
	}

	if ( ! post_type_supports( get_post_type(), 'comments' ) ) {
		return;
	} ?>

	<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'adoration' ); ?></p><?php
}

/**
 * Displays the comment form.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_after_comments_comment_form() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_after_comments_comment_form', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	}

	$req                 = get_option( 'require_name_email' );
	$author_placeholder  = ( $req ? _x( 'Name *', 'Form placeholder.', 'adoration' ) : _x( 'Name', 'Form placeholder.', 'adoration' ) );
	$email_placeholder   = ( $req ? _x( 'Email *', 'Form placeholder.', 'adoration' ) : _x( 'Email', 'Form placeholder.', 'adoration' ) );
	$website_placeholder = _x( 'Website', 'Form placeholder.', 'adoration' );

	$commenter = wp_get_current_commenter();
	$aria_req  = ( $req ? " aria-required='true'" : '' );
	$fields    =  array(
		'author' => '<input id="author" class="comment-form-author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" placeholder="' . $author_placeholder . '"' . $aria_req . ' />',
		'email'  => '<input id="email" class="comment-form-email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" aria-describedby="email-notes" placeholder="' . $email_placeholder . '"' . $aria_req . ' />',
		'url'    => '<input id="url" class="comment-form-url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" placeholder="' . $website_placeholder . '" />',
	);
	$comment_field = '<textarea id="comment" class="comment-form-comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="' . _x( 'Your comment...', 'Comment textarea placeholder.', 'adoration' ) . '"></textarea>';

	$required_text = sprintf( '<br>' . __( 'Required fields are marked %s', 'adoration' ), '<span class="required">*</span>' );

	$args = array(
		'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . __( 'Your email address will not be published.', 'adoration' ) . '</span>' . ( $req ? $required_text : '' ) . '</p>',
		'comment_notes_after'  => '<p class="form-allowed-tags" id="form-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s', 'adoration' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>',
		'comment_field'        => $comment_field,
		'fields'               => $fields,
		'title_reply'          => __( 'Post Comment', 'adoration' ),
		'logged_in_as'         => '',
	);

	$args = apply_filters( 'adoration_comment_form_args', $args );

	comment_form( $args );
}

/**
 * End wrapper for the comments section.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function adoration_after_comments_wrapper_end() {

	// Allow developers to short-circuit this function.
	$pre = apply_filters( 'adoration_pre_before_comments_wrapper_end', false );

	if ( false !== $pre ) {
		echo $pre;
		return;
	} ?>

	</div><!-- #comments --><?php
}
