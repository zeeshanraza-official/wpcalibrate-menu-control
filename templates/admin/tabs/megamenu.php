<?php
/**
 * Mega Menu tab template.
 *
 * @package WPCalibrate\MenuControl
 * @var array<string, mixed> $all_options
 */

declare(strict_types=1);

use WPCalibrate\MenuControl\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form method="post" action="">
	<?php wp_nonce_field( Settings::NONCE_ACTION, Settings::NONCE_NAME ); ?>
	<input type="hidden" name="wpcalibrate_menu_control_save_settings" value="1" />
	<input type="hidden" name="current_tab" value="megamenu" />

	<h2><?php esc_html_e( 'Lightweight Mega Menus', 'wpcalibrate-menu-control' ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Configure default container dimensions for mega menus. Mega menu mode can be enabled on any top-level menu item in Appearance > Menus.', 'wpcalibrate-menu-control' ); ?>
	</p>

	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row">
					<label for="wpcalibrate_mega_menu_dropdown_width"><?php esc_html_e( 'Full Container Max Width (px)', 'wpcalibrate-menu-control' ); ?></label>
				</th>
				<td>
					<input type="number"
						id="wpcalibrate_mega_menu_dropdown_width"
						name="wpcalibrate_menu_control[mega_menu_dropdown_width]"
						value="<?php echo esc_attr( (string) ( $all_options['mega_menu_dropdown_width'] ?? 1140 ) ); ?>"
						min="400"
						max="2400"
						step="10"
						class="small-text" />
					<p class="description"><?php esc_html_e( 'Maximum width for mega menus configured in "Full Width" container mode (default: 1140px).', 'wpcalibrate-menu-control' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<div class="wpcalibrate-card" style="margin-top: 20px;">
		<h3><?php esc_html_e( 'How Mega Menus Work in Version 1', 'wpcalibrate-menu-control' ); ?></h3>
		<ol class="wpcalibrate-ordered-list">
			<li><?php esc_html_e( 'Go to Appearance > Menus and expand any top-level menu item.', 'wpcalibrate-menu-control' ); ?></li>
			<li><?php esc_html_e( 'Check "Enable Mega Menu dropdown" and choose your desired column layout (1 to 6 columns).', 'wpcalibrate-menu-control' ); ?></li>
			<li><?php esc_html_e( 'Add sub-items beneath the parent. Each immediate child item represents a column.', 'wpcalibrate-menu-control' ); ?></li>
			<li><?php esc_html_e( 'To create column titles, check "Non-Clickable Heading" on child items.', 'wpcalibrate-menu-control' ); ?></li>
			<li><?php esc_html_e( 'On mobile screens, mega menus automatically degrade into accessible, responsive accordions with touch and keyboard support.', 'wpcalibrate-menu-control' ); ?></li>
		</ol>
	</div>

	<?php submit_button( __( 'Save Mega Menu Settings', 'wpcalibrate-menu-control' ) ); ?>
</form>
