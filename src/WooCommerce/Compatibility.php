<?php
/**
 * WooCommerce compatibility and HPOS declaration.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\WooCommerce;

use WPCalibrate\MenuControl\Support\Environment;

/**
 * Class Compatibility
 */
final class Compatibility {

	/**
	 * Main plugin file path for HPOS declaration.
	 */
	private string $plugin_file;

	/**
	 * Constructor.
	 */
	public function __construct( string $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	/**
	 * Register compatibility hooks.
	 */
	public function register(): void {
		// Declare HPOS and Cart/Checkout Blocks compatibility.
		add_action( 'before_woocommerce_init', array( $this, 'declare_features_compatibility' ) );
	}

	/**
	 * Declare HPOS and Cart/Checkout Blocks compatibility.
	 */
	public function declare_features_compatibility(): void {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $this->plugin_file, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', $this->plugin_file, true );
		}
	}
}
