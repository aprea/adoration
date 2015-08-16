<?php

if ( ! is_home() && ! is_archive() && ! is_search() ) {
	return;
}

the_posts_pagination(
	array(
		'prev_text'          => __( 'Previous page', 'adoration' ),
		'next_text'          => __( 'Next page', 'adoration' ),
		'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'adoration' ) . ' </span>',
	)
);