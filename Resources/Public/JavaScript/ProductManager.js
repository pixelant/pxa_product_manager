(function (w, $) {
	const ProductManager = w.ProductManager || {};

	// Init settings
	ProductManager.settings = ProductManager.settings || {};

	// General stuff here
	ProductManager.Main = {
		listeners: {},

		init: function () {
			for (let key in ProductManager.settings.events) {
				if (ProductManager.settings.events.hasOwnProperty(key)) {
					this.listeners[ProductManager.settings.events[key]] = [];
				}
			}

			// Main lazy loading
			if (ProductManager.LazyLoading) {
				ProductManager.LazyLoading.init(ProductManager.settings.lazyLoading || {});
			}

			// If filtering
			if (ProductManager.Filtering) {
				ProductManager.Filtering.init(ProductManager.settings.filtering || {});
			}

			// Init wish list
			if (ProductManager.WishList) {
				ProductManager.WishList.init(ProductManager.settings.wishList || {});
			}

			// Init compare list
			if (ProductManager.CompareList) {
				ProductManager.CompareList.init(ProductManager.settings.compareList || {});
			}
		},

		/**
		 * Set cookie
		 *
		 * @param cName
		 * @param value
		 * @param exdays
		 */
		setCookie: function (cName, value, exdays) {
			let exdate = new Date();
			exdate.setDate(exdate.getDate() + exdays);
			let cValue = encodeURIComponent(value) + ((exdays === null) ? '' : '; expires=' + exdate.toUTCString()) + '; path=/';
			document.cookie = cName + '=' + cValue;
		},

		/**
		 * Get cookie
		 *
		 * @param cName
		 * @return {string}|{boolean}
		 */
		getCookie: function (cName) {
			let i, x, y, ARRcookies = document.cookie.split(';');
			for (i = 0; i < ARRcookies.length; i++) {
				x = ARRcookies[i].substr(0, ARRcookies[i].indexOf('='));
				y = ARRcookies[i].substr(ARRcookies[i].indexOf('=') + 1);
				x = x.replace(/^\s+|\s+$/g, '');
				if (x === cName) {
					return decodeURIComponent(y);
				}
			}

			return false;
		},

		/**
		 * Check if needle is in haystack list
		 *
		 * @param haystack
		 * @param needle
		 * @return {boolean}
		 */
		isInList: function (haystack, needle) {
			return (',' + haystack + ',').indexOf(',' + needle + ',') !== -1;
		},

		/**
		 * Fly effect
		 *
		 * @param flyer
		 * @param flyingTo
		 */
		flyToElement: function (flyer, flyingTo) {
			const divider = 3,
				flyerClone = flyer.clone();

			flyerClone.css({
				position: 'absolute',
				top: flyer.offset().top + 'px',
				left: flyer.offset().left + 'px',
				opacity: 1,
				'z-index': 99999
			});

			$('body').append(flyerClone);
			const gotoX = flyingTo.offset().left + (flyingTo.width() / 2) - (flyer.width() / divider) / 2;
			const gotoY = flyingTo.offset().top + (flyingTo.height() / 2) - (flyer.height() / divider) / 2;

			$(flyerClone).animate({
					opacity: 0.4,
					left: gotoX,
					top: gotoY,
					width: flyer.width() / divider,
					height: flyer.height() / divider
				}, 700,
				function () {
					flyingTo.fadeOut('fast', function () {
						flyingTo.fadeIn('fast', function () {
							flyerClone.fadeOut('fast', function () {
								flyerClone.remove();
							});
						});
					});
				});
		},

		/**
		 * Update carts counter
		 *
		 * @param $cartCounter
		 * @param modifier
		 */
		updateCartCounter: function ($cartCounter, modifier) {
			modifier = modifier || 0;

			if ($cartCounter.length === 1) {
				let currentValue = parseInt($cartCounter.text().trim());
				if (isNaN(currentValue)) {
					currentValue = 0;
				}

				const newValue = currentValue + modifier;
				$cartCounter.text(newValue > 0 ? newValue : 0);
			}
		},

		/**
		 * Catch event
		 * @param event
		 * @param callback
		 */
		on: function (event, callback) {
			if (typeof callback === 'function') {
				if (this.listeners[event]) {
					this.listeners[event].push(callback);
				} else {
					console.log('On called with invalid event:', event);
				}
			} else {
				console.log('Callback is not a function');
			}
		},

		/**
		 * Trigger event
		 *
		 * @param event
		 * @param data
		 */
		trigger: function (event, data) {
			if (!this.listeners[event]) {
				console.log('Invalid event', event);
				return;
			}
			for (let i = 0; i < this.listeners[event].length; i++) {
				this.listeners[event][i](data);
			}
		},

		/**
		 * Get translations
		 *
		 * @param key
		 * @return string
		 */
		translate: function (key) {
			return TYPO3.lang[key] || '';
		}
	};

	w.ProductManager = ProductManager;
})(window, $);

$(document).ready(function () {
	ProductManager.Main.init();
});