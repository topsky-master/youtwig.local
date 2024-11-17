function bindElementSlider(){

    var galleryThumbs = galleryTop = false;
    
    if($("#slider").get(0)){

        galleryThumbs = new Swiper("#slider .gallery-thumbs", {
            centeredSlides: false,
            centeredSlidesBounds: false,
            direction: "horizontal",
            spaceBetween: 0,
            slidesPerView: 4,
            freeMode: false,
            watchSlidesVisibility: true,
            watchSlidesProgress: true,
            watchOverflow: true,
            breakpoints: {
                480: {
                    direction: "vertical",
                    slidesPerView: 4
                }
            }
        });

        galleryTop = new Swiper("#slider .swiper-container-wrapper .gallery-top", {
            direction: "horizontal",
            spaceBetween: 0,
            navigation: {
                nextEl: "#slider .swiper-button-next",
                prevEl: "#slider .swiper-button-prev"
            },
            keyboard: {
                enabled: true,
            },
            thumbs: {
                swiper: galleryThumbs
            }
        });

        galleryTop.on("slideChangeTransitionStart", function () {
            galleryThumbs.slideTo(galleryTop.activeIndex);
        });
        galleryThumbs.on("transitionStart", function () {
            galleryTop.slideTo(galleryThumbs.activeIndex);
        });
    
    }

    $('a.lightbox').on('click', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox(
            {
                alwaysShowClose: true,
                onNavigate: function(direction, itemIndex){

                    if (galleryTop && galleryThumbs) {
                        galleryTop.slideTo(itemIndex);
                        galleryThumbs.slideTo(itemIndex);
                    }

                }
            }
        );
        return false;
    });
    
    if($("#alsoBuy").get(0)){

        $('#alsoBuy .item').each(function() {
            var itemToClone = $(this);

            for (var i = 1; i < 4; i++) {
                itemToClone = itemToClone.next();

                if (!itemToClone.length) {
                    itemToClone = $(this).siblings(':first');
                }

                itemToClone.children(':first-child').clone()
                    .addClass("cloneditem cloneditem-" + (i))
                    .appendTo($(this));
            }
        });

    }

}

function bindModelCollapse(){
    if($('.models-collapse').get(0)){

        $('.models-collapse p:last-of-type').bind("click",function(){

            if($(this).parent().hasClass('models-expanded')){
                $(this).parent().removeClass('models-expanded');
            } else {
                $(this).parent().addClass('models-expanded');
            }

        });

    }

}

