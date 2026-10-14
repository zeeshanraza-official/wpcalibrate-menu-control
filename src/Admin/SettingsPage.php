<?php
/**
 * Admin settings page renderer.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Admin;

use WPCalibrate\MenuControl\Support\Capabilities;
use WPCalibrate\MenuControl\Support\Helpers;

/**
 * Class SettingsPage
 */
final class SettingsPage {

	/**
	 * Settings handler.
	 */
	private Settings $settings;

	/**
	 * Allowed tabs.
	 *
	 * @var array<string, string>
	 */
	public const TABS = array(
		'overview'      => 'Overview',
		'general'       => 'General',
		'conditions'    => 'Conditions',
		'woocommerce'   => 'WooCommerce',
		'megamenu'      => 'Mega Menu',
		'styling'       => 'Styling',
		'import-export' => 'Import / Export',
		'license'       => 'License',
		'status'        => 'Status & Diagnostics',
		'support'       => 'Support',
	);

	/**
	 * Constructor.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Get current active tab.
	 */
	public function get_current_tab(): string {
		$tab = isset( $_GET['tab'] ) ? sanitize_key( (string) $_GET['tab'] ) : 'overview';
		return array_key_exists( $tab, self::TABS ) ? $tab : 'overview';
	}

	/**
	 * Render the full settings page.
	 */
	public function render(): void {
		if ( ! Capabilities::current_user_can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'wpcalibrate-menu-control' ) );
		}

		$current_tab = $this->get_current_tab();
		$all_options = $this->settings->get_all();
		$tabs        = self::TABS;

		// Include main template.
		$template_path = Helpers::get_plugin_path( 'templates/admin/settings.php' );
		if ( file_exists( $template_path ) ) {
			include $template_path;
		}
	}
}
