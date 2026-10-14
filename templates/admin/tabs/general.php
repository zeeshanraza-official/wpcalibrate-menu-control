<?php
/**
 * General settings tab.
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
	<input type="hidden" name="current_tab" value="general" />

	<input type="hidden" name="field_present_enable_icons" value="1" />
	<input type="hidden" name="field_present_enable_badges" value="1" />
	<input type="hidden" name="field_present_enable_subtitles" value="1" />
	<input type="hidden" name="field_present_enable_non_clickable" value="1" />
	<input type="hidden" name="field_present_enable_conditions" value="1" />
	<input type="hidden" name="field_present_enable_mega_menu" value="1" />
	<input type="hidden" name="field_present_enable_woocommerce" value="1" />
	<input type="hidden" name="field_present_enable_block_nav" value="1" />
	<input type="hidden" name="field_present_enable_debug_logging" value="1" />

	<h2><?php esc_html_e( 'Feature Modules', 'wpcalibrate-menu-control' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Enable or disable individual plugin capabilities to keep your menu management lightweight and focused.', 'wpcalibrate-menu-control' ); ?></p>

	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row"><?php esc_html_e( 'Menu Item Icons', 'wpcalibrate-menu-control' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="wpcalibrate_menu_control[enable_icons]" value="1" <?php checked( ! empty( $all_options['enable_icons'] ) ); ?> />
						<?php esc_html_e( 'Allow assigning Dashicons and safe SVG icons to menu items', 'wpcalibrate-menu-control' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Badges & Callouts', 'wpcalibrate-menu-control' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="wpcalibrate_menu_control[enable_badges]" value="1" <?php checked( ! empty( $all_options['enable_badges'] ) ); ?> />
						<?php esc_html_e( 'Enable custom text badges (e.g. New, Hot, Sale) with semantic style variants', 'wpcalibrate-menu-control' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Subtitles & Descriptions', 'wpcalibrate-menu-control' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="wpcalibrate_menu_control[enable_subtitles]" value="1" <?php checked( ! empty( $all_options['enable_subtitles'] ) ); ?> />
						<?php esc_html_e( 'Enable secondary subtitle text rendered beneath menu item labels', 'wpcalibrate-menu-control' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Non-Clickable Headings', 'wpcalibrate-menu-control' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="wpcalibrate_menu_control[enable_non_clickable]" value="1" <?php checked( ! empty( $all_options['enable_non_clickable'] ) ); ?> />
						<?php esc_html_e( 'Allow menu items to serve as non-clickable semantic headings without link navigation', 'wpcalibrate-menu-control' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Conditional Visibility Engine', 'wpcalibrate-menu-control' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="wpcalibrate_menu_control[enable_conditions]" value="1" <?php checked( ! empty( $all_options['enable_conditions'] ) ); ?> />
						<?php esc_html_e( 'Enable rule-based visibility engine (user state, roles, device, schedule, URLs)', 'wpcalibrate-menu-control' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Lightweight Mega Menus', 'wpcalibrate-menu-control' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="wpcalibrate_menu_control[enable_mega_menu]" value="1" <?php checked( ! empty( $all_options['enable_mega_menu'] ) ); ?> />
						<?php esc_html_e( 'Enable mega menu grid dropdowns for classic WordPress top-level nav items', 'wpcalibrate-menu-control' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'WooCommerce Dynamic Items', 'wpcalibrate-menu-control' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="wpcalibrate_menu_control[enable_woocommerce]" value="1" <?php checked( ! empty( $all_options['enable_woocommerce'] ) ); ?> />
						<?php esc_html_e( 'Enable dynamic cart link, live cart item count, subtotal, and account items', 'wpcalibrate-menu-control' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Navigation Block Enhancement', 'wpcalibrate-menu-control' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="wpcalibrate_menu_control[enable_block_nav]" value="1" <?php checked( ! empty( $all_options['enable_block_nav'] ) ); ?> />
						<?php esc_html_e( 'Apply safe frontend filtering and icon/badge enhancements to core Navigation block links', 'wpcalibrate-menu-control' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Debug Logging', 'wpcalibrate-menu-control' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="wpcalibrate_menu_control[enable_debug_logging]" value="1" <?php checked( ! empty( $all_options['enable_debug_logging'] ) ); ?> />
						<?php esc_html_e( 'Log detailed rule evaluation errors and diagnostic events to WordPress debug log', 'wpcalibrate-menu-control' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Data Retention on Uninstall', 'wpcalibrate-menu-control' ); ?></th>
				<td>
					<select name="wpcalibrate_menu_control[uninstall_data_retention]">
						<option value="retain" <?php selected( ( $all_options['uninstall_data_retention'] ?? 'retain' ), 'retain' ); ?>>
							<?php esc_html_e( 'Retain all plugin settings and menu metadata (Recommended)', 'wpcalibrate-menu-control' ); ?>
						</option>
						<option value="delete" <?php selected( ( $all_options['uninstall_data_retention'] ?? 'retain' ), 'delete' ); ?>>
							<?php esc_html_e( 'Delete all plugin settings and metadata when uninstalled', 'wpcalibrate-menu-control' ); ?>
						</option>
					</select>
					<p class="description"><?php esc_html_e( 'Controls whether menu configurations and options are preserved if this plugin is uninstalled.', 'wpcalibrate-menu-control' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<?php submit_button( __( 'Save Changes', 'wpcalibrate-menu-control' ) ); ?>
</form>
