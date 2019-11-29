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
			$mainCart,
			$mainCartCounter,
			$cartCounters,
			$orderItemsAmount,
			$orderItemsPrices,
			$orderItemsTaxes,
			$totalPrice,
			$totalTax;

		let wishlist = [];

		/**
		 * Main wish list function
		 *
		 * @param wishListSettings
		 */
		const init = function (wishListSettings) {
			_initVars(wishListSettings);

			initButtons($buttons);
			let currentList = ProductManager.Main.getCookie('pxa_pm_wish_list');

			ProductManager.Main.updateCartCounter(
				$mainCartCounter,
				$cartCounters,
				currentList !== false ? currentList.split(',').length : 0
			);
			ajaxLoadingInProgress = false;

			if ($(settings.wishListContainer).length > 0) {
				_updatePriceAndTax();
			}
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
			$mainCart = $(wishListSettings.mainCartIdentifier);
			$mainCartCounter = $mainCart.find(wishListSettings.cartCounterIdentifier);
			$cartCounters = $(wishListSettings.cartsIdentifier).find(wishListSettings.cartCounterIdentifier);
			$orderItemsAmount = $(wishListSettings.orderItemAmountClass);
			$orderItemsPrices = $(wishListSettings.orderItemPriceClass);
			$orderItemsTaxes = $(wishListSettings.orderItemTaxClass);
			$totalPrice = $(wishListSettings.totalPriceClass);
			$totalTax = $(wishListSettings.totalTaxClass);
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
						.toggleClass(settings.notInListClass)
						.removeClass(settings.loadingClass)
						.prop('disabled', false)
						.find(settings.wishListButtonSingleView).text(data.inList ? button.data('remove-from-list-text') : button.data('add-to-list-text'))
						.attr('title', data.inList ? button.data('remove-from-list-text') : button.data('add-to-list-text'));

					if ($mainCart.length === 1 && data.inList) {
						let itemImg = $('[data-fly-image="' + productUid + '"]');

						if (itemImg.length === 1 && settings.enableFlyToCartAnimation) {
							ProductManager.Main.flyToElement(itemImg, $mainCart);
						}
					}

					if (parentToRemove.length === 1) {
						parentToRemove.fadeOut('fast', function () {
							parentToRemove.remove();

							// Update items after remove
							$orderItemsAmount = $(settings.orderItemAmountClass);

							// Update order changes
							_updatePriceAndTax();
							_saveCurrentStateOfAmountOfProducts();
						});
					}

					ProductManager.Main.updateCartCounter($mainCartCounter, $cartCounters, data.inList ? 1 : -1);
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

		const _updatePriceAndTax = function () {
			const uri = $(settings.wishListContainer).data('total-order-prices-ajax-uri');

			if (uri.length <= 0) {
				ProductManager.Messanger.showErrorMessage('Request failed: ' + 'Invalid url');
			}

			$.ajax({
				url: uri,
				dataType: 'json'
			}).done(function (data) {
				$totalPrice.text(data.totalPrice);
				$totalTax.text(data.totalTaxPrice);
			}).fail(function (jqXHR, textStatus) {
				ProductManager.Messanger.showErrorMessage('Request failed: ' + textStatus);
			}).always(function () {
				ajaxLoadingInProgress = false;
			});
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

				_updatePriceAndTax();
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

			if ($orderItemsAmount.length <= 0) {
				return false;
			}

			$orderItemsAmount.each(function () {
				const $this = $(this);
				let productUid = parseInt($this.data('product-uid'));

				if (productUid > 0) {
					currentState[productUid] = parseInt($this.val());
				}
			});

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

			_getWhilist(function (wishlistProducts) {
				$buttons.each(function () {
					let button = $(this),
						productUid = parseInt(button.data('product-uid')),
						text = '',
						className = '';

					if (ProductManager.Main.isInList(wishlistProducts, productUid)) {
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
						.addClass(className)
						.find(settings.wishListButtonSingleView).text(text);
				});
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

		const _getWhilist = function (callback) {
			if (wishlist.length > 0) {
				callback(wishlist);
				return true;
			}

			_loadWhishlistState(callback);
		};

		const _loadWhishlistState = function (callback) {
			const uri = $(ProductManager.settings.productManagerMainWrapper).data('load-whishlist-ajax-uri');

			if (!uri) {
				ProductManager.Messanger.showErrorMessage('Request failed: ' + 'Invalid url');
				return false;
			}

			$.ajax({
				url: uri,
				dataType: 'json'
			}).done(function (data) {
				if (data.wishList === undefined) {
					return false;
				}

				wishlist = Array.from(data.wishList).map(function (product) {
					return product.uid;
				});

				callback(wishlist);
			}).fail(function (jqXHR, textStatus) {
				ProductManager.Messanger.showErrorMessage('Request failed: ' + textStatus);
			}).always(function () {
				ajaxLoadingInProgress = false;
			});
		};

		return {
			init: init,
			initButtons: initButtons,
			getSettings: getSettings
		}
	})();

	w.ProductManager = ProductManager;
})(window, $);
