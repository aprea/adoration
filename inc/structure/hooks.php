<?php
/**
 * Typical WordPress themes hardcode their HTML structural elements directly into their themes.
 *
 * Adoration was build from the ground up to allow maximum flexibility by instead defining
 * a number of actions throughout its templates.
 *
 * Functions are hooked into these actions and output the required HTML.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Homepage.
add_action( 'homepage', 'adoration_homepage_content',            10 );
add_action( 'homepage', 'adoration_homepage_product_categories', 20 );
add_action( 'homepage', 'adoration_homepage_recent_products',    30 );
add_action( 'homepage', 'adoration_homepage_featured_products',  40 );
add_action( 'homepage', 'adoration_homepage_popular_products',   50 );
add_action( 'homepage', 'adoration_homepage_top_rated_products', 60 );
add_action( 'homepage', 'adoration_homepage_on_sale_products',   70 );

// Entry byline.
add_action( 'adoration_entry_byline', 'adoration_entry_byline_content_wrapper',     10 );
add_action( 'adoration_entry_byline', 'adoration_entry_byline_author',              20 );
add_action( 'adoration_entry_byline', 'adoration_entry_byline_date',                30 );
add_action( 'adoration_entry_byline', 'adoration_entry_byline_comments',            40 );
add_action( 'adoration_entry_byline', 'adoration_entry_byline_entry_views',         50 );
add_action( 'adoration_entry_byline', 'adoration_entry_byline_content_wrapper_end', 60 );

// Comments.
add_action( 'adoration_before_comments', 'adoration_before_comments_wrapper', 10 );

add_action( 'adoration_comments', 'adoration_comments_title', 10 );
add_action( 'adoration_comments', 'adoration_comments_list',  20 );
add_action( 'adoration_comments', 'adoration_comments_nav',   30 );

add_action( 'adoration_after_comments', 'adoration_after_comments_closed',       10 );
add_action( 'adoration_after_comments', 'adoration_after_comments_comment_form', 20 );
add_action( 'adoration_after_comments', 'adoration_after_comments_wrapper_end',  30 );
