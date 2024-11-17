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
				$('[data-toggle="popover"]').popover();
                var mPage = $.trim($('#spagination li.hidden-xs').last().text());
                var cPage = $.trim($('#spagination li.active').last().text());

                var mPage = $.trim($('#spagination li.hidden-xs').last().text());
                var cPage = $.trim($('#spagination li.active').last().text());

                mPage = parseInt(mPage);
                mPage = !isNaN(mPage) ? mPage : 0;

                cPage = parseInt(cPage);
                cPage = !isNaN(cPage) ? cPage : 0;

                if(mPage && cPage && mPage > cPage) {
                    $('#lpagination').removeClass('hidden');
                }


                $('#lpagination').on('click',function (event) {

                    var mPage = $.trim($('#spagination li.hidden-xs').last().text());
                    var cPage = $.trim($('#spagination li.active').last().text());

                    mPage = parseInt(mPage);
                    mPage = !isNaN(mPage) ? mPage : 0;

                    cPage = parseInt(cPage);
                    cPage = !isNaN(cPage) ? cPage : 0;
					
                    if(mPage && cPage){

                        var pUrl =  $('#spagination li').find('a').attr('href');
						
						pUrl = pUrl.replace(/\/pages-[0-9]+\//gi,'/');
						
						if(pUrl.indexOf('?') !== -1) {
							var sUrl = pUrl.replace(/.*?(\?.*)$/,"$1");
						} else {
							var sUrl = '';
						}
						
						pUrl = pUrl.replace(/\?.*$/,'');
						
						if(mPage > cPage){

                            cPage += 1;
                            var pNextURL = pUrl + 'pages-' + cPage + '/' + sUrl;
							
                            $.get(pNextURL,function (dataHTML) {

                                dataHTML = $.parseHTML(dataHTML);

                                cdataHTML = $(dataHTML).find('#comp_smart_filter .product-item');
                                pdataHTML = $(dataHTML).find('#spagination').html();

                                $(cdataHTML).insertBefore('#lpagination');
                                $('#spagination').html(pdataHTML);

								galleryTop = new Swiper(".slider .gallery-top:not(.swiper-initialized)", {
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


                                history.pushState({}, '', pNextURL);

								try {
									bindQuantity();
								} catch (e) {
								}

                                if(!(mPage > cPage)) {
                                    $('#lpagination').addClass('hidden');
                                }

                            });
                        } else {
                            $('#lpagination').addClass('hidden');
                        }

                    }

                    return false;
                })

            });


