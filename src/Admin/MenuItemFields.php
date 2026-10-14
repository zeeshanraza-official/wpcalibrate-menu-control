<?php
/**
 * Classic menu item custom fields and metadata persistence.
 *
 * @package WPCalibrate\MenuControl
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Admin;

use WPCalibrate\MenuControl\Support\Capabilities;
use WPCalibrate\MenuControl\Support\Sanitizer;
use WPCalibrate\MenuControl\Support\Helpers;

/**
 * Class MenuItemFields
 */
final class MenuItemFields {

	public const META_PREFIX = '_wpcalibrate_menu_control_';

	/**
	 * Settings instance.
	 */
	private Settings $settings;

	/**
	 * Constructor.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register hooks.
	 */
	public function register(): void {
		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'render_custom_fields' ), 10, 5 );
		add_action( 'wp_update_nav_menu_item', array( $this, 'save_custom_fields' ), 10, 3 );
	}

	/**
	 * Render custom fields inside nav menu item edit form.
	 *
	 * @param int       $item_id
	 * @param \WP_Post  $item
	 * @param int       $depth
	 * @param \stdClass $args
	 * @param int|null  $id
	 */
	public function render_custom_fields( int $item_id, \WP_Post $item, int $depth, \stdClass $args, ?int $id = null ): void {
		$options = $this->settings->get_all();

		// Fetch existing metadata.
		$icon           = (string) get_post_meta( $item_id, self::META_PREFIX . 'icon', true );
		$icon_pos       = (string) get_post_meta( $item_id, self::META_PREFIX . 'icon_position', true ) ?: 'before';
		$badge_text     = (string) get_post_meta( $item_id, self::META_PREFIX . 'badge_text', true );
		$badge_variant  = (string) get_post_meta( $item_id, self::META_PREFIX . 'badge_variant', true ) ?: 'primary';
		$subtitle       = (string) get_post_meta( $item_id, self::META_PREFIX . 'subtitle', true );
		$highlight      = (bool) get_post_meta( $item_id, self::META_PREFIX . 'highlight', true );
		$non_clickable  = (bool) get_post_meta( $item_id, self::META_PREFIX . 'non_clickable', true );
		$rel_values     = (array) ( get_post_meta( $item_id, self::META_PREFIX . 'rel_values', true ) ?: array() );
		$conditions_on  = (bool) get_post_meta( $item_id, self::META_PREFIX . 'conditions_enabled', true );
		$raw_conditions = get_post_meta( $item_id, self::META_PREFIX . 'condition_rules', true );
		$condition_rules = is_array( $raw_conditions ) ? $raw_conditions : array( 'logic' => 'and', 'rules' => array() );

		// Mega menu (primarily for top-level items, depth 0).
		$mega_menu_on   = (bool) get_post_meta( $item_id, self::META_PREFIX . 'mega_menu_enabled', true );
		$mega_columns   = (int) ( get_post_meta( $item_id, self::META_PREFIX . 'mega_menu_columns', true ) ?: 4 );
		$mega_width     = (string) get_post_meta( $item_id, self::META_PREFIX . 'mega_menu_width', true ) ?: 'content';
		$mega_align     = (string) get_post_meta( $item_id, self::META_PREFIX . 'mega_menu_alignment', true ) ?: 'left';

		wp_nonce_field( 'wpcalibrate_menu_control_item_nonce_action', 'wpcalibrate_menu_control_item_nonce_' . $item_id );
		?>
		<div class="wpcalibrate-menu-item-controls" data-item-id="<?php echo esc_attr( (string) $item_id ); ?>" data-depth="<?php echo esc_attr( (string) $depth ); ?>">
			<div class="wpcalibrate-controls-header">
				<span class="wpcalibrate-badge-logo">WPCalibrate</span>
				<strong><?php esc_html_e( 'Menu Control Settings', 'wpcalibrate-menu-control' ); ?></strong>
				<button type="button" class="button-link wpcalibrate-toggle-panel" aria-expanded="false">
					<?php esc_html_e( 'Configure', 'wpcalibrate-menu-control' ); ?>
				</button>
			</div>

			<div class="wpcalibrate-controls-body" style="display: none;">

				<?php if ( ! empty( $options['enable_icons'] ) ) : ?>
					<div class="wpcalibrate-field-row">
						<label for="wpcalibrate_icon_<?php echo esc_attr( (string) $item_id ); ?>">
							<?php esc_html_e( 'Icon (Dashicon class or slug, e.g. dashicons-star-filled)', 'wpcalibrate-menu-control' ); ?>
						</label>
						<input type="text"
							id="wpcalibrate_icon_<?php echo esc_attr( (string) $item_id ); ?>"
							name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][icon]"
							value="<?php echo esc_attr( $icon ); ?>"
							class="widefat code"
							placeholder="dashicons-star-filled" />
						
						<label for="wpcalibrate_icon_pos_<?php echo esc_attr( (string) $item_id ); ?>" class="screen-reader-text">
							<?php esc_html_e( 'Icon Position', 'wpcalibrate-menu-control' ); ?>
						</label>
						<select id="wpcalibrate_icon_pos_<?php echo esc_attr( (string) $item_id ); ?>"
							name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][icon_position]">
							<option value="before" <?php selected( $icon_pos, 'before' ); ?>><?php esc_html_e( 'Before label', 'wpcalibrate-menu-control' ); ?></option>
							<option value="after" <?php selected( $icon_pos, 'after' ); ?>><?php esc_html_e( 'After label', 'wpcalibrate-menu-control' ); ?></option>
						</select>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $options['enable_badges'] ) ) : ?>
					<div class="wpcalibrate-field-row wpcalibrate-badge-group">
						<label for="wpcalibrate_badge_text_<?php echo esc_attr( (string) $item_id ); ?>">
							<?php esc_html_e( 'Badge Text', 'wpcalibrate-menu-control' ); ?>
						</label>
						<input type="text"
							id="wpcalibrate_badge_text_<?php echo esc_attr( (string) $item_id ); ?>"
							name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][badge_text]"
							value="<?php echo esc_attr( $badge_text ); ?>"
							class="widefat"
							maxlength="30"
							placeholder="<?php esc_attr_e( 'New, Sale, Hot...', 'wpcalibrate-menu-control' ); ?>" />

						<label for="wpcalibrate_badge_var_<?php echo esc_attr( (string) $item_id ); ?>">
							<?php esc_html_e( 'Badge Style', 'wpcalibrate-menu-control' ); ?>
						</label>
						<select id="wpcalibrate_badge_var_<?php echo esc_attr( (string) $item_id ); ?>"
							name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][badge_variant]">
							<?php foreach ( Sanitizer::ALLOWED_BADGE_VARIANTS as $variant ) : ?>
								<option value="<?php echo esc_attr( $variant ); ?>" <?php selected( $badge_variant, $variant ); ?>>
									<?php echo esc_html( ucfirst( $variant ) ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $options['enable_subtitles'] ) ) : ?>
					<div class="wpcalibrate-field-row">
						<label for="wpcalibrate_subtitle_<?php echo esc_attr( (string) $item_id ); ?>">
							<?php esc_html_e( 'Subtitle / Secondary Text', 'wpcalibrate-menu-control' ); ?>
						</label>
						<input type="text"
							id="wpcalibrate_subtitle_<?php echo esc_attr( (string) $item_id ); ?>"
							name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][subtitle]"
							value="<?php echo esc_attr( $subtitle ); ?>"
							class="widefat"
							placeholder="<?php esc_attr_e( 'Short descriptive text below menu title', 'wpcalibrate-menu-control' ); ?>" />
					</div>
				<?php endif; ?>

				<div class="wpcalibrate-field-row wpcalibrate-toggles">
					<label>
						<input type="checkbox"
							name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][highlight]"
							value="1"
							<?php checked( $highlight ); ?> />
						<?php esc_html_e( 'Highlight / Accent Item (Call-to-Action)', 'wpcalibrate-menu-control' ); ?>
					</label>

					<?php if ( ! empty( $options['enable_non_clickable'] ) ) : ?>
						<label>
							<input type="checkbox"
								name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][non_clickable]"
								value="1"
								<?php checked( $non_clickable ); ?> />
							<?php esc_html_e( 'Non-Clickable Heading (Disables link, safe semantic header)', 'wpcalibrate-menu-control' ); ?>
						</label>
					<?php endif; ?>
				</div>

				<div class="wpcalibrate-field-row wpcalibrate-rel-group">
					<fieldset>
						<legend><strong><?php esc_html_e( 'Link Rel Attributes (SEO & Safety)', 'wpcalibrate-menu-control' ); ?></strong></legend>
						<?php foreach ( Sanitizer::ALLOWED_REL_VALUES as $rel_val ) : ?>
							<label style="margin-right: 12px;">
								<input type="checkbox"
									name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][rel_values][]"
									value="<?php echo esc_attr( $rel_val ); ?>"
									<?php checked( in_array( $rel_val, $rel_values, true ) ); ?> />
								<code><?php echo esc_html( $rel_val ); ?></code>
							</label>
						<?php endforeach; ?>
					</fieldset>
				</div>

				<?php if ( ! empty( $options['enable_mega_menu'] ) ) : ?>
					<div class="wpcalibrate-field-section wpcalibrate-mega-menu-section" style="<?php echo 0 === $depth ? '' : 'display:none;'; ?>">
						<h4><?php esc_html_e( 'Mega Menu Settings (Top-Level Items Only)', 'wpcalibrate-menu-control' ); ?></h4>
						<label>
							<input type="checkbox"
								class="wpcalibrate-mega-menu-toggle"
								name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][mega_menu_enabled]"
								value="1"
								<?php checked( $mega_menu_on ); ?> />
							<?php esc_html_e( 'Enable Mega Menu dropdown for this item', 'wpcalibrate-menu-control' ); ?>
						</label>

						<div class="wpcalibrate-mega-options" style="<?php echo $mega_menu_on ? '' : 'display:none;'; ?> margin-top: 8px;">
							<label for="wpcalibrate_mega_cols_<?php echo esc_attr( (string) $item_id ); ?>">
								<?php esc_html_e( 'Columns:', 'wpcalibrate-menu-control' ); ?>
							</label>
							<select id="wpcalibrate_mega_cols_<?php echo esc_attr( (string) $item_id ); ?>"
								name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][mega_menu_columns]">
								<?php for ( $c = 1; $c <= 6; $c++ ) : ?>
									<option value="<?php echo esc_attr( (string) $c ); ?>" <?php selected( $mega_columns, $c ); ?>>
										<?php echo esc_html( sprintf( _n( '%d Column', '%d Columns', $c, 'wpcalibrate-menu-control' ), $c ) ); ?>
									</option>
								<?php endfor; ?>
							</select>

							<label for="wpcalibrate_mega_width_<?php echo esc_attr( (string) $item_id ); ?>" style="margin-left: 10px;">
								<?php esc_html_e( 'Width:', 'wpcalibrate-menu-control' ); ?>
							</label>
							<select id="wpcalibrate_mega_width_<?php echo esc_attr( (string) $item_id ); ?>"
								name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][mega_menu_width]">
								<option value="content" <?php selected( $mega_width, 'content' ); ?>><?php esc_html_e( 'Content Width', 'wpcalibrate-menu-control' ); ?></option>
								<option value="full" <?php selected( $mega_width, 'full' ); ?>><?php esc_html_e( 'Full Width (Container)', 'wpcalibrate-menu-control' ); ?></option>
							</select>

							<label for="wpcalibrate_mega_align_<?php echo esc_attr( (string) $item_id ); ?>" style="margin-left: 10px;">
								<?php esc_html_e( 'Alignment:', 'wpcalibrate-menu-control' ); ?>
							</label>
							<select id="wpcalibrate_mega_align_<?php echo esc_attr( (string) $item_id ); ?>"
								name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][mega_menu_alignment]">
								<option value="left" <?php selected( $mega_align, 'left' ); ?>><?php esc_html_e( 'Left', 'wpcalibrate-menu-control' ); ?></option>
								<option value="center" <?php selected( $mega_align, 'center' ); ?>><?php esc_html_e( 'Center', 'wpcalibrate-menu-control' ); ?></option>
								<option value="right" <?php selected( $mega_align, 'right' ); ?>><?php esc_html_e( 'Right', 'wpcalibrate-menu-control' ); ?></option>
							</select>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $options['enable_conditions'] ) ) : ?>
					<div class="wpcalibrate-field-section wpcalibrate-conditions-section">
						<h4><?php esc_html_e( 'Conditional Visibility Rules', 'wpcalibrate-menu-control' ); ?></h4>
						<label>
							<input type="checkbox"
								class="wpcalibrate-conditions-toggle"
								name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][conditions_enabled]"
								value="1"
								<?php checked( $conditions_on ); ?> />
							<?php esc_html_e( 'Enable conditional visibility for this menu item', 'wpcalibrate-menu-control' ); ?>
						</label>

						<div class="wpcalibrate-conditions-container" style="<?php echo $conditions_on ? '' : 'display:none;'; ?> margin-top: 8px;">
							<div class="wpcalibrate-condition-logic-select">
								<label>
									<?php esc_html_e( 'Show item when:', 'wpcalibrate-menu-control' ); ?>
									<select name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][condition_rules][logic]">
										<option value="and" <?php selected( $condition_rules['logic'] ?? 'and', 'and' ); ?>><?php esc_html_e( 'ALL rules are met (AND)', 'wpcalibrate-menu-control' ); ?></option>
										<option value="or" <?php selected( $condition_rules['logic'] ?? 'and', 'or' ); ?>><?php esc_html_e( 'ANY rule is met (OR)', 'wpcalibrate-menu-control' ); ?></option>
									</select>
								</label>
							</div>

							<div class="wpcalibrate-rules-list" data-item-id="<?php echo esc_attr( (string) $item_id ); ?>">
								<?php
								$rules = $condition_rules['rules'] ?? array();
								if ( empty( $rules ) ) {
									$rules = array(
										array(
											'type'     => 'user_status',
											'operator' => 'is',
											'value'    => 'logged_in',
										),
									);
								}
								foreach ( $rules as $index => $rule ) :
									$type     = $rule['type'] ?? 'user_status';
									$operator = $rule['operator'] ?? 'is';
									$value    = $rule['value'] ?? '';
									?>
									<div class="wpcalibrate-rule-row" data-index="<?php echo esc_attr( (string) $index ); ?>">
										<select name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][condition_rules][rules][<?php echo esc_attr( (string) $index ); ?>][type]" class="wpcalibrate-rule-type-select">
											<optgroup label="<?php esc_attr_e( 'User Rules', 'wpcalibrate-menu-control' ); ?>">
												<option value="user_status" <?php selected( $type, 'user_status' ); ?>><?php esc_html_e( 'User Status (Logged in / Logged out)', 'wpcalibrate-menu-control' ); ?></option>
												<option value="user_role" <?php selected( $type, 'user_role' ); ?>><?php esc_html_e( 'User Role', 'wpcalibrate-menu-control' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Device Rules', 'wpcalibrate-menu-control' ); ?>">
												<option value="device_group" <?php selected( $type, 'device_group' ); ?>><?php esc_html_e( 'Device Group (Desktop / Mobile / Tablet)', 'wpcalibrate-menu-control' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Content & URL Rules', 'wpcalibrate-menu-control' ); ?>">
												<option value="page_context" <?php selected( $type, 'page_context' ); ?>><?php esc_html_e( 'Page Context (Home, Archive, Singular)', 'wpcalibrate-menu-control' ); ?></option>
												<option value="post_type" <?php selected( $type, 'post_type' ); ?>><?php esc_html_e( 'Post Type', 'wpcalibrate-menu-control' ); ?></option>
												<option value="url_path" <?php selected( $type, 'url_path' ); ?>><?php esc_html_e( 'URL / Path Match', 'wpcalibrate-menu-control' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Schedule Rules', 'wpcalibrate-menu-control' ); ?>">
												<option value="schedule_start" <?php selected( $type, 'schedule_start' ); ?>><?php esc_html_e( 'Date/Time: Start After', 'wpcalibrate-menu-control' ); ?></option>
												<option value="schedule_end" <?php selected( $type, 'schedule_end' ); ?>><?php esc_html_e( 'Date/Time: End Before', 'wpcalibrate-menu-control' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'WooCommerce Rules', 'wpcalibrate-menu-control' ); ?>">
												<option value="wc_context" <?php selected( $type, 'wc_context' ); ?>><?php esc_html_e( 'WooCommerce Context (Shop, Cart, Checkout, Account)', 'wpcalibrate-menu-control' ); ?></option>
												<option value="wc_customer" <?php selected( $type, 'wc_customer' ); ?>><?php esc_html_e( 'WooCommerce Customer State (Has Orders, Guest)', 'wpcalibrate-menu-control' ); ?></option>
											</optgroup>
										</select>

										<select name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][condition_rules][rules][<?php echo esc_attr( (string) $index ); ?>][operator]" class="wpcalibrate-rule-operator-select">
											<option value="is" <?php selected( $operator, 'is' ); ?>><?php esc_html_e( 'is', 'wpcalibrate-menu-control' ); ?></option>
											<option value="is_not" <?php selected( $operator, 'is_not' ); ?>><?php esc_html_e( 'is not', 'wpcalibrate-menu-control' ); ?></option>
										</select>

										<input type="text"
											name="wpcalibrate_menu_item[<?php echo esc_attr( (string) $item_id ); ?>][condition_rules][rules][<?php echo esc_attr( (string) $index ); ?>][value]"
											value="<?php echo esc_attr( is_array( $value ) ? implode( ',', $value ) : (string) $value ); ?>"
											class="wpcalibrate-rule-value-input regular-text"
											placeholder="<?php esc_attr_e( 'Rule parameter / value', 'wpcalibrate-menu-control' ); ?>" />

										<button type="button" class="button wpcalibrate-remove-rule" title="<?php esc_attr_e( 'Remove rule', 'wpcalibrate-menu-control' ); ?>">&times;</button>
									</div>
								<?php endforeach; ?>
							</div>

							<div class="wpcalibrate-add-rule-actions" style="margin-top: 8px;">
								<button type="button" class="button wpcalibrate-add-rule" data-item-id="<?php echo esc_attr( (string) $item_id ); ?>">
									+ <?php esc_html_e( 'Add Rule', 'wpcalibrate-menu-control' ); ?>
								</button>
								<span class="description" style="margin-left: 8px;">
									<?php
									printf(
										/* translators: %s: Current site timezone */
										esc_html__( 'Time-based rules use the site timezone (%s).', 'wpcalibrate-menu-control' ),
										esc_html( Helpers::get_site_timezone_string() )
									);
									?>
								</span>
							</div>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</div>
		<?php
	}

	/**
	 * Save custom fields when nav menu is saved.
	 *
	 * @param int   $menu_id
	 * @param int   $menu_item_db_id
	 * @param array $args
	 */
	public function save_custom_fields( int $menu_id, int $menu_item_db_id, array $args ): void {
		if ( ! Capabilities::current_user_can_edit_menus() ) {
			return;
		}

		// Verify nonce for this specific menu item if submitted via normal form.
		if ( ! isset( $_POST[ 'wpcalibrate_menu_control_item_nonce_' . $menu_item_db_id ] ) ||
			! wp_verify_nonce( sanitize_key( $_POST[ 'wpcalibrate_menu_control_item_nonce_' . $menu_item_db_id ] ), 'wpcalibrate_menu_control_item_nonce_action' ) ) {
			return;
		}

		if ( ! isset( $_POST['wpcalibrate_menu_item'][ $menu_item_db_id ] ) || ! is_array( $_POST['wpcalibrate_menu_item'][ $menu_item_db_id ] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		$data = $_POST['wpcalibrate_menu_item'][ $menu_item_db_id ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		// 1. Icon & Position.
		$icon = isset( $data['icon'] ) ? Sanitizer::sanitize_icon( (string) $data['icon'] ) : '';
		if ( ! empty( $icon ) ) {
			update_post_meta( $menu_item_db_id, self::META_PREFIX . 'icon', $icon );
		} else {
			delete_post_meta( $menu_item_db_id, self::META_PREFIX . 'icon' );
		}

		$icon_pos = isset( $data['icon_position'] ) ? Sanitizer::sanitize_icon_position( (string) $data['icon_position'] ) : 'before';
		update_post_meta( $menu_item_db_id, self::META_PREFIX . 'icon_position', $icon_pos );

		// 2. Badge & Variant.
		$badge_text = isset( $data['badge_text'] ) ? sanitize_text_field( (string) $data['badge_text'] ) : '';
		if ( '' !== $badge_text ) {
			update_post_meta( $menu_item_db_id, self::META_PREFIX . 'badge_text', $badge_text );
		} else {
			delete_post_meta( $menu_item_db_id, self::META_PREFIX . 'badge_text' );
		}

		$badge_variant = isset( $data['badge_variant'] ) ? Sanitizer::sanitize_badge_variant( (string) $data['badge_variant'] ) : 'primary';
		update_post_meta( $menu_item_db_id, self::META_PREFIX . 'badge_variant', $badge_variant );

		// 3. Subtitle.
		$subtitle = isset( $data['subtitle'] ) ? sanitize_text_field( (string) $data['subtitle'] ) : '';
		if ( '' !== $subtitle ) {
			update_post_meta( $menu_item_db_id, self::META_PREFIX . 'subtitle', $subtitle );
		} else {
			delete_post_meta( $menu_item_db_id, self::META_PREFIX . 'subtitle' );
		}

		// 4. Highlight & Non-clickable.
		$highlight = ! empty( $data['highlight'] ) ? 1 : 0;
		update_post_meta( $menu_item_db_id, self::META_PREFIX . 'highlight', $highlight );

		$non_clickable = ! empty( $data['non_clickable'] ) ? 1 : 0;
		update_post_meta( $menu_item_db_id, self::META_PREFIX . 'non_clickable', $non_clickable );

		// 5. Rel values.
		$rel_values = isset( $data['rel_values'] ) ? Sanitizer::sanitize_rel_values( $data['rel_values'] ) : array();
		if ( ! empty( $rel_values ) ) {
			update_post_meta( $menu_item_db_id, self::META_PREFIX . 'rel_values', $rel_values );
		} else {
			delete_post_meta( $menu_item_db_id, self::META_PREFIX . 'rel_values' );
		}

		// 6. Mega Menu.
		$mega_enabled = ! empty( $data['mega_menu_enabled'] ) ? 1 : 0;
		update_post_meta( $menu_item_db_id, self::META_PREFIX . 'mega_menu_enabled', $mega_enabled );

		$mega_cols = isset( $data['mega_menu_columns'] ) ? Sanitizer::sanitize_mega_menu_columns( $data['mega_menu_columns'] ) : 4;
		update_post_meta( $menu_item_db_id, self::META_PREFIX . 'mega_menu_columns', $mega_cols );

		$mega_width = isset( $data['mega_menu_width'] ) ? Sanitizer::sanitize_mega_menu_width( (string) $data['mega_menu_width'] ) : 'content';
		update_post_meta( $menu_item_db_id, self::META_PREFIX . 'mega_menu_width', $mega_width );

		$mega_align = isset( $data['mega_menu_alignment'] ) ? Sanitizer::sanitize_mega_menu_alignment( (string) $data['mega_menu_alignment'] ) : 'left';
		update_post_meta( $menu_item_db_id, self::META_PREFIX . 'mega_menu_alignment', $mega_align );

		// 7. Conditions.
		$conditions_on = ! empty( $data['conditions_enabled'] ) ? 1 : 0;
		update_post_meta( $menu_item_db_id, self::META_PREFIX . 'conditions_enabled', $conditions_on );

		if ( isset( $data['condition_rules'] ) ) {
			$clean_rules = Sanitizer::sanitize_condition_rules( $data['condition_rules'] );
			update_post_meta( $menu_item_db_id, self::META_PREFIX . 'condition_rules', $clean_rules );
		}
	}
}
