function bindReviewQuestionsAajax(captchaNotRender){

    bindPhoneMask();

    try{
        if (!(typeof captchaNotRender != "undefined" && captchaNotRender)) {
            grecaptcha.render($('#QUESTIONS .g-recaptcha').get(1), {
                'sitekey': $('#QUESTIONS .g-recaptcha').attr("data-sitekey")
            });
        }

    } catch(e){

    };


    $('#QUESTIONS .answer-form-link').each(function(event){
        $(this).attr('data-html',true);
        $(this).attr('data-placement','bottom');
        var dataRaviewId = $(this).attr('data-raview-id');
        $('#QUESTIONS input[name=REVIEW_ANSWER]').val(dataRaviewId);
        $(this).attr('data-content',$('#QUESTIONS #answer-form').html());

        $(this).popover();

        $(this).on('shown.bs.popover', function () {

            bindPhoneMask();

            try{

                grecaptcha.render($('#QUESTIONS .popover-content .g-recaptcha').get(0), {
                    'sitekey' : $('#QUESTIONS .popover-content .g-recaptcha').attr("data-sitekey")
                });

            } catch(e){

            };

            $('#QUESTIONS .popover-content form').unbind("submit");
            $('#QUESTIONS .popover-content form').bind("submit",reviewQuestionsFormAjax);
            $('#QUESTIONS .popover-content').unbind('click');
            $('#QUESTIONS .popover-content').bind('click',function(event){
                event.stopPropagation();
            });

        });
    });

    $('#QUESTIONS .pagination a').each(function(){

        var qParams = $(this).attr('href').replace(/.*?\?/gi,'');
        this.href = location.pathname + '?' + qParams;

        $(this).click(function(event){

            event.preventDefault();

            var qProductId = $('[name="ELEMENT_ID"]',$('#QUESTIONS #reviews-reply-form')).val();
            var qForumId = $('[name="FORUM_ID"]',$('#QUESTIONS #reviews-reply-form')).val();
            var qIblockId = $('[name="IBLOCK_ID"]',$('#QUESTIONS #reviews-reply-form')).val();

            var qParams = $(this).attr('href').replace(/.*?\?/gi,'');
            qParams += '&ELEMENT_ID='+qProductId+'&FORUM_ID='+qForumId+'&IBLOCK_ID='+qIblockId;

            $.get('/include/productquestionsajax.php?'+qParams,function(data){
                $('#QUESTIONS').html(data);
                bindReviewQuestionsAajax();
            });

            return false;

        });

    });

    $('#QUESTIONS #reviews-reply-form form').bind("submit",reviewQuestionsFormAjax);

}

function reviewQuestionsFormAjax(event){

    var __fself = this;

    if($('.btn-info',__fself).prop('disabled') == true)
        return false;

    $('.btn-info',__fself).prop('disabled',true);

    var dataStr = $(this).serialize();

    $.ajax({
        url: '/include/productquestionsajax.php',
        data: dataStr,
        method: 'POST'
    }).done(function(data,textStatus,jqXHR){

        if(jqXHR.status == 200){

            $('#QUESTIONS').html(data);
            bindReviewQuestionsAajax();

        } else {

            var qProductId = $('[name="ELEMENT_ID"]',$('#QUESTIONS #reviews-reply-form')).val();

            $.get('/include/productquestionsajax.php?&ELEMENT_ID='+qProductId,function(data){
                $('#QUESTIONS').html(data);
                bindReviewQuestionsAajax();

            });
        }

        setTimeout(function(){
            $('.btn-info',__fself).prop('disabled',false);
        },2000)

    });

    event.preventDefault();
    return false;

};

function loadMoreQuestionsAjax(){
    var dCurrent = $(this).data('current');
    var dMax = $(this).data('num');
    var dPagen = $(this).data('pagen');


    if (dMax > dCurrent) {
        ++dCurrent;
        var nPage = location.protocol + '//' + 	location.hostname + location.pathname + '?PAGEN_' + dPagen + '=' + dCurrent;
        $.get(nPage,function(sHtml){
            var oHtml = $.parseHTML(sHtml);
            var cHtml = $('#QUESTIONS .reviews-block-container').html() + $('#QUESTIONS .reviews-block-container',oHtml).html();
            $('#QUESTIONS .reviews-block-container').html(cHtml);
            $(this).data('current',dCurrent);
            if (!(dMax > dCurrent)) {
                $('#QUESTIONS #more-reviews-btn').remove();
            }

        });

    }

};

function bindPhoneMask() {

    phonePlaceholder = '+79_________';
    phoneMask = '+79000000000';

    $('[name="REVIEW_PHONE"]').attr('autocomplete', false);
    $('[name="REVIEW_PHONE"]').attr('placeholder', phonePlaceholder);

    try {
        var maskOptions = {

            onKeyPress: function (cep, event, currentField, options) {
                if ($(currentField).get(0)
                    && $(currentField).val().indexOf('+78') === 0) {
                    $(currentField).val($(currentField).val().replace('+78', '+79'));
                }
            }

        };

        $('[name="REVIEW_PHONE"]').each(function(){
            $(this).mask(phoneMask, maskOptions);
        });

    } catch (e) {

    }
}

$(function(){
    $('#QUESTIONS #more-reviews-btn').on('click',loadMoreQuestionsAjax);
    bindReviewQuestionsAajax(true);
});
