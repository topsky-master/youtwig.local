function ajaxOrderFormSend(event,oForm){

    event.preventDefault();

    if($('.iblock_submit_top',oForm).prop('disabled') == true)
        return false;

    $('.iblock_submit_top',oForm).attr('disabled',true);
    $('.iblock_submit_top',oForm).prop('disabled',true);

    var formData                                                                        = new FormData(oForm);
    formData.append('AJAX_ORDER_FORM', 'Y');
    formData.append('iblock_submit', '1');

    var pAction                                                                         = oForm.action;

    $.ajax({
        method: "POST",
        url: pAction,
        data:  formData,
        processData: false,
        contentType: false,
        mimeType: "multipart/form-data",
        success: function( data, textStatus, jqXHR ) {
            data = $.parseHTML(data, document, true);

            $(oForm).html($("#iblock_add",data));
            $(".selectpicker").selectpicker();

            recaptchaAskFormInit();
        }
    });


    return false;
}

$(function() {
    $(document).one("click mousemove tap", function () {
        recaptchaAskFormInit();
    });
});


function recaptchaAskFormInit(){
    if($("#asknotauth").get(0)){

       /* try{
            if($("#recaptchascript").get(0))
                $("#recaptchascript").remove();
        } catch (e){

        }

        var t = document,
            e = window,
            a = t.getElementsByTagName("script")[0],
            o = t.createElement("script"),
            i = function() {
                a.parentNode.insertBefore(o, a)
            };
        o.async = true;
        o.defer = true;
        o.id = "recaptchascript";
        o.type = "text/javascript";
        o.src = "https://www.google.com/recaptcha/api.js", "[object Opera]" == e.opera ? t.addEventListener("DOMContentLoaded", i, !1) : i();*/

        try{

            //var gRecaptchaAsk = setInterval(function(){

                //if(typeof grecaptcha != "undefined"){

                   // clearInterval(gRecaptchaAsk);
                    //gRecaptchaAsk = null;
                    grecaptcha.render($('.g-recaptchaask_feedback').get(0), {
                        'sitekey' : $('.g-recaptchaask_feedback').attr("data-sitekey")
                    });

                //}

            //},300);

        } catch(e){

        };

    };
}