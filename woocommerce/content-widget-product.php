<?php
/**
 * Product widget content
 *
 * This template was overridden to rearrange the markup used to display the product.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

global $product; ?>

<li>
	<a href="<?php echo esc_url( get_permalink( $product->id ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>">
		<?php echo $product->get_image(); ?>
		<div class="product-meta">
			<span class="product-title"><?php echo $product->get_title(); ?></span>
			<?php if ( ! empty( $show_rating ) ) echo $product->get_rating_html(); ?>
			<span class="product-price"><?php echo $product->get_price_html(); ?></span>
		</div>
	</a>
</li>
