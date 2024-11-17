function checkDeviceJs(){

    if (!($("#devicejs").get(0))) {

        if($("#PAY_ONE_CLICK").get(0))
            $("#PAY_ONE_CLICK").attr('disabled',true);

        var deviceUrl = location.protocol + '//' + location.hostname + '/local/components/impel/cartpreorder/templates/.default/device.js';
        var s = document.createElement("script");
        s.type = "text/javascript";
        s.id = "devicejs";
        s.src = deviceUrl;
        $(s).appendTo($('head'));

        var dInterval = setInterval(function () {

            if (!(typeof device == 'undefined')) {
                clearInterval(dInterval);
                dInterval = null;

                if($("#PAY_ONE_CLICK").get(0))
                    $("#PAY_ONE_CLICK").attr('disabled',false);

            }

        }, 200);

    };

}

function bindPreOrderItems() {

    phonePlaceholder = '+79_________';
    phoneMask = '+79000000000';

    $('#PAYER_PHONE').attr('autocomplete', false);
    $('#PAYER_PHONE').attr('placeholder', phonePlaceholder);

    var maskOptions = {

        onKeyPress: function (cep, event, currentField, options) {
            if ($(currentField).get(0)
                && $(currentField).val().indexOf('+78') === 0) {
                $(currentField).val($(currentField).val().replace('+78', '+79'));
            }
        }

    };

    $('#PAYER_PHONE').mask(phoneMask, maskOptions);

    if ($('.btn-preorder').get(0)) {
        $('.btn-preorder').bind("click", function () {

            checkDeviceJs();

            if($("#PAY_ONE_CLICK").get(0))
                $("#PAY_ONE_CLICK").attr('disabled', false);

            $('#modalOCBuy .form-group').css('display', 'table');
            $('#oc_error .errors')[0].innerHTML = "";
            $('#oc_order')[0].innerHTML = "";
            $('#oc_error,#oc_order').addClass("hidden");
            $("#PRODUCT_ID").val($(this).attr("data-product-id"));

            resultAnswer = $.trim($('#modalOCBuy #resultpOk').eq(0).text());
            $("#modalOCBuy").find('.modal-title').find('.pre_order').removeClass('hidden').end().find('.order').addClass('hidden');
            $("#PREORDER_ACTION").val('/ajax_cart/preorder.php');
        });
    }

    if ($('.btn-oneclick').get(0)) {
        $('.btn-oneclick').bind("click", function () {

            checkDeviceJs();
            if($("#PAY_ONE_CLICK").get(0))
                $("#PAY_ONE_CLICK").attr('disabled', false);


            $('#modalOCBuy .form-group').css('display', 'table');
            $('#oc_error .errors')[0].innerHTML = "";
            $('#oc_order')[0].innerHTML = "";
            $('#oc_error,#oc_order').addClass("hidden");
            $("#PRODUCT_ID").val($(this).attr("data-product-id"));

            resultAnswer = $.trim($('#modalOCBuy #resultOk').eq(0).text());
            $("#modalOCBuy").find('.modal-title').find('.order').removeClass('hidden').end().find('.pre_order').addClass('hidden');
            $("#PREORDER_ACTION").val('/ajax_cart/fastoder.php');
        });
    }

    $("#PAY_ONE_CLICK").bind("click", function () {

        var __self = this;
        $(__self).attr('disabled', true);

        var deviceUrl = location.protocol + '//' + location.hostname + '/include/resolution.php?';
        deviceUrl += '&user_resolution=' + $(window).width() + 'x' + $(window).height();
        deviceUrl += '&deviceinfo=' + encodeURIComponent(device.type + ' ' + device.os + ' ' + device.orientation);

        jQuery.ajax({
            url: deviceUrl,
            dataType: 'json',
            async: true,
            processData: true
        }).done(function (result) {

            var ajax_cart_action = $("#PREORDER_ACTION").val();
            var ajax_location = location.href;

            $('#order_one_click_form .form-group').removeClass("has-error");
            $('#order_one_click_form .form-group').removeClass("has-success");

            var ocProductId = $("#PRODUCT_ID").val();
            var ocPayerName = $("#PAYER_NAME")[0].value;
            ocPayerName = $.trim(ocPayerName);

            var hasErrors = false;

            if (ocPayerName.length > 3) {
                $('#modalOCBuy .name-group').addClass('has-success');
            } else {
                $('#modalOCBuy .name-group').addClass('has-error');
                hasErrors = true;
            }

            var ocPayerPhone = $("#PAYER_PHONE")[0].value;
            ocPayerPhone = $.trim(ocPayerPhone);

            if (ocPayerPhone.length > 3) {
                $('#modalOCBuy .phone-group').addClass('has-success');
            } else {
                $('#modalOCBuy .phone-group').addClass('has-error');
                hasErrors = true;
            }

            var ocPayerEmail = $("#PAYER_EMAIL")[0].value;
            ocPayerEmail = $.trim(ocPayerEmail);

            if ((ocPayerEmail.length > 5
                && ocPayerEmail.indexOf('@') !== -1
                && ocPayerEmail.indexOf('.') !== -1)
            ) {
                $('#modalOCBuy .email-group').addClass('has-success');
            } else {
                $('#modalOCBuy .email-group').addClass('has-error');
                hasErrors = true;
            }

            $('#oc_error .errors')[0].innerHTML = "";
            $('#oc_order')[0].innerHTML = "";
            $('#oc_error,#oc_order').addClass("hidden");

            if (!hasErrors) {

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
                        'PAYER_EMAIL': ocPayerEmail,
                        'AJAX_LOCATION': ajax_location
                    }
                }).done(function (result) {

                    if (result.ERROR) {
                        $('#oc_error').removeClass("hidden");
                        $(__self).attr('disabled', false);

                        if (result.ERROR && result.ERROR.length) {
                            for (var i = 0; i < result.ERROR.length; i++) {
                                $('#oc_error .errors')[0].innerHTML += ''
                                    + '<div class="alert alert-danger text-left" role="alert">'
                                    + '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true">'
                                    + '</span>'
                                    + '<span class="sr-only">'
                                    + 'Error:'
                                    + '</span>'
                                    + ' '
                                    + result.ERROR[i]
                                    + '</div>';
                            }

                        }

                    } else if (result.ORDER_ID) {

                        $.cookie(
                            'BITRIX_SM_ispreodered' + ocProductId,
                            true, {
                                expires: 30,
                                path: '/',
                                domain: location.hostname,
                                secure: ((location.protocol == 'https:') ? true : false)
                            }
                        );

                        $('.btn-preorder-' + ocProductId).attr('disabled', true);
                        $('#modalOCBuy .form-group').css('display', 'none');

                        $('#oc_order')[0].innerHTML = resultAnswer.replace('#', result.ORDER_ID);
                        $('#oc_order').removeClass("hidden");

                        var dataLayerLink = location.protocol + '//' + location.hostname + '/include/datalayer.php?ORDER_ID=' + result.ORDER_ID;

                        jQuery.getJSON(dataLayerLink, function (dataLayerData) {

                            if (dataLayerData
                                && typeof dataLayerData.success != "undefined"
                                && dataLayerData.success == true
                            ) {

                                window.dataLayer = window.dataLayer || [];

                                if (ajax_cart_action == '/ajax_cart/fastoder.php') {
                                    var goal_id = 'oneclick';
                                }

                                if (ajax_cart_action == '/ajax_cart/preorder.php') {
                                    var goal_id = 'preorder';
                                }

                                var actionField = {id: result.ORDER_ID};

                                if (goal_id) {
                                    actionField.goal_id = goal_id;
                                }

                                dataLayer.push({
                                    "ecommerce": {
                                        "purchase": {
                                            "actionField": actionField,
                                            "products": dataLayerData.products
                                        }
                                    }
                                });

                            }

                        });

                    } else {
                        $(__self).attr('disabled', false);
                    }
                }).fail(function(jqXHR, textStatus){
                    $(__self).attr('disabled', false);
                });

            } else {
                $(__self).attr('disabled', false);
            }

        }).fail(function(jqXHR, textStatus){
            $(__self).attr('disabled', false);
        });

    });

}

