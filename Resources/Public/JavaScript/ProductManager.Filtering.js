(function (w, $) {
	const ProductManager = w.ProductManager || {};

	// Filtering
	ProductManager.Filtering = (function () {
		/**
		 * Filtering settings
		 */
		let _settings;

		/**
		 * if script should do something on select change
		 * @type {boolean}
		 */
		let _triggerSelectBoxChange = true;

		/**
		 * Jquery objects
		 */
		let $selectBoxes,
			$resetButton;

		/**
		 * Select box instances
		 * @type object
		 */
		let _selectInstances = {};

		/**
		 * Available options and categories for filtering
		 */
		let _filtersAvailableOptions = {};

		let _filterTypeCategories = 1,
			_filterTypeAttributeOptions = 2,
			_filterTypeAttributeMinMax = 3;

		/**
		 * Last filter trigger. Need to remember it, to
		 * don't remove options of it
		 *
		 * @type {null}
		 * @private
		 */
		let _lastFilterBox = null;

		/**
		 * Main initialize filtering
		 * @param initializeSettings
		 */
		const init = function (initializeSettings) {
			_initVariables(initializeSettings);

			$selectBoxes.each(function () {
				let select = $(this);
				_selectInstances[select.data('identifier')] = select.select2();

				// Disable search on mobile
				if ($(window).width() <= 739) {
					select.on('select2:opening select2:closing', function (event) {
						$(this).parent().find('.select2-search__field').prop('disabled', true);
					});
				}
			});

			updateFilteringOptions();

			$selectBoxes.on('change', function () {
				_selectBoxChanged($(this));
			});

			$resetButton.on('click', function (e) {
				e.preventDefault();
				_resetFiltering();
			});

			// check if filter is set in url
			_initUrlStashState();
		};

		/**
		 * If url with hash
		 * @private
		 */
		const _initUrlStashState = function () {
			let hash = ProductManager.Main.readStatusFromHash(),
				filters = hash['filters'] || null;

			if (filters !== null && filters.length) {
				// disable select box onchange
				_triggerSelectBoxChange = false;

				for (let i = 0; i < filters.length; i++) {
					if (_selectInstances.hasOwnProperty(filters[i].id)) {
						_selectInstances[filters[i].id].val(filters[i].v.split(',')).trigger('change');
					}
				}

				_triggerUpdate();
				// enable it back
				_triggerSelectBoxChange = true;
			}
		};

		/**
		 * Selection was changed
		 *
		 * @param $selectBox
		 * @private
		 */
		const _selectBoxChanged = function ($selectBox) {
			if (_triggerSelectBoxChange) {
				_lastFilterBox = $selectBox;
				_triggerUpdate();
			}
		};

		/**
		 * Reset filtering
		 * @private
		 */
		const _resetFiltering = function () {
			// while reset disable onchange
			_triggerSelectBoxChange = false;
			// remove last filter box
			_lastFilterBox = null;

			$selectBoxes.select2().val(null).trigger('change');

			_triggerUpdate();

			// enable onchange back
			_triggerSelectBoxChange = true;
		};

		/**
		 * Trigger filter update and build filtering data
		 * @private
		 */
		const _triggerUpdate = function () {
			// build fitering array
			let filteringData = buildFilteringData(),
				urlData = [];

			// update url hash
			for (let key in filteringData) {
				if (!filteringData.hasOwnProperty(key)) continue;
				let singleFilter = {
					id: key,
					v: filteringData[key]['value'].join(',')
				};

				urlData.push(singleFilter);
			}

			ProductManager.Main.writeToHash('filters', urlData);
			ProductManager.Main.trigger(
				'FILTER_UPDATE',
				{
					filteringData: filteringData
				}
			);
		};

		/**
		 * Init main variables
		 *
		 * @param initializeSettings
		 * @private
		 */
		const _initVariables = function (initializeSettings) {
			_settings = initializeSettings;

			// jquery
			$selectBoxes = $(_settings.selectBoxes);
			$resetButton = $(_settings.resetButton);

			if (_settings.filtersAvailableOptions) {
				_filtersAvailableOptions = _settings.filtersAvailableOptions
			}
		};

		/**
		 * Build filtering object
		 *
		 * @return {{}}
		 * @private
		 */
		const buildFilteringData = function () {
			let filteringData = {},
				currentValue = '',
				key = '';

			$selectBoxes.each(function () {
				let $this = $(this),
					uid = parseInt($this.data('uid')), // Filter uid
					type = parseInt($this.data('filter-type')), // Type of filter
					attributeUid = parseInt($this.data('attribute-uid')); // Attribute uid or parent category
				// select box type
				if (type <= 2) {
					currentValue = $this.val();
					key = type + '-' + attributeUid;

					if (currentValue !== null && currentValue.length > 0) {
						filteringData[key] = {
							uid: uid,
							attributeUid: attributeUid,
							value: currentValue
						}
					}

				}
				// min-max box type
				if (type === 3) {
					currentValue = $this.val();
					// string, two dropdowns so add data-range to key
					key = type + '-' + attributeUid + '-' + $this.data('range');
					if (currentValue !== null && currentValue.length > 0) {
						filteringData[key] = {
							uid: uid,
							attributeUid: attributeUid,
							value: [currentValue, $this.data('range')]
						}
					}
				}
			});

			return filteringData;
		};

		/**
		 * Update options of filter
		 * We need to show only options that has product results
		 */
		const updateFilteringOptions = function () {
			let minMaxFilter = null;

			// Go for each filter
			$selectBoxes.each(function () {
				let identifier = $(this).data('identifier'),
					filterUid = $(this).data('uid'),
					filterType = $(this).data('filter-type');

				if (_lastFilterBox !== null
					&& identifier === _lastFilterBox.data('identifier')
					&& filterType === _filterTypeAttributeMinMax
				) {
					minMaxFilter = $(this);
				} else {
					let $selectFilter = $(this);

					let availableListOfOptionsForFilter = _getAvailableListOfOptionsForFilter(filterUid, filterType);
					$selectFilter.find('option').each(function () {
						let $option = $(this);

						// If it's selected then it's active by default
						if ($option.is(':selected') === false) {
							let inList;

							switch (filterType) {
								// categories and simple attributes select
								case _filterTypeCategories:
								case _filterTypeAttributeOptions:
									inList = ProductManager.Main.isInList(
										availableListOfOptionsForFilter,
										$option.attr('value')
									);
									break;
								// max and min
								case _filterTypeAttributeMinMax:
									inList = ProductManager.Main.isInList(
										availableListOfOptionsForFilter,
										$option.data('option-uid')
									);
									break;
							}

							$option.prop('disabled', !inList);
						}
					});

					// re-init to respect changes
					$selectFilter.select2();
				}
			});

			if (minMaxFilter !== null) {
				// If last changed filter was min-max type
				// we need to disable options with bigger/lower values
				let isCurrentMinMaxFilterRangeMin = minMaxFilter.data('range') === 'min',
					identifierParts = _lastFilterBox.data('identifier').split('-'),
					secondMinMaxFilterIdentifier = identifierParts[0] + '-' + identifierParts[1] + '-' + (isCurrentMinMaxFilterRangeMin ? 'max' : 'min'),
					secondMinMaxFilter = $('[data-identifier="' + secondMinMaxFilterIdentifier + '"]');

				if (secondMinMaxFilter.length === 1) {
					let currentFilterValue = parseInt(minMaxFilter.val()),
						secondFilterValue = parseInt(secondMinMaxFilter.val());

					secondMinMaxFilter.find('option').each(function () {
						let $optionMinMax = $(this),
							value = parseInt($optionMinMax.attr('value'));

						if ((isCurrentMinMaxFilterRangeMin && value < currentFilterValue)
							|| (!isCurrentMinMaxFilterRangeMin && value > currentFilterValue)
						) {
							$optionMinMax.prop('disabled', true);
						}
					});
				}
			}
		};

		/**
		 * Available options for filter
		 *
		 * @param filterUid
		 * @param filterType
		 */
		const _getAvailableListOfOptionsForFilter = function (filterUid, filterType) {
			let options = _filtersAvailableOptions[filterType === 1 ? 'availableCategories' : 'availableAttributes'];

			if (typeof options === 'object') {
				let optionsResult = options.hasOwnProperty(filterUid)
					? options[filterUid]
					: options['all'];

				return optionsResult.join(',');
			}

			return '';
		};

		/**
		 * Used to update available options
		 *
		 * @param newFiltersAvailableOptions
		 */
		const setFiltersAvailableOptions = function (newFiltersAvailableOptions) {
			_filtersAvailableOptions = newFiltersAvailableOptions;
		};

		return {
			init: init,
			buildFilteringData: buildFilteringData,
			updateFilteringOptions: updateFilteringOptions,
			setFiltersAvailableOptions: setFiltersAvailableOptions
		}
	})();

	w.ProductManager = ProductManager;
})(window, $);