<?php
/**
 * Main plugin orchestrator.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl;

use WPCalibrate\MenuControl\Admin\AdminMenu;
use WPCalibrate\MenuControl\Admin\SettingsPage;
use WPCalibrate\MenuControl\Admin\Settings;
use WPCalibrate\MenuControl\Admin\MenuItemFields;
use WPCalibrate\MenuControl\Admin\Assets;
use WPCalibrate\MenuControl\Admin\ImportExport;
use WPCalibrate\MenuControl\Conditions\ConditionRegistry;
use WPCalibrate\MenuControl\Conditions\ConditionEngine;
use WPCalibrate\MenuControl\Conditions\UserCondition;
use WPCalibrate\MenuControl\Conditions\DeviceCondition;
use WPCalibrate\MenuControl\Conditions\ContentCondition;
use WPCalibrate\MenuControl\Conditions\UrlCondition;
use WPCalibrate\MenuControl\Conditions\ScheduleCondition;
use WPCalibrate\MenuControl\Conditions\WooCommerceCondition;
use WPCalibrate\MenuControl\Frontend\MenuEnhancer;
use WPCalibrate\MenuControl\Frontend\NavigationBlockIntegration;
use WPCalibrate\MenuControl\MegaMenu\MegaMenu;
use WPCalibrate\MenuControl\WooCommerce\Compatibility;
use WPCalibrate\MenuControl\WooCommerce\DynamicMenuItems;
use WPCalibrate\MenuControl\Licensing\LicenseManager;
use WPCalibrate\MenuControl\Migration\MigrationManager;
use WPCalibrate\MenuControl\Support\Environment;
use WPCalibrate\MenuControl\Support\GitHubUpdater;

/**
 * Class Plugin
 */
final class Plugin {

	private static ?Plugin $instance = null;

	public Settings $settings;
	public ConditionRegistry $condition_registry;
	public ConditionEngine $condition_engine;
	public AdminMenu $admin_menu;
	public SettingsPage $settings_page;
	public MenuItemFields $menu_item_fields;
	public Assets $assets;
	public ImportExport $import_export;
	public MenuEnhancer $menu_enhancer;
	public MegaMenu $mega_menu;
	public NavigationBlockIntegration $nav_block_integration;
	public Compatibility $wc_compatibility;
	public DynamicMenuItems $wc_dynamic_items;
	public LicenseManager $license_manager;
	public ?GitHubUpdater $github_updater = null;

	/**
	 * Get singleton instance.
	 */
	public static function get_instance( string $plugin_file = '' ): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self( $plugin_file );
		}
		return self::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct( string $plugin_file ) {
		$this->settings           = new Settings();
		$this->condition_registry = new ConditionRegistry();
		$this->condition_engine   = new ConditionEngine( $this->condition_registry );

		$this->settings_page      = new SettingsPage( $this->settings );
		$this->admin_menu         = new AdminMenu( $this->settings_page );
		$this->menu_item_fields   = new MenuItemFields( $this->settings );
		$this->assets             = new Assets( $this->settings );
		$this->import_export      = new ImportExport( $this->settings );

		$this->menu_enhancer         = new MenuEnhancer( $this->settings, $this->condition_engine );
		$this->mega_menu             = new MegaMenu( $this->settings );
		$this->nav_block_integration = new NavigationBlockIntegration( $this->settings, $this->condition_engine );

		$this->wc_compatibility = new Compatibility( $plugin_file );
		$this->wc_dynamic_items = new DynamicMenuItems( $this->settings );
		$this->license_manager  = new LicenseManager();

		$version = defined( 'WPCALIBRATE_MENU_CONTROL_VERSION' ) ? WPCALIBRATE_MENU_CONTROL_VERSION : '1.0.1';
		if ( ! empty( $plugin_file ) ) {
			$this->github_updater = new GitHubUpdater(
				'zeeshanraza-official/wpcalibrate-menu-control',
				$plugin_file,
				$version
			);
		}
	}

	/**
	 * Bootstraps the plugin lifecycle and hooks.
	 */
	public function run(): void {
		// Run upgrade/migration checks safely during admin_init.
		add_action( 'admin_init', array( $this, 'check_upgrades' ) );

		// Load plugin textdomain.
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Register built-in condition providers.
		$this->register_default_conditions();

		// Register components.
		$this->settings->register();
		$this->admin_menu->register();
		$this->menu_item_fields->register();
		$this->assets->register();
		$this->import_export->register();
		$this->menu_enhancer->register();
		$this->mega_menu->register();
		$this->nav_block_integration->register();
		$this->wc_compatibility->register();
		$this->wc_dynamic_items->register();
		$this->license_manager->register();

		if ( null !== $this->github_updater ) {
			$this->github_updater->register();
		}
	}

	/**
	 * Register default condition providers.
	 */
	private function register_default_conditions(): void {
		$this->condition_registry->register( new UserCondition( 'user_status' ) );
		$this->condition_registry->register( new UserCondition( 'user_role' ) );
		$this->condition_registry->register( new DeviceCondition() );
		$this->condition_registry->register( new ContentCondition( 'page_context' ) );
		$this->condition_registry->register( new ContentCondition( 'post_type' ) );
		$this->condition_registry->register( new UrlCondition() );
		$this->condition_registry->register( new ScheduleCondition( 'schedule_start' ) );
		$this->condition_registry->register( new ScheduleCondition( 'schedule_end' ) );
		$this->condition_registry->register( new WooCommerceCondition( 'wc_context' ) );
		$this->condition_registry->register( new WooCommerceCondition( 'wc_customer' ) );

		// Allow third-party condition providers to register.
		do_action( 'wpcalibrate_menu_control_register_conditions', $this->condition_registry );
	}

	/**
	 * Run upgrade checks during bootstrap.
	 */
	public function check_upgrades(): void {
		$stored_schema = (int) get_option( MigrationManager::SCHEMA_OPTION_KEY, 0 );
		if ( $stored_schema < MigrationManager::CURRENT_SCHEMA_VERSION ) {
			MigrationManager::run_migrations();
		}
	}

	/**
	 * Load translation text domain.
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'wpcalibrate-menu-control',
			false,
			dirname( plugin_basename( defined( 'WPCALIBRATE_MENU_CONTROL_FILE' ) ? WPCALIBRATE_MENU_CONTROL_FILE : __FILE__ ) ) . '/languages'
		);
	}
}
