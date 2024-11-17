function bindReviewAajax(captchaNotRender){

    try{
        if (!(typeof captchaNotRender != "undefined" && captchaNotRender)) {
            grecaptcha.render($('#REVIEWS .g-recaptcha').get(1), {
                'sitekey' : $('#REVIEWS .g-recaptcha').attr("data-sitekey")
            });
        }

    } catch(e){

    };

    $('#REVIEWS .answer-form-link').each(function(event){
        $(this).attr('data-html',true);
        $(this).attr('data-placement','bottom');
        var dataRaviewId = $(this).attr('data-raview-id');
        $('#REVIEWS input[name=REVIEW_ANSWER]').val(dataRaviewId);
        $(this).attr('data-content',$('#REVIEWS #answer-form').html());

        $(this).popover();

        $(this).on('shown.bs.popover', function () {

            try{

                grecaptcha.render($('#REVIEWS .popover-content .g-recaptcha').get(0), {
                    'sitekey' : $('#REVIEWS .popover-content .g-recaptcha').attr("data-sitekey")
                });

            } catch(e){

            };

            $('#REVIEWS .popover-content form').unbind("submit");
            $('#REVIEWS .popover-content form').bind("submit",reviewFormAjax);
            $('#REVIEWS .popover-content').unbind('click');
            $('#REVIEWS .popover-content').bind('click',function(event){
                event.stopPropagation();
            });

        });
    });

    $('#REVIEWS .pagination a').each(function(){

        var qParams = $(this).attr('href').replace(/.*?\?/gi,'');
        this.href = location.pathname + '?' + qParams;

        $(this).click(function(event){

            event.preventDefault();

            var qProductId = $('[name="ELEMENT_ID"]',$('#REVIEWS #reviews-reply-form')).val();
            var qForumId = $('[name="FORUM_ID"]',$('#REVIEWS #reviews-reply-form')).val();
            var qIblockId = $('[name="IBLOCK_ID"]',$('#REVIEWS #reviews-reply-form')).val();

            var qParams = $(this).attr('href').replace(/.*?\?/gi,'');
            qParams += '&ELEMENT_ID='+qProductId+'&FORUM_ID='+qForumId+'&IBLOCK_ID='+qIblockId;


            $.get('/include/productreviewajax.php?'+qParams,function(data){
                $('#REVIEWS').html(data);
                bindReviewAajax();
            });

            return false;

        });

    });

    $('#REVIEWS #reviews-reply-form form').bind("submit",reviewFormAjax);

}

function reviewFormAjax(event){

    var __fself = this;

    if($('.btn-info',__fself).prop('disabled') == true)
        return false;

    $('.btn-info',__fself).prop('disabled',true);

    var dataStr = $(this).serialize();

    $.ajax({
        url: '/include/productreviewajax.php',
        data: dataStr,
        method: 'POST'
    }).done(function(data,textStatus,jqXHR){

        if(jqXHR.status == 200){

            $('#REVIEWS').html(data);
            bindReviewAajax();

        } else {

            var qProductId = $('[name="ELEMENT_ID"]',$('#REVIEWS #reviews-reply-form')).val();

            $.get('/include/productreviewajax.php?&ELEMENT_ID='+qProductId,function(data){
                $('#REVIEWS').html(data);
                bindReviewAajax();

            });
        }

        setTimeout(function(){
            $('.btn-info',__fself).prop('disabled',false);
        },2000)

    });

    event.preventDefault();
    return false;

};

function loadMoreCommentsAjax(){
    var dCurrent = $(this).data('current');
    var dMax = $(this).data('num');
    var dPagen = $(this).data('pagen');


    if (dMax > dCurrent) {
        ++dCurrent;
        var nPage = location.protocol + '//' + 	location.hostname + location.pathname + '?PAGEN_' + dPagen + '=' + dCurrent;
        $.get(nPage,function(sHtml){
            var oHtml = $.parseHTML(sHtml);
            var cHtml = $('#REVIEWS .reviews-block-container').html() + $('#REVIEWS .reviews-block-container',oHtml).html();
            $('#REVIEWS .reviews-block-container').html(cHtml);
            $(this).data('current',dCurrent);
            if (!(dMax > dCurrent)) {
                $('#REVIEWS #more-reviews-btn').remove();
            }

        });

    }

};

$(function(){

    $('#REVIEWS #more-reviews-btn').on('click',loadMoreCommentsAjax);
    bindReviewAajax(true);

});