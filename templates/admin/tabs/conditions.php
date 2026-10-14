<?php
/**
 * Conditions tab template.
 *
 * @package WPCalibrate\MenuControl
 * @var array<string, mixed> $all_options
 */

declare(strict_types=1);

use WPCalibrate\MenuControl\Support\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpcalibrate-info-panel">
	<h2><?php esc_html_e( 'Conditional Visibility Rules', 'wpcalibrate-menu-control' ); ?></h2>
	<p>
		<?php esc_html_e( 'The conditional visibility engine allows you to specify exact rules for each menu item in Appearance > Menus.', 'wpcalibrate-menu-control' ); ?>
		<strong><?php esc_html_e( 'Remember:', 'wpcalibrate-menu-control' ); ?></strong>
		<?php esc_html_e( 'Hiding a navigation link is for presentation and user experience; it does not replace page-level permission checks or resource authorization.', 'wpcalibrate-menu-control' ); ?>
	</p>

	<div class="wpcalibrate-card">
		<h3><?php esc_html_e( 'Active Site Timezone', 'wpcalibrate-menu-control' ); ?></h3>
		<p>
			<?php
			printf(
				/* translators: 1: Current timezone, 2: Current site time */
				esc_html__( 'All schedule start and end rules strictly use your WordPress site timezone: %1$s (Current time: %2$s).', 'wpcalibrate-menu-control' ),
				'<strong>' . esc_html( Helpers::get_site_timezone_string() ) . '</strong>',
				'<code>' . esc_html( Helpers::get_current_site_datetime()->format( 'Y-m-d H:i:s' ) ) . '</code>'
			);
			?>
		</p>
	</div>

	<div class="wpcalibrate-card" style="margin-top: 15px;">
		<h3><?php esc_html_e( 'Available Rule Types in Menu Editor', 'wpcalibrate-menu-control' ); ?></h3>
		<ul class="wpcalibrate-bullet-list">
			<li><strong><?php esc_html_e( 'User Status:', 'wpcalibrate-menu-control' ); ?></strong> <?php esc_html_e( 'Logged In vs Logged Out visitors.', 'wpcalibrate-menu-control' ); ?></li>
			<li><strong><?php esc_html_e( 'User Role:', 'wpcalibrate-menu-control' ); ?></strong> <?php esc_html_e( 'Target specific roles (e.g. Administrator, Customer, Subscriber). Supports multiple comma-separated roles.', 'wpcalibrate-menu-control' ); ?></li>
			<li><strong><?php esc_html_e( 'Device Group:', 'wpcalibrate-menu-control' ); ?></strong> <?php esc_html_e( 'Desktop, Tablet, or Mobile. (Presentation-oriented).', 'wpcalibrate-menu-control' ); ?></li>
			<li><strong><?php esc_html_e( 'Page Context:', 'wpcalibrate-menu-control' ); ?></strong> <?php esc_html_e( 'Front Page, Blog Home, Singular Post/Page, Archive, Search, or 404.', 'wpcalibrate-menu-control' ); ?></li>
			<li><strong><?php esc_html_e( 'Post Type:', 'wpcalibrate-menu-control' ); ?></strong> <?php esc_html_e( 'Target post types (e.g. post, page, product).', 'wpcalibrate-menu-control' ); ?></li>
			<li><strong><?php esc_html_e( 'URL / Path Match:', 'wpcalibrate-menu-control' ); ?></strong> <?php esc_html_e( 'Match current request path with "is", "contains", "starts_with", or "is_not".', 'wpcalibrate-menu-control' ); ?></li>
			<li><strong><?php esc_html_e( 'Schedule (Start / End):', 'wpcalibrate-menu-control' ); ?></strong> <?php esc_html_e( 'Set inclusive start dates/times and expiration dates/times.', 'wpcalibrate-menu-control' ); ?></li>
			<li><strong><?php esc_html_e( 'WooCommerce Context & Customer:', 'wpcalibrate-menu-control' ); ?></strong> <?php esc_html_e( 'Shop page, Cart, Checkout, My Account, or Customers with order history.', 'wpcalibrate-menu-control' ); ?></li>
		</ul>
	</div>

	<div class="wpcalibrate-card" style="margin-top: 15px;">
		<h3><?php esc_html_e( 'Logic Modes', 'wpcalibrate-menu-control' ); ?></h3>
		<p>
			<strong><?php esc_html_e( 'ALL (AND):', 'wpcalibrate-menu-control' ); ?></strong> <?php esc_html_e( 'Every rule in the group must evaluate to true for the item to appear.', 'wpcalibrate-menu-control' ); ?><br />
			<strong><?php esc_html_e( 'ANY (OR):', 'wpcalibrate-menu-control' ); ?></strong> <?php esc_html_e( 'If any one rule in the group evaluates to true, the item is displayed.', 'wpcalibrate-menu-control' ); ?>
		</p>
	</div>
</div>
