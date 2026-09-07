<?php
/**
 * Unfold for Elementor - Helper Functions
 *
 * @package Dynime\UnfoldElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'unfold_elementor_render_icon' ) ) {
	/**
	 * Render an Elementor icon safely.
	 *
	 * @param array  $icon       The icon setting array from Elementor ICONS control.
	 * @param array  $attributes HTML attributes for the icon.
	 * @param string $tag        HTML container tag.
	 * @return string
	 */
	function unfold_elementor_render_icon( $icon, $attributes = array(), $tag = 'span' ) {
		if ( empty( $icon ) || empty( $icon['value'] ) ) {
			return '';
		}

		ob_start();
		if ( class_exists( '\Elementor\Icons_Manager' ) ) {
			\Elementor\Icons_Manager::render_icon( $icon, $attributes, $tag );
		}
		return ob_get_clean();
	}
}

if ( ! function_exists( 'unfold_elementor_validate_heading_tag' ) ) {
	/**
	 * Validate HTML heading tag.
	 *
	 * @param string $tag Requested tag.
	 * @return string Validated tag.
	 */
	function unfold_elementor_validate_heading_tag( $tag ) {
		$allowed = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div' );
		$tag     = strtolower( trim( (string) $tag ) );
		return in_array( $tag, $allowed, true ) ? $tag : 'h3';
	}
}
