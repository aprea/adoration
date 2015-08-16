<?php
/**
 * Abstract Widget Class.
 *
 * @package    Adoration
 * @author     Chris Aprea <chris@wpaxl.com>
 * @copyright  Copyright 2014, Chris Aprea
 * @link       http://wpaxl.com/themes/adoration
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

abstract class Adoration_Widget extends WP_Widget {

	/**
	 * CSS class
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	public $widget_cssclass;

	/**
	 * Widget description
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	public $widget_description;

	/**
	 * Widget ID
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	public $widget_id;

	/**
	 * Widget name
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	public $widget_name;

	/**
	 * Settings
	 *
	 * @since  1.0.0
	 * @var    array
	 */
	public $settings;

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		$this->WP_Widget( $this->widget_id, $this->widget_name, $widget_ops );

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	/**
	 * get_cached_widget function.
	 */
	public function get_cached_widget( $args ) {

		$cache = wp_cache_get( apply_filters( 'adoration_cached_widget_id', $this->widget_id ), 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget.
	 *
	 * @since   1.0.0
	 * @param   array   $args
	 * @param   string  $content
	 * @return  string  The content that was cached.
	 */
	public function cache_widget( $args, $content ) {
		wp_cache_set( apply_filters( 'adoration_cached_widget_id', $this->widget_id ), array( $args['widget_id'] => $content ), 'widget' );

		return $content;
	}

	/**
	 * Flush the cache.
	 *
	 * @since  1.0.0
	 */
	public function flush_widget_cache() {
		wp_cache_delete( apply_filters( 'adoration_cached_widget_id', $this->widget_id ), 'widget' );
	}

	/**
	 * Output the html at the start of a widget.
	 *
	 * @since   1.0.0
	 * @param   array   $args
	 * @param   array   $instance
	 * @return  string
	 */
	public function widget_start( $args, $instance ) {
		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
	}

	/**
	 * Output the html at the end of a widget
	 *
	 * @since   1.0.0
	 * @param   array   $args
	 * @return  string
	 */
	public function widget_end( $args ) {
		echo $args['after_widget'];
	}

	/**
	 * Update function.
	 *
	 * @since   1.0.0
	 * @see     WP_Widget->update
	 * @param   array  $new_instance
	 * @param   array  $old_instance
	 * @return  array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		if ( ! $this->settings ) {
			return $instance;
		}

		foreach ( $this->settings as $key => $setting ) {

			if ( isset( $new_instance[ $key ] ) ) {
				$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
			} elseif ( 'checkbox' === $setting['type'] ) {
				$instance[ $key ] = 0;
			}
		}

		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * Form function.
	 *
	 * @since  1.0.0
	 * @see    WP_Widget->form
	 * @param  array  $instance
	 */
	public function form( $instance ) {

		if ( ! $this->settings ) {
			return;
		}

		foreach ( $this->settings as $key => $setting ) {

			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

			switch ( $setting['type'] ) {

				case 'text' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;

				case 'number' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;

				case 'select' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
							<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
				break;

				case 'checkbox' :
					?>
					<p>
						<input id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> />
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
					</p>
					<?php
				break;
			}
		}
	}
}
