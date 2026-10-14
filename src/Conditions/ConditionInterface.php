<?php
/**
 * Condition handler interface.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Conditions;

/**
 * Interface ConditionInterface
 */
interface ConditionInterface {

	/**
	 * Get the condition type identifier (e.g. 'user_status', 'user_role').
	 */
	public function get_type(): string;

	/**
	 * Evaluate the rule against current request context.
	 *
	 * @param array{type: string, operator: string, value: mixed} $rule
	 * @return bool True if condition passes, false otherwise.
	 */
	public function evaluate( array $rule ): bool;
}
