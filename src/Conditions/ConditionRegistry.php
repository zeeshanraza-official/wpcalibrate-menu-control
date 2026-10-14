<?php
/**
 * Registry of available condition providers.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Conditions;

/**
 * Class ConditionRegistry
 */
final class ConditionRegistry {

	/**
	 * Map of condition type to provider instance.
	 *
	 * @var array<string, ConditionInterface>
	 */
	private array $providers = array();

	/**
	 * Register a condition provider.
	 */
	public function register( ConditionInterface $provider ): void {
		$this->providers[ $provider->get_type() ] = $provider;
	}

	/**
	 * Get a provider by type.
	 */
	public function get( string $type ): ?ConditionInterface {
		return $this->providers[ $type ] ?? null;
	}

	/**
	 * Check if a provider is registered for a type.
	 */
	public function has( string $type ): bool {
		return isset( $this->providers[ $type ] );
	}

	/**
	 * Get all registered providers.
	 *
	 * @return array<string, ConditionInterface>
	 */
	public function get_all(): array {
		return $this->providers;
	}
}
