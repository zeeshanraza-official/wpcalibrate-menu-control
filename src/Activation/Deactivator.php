<?php
/**
 * Plugin deactivation handler.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Activation;

/**
 * Class Deactivator
 */
final class Deactivator {

	/**
	 * Run deactivation logic.
	 * Preserves all configuration and data.
	 */
	public static function deactivate(): void {
		// Clear any scheduled hooks owned by the plugin if registered.
		wp_clear_scheduled_hook( 'wpcalibrate_menu_control_daily_event' );
	}
}
