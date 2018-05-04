(function (w, $) {
	const ProductManager = w.ProductManager || {};

	// Lazy loading main function
	ProductManager.WishList = (function () {
		/**
		 * Main settings
		 */
		let settings;

		let ajaxLoadingInProgress = true;

		/**
		 * Name of cookie
		 * @type {string}
		 */
		const ORDER_STATE_COOKIE_NAME = 'pxa_pm_order_state';

		/**
		 * Save order information for number of days
		 * @type {number}
		 */
		const EXPIRE_ORDER_COOKIE_DAYS = 1;

		/**
		 * Dom elements
		 */
		let $buttons,
			$cart,
			$cartCounter,
			$orderItemsAmount,
			$orderItemsPrices,
			$totalPrice;

		/**
		 * Main wish list function
		 *
		 * @param wishListSettings
		 */
		const init = function (wishListSettings) {
			_initVars(wishListSettings);

			initButtons($buttons);
			let currentList = ProductManager.Main.getCookie('pxa_pm_wish_list');

			ProductManager.Main.updateCartCounter($cartCounter, currentList !== false ? currentList.split(',').length : 0);
			ajaxLoadingInProgress = false;

			_updateTotalPrice();
			_saveCurrentStateOfAmountOfProducts();
			_trackOrderAmountChanges();
		};

		/**
		 * Init main variables
		 *
		 * @param wishListSettings
		 * @private
		 */
		const _initVars = function (wishListSettings) {
			settings = wishListSettings;

			$buttons = $(wishListSettings.buttonIdentifier + '.' + wishListSettings.loadingClass);
			$cart = $(wishListSettings.cartIdentifier);
			$cartCounter = $(wishListSettings.cartCounter);
			$orderItemsAmount = $(wishListSettings.orderItemAmountClass);
			$orderItemsPrices = $(wishListSettings.orderItemPriceClass);
			$totalPrice = $(wishListSettings.totalPriceClass);
		};

		/**
		 * Perform request
		 *
		 * @param button
		 * @private
		 */
		const _toggleWishListAjaxAction = function (button) {
			ajaxLoadingInProgress = true;

			let parentToRemove = button.data('delete-parent-on-remove'),
				productUid = parseInt(button.data('product-uid')),
				uri = button.data('ajax-uri');

			if (parentToRemove) {
				parentToRemove = button.closest(parentToRemove);
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
						let itemImg = $('[data-fly-image="' + productUid + '"]');

						if (itemImg.length === 1) {
							ProductManager.Main.flyToElement(itemImg, $cart);
						}
					}

					if (parentToRemove.length === 1) {
						parentToRemove.fadeOut('fast', function () {
							parentToRemove.remove();

							// Update items after remove
							$orderItemsAmount = $(settings.orderItemAmountClass);

							// Update order changes
							_updateTotalPrice();
							_saveCurrentStateOfAmountOfProducts();
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
		 * Update total price if pricing enabled
		 *
		 * @returns {boolean}
		 * @private
		 */
		const _updateTotalPrice = function () {
			if ($totalPrice.length === 0) {
				return false;
			}

			let sum = 0,
				currencyFormat = $totalPrice.first().data('currency-format') || '',
				numberFormat = $totalPrice.first().data('nubmer-format') || '',
				format = ProductManager.Main.trimChar(numberFormat, '|').split('|'),

				decimals = parseInt(format[0]) || 2,
				decimalSep = format[1] || '.',
				thousandsSep = format[2] || ',';

			$orderItemsPrices.each(function () {
				const $this = $(this);

				let productUid = parseInt($this.data('product-uid'));
				if (productUid > 0) {
					let $amountItem = $(_convertClassToIdWithProductId(settings.orderItemAmountClass, productUid));
					if ($amountItem.length === 1) {
						let amount = parseInt($amountItem.val());
						sum += amount * parseFloat($this.data('price'));
					}
				}
			});

			$totalPrice.text(
				sprintf(
					currencyFormat,
					ProductManager.Main.numberFormat(sum, decimals, decimalSep, thousandsSep)
				)
			);
		};

		/**
		 * Check if amount was changed
		 *
		 * @returns {boolean}
		 * @private
		 */
		const _trackOrderAmountChanges = function () {
			if ($orderItemsAmount.length === 0) {
				return false;
			}

			$orderItemsAmount.on('change', function () {
				const $this = $(this);
				let value = parseInt($this.val());

				if (value <= 0) {
					$this.val(1);
				}

				_updateTotalPrice();
				_saveCurrentStateOfAmountOfProducts();
			});
		};

		/**
		 * Save state of order
		 *
		 * @returns {boolean}
		 * @private
		 */
		const _saveCurrentStateOfAmountOfProducts = function () {
			let currentState = {};

			if ($orderItemsAmount.length > 0) {
				$orderItemsAmount.each(function () {
					const $this = $(this);
					let productUid = parseInt($this.data('product-uid'));

					if (productUid > 0) {
						currentState[productUid] = parseInt($this.val());
					}
				});
			}

			ProductManager.Main.setCookie(
				ORDER_STATE_COOKIE_NAME,
				ProductManager.Main.utf8_to_b64(JSON.stringify(currentState)),
				EXPIRE_ORDER_COOKIE_DAYS,
				true // disable encoding, because it was already done
			)
		};

		/**
		 * Make ID selector from class + product uid
		 *
		 * @param className
		 * @param productUid
		 * @returns {*}
		 * @private
		 */
		const _convertClassToIdWithProductId = function (className, productUid) {
			return className.replace('.', '#') + '-' + productUid;
		};

		/**
		 * Init buttons state
		 *
		 * @param $buttons
		 * @public
		 */
		const initButtons = function ($buttons) {
			const productsWishList = ProductManager.Main.getCookie('pxa_pm_wish_list') || '';

			$buttons.on('click', function (e) {
				e.preventDefault();

				if (!ajaxLoadingInProgress) {
					_toggleWishListAjaxAction($(this));
				}
			});

			$buttons.each(function () {
				let button = $(this),
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
		const getSettings = function () {
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