<?php
/**
 * System diagnostics and environment reporting.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Admin;

use WPCalibrate\MenuControl\Support\Environment;
use WPCalibrate\MenuControl\Migration\MigrationManager;

/**
 * Class Diagnostics
 */
final class Diagnostics {

	/**
	 * Get structured diagnostic data.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_diagnostic_data(): array {
		global $wp_version;

		$theme_info = Environment::get_theme_info();

		return array(
			'Plugin Version'        => defined( 'WPCALIBRATE_MENU_CONTROL_VERSION' ) ? WPCALIBRATE_MENU_CONTROL_VERSION : '1.0.0',
			'Schema Version'        => (string) get_option( MigrationManager::SCHEMA_OPTION_KEY, '1' ),
			'WordPress Version'     => $wp_version ?? 'Unknown',
			'PHP Version'           => PHP_VERSION,
			'PHP Memory Limit'      => ini_get( 'memory_limit' ) ?: 'Unknown',
			'Active Theme'          => sprintf( '%s (v%s)', $theme_info['name'], $theme_info['version'] ),
			'Block Theme Active'    => $theme_info['is_block_theme'] ? 'Yes' : 'No',
			'Classic Menus Active'  => Environment::supports_classic_menus() ? 'Yes' : 'No',
			'Navigation Block'      => Environment::supports_navigation_block() ? 'Supported' : 'Not supported',
			'WooCommerce Active'    => Environment::is_woocommerce_active() ? sprintf( 'Yes (v%s)', Environment::get_woocommerce_version() ) : 'No',
			'WooCommerce HPOS'      => Environment::is_hpos_enabled() ? 'Enabled' : 'Disabled / Inactive',
			'Site Timezone'         => get_option( 'timezone_string' ) ?: ( 'UTC' . ( get_option( 'gmt_offset', 0 ) >= 0 ? '+' : '' ) . get_option( 'gmt_offset', 0 ) ),
			'Multisite'             => is_multisite() ? 'Yes' : 'No',
		);
	}

	/**
	 * Generate clean plain-text copyable diagnostic report.
	 */
	public static function generate_plain_text_report(): string {
		$data   = self::get_diagnostic_data();
		$report = "### WPCalibrate Menu Control Diagnostics ###\n\n";

		foreach ( $data as $label => $value ) {
			$report .= sprintf( "%-24s: %s\n", $label, (string) $value );
		}

		$report .= "\n### Generated at: " . gmdate( 'Y-m-d H:i:s \U\T\C' ) . " ###\n";
		return $report;
	}
}
