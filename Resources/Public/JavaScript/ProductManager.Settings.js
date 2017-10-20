(function (w) {
	var ProductManager = w.ProductManager || {};

	// Init settings
	ProductManager.settings = ProductManager.settings || {};

	// Wish list
	ProductManager.settings.wishList = {
		buttonIdentifier: '[data-wish-list-button="1"]',
		itemClass: '.item',
		cartIdentifier: '#pxa-pm-wish-list-cart',
		cartCounter: '#pxa-pm-wish-list-cart__counter',
		inListClass: 'selected',
		notInListClass: 'inactive-icon',
		initializationClass: 'ongoing-initialization',
		loadingClass: 'in-progress'
	};

	// Wish list
	ProductManager.settings.compareList = {
		buttonIdentifier: '[data-compare-list-button="1"]',
		itemClass: '.item',
		cartIdentifier: '#pxa-pm-compare-list-cart',
		cartCounter: '#pxa-pm-compare-list-cart__counter',
		inListClass: 'selected',
		notInListClass: 'inactive-icon',
		loadingClass: 'in-progress',
		initializationClass: 'ongoing-initialization',
		listUrl: '/?type=201702&tx_pxaproductmanager_pi1%5Baction%5D=loadCompareList'
	};

	// Events
	ProductManager.settings.events = {
		FILTER_UPDATE: 'FILTER_UPDATE',
		LAZY_LOADING_REQUEST_COMPLETE: 'LAZY_LOADING_REQUEST_COMPLETE',

		PRODUCT_ADDED_TO_WISHLIST: 'PRODUCT_ADDED_TO_WISHLIST',
		PRODUCT_REMOVED_FROM_WISHLIST: 'PRODUCT_REMOVED_FROM_WISHLIST',

		PRODUCT_REMOVED_FROM_COMPARELIST: 'PRODUCT_REMOVED_FROM_COMPARELIST',
		PRODUCT_ADDED_TO_COMPARELIST: 'PRODUCT_ADDED_TO_COMPARELIST'
	};

	w.ProductManager = ProductManager;
})(window);