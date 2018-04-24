(function ($) {
	$(document).ready(function () {
		const productWrapper = $('.single-product-wrapper');

		if (productWrapper.length > 0) { // only run if .single-product-wrapper is found
			const showButtons = !!productWrapper.data('gallery-mode');

			$('.single-product-gallery__items').tosrus({
				infinite: true,
				caption: {
					add: true
				},
				slides: {
					visible: 1
				},
				pagination: {
					add: true,
					type: 'thumbnails',
					target: '.single-product-gallery__pagination'
				},
				buttons: showButtons
			});

			$('.single-product-gallery__items a').not('.tos-prev, .tos-next').tosrus({
				infinite: true,
				caption: {
					add: true
				},
				pagination: {
					add: true
				},
				keys: {
					prev: true,
					next: true,
					close: true
				}
			});

			$('.single-product-gallery__assets').tosrus({
				infinite: true,
				caption: {
					add: false
				},
				slides: {
					visible: 1
				},
				pagination: {
					add: true,
					type: 'bullets',
					target: '.single-product-gallery-assets__pagination'
				},
				buttons: true
			});

		}

	});
})(jQuery);