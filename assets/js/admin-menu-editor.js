/**
 * Admin Menu Item Editor JavaScript
 *
 * @package WPCalibrate\MenuControl
 */

/* global jQuery, wpcalibrateMenuControlData */
(function ($) {
	'use strict';

	$(function () {
		// Toggle WPCalibrate Settings Panel
		$(document).on('click', '.wpcalibrate-toggle-panel', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var $panel = $btn.closest('.wpcalibrate-menu-item-controls').find('.wpcalibrate-controls-body');
			var isExpanded = $btn.attr('aria-expanded') === 'true';

			$panel.slideToggle(200);
			$btn.attr('aria-expanded', !isExpanded);
			$btn.text(isExpanded ? 'Configure' : 'Close');
		});

		// Toggle Mega Menu Options
		$(document).on('change', '.wpcalibrate-mega-menu-toggle', function () {
			var $checkbox = $(this);
			var $options = $checkbox.closest('.wpcalibrate-mega-menu-section').find('.wpcalibrate-mega-options');
			if ($checkbox.is(':checked')) {
				$options.slideDown(150);
			} else {
				$options.slideUp(150);
			}
		});

		// Toggle Conditions Container
		$(document).on('change', '.wpcalibrate-conditions-toggle', function () {
			var $checkbox = $(this);
			var $container = $checkbox.closest('.wpcalibrate-conditions-section').find('.wpcalibrate-conditions-container');
			if ($checkbox.is(':checked')) {
				$container.slideDown(150);
			} else {
				$container.slideUp(150);
			}
		});

		// Add Rule Row
		$(document).on('click', '.wpcalibrate-add-rule', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var itemId = $btn.data('item-id');
			var $rulesList = $btn.closest('.wpcalibrate-conditions-container').find('.wpcalibrate-rules-list');
			var newIndex = $rulesList.find('.wpcalibrate-rule-row').length;

			var rowHtml = '<div class="wpcalibrate-rule-row" data-index="' + newIndex + '">' +
				'<select name="wpcalibrate_menu_item[' + itemId + '][condition_rules][rules][' + newIndex + '][type]" class="wpcalibrate-rule-type-select">' +
					'<optgroup label="User Rules">' +
						'<option value="user_status">User Status (Logged in / Logged out)</option>' +
						'<option value="user_role">User Role</option>' +
					'</optgroup>' +
					'<optgroup label="Device Rules">' +
						'<option value="device_group">Device Group (Desktop / Mobile / Tablet)</option>' +
					'</optgroup>' +
					'<optgroup label="Content & URL Rules">' +
						'<option value="page_context">Page Context (Home, Archive, Singular)</option>' +
						'<option value="post_type">Post Type</option>' +
						'<option value="url_path">URL / Path Match</option>' +
					'</optgroup>' +
					'<optgroup label="Schedule Rules">' +
						'<option value="schedule_start">Date/Time: Start After</option>' +
						'<option value="schedule_end">Date/Time: End Before</option>' +
					'</optgroup>' +
					'<optgroup label="WooCommerce Rules">' +
						'<option value="wc_context">WooCommerce Context (Shop, Cart, Checkout, Account)</option>' +
						'<option value="wc_customer">WooCommerce Customer State (Has Orders, Guest)</option>' +
					'</optgroup>' +
				'</select> ' +
				'<select name="wpcalibrate_menu_item[' + itemId + '][condition_rules][rules][' + newIndex + '][operator]" class="wpcalibrate-rule-operator-select">' +
					'<option value="is">is</option>' +
					'<option value="is_not">is not</option>' +
				'</select> ' +
				'<input type="text" name="wpcalibrate_menu_item[' + itemId + '][condition_rules][rules][' + newIndex + '][value]" class="wpcalibrate-rule-value-input regular-text" placeholder="Rule parameter / value" /> ' +
				'<button type="button" class="button wpcalibrate-remove-rule" title="Remove rule">&times;</button>' +
			'</div>';

			$rulesList.append(rowHtml);
		});

		// Remove Rule Row
		$(document).on('click', '.wpcalibrate-remove-rule', function (e) {
			e.preventDefault();
			var $row = $(this).closest('.wpcalibrate-rule-row');
			$row.fadeOut(150, function () {
				$(this).remove();
			});
		});
	});
})(jQuery);
