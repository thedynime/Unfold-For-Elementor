<?php
/**
 * Unfold for Elementor - Core Plugin Class
 *
 * @package Dynime\UnfoldElementor
 */

namespace Dynime\UnfoldElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	protected static $instance = null;

	const MINIMUM_ELEMENTOR_VERSION = '3.5.0';
	const MINIMUM_PHP_VERSION       = '7.4';
	const MINIMUM_WP_VERSION        = '5.8';

	/**
	 * Get instance.
	 *
	 * @return Plugin
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
		$this->init();
	}

	/**
	 * Initialize plugin components.
	 */
	public function init() {
		load_plugin_textdomain( 'unfold-for-elementor', false, dirname( plugin_basename( UNFOLD_ELEMENTOR_FILE ) ) . '/languages' );

		if ( ! $this->is_compatible() ) {
			return;
		}

		// Initialize Core Assets & Elementor Integration
		Assets::instance();
		Elementor\Integration::instance();
	}

	/**
	 * Check environment compatibility.
	 *
	 * @return bool
	 */
	public function is_compatible() {
		// Check if Elementor is installed and active
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_elementor' ) );
			return false;
		}

		// Check Elementor version
		if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return false;
		}

		// Check PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return false;
		}

		return true;
	}

	/**
	 * Admin notice for missing Elementor.
	 */
	public function admin_notice_missing_elementor() {
		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'unfold-for-elementor' ),
			'<strong>' . esc_html__( 'Unfold for Elementor', 'unfold-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'unfold-for-elementor' ) . '</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}

	/**
	 * Admin notice for minimum Elementor version.
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'unfold-for-elementor' ),
			'<strong>' . esc_html__( 'Unfold for Elementor', 'unfold-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'unfold-for-elementor' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}

	/**
	 * Admin notice for minimum PHP version.
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'unfold-for-elementor' ),
			'<strong>' . esc_html__( 'Unfold for Elementor', 'unfold-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'unfold-for-elementor' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}
}
