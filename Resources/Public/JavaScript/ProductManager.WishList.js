(function (w, $) {
	var ProductManager = w.ProductManager || {};

	// Lazy loading main function
	ProductManager.WishList = (function () {
		/**
		 * Main settings
		 */
		var settings;

		var ajaxLoadingInProgress = true;

		/**
		 * Dom elements
		 */
		var $buttons,
			$cart,
			$cartCounter;

		/**
		 * Main wish list function
		 *
		 * @param wishListSettings
		 */
		var init = function (wishListSettings) {
			_initVars(wishListSettings);

			initButtons($buttons);
			var currentList = ProductManager.Main.getCookie('pxa_pm_wish_list');

			ProductManager.Main.updateCartCounter($cartCounter, currentList !== false ? currentList.split(',').length : 0);
			ajaxLoadingInProgress = false;
		};

		/**
		 * Init main variables
		 *
		 * @param wishListSettings
		 * @private
		 */
		var _initVars = function (wishListSettings) {
			settings = wishListSettings;

			$buttons = $(wishListSettings.buttonIdentifier + '.' + wishListSettings.loadingClass);
			$cart = $(wishListSettings.cartIdentifier);
			$cartCounter = $(wishListSettings.cartCounter);
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

			if(parentToRemove) {
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

					if(parentToRemove.length === 1) {
						parentToRemove.fadeOut('fast', function () {
							parentToRemove.remove();
						});
					}

					ProductManager.Main.updateCartCounter($cartCounter, data.inList ? 1 : -1);
					ProductManager.Messanger.showSuccessMessage(data.message);
				} else {
					button
						.removeClass(settings.loadingClass)
						.prop('disabled', false);

					ProductManager.Messanger.showErrorMessage(data.message);
				}
				ProductManager.Main.trigger(
					data.inList ? 'PRODUCT_ADDED_TO_WISHLIST' : 'PRODUCT_REMOVED_FROM_WISHLIST',
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
			var productsWishList = ProductManager.Main.getCookie('pxa_pm_wish_list') || '';

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

				if (ProductManager.Main.isInList(productsWishList, productUid)) {
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
		 * Wish list settings
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