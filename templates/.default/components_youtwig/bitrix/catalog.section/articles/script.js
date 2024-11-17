$(function(){
	let ArticleSwiper = new Swiper('.swiper-container', {
        loop: true,
        // freeMode: true,
      pagination: {
        el: ".pagination",
        	type: 'progressbar',
      },
        //slidesPerView: 4,
        spaceBetween: 20, // Optional: Adds space between slides
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            767: {
                slidesPerView: 2
            },
            768: {
                slidesPerView: 3
            },
            1024: {
                slidesPerView: 4
            }
        }
    });
});