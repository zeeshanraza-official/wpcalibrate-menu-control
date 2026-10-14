<?php
/**
 * License provider interface.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Licensing;

/**
 * Interface LicenseProviderInterface
 */
interface LicenseProviderInterface {

	/**
	 * Check if remote provider is configured with endpoints and credentials.
	 */
	public function is_configured(): bool;

	/**
	 * Activate a license key.
	 *
	 * @return array{success: bool, status: string, message: string}
	 */
	public function activate( string $license_key ): array;

	/**
	 * Deactivate a license key.
	 *
	 * @return array{success: bool, status: string, message: string}
	 */
	public function deactivate( string $license_key ): array;

	/**
	 * Check license status.
	 *
	 * @return array{success: bool, status: string, message: string, expires?: string}
	 */
	public function check_status( string $license_key ): array;
}
