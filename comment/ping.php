<li <?php hybrid_attr( 'comment' ); ?>>

	<article id="div-comment-<?php comment_ID(); ?>">
		<header class="comment-meta">
			<?php echo get_avatar( $comment, 140 ); ?>
			<cite <?php hybrid_attr( 'comment-author' ); ?>><?php comment_author_link(); ?></cite>
			<a <?php hybrid_attr( 'comment-permalink' ); ?>><time <?php hybrid_attr( 'comment-published' ); ?>><?php printf( __( '%s ago', 'adoration' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ); ?></time></a>
		</header><!-- .comment-meta -->
	</article>

<?php /* No closing </li> is needed.  WordPress will know where to add it. */ ?>
