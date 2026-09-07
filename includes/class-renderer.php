<?php
/**
 * Unfold for Elementor - Frontend Renderer
 *
 * Handles Elementor render lifecycle and data attribute injection.
 *
 * @package Dynime\UnfoldElementor
 */

namespace Dynime\UnfoldElementor\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Element_Base;

class Renderer {

	/**
	 * Singleton instance.
	 *
	 * @var Renderer|null
	 */
	protected static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Renderer
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'elementor/frontend/before_render', array( $this, 'before_element_render' ), 10, 1 );
	}

	/**
	 * Inject Unfold attributes and classes before element render.
	 *
	 * @param Element_Base $element
	 */
	public function before_element_render( $element ) {
		$settings = $element->get_settings_for_display();

		if ( empty( $settings['unfold_elementor_enable'] ) || 'yes' !== $settings['unfold_elementor_enable'] ) {
			return;
		}

		$element_id = $element->get_id();
		$mode       = ! empty( $settings['unfold_elementor_mode'] ) ? $settings['unfold_elementor_mode'] : 'height';
		$init_open  = ( ! empty( $settings['unfold_elementor_initially_expanded'] ) && 'yes' === $settings['unfold_elementor_initially_expanded'] );

		// Prepare expand / collapse icons HTML
		$expand_icon_html   = '';
		$collapse_icon_html = '';

		if ( ! empty( $settings['unfold_elementor_show_icons'] ) && 'yes' === $settings['unfold_elementor_show_icons'] ) {
			if ( ! empty( $settings['unfold_elementor_expand_icon'] ) ) {
				$expand_icon_html = unfold_elementor_render_icon(
					$settings['unfold_elementor_expand_icon'],
					array(
						'aria-hidden' => 'true',
						'class'       => 'unfold-elementor__icon unfold-elementor__icon--expand',
					)
				);
			}
			if ( ! empty( $settings['unfold_elementor_collapse_icon'] ) ) {
				$collapse_icon_html = unfold_elementor_render_icon(
					$settings['unfold_elementor_collapse_icon'],
					array(
						'aria-hidden' => 'true',
						'class'       => 'unfold-elementor__icon unfold-elementor__icon--collapse',
					)
				);
			}
		}

		// Calculate duration based on preset
		$animation_type = ! empty( $settings['unfold_elementor_animation_type'] ) ? $settings['unfold_elementor_animation_type'] : 'slide_fade';
		$speed_preset   = ! empty( $settings['unfold_elementor_speed_preset'] ) ? $settings['unfold_elementor_speed_preset'] : 'smooth';
		$duration       = 450;

		if ( 'instant' === $animation_type ) {
			$duration = 0;
		} else {
			switch ( $speed_preset ) {
				case 'fast':
					$duration = 250;
					break;
				case 'slow':
					$duration = 800;
					break;
				case 'custom':
					$duration = ! empty( $settings['unfold_elementor_duration'] ) ? intval( $settings['unfold_elementor_duration'] ) : 450;
					break;
				case 'smooth':
				default:
					$duration = 450;
					break;
			}
		}

		// Extract responsive heights (supporting 0px)
		$collapsed_height_desktop = isset( $settings['unfold_elementor_collapsed_height']['size'] ) ? $settings['unfold_elementor_collapsed_height']['size'] : 160;
		$collapsed_height_tablet  = isset( $settings['unfold_elementor_collapsed_height_tablet']['size'] ) ? $settings['unfold_elementor_collapsed_height_tablet']['size'] : $collapsed_height_desktop;
		$collapsed_height_mobile  = isset( $settings['unfold_elementor_collapsed_height_mobile']['size'] ) ? $settings['unfold_elementor_collapsed_height_mobile']['size'] : $collapsed_height_tablet;
		$height_unit              = isset( $settings['unfold_elementor_collapsed_height']['unit'] ) ? $settings['unfold_elementor_collapsed_height']['unit'] : 'px';

		$config = array(
			'id'                 => $element_id,
			'mode'               => $mode,
			'initially_expanded' => $init_open,
			'auto_hide'          => ( ! empty( $settings['unfold_elementor_auto_hide'] ) && 'yes' === $settings['unfold_elementor_auto_hide'] ),
			'height'             => array(
				'desktop' => floatval( $collapsed_height_desktop ),
				'tablet'  => floatval( $collapsed_height_tablet ),
				'mobile'  => floatval( $collapsed_height_mobile ),
				'unit'    => $height_unit,
			),
			'preview_percentage' => isset( $settings['unfold_elementor_preview_percentage']['size'] ) ? floatval( $settings['unfold_elementor_preview_percentage']['size'] ) : 35,
			'animation_type'     => $animation_type,
			'duration'           => $duration,
			'easing'             => ! empty( $settings['unfold_elementor_easing'] ) ? $settings['unfold_elementor_easing'] : 'cubic-bezier(0.25, 1, 0.5, 1)',
			'show_button'        => ( ! empty( $settings['unfold_elementor_show_button'] ) && 'yes' === $settings['unfold_elementor_show_button'] ),
			'button_type'        => ! empty( $settings['unfold_elementor_button_type'] ) ? $settings['unfold_elementor_button_type'] : 'standard',
			'button_position'    => ! empty( $settings['unfold_elementor_button_position'] ) ? $settings['unfold_elementor_button_position'] : 'below',
			'expand_text'        => ! empty( $settings['unfold_elementor_expand_text'] ) ? esc_html( $settings['unfold_elementor_expand_text'] ) : esc_html__( 'Read More', 'unfold-for-elementor' ),
			'collapse_text'      => ! empty( $settings['unfold_elementor_collapse_text'] ) ? esc_html( $settings['unfold_elementor_collapse_text'] ) : esc_html__( 'Read Less', 'unfold-for-elementor' ),
			'show_icons'         => ( ! empty( $settings['unfold_elementor_show_icons'] ) && 'yes' === $settings['unfold_elementor_show_icons'] ),
			'icon_rotate'        => ( ! empty( $settings['unfold_elementor_icon_rotate'] ) && 'yes' === $settings['unfold_elementor_icon_rotate'] ),
			'expand_icon_html'   => $expand_icon_html,
			'collapse_icon_html' => $collapse_icon_html,
			'icon_position'      => ! empty( $settings['unfold_elementor_icon_position'] ) ? $settings['unfold_elementor_icon_position'] : 'after',
			'fade_enable'        => ( ! empty( $settings['unfold_elementor_fade_enable'] ) && 'yes' === $settings['unfold_elementor_fade_enable'] ),
			'overlay_style'      => ! empty( $settings['unfold_elementor_overlay_style'] ) ? $settings['unfold_elementor_overlay_style'] : 'classic',
			'scroll_to'          => ( ! empty( $settings['unfold_elementor_scroll_to'] ) && 'yes' === $settings['unfold_elementor_scroll_to'] ),
			'scroll_offset'      => isset( $settings['unfold_elementor_scroll_offset'] ) ? intval( $settings['unfold_elementor_scroll_offset'] ) : -60,
			'accordion_group'    => ! empty( $settings['unfold_elementor_accordion_group'] ) ? sanitize_text_field( $settings['unfold_elementor_accordion_group'] ) : '',
			'remember_state'     => ( ! empty( $settings['unfold_elementor_remember_state'] ) && 'yes' === $settings['unfold_elementor_remember_state'] ),
			'disable_desktop'    => ( ! empty( $settings['unfold_elementor_disable_desktop'] ) && 'yes' === $settings['unfold_elementor_disable_desktop'] ),
			'disable_tablet'     => ( ! empty( $settings['unfold_elementor_disable_tablet'] ) && 'yes' === $settings['unfold_elementor_disable_tablet'] ),
			'disable_mobile'     => ( ! empty( $settings['unfold_elementor_disable_mobile'] ) && 'yes' === $settings['unfold_elementor_disable_mobile'] ),
		);

		// Add wrapper CSS classes
		$element->add_render_attribute(
			'_wrapper',
			'class',
			array(
				'unfold-elementor-container',
				'unfold-elementor--active',
				$init_open ? 'unfold-elementor--expanded' : 'unfold-elementor--collapsed',
				'unfold-elementor--mode-' . esc_attr( $mode ),
				'unfold-elementor--btn-pos-' . esc_attr( ! empty( $settings['unfold_elementor_button_position'] ) ? $settings['unfold_elementor_button_position'] : 'below' ),
				'unfold-elementor--anim-' . esc_attr( $animation_type ),
			)
		);

		if ( ! empty( $settings['unfold_elementor_button_align'] ) ) {
			$element->add_render_attribute( '_wrapper', 'class', 'unfold-elementor-align-' . esc_attr( $settings['unfold_elementor_button_align'] ) );
		}

		// Attach data-unfold-elementor JSON configuration
		$element->add_render_attribute( '_wrapper', 'data-unfold-elementor', wp_json_encode( $config ) );
	}
}
