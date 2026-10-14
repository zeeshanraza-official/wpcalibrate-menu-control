<?php
/**
 * Status and diagnostics tab template.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

use WPCalibrate\MenuControl\Admin\Diagnostics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$diag_data = Diagnostics::get_diagnostic_data();
$plain_txt = Diagnostics::generate_plain_text_report();
?>
<div class="wpcalibrate-status-wrap">
	<h2><?php esc_html_e( 'System Status & Environment Diagnostics', 'wpcalibrate-menu-control' ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Technical details and compatibility status of your WordPress installation and active environment. Sensitive tokens, keys, and credentials are automatically redacted.', 'wpcalibrate-menu-control' ); ?>
	</p>

	<div class="wpcalibrate-card" style="margin-top: 20px;">
		<table class="widefat striped" role="presentation">
			<thead>
				<tr>
					<th style="width: 300px;"><?php esc_html_e( 'System Parameter', 'wpcalibrate-menu-control' ); ?></th>
					<th><?php esc_html_e( 'Detected Value', 'wpcalibrate-menu-control' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $diag_data as $param => $val ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $param ); ?></strong></td>
						<td><code><?php echo esc_html( (string) $val ); ?></code></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<div class="wpcalibrate-card" style="margin-top: 20px;">
		<h3><?php esc_html_e( 'Diagnostic Report for Support', 'wpcalibrate-menu-control' ); ?></h3>
		<p class="description"><?php esc_html_e( 'Copy and share this report when contacting WPCalibrate Support.', 'wpcalibrate-menu-control' ); ?></p>
		
		<textarea id="wpcalibrate_diagnostic_text" class="widefat code" rows="12" readonly style="margin-top: 8px;"><?php echo esc_textarea( $plain_txt ); ?></textarea>
		
		<p style="margin-top: 10px;">
			<button type="button" class="button button-secondary" onclick="navigator.clipboard.writeText(document.getElementById('wpcalibrate_diagnostic_text').value); alert('<?php echo esc_js( __( 'Diagnostic report copied to clipboard!', 'wpcalibrate-menu-control' ) ); ?>');">
				<?php esc_html_e( 'Copy Diagnostic Report', 'wpcalibrate-menu-control' ); ?>
			</button>
		</p>
	</div>
</div>
