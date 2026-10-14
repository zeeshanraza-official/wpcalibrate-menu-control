<?php
/**
 * License tab template.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

use WPCalibrate\MenuControl\Licensing\LicenseManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$license_manager = \WPCalibrate\MenuControl\Plugin::get_instance()->license_manager;
$masked_key      = $license_manager->get_masked_license_key();
$has_key         = $license_manager->has_license_key();
$status_info     = $license_manager->get_status();
?>
<div class="wpcalibrate-license-wrap">
	<h2><?php esc_html_e( 'License Management', 'wpcalibrate-menu-control' ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Manage your product license key for WPCalibrate Menu Control.', 'wpcalibrate-menu-control' ); ?>
	</p>

	<div class="wpcalibrate-card" style="max-width: 650px; margin-top: 20px;">
		<form method="post" action="">
			<?php wp_nonce_field( LicenseManager::NONCE_ACTION, LicenseManager::NONCE_NAME ); ?>
			<input type="hidden" name="wpcalibrate_menu_control_save_license" value="1" />
			<input type="hidden" name="license_action" value="save" />

			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="wpcalibrate_license_key"><?php esc_html_e( 'License Key', 'wpcalibrate-menu-control' ); ?></label>
						</th>
						<td>
							<input type="text"
								id="wpcalibrate_license_key"
								name="license_key"
								value="<?php echo esc_attr( $masked_key ); ?>"
								class="regular-text code"
								placeholder="Enter your license key"
								autocomplete="off" />
							<?php if ( $has_key ) : ?>
								<p class="description"><?php esc_html_e( 'Key is masked for security.', 'wpcalibrate-menu-control' ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Remote Status', 'wpcalibrate-menu-control' ); ?></th>
						<td>
							<span class="wpcalibrate-status-badge wpcalibrate-status-warning">
								<?php esc_html_e( 'Not Configured (Pending Provider Specification)', 'wpcalibrate-menu-control' ); ?>
							</span>
							<p class="description" style="margin-top: 6px;">
								<?php echo esc_html( $status_info['message'] ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>

			<div style="margin-top: 15px; display: flex; gap: 10px; align-items: center;">
				<?php submit_button( __( 'Save License Key', 'wpcalibrate-menu-control' ), 'primary', 'submit', false ); ?>

				<?php if ( $has_key ) : ?>
					<button type="submit" name="license_action" value="remove" class="button button-secondary" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to remove the stored license key?', 'wpcalibrate-menu-control' ) ); ?>');">
						<?php esc_html_e( 'Remove Key', 'wpcalibrate-menu-control' ); ?>
					</button>
				<?php endif; ?>
			</div>
		</form>
	</div>

	<div class="wpcalibrate-card" style="max-width: 650px; margin-top: 20px; border-left: 4px solid #f0b849;">
		<h3><?php esc_html_e( 'Notice on Remote Licensing Integration', 'wpcalibrate-menu-control' ); ?></h3>
		<p>
			<?php esc_html_e( 'In accordance with WPCalibrate architectural guidelines, remote activation endpoints are intentionally kept unconfigured until the official WPCalibrate licensing provider and API specifications (endpoints, authentication, and response schemas) are supplied.', 'wpcalibrate-menu-control' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Core navigation enhancements, conditional visibility, mega menus, and WooCommerce dynamic elements are 100% functional and never blocked by missing remote licensing.', 'wpcalibrate-menu-control' ); ?>
		</p>
	</div>
</div>
