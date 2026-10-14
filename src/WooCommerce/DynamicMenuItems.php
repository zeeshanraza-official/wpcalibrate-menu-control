<?php
/**
 * WooCommerce dynamic menu items and live cart fragment synchronization.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\WooCommerce;

use WPCalibrate\MenuControl\Support\Environment;
use WPCalibrate\MenuControl\Admin\Settings;

/**
 * Class DynamicMenuItems
 */
final class DynamicMenuItems {

	private Settings $settings;

	/**
	 * Constructor.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register hooks.
	 */
	public function register(): void {
		add_filter( 'wp_nav_menu_objects', array( $this, 'filter_dynamic_items' ), 20, 2 );
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'sync_cart_fragments' ) );
	}

	/**
	 * Filter menu items replacing dynamic placeholders with live WooCommerce data.
	 *
	 * @param array<\WP_Post> $items
	 * @param \stdClass       $args
	 * @return array<\WP_Post>
	 */
	public function filter_dynamic_items( array $items, \stdClass $args ): array {
		if ( ! Environment::is_woocommerce_active() ) {
			return $items;
		}

		$options = $this->settings->get_all();
		if ( empty( $options['enable_woocommerce'] ) ) {
			return $items;
		}

		foreach ( $items as $item ) {
			$url   = (string) $item->url;
			$title = (string) $item->title;

			// Dynamic URLs.
			if ( str_contains( $url, '#wpcalibrate-wc-cart' ) && function_exists( 'wc_get_cart_url' ) ) {
				$item->url = wc_get_cart_url();
			} elseif ( str_contains( $url, '#wpcalibrate-wc-checkout' ) && function_exists( 'wc_get_checkout_url' ) ) {
				$item->url = wc_get_checkout_url();
			} elseif ( str_contains( $url, '#wpcalibrate-wc-account' ) && function_exists( 'wc_get_page_permalink' ) ) {
				$item->url = wc_get_page_permalink( 'myaccount' );
			} elseif ( str_contains( $url, '#wpcalibrate-wc-login-logout' ) ) {
				if ( is_user_logged_in() ) {
					$item->url   = wp_logout_url( home_url() );
					$item->title = __( 'Log Out', 'wpcalibrate-menu-control' );
				} else {
					$item->url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
					$item->title = __( 'Log In', 'wpcalibrate-menu-control' );
				}
			}

			// Dynamic Title Placeholders: {cart_count}, {cart_total}, {user_name}.
			if ( str_contains( $title, '{cart_count}' ) || str_contains( $title, '{cart_total}' ) || str_contains( $title, '{user_name}' ) ) {
				$cart_count = 0;
				$cart_total = '';

				if ( function_exists( 'WC' ) && WC()->cart ) {
					$cart_count = (int) WC()->cart->get_cart_contents_count();
					$cart_total = WC()->cart->get_cart_subtotal();
				}

				$user_name = '';
				if ( is_user_logged_in() ) {
					$current_user = wp_get_current_user();
					$user_name    = esc_html( $current_user->display_name );
				}

				$count_markup = sprintf( '<span class="wpcalibrate-wc-cart-count">%d</span>', $cart_count );
				$total_markup = sprintf( '<span class="wpcalibrate-wc-cart-total">%s</span>', $cart_total );

				$title = str_replace( '{cart_count}', $count_markup, $title );
				$title = str_replace( '{cart_total}', $total_markup, $title );
				$title = str_replace( '{user_name}', $user_name, $title );

				$item->title = $title;
			}
		}

		return $items;
	}

	/**
	 * Synchronize live cart count and subtotal with WooCommerce AJAX cart fragments.
	 *
	 * @param array<string, string> $fragments
	 * @return array<string, string>
	 */
	public function sync_cart_fragments( array $fragments ): array {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return $fragments;
		}

		$cart_count = (int) WC()->cart->get_cart_contents_count();
		$cart_total = WC()->cart->get_cart_subtotal();

		$fragments['span.wpcalibrate-wc-cart-count'] = sprintf( '<span class="wpcalibrate-wc-cart-count">%d</span>', $cart_count );
		$fragments['span.wpcalibrate-wc-cart-total'] = sprintf( '<span class="wpcalibrate-wc-cart-total">%s</span>', $cart_total );

		return $fragments;
	}
}