function bindOrderItems(){
    if($('#modalCart').get(0)){

        $('#modalCart').on("hidden.bs.modal",function(event){
            $("#modalCart").css('opacity','0');
        });

        $('#modalCart').on("show.bs.modal",function(event){

            $('.alert-danger',$("#modalCart")).addClass('hidden');

            $('.cart-picture',$("#modalCart")).addClass('hidden');
            $('.h4',$("#modalCart")).addClass('hidden');
            $('.btn-primary',$("#modalCart")).addClass('hidden');
            $('#errorResult',$("#modalCart")).html("");

            var __self = event.relatedTarget;

            var __parent = $(event.relatedTarget).parents('.product-item');

            if($('.piiw img',__parent).get(0)){
                var imtThumb = $('.piiw img',__parent).attr('src');
            } else {
                var imtThumb = $(__parent).attr('data-src');
            }

            $('.cart-picture',$('#modalCart')).css({'background-image':'url('+imtThumb+')'});

            if($.trim($('.item-title',__parent).get(0))){
                $('p.h4',$('#modalCart')).html($.trim($('.item-title',__parent).text()));
            } else {
                $('p.h4',$('#modalCart')).html($.trim($(__parent).attr('data-title')));
            }

            var productId = $(__self).attr("data-product-id");
            var quantity = $(__self).parent().find('.order_quantity').val();
            quantity = (quantity != "undefined" && quantity) ? quantity : 1;

            var ajax_cart_action = location.protocol + '//' + location.hostname + '/include/addtocart.php?action=addbasket&id=' + productId + '&quantity=' + quantity;

            var productCartName = $('.product_cart_name',__parent).get(0);

            if(productCartName){
                var productName = $(productCartName).val();
                ajax_cart_action += '&product_name='+productName;
            };


            jQuery.ajax({
                url: ajax_cart_action,
                dataType: 'json',
                async: true,
                processData: true
            }).done(function (result) {

                if (result
                    && typeof result.STATUS != "undefined") {

                    if (result.STATUS == "OK") {

                        if(typeof result.FROM_PROVIDER != "undefined"){
                            location.replace('/personal/provider/');
                            return false;
                        }

                        $('.cart-picture', $("#modalCart")).removeClass('hidden');
                        $('.h4', $("#modalCart")).removeClass('hidden');
                        $('.btn-primary', $("#modalCart")).removeClass('hidden');

                        var cVal = $.trim($("#miniCart .acq").text());
                        cVal = parseInt(cVal);
                        cVal = !isNaN(cVal) ? cVal : 0;
                        ++cVal;
                        $("#miniCart .acq").html(cVal);
                        $("#modalCart").css('opacity', '1.0');

                        var dataLayerLink = location.protocol + '//' + location.hostname + '/include/datalayer.php?ID=' + productId;

                        jQuery.getJSON(dataLayerLink, function (dataLayerData) {

                            if (dataLayerData
                                && typeof dataLayerData.success != "undefined"
                                && dataLayerData.success == true
                            ) {


                                window.dataLayer = window.dataLayer || [];

                                dataLayer.push({
                                    "ecommerce": {
                                        "add": {
                                            "products": [
                                                {
                                                    "id": dataLayerData.id,
                                                    "name": dataLayerData.name,
                                                    "price": dataLayerData.price,
                                                    "brand": dataLayerData.brand,
                                                    "category": dataLayerData.category,
                                                    "quantity": quantity
                                                }
                                            ]
                                        }
                                    }
                                });

                            }

                        });

                    } else if (result.STATUS == "ERROR") {

                        $('.alert-danger', $("#modalCart")).removeClass('hidden');

                        $('.cart-picture', $("#modalCart")).addClass('hidden');
                        $('.h4', $("#modalCart")).addClass('hidden');
                        $('.btn-primary', $("#modalCart")).addClass('hidden');

                        if (typeof result.MESSAGE != "undefined"
                            && result.MESSAGE) {

                            $('#errorResult', $("#modalCart")).html(result.MESSAGE);
                            $("#modalCart").css('opacity', '1.0');

                        }


                    }

                }

            });

        });

    };

}

function bindQuantity() {
    $('.btn-buy').each(function(){
        let __self = this;
        let maxQuantity = $(this).data('max-quantity');
        //$(this).after('<input type="text" class="order_quantity line_quantity" value="1" min="1" max="'+maxQuantity+'" />');
        let quantity = $(this).parent().find('.order_quantity');
        if($(quantity).get(0)){
            $(quantity).each(function(){
                if(!$(this).attr('number')){
                    $(this).attr('number',true);
                    $(this).bootstrapNumber();
                };
            });
        };
    })
}

$(function(){

    bindPreOrderItems();
    bindOrderItems();
    bindQuantity();

});

