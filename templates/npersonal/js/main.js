$(function() {
    if (!$("#devicejs").get(0)) {
        var e = location.protocol + "//" + location.hostname + "/local/components/impel/cartpreorder/templates/.default/device.js",
            t = document.createElement("script");
        t.type = "text/javascript", t.id = "devicejs", t.src = e, $(t).appendTo($("head"))
    }
    var n = setInterval(function() {
        if ("undefined" != typeof device) {
            clearInterval(n), n = null;
            var e = location.protocol + "//" + location.hostname + "/include/resolution.php?";
            e += "&user_resolution=" + $(window).width() + "x" + $(window).height(), e += "&deviceinfo=" + encodeURIComponent(device.type + " " + device.os + " " + device.orientation), jQuery.ajax({
                url: e,
                dataType: "json",
                async: !0,
                processData: !0
            }).done(function(e) {})
        }
    }, 50);
    if ($(".dw").get(0) && $(".cma").get(0)) {
        var o = $(".bcb").height() + parseInt($(".bcb").css("marginBottom")) + parseInt($(".bcb").css("marginTop"));
        o = isNaN(o) ? 60 : o, $(window).width() > 992 && $(".cma").css("margin-top", "-" + o + "px");
        var a = $.cookie("isCatalogMenuHidden");
        "true" == (a = null == a ? "true" : a) || $(window).width() < 992 ? $(".lv-1.dropdown", ".dw").removeClass("open") : ($(".lv-1.dropdown", ".dw").addClass("open"), $(window).width() > 992 && $(".cma").css("margin-top", $(".lv-1.dropdown > ul", ".dw").height() - o + "px")), $("html#js .cma").css("opacity", "1.0"), $(".lv-1.dropdown > a", ".dw").click(function() {
            $(this).parent().hasClass("open") ? ($.cookie("isCatalogMenuHidden", null), $(window).width() > 992 && $(".cma").css("margin-top", $(".lv-1.dropdown > ul", ".dw").height() - o + "px")) : ($(window).width() > 992 && $(".cma").css("margin-top", "-" + o + "px"), $.cookie("isCatalogMenuHidden", !0))
        }), $(".dropdown-toggle", ".dw").click(function() {
            $(this).parents(".lv-1.dropdown").hasClass("open") ? ($.cookie("isCatalogMenuHidden", null), $(window).width() > 992 && $(".cma").css("margin-top", $(".lv-1.dropdown > ul", ".dw").height() - o + "px")) : ($(window).width() > 992 && $(".cma").css("margin-top", "-" + o + "px"), $.cookie("isCatalogMenuHidden", !0))
        }), $(window).resize(function() {
            var e = $(".bcb").height() + parseInt($(".bcb").css("marginBottom")) + parseInt($(".bcb").css("marginTop"));
            e = isNaN(e) ? 60 : e, $(window).width() > 992 && $(".lv-1.dropdown", ".dw").hasClass("open") ? $(".cma").css("margin-top", $(".lv-1.dropdown > ul", ".dw").height() - e + "px") : $(window).width() > 767 ? $(".cma").css("margin-top", "-" + e + "px") : $(".cma").css("margin-top", "0")
        })
    }
}), $(window).bind("load", function() {
    $(document).one("click tap mousemove scroll", function e() {
        window.dataLayer = window.dataLayer || [], window.dataLayer.push({
            "gtm.start": (new Date).getTime(),
            event: "gtm.js"
        });
        var t, n = document.getElementsByTagName("script")[0];
        (t = document.createElement("script")).async = !0, t.src = "https://www.googletagmanager.com/gtm.js?id=GTM-KJLHBZ", n.parentNode.insertBefore(t, n), window.dataLayer = window.dataLayer || [], window.dataLayer.push({
            "gtm.start": (new Date).getTime(),
            event: "gtm.js"
        }), n = document.getElementsByTagName("script")[0], (t = document.createElement("script")).async = !0, t.src = "https://www.googletagmanager.com/gtm.js?id=GTM-KJLHBZ", n.parentNode.insertBefore(t, n), (window.yandex_metrika_callbacks = window.yandex_metrika_callbacks || []).push(function() {
            try {
                window.yaCounter21503785 = new Ya.Metrika({
                    id: 21503785,
                    clickmap: !0,
                    trackLinks: !0,
                    accurateTrackBounce: !0,
                    webvisor: !0,
                    ecommerce: "dataLayer"
                })
            } catch (e) {}
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
        var o = document.getElementsByTagName("script")[0],
            a = document.createElement("script");
        n = function() {
            o.parentNode.insertBefore(a, o)
        }, a.type = "text/javascript", a.async = !0, a.src = "https://cdn.jsdelivr.net/npm/yandex-metrica-watch/watch.js", "[object Opera]" == window.opera ? document.addEventListener("DOMContentLoaded", n, !1) : n(),
            function(t, n, o, a) {
                var cHour = $('html').data('time');
                if(cHour > 19 || cHour < 10) return false;
                var i = n.getElementsByTagName(o)[0],
                    c = n.createElement(o);
                c.async = !0, c.src = "//code-eu1.jivosite.com/widget/qllQjdC8le", i.parentNode.insertBefore(c, i), $(document).off("click tap mousemove scroll", e)
            }(window, document, "script")
    })
}), document.documentElement && (document.documentElement.id = "js");