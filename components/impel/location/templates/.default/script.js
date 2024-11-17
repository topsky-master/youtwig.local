auItems = [];

$.extend($.ui.autocomplete.prototype, {
    _renderMenu: function (ul, items) {
        //remove scroll event to prevent attaching multiple scroll events to one container element
        $(ul).unbind("scroll");
        var self = this;
        self._scrollMenu(ul, items);
    },

    _scrollMenu: function (ul, items) {

        var self = this;

        var startShow = items.length;
        var stepShow = items.length;

        $.each(items, function(index, item) {
            self._renderItem(ul, item);
        });

        self.menu.refresh();

        $(ul).scroll(function() {

            var height = $(ul).outerHeight();
            var scrollHeight = $(ul)[0].scrollHeight;
            var scrollTop = $(ul).scrollTop();
            inaAjax = false;

            if($('.ui-autocomplete.ui-menu').hasClass('ui-scrolling')){
                $('.ui-autocomplete.ui-menu').removeClass('ui-scrolling');
            }

            if (startShow !== false
                && !inaAjax
                && scrollTop >= scrollHeight - height) {

                if(!$('.ui-autocomplete.ui-menu').hasClass('ui-scrolling')){
                    $('.ui-autocomplete.ui-menu').addClass('ui-scrolling');
                }

                var acUrl = window.rUrl + "&offset="+startShow+"&loctype="+locType;

                startShow += stepShow;
                inaAjax = true;

                $.getJSON(acUrl , function (data){

                    if(!data){
                        startShow = false;
                        return;
                    }

                    var nresults = [];

                    $.each(data, function (k, opt) {

                        locType = opt.loctype;

                        auItems.push({
                            label : opt.name,
                            value : opt.value
                        });

                        nresults.push({
                            label : opt.name,
                            value : opt.value
                        });
                    });

                    $.each(nresults, function(index, item) {
                        self._renderItem(ul, item);
                    });

                    self.menu.refresh();
                    self._resizeMenu();
                    ul.show();

                    ul.position($.extend({
                        of: self.element
                    }, self.options.position));

                    inaAjax = false;

                    if($('.ui-autocomplete.ui-menu').hasClass('ui-scrolling')){
                        $('.ui-autocomplete.ui-menu').removeClass('ui-scrolling');
                    }

                });

            }
        });

    }

});

function chooseLocation(){

    $(window).on('resize orientationchange',function(){
        try {
            $('.ui-autocomplete.ui-menu').css('display','none');
        } catch(e) {

        }
    });

    $(".chooselocation").each(function(){

        getLocationCity($(".form-control.city",this),this);
        getLocationStreet($(".form-control.street",this),this);
        copyZipCodeAfterReload(this);

    });

}

function clearLocation(sElt,pElt,aElt){
    $(sElt).bind('change',function(){
        if($.trim(this.value) == ''){

            if(typeof aElt != "undefined" && aElt){
                $(aElt).val("");
            }

            var cityId = $('.loc.city').attr('data-value');
            $('.locid',pElt).val(cityId);

        }
    });
}

function getLocationCity(stEl,pElt){

    var stId = $(stEl).attr('data-typeid');
    clearLocation(stEl,pElt,$(".form-control.street",pElt));

    $(stEl).unbind('change');

    try{

        if(iCityTimeout){
            clearTimeout(iCityTimeout);
            iCityTimeout = null;
        }

        if (jQuery(stEl).data('autocomplete')) {
            jQuery(stEl).autocomplete("destroy");
            jQuery(stEl).removeData('autocomplete');
        }

    } catch (e){

    }

    try {
        $('.ui-autocomplete.ui-menu').css('display','none');
    } catch(e) {

    }

    $(stEl).autocomplete({
        source: function(request ,response){

            locType = 'city';
            var sVal = $.trim(request.term);
            sVal = encodeURIComponent(sVal);

            if(sVal != ""){

                var rURI = location.protocol + '//' + location.hostname + '/include/location.php';
                var acUrl = rURI + "?&typeid="+stId+"&rjson=true&phrase="+sVal;
                window.rUrl = acUrl;

                try{
                    BX.showWait();
                } catch(e){

                }

                $.getJSON(acUrl , function (data){

                    if(!data){

                        data = {0: {name: $(stEl).attr('data-no-results'), value: ""}};

                    }

                    try {
                        BX.closeWait();
                    } catch(e) {

                    }

                    auItems = [];

                    response($.map(data,function(opt){

                        locType = opt.loctype;

                        auItems.push({
                            label : opt.name,
                            value : opt.value
                        });

                        return {
                            label : opt.name,
                            value : opt.value
                        }
                    }));

                });



            }

        },
        select: function( event, ui ) {

            var oCityId = $(stEl).attr('data-value');

            if(!ui.item){
                if(event.originalEvent
                    && event.originalEvent.originalEvent
                    && event.originalEvent.originalEvent.currentTarget){

                    var cLbl = $.trim($(event.originalEvent.originalEvent.currentTarget).text());

                    if(auItems
                        && auItems.length){

                        $.each(auItems,function(k,val){
                            if(val.label == cLbl){
                                ui.item = {label: val.label, value: val.value};
                                return false;
                            }
                        })

                    }
                }
            }

            if(ui.item.value){

                hasStreets(ui.item.value,pElt);

                $(stEl).val($.trim(ui.item.label));
                $(stEl).attr('data-value',$.trim(ui.item.value));

                if(oCityId != ui.item.value) {

                    var rURI = location.protocol + '//' + location.hostname + '/include/location.php';
                    var acUrl = rURI + "?&action=zipcode&rjson=true&locid="+ui.item.value;

                    $.getJSON(acUrl , copyZipCode);

                    $(".form-control.street",pElt).attr('data-value','');
                    $(".form-control.street",pElt).val('');
                }
            }

            return false;

        },
        position: {  my: "right top", at: "right bottom" },
        minLength: 3,
        delay: 500,
        disabled: true

    });

    iCityTimeout = setTimeout(function(){
        $(stEl).autocomplete({"disabled": false});
    },300);
}

