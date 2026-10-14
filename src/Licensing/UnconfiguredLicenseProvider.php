<?php
/**
 * Unconfigured default license provider.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Licensing;

/**
 * Class UnconfiguredLicenseProvider
 */
final class UnconfiguredLicenseProvider implements LicenseProviderInterface {

	/**
	 * Remote integration is not configured pending WPCalibrate licensing API specifications.
	 */
	public function is_configured(): bool {
		return false;
	}

	public function activate( string $license_key ): array {
		return array(
			'success' => false,
			'status'  => 'not_configured',
			'message' => __( 'Remote license activation endpoint is not yet configured. The key has been saved locally.', 'wpcalibrate-menu-control' ),
		);
	}

	public function deactivate( string $license_key ): array {
		return array(
			'success' => false,
			'status'  => 'not_configured',
			'message' => __( 'Remote license deactivation endpoint is not yet configured.', 'wpcalibrate-menu-control' ),
		);
	}

	public function check_status( string $license_key ): array {
		return array(
			'success' => false,
			'status'  => 'unconfigured',
			'message' => __( 'Remote licensing validation provider is pending configuration from WPCalibrate.', 'wpcalibrate-menu-control' ),
		);
	}
}
