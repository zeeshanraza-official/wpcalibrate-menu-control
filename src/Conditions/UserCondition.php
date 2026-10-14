<?php
/**
 * User status and user role condition provider.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Conditions;

/**
 * Class UserCondition
 */
final class UserCondition implements ConditionInterface {

	/**
	 * Type to handle: 'user_status' or 'user_role'.
	 */
	private string $type;

	/**
	 * Constructor.
	 */
	public function __construct( string $type = 'user_status' ) {
		$this->type = $type;
	}

	/**
	 * Get type.
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Evaluate user condition.
	 */
	public function evaluate( array $rule ): bool {
		$operator = $rule['operator'] ?? 'is';
		$value    = $rule['value'] ?? '';

		if ( 'user_status' === $this->type ) {
			$is_logged_in = is_user_logged_in();
			$target       = is_string( $value ) ? strtolower( trim( $value ) ) : '';

			$matches = ( 'logged_in' === $target && $is_logged_in ) || ( 'logged_out' === $target && ! $is_logged_in );

			return 'is_not' === $operator ? ! $matches : $matches;
		}

		if ( 'user_role' === $this->type ) {
			if ( ! is_user_logged_in() ) {
				return 'is_not' === $operator;
			}

			$user = wp_get_current_user();
			$user_roles = (array) $user->roles;

			$target_roles = is_array( $value )
				? $value
				: array_filter( array_map( 'trim', explode( ',', (string) $value ) ) );

			$has_role = false;
			foreach ( $target_roles as $role ) {
				if ( in_array( strtolower( (string) $role ), array_map( 'strtolower', $user_roles ), true ) ) {
					$has_role = true;
					break;
				}
			}

			return 'is_not' === $operator ? ! $has_role : $has_role;
		}

		return true;
	}
}
