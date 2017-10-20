(function (w, $) {
	var ProductManager = w.ProductManager || {};

	// Compare list
	ProductManager.CompareList = (function () {
		/**
		 * Main settings
		 */
		var settings;

		/**
		 * Other settings
		 */
		var ajaxLoadingInProgress = true,
			productsList = [];

		/**
		 * Dom elements
		 */
		var $buttons,
			$cart,
			$cartCounter;

		/**
		 * Main wish list function
		 *
		 * @param settings
		 */
		var init = function (settings) {
			_initVars(settings);

			_loadList();
		};

		/**
		 * Init main variables
		 *
		 * @param listSettings
		 * @private
		 */
		var _initVars = function (listSettings) {
			settings = listSettings;

			$buttons = $(listSettings.buttonIdentifier + '.' + listSettings.loadingClass);
			$cart = $(listSettings.cartIdentifier);
			$cartCounter = $(listSettings.cartCounter);
		};

		/**
		 * Load list of compre from server
		 *
		 * @private
		 */
		var _loadList = function () {
			ajaxLoadingInProgress = true;

			$.ajax({
				url: settings.listUrl,
				dataType: 'json'
			}).done(function (data) {
				for (var i in data.compareList) {
					if (data.compareList.hasOwnProperty(i)) {
						productsList.push(data.compareList[i]);
					}
				}

				initButtons($buttons);
				ProductManager.Main.updateCartCounter($cartCounter, productsList.length);
			}).fail(function (jqXHR, textStatus) {
				console.log('Request failed: ' + textStatus);
			}).always(function () {
				ajaxLoadingInProgress = false;
			});
		};

		/**
		 * Perform request
		 *
		 * @param button
		 * @private
		 */
		var _toggleWishListAjaxAction = function (button) {
			ajaxLoadingInProgress = true;

			var parentToRemove = button.data('delete-parent-on-remove'),
				productUid = parseInt(button.data('product-uid')),
				uri = button.data('ajax-uri');

			if (parentToRemove) {
				parentToRemove = button.parents(parentToRemove);
			}

			button
				.addClass(settings.loadingClass)
				.prop('disabled', true);

			$.ajax({
				url: uri,
				dataType: 'json'
			}).done(function (data) {
				if (data.success) {
					button
						.toggleClass(settings.inListClass)
						.removeClass(settings.loadingClass)
						.prop('disabled', false)
						.attr('title', data.inList ? button.data('remove-from-list-text') : button.data('add-to-list-text'));

					if ($cart.length === 1 && data.inList) {
						var itemImg = button.parents(settings.itemClass).find('img').eq(0);

						ProductManager.Main.flyToElement($(itemImg), $cart);
					}

					if (parentToRemove.length === 1) {
						parentToRemove.fadeOut('fast', function () {
							parentToRemove.remove();
						});
					}

					ProductManager.Main.updateCartCounter($cartCounter, data.inList ? 1 : -1);

					ProductManager.Messanger.showSuccessMessage(data.message);
				}
				ProductManager.Main.trigger(
					data.inList ? 'PRODUCT_ADDED_TO_COMPARELIST' : 'PRODUCT_REMOVED_FROM_COMPARELIST',
					{
						data: {
							response: data,
							button: button,
							productUid: productUid
						}
					}
				);
			}).fail(function (jqXHR, textStatus) {
				ProductManager.Messanger.showErrorMessage('Request failed: ' + textStatus);
				console.log('Request failed: ' + textStatus);
			}).always(function () {
				ajaxLoadingInProgress = false;
			});
		};

		/**
		 * Init buttons state
		 *
		 * @param $buttons
		 * @public
		 */
		var initButtons = function ($buttons) {
			$buttons.on('click', function (e) {
				e.preventDefault();

				if (!ajaxLoadingInProgress) {
					_toggleWishListAjaxAction($(this));
				}
			});

			$buttons.each(function () {
				var button = $(this),
					productUid = parseInt(button.data('product-uid')),
					text = '',
					className = '';

				if (ProductManager.Main.isInList(productsList.join(), productUid)) {
					text = button.data('remove-from-list-text');
					className = settings.inListClass;
				} else {
					text = button.data('add-to-list-text');
					className = settings.notInListClass;
				}

				button
					.attr('title', text)
					.removeClass(settings.loadingClass)
					.removeClass(settings.initializationClass)
					.addClass(className);
			});
		};

		/**
		 * Compare list settings
		 *
		 * @return string
		 */
		var getSettings = function () {
			return settings;
		};

		return {
			init: init,
			initButtons: initButtons,
			getSettings: getSettings
		}
	})();

	w.ProductManager = ProductManager;
})(window, $);