function copyZipCode(data){

    if(data && data.ZIP) {
        var zipCopyProp = $('.zipcopy').val();
        if(zipCopyProp)
            $(zipCopyProp).val(data.ZIP);

    }
}

function hasStreets(locId,pElt){

    var rURI = location.protocol + '//' + location.hostname + '/include/location.php';
    var acUrl = rURI + "?&action=hasstreets&rjson=true&locid="+locId;

    $.getJSON(acUrl , function(data){

        if(data
            && !data.CNT) {

            $('.locid',pElt).val($.trim(locId));
            $('.loc.street').parents('.row').addClass('hidden');

        } else if(data
            && data.CNT
            && ($('.loc.street').parents('.row').hasClass('hidden')
                || $('.loc.street').parents('.row').css('display') == 'none')
        ) {

            $('.locid',pElt).val($.trim(locId));
            //$('.loc.street').attr('disabled',false);

        } else {

            var cityId = $('.loc.city').attr('data-value');
            $('.locid',pElt).val(cityId);

        }

        try {
            if ($('.jsaction', pElt).get(0)) {

                var sfName = $('.jsaction', pElt).val();
                eval(sfName);
            }

        } catch (e) {

        }

    });



}

function copyZipCodeAfterReload(pElt){


    var locId = $('.locid',pElt).val();


    var acUrl = '';

    if(locId) {

        var zipCopyProp = $('.zipcopy').val();
        if(zipCopyProp){

            var zipVal = $(zipCopyProp).val();

            if(zipVal == '' || true) {
                var rURI = location.protocol + '//' + location.hostname + '/include/location.php';
                var acUrl = rURI + "?&action=zipcode&rjson=true&locid="+locId;
                $.getJSON(acUrl , copyZipCode);
            }
        }
    }

}

function getLocationStreet(stEl,pElt){

    clearLocation(stEl,pElt);
    var stId = $(stEl).attr('data-typeid');

    $(stEl).unbind('change');

    try {
        if(iStreetTimeOut){
            clearTimeout(iStreetTimeOut);
            iStreetTimeOut = null;
        }

        if (jQuery(stEl).data('autocomplete')) {
            jQuery(stEl).autocomplete("destroy");
            jQuery(stEl).removeData('autocomplete');
        }

    } catch(e) {

    }

    try {
        $('.ui-autocomplete.ui-menu').css('display','none');
    } catch(e) {

    }

    $(stEl).autocomplete({
        source: function(request ,response){

            var sVal = $.trim(request.term);
            sVal = encodeURIComponent(sVal);

            if(sVal != ""){

                var cId = $(".form-control.city",pElt).attr('data-value');

                if(cId) {

                    var rURI = location.protocol + '//' + location.hostname + '/include/location.php';
                    var acUrl = rURI + "?&typeid=" + stId + "&rjson=true&phrase=" + sVal + '&cityid=' + cId;
                    window.rUrl = acUrl;

                    try{
                        BX.showWait();
                    } catch(e){

                    }

                    $.getJSON(acUrl, function (data) {

                        if(!data){

                            data = {0: {name: $(stEl).attr('data-no-results'), value: ""}};

                        }

                        try{
                            BX.closeWait();
                        } catch(e){

                        }

                        auItems = [];

                        response($.map(data, function (opt) {

                            auItems.push({
                                label : opt.name,
                                value : opt.value
                            });

                            return {
                                label: opt.name,
                                value: opt.value
                            }
                        }));

                    });

                }

            }

        },
        select: function( event, ui ) {

            if(!ui.item){
                if(event.originalEvent
                    && event.originalEvent.originalEvent
                    && event.originalEvent.originalEvent.currentTarget){

                    var cLbl = $.trim($(event.originalEvent.originalEvent.currentTarget).text());

                    if(auItems
                        && auItems.length){

                        $.each(auItems,function(k,val){
                            if(val.label == cLbl){
                                ui.item = {label: val.label, value: val.value};
                                return false;
                            }
                        })

                    }
                }
            }

            if(ui.item.value){

                $('.locid',pElt).val($.trim(ui.item.value));

                try{
                    if($('.jsaction',pElt).get(0)){

                        var sfName = $('.jsaction',pElt).val();
                        eval(sfName);
                    }

                } catch (e) {

                }

                var rURI = location.protocol + '//' + location.hostname + '/include/location.php';
                var acUrl = rURI + "?&action=zipcode&rjson=true&locid="+ui.item.value;

                $.getJSON(acUrl , copyZipCode);

                $(stEl).val($.trim(ui.item.label));
                $(stEl).attr('data-value',$.trim(ui.item.value));

            }

            return false;

        },
        position: {  my: "right top", at: "right bottom" },
        minLength: 3,
        delay: 500,
        disabled: true

    });

    iStreetTimeOut = setTimeout(function(){
        $(stEl).autocomplete({"disabled": false});
    },300);

}

$(function(){

    chooseLocation();

    BX.addCustomEvent('onAjaxSuccess', chooseLocation);
});