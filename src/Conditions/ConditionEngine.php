<?php
/**
 * Centralized conditional visibility evaluation engine.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Conditions;

use WPCalibrate\MenuControl\Support\Logger;

/**
 * Class ConditionEngine
 */
final class ConditionEngine {

	/**
	 * Condition registry instance.
	 */
	private ConditionRegistry $registry;

	/**
	 * Per-request evaluation cache.
	 *
	 * @var array<string, bool>
	 */
	private array $evaluation_cache = array();

	/**
	 * Constructor.
	 */
	public function __construct( ConditionRegistry $registry ) {
		$this->registry = $registry;
	}

	/**
	 * Evaluate condition rules for a menu item.
	 *
	 * @param array{logic?: string, rules?: array<int, array{type: string, operator: string, value: mixed}>} $condition_data
	 * @param int|null $item_id Optional item ID for filtering.
	 * @return bool True if item should be visible, false to hide.
	 */
	public function is_visible( array $condition_data, ?int $item_id = null ): bool {
		$rules = $condition_data['rules'] ?? array();

		// Zero conditions means normal WordPress visibility.
		if ( empty( $rules ) ) {
			return apply_filters( 'wpcalibrate_menu_control_item_visibility', true, $condition_data, $item_id );
		}

		$logic = strtolower( (string) ( $condition_data['logic'] ?? 'and' ) );
		$cache_key = md5( (string) wp_json_encode( $condition_data ) );

		if ( isset( $this->evaluation_cache[ $cache_key ] ) ) {
			$cached_result = $this->evaluation_cache[ $cache_key ];
			return apply_filters( 'wpcalibrate_menu_control_item_visibility', $cached_result, $condition_data, $item_id );
		}

		$rule_results = array();

		foreach ( $rules as $rule ) {
			if ( empty( $rule['type'] ) ) {
				continue;
			}

			$type     = (string) $rule['type'];
			$provider = $this->registry->get( $type );

			if ( null === $provider ) {
				// Fail safely when condition provider is missing or unavailable.
				// By default, missing provider evaluates to true to avoid accidental hidden menus.
				Logger::log( Logger::LEVEL_WARNING, sprintf( 'Condition provider "%s" not registered; failing safely.', $type ) );
				$rule_results[] = true;
				continue;
			}

			try {
				$rule_results[] = $provider->evaluate( $rule );
			} catch ( \Throwable $e ) {
				Logger::log( Logger::LEVEL_ERROR, sprintf( 'Error evaluating rule "%s": %s', $type, $e->getMessage() ) );
				$rule_results[] = true; // Fail safe.
			}
		}

		if ( empty( $rule_results ) ) {
			$this->evaluation_cache[ $cache_key ] = true;
			return true;
		}

		if ( 'or' === $logic ) {
			// At least one rule must be true.
			$final_result = in_array( true, $rule_results, true );
		} else {
			// ALL rules must be true (AND).
			$final_result = ! in_array( false, $rule_results, true );
		}

		$this->evaluation_cache[ $cache_key ] = $final_result;

		return apply_filters( 'wpcalibrate_menu_control_item_visibility', $final_result, $condition_data, $item_id );
	}

	/**
	 * Clear in-memory evaluation cache.
	 */
	public function clear_cache(): void {
		$this->evaluation_cache = array();
	}
}
