<?php
/**
 * License manager for key storage and status display.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Licensing;

use WPCalibrate\MenuControl\Support\Capabilities;
use WPCalibrate\MenuControl\Admin\AdminMenu;

/**
 * Class LicenseManager
 */
final class LicenseManager {

	public const OPTION_KEY = 'wpcalibrate_menu_control_license_key';
	public const NONCE_ACTION = 'wpcalibrate_menu_control_license_action';
	public const NONCE_NAME   = 'wpcalibrate_license_nonce';

	private LicenseProviderInterface $provider;

	/**
	 * Constructor.
	 */
	public function __construct( ?LicenseProviderInterface $provider = null ) {
		$this->provider = $provider ?? new UnconfiguredLicenseProvider();
	}

	/**
	 * Register hooks.
	 */
	public function register(): void {
		add_action( 'admin_init', array( $this, 'handle_save' ) );
	}

	/**
	 * Get raw stored license key.
	 */
	public function get_license_key(): string {
		return (string) get_option( self::OPTION_KEY, '' );
	}

	/**
	 * Get masked license key for safe display.
	 */
	public function get_masked_license_key(): string {
		$key = $this->get_license_key();
		if ( empty( $key ) ) {
			return '';
		}

		$len = strlen( $key );
		if ( $len <= 4 ) {
			return str_repeat( '•', $len );
		}

		$visible_suffix = substr( $key, -4 );
		return str_repeat( '•', $len - 4 ) . $visible_suffix;
	}

	/**
	 * Check if a license key is set.
	 */
	public function has_license_key(): bool {
		return '' !== $this->get_license_key();
	}

	/**
	 * Get status message.
	 *
	 * @return array{success: bool, status: string, message: string}
	 */
	public function get_status(): array {
		if ( ! $this->has_license_key() ) {
			return array(
				'success' => false,
				'status'  => 'empty',
				'message' => __( 'No license key entered.', 'wpcalibrate-menu-control' ),
			);
		}

		return $this->provider->check_status( $this->get_license_key() );
	}

	/**
	 * Handle save/deactivate action from license settings tab.
	 */
	public function handle_save(): void {
		if ( ! isset( $_POST['wpcalibrate_menu_control_save_license'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		if ( empty( $_GET['page'] ) || AdminMenu::SUBMENU_SLUG !== $_GET['page'] ) {
			return;
		}

		if ( ! Capabilities::current_user_can_manage() ) {
			wp_die( esc_html__( 'Unauthorized access.', 'wpcalibrate-menu-control' ) );
		}

		check_admin_referer( self::NONCE_ACTION, self::NONCE_NAME );

		$action = isset( $_POST['license_action'] ) ? sanitize_key( (string) $_POST['license_action'] ) : 'save';

		if ( 'remove' === $action ) {
			delete_option( self::OPTION_KEY );
			add_settings_error(
				'wpcalibrate_menu_control_notices',
				'license_removed',
				__( 'License key removed.', 'wpcalibrate-menu-control' ),
				'info'
			);
		} else {
			$raw_key = isset( $_POST['license_key'] ) ? sanitize_text_field( (string) $_POST['license_key'] ) : '';
			$raw_key = trim( $raw_key );

			// If the user submitted masked text without changing it, preserve existing key.
			if ( str_contains( $raw_key, '•' ) ) {
				$raw_key = $this->get_license_key();
			}

			if ( ! empty( $raw_key ) ) {
				update_option( self::OPTION_KEY, $raw_key, false );
				add_settings_error(
					'wpcalibrate_menu_control_notices',
					'license_saved',
					__( 'License key saved. Remote activation remains unconfigured until WPCalibrate API details are provided.', 'wpcalibrate-menu-control' ),
					'success'
				);
			} else {
				delete_option( self::OPTION_KEY );
			}
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page' => AdminMenu::SUBMENU_SLUG,
					'tab'  => 'license',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}
}
