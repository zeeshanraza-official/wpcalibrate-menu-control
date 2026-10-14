=== WPCalibrate Menu Control ===
Contributors: wpcalibrate
Tags: menu, navigation, conditional menu, mega menu, woocommerce menu
Requires at least: 6.8
Tested up to: 7.1.3
Requires PHP: 8.2
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Control who sees each menu item, when they see it, and how it appears. Advanced visibility conditions, badges, icons, non-clickable headings, WooCommerce dynamic elements, and lightweight mega menus.

== Description ==

**WPCalibrate Menu Control** is a lightweight, advanced frontend navigation enhancement plugin for WordPress. It empowers site administrators to take full control of navigation menus without modifying active theme files or installing heavy visual page builders.

### Primary Capabilities

* **Conditional Visibility Engine:** Display or hide menu items based on user login status, user roles, device groups (desktop/tablet/mobile), page context, post types, URL/path patterns, and scheduled date/time boundaries using the site's local timezone. Supports both **AND** (all rules required) and **OR** (any rule required) logic.
* **Badges & Callouts:** Attach semantic callout badges (such as *New*, *Hot*, *Sale*, or custom text) with configurable color variants.
* **Menu Item Icons:** Attach Dashicons or sanitized SVG icons positioned before or after link labels, with full `aria-hidden` screen reader accessibility.
* **Subtitles & Descriptions:** Add secondary descriptive text beneath menu labels.
* **Non-Clickable Parent Headings:** Turn parent items into accessible non-clickable headings without `javascript:void(0)` workarounds.
* **Link Attributes & SEO Controls:** Predefined allowlisted `rel` values (`nofollow`, `sponsored`, `noopener`, `noreferrer`).
* **Highlight State:** Emphasize primary calls-to-action with accent styling.
* **Lightweight Mega Menus:** Transform standard classic top-level menu items into 1-to-6 column grid dropdowns with full keyboard navigation and automatic mobile accordion degradation.
* **WooCommerce Dynamic Elements (Optional):** Dynamic live cart link (`#wpcalibrate-wc-cart`), cart item count badge (`{cart_count}`), cart subtotal (`{cart_total}`), My Account (`#wpcalibrate-wc-account`), and dynamic Login/Logout links with AJAX fragment synchronization.
* **Core Navigation Block Support:** Safe frontend filtering for `core/navigation-link` blocks without corrupting block attributes or serialized content.
* **Import & Export:** Export and restore plugin global settings in versioned, strictly validated JSON format.
* **Isolated Administration:** Cooperatively shares the top-level WPCalibrate admin menu with other WPCalibrate plugins without screen or option collisions.

### Security & Access Notice

**Important:** Hiding a menu item with conditional rules is a presentation and user-experience control, not a security authorization boundary. Hidden URLs remain governed by WordPress capabilities and page access permissions.

== Installation ==

1. Upload the `wpcalibrate-menu-control` folder to your `/wp-content/plugins/` directory, or upload `wpcalibrate-menu-control.zip` via **Plugins > Add New > Upload Plugin**.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Configure global feature options under **WPCalibrate > Menu Control**.
4. Open **Appearance > Menus** and expand any menu item to configure item-level icons, badges, subtitles, visibility conditions, or mega menu options.

== Frequently Asked Questions ==

= Does this plugin require WooCommerce? =
No. WooCommerce is entirely optional. When WooCommerce is installed, dynamic cart and account items and WooCommerce condition rules become available. When WooCommerce is absent, the plugin operates smoothly with zero fatal errors.

= Is WooCommerce High-Performance Order Storage (HPOS) supported? =
Yes. WPCalibrate Menu Control declares official compatibility with WooCommerce HPOS (`custom_order_tables`) and Cart/Checkout Blocks (`cart_checkout_blocks`).

= What happens on plugin uninstall? =
By default, the plugin retains settings and metadata upon uninstallation to prevent accidental data loss. You can change this behavior in **WPCalibrate > Menu Control > General** to delete all plugin options and metadata upon uninstall. The plugin never deletes WordPress menus or items belonging to other plugins.

= Does this plugin support Full Site Editing (FSE) block themes? =
Yes. The plugin provides safe frontend filtering for core Navigation block links. Advanced multi-column mega menu configurations apply to classic nav menus.

= Where do I configure licensing? =
Under **WPCalibrate > Menu Control > License**, you can enter and store your license key. Remote activation endpoints are marked unconfigured pending provider API specifications from WPCalibrate.

== Developer Hooks & Filters ==

WPCalibrate Menu Control provides clean, prefixed extension hooks:

* `wpcalibrate_menu_control_register_conditions`: Action to register custom `ConditionInterface` providers with the `ConditionRegistry`.
* `wpcalibrate_menu_control_item_visibility`: Filter boolean visibility for a menu item (`$is_visible, $condition_data, $item_id`).
* `wpcalibrate_menu_control_allowed_badge_variants`: Filter allowed badge style slugs.
* `wpcalibrate_menu_control_detected_device`: Filter detected device group (`desktop`, `tablet`, `mobile`).
* `wpcalibrate_menu_control_filtered_menu_objects`: Filter sorted menu items after condition pruning.
* `wpcalibrate_menu_control_item_classes`: Filter CSS classes applied to each `<li>` menu item.
* `wpcalibrate_menu_control_sanitize_settings`: Filter sanitized global settings array.

== Changelog ==

= 1.0.1 =
* Fixed admin sidebar menu icon color tone and contrast using CSS mask technique matching native WordPress dashboard icon colors (#a7aaad base, #72aee6 hover, #ffffff active).
* Added comprehensive Overview tab in settings covering plugin purpose, visual workflow, and developer methods.
* Integrated native GitHub updater for seamless in-dashboard WordPress update notifications and 1-click installations.
* Added official GNU GPLv2 license file (LICENSE.txt).
* Streamlined production plugin packaging.

= 1.0.0 =
* Initial public release.
* Conditional visibility engine supporting user status, roles, device, page context, post types, URL match, and site timezone date/time schedules.
* Support for menu item icons (Dashicons/SVG), badges, subtitles, highlight states, and non-clickable headings.
* Lightweight mega menu grid dropdowns (1-6 columns) with mobile accordion degradation.
* Optional WooCommerce dynamic items (cart, live count, subtotal, my account, login/logout) and HPOS declaration.
* Core Navigation block link frontend enhancement.
* Settings import and export with strict schema validation.
* Cooperative WPCalibrate top-level admin menu integration.
