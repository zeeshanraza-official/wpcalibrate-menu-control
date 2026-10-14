<?php
/**
 * Device group condition provider.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Conditions;

/**
 * Class DeviceCondition
 */
final class DeviceCondition implements ConditionInterface {

	public const TYPE = 'device_group';

	public function get_type(): string {
		return self::TYPE;
	}

	/**
	 * Detect current device category.
	 *
	 * Note: Device detection is presentation-oriented, not a security boundary.
	 * Can be filtered via wpcalibrate_menu_control_detected_device.
	 */
	public static function detect_device(): string {
		$device = 'desktop';

		if ( function_exists( 'wp_is_mobile' ) && wp_is_mobile() ) {
			$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( (string) $_SERVER['HTTP_USER_AGENT'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( str_contains( $ua, 'ipad' ) || str_contains( $ua, 'tablet' ) || str_contains( $ua, 'playbook' ) || str_contains( $ua, 'silk' ) ) {
				$device = 'tablet';
			} else {
				$device = 'mobile';
			}
		}

		return (string) apply_filters( 'wpcalibrate_menu_control_detected_device', $device );
	}

	/**
	 * Evaluate device condition.
	 */
	public function evaluate( array $rule ): bool {
		$operator       = $rule['operator'] ?? 'is';
		$target_device  = is_string( $rule['value'] ) ? strtolower( trim( $rule['value'] ) ) : '';
		$current_device = self::detect_device();

		$matches = ( $target_device === $current_device );

		return 'is_not' === $operator ? ! $matches : $matches;
	}
}
