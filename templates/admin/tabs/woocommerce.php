<?php
/**
 * WooCommerce settings and dynamic items tab template.
 *
 * @package WPCalibrate\MenuControl
 * @var array<string, mixed> $all_options
 */

declare(strict_types=1);

use WPCalibrate\MenuControl\Support\Environment;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_wc_active = Environment::is_woocommerce_active();
$wc_version   = Environment::get_woocommerce_version();
$hpos_enabled = Environment::is_hpos_enabled();
?>
<div class="wpcalibrate-woocommerce-tab">
	<h2><?php esc_html_e( 'WooCommerce Dynamic Elements', 'wpcalibrate-menu-control' ); ?></h2>

	<div class="wpcalibrate-card">
		<h3><?php esc_html_e( 'WooCommerce Integration Status', 'wpcalibrate-menu-control' ); ?></h3>
		<table class="widefat striped" style="max-width: 650px;" role="presentation">
			<tbody>
				<tr>
					<td><strong><?php esc_html_e( 'WooCommerce Active', 'wpcalibrate-menu-control' ); ?></strong></td>
					<td>
						<?php if ( $is_wc_active ) : ?>
							<span class="wpcalibrate-status-badge wpcalibrate-status-ok"><?php echo esc_html( sprintf( __( 'Active (v%s)', 'wpcalibrate-menu-control' ), $wc_version ) ); ?></span>
						<?php else : ?>
							<span class="wpcalibrate-status-badge wpcalibrate-status-inactive"><?php esc_html_e( 'Not Installed / Inactive', 'wpcalibrate-menu-control' ); ?></span>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'High-Performance Order Storage (HPOS)', 'wpcalibrate-menu-control' ); ?></strong></td>
					<td>
						<?php if ( $hpos_enabled ) : ?>
							<span class="wpcalibrate-status-badge wpcalibrate-status-ok"><?php esc_html_e( 'Enabled & Supported', 'wpcalibrate-menu-control' ); ?></span>
						<?php else : ?>
							<span><?php esc_html_e( 'Disabled or Standard Tables in Use', 'wpcalibrate-menu-control' ); ?></span>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Cart & Checkout Blocks Compatibility', 'wpcalibrate-menu-control' ); ?></strong></td>
					<td><span class="wpcalibrate-status-badge wpcalibrate-status-ok"><?php esc_html_e( 'Declared & Supported', 'wpcalibrate-menu-control' ); ?></span></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="wpcalibrate-card" style="margin-top: 15px;">
		<h3><?php esc_html_e( 'Dynamic Menu Item Shortcodes & Special URLs', 'wpcalibrate-menu-control' ); ?></h3>
		<p><?php esc_html_e( 'In Appearance > Menus, add a Custom Link item using the special URLs and placeholders below. WPCalibrate Menu Control dynamically renders live WooCommerce data with AJAX cart fragment synchronization:', 'wpcalibrate-menu-control' ); ?></p>

		<table class="widefat striped" style="max-width: 750px;" role="presentation">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Purpose', 'wpcalibrate-menu-control' ); ?></th>
					<th><?php esc_html_e( 'Custom Link URL', 'wpcalibrate-menu-control' ); ?></th>
					<th><?php esc_html_e( 'Link Text Placeholders', 'wpcalibrate-menu-control' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><strong><?php esc_html_e( 'Cart with Live Count', 'wpcalibrate-menu-control' ); ?></strong></td>
					<td><code>#wpcalibrate-wc-cart</code></td>
					<td><code>Cart ({cart_count})</code></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Cart with Subtotal', 'wpcalibrate-menu-control' ); ?></strong></td>
					<td><code>#wpcalibrate-wc-cart</code></td>
					<td><code>Cart: {cart_total}</code></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'My Account Page', 'wpcalibrate-menu-control' ); ?></strong></td>
					<td><code>#wpcalibrate-wc-account</code></td>
					<td><code>My Account</code> or <code>Hello, {user_name}</code></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Dynamic Login / Logout', 'wpcalibrate-menu-control' ); ?></strong></td>
					<td><code>#wpcalibrate-wc-login-logout</code></td>
					<td><em><?php esc_html_e( 'Automatically flips between Log In & Log Out', 'wpcalibrate-menu-control' ); ?></em></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Checkout Page', 'wpcalibrate-menu-control' ); ?></strong></td>
					<td><code>#wpcalibrate-wc-checkout</code></td>
					<td><code>Checkout</code></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
