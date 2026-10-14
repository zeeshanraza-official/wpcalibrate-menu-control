<?php
/**
 * Frontend menu enhancer for classic WordPress menus.
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
 * Class MenuEnhancer
 */
final class MenuEnhancer {

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
	 * Register frontend hooks.
	 */
	public function register(): void {
		add_filter( 'wp_nav_menu_objects', array( $this, 'filter_menu_objects' ), 10, 2 );
		add_filter( 'nav_menu_item_title', array( $this, 'filter_item_title' ), 10, 4 );
		add_filter( 'nav_menu_link_attributes', array( $this, 'filter_link_attributes' ), 10, 4 );
		add_filter( 'nav_menu_css_class', array( $this, 'filter_css_classes' ), 10, 4 );
	}

	/**
	 * Filter menu objects: evaluate conditions & prime metadata cache.
	 *
	 * @param array<\WP_Post> $sorted_items
	 * @param \stdClass       $args
	 * @return array<\WP_Post>
	 */
	public function filter_menu_objects( array $sorted_items, \stdClass $args ): array {
		if ( empty( $sorted_items ) ) {
			return $sorted_items;
		}

		$options = $this->settings->get_all();

		// Prime postmeta cache for all menu items in a single query to eliminate N+1 overhead.
		$item_ids = wp_list_pluck( $sorted_items, 'ID' );
		if ( function_exists( 'update_meta_cache' ) ) {
			update_meta_cache( 'post', $item_ids );
		}

		$hidden_item_ids = array();
		$filtered_items  = array();

		foreach ( $sorted_items as $item ) {
			$item_id   = (int) $item->ID;
			$parent_id = (int) $item->menu_item_parent;

			// If parent item was hidden, hide its children as well.
			if ( $parent_id > 0 && in_array( $parent_id, $hidden_item_ids, true ) ) {
				$hidden_item_ids[] = $item_id;
				continue;
			}

			// Check conditions if enabled.
			if ( ! empty( $options['enable_conditions'] ) ) {
				$conditions_enabled = (bool) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'conditions_enabled', true );
				if ( $conditions_enabled ) {
					$rules = get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'condition_rules', true );
					if ( is_array( $rules ) && ! empty( $rules['rules'] ) ) {
						$is_visible = $this->condition_engine->is_visible( $rules, $item_id );
						if ( ! $is_visible ) {
							$hidden_item_ids[] = $item_id;
							continue;
						}
					}
				}
			}

