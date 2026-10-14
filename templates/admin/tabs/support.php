<?php
/**
 * Support tab template.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpcalibrate-support-wrap">
	<h2><?php esc_html_e( 'Help & Support', 'wpcalibrate-menu-control' ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Need assistance, have questions, or want to explore more professional WordPress plugins? Get in touch with our team.', 'wpcalibrate-menu-control' ); ?>
	</p>

	<div class="wpcalibrate-card" style="max-width: 650px; margin-top: 20px;">
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><strong><?php esc_html_e( 'Email Support', 'wpcalibrate-menu-control' ); ?></strong></th>
					<td>
						<a href="mailto:support@wpcalibrate.com" class="wpcalibrate-support-link">
							support@wpcalibrate.com
						</a>
					</td>
				</tr>
				<tr>
					<th scope="row"><strong><?php esc_html_e( 'Official Website', 'wpcalibrate-menu-control' ); ?></strong></th>
					<td>
						<a href="<?php echo esc_url( 'https://wpcalibrate.com' ); ?>" target="_blank" rel="noopener noreferrer" class="wpcalibrate-support-link">
							https://wpcalibrate.com
						</a>
					</td>
				</tr>
				<tr>
					<th scope="row"><strong><?php esc_html_e( 'Plugin Marketplace', 'wpcalibrate-menu-control' ); ?></strong></th>
					<td>
						<a href="<?php echo esc_url( 'https://marketplace.wpcalibrate.com/' ); ?>" target="_blank" rel="noopener noreferrer" class="wpcalibrate-support-link">
							https://marketplace.wpcalibrate.com/
						</a>
					</td>
				</tr>
				<tr>
					<th scope="row"><strong><?php esc_html_e( 'Phone & WhatsApp', 'wpcalibrate-menu-control' ); ?></strong></th>
					<td>
						<a href="tel:+447474795976" class="wpcalibrate-support-link">
							+447474795976
						</a>
						<span class="description" style="margin-left: 8px;">(<?php esc_html_e( 'Available on WhatsApp', 'wpcalibrate-menu-control' ); ?>)</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
