<?php
/**
 * Styling and responsive settings tab template.
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
	<input type="hidden" name="current_tab" value="styling" />
	<input type="hidden" name="field_present_disable_frontend_css" value="1" />

	<h2><?php esc_html_e( 'Styling & Responsive Settings', 'wpcalibrate-menu-control' ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Configure scoped CSS variables and responsive breakpoints. WPCalibrate Menu Control preserves your active theme typography and color scheme by default.', 'wpcalibrate-menu-control' ); ?>
	</p>

	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row">
					<label for="wpcalibrate_default_breakpoint"><?php esc_html_e( 'Mobile / Tablet Breakpoint (px)', 'wpcalibrate-menu-control' ); ?></label>
				</th>
				<td>
					<input type="number"
						id="wpcalibrate_default_breakpoint"
						name="wpcalibrate_menu_control[default_breakpoint]"
						value="<?php echo esc_attr( (string) ( $all_options['default_breakpoint'] ?? 768 ) ); ?>"
						min="320"
						max="1920"
						step="1"
						class="small-text" />
					<p class="description"><?php esc_html_e( 'Screens narrower than this width switch into responsive mobile accordion behavior (default: 768px).', 'wpcalibrate-menu-control' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="wpcalibrate_icon_spacing"><?php esc_html_e( 'Icon Spacing (px)', 'wpcalibrate-menu-control' ); ?></label>
				</th>
				<td>
					<input type="number"
						id="wpcalibrate_icon_spacing"
						name="wpcalibrate_menu_control[icon_spacing]"
						value="<?php echo esc_attr( (string) ( $all_options['icon_spacing'] ?? 6 ) ); ?>"
						min="0"
						max="50"
						class="small-text" />
					<p class="description"><?php esc_html_e( 'Spacing between the icon and the menu item text label (default: 6px).', 'wpcalibrate-menu-control' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="wpcalibrate_badge_border_radius"><?php esc_html_e( 'Badge Border Radius (px)', 'wpcalibrate-menu-control' ); ?></label>
				</th>
				<td>
					<input type="number"
						id="wpcalibrate_badge_border_radius"
						name="wpcalibrate_menu_control[badge_border_radius]"
						value="<?php echo esc_attr( (string) ( $all_options['badge_border_radius'] ?? 4 ) ); ?>"
						min="0"
						max="30"
						class="small-text" />
					<p class="description"><?php esc_html_e( 'Corner curvature for text badges (default: 4px).', 'wpcalibrate-menu-control' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Disable Plugin Frontend CSS', 'wpcalibrate-menu-control' ); ?></th>
				<td>
					<label>
						<input type="checkbox"
							name="wpcalibrate_menu_control[disable_frontend_css]"
							value="1"
							<?php checked( ! empty( $all_options['disable_frontend_css'] ) ); ?> />
						<?php esc_html_e( 'Check this to disable loading the plugin frontend stylesheet entirely (for theme developers who provide complete custom menu styling).', 'wpcalibrate-menu-control' ); ?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>

	<?php submit_button( __( 'Save Styling Settings', 'wpcalibrate-menu-control' ) ); ?>
</form>
