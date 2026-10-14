<?php
/**
 * Settings model and persistence handler.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Admin;

use WPCalibrate\MenuControl\Support\Capabilities;
use WPCalibrate\MenuControl\Support\Logger;
use WPCalibrate\MenuControl\Migration\MigrationManager;

/**
 * Class Settings
 */
final class Settings {

	public const OPTION_KEY = MigrationManager::SETTINGS_OPTION_KEY;
	public const NONCE_ACTION = 'wpcalibrate_menu_control_save_settings_action';
	public const NONCE_NAME   = 'wpcalibrate_menu_control_settings_nonce';

	/**
	 * Cached settings.
	 *
	 * @var array<string, mixed>|null
	 */
	private ?array $settings = null;

	/**
	 * Register settings hooks.
	 */
	public function register(): void {
		add_action( 'admin_init', array( $this, 'handle_save' ) );
	}

	/**
	 * Get all settings merged with defaults.
	 *
	 * @return array<string, mixed>
	 */
	public function get_all(): array {
		if ( null !== $this->settings ) {
			return $this->settings;
		}

		$defaults = MigrationManager::get_default_settings();
		$stored   = get_option( self::OPTION_KEY, array() );

		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$this->settings = array_merge( $defaults, $stored );
		return $this->settings;
	}

	/**
	 * Get a specific setting value.
	 */
	public function get( string $key, mixed $default = null ): mixed {
		$all = $this->get_all();
		return $all[ $key ] ?? $default;
	}

	/**
	 * Update settings array.
	 *
	 * @param array<string, mixed> $new_settings
	 */
	public function update( array $new_settings ): bool {
		$sanitized = $this->sanitize_settings( $new_settings );
		$updated   = update_option( self::OPTION_KEY, $sanitized, false );
		$this->settings = $sanitized;
		return $updated;
	}

	/**
	 * Handle form submission for settings tabs.
	 */
	public function handle_save(): void {
		if ( ! isset( $_POST['wpcalibrate_menu_control_save_settings'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		// Strictly verify this is our plugin page.
		if ( empty( $_GET['page'] ) || AdminMenu::SUBMENU_SLUG !== $_GET['page'] ) {
			return;
		}

		if ( ! Capabilities::current_user_can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to modify these settings.', 'wpcalibrate-menu-control' ) );
		}

		check_admin_referer( self::NONCE_ACTION, self::NONCE_NAME );

		$current = $this->get_all();
		$input   = isset( $_POST['wpcalibrate_menu_control'] ) && is_array( $_POST['wpcalibrate_menu_control'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			? $_POST['wpcalibrate_menu_control'] // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			: array();

		$merged = array_merge( $current, $input );

		// Handle checkboxes that might be unchecked (absent from POST).
		$checkbox_keys = array(
			'enable_icons',
			'enable_badges',
			'enable_subtitles',
			'enable_non_clickable',
			'enable_conditions',
			'enable_mega_menu',
			'enable_woocommerce',
			'enable_block_nav',
			'disable_frontend_css',
			'enable_debug_logging',
		);

		$tab = isset( $_POST['current_tab'] ) ? sanitize_key( (string) $_POST['current_tab'] ) : 'general';

		// If saving on a tab that contains these checkboxes, ensure unchecked ones become 0.
		if ( in_array( $tab, array( 'general', 'conditions', 'woocommerce', 'megamenu', 'styling' ), true ) ) {
			foreach ( $checkbox_keys as $chk ) {
				if ( isset( $_POST[ 'field_present_' . $chk ] ) && ! isset( $input[ $chk ] ) ) {
					$merged[ $chk ] = 0;
				}
			}
		}

		$this->update( $merged );

		add_settings_error(
			'wpcalibrate_menu_control_notices',
			'settings_saved',
			__( 'Settings saved successfully.', 'wpcalibrate-menu-control' ),
			'success'
		);

		// Redirect to prevent form resubmission on refresh.
		$redirect_url = add_query_arg(
			array(
				'page'     => AdminMenu::SUBMENU_SLUG,
				'tab'      => $tab,
				'settings-updated' => 'true',
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Strictly sanitize all settings fields.
	 *
	 * @param array<string, mixed> $input
	 * @return array<string, mixed>
	 */
	public function sanitize_settings( array $input ): array {
		$sanitized = MigrationManager::get_default_settings();

		// Toggles (0 or 1).
		$boolean_keys = array(
			'enable_icons',
			'enable_badges',
			'enable_subtitles',
			'enable_non_clickable',
			'enable_conditions',
			'enable_mega_menu',
			'enable_woocommerce',
			'enable_block_nav',
			'disable_frontend_css',
			'enable_debug_logging',
		);

		foreach ( $boolean_keys as $key ) {
			if ( isset( $input[ $key ] ) ) {
				$sanitized[ $key ] = ! empty( $input[ $key ] ) ? 1 : 0;
			}
		}

		// Responsive Breakpoint (320 to 1920 px).
		if ( isset( $input['default_breakpoint'] ) ) {
			$bp = absint( $input['default_breakpoint'] );
			$sanitized['default_breakpoint'] = ( $bp >= 320 && $bp <= 1920 ) ? $bp : 768;
		}

		// Icon Spacing (0 to 50 px).
		if ( isset( $input['icon_spacing'] ) ) {
			$spacing = absint( $input['icon_spacing'] );
			$sanitized['icon_spacing'] = min( $spacing, 50 );
		}

		// Badge Border Radius (0 to 30 px).
		if ( isset( $input['badge_border_radius'] ) ) {
			$radius = absint( $input['badge_border_radius'] );
			$sanitized['badge_border_radius'] = min( $radius, 30 );
		}

		// Mega Menu Dropdown Width (400 to 2400 px).
		if ( isset( $input['mega_menu_dropdown_width'] ) ) {
			$width = absint( $input['mega_menu_dropdown_width'] );
			$sanitized['mega_menu_dropdown_width'] = ( $width >= 400 && $width <= 2400 ) ? $width : 1140;
		}

		// Uninstall Data Retention: 'retain' or 'delete'.
		if ( isset( $input['uninstall_data_retention'] ) ) {
			$retention = sanitize_key( (string) $input['uninstall_data_retention'] );
			$sanitized['uninstall_data_retention'] = 'delete' === $retention ? 'delete' : 'retain';
		}

		return apply_filters( 'wpcalibrate_menu_control_sanitize_settings', $sanitized, $input );
	}
}
