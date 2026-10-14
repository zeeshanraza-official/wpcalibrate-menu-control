<?php
/**
 * Plugin Name:       WPCalibrate Menu Control
 * Plugin URI:        https://marketplace.wpcalibrate.com/
 * Description:       Advanced frontend menu control: conditional visibility, icons, badges, subtitles, non-clickable headings, WooCommerce dynamic elements, and lightweight mega menus.
 * Version:           1.0.1
 * Requires at least: 6.8
 * Requires PHP:      8.2
 * Author:            WPCalibrate
 * Author URI:        https://wpcalibrate.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpcalibrate-menu-control
 * Domain Path:       /languages
 * Update URI:        https://github.com/zeeshanraza-official/wpcalibrate-menu-control
 * GitHub Plugin URI: https://github.com/zeeshanraza-official/wpcalibrate-menu-control
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

// Prevent direct script execution.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin Constants.
define( 'WPCALIBRATE_MENU_CONTROL_VERSION', '1.0.1' );
define( 'WPCALIBRATE_MENU_CONTROL_FILE', __FILE__ );
define( 'WPCALIBRATE_MENU_CONTROL_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPCALIBRATE_MENU_CONTROL_URL', plugin_dir_url( __FILE__ ) );
define( 'WPCALIBRATE_MENU_CONTROL_BASENAME', plugin_basename( __FILE__ ) );

/**
 * PSR-4 Autoloader for WPCalibrate\MenuControl namespace.
 */
spl_autoload_register(
	static function ( string $class ): void {
		$prefix = 'WPCalibrate\\MenuControl\\';
		$base_dir = WPCALIBRATE_MENU_CONTROL_PATH . 'src/';

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class, $len );
		$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);

// Register activation and deactivation hooks.
register_activation_hook( __FILE__, array( \WPCalibrate\MenuControl\Activation\Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( \WPCalibrate\MenuControl\Activation\Deactivator::class, 'deactivate' ) );

/**
 * Early environment check before initializing full plugin.
 */
add_action(
	'plugins_loaded',
	static function (): void {
		// Verify environment requirements safely.
		if ( ! \WPCalibrate\MenuControl\Support\Environment::is_php_compatible() || ! \WPCalibrate\MenuControl\Support\Environment::is_wp_compatible() ) {
			add_action(
				'admin_notices',
				static function (): void {
					echo '<div class="notice notice-error"><p>' .
						esc_html__( 'WPCalibrate Menu Control requires at least PHP 8.2 and WordPress 6.8. Please upgrade your server environment.', 'wpcalibrate-menu-control' ) .
						'</p></div>';
				}
			);
			return;
		}

		// Initialize orchestrator.
		\WPCalibrate\MenuControl\Plugin::get_instance( WPCALIBRATE_MENU_CONTROL_FILE )->run();
	}
);