			$filtered_items[] = $item;
		}

		return apply_filters( 'wpcalibrate_menu_control_filtered_menu_objects', $filtered_items, $args );
	}

	/**
	 * Enhance menu item title with icons, badges, and subtitles.
	 *
	 * @param string    $title
	 * @param \WP_Post  $item
	 * @param \stdClass $args
	 * @param int       $depth
	 * @return string
	 */
	public function filter_item_title( string $title, \WP_Post $item, \stdClass $args, int $depth ): string {
		$item_id = (int) $item->ID;
		$options = $this->settings->get_all();

		$before_html = '';
		$after_html  = '';

		// 1. Icon.
		if ( ! empty( $options['enable_icons'] ) ) {
			$icon     = (string) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'icon', true );
			$icon_pos = (string) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'icon_position', true ) ?: 'before';

			if ( ! empty( $icon ) ) {
				$icon_markup = '';
				if ( str_starts_with( $icon, '<svg' ) ) {
					// Safe SVG with aria-hidden="true".
					$icon_markup = '<span class="wpcalibrate-menu-icon wpcalibrate-icon-svg" aria-hidden="true">' . Sanitizer::sanitize_icon( $icon ) . '</span>';
				} else {
					// Dashicons or CSS class.
					$icon_markup = '<span class="wpcalibrate-menu-icon dashicons ' . esc_attr( $icon ) . '" aria-hidden="true"></span>';
				}

				if ( 'after' === $icon_pos ) {
					$after_html .= ' ' . $icon_markup;
				} else {
					$before_html .= $icon_markup . ' ';
				}
			}
		}

		// 2. Badge.
		if ( ! empty( $options['enable_badges'] ) ) {
			$badge_text = (string) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'badge_text', true );
			if ( '' !== $badge_text ) {
				$badge_variant = (string) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'badge_variant', true ) ?: 'primary';
				$badge_variant = Sanitizer::sanitize_badge_variant( $badge_variant );
				$badge_html    = sprintf(
					'<span class="wpcalibrate-badge wpcalibrate-badge--%s">%s</span>',
					esc_attr( $badge_variant ),
					esc_html( $badge_text )
				);
				$after_html .= ' ' . $badge_html;
			}
		}

		// 3. Subtitle.
		if ( ! empty( $options['enable_subtitles'] ) ) {
			$subtitle = (string) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'subtitle', true );
			if ( '' !== $subtitle ) {
				$after_html .= sprintf(
					'<span class="wpcalibrate-subtitle">%s</span>',
					esc_html( $subtitle )
				);
			}
		}

		return $before_html . '<span class="wpcalibrate-item-label">' . $title . '</span>' . $after_html;
	}

	/**
	 * Enhance link attributes: rel values, non-clickable heading behavior.
	 *
	 * @param array<string, string> $atts
	 * @param \WP_Post              $item
	 * @param \stdClass             $args
	 * @param int                   $depth
	 * @return array<string, string>
	 */
	public function filter_link_attributes( array $atts, \WP_Post $item, \stdClass $args, int $depth ): array {
		$item_id = (int) $item->ID;
		$options = $this->settings->get_all();

		// Handle Rel values allowlist.
		$rel_values = (array) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'rel_values', true );
		if ( ! empty( $rel_values ) ) {
			$existing_rel = ! empty( $atts['rel'] ) ? explode( ' ', (string) $atts['rel'] ) : array();
			$merged_rel   = array_unique( array_merge( $existing_rel, $rel_values ) );
			$atts['rel']  = esc_attr( implode( ' ', $merged_rel ) );
		}

		// Handle Non-Clickable Heading.
		if ( ! empty( $options['enable_non_clickable'] ) ) {
			$non_clickable = (bool) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'non_clickable', true );
			if ( $non_clickable ) {
				$atts['href']          = '#';
				$atts['role']          = 'heading';
				$atts['aria-level']    = '3';
				$atts['aria-disabled'] = 'true';
				$atts['class']         = ( ! empty( $atts['class'] ) ? $atts['class'] . ' ' : '' ) . 'wpcalibrate-non-clickable';
			}
		}

		return $atts;
	}

	/**
	 * Add custom CSS classes to menu items.
	 *
	 * @param array<string> $classes
	 * @param \WP_Post      $item
	 * @param \stdClass     $args
	 * @param int           $depth
	 * @return array<string>
	 */
	public function filter_css_classes( array $classes, \WP_Post $item, \stdClass $args, int $depth ): array {
		$item_id = (int) $item->ID;
		$options = $this->settings->get_all();

		$classes[] = 'wpcalibrate-item';

		// Highlight.
		$highlight = (bool) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'highlight', true );
		if ( $highlight ) {
			$classes[] = 'wpcalibrate-highlight';
		}

		// Non-clickable.
		$non_clickable = (bool) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'non_clickable', true );
		if ( $non_clickable ) {
			$classes[] = 'wpcalibrate-is-heading';
		}

		// Mega menu (top-level only).
		if ( 0 === $depth && ! empty( $options['enable_mega_menu'] ) ) {
			$mega_enabled = (bool) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'mega_menu_enabled', true );
			if ( $mega_enabled ) {
				$cols  = Sanitizer::sanitize_mega_menu_columns( get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'mega_menu_columns', true ) ?: 4 );
				$width = Sanitizer::sanitize_mega_menu_width( (string) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'mega_menu_width', true ) ?: 'content' );
				$align = Sanitizer::sanitize_mega_menu_alignment( (string) get_post_meta( $item_id, MenuItemFields::META_PREFIX . 'mega_menu_alignment', true ) ?: 'left' );

				$classes[] = 'wpcalibrate-mega-menu';
				$classes[] = 'wpcalibrate-mega-cols-' . $cols;
				$classes[] = 'wpcalibrate-mega-width-' . $width;
				$classes[] = 'wpcalibrate-mega-align-' . $align;
			}
		}

		return apply_filters( 'wpcalibrate_menu_control_item_classes', array_unique( $classes ), $item, $args, $depth );
	}
}
