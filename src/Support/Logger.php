<?php
/**
 * Safe logging abstraction.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Support;

/**
 * Class Logger
 */
final class Logger {

	/**
	 * Log levels.
	 */
	public const LEVEL_DEBUG   = 'debug';
	public const LEVEL_INFO    = 'info';
	public const LEVEL_WARNING = 'warning';
	public const LEVEL_ERROR   = 'error';

	/**
	 * Log a message if debug logging is enabled.
	 *
	 * @param string               $level Log level.
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional contextual data (secrets filtered).
	 */
	public static function log( string $level, string $message, array $context = array() ): void {
		$options = get_option( 'wpcalibrate_menu_control_settings', array() );
		$enabled = ! empty( $options['enable_debug_logging'] );

		if ( ! $enabled && self::LEVEL_ERROR !== $level ) {
			return;
		}

		$filtered_context = self::filter_sensitive_data( $context );
		$log_entry        = sprintf(
			'[%s] [WPCalibrate Menu Control] [%s] %s %s',
			gmdate( 'Y-m-d H:i:s' ),
			strtoupper( $level ),
			$message,
			! empty( $filtered_context ) ? wp_json_encode( $filtered_context ) : ''
		);

		// If WooCommerce is active, we can also use WC_Logger for wc-related contexts.
		if ( Environment::is_woocommerce_active() && ! empty( $context['is_wc'] ) && function_exists( 'wc_get_logger' ) ) {
			$wc_logger = wc_get_logger();
			$wc_logger->log( $level, $message, array( 'source' => 'wpcalibrate-menu-control' ) );
			return;
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $log_entry );
		}
	}

	/**
	 * Filter sensitive parameters from context to prevent accidental leaks.
	 *
	 * @param array<string, mixed> $data
	 * @return array<string, mixed>
	 */
	public static function filter_sensitive_data( array $data ): array {
		$sensitive_keys = array(
			'license_key',
			'license',
			'key',
			'password',
			'secret',
			'nonce',
			'_wpnonce',
			'cookie',
			'authorization',
			'token',
			'auth_header',
			'credit_card',
		);

		$cleaned = array();
		foreach ( $data as $k => $v ) {
			$lower_k = strtolower( (string) $k );
			if ( in_array( $lower_k, $sensitive_keys, true ) ) {
				$cleaned[ $k ] = '[REDACTED]';
			} elseif ( is_array( $v ) ) {
				$cleaned[ $k ] = self::filter_sensitive_data( $v );
			} else {
				$cleaned[ $k ] = $v;
			}
		}

		return $cleaned;
	}
}
