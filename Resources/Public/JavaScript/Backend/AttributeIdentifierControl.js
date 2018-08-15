define(['jquery'], function ($) {
	'use strict';

	/**
	 * Input name element
	 *
	 * @type {jQuery}
	 */
	const $inputName = $('input[data-formengine-input-name$="[name]"]');

	/**
	 * Input identifier element
	 *
	 * @type {jQuery}
	 */
	const $inputIdentifier = $('input[data-formengine-input-name$="[identifier]"]');

	/**
	 * @exports TYPO3/CMS/PxaProductManager/Backend/AttributeIdentifierControl
	 */
	let AttributeIdentifierControl = {};

	/**
	 * Sync identifier with name field
	 */
	AttributeIdentifierControl.syncIdentifier = function () {
		let value = $inputName.val();

		$.ajax({
			type: 'POST',
			url: TYPO3.settings.ajaxUrls['pxa-pm-attribute-identifier-convert'],
			data: {
				'value': value
			}
		}).done(function (response) {
			if (response.success) {
				$inputIdentifier
					.val(response.output)
					.trigger('change');
			} else {
				top.TYPO3.Notification.error('Error while syncing!');
			}
		});
	};

	/**
	 * Initializes events
	 */
	AttributeIdentifierControl.initializeEvents = function () {
		$('.attributeIdentifier').on('click', function (evt) {
			evt.preventDefault();
			AttributeIdentifierControl.syncIdentifier();
		});
	};

	$(AttributeIdentifierControl.initializeEvents);

	return AttributeIdentifierControl;
});
