<?php
/**
 * Unfold for Elementor - Elementor Integration Core
 *
 * @package Dynime\UnfoldElementor
 */

namespace Dynime\UnfoldElementor\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Integration {

	/**
	 * Singleton instance.
	 *
	 * @var Integration|null
	 */
	protected static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Integration
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
		add_action( 'elementor/init', array( $this, 'init' ) );
	}

	/**
	 * Initialize Elementor components.
	 */
	public function init() {
		Controls::instance();
		\Dynime\UnfoldElementor\Frontend\Renderer::instance();
	}
}
