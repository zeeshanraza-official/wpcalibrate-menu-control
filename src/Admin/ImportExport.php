<?php
/**
 * Settings import and export handler.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Admin;

use WPCalibrate\MenuControl\Support\Capabilities;
use WPCalibrate\MenuControl\Support\Logger;
use WPCalibrate\MenuControl\Migration\MigrationManager;

/**
 * Class ImportExport
 */
final class ImportExport {

	public const EXPORT_ACTION = 'wpcalibrate_menu_control_export_action';
	public const IMPORT_ACTION = 'wpcalibrate_menu_control_import_action';
	public const MAX_FILE_SIZE  = 2097152; // 2MB

	private Settings $settings;

	/**
	 * Constructor.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register hooks.
	 */
	public function register(): void {
		add_action( 'admin_init', array( $this, 'handle_export' ) );
		add_action( 'admin_init', array( $this, 'handle_import' ) );
	}

	/**
	 * Handle settings export to downloadable JSON.
	 */
	public function handle_export(): void {
		if ( ! isset( $_POST['wpcalibrate_menu_control_export_settings'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		if ( empty( $_GET['page'] ) || AdminMenu::SUBMENU_SLUG !== $_GET['page'] ) {
			return;
		}

		if ( ! Capabilities::current_user_can_manage() ) {
			wp_die( esc_html__( 'Unauthorized access.', 'wpcalibrate-menu-control' ) );
		}

		check_admin_referer( self::EXPORT_ACTION, 'wpcalibrate_export_nonce' );

		$all_settings = $this->settings->get_all();

		// Never export secrets.
		unset( $all_settings['license_key'] );

		$export_data = array(
			'generator'      => 'WPCalibrate Menu Control',
			'schema_version' => MigrationManager::CURRENT_SCHEMA_VERSION,
			'plugin_version' => defined( 'WPCALIBRATE_MENU_CONTROL_VERSION' ) ? WPCALIBRATE_MENU_CONTROL_VERSION : '1.0.0',
			'exported_at'    => gmdate( 'Y-m-d\TH:i:s\Z' ),
			'settings'       => $all_settings,
		);

		$json = wp_json_encode( $export_data, JSON_PRETTY_PRINT );
		$filename = sprintf( 'wpcalibrate-menu-control-settings-%s.json', gmdate( 'Y-m-d' ) );

		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}

	/**
	 * Handle settings import from JSON file.
	 */
	public function handle_import(): void {
		if ( ! isset( $_POST['wpcalibrate_menu_control_import_settings'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		if ( empty( $_GET['page'] ) || AdminMenu::SUBMENU_SLUG !== $_GET['page'] ) {
			return;
		}

		if ( ! Capabilities::current_user_can_manage() ) {
			wp_die( esc_html__( 'Unauthorized access.', 'wpcalibrate-menu-control' ) );
		}

		check_admin_referer( self::IMPORT_ACTION, 'wpcalibrate_import_nonce' );

		if ( empty( $_FILES['wpcalibrate_import_file']['tmp_name'] ) || ! is_uploaded_file( $_FILES['wpcalibrate_import_file']['tmp_name'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			add_settings_error(
				'wpcalibrate_menu_control_notices',
				'import_no_file',
				__( 'Please select a valid JSON export file to import.', 'wpcalibrate-menu-control' ),
				'error'
			);
			return;
		}

		$file = $_FILES['wpcalibrate_import_file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		// Reject oversized files.
		if ( $file['size'] > self::MAX_FILE_SIZE ) {
			add_settings_error(
				'wpcalibrate_menu_control_notices',
				'import_oversized',
				__( 'Import file exceeds the maximum allowed size (2MB).', 'wpcalibrate-menu-control' ),
				'error'
			);
			return;
		}

		$raw_content = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $raw_content ) {
			add_settings_error(
				'wpcalibrate_menu_control_notices',
				'import_read_error',
				__( 'Failed to read uploaded file.', 'wpcalibrate-menu-control' ),
				'error'
			);
			return;
		}

		$decoded = json_decode( $raw_content, true );
		if ( ! is_array( $decoded ) || ! isset( $decoded['settings'] ) || ! is_array( $decoded['settings'] ) ) {
			add_settings_error(
				'wpcalibrate_menu_control_notices',
				'import_invalid_json',
				__( 'Invalid or malformed JSON schema. Import was aborted safely without modifying settings.', 'wpcalibrate-menu-control' ),
				'error'
			);
			return;
		}

		if ( empty( $decoded['generator'] ) || 'WPCalibrate Menu Control' !== $decoded['generator'] ) {
			add_settings_error(
				'wpcalibrate_menu_control_notices',
				'import_unsupported_generator',
				__( 'The provided file is not a valid WPCalibrate Menu Control export.', 'wpcalibrate-menu-control' ),
				'error'
			);
			return;
		}

		// Transactional approach: Sanitize and apply.
		$existing_settings = $this->settings->get_all();
		$new_settings      = $decoded['settings'];

		// Prevent importing any secret keys.
		unset( $new_settings['license_key'] );

		$merged = array_merge( $existing_settings, $new_settings );
		$success = $this->settings->update( $merged );

		if ( $success ) {
			$count = count( $new_settings );
			add_settings_error(
				'wpcalibrate_menu_control_notices',
				'import_success',
				sprintf(
					/* translators: %d: Number of settings imported */
					__( 'Settings imported successfully (%d configuration values updated).', 'wpcalibrate-menu-control' ),
					$count
				),
				'success'
			);
		} else {
			add_settings_error(
				'wpcalibrate_menu_control_notices',
				'import_no_change',
				__( 'Settings imported with no changes to current values.', 'wpcalibrate-menu-control' ),
				'info'
			);
		}
	}
}
