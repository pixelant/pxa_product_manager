var galleryThumbs = new Swiper('.product-gallery-thumbs', {
  spaceBetween: 10,
  slidesPerView: 4,
  freeMode: true,
  watchSlidesVisibility: true,
  watchSlidesProgress: true,
});

var galleryTop = new Swiper('.product-gallery-top', {
  spaceBetween: 10,
  navigation: {
    nextEl: '.product-gallery-button-next',
    prevEl: '.product-gallery-button-prev',
  },
  thumbs: {
    swiper: galleryThumbs
  }
});

var relatedProducts = new Swiper('.product-relations', {
  slidesPerView: 'auto',
  spaceBetween: 30,
  scrollbar: {
    el: '.product-relations-scrollbar',
    draggable: false,
    dragSize: 90,
    hide: false
  },
  navigation: {
    nextEl: '.product-relations-button-next',
    prevEl: '.product-relations-button-prev',
  }
});
