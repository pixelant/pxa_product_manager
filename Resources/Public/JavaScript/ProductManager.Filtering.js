(function (w, $) {
	var ProductManager = w.ProductManager || {};

	// Filtering
	ProductManager.Filtering = (function () {
		/**
		 * Filtering settings
		 */
		var _settings;

		/**
		 * if script should do something on select change
		 * @type {boolean}
		 */
		var _triggerSelectBoxChange = true;

		/**
		 * Jquery objects
		 */
		var $selectBoxes,
			$resetButton;

		/**
		 * Select box instances
		 * @type object
		 */
		var _selectInstances = {};

		/**
		 * Available options and categories for filtering
		 */
		var _availableOptionsList = '',
			_availableCategoriesList = '';

		/**
		 * Last filter trigger. Need to remember it, to
		 * don't remove options of it
		 *
		 * @type {null}
		 * @private
		 */
		var _lastFilterBoxIdentifier = null;

		/**
		 * Main initialize filtering
		 * @param initializeSettings
		 */
		var init = function (initializeSettings) {
			_initVariables(initializeSettings);

			$selectBoxes.each(function () {
				_selectInstances[$(this).data('identifier')] = $(this).select2();
			});

			updateFilteringOptions();

			$selectBoxes.on('change', function (e) {
				_selectBoxChanged(e, $(this));
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
		var _initUrlStashState = function () {
			var hash = decodeURIComponent(window.location.hash);

			if (hash.length > 0 && hash.substring(0, 8) === '#filter:') {
				hash = hash.substring(8);

				try {
					var filters = JSON.parse(hash);
				} catch (e) {
					console.log(e);
				}

				if (typeof filters !== 'undefined' && filters.length) {
					// disable select box onchange
					_triggerSelectBoxChange = false;

					for (var i = 0; i < filters.length; i++) {
						if (_selectInstances.hasOwnProperty(filters[i].id)) {
							_selectInstances[filters[i].id].val(filters[i].v.split(',')).trigger('change');
						}
					}

					_triggerUpdate();
					// enable it back
					_triggerSelectBoxChange = true;
				}
			}
		};

		/**
		 * Selection was changed
		 *
		 * @param event
		 * @param $selectBox
		 * @private
		 */
		var _selectBoxChanged = function (event, $selectBox) {
			if (_triggerSelectBoxChange) {
				_lastFilterBoxIdentifier = $selectBox.data('identifier');
				_triggerUpdate();
			}
		};

		/**
		 * Reset filtering
		 * @private
		 */
		var _resetFiltering = function () {
			// while reset disable onchange
			_triggerSelectBoxChange = false;
			// remove last filter box
			_lastFilterBoxIdentifier = null;

			$selectBoxes.select2().val(null).trigger('change');

			_triggerUpdate();

			// enable onchange back
			_triggerSelectBoxChange = true;
		};

		/**
		 * Trigger filter update and build filtering data
		 * @private
		 */
		var _triggerUpdate = function () {
			// build fitering array
			var filteringData = buildFilteringData(),
				urlData = [];

			// update url hash
			for (var key in filteringData) {
				if (!filteringData.hasOwnProperty(key)) continue;
				var singleFilter = {
					id: key,
					v: filteringData[key]['value'].join(',')
				};

				urlData.push(singleFilter);
			}

			window.location.hash = urlData.length ? encodeURIComponent('filter:' + JSON.stringify(urlData)) : '';

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
		var _initVariables = function (initializeSettings) {
			_settings = initializeSettings;

			// jquery
			$selectBoxes = $(_settings.selectBoxes);
			$resetButton = $(_settings.resetButton);

			if (_settings.availableOptionsList) {
				_availableOptionsList = _settings.availableOptionsList
			}
			if (_settings.availableCategoriesList) {
				_availableCategoriesList = _settings.availableCategoriesList
			}
		};

		/**
		 * Build filtering object
		 *
		 * @return {{}}
		 * @private
		 */
		var buildFilteringData = function () {
			var filteringData = {},
				currentValue = '',
				key = '';

			$selectBoxes.each(function () {
				var $this = $(this),
					type = parseInt($this.data('filter-type')),
					uid = parseInt($this.data('attribute-uid'));
				// select box type
				if (type <= 2) {
					currentValue = $this.val();
					key = type + '-' + uid;

					if (currentValue !== null && currentValue.length > 0) {
						filteringData[key] = {
							type: type,
							attributeUid: uid,
							value: currentValue
						}
					}

				}
				// min-max box type
				if (type === 3) {
					currentValue = $this.val();
						// string, two dropdowns so add data-range to key
					key = type + '-' + uid + '-' + $this.data('range');
					if (currentValue !== null && currentValue.length > 0) {
						filteringData[key] = {
							attributeUid: uid,
							type: type,
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
		var updateFilteringOptions = function () {
			$selectBoxes.each(function () {
				var identifier = $(this).data('identifier');

				if (identifier !== _lastFilterBoxIdentifier) {
					var filterType = $(this).data('filter-type'),
						$selectFilter = $(this);

					$selectFilter.find('option').each(function () {
						var $option = $(this);

						// If it's selected then it's active by default
						if ($option.is(':selected') === false) {
							var inList;

							switch(filterType) {
								// categories and simple attributes select
								case 1:
								case 2:
									inList = ProductManager.Main.isInList(
										filterType === 1 ? _availableCategoriesList : _availableOptionsList,
										$option.attr('value')
									);
									break;
								// max and min
								case 3:
									inList = ProductManager.Main.isInList(
										_availableOptionsList,
										$option.data('option-uid')
									);
									break;
							}

							if (inList || (_availableCategoriesList + _availableOptionsList) === '') {
								$option.prop('disabled', false);
							} else {
								$option.prop('disabled', true);
							}
						}
					});

					// re-init to respect changes
					$selectFilter.select2();
				}
			});
		};

		/**
		 * Used to update available options
		 *
		 * @param newAvailableCategoriesList
		 */
		var setAvailableCategoriesList = function (newAvailableCategoriesList) {
			_availableCategoriesList = newAvailableCategoriesList;
		};

		/**
		 * Used to update available options
		 *
		 * @param newAvailableOptionsList
		 */
		var setAvailableOptionsList = function (newAvailableOptionsList) {
			_availableOptionsList = newAvailableOptionsList;
		};

		return {
			init: init,
			buildFilteringData: buildFilteringData,
			updateFilteringOptions: updateFilteringOptions,
			setAvailableCategoriesList: setAvailableCategoriesList,
			setAvailableOptionsList: setAvailableOptionsList
		}
	})();

	w.ProductManager = ProductManager;
})(window, $);