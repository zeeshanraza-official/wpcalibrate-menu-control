<?php
/**
 * Overview tab template: Purpose, How to Use, and Methods.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

use WPCalibrate\MenuControl\Admin\AdminMenu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$general_tab_url    = add_query_arg( array( 'page' => AdminMenu::SUBMENU_SLUG, 'tab' => 'general' ), admin_url( 'admin.php' ) );
$conditions_tab_url = add_query_arg( array( 'page' => AdminMenu::SUBMENU_SLUG, 'tab' => 'conditions' ), admin_url( 'admin.php' ) );
$wc_tab_url         = add_query_arg( array( 'page' => AdminMenu::SUBMENU_SLUG, 'tab' => 'woocommerce' ), admin_url( 'admin.php' ) );
$menus_admin_url    = admin_url( 'nav-menus.php' );
?>
<div class="wpcalibrate-overview-wrap">
	<!-- Hero Section: Purpose -->
	<div class="wpcalibrate-hero-card" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); color: #ffffff; padding: 28px; border-radius: 8px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
		<h2 style="color: #ffffff; margin-top: 0; font-size: 24px; font-weight: 700;">
			<?php esc_html_e( 'Take Complete Control of Your Navigation', 'wpcalibrate-menu-control' ); ?>
		</h2>
		<p style="font-size: 15px; line-height: 1.6; max-width: 820px; color: #cbd5e1; margin-bottom: 20px;">
			<?php esc_html_e( 'WPCalibrate Menu Control is designed to solve a fundamental navigation challenge: controlling who sees each menu item, when they see it, and how it appears. It brings enterprise-grade conditional visibility, visual badges, icons, subtitles, and lightweight mega menus into WordPress without modifying active theme files or requiring heavy page builders.', 'wpcalibrate-menu-control' ); ?>
		</p>
		<div style="display: flex; gap: 12px; flex-wrap: wrap;">
			<a href="<?php echo esc_url( $menus_admin_url ); ?>" class="button button-primary button-hero" style="background: #3b82f6; border-color: #2563eb; color: #ffffff;">
				<?php esc_html_e( 'Configure Appearance > Menus', 'wpcalibrate-menu-control' ); ?> &rarr;
			</a>
			<a href="<?php echo esc_url( $general_tab_url ); ?>" class="button button-secondary button-hero" style="background: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.3); color: #ffffff;">
				<?php esc_html_e( 'Feature Settings', 'wpcalibrate-menu-control' ); ?>
			</a>
		</div>
	</div>

	<!-- 4 Core Pillars Grid -->
	<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 28px;">
		<div class="wpcalibrate-card" style="border-top: 3px solid #3b82f6;">
			<h4 style="margin: 0 0 8px 0; font-size: 16px; color: #0f172a;">🎯 <?php esc_html_e( 'Conditional Visibility', 'wpcalibrate-menu-control' ); ?></h4>
			<p class="description" style="margin: 0; line-height: 1.5;">
				<?php esc_html_e( 'Show or hide navigation links dynamically based on user login status, roles, devices (desktop/tablet/mobile), page contexts, URL patterns, or site timezone schedules.', 'wpcalibrate-menu-control' ); ?>
			</p>
		</div>
		<div class="wpcalibrate-card" style="border-top: 3px solid #10b981;">
			<h4 style="margin: 0 0 8px 0; font-size: 16px; color: #0f172a;">🎨 <?php esc_html_e( 'Visual Enhancements', 'wpcalibrate-menu-control' ); ?></h4>
			<p class="description" style="margin: 0; line-height: 1.5;">
				<?php esc_html_e( 'Attach Dashicons or safe SVGs, callout badges (New, Sale, Hot) with 8 semantic style variants, subtitles, CTA highlight styles, and non-clickable parent headings.', 'wpcalibrate-menu-control' ); ?>
			</p>
		</div>
		<div class="wpcalibrate-card" style="border-top: 3px solid #8b5cf6;">
			<h4 style="margin: 0 0 8px 0; font-size: 16px; color: #0f172a;">🛍️ <?php esc_html_e( 'WooCommerce Dynamic', 'wpcalibrate-menu-control' ); ?></h4>
			<p class="description" style="margin: 0; line-height: 1.5;">
				<?php esc_html_e( 'Real-time AJAX cart items with live item counts and subtotals, smart My Account links, and auto-flipping Login/Logout links without hard-coded endpoints.', 'wpcalibrate-menu-control' ); ?>
			</p>
		</div>
		<div class="wpcalibrate-card" style="border-top: 3px solid #f59e0b;">
			<h4 style="margin: 0 0 8px 0; font-size: 16px; color: #0f172a;">📱 <?php esc_html_e( 'Lightweight Mega Menu', 'wpcalibrate-menu-control' ); ?></h4>
			<p class="description" style="margin: 0; line-height: 1.5;">
				<?php esc_html_e( 'Convert any top-level menu item into a 1–6 column grid dropdown on desktop, while automatically collapsing into an accessible touch and keyboard accordion on mobile.', 'wpcalibrate-menu-control' ); ?>
			</p>
		</div>
	</div>

	<!-- Section: How to Use -->
	<div class="wpcalibrate-card" style="margin-bottom: 24px; padding: 24px;">
		<h3 style="margin-top: 0; font-size: 18px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
			📖 <?php esc_html_e( 'How to Use WPCalibrate Menu Control (Quick Start Guide)', 'wpcalibrate-menu-control' ); ?>
		</h3>
		<div style="display: flex; flex-direction: column; gap: 16px; margin-top: 16px;">
			<div style="display: flex; gap: 16px; align-items: flex-start;">
				<div style="background: #3b82f6; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">1</div>
				<div>
					<strong><?php esc_html_e( 'Enable Needed Modules', 'wpcalibrate-menu-control' ); ?></strong>
					<p class="description" style="margin: 4px 0 0 0;">
						<?php
						printf(
							/* translators: %s: Link to general tab */
							esc_html__( 'Open the %s tab to toggle features like Icons, Badges, Mega Menus, or WooCommerce elements on or off.', 'wpcalibrate-menu-control' ),
							'<a href="' . esc_url( $general_tab_url ) . '">' . esc_html__( 'General Settings', 'wpcalibrate-menu-control' ) . '</a>'
						);
						?>
					</p>
				</div>
			</div>

			<div style="display: flex; gap: 16px; align-items: flex-start;">
				<div style="background: #3b82f6; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">2</div>
				<div>
					<strong><?php esc_html_e( 'Open Appearance > Menus', 'wpcalibrate-menu-control' ); ?></strong>
					<p class="description" style="margin: 4px 0 0 0;">
						<?php
						printf(
							/* translators: %s: Link to Appearance Menus */
							esc_html__( 'Go to %s. Expand any menu item to see the dedicated "Menu Control Settings" panel.', 'wpcalibrate-menu-control' ),
							'<a href="' . esc_url( $menus_admin_url ) . '">' . esc_html__( 'Appearance > Menus', 'wpcalibrate-menu-control' ) . '</a>'
						);
						?>
					</p>
				</div>
			</div>

			<div style="display: flex; gap: 16px; align-items: flex-start;">
				<div style="background: #3b82f6; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">3</div>
				<div>
					<strong><?php esc_html_e( 'Apply Visual Enhancements', 'wpcalibrate-menu-control' ); ?></strong>
					<p class="description" style="margin: 4px 0 0 0;">
						<?php esc_html_e( 'Click "Configure" on any item. Enter a Dashicon slug (e.g. dashicons-star-filled), add a badge (e.g. "SALE" with style "Danger"), enter a subtitle, or mark parent items as "Non-Clickable Heading".', 'wpcalibrate-menu-control' ); ?>
					</p>
				</div>
			</div>

			<div style="display: flex; gap: 16px; align-items: flex-start;">
				<div style="background: #3b82f6; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">4</div>
				<div>
					<strong><?php esc_html_e( 'Configure Conditional Visibility Rules', 'wpcalibrate-menu-control' ); ?></strong>
					<p class="description" style="margin: 4px 0 0 0;">
						<?php esc_html_e( 'Check "Enable conditional visibility for this menu item". Choose whether ALL rules (AND) or ANY rule (OR) must match. Add rules such as User Status is Logged In, or User Role is Customer.', 'wpcalibrate-menu-control' ); ?>
					</p>
				</div>
			</div>

			<div style="display: flex; gap: 16px; align-items: flex-start;">
				<div style="background: #3b82f6; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">5</div>
				<div>
					<strong><?php esc_html_e( 'Create a Mega Menu (Optional)', 'wpcalibrate-menu-control' ); ?></strong>
					<p class="description" style="margin: 4px 0 0 0;">
						<?php esc_html_e( 'On any top-level item, check "Enable Mega Menu dropdown" and choose 1 to 6 columns. Its immediate child items will serve as column headings.', 'wpcalibrate-menu-control' ); ?>
					</p>
				</div>
			</div>

			<div style="display: flex; gap: 16px; align-items: flex-start;">
				<div style="background: #3b82f6; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">6</div>
				<div>
					<strong><?php esc_html_e( 'Add Live WooCommerce Items (Optional)', 'wpcalibrate-menu-control' ); ?></strong>
					<p class="description" style="margin: 4px 0 0 0;">
						<?php esc_html_e( 'Add a Custom Link with URL #wpcalibrate-wc-cart and Link Text "Cart ({cart_count})". WPCalibrate Menu Control dynamically updates the item count in real-time via AJAX fragments!', 'wpcalibrate-menu-control' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Section: Method & Architectural Principles -->
	<div class="wpcalibrate-card" style="padding: 24px;">
		<h3 style="margin-top: 0; font-size: 18px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
			⚙️ <?php esc_html_e( 'Method & Architectural Principles', 'wpcalibrate-menu-control' ); ?>
		</h3>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 16px;">
			<div>
				<h4 style="margin: 0 0 6px 0; color: #0f172a;">⚡ <?php esc_html_e( 'Single-Query Performance (Zero N+1)', 'wpcalibrate-menu-control' ); ?></h4>
				<p class="description" style="margin: 0; line-height: 1.5;">
					<?php esc_html_e( 'Unlike naive plugins that query postmeta on every item render, WPCalibrate Menu Control batch-primes all menu item metadata in a single query (update_meta_cache) and caches rule evaluation per request.', 'wpcalibrate-menu-control' ); ?>
				</p>
			</div>

			<div>
				<h4 style="margin: 0 0 6px 0; color: #0f172a;">🛡️ <?php esc_html_e( 'Presentation vs Security Notice', 'wpcalibrate-menu-control' ); ?></h4>
				<p class="description" style="margin: 0; line-height: 1.5;">
					<?php esc_html_e( 'Conditional menu visibility is a presentation and user experience control. Hiding a link simplifies navigation, but does not replace WordPress permissions. Destination resources remain governed by WordPress authorization.', 'wpcalibrate-menu-control' ); ?>
				</p>
			</div>

			<div>
				<h4 style="margin: 0 0 6px 0; color: #0f172a;">♿ <?php esc_html_e( 'Accessibility & Keyboard Navigation', 'wpcalibrate-menu-control' ); ?></h4>
				<p class="description" style="margin: 0; line-height: 1.5;">
					<?php esc_html_e( 'All decorative icons include aria-hidden="true". Non-clickable headings avoid invalid javascript:void(0) links and support Space/Enter keyboard toggling. Mega menus close gracefully on Escape.', 'wpcalibrate-menu-control' ); ?>
				</p>
			</div>

			<div>
				<h4 style="margin: 0 0 6px 0; color: #0f172a;">🔌 <?php esc_html_e( 'Developer Extensibility', 'wpcalibrate-menu-control' ); ?></h4>
				<p class="description" style="margin: 0; line-height: 1.5;">
					<?php esc_html_e( 'Easily register custom condition providers with the ConditionRegistry via the wpcalibrate_menu_control_register_conditions hook, or filter visibility with wpcalibrate_menu_control_item_visibility.', 'wpcalibrate-menu-control' ); ?>
				</p>
			</div>
		</div>
	</div>
</div>
