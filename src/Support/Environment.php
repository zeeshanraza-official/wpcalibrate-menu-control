<?php
/**
 * Environment and compatibility helper.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Support;

/**
 * Class Environment
 */
final class Environment {

	public const MIN_PHP_VERSION = '8.2.0';
	public const MIN_WP_VERSION  = '6.8';

	/**
	 * Check if PHP meets minimum requirement.
	 */
	public static function is_php_compatible(): bool {
		return version_compare( PHP_VERSION, self::MIN_PHP_VERSION, '>=' );
	}

	/**
	 * Check if WordPress meets minimum requirement.
	 */
	public static function is_wp_compatible(): bool {
		global $wp_version;
		return version_compare( $wp_version ?? '0', self::MIN_WP_VERSION, '>=' );
	}

	/**
	 * Check if WooCommerce is installed and active.
	 */
	public static function is_woocommerce_active(): bool {
		return class_exists( '\WooCommerce' ) && function_exists( 'WC' );
	}

	/**
	 * Get WooCommerce version, or empty string if not active.
	 */
	public static function get_woocommerce_version(): string {
		if ( ! self::is_woocommerce_active() ) {
			return '';
		}
		return defined( 'WC_VERSION' ) ? WC_VERSION : '';
	}

	/**
	 * Check if WooCommerce HPOS (High-Performance Order Storage) is enabled.
	 */
	public static function is_hpos_enabled(): bool {
		if ( ! self::is_woocommerce_active() ) {
			return false;
		}

		if ( class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && method_exists( '\Automattic\WooCommerce\Utilities\OrderUtil', 'custom_orders_table_usage_is_enabled' ) ) {
			return \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
		}

		return false;
	}

	/**
	 * Check if the active theme supports classic menus.
	 */
	public static function supports_classic_menus(): bool {
		return current_theme_supports( 'menus' ) || ! wp_is_block_theme();
	}

	/**
	 * Check if block themes / navigation blocks are active.
	 */
	public static function supports_navigation_block(): bool {
		return function_exists( 'register_block_type' );
	}

	/**
	 * Get active theme details.
	 *
	 * @return array{name: string, version: string, is_block_theme: bool}
	 */
	public static function get_theme_info(): array {
		$theme = wp_get_theme();
		return array(
			'name'           => $theme->get( 'Name' ) ?: 'Unknown',
			'version'        => $theme->get( 'Version' ) ?: '1.0.0',
			'is_block_theme' => function_exists( 'wp_is_block_theme' ) && wp_is_block_theme(),
		);
	}
}
