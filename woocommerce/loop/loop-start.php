<?php
/**
 * Product Loop Start.
 *
 * This template was overridden to add the wrapper containing the column number class. e.g. columns-3
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

global $woocommerce_loop;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 3 );
} ?>

<div class="woocommerce <?php printf( 'columns-%s', esc_attr( $woocommerce_loop['columns'] ) ); ?>">
	<ul class="products">
