function createFavorite() {

    let favIdsStr = $.cookie('favorites_cookies');
    let favIds = [];

    if (typeof favIdsStr != "undefined" && favIdsStr) {
        favIds = favIdsStr.split(',');
    }

    setListFavorites(favIds);

}

function setListFavorites(favIds) {

    $(".product-item").each(function(){

        let productId = $('.btn-buy',this).data('product-id');

        if (productId > 0) {
            productId += '';
            let clsName = (favIds.length && $.inArray(productId,favIds) !== -1) ? ' fa-heart' : ' fa-heart-o';
            $(this).append('<button data-id="'+productId+'" class="favorite-btn fa icon-control'+clsName+'"></button>');
        }

    });

    $('.favorite-btn').on('click',setFavorite);

    let favCount = $('.fav-count');
    favCount.parent().on('click',function(){
        location.href = '/favorites/';
    });
    favCount.html(favIds.length);

}

function setFavorite(event) {

    let productId = $(this).data('id') + '';

    let favIdsStr = $.cookie('favorites_cookies');

    let favIds = [];

    if (typeof favIdsStr != "undefined" && favIdsStr) {
        favIds = favIdsStr.split(',');
    }

    let dIndex = -1;

    if ((dIndex = $.inArray(productId,favIds)) !== -1) {

        if (dIndex !== -1) {
            favIds.splice(dIndex,1);
        }

        $(this).removeClass('fa-heart').addClass('fa-heart-o');

    } else {

        favIds[favIds.length] = productId;
        $(this).removeClass('fa-heart-o').addClass('fa-heart');

    }

    setFavoriteCookies(favIds);

    if ($('body').hasClass('is_authorized')) {

        let tStamp = new Date().getTime();

        $.getJSON('/ajax_cart/ajax_cookies.php?time='+tStamp,function (favIds){
            console.log(favIds);

            if (location.pathname.indexOf('/favorites/') !== -1) {
                location.reload();
            }
        });

    } else {

        if (location.pathname.indexOf('/favorites/') !== -1) {
            location.reload();
        }
    }

    event.preventDefault();
    return false;

}

function setFavoriteCookies(favIds) {

    let favIdsStr = favIds.join(',');
    $.cookie('favorites_cookies',favIdsStr, {expires: 365, path: '/', domain: location.hostname, https: true});
    $('.fav-count').html(favIds.length);

}

$(function () {

    if ($('#mobile_overlay').get(0)) {
        setTimeout(function(){
            $('#mobile_overlay').removeClass('mobile_preview');
        },1500);

        $(document).on('click', 'a', function(event) {
            if (this.href.indexOf('#') === -1) {

                $('#mobile_overlay').addClass('mobile_preview');

                setTimeout(function(){
                    $('#mobile_overlay').removeClass('mobile_preview');
                },2000);

            }
        });

    }

    createFavorite();

    if ($(".dw").get(0) && $(".cma").get(0)) {
        var e = $(".bcb").height() + parseInt($(".bcb").css("marginBottom")) + parseInt($(".bcb").css("marginTop"));
        e = isNaN(e) ? 60 : e, 992 < $(window).width() && $(".cma").css("margin-top", "-" + e + "px");
        var t = $.cookie("isCatalogMenuHidden");
        "true" == (t = null == t ? "true" : t) || $(window).width() < 992 ? $(".lv-1.dropdown", ".dw").removeClass("open") : ($(".lv-1.dropdown", ".dw").addClass("open"), 992 < $(window).width() && $(".cma").css("margin-top", $(".lv-1.dropdown > ul", ".dw").height() - e + "px")), $("html#js .cma").css("opacity", "1.0"), $(".lv-1.dropdown > a", ".dw").click(function () {
            $(this).parent().hasClass("open") ? ($.cookie("isCatalogMenuHidden", null), 992 < $(window).width() && $(".cma").css("margin-top", $(".lv-1.dropdown > ul", ".dw").height() - e + "px")) : (992 < $(window).width() && $(".cma").css("margin-top", "-" + e + "px"), $.cookie("isCatalogMenuHidden", !0))
        }), $(".dropdown-toggle", ".dw").click(function () {
            $(this).parents(".lv-1.dropdown").hasClass("open") ? ($.cookie("isCatalogMenuHidden", null), 992 < $(window).width() && $(".cma").css("margin-top", $(".lv-1.dropdown > ul", ".dw").height() - e + "px")) : (992 < $(window).width() && $(".cma").css("margin-top", "-" + e + "px"), $.cookie("isCatalogMenuHidden", !0))
        }), $(window).resize(function () {
            var e = $(".bcb").height() + parseInt($(".bcb").css("marginBottom")) + parseInt($(".bcb").css("marginTop"));
            e = isNaN(e) ? 60 : e, 992 < $(window).width() && $(".lv-1.dropdown", ".dw").hasClass("open") ? $(".cma").css("margin-top", $(".lv-1.dropdown > ul", ".dw").height() - e + "px") : 767 < $(window).width() ? $(".cma").css("margin-top", "-" + e + "px") : $(".cma").css("margin-top", "0")
        })
    }
}), $(window).bind("load", function () {
    $(document).one("click tap mousemove scroll", function e() {
        window.dataLayer = window.dataLayer || [], window.dataLayer.push({
            "gtm.start": (new Date).getTime(),
            event: "gtm.js"
        });
        var t, n = document.getElementsByTagName("script")[0];
        (t = document.createElement("script")).async = !0, t.src = "https://www.googletagmanager.com/gtm.js?id=GTM-KJLHBZ", n.parentNode.insertBefore(t, n), window.dataLayer = window.dataLayer || [], window.dataLayer.push({
            "gtm.start": (new Date).getTime(),
            event: "gtm.js"
        }), n = document.getElementsByTagName("script")[0], (t = document.createElement("script")).async = !0, t.src = "https://www.googletagmanager.com/gtm.js?id=GTM-KJLHBZ", n.parentNode.insertBefore(t, n), (window.yandex_metrika_callbacks = window.yandex_metrika_callbacks || []).push(function () {
            try {
                window.yaCounter21503785 = new Ya.Metrika({
                    id: 21503785,
                    clickmap: !0,
                    trackLinks: !0,
                    accurateTrackBounce: !0,
                    webvisor: !0,
                    ecommerce: "dataLayer"
                })
            } catch (e) { }
        });
        var _tmr = window._tmr || (window._tmr = []);
        _tmr.push({ id: "1958713", type: "pageView", start: (new Date()).getTime() });
        (function (d, w, id) {
            if (d.getElementById(id)) return;
            var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
            ts.src = "https://top-fwz1.mail.ru/js/code.js";
            var f = function () { var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s); };
            if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
        })(document, window, "tmr-code");
        var a = document.getElementsByTagName("script")[0],
            o = document.createElement("script");
        n = function () {
            a.parentNode.insertBefore(o, a)
        }, o.type = "text/javascript", o.async = !0, o.src = "https://cdn.jsdelivr.net/npm/yandex-metrica-watch/watch.js", "[object Opera]" == window.opera ? document.addEventListener("DOMContentLoaded", n, !1) : n(),
            function (t, n, a, o) {
                var cHour = $('html').data('time');
                if (cHour > 19 || cHour < 10) return false;
                var d = n.getElementsByTagName(a)[0],
                    i = n.createElement(a);
                i.async = !0, i.src = "//code.jivo.ru/widget/qllQjdC8le", d.parentNode.insertBefore(i, d), $(document).off("click tap mousemove scroll", e)
            }(window, document, "script")

    })
}), document.documentElement && (document.documentElement.id = "js");