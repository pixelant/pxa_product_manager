(function (w, $) {
	const ProductManager = w.ProductManager || {};

	// Use to show meesages on FE
	ProductManager.Messanger = (function () {
		/**
		 * Classes
		 *
		 * @type {{success: string, warning: string, error: string}}
		 * @private
		 */
		const _availableTypesClasses = {
			'success': 'alert-success',
			'warning': 'alert-warning',
			'error': 'alert-error'
		};

		/**
		 * Options
		 */
		let _delay = 3000,
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
		const _showMessage = function (message, type, delay, activeClass) {
			const popUp = $(_getMessageTemplate(message, type));
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
		const _getTypeClass = function (type) {
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
		let _getMessageTemplate = function (message, type) {
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
		const _determinateTransitionEvent = function () {
			if (_transitionEvent === null) {
				let el = document.createElement('fake'),
					transEndEventNames = {
						'WebkitTransition': 'webkitTransitionEnd',
						'MozTransition': 'transitionend',
						'transition': 'transitionend'
					};
				for (let t in transEndEventNames) {
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
		const setMessageTemplateGetter = function (customFunction) {
			_getMessageTemplate = customFunction;
		};

		/**
		 * Success message
		 * @param message
		 * @param delay
		 * @param activeClass
		 */
		const showSuccessMessage = function (message, delay, activeClass) {
			_showMessage(message, 'success', delay, activeClass);
		};

		/**
		 * Error message
		 * @param message
		 * @param delay
		 * @param activeClass
		 */
		const showErrorMessage = function (message, delay, activeClass) {
			_showMessage(message, 'error', delay, activeClass);
		};

		/**
		 * Warning message
		 * @param message
		 * @param delay
		 * @param activeClass
		 */
		const showWarningMessage = function (message, delay, activeClass) {
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