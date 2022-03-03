var galleryThumbs = new Swiper('.product-gallery-thumbs', {
  spaceBetween: 10,
  slidesPerView: 2,
  freeMode: true,
  watchSlidesVisibility: true,
  watchSlidesProgress: true,
  breakpoints: {
    // when window width is >= 480px
    480: {
      slidesPerView: 3
    },
    // when window width is >= 600px
    600: {
      slidesPerView: 4
    },
    // when window width is >= 768px
    768: {
      slidesPerView: 2
    },
    // when window width is >= 992px
    992: {
      slidesPerView: 4
    },
    // when window width is >= 992px
    1200: {
      slidesPerView: 5
    }
  }
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
  slidesPerView: 1,
  spaceBetween: 30,
  watchOverflow: true,
  watchSlidesVisibility: true,
  preloadImages: false,
  lazy: true,
  scrollbar: {
    el: '.product-relations-scroll',
    draggable: false,
    dragSize: 90,
    hide: false
  },
  navigation: {
    nextEl: '.product-relations-button-next',
    prevEl: '.product-relations-button-prev',
  },
  breakpoints: {
    // when window width is >= 600px
    600: {
      slidesPerView: 1
    },
    // when window width is >= 768px
    768: {
      slidesPerView: 2
    },
    // when window width is >= 992px
    992: {
      slidesPerView: 4
    },
    // when window width is >= 1200px
    1200: {
      slidesPerView: 4
    }
  }
});
