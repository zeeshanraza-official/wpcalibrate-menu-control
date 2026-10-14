<?php
/**
 * Plugin activation handler.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Activation;

use WPCalibrate\MenuControl\Support\Environment;
use WPCalibrate\MenuControl\Migration\MigrationManager;

/**
 * Class Activator
 */
final class Activator {

	/**
	 * Run activation logic.
	 */
	public static function activate(): void {
		// Verify minimum PHP and WordPress requirements.
		if ( ! Environment::is_php_compatible() ) {
			deactivate_plugins( plugin_basename( defined( 'WPCALIBRATE_MENU_CONTROL_FILE' ) ? WPCALIBRATE_MENU_CONTROL_FILE : __FILE__ ) );
			wp_die(
				sprintf(
					/* translators: 1: Plugin name, 2: Required PHP version, 3: Current PHP version */
					esc_html__( '%1$s requires PHP version %2$s or higher. Your server is running PHP %3$s.', 'wpcalibrate-menu-control' ),
					'<strong>WPCalibrate Menu Control</strong>',
					esc_html( Environment::MIN_PHP_VERSION ),
					esc_html( PHP_VERSION )
				),
				esc_html__( 'Plugin Activation Error', 'wpcalibrate-menu-control' ),
				array( 'back_link' => true )
			);
		}

		if ( ! Environment::is_wp_compatible() ) {
			global $wp_version;
			deactivate_plugins( plugin_basename( defined( 'WPCALIBRATE_MENU_CONTROL_FILE' ) ? WPCALIBRATE_MENU_CONTROL_FILE : __FILE__ ) );
			wp_die(
				sprintf(
					/* translators: 1: Plugin name, 2: Required WP version, 3: Current WP version */
					esc_html__( '%1$s requires WordPress version %2$s or higher. Your site is running WordPress %3$s.', 'wpcalibrate-menu-control' ),
					'<strong>WPCalibrate Menu Control</strong>',
					esc_html( Environment::MIN_WP_VERSION ),
					esc_html( $wp_version ?? 'unknown' )
				),
				esc_html__( 'Plugin Activation Error', 'wpcalibrate-menu-control' ),
				array( 'back_link' => true )
			);
		}

		// Initialize default settings and run migrations.
		MigrationManager::run_migrations();
	}
}
