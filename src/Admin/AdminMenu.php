<?php
/**
 * Cooperative WPCalibrate admin menu registration.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Admin;

use WPCalibrate\MenuControl\Support\Capabilities;
use WPCalibrate\MenuControl\Support\Helpers;

/**
 * Class AdminMenu
 */
final class AdminMenu {

	public const PARENT_SLUG  = 'wpcalibrate';
	public const SUBMENU_SLUG = 'wpcalibrate-menu-control';

	/**
	 * Settings page renderer instance.
	 */
	private SettingsPage $settings_page;

	/**
	 * Constructor.
	 */
	public function __construct( SettingsPage $settings_page ) {
		$this->settings_page = $settings_page;
	}

	/**
	 * Register admin menu hooks.
	 */
	public function register(): void {
		// Priority 9 allows cooperative registration before other late submenus.
		add_action( 'admin_menu', array( $this, 'register_menu' ), 9 );
		add_action( 'admin_head', array( $this, 'output_menu_icon_styles' ) );
	}

	/**
	 * Register the cooperative parent and plugin submenu.
	 */
	public function register_menu(): void {
		$capability = Capabilities::DEFAULT_CAPABILITY;

		if ( ! current_user_can( $capability ) ) {
			return;
		}

		global $admin_page_hooks;

		// Cooperative parent menu registration: only register if not already registered by another WPCalibrate plugin.
		if ( empty( $admin_page_hooks[ self::PARENT_SLUG ] ) ) {
			$icon_url = Helpers::get_branding_icon_url( 'white' );
			add_menu_page(
				'WPCalibrate',
				'WPCalibrate',
				$capability,
				self::PARENT_SLUG,
				array( $this, 'render_parent_page' ),
				! empty( $icon_url ) ? esc_url( $icon_url ) : 'dashicons-menu-alt3',
				59
			);
		}

		// Register WPCalibrate Menu Control submenu.
		add_submenu_page(
			self::PARENT_SLUG,
			__( 'WPCalibrate Menu Control', 'wpcalibrate-menu-control' ),
			__( 'Menu Control', 'wpcalibrate-menu-control' ),
			$capability,
			self::SUBMENU_SLUG,
			array( $this->settings_page, 'render' )
		);
	}

	/**
	 * Fallback render for top-level parent menu if no central dashboard exists.
	 */
	public function render_parent_page(): void {
		// If user accessed parent page directly, redirect cleanly to Menu Control.
		$url = admin_url( 'admin.php?page=' . self::SUBMENU_SLUG );
		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Inline admin CSS to ensure menu icon matches native WordPress dashboard icon sizing and colors.
	 */
	public function output_menu_icon_styles(): void {
		$icon_url = Helpers::get_branding_icon_url( 'white' );
		if ( empty( $icon_url ) ) {
			return;
		}

		echo '<style id="wpcalibrate-menu-icon-styles">
			#adminmenu .toplevel_page_wpcalibrate .wp-menu-image img {
				width: 20px !important;
				height: 20px !important;
				padding: 7px 0 0 0 !important;
				margin: 0 auto !important;
				display: inline-block !important;
				opacity: 0.7;
				transition: opacity 0.15s ease-in-out;
			}
			@supports (mask-image: url("")) or (-webkit-mask-image: url("")) {
				#adminmenu .toplevel_page_wpcalibrate .wp-menu-image img {
					display: none !important;
				}
				#adminmenu .toplevel_page_wpcalibrate .wp-menu-image::before {
					content: "" !important;
					display: inline-block !important;
					width: 20px !important;
					height: 20px !important;
					padding: 0 !important;
					margin: 7px 0 0 0 !important;
					background-color: #a7aaad !important;
					-webkit-mask-image: url(\'' . esc_url( $icon_url ) . '\') !important;
					mask-image: url(\'' . esc_url( $icon_url ) . '\') !important;
					-webkit-mask-repeat: no-repeat !important;
					mask-repeat: no-repeat !important;
					-webkit-mask-position: center !important;
					mask-position: center !important;
					-webkit-mask-size: 20px 20px !important;
					mask-size: 20px 20px !important;
					transition: background-color 0.15s ease-in-out !important;
				}
				#adminmenu .toplevel_page_wpcalibrate:hover .wp-menu-image::before,
				#adminmenu .toplevel_page_wpcalibrate.opensub .wp-menu-image::before,
				#adminmenu .toplevel_page_wpcalibrate > a:focus .wp-menu-image::before {
					background-color: #72aee6 !important;
				}
				#adminmenu .toplevel_page_wpcalibrate.wp-has-current-submenu .wp-menu-image::before,
				#adminmenu .toplevel_page_wpcalibrate.current .wp-menu-image::before,
				#adminmenu .toplevel_page_wpcalibrate.wp-menu-open .wp-menu-image::before {
					background-color: #ffffff !important;
				}
			}
		</style>';
	}
}
