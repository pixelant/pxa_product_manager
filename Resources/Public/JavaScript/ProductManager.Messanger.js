(function (w, $) {
	var ProductManager = w.ProductManager || {};

	// Use to show meesages on FE
	ProductManager.Messanger = (function () {
		/**
		 * Classes
		 *
		 * @type {{success: string, warning: string, error: string}}
		 * @private
		 */
		var _availableTypesClasses = {
			'success': 'alert-success',
			'warning': 'alert-warning',
			'error': 'alert-error'
		};

		/**
		 * Options
		 */
		var _delay = 3000,
			_transitionEvent = null,
			_activeClass = 'pm-popup-active';


		/**
		 * Create html for message
		 *
		 * @param message
		 * @param delay
		 * @param activeClass
		 * @param type
		 * @private
		 */
		var _showMessage = function (message, type, delay, activeClass) {
			var popUp = $(_getMessageTemplate(message, type));
			delay = delay || _delay;
			activeClass = activeClass || _activeClass;

			$('body').append(popUp);

			setTimeout(function () {
				popUp.addClass(activeClass);

				setTimeout(function () {
					popUp
						.removeClass(activeClass)
						.one(_determinateTransitionEvent(), function () {
							$(this).remove();
						});
				}, delay);
			}, 100);
		};

		/**
		 * Get class for message
		 *
		 * @param type
		 * @return string
		 * @private
		 */
		var _getTypeClass = function (type) {
			return _availableTypesClasses[type] || '';
		};

		/**
		 * Return template for pop-up
		 *
		 * @param message
		 * @param type
		 * @return {string}
		 * @private
		 */
		var _getMessageTemplate = function (message, type) {
			return '<div id="pxa-pm-popup-manager-container"><div id="pxa-pm-popup-manager" class="pm-alert ' + _getTypeClass(type) + '">' +
				'<div class="media">' +
				'<div class="media-body"><p class="alert-message">' + message + '</p></div>' +
				'</div>' +
				'</div></div>';
		};

		/**
		 * Check event name
		 *
		 * @private
		 */
		var _determinateTransitionEvent = function () {
			if (_transitionEvent === null) {
				var el = document.createElement('fake'),
					transEndEventNames = {
						'WebkitTransition': 'webkitTransitionEnd',
						'MozTransition': 'transitionend',
						'transition': 'transitionend'
					};
				for (var t in transEndEventNames) {
					if (el.style[t] !== undefined) {
						_transitionEvent = transEndEventNames[t];
					}
				}
			}

			return _transitionEvent;
		};

		/**
		 * Set custom template generator
		 *
		 * @param customFunction
		 */
		var setMessageTemplateGetter = function (customFunction) {
			_getMessageTemplate = customFunction;
		};

		/**
		 * Success message
		 * @param message
		 * @param delay
		 * @param activeClass
		 */
		var showSuccessMessage = function (message, delay, activeClass) {
			_showMessage(message, 'success', delay, activeClass);
		};

		/**
		 * Error message
		 * @param message
		 * @param delay
		 * @param activeClass
		 */
		var showErrorMessage = function (message, delay, activeClass) {
			_showMessage(message, 'error', delay, activeClass);
		};

		/**
		 * Warning message
		 * @param message
		 * @param delay
		 * @param activeClass
		 */
		var showWarningMessage = function (message, delay, activeClass) {
			_showMessage(message, 'warning', delay, activeClass);
		};

		/**
		 * Public funtion
		 */
		return {
			showWarningMessage: showWarningMessage,
			showErrorMessage: showErrorMessage,
			showSuccessMessage: showSuccessMessage,
			setMessageTemplateGetter: setMessageTemplateGetter
		}
	})();

	w.ProductManager = ProductManager;
})(window, $);