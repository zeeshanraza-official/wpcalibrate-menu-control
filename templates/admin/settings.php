<?php
/**
 * Admin settings layout template.
 *
 * @package WPCalibrate\MenuControl
 * @var string               $current_tab
 * @var array<string, mixed> $all_options
 * @var array<string, string>$tabs
 */

declare(strict_types=1);

use WPCalibrate\MenuControl\Support\Helpers;
use WPCalibrate\MenuControl\Admin\AdminMenu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dark_icon_url = Helpers::get_branding_icon_url( 'dark' );
?>
<div class="wrap wpcalibrate-admin-wrap">
	<div class="wpcalibrate-admin-header">
		<div class="wpcalibrate-header-brand">
			<?php if ( ! empty( $dark_icon_url ) ) : ?>
				<img src="<?php echo esc_url( $dark_icon_url ); ?>" alt="WPCalibrate" class="wpcalibrate-header-logo" width="40" height="40" />
			<?php endif; ?>
			<div class="wpcalibrate-header-titles">
				<h1><?php esc_html_e( 'WPCalibrate Menu Control', 'wpcalibrate-menu-control' ); ?> <span class="wpcalibrate-version-tag">v<?php echo esc_html( defined( 'WPCALIBRATE_MENU_CONTROL_VERSION' ) ? WPCALIBRATE_MENU_CONTROL_VERSION : '1.0.0' ); ?></span></h1>
				<p class="wpcalibrate-header-tagline"><?php esc_html_e( 'Advanced conditional navigation, badges, icons, WooCommerce dynamic elements & lightweight mega menus.', 'wpcalibrate-menu-control' ); ?></p>
			</div>
		</div>
	</div>

	<?php settings_errors( 'wpcalibrate_menu_control_notices' ); ?>

	<nav class="nav-tab-wrapper wpcalibrate-nav-tabs" aria-label="<?php esc_attr_e( 'Settings Tabs', 'wpcalibrate-menu-control' ); ?>">
		<?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
			<?php
			$tab_url = add_query_arg(
				array(
					'page' => AdminMenu::SUBMENU_SLUG,
					'tab'  => $tab_key,
				),
				admin_url( 'admin.php' )
			);
			$active_class = ( $current_tab === $tab_key ) ? 'nav-tab-active' : '';
			?>
			<a href="<?php echo esc_url( $tab_url ); ?>" class="nav-tab <?php echo esc_attr( $active_class ); ?>">
				<?php echo esc_html( $tab_label ); ?>
			</a>
		<?php endforeach; ?>
	</nav>

	<div class="wpcalibrate-tab-content">
		<?php
		$tab_file = Helpers::get_plugin_path( 'templates/admin/tabs/' . $current_tab . '.php' );
		if ( file_exists( $tab_file ) ) {
			include $tab_file;
		} else {
			echo '<p>' . esc_html__( 'Tab content not found.', 'wpcalibrate-menu-control' ) . '</p>';
		}
		?>
	</div>
</div>
