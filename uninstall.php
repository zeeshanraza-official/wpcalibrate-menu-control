<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Check administrator data retention preference.
$settings = get_option( 'wpcalibrate_menu_control_settings', array() );
$retention = isset( $settings['uninstall_data_retention'] ) ? (string) $settings['uninstall_data_retention'] : 'retain';

// If retention is set to 'retain' (the safe default), do not delete any data.
if ( 'delete' !== $retention ) {
	return;
}

global $wpdb;

// 1. Remove plugin-owned options.
delete_option( 'wpcalibrate_menu_control_settings' );
delete_option( 'wpcalibrate_menu_control_schema_version' );
delete_option( 'wpcalibrate_menu_control_version' );
delete_option( 'wpcalibrate_menu_control_license_key' );

// 2. Clear scheduled cron tasks if any.
wp_clear_scheduled_hook( 'wpcalibrate_menu_control_daily_event' );

// 3. Delete only plugin-owned post metadata (prefixed with _wpcalibrate_menu_control_).
// Does NOT delete nav_menu_item posts, WordPress menus, or media attachments.
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
		$wpdb->esc_like( '_wpcalibrate_menu_control_' ) . '%'
	)
);

// 4. Delete plugin transients if any.
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
		$wpdb->esc_like( '_transient_wpcalibrate_menu_control_' ) . '%',
		$wpdb->esc_like( '_transient_timeout_wpcalibrate_menu_control_' ) . '%'
	)
);
