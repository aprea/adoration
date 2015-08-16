<?php
/**
 * Cart item data (when outputting non-flat)
 *
 * This template was overridden to replace WooCommerce's default behaviour of using definition lists
 * for displaying variation information with standard HTML tables.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit; ?>

<table class="variation">
		<tbody>
		<?php foreach ( $item_data as $data ) :
				$key = sanitize_text_field( $data['key'] ); ?>
				<tr class="variation-<?php echo sanitize_html_class( $key ); ?>">
					<td class="variation-key"><?php echo wp_kses_post( $data['key'] ); ?></td>
					<td class="variation-value"><?php echo wp_kses_post( wpautop( $data['value'] ) ); ?></td>
				</tr>
		<?php endforeach; ?>
	</tbody>
</table>
