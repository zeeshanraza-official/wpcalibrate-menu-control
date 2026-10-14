<?php
/**
 * Migration and schema version manager.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Migration;

use WPCalibrate\MenuControl\Support\Logger;

/**
 * Class MigrationManager
 */
final class MigrationManager {

	public const CURRENT_SCHEMA_VERSION = 1;
	public const SCHEMA_OPTION_KEY      = 'wpcalibrate_menu_control_schema_version';
	public const VERSION_OPTION_KEY     = 'wpcalibrate_menu_control_version';
	public const SETTINGS_OPTION_KEY    = 'wpcalibrate_menu_control_settings';

	/**
	 * Run required migrations.
	 */
	public static function run_migrations(): void {
		$installed_schema = (int) get_option( self::SCHEMA_OPTION_KEY, 0 );

		if ( $installed_schema < 1 ) {
			self::migrate_v1();
		}

		// Update version metadata.
		update_option( self::SCHEMA_OPTION_KEY, self::CURRENT_SCHEMA_VERSION, false );
		update_option( self::VERSION_OPTION_KEY, defined( 'WPCALIBRATE_MENU_CONTROL_VERSION' ) ? WPCALIBRATE_MENU_CONTROL_VERSION : '1.0.0', false );
	}

	/**
	 * Initial schema migration v1.
	 */
	private static function migrate_v1(): void {
		$defaults = self::get_default_settings();
		$existing = get_option( self::SETTINGS_OPTION_KEY, array() );

		if ( ! is_array( $existing ) ) {
			$existing = array();
		}

		$merged = array_merge( $defaults, $existing );
		update_option( self::SETTINGS_OPTION_KEY, $merged, false );

		Logger::log( Logger::LEVEL_INFO, 'Initial v1 migration completed successfully.' );
	}

	/**
	 * Get default settings array.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_default_settings(): array {
		return array(
			// General
			'enable_icons'             => 1,
			'enable_badges'            => 1,
			'enable_subtitles'         => 1,
			'enable_non_clickable'     => 1,
			'enable_conditions'        => 1,
			'enable_mega_menu'         => 1,
			'enable_woocommerce'       => 1,
			'enable_block_nav'         => 1,

			// Responsive & Styling
			'default_breakpoint'       => 768,
			'disable_frontend_css'     => 0,
			'icon_spacing'             => 6,
			'badge_border_radius'      => 4,
			'mega_menu_dropdown_width' => 1140,

			// Uninstall & Diagnostics
			'uninstall_data_retention' => 'retain', // 'retain' or 'delete'
			'enable_debug_logging'     => 0,
		);
	}
}
