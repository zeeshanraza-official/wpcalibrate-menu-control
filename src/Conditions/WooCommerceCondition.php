<?php
/**
 * WooCommerce conditional visibility provider.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Conditions;

use WPCalibrate\MenuControl\Support\Environment;

/**
 * Class WooCommerceCondition
 */
final class WooCommerceCondition implements ConditionInterface {

	/**
	 * Supported type: 'wc_context' or 'wc_customer'.
	 */
	private string $type;

	/**
	 * Constructor.
	 */
	public function __construct( string $type = 'wc_context' ) {
		$this->type = $type;
	}

	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Evaluate WooCommerce condition.
	 */
	public function evaluate( array $rule ): bool {
		// If WooCommerce is inactive, fail safely without errors.
		if ( ! Environment::is_woocommerce_active() ) {
			return false;
		}

		$operator = $rule['operator'] ?? 'is';
		$value    = is_string( $rule['value'] ) ? strtolower( trim( $rule['value'] ) ) : '';

		if ( 'wc_context' === $this->type ) {
			$matches = false;
			switch ( $value ) {
				case 'shop':
					$matches = function_exists( 'is_shop' ) && is_shop();
					break;
				case 'product':
					$matches = function_exists( 'is_product' ) && is_product();
					break;
				case 'product_cat':
					$matches = function_exists( 'is_product_category' ) && is_product_category();
					break;
				case 'cart':
					$matches = function_exists( 'is_cart' ) && is_cart();
					break;
				case 'checkout':
					$matches = function_exists( 'is_checkout' ) && is_checkout();
					break;
				case 'account':
				case 'my_account':
					$matches = function_exists( 'is_account_page' ) && is_account_page();
					break;
			}
			return 'is_not' === $operator ? ! $matches : $matches;
		}

		if ( 'wc_customer' === $this->type ) {
			$matches = false;
			switch ( $value ) {
				case 'logged_in':
				case 'customer':
					$matches = is_user_logged_in();
					break;
				case 'guest':
					$matches = ! is_user_logged_in();
					break;
				case 'has_orders':
					if ( is_user_logged_in() && function_exists( 'wc_customer_has_orders' ) ) {
						$matches = wc_customer_has_orders( get_current_user_id() );
					}
					break;
			}
			return 'is_not' === $operator ? ! $matches : $matches;
		}

		return true;
	}
}
