(function (w, $) {
	const ProductManager = w.ProductManager || {};

	// Lazy loading main function
	ProductManager.LazyLoading = (function () {
		/**
		 * Scroll type loading
		 * @type {number}
		 */
		const LAZY_LOADING_SCROLL_TYPE = 1;

		/**
		 * wrapper of list
		 */
		let $wrapper;

		/**
		 * Loading spinner
		 */
		let $loaderOverlay;

		/**
		 * Item template
		 */
		let $template;

		/**
		 * Jquery elements
		 */
		let $loadMoreButton,
			$countContainer,
			$lastItem,
			$itemsContainer,
			$nothingFound;

		/**
		 *  Loading settings
		 */
		let settings;

		/**
		 * Lazy loading
		 */
		let lazyLoadingInProgress = false,
			lazyLoadingStop = false,
			offSet = 0,
			loadMoreType,
			storage = [],
			filteringData = {},
			wishListEnable = false,
			compareListEnable = false,
			firstLoadingLimit = 0,
			hideFilterOptionsNoResult = 0;

		/**
		 * Main init function
		 * require valid settings from Resources/Private/Partials/JavaScript/LazyLoadingJsConfiguration.html
		 *
		 * @param lazySettings
		 */
		const init = function (lazySettings) {
			_initvars(lazySettings);

			if ($wrapper.length) {
				if (loadMoreType === LAZY_LOADING_SCROLL_TYPE) {
					_initScrollLoading();
				} else {
					_initLoadMoreButton();
				}
			}

			// Check status hash and run loading if needed
			_checkHashStatusAndRunLoading();

			// On filter update, reset some values and save filtering data
			ProductManager.Main.on('FILTER_UPDATE', function (data) {
				offSet = 0;
				lazyLoadingStop = false;
				$wrapper.find(settings.item).remove();
				filteringData = data.filteringData;

				if (loadMoreType !== LAZY_LOADING_SCROLL_TYPE) {
					$loadMoreButton.removeClass(settings.hiddenClass);
				}

				_runAjax(true);
			});
		};

		/**
		 * Init main variables
		 *
		 * @param lazySettings
		 * @private
		 */
		const _initvars = function (lazySettings) {
			settings = lazySettings;

			// double check for limit
			let limit = parseInt(settings.limit, 10);
			settings.limit = isNaN(limit) ? 8 : limit;

			if (typeof settings.storagePid !== 'undefined' && settings.storagePid !== '') {
				storage = settings.storagePid.split(',');
			}

			// check if limit is reached
			lazyLoadingStop = parseInt(settings.lazyLoadingStop, 10) === 1;

			loadMoreType = parseInt(settings.loadMoreType, 10);

			wishListEnable = parseInt(settings.wishListEnable, 10) === 1;
			compareListEnable = parseInt(settings.compareListEnable, 10) === 1;
			hideFilterOptionsNoResult = parseInt(settings.hideFilterOptionsNoResult, 10);

			// Jquery objects
			$wrapper = $(settings.wrapper);
			$loaderOverlay = $(settings.loaderOverlay);
			$lastItem = $wrapper.find(settings.item).last();
			$template = $(settings.template);
			$loadMoreButton = $(settings.loadMoreButton);
			$itemsContainer = $(settings.itemsContainer);
			$countContainer = $(settings.countContainer);
			$nothingFound = $(settings.nothingFound);
		};

		/**
		 * Check if filter or limit are set in hash url
		 *
		 * @private
		 */
		const _checkHashStatusAndRunLoading = function () {
			let statusHash = ProductManager.Main.readStatusFromHash();

			firstLoadingLimit = statusHash['limit'] ? parseInt(statusHash['limit']) : 0;
			//if (typeof statusHash['filters'] === 'undefined') {
				// If no filter run first load, otherwise filters will trigger loading
				_runAjax(false, firstLoadingLimit);
			//}
		};

		/**
		 * Load product on click
		 *
		 * @private
		 */
		const _initLoadMoreButton = function () {
			$loadMoreButton.on('click', function (e) {
				e.preventDefault();
				$loadMoreButton.prop('disabled', true);
				_runAjax();
			})
		};

		/**
		 * Load on scroll
		 *
		 * @private
		 */
		const _initScrollLoading = function () {
			$(window).scroll(function () {
				if (!lazyLoadingInProgress && !lazyLoadingStop) {
					if ($lastItem.length > 0 && $(window).scrollTop() >= $lastItem.offset().top - $(window).height()) {
						_runAjax();
					}
				}
			});
		};

		/**
		 * Ajax request to load more items
		 *
		 * @param updateFilteringOptions // Update options only on filter changes
		 * @param overrideLimit allow to override settings limit
		 * @private
		 */
		const _runAjax = function (updateFilteringOptions, overrideLimit) {
			updateFilteringOptions = updateFilteringOptions || false;
			lazyLoadingInProgress = true;
			$loaderOverlay.removeClass(settings.hiddenClass);

			let limit = overrideLimit || settings.limit;

			let data = {
				tx_pxaproductmanager_pi1: {
					demand: {
						offSet: offSet,
						categories: (settings.demandCategories.length > 0) ? settings.demandCategories.split(',') : [],
						limit: limit,
						filters: filteringData,
						storagePid: storage,
						orderBy: settings.orderBy,
						orderDirection: settings.orderDirection
					},
					pagePid: settings.pagePid,
					hideFilterOptionsNoResult: hideFilterOptionsNoResult
				}
			};

			$.ajax({
				url: settings.ajaxUrl,
				method: 'post',
				data: data,
				dataType: 'json'
			}).done(function (data) {
				$loaderOverlay.addClass(settings.hiddenClass);
				offSet += settings.limit;
				lazyLoadingInProgress = false;

				// if button, enable it again
				$loadMoreButton.prop('disabled', false);

				if (data.lazyLoadingStop) {
					lazyLoadingStop = true;
					$loadMoreButton.addClass(settings.hiddenClass);
				}

				if (data.countResults > 0) {
					$nothingFound.addClass(settings.hiddenClass);
					$itemsContainer.append(data.html);
					$lastItem = $itemsContainer.find(settings.item).last();

					// Update wish list buttons
					if (wishListEnable) {
						// Init for new loaded buttons
						let $buttons = $itemsContainer.find(
							ProductManager.WishList.getSettings().buttonIdentifier + '.' + ProductManager.WishList.getSettings().loadingClass
						);

						ProductManager.WishList.initButtons($buttons);
					}
					// Update compare list buttons
					if (compareListEnable) {
						// Init for new loaded buttons
						let $buttonsCompareList = $itemsContainer.find(
							ProductManager.CompareList.getSettings().buttonIdentifier +	'.' + ProductManager.CompareList.getSettings().loadingClass
						);

						ProductManager.CompareList.initButtons($buttonsCompareList);
					}

					// Update filtering options
					if (hideFilterOptionsNoResult && updateFilteringOptions) {
						ProductManager.Filtering.setAvailableCategoriesList(data.availableCategoriesList);
						ProductManager.Filtering.setAvailableOptionsList(data.availableOptionsList);
						ProductManager.Filtering.updateFilteringOptions();
					}
				} else {
					$nothingFound.removeClass(settings.hiddenClass);
				}

				// update count
				$countContainer.text(data.countResults);

				ProductManager.Main.trigger(
					'LAZY_LOADING_REQUEST_COMPLETE',
					{
						data: data
					}
				);
			}).fail(function (jqXHR, textStatus) {
				console.log('Request failed: ' + textStatus);
			});
		};

		return {
			init: init
		}
	})();

	w.ProductManager = ProductManager;
})(window, $);