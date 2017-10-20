define(['jquery', 'TYPO3/CMS/Recordlist/LinkBrowser'], function ($, LinkBrowser) {

	'use strict';

	/**
	 *
	 * @type {{}}
	 * @exports TYPO3/CMS/Recordlist/LinkHandler
	 */
	var LinkHandler = {};

	/**
	 *
	 * @param {Event} event
	 */
	LinkHandler.linkCurrent = function (event) {
		event.preventDefault();

		var value = $(this).attr('href');

		// LinkBrowser.setAdditionalLinkAttribute('data-product-manager', '1');

		LinkBrowser.finalizeFunction(value);
	};

	$(function () {
		$('.t3js-recordLink').on('click', LinkHandler.linkCurrent);
		$('.t3js-pageLink').on('click', function (e) {
			e.preventDefault();

			$(this)
				.parents('.list-tree-group')
				.find('.list-tree-show')
				.trigger('click');
		})
	});

	return LinkHandler;
});
