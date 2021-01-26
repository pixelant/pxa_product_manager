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

/*
var galleryThumbs = new Swiper('.gallery-thumbs', {
  spaceBetween: 10,
  slidesPerView: 5,
  freeMode: true,
  watchSlidesVisibility: true,
  watchSlidesProgress: true,
  direction:'vertical',
});

var galleryTop = new Swiper('.gallery-top', {
  spaceBetween: 10,
  navigation: {
    nextEl: '.product-swiper-button-next',
    prevEl: '.product-swiper-button-prev',
  },
  thumbs: {
    swiper: galleryThumbs,
  }
});

var productRelated = new Swiper('.product-slider-container', {
  slidesPerView: 'auto',
  spaceBetween: 30,
  scrollbar: {
    el: '.product-scroll',
    draggable: false,
    dragSize: 90,
    hide: false
  },
  navigation: {
    nextEl: '.product-button-next',
    prevEl: '.product-button-prev'
  },
  breakpoints: {
    1200: {
      spaceBetween: 35
    }
  }
})
*/
