<?php
/**
 * Import and Export tab template.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

use WPCalibrate\MenuControl\Admin\ImportExport;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpcalibrate-import-export-wrap">
	<h2><?php esc_html_e( 'Import / Export Global Settings', 'wpcalibrate-menu-control' ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Export your WPCalibrate Menu Control global configuration as a JSON file, or restore configuration from a previously exported backup file.', 'wpcalibrate-menu-control' ); ?>
	</p>

	<div class="wpcalibrate-two-col-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
		<div class="wpcalibrate-card">
			<h3><?php esc_html_e( 'Export Settings', 'wpcalibrate-menu-control' ); ?></h3>
			<p><?php esc_html_e( 'Download a JSON file containing all active plugin global settings and module options. Sensitive data such as license keys are excluded automatically.', 'wpcalibrate-menu-control' ); ?></p>
			
			<form method="post" action="">
				<?php wp_nonce_field( ImportExport::EXPORT_ACTION, 'wpcalibrate_export_nonce' ); ?>
				<input type="hidden" name="wpcalibrate_menu_control_export_settings" value="1" />
				<?php submit_button( __( 'Export Settings (JSON)', 'wpcalibrate-menu-control' ), 'primary', 'submit', false ); ?>
			</form>
		</div>

		<div class="wpcalibrate-card">
			<h3><?php esc_html_e( 'Import Settings', 'wpcalibrate-menu-control' ); ?></h3>
			<p><?php esc_html_e( 'Upload a valid WPCalibrate Menu Control JSON export file to restore or synchronize configuration across sites.', 'wpcalibrate-menu-control' ); ?></p>

			<form method="post" action="" enctype="multipart/form-data">
				<?php wp_nonce_field( ImportExport::IMPORT_ACTION, 'wpcalibrate_import_nonce' ); ?>
				<input type="hidden" name="wpcalibrate_menu_control_import_settings" value="1" />

				<p>
					<label for="wpcalibrate_import_file"><strong><?php esc_html_e( 'Select JSON File (Max 2MB):', 'wpcalibrate-menu-control' ); ?></strong></label><br />
					<input type="file" id="wpcalibrate_import_file" name="wpcalibrate_import_file" accept=".json,application/json" required style="margin-top: 6px;" />
				</p>

				<p>
					<label>
						<input type="checkbox" name="confirm_overwrite" value="1" required />
						<?php esc_html_e( 'I understand that importing will update current global plugin settings.', 'wpcalibrate-menu-control' ); ?>
					</label>
				</p>

				<?php submit_button( __( 'Import Settings', 'wpcalibrate-menu-control' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>
	</div>
</div>
