$(function(){
	galleryTop = new Swiper(".slider .gallery-top", {
					direction: "horizontal",
					spaceBetween: 0,
					pagination: {
						el: '.swiper-pagination',
						type: 'progressbar'
					},
					keyboard: {
						enabled: true
					} 
				});

})