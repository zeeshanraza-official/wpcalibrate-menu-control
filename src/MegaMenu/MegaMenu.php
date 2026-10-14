<?php
/**
 * Mega menu coordinator and walker integration.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\MegaMenu;

use WPCalibrate\MenuControl\Admin\Settings;
use WPCalibrate\MenuControl\Admin\MenuItemFields;

/**
 * Class MegaMenu
 */
final class MegaMenu {

	private Settings $settings;

	/**
	 * Constructor.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register mega menu hooks.
	 */
	public function register(): void {
		add_filter( 'nav_menu_submenu_css_class', array( $this, 'filter_submenu_classes' ), 10, 3 );
	}

	/**
	 * Add mega menu container class to the immediate sub-menu `<ul>` of a mega menu item.
	 *
	 * @param array<string> $classes
	 * @param \stdClass     $args
	 * @param int           $depth
	 * @return array<string>
	 */
	public function filter_submenu_classes( array $classes, \stdClass $args, int $depth ): array {
		if ( 0 === $depth ) {
			$classes[] = 'wpcalibrate-dropdown-menu';
		}
		return $classes;
	}
}
