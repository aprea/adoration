<li <?php hybrid_attr( 'comment' ); ?>>

	<article id="div-comment-<?php comment_ID(); ?>">
		<header class="comment-meta">
			<span class="avatar-wrap"><?php echo get_avatar( $comment, 140 ); ?></span>
			<cite <?php hybrid_attr( 'comment-author' ); ?>><?php comment_author_link(); ?></cite>
			<a <?php hybrid_attr( 'comment-permalink' ); ?>><time <?php hybrid_attr( 'comment-published' ); ?>><?php printf( __( '%s ago', 'adoration' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ); ?></time></a>
		</header><!-- .comment-meta -->

		<?php if ( '0' == $comment->comment_approved ) : ?>
			<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'adoration' ); ?></p>
		<?php endif; ?>

		<div <?php hybrid_attr( 'comment-content' ); ?>>
			<?php comment_text(); ?>
		</div><!-- .comment-content -->

		<?php hybrid_comment_reply_link( array( 'add_below' => 'div-comment' ) ); ?>
	</article>

<?php /* No closing </li> is needed.  WordPress will know where to add it. */ ?>
