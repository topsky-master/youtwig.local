$(function(){

    kitArea = $('.kit');

    if((kitArea.attr("data-count") > 1)
        && !kitArea.hasClass('slick-slider')){

        // kitArea.on('init',function(){
        //     $('.kit-area').append('<button class="slick-prev slick-arrow" aria-label="Previous" type="button">Previous</button>');
        //     $('.kit-area').append('<button class="slick-next slick-arrow" aria-label="Next" type="button">Next</button>');
        // });

        kitArea.slick({
            infinite: false,
            arrows: false,
            speed: 300,
            slidesToShow: 4,
            slidesToScroll: 1,
            dots: false,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1
                    }
                }

            ]
        });

        $('.slick-next',kitArea.parent()).on('click', function(){

            var sCount = parseInt($(kitArea).attr('data-number'));
            var sMax = parseInt($(kitArea).attr('data-count'));

            if(sMax > sCount){

                ++sCount;

                var sUri = location.protocol + '//' + location.hostname + '/include/sliderajax.php?PageSpeed=off';
                var sData = 'PAGEN_1=' + sCount + '&PageSpeed=off';
                if($(kitArea).attr('data-value')){
                    sData += '&product_id='+$(kitArea).attr('data-value');
                }

                kitArea.parent().addClass('loading');
                $('.slick-next',kitArea.parent()).attr('disabled',true);

                $.ajax({
                    type : 'POST',
                    data: sData,
                    url : sUri,
                    async: true,
                    dataType: 'text'
                }).done(function(data){

                    try{
                        $(kitArea).slick('slickAdd',data);
                        $(kitArea).attr('data-number',sCount);
                    } catch(e){

                    }

                    if(typeof snInt != "undefined"){
                        clearTimeout(snInt);
                        snInt = null;
                    }

                    snInt = setTimeout(function(){
                        $(kitArea).slick("slickNext");
                    },500);

                    $('.slick-next',kitArea.parent()).attr('disabled',false);
                    kitArea.parent().removeClass('loading');

                });

            } else {

                $(kitArea).slick("slickNext");

            }
        });

        $('.slick-prev',kitArea.parent()).on('click', function(){

            $(kitArea).slick("slickPrev");

        });

    };

});