function bindQuickPhoneForm(){

    if($('.btn-oneclick').get(0)){
        var ajax_cart_action = '/ajax_cart/fastoder.php';
    } else {
        var ajax_cart_action = '/ajax_cart/preorder.php';
    }

    var ocProductId = $('#oqPhoneOrder').attr('data-product-id');
    ocProductId = parseInt(ocProductId);

    if(!isNaN(ocProductId)){

        phonePlaceholder = '+79_________';
        phoneMask = '+79000000000';

        $('#oqQuickPhone').attr('autocomplete',false);
        $('#oqQuickPhone').attr('placeholder', phonePlaceholder);

        var maskOptions =  {

            onKeyPress: function(cep, event, currentField, options){
                if($(currentField).get(0)
                    && $(currentField).val().indexOf('+78') === 0){
                    $(currentField).val($(currentField).val().replace('+78','+79'));
                }
            }

        };

        $('#oqQuickPhone').mask(phoneMask,maskOptions);

        $('#oqCallbackme').bind('click',function(event){

            if($('#oqCallbackme').prop('disabled') == true)
                return false;

            $('#oqCallbackme').prop('disabled',true);

            $(this).removeClass('btn-danger').addClass('btn-default').parents('.form-group').removeClass('has-error');

            if($('#oqQuickPhone').val() == ''){

                $(this).addClass('btn-danger').removeClass('btn-default').parents('.form-group').addClass('has-error');
                $('#oqQuickPhone').focus();
                $('#oqCallbackme').prop('disabled',false);


            } else {

                var ocPayerName = 'Клиент';
                var ocPayerPhone = $('#oqQuickPhone').val();
                var ocPayerEmail = 'no-reply@'+location.hostname;
                ocPayerEmail = $.trim(ocPayerEmail);

                $('#oqError .errors')[0].innerHTML = "";
                $('#oqOrder,#oqError').addClass("hidden");

                $('#oqCallbackme').attr('disabled',true);

                jQuery.ajax({
                    url: ajax_cart_action,
                    method: 'POST',
                    dataType: 'json',
                    async: true,
                    processData: true,
                    data: {
                        'PRODUCT_ID': ocProductId,
                        'PAYER_NAME': ocPayerName,
                        'PAYER_PHONE': ocPayerPhone,
                        'PAYER_EMAIL': ocPayerEmail
                    }
                }).done(function(result){

                    if(result.ERROR){
                        $('#oqError').removeClass("hidden");
                        $('#oqCallbackme').attr('disabled',false);

                        if(result.ERROR && result.ERROR.length){
                            for(var i = 0; i < result.ERROR.length; i ++){
                                $('#oqError .errors')[0].innerHTML += ''
                                    +'<div class="alert alert-danger text-left" role="alert">'
                                    +'<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true">'
                                    +'</span>'
                                    +'<span class="sr-only">'
                                    +'Error:'
                                    +'</span>'
                                    +' '
                                    +result.ERROR[i]
                                    +'</div>';
                            }

                        }

                    } else if(result.ORDER_ID){

                        $.cookie('BITRIX_SM_ispreodered' + ocProductId,1);
                        $('.btn-preorder-' + ocProductId).attr('disabled',true);

                        var resultAnswer = $.trim($('#oqResultOk').eq(0).text());

                        $('#oqOrder')[0].innerHTML = resultAnswer.replace('#',result.ORDER_ID);
                        $('#oqOrder').removeClass("hidden");

                    }

                    setTimeout(function(){
                        $('#oqCallbackme').prop('disabled',false);
                    },2000);

                });

            }

        });

    }

}

function bindGoogleDataLayer(){
    jQuery(document).on('yacounter21503785inited', function() {

        if($("#product-item").get(0)
            && $("#product-item").attr("data-hasprice") == 1){

            var prId = $("#product-item").attr("data-product-id");

            var pName = $.trim($('h1[itemprop="name"]',$("#product-item")).text());
            var pPrice = $.trim($('meta[itemprop="price"]',$("#product-item")).attr("content"));
            var pBrand = $.trim($('meta[itemprop="manufacturer"]',$("#product-item")).attr("content"));
            var pCat = $.trim($('meta[itemprop="category"]',$("#product-item")).attr("content"));
            var pCur = $.trim($('meta[itemprop="priceCurrency"]',$("#product-item")).attr("content"));
            var pProducts = [{"id": prId, "name": pName, "price": pPrice}];

            if(pCat != ""){
                pProducts[0].category = pCat;
            }

            if(pBrand != ""){
                pProducts[0].brand = pBrand;
            }

            window.dataLayer = window.dataLayer || [];
            dataLayer.push({
                "ecommerce": {
                    "currencyCode": pCur,
                    "detail": {
                        "products": pProducts
                    }
                }
            });

        }
    });
}

function bindOrgCodeReadMore(){
    if($('.original-props-area.collapsed').get(0)){

        $('.read-more').bind('click',function(){

            if($('.original-props-area').hasClass('collapsed')){
                $('.original-props-area').removeClass('collapsed');
            } else {
                $('.original-props-area').addClass('collapsed');
            };

        });

    };
}

$(function(){

    bindElementSlider();
    bindModelCollapse();
    bindQuickPhoneForm();
    bindGoogleDataLayer();
    bindOrgCodeReadMore();

    $('[data-toggle="popover"]').popover();

});

