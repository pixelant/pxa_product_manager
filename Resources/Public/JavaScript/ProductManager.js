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

			if (ProductManager.settings.latestVisitedProductsWrapper) {
				let $wrapper = $(ProductManager.settings.latestVisitedProductsWrapper);
				this.loadLatestVisitedProductsTo($wrapper);
			}
		},

		/**
		 * Set cookie
		 *
		 * @param cName
		 * @param value
		 * @param exdays
		 * @param disableEncode
		 */
		setCookie: function (cName, value, exdays, disableEncode) {
			let exdate = new Date();
			disableEncode = disableEncode || false;

			exdate.setDate(exdate.getDate() + exdays);
			let cValue = (disableEncode ? value : encodeURIComponent(value)) + ((exdays === null) ? '' : '; expires=' + exdate.toUTCString()) + '; path=/';
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
		 * Encode to b64
		 * @param str
		 */
		utf8_to_b64: function (str) {
			return window.btoa(encodeURIComponent(str));
		},

		/**
		 * Decode from b64
		 *
		 * @param str
		 * @returns {*}
		 */
		b64_to_utf8: function (str) {
			return decodeURIComponent(window.atob(str));
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
				originalWidth = flyer.width(),
				flyerClone = flyer.clone();

			flyerClone.css({
				position: 'absolute',
				top: flyer.offset().top + 'px',
				left: flyer.offset().left + 'px',
				opacity: 1,
				'max-width': originalWidth,
				height: 'auto',
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
		 * @param $mainCartCounter
		 * @param $cartCounters
		 * @param modifier
		 */
		updateCartCounter: function ($mainCartCounter, $cartCounters, modifier) {
			modifier = modifier || 0;

			if ($mainCartCounter.length === 1) {
				let currentValue = parseInt($mainCartCounter.text().trim());
				if (isNaN(currentValue)) {
					currentValue = 0;
				}

				let newValue = currentValue + modifier;
				newValue = newValue > 0 ? newValue : 0;

				if ($cartCounters.length >= 1) {
					$cartCounters.text(newValue);
				}
			}
		},

		/**
		 * Format price
		 *
		 * @param n
		 * @param decimals
		 * @param decimal_sep
		 * @param thousands_sep
		 * @returns {string}
		 */
		numberFormat: function (n, decimals, decimal_sep, thousands_sep) {
			let c = isNaN(decimals) ? 2 : Math.abs(decimals), //if decimal is zero we must take it, it means user does not want to show any decimal
				d = decimal_sep || '.', //if no decimal separator is passed we use the dot as default decimal separator (we MUST use a decimal separator)

				/*
				according to [https://stackoverflow.com/questions/411352/how-best-to-determine-if-an-argument-is-not-sent-to-the-javascript-function]
				the fastest way to check for not defined parameter is to use typeof value === 'undefined'
				rather than doing value === undefined.
				*/
				t = (typeof thousands_sep === 'undefined') ? ' ' : thousands_sep, //if you don't want to use a thousands separator you can pass empty string as thousands_sep value

				sign = (n < 0) ? '-' : '',

				//extracting the absolute value of the integer part of the number and converting to string
				i = parseInt(n = Math.abs(n).toFixed(c)) + '',

				j = (i.length > 3) ? i.length % 3 : 0;
			return sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
		},

		/**
		 * Write to status by key
		 *
		 * @param key
		 * @param value
		 */
		writeToHash: function (key, value) {
			let status = this.readStatusFromHash();
			status[key] = value;

			window.location.hash = $.isEmptyObject(status) ? '' : encodeURIComponent('pm:' + JSON.stringify(status));
		},

		/**
		 * Read data from hash status in url
		 *
		 * @returns null|object
		 */
		readStatusFromHash: function () {
			let hash = decodeURIComponent(window.location.hash);

			if (hash.length > 0 && hash.substring(0, 4) === '#pm:') {
				hash = hash.substring(4);

				try {
					return JSON.parse(hash);
				} catch (e) {
					console.log(e);
				}
			}

			return {};
		},

		/**
		 * Animate scroll
		 *
		 * @param scroll
		 */
		scrollTo: function (scroll) {
			$('html, body').animate({
				scrollTop: scroll
			}, 200);
		},

		/**
		 * Special trim with char
		 *
		 * @param string
		 * @param charToRemove
		 * @returns {*}
		 */
		trimChar: function (string, charToRemove) {
			while (string.charAt(0) === charToRemove) {
				string = string.substring(1);
			}

			while (string.charAt(string.length - 1) === charToRemove) {
				string = string.substring(0, string.length - 1);
			}

			return string;
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
		},

		/**
		 * Load latest visited products
		 *
		 * @param $wrapper
		 */
		loadLatestVisitedProductsTo: function ($wrapper) {
			if ($wrapper.length === 0) {
				return false;
			}

			let currentProductUid = (typeof pxaproductmanager_current_product_uid !== 'undefined') ? pxaproductmanager_current_product_uid : 0;
			$.get('/?type=201703&tx_pxaproductmanager_pi1%5BexcludeProduct%5D=' + currentProductUid, function (data) {
				$($wrapper).html(data);
			});
		}
	};

	w.ProductManager = ProductManager;
})(window, $);

$(document).ready(function () {
	ProductManager.Main.init();
});