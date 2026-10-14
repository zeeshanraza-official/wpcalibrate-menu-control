<?php
/**
 * Date and time schedule condition provider.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Conditions;

use WPCalibrate\MenuControl\Support\Helpers;

/**
 * Class ScheduleCondition
 */
final class ScheduleCondition implements ConditionInterface {

	/**
	 * Supported type: 'schedule_start' or 'schedule_end'.
	 */
	private string $type;

	/**
	 * Constructor.
	 */
	public function __construct( string $type = 'schedule_start' ) {
		$this->type = $type;
	}

	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Evaluate schedule boundary condition using site timezone.
	 */
	public function evaluate( array $rule ): bool {
		$date_val = is_string( $rule['value'] ) ? trim( $rule['value'] ) : '';

		if ( '' === $date_val ) {
			return true;
		}

		$boundary_time = Helpers::parse_site_datetime( $date_val );
		if ( null === $boundary_time ) {
			// Fail safely on unparseable date strings.
			return true;
		}

		$current_time = Helpers::get_current_site_datetime();

		if ( 'schedule_start' === $this->type ) {
			// Visible if current time is at or after start time (inclusive).
			return $current_time >= $boundary_time;
		}

		if ( 'schedule_end' === $this->type ) {
			// Visible if current time is at or before end time (inclusive).
			return $current_time <= $boundary_time;
		}

		return true;
	}
}
