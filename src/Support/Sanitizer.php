<?php
/**
 * Sanitizer and validator for menu control data.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Support;

/**
 * Class Sanitizer
 */
final class Sanitizer {

	/**
	 * Allowed badge variants.
	 *
	 * @var array<string>
	 */
	public const ALLOWED_BADGE_VARIANTS = array(
		'primary',
		'secondary',
		'accent',
		'success',
		'warning',
		'danger',
		'info',
		'neutral',
	);

	/**
	 * Allowed mega menu widths.
	 *
	 * @var array<string>
	 */
	public const ALLOWED_MEGA_MENU_WIDTHS = array(
		'content',
		'full',
	);

	/**
	 * Allowed mega menu alignments.
	 *
	 * @var array<string>
	 */
	public const ALLOWED_MEGA_MENU_ALIGNMENTS = array(
		'left',
		'center',
		'right',
	);

	/**
	 * Allowed rel attributes.
	 *
	 * @var array<string>
	 */
	public const ALLOWED_REL_VALUES = array(
		'nofollow',
		'sponsored',
		'noopener',
		'noreferrer',
	);

	/**
	 * Sanitize badge variant.
	 */
	public static function sanitize_badge_variant( string $variant ): string {
		$variants = apply_filters( 'wpcalibrate_menu_control_allowed_badge_variants', self::ALLOWED_BADGE_VARIANTS );
		$variant  = sanitize_key( $variant );
		return in_array( $variant, (array) $variants, true ) ? $variant : 'primary';
	}

	/**
	 * Sanitize icon position.
	 */
	public static function sanitize_icon_position( string $position ): string {
		return 'after' === $position ? 'after' : 'before';
	}

	/**
	 * Sanitize mega menu columns (1 to 6).
	 */
	public static function sanitize_mega_menu_columns( mixed $columns ): int {
		$cols = absint( $columns );
		if ( $cols < 1 ) {
			return 4;
		}
		return min( $cols, 6 );
	}

	/**
	 * Sanitize mega menu width.
	 */
	public static function sanitize_mega_menu_width( string $width ): string {
		$width = sanitize_key( $width );
		return in_array( $width, self::ALLOWED_MEGA_MENU_WIDTHS, true ) ? $width : 'content';
	}

	/**
	 * Sanitize mega menu alignment.
	 */
	public static function sanitize_mega_menu_alignment( string $alignment ): string {
		$alignment = sanitize_key( $alignment );
		return in_array( $alignment, self::ALLOWED_MEGA_MENU_ALIGNMENTS, true ) ? $alignment : 'left';
	}

	/**
	 * Sanitize an array of rel values.
	 *
	 * @param mixed $rel_values
	 * @return array<string>
	 */
	public static function sanitize_rel_values( mixed $rel_values ): array {
		if ( is_string( $rel_values ) ) {
			$rel_values = explode( ' ', $rel_values );
		}
		if ( ! is_array( $rel_values ) ) {
			return array();
		}

		$sanitized = array();
		foreach ( $rel_values as $val ) {
			$val = sanitize_key( (string) $val );
			if ( in_array( $val, self::ALLOWED_REL_VALUES, true ) ) {
				$sanitized[] = $val;
			}
		}

		return array_values( array_unique( $sanitized ) );
	}

	/**
	 * Sanitize condition rules array.
	 *
	 * Structure:
	 * [
	 *   'logic' => 'and'|'or',
	 *   'rules' => [
	 *      [ 'type' => string, 'operator' => string, 'value' => mixed ]
	 *   ]
	 * ]
	 *
	 * @param mixed $condition_data
	 * @return array{logic: string, rules: array<int, array{type: string, operator: string, value: mixed}>}
	 */
	public static function sanitize_condition_rules( mixed $condition_data ): array {
		$result = array(
			'logic' => 'and',
			'rules' => array(),
		);

		if ( ! is_array( $condition_data ) ) {
			return $result;
		}

		if ( isset( $condition_data['logic'] ) && 'or' === strtolower( (string) $condition_data['logic'] ) ) {
			$result['logic'] = 'or';
		}

		if ( ! isset( $condition_data['rules'] ) || ! is_array( $condition_data['rules'] ) ) {
			return $result;
		}

		foreach ( $condition_data['rules'] as $rule ) {
			if ( ! is_array( $rule ) || empty( $rule['type'] ) ) {
				continue;
			}

			$type     = sanitize_key( (string) $rule['type'] );
			$operator = isset( $rule['operator'] ) ? sanitize_key( (string) $rule['operator'] ) : 'is';
			$value    = $rule['value'] ?? '';

			if ( is_array( $value ) ) {
				$sanitized_value = array_map(
					static function ( $v ) {
						return is_scalar( $v ) ? sanitize_text_field( (string) $v ) : '';
					},
					$value
				);
			} elseif ( is_bool( $value ) ) {
				$sanitized_value = $value;
			} else {
				$sanitized_value = sanitize_text_field( (string) $value );
			}

			$result['rules'][] = array(
				'type'     => $type,
				'operator' => $operator,
				'value'    => $sanitized_value,
			);
		}

		return $result;
	}

	/**
	 * Sanitize safe SVG or icon identifier.
	 */
	public static function sanitize_icon( string $icon ): string {
		$icon = trim( $icon );
		if ( empty( $icon ) ) {
			return '';
		}

		// If it's a dashicon or font-icon class name.
		if ( preg_match( '/^[a-zA-Z0-9_\-\s]+$/', $icon ) ) {
			$classes = explode( ' ', $icon );
			$clean   = array_filter( array_map( 'sanitize_html_class', $classes ) );
			return implode( ' ', $clean );
		}

		// If it looks like raw SVG markup, sanitize strictly through KSES.
		if ( str_starts_with( $icon, '<svg' ) && str_ends_with( $icon, '</svg>' ) ) {
			// Strip any embedded script tags and inline event handlers defensively.
			$clean_svg = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $icon );
			$clean_svg = preg_replace( '/\bon\w+\s*=\s*(["\']?).*?\1/i', '', (string) $clean_svg );

			$allowed = array(
				'svg'  => array(
					'class'       => true,
					'aria-hidden' => true,
					'aria-label'  => true,
					'role'        => true,
					'viewbox'     => true,
					'width'       => true,
					'height'      => true,
					'fill'        => true,
					'stroke'      => true,
					'xmlns'       => true,
				),
				'g'    => array( 'fill' => true, 'stroke' => true ),
				'path' => array(
					'd'      => true,
					'fill'   => true,
					'stroke' => true,
					'stroke-width' => true,
					'stroke-linecap' => true,
					'stroke-linejoin' => true,
				),
				'circle' => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true ),
				'rect'   => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'fill' => true ),
			);
			return wp_kses( (string) $clean_svg, $allowed );
		}

		return sanitize_text_field( $icon );
	}
}
