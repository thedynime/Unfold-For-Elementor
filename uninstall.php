<?php
/**
 * Uninstaller for Unfold for Elementor
 *
 * @package Dynime\UnfoldElementor
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Unfold for Elementor stores settings directly inside Elementor page meta
// without modifying global tables or creating persistent clutter.
// No residual data to delete.
