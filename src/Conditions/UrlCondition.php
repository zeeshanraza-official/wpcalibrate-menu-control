<?php
/**
 * URL and path matching condition provider.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Conditions;

/**
 * Class UrlCondition
 */
final class UrlCondition implements ConditionInterface {

	public const TYPE = 'url_path';

	public function get_type(): string {
		return self::TYPE;
	}

	/**
	 * Evaluate URL/path condition.
	 */
	public function evaluate( array $rule ): bool {
		$operator    = $rule['operator'] ?? 'is';
		$target_path = is_string( $rule['value'] ) ? trim( $rule['value'] ) : '';

		if ( '' === $target_path ) {
			return true;
		}

		$current_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$current_path = wp_parse_url( $current_uri, PHP_URL_PATH ) ?: '/';

		$norm_current = rtrim( $current_path, '/' );
		$norm_target  = rtrim( $target_path, '/' );

		$matches = false;
		switch ( $operator ) {
			case 'contains':
				$matches = str_contains( $current_path, $target_path );
				break;
			case 'starts_with':
				$matches = str_starts_with( $current_path, $target_path );
				break;
			case 'is_not':
				$matches = ( $norm_current !== $norm_target );
				return $matches;
			case 'is':
			default:
				$matches = ( $norm_current === $norm_target );
				break;
		}

		return $matches;
	}
}
