<?php
/**
 * Safe frontend enhancement for WordPress core Navigation blocks.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Frontend;

use WPCalibrate\MenuControl\Admin\Settings;
use WPCalibrate\MenuControl\Admin\MenuItemFields;
use WPCalibrate\MenuControl\Conditions\ConditionEngine;
use WPCalibrate\MenuControl\Support\Sanitizer;

/**
 * Class NavigationBlockIntegration
 */
final class NavigationBlockIntegration {

	private Settings $settings;
	private ConditionEngine $condition_engine;

	/**
	 * Constructor.
	 */
	public function __construct( Settings $settings, ConditionEngine $condition_engine ) {
		$this->settings         = $settings;
		$this->condition_engine = $condition_engine;
	}

	/**
	 * Register hooks for Navigation block rendering.
	 */
	public function register(): void {
		add_filter( 'render_block_core/navigation-link', array( $this, 'filter_navigation_link' ), 10, 2 );
		add_filter( 'render_block_core/navigation-submenu', array( $this, 'filter_navigation_link' ), 10, 2 );
	}

	/**
	 * Enhance core Navigation block links safely without mutating serialized block content.
	 *
	 * @param string               $block_content Rendered HTML.
	 * @param array<string, mixed> $block Block attributes and data.
	 * @return string Filtered HTML or empty string if hidden.
	 */
	public function filter_navigation_link( string $block_content, array $block ): string {
		if ( empty( $block_content ) ) {
			return $block_content;
		}

		$options = $this->settings->get_all();
		if ( empty( $options['enable_block_nav'] ) ) {
			return $block_content;
		}

		$attrs   = $block['attrs'] ?? array();
		$item_id = ! empty( $attrs['id'] ) ? (int) $attrs['id'] : 0;

		// 1. Condition evaluation if backed by a nav_menu_item.
		if ( $item_id > 0 && ! empty( $options['enable_conditions'] ) ) {
			$conditions_on = (bool) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'conditions_enabled', true );
			if ( $conditions_on ) {
				$rules = get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'condition_rules', true );
				if ( is_array( $rules ) && ! empty( $rules['rules'] ) ) {
					if ( ! $this->condition_engine->is_visible( $rules, $item_id ) ) {
						return ''; // Hide navigation link.
					}
				}
			}
		}

		// 2. Class-based quick visibility rules (if custom classes are used in block editor).
		$class_name = $attrs['className'] ?? '';
		if ( ! empty( $class_name ) && is_string( $class_name ) ) {
			if ( str_contains( $class_name, 'wpcalibrate-logged-in-only' ) && ! is_user_logged_in() ) {
				return '';
			}
			if ( str_contains( $class_name, 'wpcalibrate-logged-out-only' ) && is_user_logged_in() ) {
				return '';
			}
		}

		// 3. Enhance label with icon/badge if meta exists.
		if ( $item_id > 0 ) {
			$badge_text = (string) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'badge_text', true );
			$badge_var  = (string) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'badge_variant', true ) ?: 'primary';
			$icon       = (string) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'icon', true );
			$icon_pos   = (string) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'icon_position', true ) ?: 'before';

			if ( '' !== $badge_text || ! empty( $icon ) ) {
				$badge_html = '';
				if ( '' !== $badge_text && ! empty( $options['enable_badges'] ) ) {
					$badge_html = sprintf(
						' <span class="wpcalibrate-badge wpcalibrate-badge--%s">%s</span>',
						esc_attr( Sanitizer::sanitize_badge_variant( $badge_var ) ),
						esc_html( $badge_text )
					);
				}

				$icon_html = '';
				if ( ! empty( $icon ) && ! empty( $options['enable_icons'] ) ) {
					if ( str_starts_with( $icon, '<svg' ) ) {
						$icon_html = '<span class="wpcalibrate-menu-icon wpcalibrate-icon-svg" aria-hidden="true">' . Sanitizer::sanitize_icon( $icon ) . '</span> ';
					} else {
						$icon_html = '<span class="wpcalibrate-menu-icon dashicons ' . esc_attr( $icon ) . '" aria-hidden="true"></span> ';
					}
				}

				// Inject into the inner label span of navigation block: <span class="wp-block-navigation-item__label">...</span>
				$pattern = '/(<span class="wp-block-navigation-item__label">)(.*?)(<\/span>)/i';
				if ( preg_match( $pattern, $block_content ) ) {
					$block_content = (string) preg_replace(
						$pattern,
						'$1' . ( 'before' === $icon_pos ? $icon_html : '' ) . '$2' . ( 'after' === $icon_pos ? ' ' . $icon_html : '' ) . $badge_html . '$3',
						$block_content,
						1
					);
				}
			}
		}

		return $block_content;
	}
}
