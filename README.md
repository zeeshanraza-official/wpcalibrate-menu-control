# WPCalibrate Menu Control

[![WordPress Version](https://img.shields.io/badge/WordPress-6.8%20to%207.1.x-blue.svg)](https://wordpress.org)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL%202.0%2B-green.svg)](LICENSE.txt)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-HPOS%20%26%20Blocks%20Compatible-96588a.svg)](https://woocommerce.com)
[![Version](https://img.shields.io/badge/Version-1.0.1-orange.svg)](https://github.com/zeeshanraza-official/wpcalibrate-menu-control/releases)

**WPCalibrate Menu Control** is a high-performance, lightweight WordPress navigation enhancement plugin built with PHP 8.2+ strict types and clean WordPress core APIs. It empowers site administrators to take granular control over WordPress navigation menus without writing code, modifying active theme templates, or installing resource-heavy visual page builders.

---

## Table of Contents
1. [About](#about)
2. [Key Features](#key-features)
3. [Architecture & Performance](#architecture--performance)
4. [Documentation & Usage Guide](#documentation--usage-guide)
   - [Managing Menu Items](#managing-menu-items)
   - [Conditional Visibility Engine](#conditional-visibility-engine)
   - [Menu Item Badges](#menu-item-badges)
   - [Icons & Custom SVG](#icons--custom-svg)
   - [Lightweight Mega Menus](#lightweight-mega-menus)
   - [WooCommerce Dynamic Elements](#woocommerce-dynamic-elements)
5. [Developer Hooks & API](#developer-hooks--api)
6. [In-Dashboard Updates](#in-dashboard-updates)
7. [Changelog](#changelog)
8. [Support & Contact](#support--contact)
9. [License](#license)

---

## About

Navigation menus are the backbone of user experience and site conversions. Most themes offer rigid, all-or-nothing menu structures, forcing store owners and publishers into complex custom templates or bloated third-party page builders.

**WPCalibrate Menu Control** solves this by inserting non-invasive, accessible controls directly into standard WordPress menus:
- **Appearance > Menus** for classic nav menus.
- **`core/navigation-link` & `core/navigation-submenu`** block filters for modern block themes.

Built from the ground up for modern enterprise environments, it features zero jQuery on the frontend, batch database querying to prevent N+1 overhead, and in-request MD5 hashing for condition evaluation.

---

## Key Features

### 🎯 Conditional Visibility Engine
- **User Authentication:** Show menu items exclusively to logged-in users or guests/logged-out visitors.
- **User Role Targeting:** Filter by specific WordPress roles (`administrator`, `editor`, `author`, `shop_manager`, `customer`, `subscriber`, etc.) with positive and inverted (`is_not`) matching.
- **Device Detection:** Target `desktop`, `tablet`, or `mobile` devices using server-side device detection.
- **Page & Content Context:** Target front page, blog index, single posts, static pages, specific post types, or archive views.
- **URL & Path Matching:** Display items based on current URL path matching (`equals`, `contains`, `starts_with`, or `is_not`).
- **Timezone-Aware Scheduling:** Schedule promotional or seasonal menu items with start and end date/time boundaries evaluated in the site's configured local timezone (`wp_timezone()`).
- **Flexible Logic:** Combine multiple conditions using deterministic **AND** (all rules must match) or **OR** (any rule matches) logic.

### 🏷️ Semantic Badges & Callouts
- Add vibrant callout badges (such as *New*, *Hot*, *Sale*, *Pro*, or custom text).
- 8 semantic style variants: `primary`, `secondary`, `accent`, `success`, `warning`, `danger`, `info`, and `neutral`.
- Configurable border-radius, font sizing, and margins via plugin settings.

### 🎨 Icons & Accessible SVG
- Attach native WordPress **Dashicons** or safe **custom SVG** markup.
- Configurable icon position: **Before** or **After** the menu item label.
- Screen-reader accessible: automatically enforces `aria-hidden="true"`.
- Defense-in-depth SVG sanitization strips malicious scripts, external entity declarations, and inline `on*` event handlers.

### 📐 Lightweight Mega Menus
- Convert any standard classic top-level menu item into an accessible multi-column mega menu dropdown.
- Configurable column grids: **1, 2, 3, 4, 5, or 6 columns**.
- Container width modes: **Content Width** or **Full Container Width**.
- Dropdown alignment: **Left**, **Center**, or **Right**.
- Native accessibility: keyboard navigation with <kbd>Esc</kbd> closure, touch-device toggles, and seamless mobile accordion collapse (`<= 768px`).

### 🛒 WooCommerce Dynamic Elements (Optional)
- **Live Cart Item:** Add `#wpcalibrate-wc-cart` for a dynamic link synchronized in real time via `woocommerce_add_to_cart_fragments`.
- **Dynamic Cart Tokens:** Use `{cart_count}` or `{cart_total}` inside menu item titles or badges to display live item counts and cart subtotal.
- **My Account & Login/Logout:** Use `#wpcalibrate-wc-account` and `#wpcalibrate-wc-login-logout` to show dynamic customer account and authentication links.
- **WooCommerce Conditions:** Restrict links to active WooCommerce pages (`shop`, `cart`, `checkout`, `account`) or customer order history.
- **HPOS Ready:** Fully declares compatibility with High-Performance Order Storage (`custom_order_tables`) and Cart/Checkout Blocks.

### 📦 Administration & Utilities
- **Overview Guide:** Built-in interactive visual guide in the WordPress admin explaining features, workflows, and developer methods.
- **Settings Import & Export:** Export and restore configuration in versioned, strictly schema-validated JSON format.
- **Diagnostics & Status:** Instant environment check (PHP, WordPress, WooCommerce, HPOS status, active theme).
- **Safe Uninstallation:** Preserves all data by default on deactivation; optional complete cleanup on deletion.

---

## Architecture & Performance

WPCalibrate Menu Control is engineered following WordPress VIP and enterprise performance guidelines:

| Metric | Implementation Detail |
|---|---|
| **PHP Version** | PHP 8.2+ with strict typing (`declare(strict_types=1);`) across all classes. |
| **Database Queries** | **Zero N+1 queries.** Pre-fetches all menu item postmeta in a single batch query via `update_meta_cache('post')`. |
| **Condition Cache** | In-memory MD5 evaluation hashing prevents duplicate evaluation during the request lifecycle. |
| **Frontend Assets** | Pure vanilla JavaScript (`0 KB` external dependencies, zero jQuery). Scoped CSS custom properties. |
| **Responsive Support** | Respects `prefers-reduced-motion` and supports RTL layouts with logical CSS properties (`margin-inline`, `padding-inline`). |

---

## Documentation & Usage Guide

### Managing Menu Items
1. Navigate to **Appearance > Menus** in your WordPress admin dashboard.
2. Click the disclosure arrow on any menu item to expand its settings.
3. You will see dedicated panels added by **WPCalibrate Menu Control**:
   - **General & Presentation:** Subtitle, Badge text, Badge style, Highlight toggle, Non-clickable heading toggle.
   - **Icon Settings:** Icon type (None / Dashicon / Custom SVG), Dashicon class, SVG code, Icon position.
   - **Mega Menu Settings:** Enable mega menu, Column count (1–6), Width mode, Dropdown alignment.
   - **Conditional Visibility:** Match logic (AND / OR), Add Rule (User, Role, Device, Context, URL, Schedule, WooCommerce).
4. Save the menu.

### Conditional Visibility Engine
Conditions allow you to build personalized, dynamic navigation experiences:

```
[ Rule Logic: ALL (AND) ]
├── Rule 1: User Status = Logged In
└── Rule 2: User Role = Wholesale Customer
```

- **User Status:** `logged_in` or `logged_out`.
- **User Role:** Match any registered WordPress role or use `is_not` to invert the condition.
- **Schedule:** Set starting and ending date/time. The engine checks against the site's local timezone configured under **Settings > General**, avoiding server timezone offsets.
- **URL Match:** Normalize trailing slashes and evaluate path patterns.

### Menu Item Badges
- Set Badge Text (e.g. `New`, `Sale`, `20% OFF`).
- Pick a semantic variant:
  - `primary`: Royal Blue
  - `secondary`: Slate Gray
  - `accent`: Electric Indigo
  - `success`: Emerald Green
  - `warning`: Amber
  - `danger`: Crimson Red
  - `info`: Sky Blue
  - `neutral`: Soft Cool Gray

### Lightweight Mega Menus
1. Check **Enable Mega Menu on this item** (on a top-level menu item).
2. Select your column grid (e.g., **4 Columns**).
3. Select width: **Content Width** or **Full Container Width**.
4. Drag sub-items beneath this item in standard WordPress nested hierarchy:
   - First-level sub-items act as **Column Headers**.
   - Second-level sub-items act as links under each column.

### WooCommerce Dynamic Elements
To insert live WooCommerce links, add a **Custom Link** menu item with one of the following special URLs:

| URL Target | Behavior |
|---|---|
| `#wpcalibrate-wc-cart` | Automatically links to your WooCommerce cart page. Replaced dynamically with live cart URL. |
| `#wpcalibrate-wc-account` | Automatically links to the My Account page. |
| `#wpcalibrate-wc-login-logout` | Automatically links to Login when logged out, or Logout with nonce when logged in. |

You can use dynamic tokens anywhere in the menu title or badge text:
- `{cart_count}` — Displays current number of items in the cart (e.g. `Cart ({cart_count})`).
- `{cart_total}` — Displays formatted cart subtotal (e.g. `Checkout ({cart_total})`).
- `{user_name}` — Displays the current logged-in user's display name.

---

## Developer Hooks & API

Extend or customize WPCalibrate Menu Control using WordPress action and filter hooks:

### Register a Custom Condition Provider
```php
use WPCalibrate\MenuControl\Conditions\ConditionInterface;
use WPCalibrate\MenuControl\Conditions\ConditionRegistry;

add_action( 'wpcalibrate_menu_control_register_conditions', function( ConditionRegistry $registry ) {
    $registry->register( new class implements ConditionInterface {
        public function get_id(): string {
            return 'custom_membership';
        }

        public function get_label(): string {
            return __( 'Membership Level', 'my-plugin' );
        }

        public function evaluate( array $rule, array $context = array() ): bool {
            $required_level = $rule['value'] ?? '';
            return current_user_has_membership( $required_level );
        }
    } );
} );
```

### Additional Available Filters
- `wpcalibrate_menu_control_item_visibility` — `($is_visible, $condition_data, $item_id)`: Override visibility for any menu item.
- `wpcalibrate_menu_control_allowed_badge_variants` — `($variants)`: Add custom badge color variant slugs.
- `wpcalibrate_menu_control_detected_device` — `($device)`: Override device detection result (`desktop`, `tablet`, `mobile`).
- `wpcalibrate_menu_control_filtered_menu_objects` — `($items)`: Access sorted menu item objects after condition pruning.
- `wpcalibrate_menu_control_item_classes` — `($classes, $item)`: Customize HTML classes applied to menu item `<li>` elements.
- `wpcalibrate_menu_control_sanitize_settings` — `($clean, $raw)`: Filter sanitized global settings array.

---

## In-Dashboard Updates

WPCalibrate Menu Control includes a native GitHub Release updater. When a new version is released on GitHub:
1. WordPress will display an update notification on your **Dashboard > Updates** and **Plugins** screens.
2. Clicking **Update Now** automatically downloads and extracts the verified release package directly from GitHub releases.
3. No external updater plugins required.

To force an immediate check, visit **Dashboard > Updates** and click **Check Again**.

---

## Changelog

### 1.0.1 (2026-10-14)
- **Fixed:** WordPress admin sidebar menu icon color tone and contrast using CSS mask technique matching native WordPress dashboard icon colors (`#a7aaad` base, `#72aee6` hover, `#ffffff` active).
- **Added:** Comprehensive **Overview** tab in plugin settings detailing product purpose, visual step-by-step workflow, and developer methods.
- **Added:** Native GitHub updater integration for seamless in-dashboard WordPress update notifications and 1-click installations.
- **Added:** Official GNU GPLv2 license file (`LICENSE.txt`).
- **Packaging:** Streamlined production plugin packaging adhering to WordPress.org and GitHub release standards.

### 1.0.0 (2026-10-14)
- Initial public release.
- Conditional visibility engine (user status, roles, device, page context, post types, URL match, site timezone schedule).
- Menu enhancements (Dashicons, sanitized SVG icons, 8 semantic badge variants, subtitles, non-clickable headings).
- Lightweight mega menus with 1–6 column grids, responsive touch support, and mobile accordion degradation.
- WooCommerce dynamic cart/account links, live fragment updates, and HPOS compatibility.
- Settings import and export with strict schema validation.
- Cooperative WPCalibrate top-level admin menu integration.

---

## Support & Contact

- **Plugin Website:** [https://wpcalibrate.com](https://wpcalibrate.com)
- **Marketplace:** [https://marketplace.wpcalibrate.com/](https://marketplace.wpcalibrate.com/)
- **Technical Support:** [support@wpcalibrate.com](mailto:support@wpcalibrate.com)
- **Phone / WhatsApp:** +447474795976

---

## License

This plugin is free software licensed under the **GNU General Public License v2.0 or later**. See [LICENSE.txt](LICENSE.txt) for the full license text.
