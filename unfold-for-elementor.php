<?php
/**
 * Plugin Name:       Unfold for Elementor
 * Plugin URI:        https://dynime.com
 * Description:       A flexible Show More and Show Less solution for Elementor that lets you make widgets, containers, sections, and nested Elementor content expandable and collapsible directly from the Advanced tab.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Dynime LLC
 * Author URI:        https://dynime.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       unfold-for-elementor
 * Domain Path:       /languages
 *
 * @package           Dynime\UnfoldElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'UNFOLD_ELEMENTOR_VERSION', '1.0.0' );
define( 'UNFOLD_ELEMENTOR_FILE', __FILE__ );
define( 'UNFOLD_ELEMENTOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'UNFOLD_ELEMENTOR_URL', plugin_dir_url( __FILE__ ) );
define( 'UNFOLD_ELEMENTOR_BASENAME', plugin_basename( __FILE__ ) );

require_once UNFOLD_ELEMENTOR_PATH . 'includes/helper-functions.php';
require_once UNFOLD_ELEMENTOR_PATH . 'includes/class-assets.php';
require_once UNFOLD_ELEMENTOR_PATH . 'includes/class-elementor-integration.php';
require_once UNFOLD_ELEMENTOR_PATH . 'includes/class-controls.php';
require_once UNFOLD_ELEMENTOR_PATH . 'includes/class-renderer.php';
require_once UNFOLD_ELEMENTOR_PATH . 'includes/class-plugin.php';

/**
 * Bootstrap the plugin.
 */
function unfold_elementor_init() {
	\Dynime\UnfoldElementor\Plugin::instance();
}
add_action( 'plugins_loaded', 'unfold_elementor_init' );
