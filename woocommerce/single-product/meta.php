<?php
/**
 * Single Product Meta
 *
 * This template was overridden to organise the meta information into a HTML table.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

global $post, $product;

$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) ); ?>

<table class="product_meta">

	<tbody>

		<?php do_action( 'woocommerce_product_meta_start' ); ?>

		<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

			<tr class="sku_wrapper"><th><?php _e( 'SKU', 'adoration' ); ?></th><td class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'adoration' ); ?></td></tr>

		<?php endif; ?>

		<?php echo $product->get_categories( ', ', '<tr class="posted_in"><th>' . _n( 'Category', 'Categories', $cat_count, 'adoration' ) . '</th><td>', '</td></tr>' ); ?>

		<?php echo $product->get_tags( ', ', '<tr class="tagged_as"><th>' . _n( 'Tag', 'Tags', $tag_count, 'adoration' ) . '</th><td>', '</td></tr>' ); ?>

		<?php do_action( 'woocommerce_product_meta_end' ); ?>

	</tbody>

</table>
