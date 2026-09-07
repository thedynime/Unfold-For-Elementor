<?php
/**
 * Unfold for Elementor - Assets Manager
 *
 * @package Dynime\UnfoldElementor
 */

namespace Dynime\UnfoldElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Assets {

	/**
	 * Singleton instance.
	 *
	 * @var Assets|null
	 */
	protected static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Assets
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
		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_styles' ) );
		add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_styles' ) );
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_editor_styles' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );
	}

	/**
	 * Enqueue frontend and preview CSS.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'unfold-elementor-style',
			UNFOLD_ELEMENTOR_URL . 'assets/css/unfold-elementor.css',
			array(),
			UNFOLD_ELEMENTOR_VERSION,
			'all'
		);
	}

	/**
	 * Enqueue frontend and preview JS.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'unfold-elementor-script',
			UNFOLD_ELEMENTOR_URL . 'assets/js/unfold-elementor.js',
			array( 'jquery' ),
			UNFOLD_ELEMENTOR_VERSION,
			true
		);
	}

	/**
	 * Enqueue editor panel styles.
	 */
	public function enqueue_editor_styles() {
		wp_enqueue_style(
			'unfold-elementor-editor-style',
			UNFOLD_ELEMENTOR_URL . 'assets/css/unfold-elementor-editor.css',
			array(),
			UNFOLD_ELEMENTOR_VERSION,
			'all'
		);
	}

	/**
	 * Enqueue editor panel scripts.
	 */
	public function enqueue_editor_scripts() {
		wp_enqueue_script(
			'unfold-elementor-editor-script',
			UNFOLD_ELEMENTOR_URL . 'assets/js/unfold-elementor-editor.js',
			array( 'jquery' ),
			UNFOLD_ELEMENTOR_VERSION,
			true
		);
	}
}
