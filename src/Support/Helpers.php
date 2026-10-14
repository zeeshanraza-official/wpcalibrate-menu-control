<?php
/**
 * Common helper functions.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Support;

/**
 * Class Helpers
 */
final class Helpers {

	/**
	 * Get absolute path to plugin file or directory.
	 */
	public static function get_plugin_path( string $relative_path = '' ): string {
		$base = defined( 'WPCALIBRATE_MENU_CONTROL_PATH' ) ? WPCALIBRATE_MENU_CONTROL_PATH : dirname( __DIR__, 2 ) . '/';
		return $base . ltrim( $relative_path, '/\\' );
	}

	/**
	 * Get URL to plugin file or directory.
	 */
	public static function get_plugin_url( string $relative_path = '' ): string {
		$base = defined( 'WPCALIBRATE_MENU_CONTROL_URL' ) ? WPCALIBRATE_MENU_CONTROL_URL : plugins_url( '/', dirname( __DIR__, 1 ) );
		return $base . ltrim( $relative_path, '/' );
	}

	/**
	 * Dynamically locate branding icon based on actual file existence.
	 *
	 * @param 'white'|'dark' $variant
	 * @return string URL to icon or empty string if not found.
	 */
	public static function get_branding_icon_url( string $variant = 'dark' ): string {
		$filename_prefix = 'icon-' . ( 'white' === $variant ? 'white' : 'dark' );
		$extensions      = array( 'png', 'svg', 'webp', 'jpg', 'jpeg' );
		$branding_dir    = self::get_plugin_path( 'branding/' );

		foreach ( $extensions as $ext ) {
			$file = $branding_dir . $filename_prefix . '.' . $ext;
			if ( file_exists( $file ) ) {
				return self::get_plugin_url( 'branding/' . $filename_prefix . '.' . $ext );
			}
		}

		return '';
	}

	/**
	 * Get site timezone string (e.g. "Europe/London" or "+00:00").
	 */
	public static function get_site_timezone_string(): string {
		$tz = get_option( 'timezone_string' );
		if ( ! empty( $tz ) ) {
			return (string) $tz;
		}

		$gmt_offset = get_option( 'gmt_offset', 0 );
		if ( 0 == $gmt_offset ) { // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
			return 'UTC+0';
		}

		$formatted_offset = sprintf( '%+03d:%02u', (int) $gmt_offset, abs( ( (int) $gmt_offset - (int) $gmt_offset ) * 60 ) );
		return 'UTC' . $formatted_offset;
	}

	/**
	 * Convert local site time string to DateTimeImmutable in site timezone.
	 */
	public static function parse_site_datetime( ?string $date_string ): ?\DateTimeImmutable {
		if ( empty( $date_string ) ) {
			return null;
		}

		try {
			$timezone = wp_timezone();
			return new \DateTimeImmutable( $date_string, $timezone );
		} catch ( \Throwable $e ) {
			return null;
		}
	}

	/**
	 * Get current site time as DateTimeImmutable.
	 */
	public static function get_current_site_datetime(): \DateTimeImmutable {
		$timezone = wp_timezone();
		return new \DateTimeImmutable( 'now', $timezone );
	}
}
