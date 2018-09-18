(function (w) {
	const ProductManager = w.ProductManager || {};

	// Init settings
	ProductManager.settings = ProductManager.settings || {};

	// Wish list
	ProductManager.settings.wishList = {
		buttonIdentifier: '[data-wish-list-button="1"]',
		itemClass: '.item',
		orderItemAmountClass: '.order-product-amount',
		orderItemPriceClass: '.item-order-price',
		orderItemTaxClass: '.item-order-tax',
		totalPriceClass: '.pxa-pm-order-total-price',
		totalTaxClass: '.pxa-pm-order-total-tax',
		cartsIdentifier: '.pxa-pm-wish-list-cart',
		mainCartIdentifier: '.pxa-pm-wish-list-cart.main-cart:first',
		cartCounterIdentifier: '.pxa-pm-wish-list-cart__counter',
		inListClass: 'active-icon',
		notInListClass: 'inactive-icon',
		initializationClass: 'ongoing-initialization',
		loadingClass: 'in-progress'
	};

	if (ProductManager.settings.wishlistTSSettings) {
		ProductManager.settings.wishList = Object.assign(
			{},
			ProductManager.settings.wishList,
			ProductManager.settings.wishlistTSSettings
		);
		delete ProductManager.settings.wishlistTSSettings;
	}

	// Wish list
	ProductManager.settings.compareList = {
		buttonIdentifier: '[data-compare-list-button="1"]',
		itemClass: '.item',
		cartsIdentifier: '.pxa-pm-compare-list-cart',
		mainCartIdentifier: '.pxa-pm-compare-list-cart.main-cart:first',
		cartCounterIdentifier: '.pxa-pm-compare-list-cart__counter',
		inListClass: 'active-icon',
		notInListClass: 'inactive-icon',
		loadingClass: 'in-progress',
		initializationClass: 'ongoing-initialization',
		listUrl: '/?type=201702&tx_pxaproductmanager_pi1%5Baction%5D=loadCompareList'
	};

	if (ProductManager.settings.compareListTSSettings) {
		ProductManager.settings.compareList = Object.assign(
			{},
			ProductManager.settings.compareList,
			ProductManager.settings.compareListTSSettings
		);
		delete ProductManager.settings.compareListTSSettings;
	}
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