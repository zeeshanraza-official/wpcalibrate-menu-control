<?php
/**
 * Script and style asset management.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Admin;

use WPCalibrate\MenuControl\Support\Helpers;

/**
 * Class Assets
 */
final class Assets {

	private Settings $settings;

	/**
	 * Constructor.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register asset hooks.
	 */
	public function register(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
	}

	/**
	 * Enqueue admin styles and scripts strictly on relevant screens.
	 *
	 * @param string $hook_suffix
	 */
	public function enqueue_admin_assets( string $hook_suffix ): void {
		$is_settings_page = ( isset( $_GET['page'] ) && AdminMenu::SUBMENU_SLUG === $_GET['page'] );
		$is_menu_editor   = ( 'nav-menus.php' === $hook_suffix );

		if ( ! $is_settings_page && ! $is_menu_editor ) {
			return;
		}

		$version = defined( 'WPCALIBRATE_MENU_CONTROL_VERSION' ) ? WPCALIBRATE_MENU_CONTROL_VERSION : '1.0.0';

		// Enqueue Dashicons.
		wp_enqueue_style( 'dashicons' );

		// Admin CSS.
		wp_enqueue_style(
			'wpcalibrate-menu-control-admin',
			Helpers::get_plugin_url( 'assets/css/admin.css' ),
			array( 'dashicons' ),
			$version
		);

		if ( $is_menu_editor ) {
			// Menu editor script.
			wp_enqueue_script(
				'wpcalibrate-menu-control-editor',
				Helpers::get_plugin_url( 'assets/js/admin-menu-editor.js' ),
				array( 'jquery' ),
				$version,
				true
			);

			wp_localize_script(
				'wpcalibrate-menu-control-editor',
				'wpcalibrateMenuControlData',
				array(
					'timezone' => Helpers::get_site_timezone_string(),
					'i18n'     => array(
						'addRule'    => __( 'Add Rule', 'wpcalibrate-menu-control' ),
						'removeRule' => __( 'Remove', 'wpcalibrate-menu-control' ),
					),
				)
			);
		}
	}

	/**
	 * Enqueue frontend CSS and JS.
	 */
	public function enqueue_frontend_assets(): void {
		$options = $this->settings->get_all();

		// If frontend CSS is disabled by administrator, do not enqueue stylesheet.
		if ( ! empty( $options['disable_frontend_css'] ) ) {
			return;
		}

		$version = defined( 'WPCALIBRATE_MENU_CONTROL_VERSION' ) ? WPCALIBRATE_MENU_CONTROL_VERSION : '1.0.0';

		wp_enqueue_style(
			'wpcalibrate-menu-control-frontend',
			Helpers::get_plugin_url( 'assets/css/frontend.css' ),
			array(),
			$version
		);

		// Dynamic CSS custom properties.
		$bp     = (int) ( $options['default_breakpoint'] ?? 768 );
		$isp    = (int) ( $options['icon_spacing'] ?? 6 );
		$br     = (int) ( $options['badge_border_radius'] ?? 4 );
		$mw     = (int) ( $options['mega_menu_dropdown_width'] ?? 1140 );

		$custom_css = sprintf(
			':root {
				--wpcalibrate-breakpoint: %dpx;
				--wpcalibrate-icon-spacing: %dpx;
				--wpcalibrate-badge-radius: %dpx;
				--wpcalibrate-mega-width: %dpx;
			}',
			$bp,
			$isp,
			$br,
			$mw
		);

		wp_add_inline_style( 'wpcalibrate-menu-control-frontend', $custom_css );

		// Frontend interactive script (mega menu keyboard/touch & non-clickable heading helper).
		wp_enqueue_script(
			'wpcalibrate-menu-control-frontend',
			Helpers::get_plugin_url( 'assets/js/frontend.js' ),
			array(),
			$version,
			true
		);
	}
}
