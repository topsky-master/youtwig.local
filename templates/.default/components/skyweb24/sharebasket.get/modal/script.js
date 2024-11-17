BX.ready(function () {
    if (document.getElementById('captcha'))
        updateCaptcha(); 
});

function email() {
    var elem = document.getElementById('BasEmail');
    if (elem.style.display == "none")
        elem.style.display = "block";
    else
        elem.style.display = "none";
}

function show_url() {
    var elem = document.getElementById('BasUrl');
    var sub = document.getElementById('sub_url');
    var sub_url_before = document.getElementById('sub_url_before');
    var selection = window.getSelection();
    var el = document.getElementById('url_get');
    elem.style.display = "inline-block";
    sub.style.display = "block";
    selection.selectAllChildren(elem);
    el.style.display = "none";
    sub_url_before.style.display = "none";

    try {
        var successful = document.execCommand('copy');
    } catch (err) {
    }
}



function sendEmail() {
    var email = document.getElementById('BasketEmail').value;
    var form = document.getElementById('BasEmail');
    var formData = serializeArray(form);
    var p_error_form = document.getElementById('p_error_form');
    p_error_form.innerHTML = "";

    BX.ajax({
        url: location.pathname + '?url_for_friends=' + url_for_friends,
        data: {'formData': formData},
        method: 'POST',
        dataType: 'JSON',
        timeout: 30,
        async: true,
        processData: true,
        scriptsRunFirst: true,
        emulateOnload: true,
        start: true,
        cache: false,
        onsuccess: function (data) {
            //console.log(data);
            if (data.type == "Success") {
                document.getElementById('BasEmail').innerHTML = thanksForMail;
            } else {
                dataParse = data.errors;
                p_error_form.parentElement.style.display = 'block';
                for (var i = 0; i < dataParse.length; i++) {
                    p_error_form.innerHTML += dataParse[i] + '<br />'
                }
                updateCaptcha();
            }
        },
        onfailure: function () {
        }
    });
}

function updateCaptcha() {
    BX.ajax({
        url: componentPath + '/captcha.php',
        data: {'captcha': 'yes'},
        method: 'POST',
        dataType: 'JSON',
        timeout: 30,
        async: true,
        processData: true,
        scriptsRunFirst: true,
        emulateOnload: true,
        start: true,
        cache: false,
        onsuccess: function (data) {
            // console.log(data);
            document.getElementById('captcha_pic').setAttribute("src", '/bitrix/tools/captcha.php?captcha_code=' + data.code);
            document.getElementById('captcha_code').setAttribute("value", data.code);
        },
        onfailure: function () {
        }
    });
}




    function un_show_url() {
        var elem = document.getElementById('BasUrl');
        var sub = document.getElementById('sub_url');
        var sub_url_before = document.getElementById('sub_url_before');
        var selection = window.getSelection();
        var el = document.getElementById('url_get');
        elem.style.display = "none";
        sub.style.display = "none";
        selection.selectAllChildren(elem);
        el.style.display = "inline-block";
        sub_url_before.style.display = "block";
    }

    function createShareBasketLink() {
        var shareBasketLink = document.getElementsByClassName('shareBasketLink')[0];
        var basket_distribution = document.getElementsByClassName('basket_distribution')[0];
        // if (basket_distribution.style.display == "none") {
        //     basket_distribution.style.display = "block";
        // } else {
        //     basket_distribution.style.display = "none";
        //     return;
        // }
        var elem = document.getElementById('BasUrl');
        var el = document.getElementById('url_get');
        var social_link = document.getElementsByClassName('socialButtons')[0];
        var elems_link = social_link.getElementsByTagName('a');
    
    
        BX.ajax({
            url: location.pathname,
            data: {'getShareBasket': 'yes'},
            method: 'POST',
            dataType: 'JSON',
            onsuccess: function (data) {
                console.log(data);
                elem.innerText = data.URL_FOR_FRIENDS;
                el.innerText = data.URL_FOR_FRIENDS;
                url_for_friends = data.URL_FOR_FRIENDS;
                for (var i = 0; i < elems_link.length; i++) {
                    if (elems_link[i].href != "") {
                        elems_link[i].href += data.URL_FOR_FRIENDS;
                    }
                }
            },
            onfailure: function (data) {
            }
        });
    }





function serializeArray(form) {
    var field, l, s = [];
    if (typeof form == 'object' && form.nodeName == "FORM") {
        var len = form.elements.length;
        for (var i = 0; i < len; i++) {
            field = form.elements[i];
            if (field.name && !field.disabled && field.type != 'file' && field.type != 'reset' && field.type != 'submit' && field.type != 'button') {
                if (field.type == 'select-multiple') {
                    l = form.elements[i].options.length;
                    for (j = 0; j < l; j++) {
                        if (field.options[j].selected)
                            s[s.length] = {name: field.name, value: field.options[j].value};
                    }
                } else if ((field.type != 'checkbox' && field.type != 'radio') || field.checked) {
                    s[s.length] = {name: field.name, value: field.value};
                }
            }
        }
    }
    return s;
}
