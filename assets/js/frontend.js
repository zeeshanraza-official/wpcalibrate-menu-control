/**
 * WPCalibrate Menu Control Frontend JavaScript
 *
 * Provides accessibility, keyboard handling, and non-clickable heading behavior.
 * Vanilla JavaScript (Zero dependencies).
 *
 * @package WPCalibrate\MenuControl
 */

(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		// 1. Non-clickable heading handler.
		var headingLinks = document.querySelectorAll('.wpcalibrate-non-clickable, .wpcalibrate-is-heading > a');
		headingLinks.forEach(function (link) {
			link.addEventListener('click', function (e) {
				e.preventDefault();

				// If parent item has a sub-menu, toggle it on touch/click.
				var parentItem = link.closest('.wpcalibrate-item');
				if (parentItem) {
					var subMenu = parentItem.querySelector('ul.sub-menu');
					if (subMenu) {
						var isOpen = parentItem.classList.toggle('wpcalibrate-dropdown-open');
						link.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
					}
				}
			});

			// Accessible keyboard toggle (Space or Enter).
			link.addEventListener('keydown', function (e) {
				if (e.key === ' ' || e.key === 'Enter') {
					e.preventDefault();
					link.click();
				}
			});
		});

		// 2. Keyboard accessibility: Close mega menu on Escape key.
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				var openDropdowns = document.querySelectorAll('.wpcalibrate-dropdown-open');
				openDropdowns.forEach(function (el) {
					el.classList.remove('wpcalibrate-dropdown-open');
					var toggle = el.querySelector('a');
					if (toggle) {
						toggle.setAttribute('aria-expanded', 'false');
					}
				});
			}
		});
	});
})();
