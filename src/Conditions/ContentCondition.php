<?php
/**
 * Page context and post type condition provider.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Conditions;

/**
 * Class ContentCondition
 */
final class ContentCondition implements ConditionInterface {

	/**
	 * Supported type: 'page_context' or 'post_type'.
	 */
	private string $type;

	/**
	 * Constructor.
	 */
	public function __construct( string $type = 'page_context' ) {
		$this->type = $type;
	}

	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Evaluate page context or post type rule.
	 */
	public function evaluate( array $rule ): bool {
		$operator = $rule['operator'] ?? 'is';
		$value    = is_string( $rule['value'] ) ? strtolower( trim( $rule['value'] ) ) : '';

		if ( 'page_context' === $this->type ) {
			$matches = false;
			switch ( $value ) {
				case 'home':
				case 'front_page':
					$matches = is_front_page() || is_home();
					break;
				case 'singular':
					$matches = is_singular();
					break;
				case 'archive':
					$matches = is_archive();
					break;
				case 'search':
					$matches = is_search();
					break;
				case '404':
					$matches = is_404();
					break;
			}
			return 'is_not' === $operator ? ! $matches : $matches;
		}

		if ( 'post_type' === $this->type ) {
			$current_pt = is_singular() ? get_post_type() : '';
			$matches    = ( $current_pt && strtolower( (string) $current_pt ) === $value );
			return 'is_not' === $operator ? ! $matches : $matches;
		}

		return true;
	}
}
