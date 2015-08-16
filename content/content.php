<?php
/**
 * Generic content template.
 *
 * @package Adoration
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( is_singular( get_post_type() ) ) { // If viewing a single post.
	adoration_singular_content();
} else { // If viewing a post archive.
	adoration_archive_content();
}
