<?php
/**
 * User capabilities manager.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Support;

/**
 * Class Capabilities
 */
final class Capabilities {

	/**
	 * Default capability required to manage plugin settings.
	 */
	public const DEFAULT_CAPABILITY = 'manage_options';

	/**
	 * Check if the current user can manage menu control settings.
	 */
	public static function current_user_can_manage(): bool {
		$capability = apply_filters( 'wpcalibrate_menu_control_capability', self::DEFAULT_CAPABILITY );
		return current_user_can( (string) $capability );
	}

	/**
	 * Check if the current user can manage nav menus.
	 */
	public static function current_user_can_edit_menus(): bool {
		return current_user_can( 'edit_theme_options' );
	}
}
