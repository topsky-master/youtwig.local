BX.saleOrderAjax = {

    BXCallAllowed: false,

    options: {},
    indexCache: {},
    controls: {},

    modes: {},
    properties: {},

    // called once, on component load
    init: function (options) {
        var ctx = this;
        this.options = options;

        window.submitFormProxy = BX.proxy(function () {
            ctx.submitFormProxy.apply(ctx, arguments);
        }, this);

        BX(function () {
            ctx.initDeferredControl();
        });
        BX(function () {
            ctx.BXCallAllowed = true; // unlock form refresher
        });

        this.controls.scope = BX('order_form_div');

        // user presses "add location" when he cannot find location in popup mode
        BX.bindDelegate(this.controls.scope, 'click', { className: '-bx-popup-set-mode-add-loc' }, function () {

            var input = BX.create('input', {
                attrs: {
                    type: 'hidden',
                    name: 'PERMANENT_MODE_STEPS',
                    value: '1'
                }
            });

            BX.prepend(input, BX('ORDER_FORM'));

            ctx.BXCallAllowed = false;
            submitForm();
        });
    },

    cleanUp: function () {

        for (var k in this.properties) {
            if (typeof this.properties[k].input != 'undefined') {
                BX.unbindAll(this.properties[k].input);
                this.properties[k].input = null;
            }

            if (typeof this.properties[k].control != 'undefined') {
                BX.unbindAll(this.properties[k].control);
            }
        }

        this.properties = {};
    },

    addPropertyDesc: function (desc) {
        this.properties[desc.id] = desc.attributes;
        this.properties[desc.id].id = desc.id;
    },

    // called each time form refreshes
    initDeferredControl: function () {
        var ctx = this;

        // first, init all controls
        if (typeof window.BX.locationsDeferred != 'undefined') {

            this.BXCallAllowed = false;

            for (var k in window.BX.locationsDeferred) {

                window.BX.locationsDeferred[k].call(this);
                window.BX.locationsDeferred[k] = null;
                delete (window.BX.locationsDeferred[k]);

                if (typeof window.BX.locationSelectors[k] != "undefined"
                    && typeof this.properties[k] != "undefined") {
                    this.properties[k].control = window.BX.locationSelectors[k];
                    delete (window.BX.locationSelectors[k]);
                }
            }
        }

        for (var k in this.properties) {

            // zip input handling
            if (this.properties[k].isZip) {
                var row = this.controls.scope.querySelector('[data-property-id-row="' + k + '"]');
                if (BX.type.isElementNode(row)) {

                    var input = row.querySelector('input[type="text"]');
                    if (BX.type.isElementNode(input)) {
                        this.properties[k].input = input;

                        // set value for the first "location" property met
                        var locPropId = false;
                        for (var m in this.properties) {
                            if (this.properties[m].type == 'LOCATION') {
                                locPropId = m;
                                break;
                            }
                        }

                        if (locPropId !== false) {
                            BX.bindDebouncedChange(input, function (value) {

                                input = null;
                                row = null;

                                if (/^\s*\d{6}\s*$/.test(value)) {

                                    ctx.getLocationByZip(value, function (locationId) {
                                        ctx.properties[locPropId].control.setValueById(locationId);
                                    }, function () {
                                        try {
                                            ctx.properties[locPropId].control.clearSelected(locationId);
                                        } catch (e) { }
                                    });


                                }
                            });
                        }
                    }
                }
            }

            if (this.checkAbility(k, 'canHaveAltLocation')) {

                //this.checkMode(k, 'altLocationChoosen');

                var control = this.properties[k].control;

                // control can have "select other location" option
                control.setOption('pseudoValues', ['other']);

                // when control tries to search for items
                control.bindEvent('before-control-item-discover-done', function (knownItems, adapter) {

                    control = null;

                    var parentValue = adapter.getParentValue();

                    // you can choose "other" location only if parentNode is not root and is selectable
                    if (parentValue == this.getOption('rootNodeValue') || !this.checkCanSelectItem(parentValue))
                        return;

                    knownItems.unshift({ DISPLAY: ctx.options.messages.otherLocation, VALUE: 'other', CODE: 'other', IS_PARENT: false });
                });

                // currently wont work for initially created controls, so commented out
                /*
                // when control is being created with knownItems
                control.bindEvent('before-control-placed', function(adapter){
                    if(typeof adapter.opts.knownItems != 'undefined')
                        adapter.opts.knownItems.unshift({DISPLAY: so.messages.otherLocation, VALUE: 'other', CODE: 'other', IS_PARENT: false});
    
                });
                */

                // add special value "other", if there is "city" input
                if (this.checkMode(k, 'altLocationChoosen')) {

                    var altLocProp = this.getAltLocPropByRealLocProp(k);
                    this.toggleProperty(altLocProp.id, true);

                    var adapter = control.getAdapterAtPosition(control.getStackSize() - 1);

                    // also restore "other location" label on the last control
                    if (typeof adapter != 'undefined' && adapter !== null)
                        adapter.setValuePair('other', ctx.options.messages.otherLocation); // a little hack
                } else {

                    var altLocProp = this.getAltLocPropByRealLocProp(k);
                    this.toggleProperty(altLocProp.id, false);

                }
            } else {

                var altLocProp = this.getAltLocPropByRealLocProp(k);
                if (altLocProp && altLocProp !== false) {

                    // replace default boring "nothing found" label for popup with "-bx-popup-set-mode-add-loc" inside
                    if (this.properties[k].type == 'LOCATION' && typeof this.properties[k].control != 'undefined' && this.properties[k].control.getSysCode() == 'sls')
                        this.properties[k].control.replaceTemplate('nothing-found', this.options.messages.notFoundPrompt);

                    this.toggleProperty(altLocProp.id, false);
                }
            }

            if (typeof this.properties[k].control != 'undefined' && this.properties[k].control.getSysCode() == 'slst') {

                var control = this.properties[k].control;

                // if a children of CITY is shown, we must replace label for 'not selected' variant
                var adapter = control.getAdapterAtPosition(control.getStackSize() - 1);
                var node = this.getPreviousAdapterSelectedNode(control, adapter);

                if (node !== false && node.TYPE_ID == ctx.options.cityTypeId) {

                    var selectBox = adapter.getControl();
                    if (selectBox.getValue() == false) {

                        adapter.getControl().replaceMessage('notSelected', ctx.options.messages.moreInfoLocation);
                        adapter.setValuePair('', ctx.options.messages.moreInfoLocation);
                    }
                }
            }

        }

        this.BXCallAllowed = true;
    },

    // checkMode: function (propId, mode) {

        //if(typeof this.modes[propId] == 'undefined')
        //	this.modes[propId] = {};

        //if(typeof this.modes[propId] != 'undefined' && this.modes[propId][mode])
        //	return true;

        // if (mode == 'altLocationChoosen') {

        //     if (this.checkAbility(propId, 'canHaveAltLocation')) {

        //         var input = this.getInputByPropId(this.properties[propId].altLocationPropId);
        //         var altPropId = this.properties[propId].altLocationPropId;

        //         if (input !== false && input.value.length > 0 && !input.disabled && this.properties[altPropId].valueSource != 'default') {

        //             //this.modes[propId][mode] = true;
        //             return true;
        //         }
        //     }
        // }

    // },

    checkAbility: function (propId, ability) {

        if (typeof this.properties[propId] == 'undefined')
            this.properties[propId] = {};

        if (typeof this.properties[propId].abilities == 'undefined')
            this.properties[propId].abilities = {};

        if (typeof this.properties[propId].abilities != 'undefined' && this.properties[propId].abilities[ability])
            return true;

        if (ability == 'canHaveAltLocation') {

            if (this.properties[propId].type == 'LOCATION') {

                // try to find corresponding alternate location prop
                if (typeof this.properties[propId].altLocationPropId != 'undefined' && typeof this.properties[this.properties[propId].altLocationPropId]) {

                    var altLocPropId = this.properties[propId].altLocationPropId;

                    if (typeof this.properties[propId].control != 'undefined' && this.properties[propId].control.getSysCode() == 'slst') {

                        if (this.getInputByPropId(altLocPropId) !== false) {
                            this.properties[propId].abilities[ability] = true;
                            return true;
                        }
                    }
                }
            }

        }

        return false;
    },

    getInputByPropId: function (propId) {
        if (typeof this.properties[propId].input != 'undefined')
            return this.properties[propId].input;

        var row = this.getRowByPropId(propId);
        if (BX.type.isElementNode(row)) {
            var input = row.querySelector('input[type="text"]');
            if (BX.type.isElementNode(input)) {
                this.properties[propId].input = input;
                return input;
            }
        }

        return false;
    },

    getRowByPropId: function (propId) {

        if (typeof this.properties[propId].row != 'undefined')
            return this.properties[propId].row;

        var row = this.controls.scope.querySelector('[data-property-id-row="' + propId + '"]');
        if (BX.type.isElementNode(row)) {
            this.properties[propId].row = row;
            return row;
        }

        return false;
    },

    getAltLocPropByRealLocProp: function (propId) {
        if (typeof this.properties[propId].altLocationPropId != 'undefined')
            return this.properties[this.properties[propId].altLocationPropId];

        return false;
    },

    toggleProperty: function (propId, way, dontModifyRow) {

        var prop = this.properties[propId];

        if (typeof prop.row == 'undefined')
            prop.row = this.getRowByPropId(propId);

        if (typeof prop.input == 'undefined')
            prop.input = this.getInputByPropId(propId);

        if (!way) {

            if (!dontModifyRow) {
                try {
                    BX.hide(prop.row);
                } catch (e) {

                }
            }

            prop.input.disabled = true;
        } else {
            if (!dontModifyRow) {
                try {
                    BX.show(prop.row);
                } catch (e) {

                }
            }
            prop.input.disabled = false;
        }
    },

    submitFormProxy: function (item, control) {
        var propId = false;
        for (var k in this.properties) {
            if (typeof this.properties[k].control != 'undefined' && this.properties[k].control == control) {
                propId = k;
                break;
            }
        }

        if (item != 'other') {

            if (this.BXCallAllowed) {

                // drop mode "other"
                if (propId != false) {
                    if (this.checkAbility(propId, 'canHaveAltLocation')) {

                        if (typeof this.modes[propId] == 'undefined')
                            this.modes[propId] = {};

                        this.modes[propId]['altLocationChoosen'] = false;

                        var altLocProp = this.getAltLocPropByRealLocProp(propId);
                        if (altLocProp !== false) {

                            this.toggleProperty(altLocProp.id, false);
                        }
                    }
                }

                this.BXCallAllowed = false;
                submitForm();
            }

        } else { // only for sale.location.selector.steps

            if (this.checkAbility(propId, 'canHaveAltLocation')) {

                var adapter = control.getAdapterAtPosition(control.getStackSize() - 2);
                if (adapter !== null) {
                    var value = adapter.getValue();
                    control.setTargetInputValue(value);

                    // set mode "other"
                    if (typeof this.modes[propId] == 'undefined')
                        this.modes[propId] = {};

                    this.modes[propId]['altLocationChoosen'] = true;

                    var altLocProp = this.getAltLocPropByRealLocProp(propId);
                    if (altLocProp !== false) {

                        this.toggleProperty(altLocProp.id, true, true);
                    }

                    this.BXCallAllowed = false;
                    submitForm();
                }
            }
        }
    },

    getPreviousAdapterSelectedNode: function (control, adapter) {

        var index = adapter.getIndex();
        var prevAdapter = control.getAdapterAtPosition(index - 1);

        if (typeof prevAdapter !== 'undefined' && prevAdapter != null) {
            var prevValue = prevAdapter.getControl().getValue();

            if (typeof prevValue != 'undefined') {
                var node = control.getNodeByValue(prevValue);

                if (typeof node != 'undefined')
                    return node;

                return false;
            }
        }

        return false;
    },
    getLocationByZip: function (value, successCallback, notFoundCallback) {
        if (typeof this.indexCache[value] != 'undefined') {
            successCallback.apply(this, [this.indexCache[value]]);
            return;
        }

        //ShowWaitWindow();
        try {
            BX.showWait();
        } catch (e) {

        }
        var ctx = this;

        BX.ajax({

            url: this.options.source,
            method: 'post',
            dataType: 'json',
            async: true,
            processData: true,
            emulateOnload: true,
            start: true,
            data: { 'ACT': 'GET_LOC_BY_ZIP', 'ZIP': value },
            //cache: true,
            onsuccess: function (result) {


                //try{

                //CloseWaitWindow();
                BX.closeWait();

                if (result.result) {

                    ctx.indexCache[value] = result.data.ID;
                    successCallback.apply(ctx, [result.data.ID]);

                } else
                    notFoundCallback.call(ctx);

                //}catch(e){console.dir(e);}

            },
            onfailure: function (type, e) {

                //CloseWaitWindow();
                BX.closeWait();
                // on error do nothing
            }

        });
    }

}
function getPlaceholder(country) {

    var placeholder = '';

    for (var nm = 0; nm < orderCountries.length; nm++) {

        if (orderCountries[nm] == country) {
            placeholder = orderCountriesPhonePlaceholder[nm];
            break;
        }

    }

    return placeholder;


};

function phoneMaskBehavior(country) {

    var mask = '';

    for (var nm = 0; nm < orderCountries.length; nm++) {

        if (orderCountries[nm] == country) {
            mask = orderCountriesPhoneMask[nm];
            break;
        }

    }

    return mask;

};

orderCountries = ['Россия', 'Казахстан', 'Украина', 'Беларусь'];
orderCountriesPhonePlaceholder = ['+79_________', '+77_________', '+380_________', '+375_________'];
orderCountriesPhoneMask = ['+79000000000', '+77000000000', '+380000000000', '+375000000000'];
checkmodelHidden = false;

function remapPhoneMask() {

    setTimeout(function () {
        if ($('#orderError').get(0)) {
            $([document.documentElement, document.body]).animate({
                scrollTop: $('#orderError').offset().top
            }, 300);
        }
    }, 300);

    var phoneElt = $('#ORDER_PROP_3').get(0) ? $('#ORDER_PROP_3').get(0) : $('#ORDER_PROP_14').get(0);
    var phoneParent = $('.ORDER_PROP_3').get(0) ? $('.ORDER_PROP_3').get(0) : $('.ORDER_PROP_14').get(0);


    if ($(phoneElt).get(0)) {

        var idDefaultRussia = true;
        var countrySelected = $.trim($('#countryName').val());

        if (countrySelected) {

            countrySelected = ($.inArray(countrySelected, orderCountries) === -1) ? 'Россия' : countrySelected;

            if ($.inArray(countrySelected, orderCountries) !== -1) {

                var phoneMask = phoneMaskBehavior(countrySelected);
                var phonePlaceholder = getPlaceholder(countrySelected);

                try {

                    $(phoneElt).cleanVal();
                    $(phoneElt).unmask();
                    $(phoneElt).unbind('keydown');

                    $(phoneElt).removeClass('has_error');
                    $(phoneElt).removeClass('has_order_error');

                    $(phoneElt).tooltip('destroy');


                } catch (e) {

                }


                $(phoneElt).attr('autocomplete', false);

                $(phoneElt).attr('placeholder', phonePlaceholder);

                switch (countrySelected) {
                    case 'Россия':

                        var spValue = $(phoneElt).val();

                        if (spValue[0] == '9' || spValue[0] == '8') {
                            spValue = '7' + spValue;
                            $(phoneElt).val(spValue);
                        }

                        var iMaxLength = 12;
                        iMaxLength = spValue[0] == '+' ? 12 : 11;

                        if ($(phoneElt).val().length < iMaxLength) {

                            $(phoneParent).find('.has-success').removeClass('has-success').addClass('has_order_error');
                            $(phoneParent).find('.has_tooltip').addClass('has_error');
                            $('#ORDER_CONFIRM_BUTTON').prop('haserror', true);

                            if ($('#ORDER_CONFIRM_BUTTON').prop('disabled') == false) {
                                $('#ORDER_CONFIRM_BUTTON').prop('disabled', true);
                            }


                        }

                        if ($(phoneElt).val().length > (iMaxLength - 1)) {

                            $(phoneParent).find('.has_order_error').removeClass('has_order_error').addClass('has-success');
                            $(phoneParent).find('.has_tooltip').removeClass('has_error');
                            $('#ORDER_CONFIRM_BUTTON').prop('haserror', false);

                            if ($('#ORDER_CONFIRM_BUTTON').prop('disabled') != false) {
                                $('#ORDER_CONFIRM_BUTTON').prop('disabled', false);
                                console.log('called');
                            }
                        }


                        var maskOptions = {

                            onComplete: function (cep) {

                                $(phoneParent).find('.has_order_error').removeClass('has_order_error').addClass('has-success');
                                $(phoneParent).find('.has_tooltip').removeClass('has_error');
                                $('#ORDER_CONFIRM_BUTTON').prop('haserror', false);

                                if ($('#ORDER_CONFIRM_BUTTON').prop('disabled') != false) {
                                    $('#ORDER_CONFIRM_BUTTON').prop('disabled', false);
                                    console.log('called 2');
                                }

                            },

                            onKeyPress: function (cep, event, currentField, options) {

                                var spValue = $(phoneElt).val();
                                var iMaxLength = 12;

                                if (spValue[0] == '9' || spValue[0] == '8') {
                                    spValue = '7' + spValue;
                                    $(phoneElt).val(spValue);
                                }

                                iMaxLength = spValue[0] == '+' ? 12 : 11;

                                if ($(currentField).get(0)
                                    && $(currentField).val().indexOf('+78') === 0) {
                                    $(currentField).val($(currentField).val().replace('+78', '+79'));
                                }

                                if ($(currentField).get(0)
                                    && $(currentField).val().indexOf('99') !== -1
                                    && $(currentField).attr('id') == phoneElt.id
                                    && !$('#checkPhoneModal').hasClass('in')
                                    && !checkmodelHidden
                                    && $(currentField).val().length == iMaxLength
                                ) {

                                    $('#checkphone').val($(currentField).val());
                                    $('#checkphone').mask(phoneMask);

                                    try {
                                        $('#checkPhoneModal').modal('hide');
                                        checkmodelHidden = false;
                                    } catch (e) {

                                    }

                                    $('#checkPhoneModal').modal();
                                    checkmodelHidden = true;
                                    $('#checkPhoneModal button').unbind('click');
                                    $('#checkPhoneModal button').on('click', function (event) {

                                        $(phoneElt).val($('#checkphone').val());

                                        $.cookie('phonechecked', $('#checkphone').val());

                                        try {
                                            $('#checkPhoneModal').modal('hide');
                                            checkmodelHidden = false;
                                        } catch (e) {

                                        }

                                        event.preventDefault();
                                        return false;
                                    })

                                }


                                if ($(currentField).val().length < iMaxLength) {

                                    $(phoneParent).find('.has-success').removeClass('has-success').addClass('has_order_error');
                                    $(phoneParent).find('.has_tooltip').addClass('has_error');
                                    $('#ORDER_CONFIRM_BUTTON').prop('haserror', true);

                                    if ($('#ORDER_CONFIRM_BUTTON').prop('disabled') == false) {
                                        $('#ORDER_CONFIRM_BUTTON').prop('disabled', true);
                                    }
                                }

                                if ($(currentField).val().length > (iMaxLength - 1)) {

                                    $(phoneParent).find('.has_order_error').removeClass('has_order_error').addClass('has-success');
                                    $(phoneParent).find('.has_tooltip').removeClass('has_error');
                                    $('#ORDER_CONFIRM_BUTTON').prop('haserror', false);

                                    if ($('#ORDER_CONFIRM_BUTTON').prop('disabled') != false) {
                                        $('#ORDER_CONFIRM_BUTTON').prop('disabled', false);
                                        console.log('called 3');
                                    }
                                }

                            }

                        };

                        $(phoneElt).mask(phoneMask, maskOptions);

                        break;
                    default:

                        $(phoneParent).find('.has_order_error').removeClass('has_order_error').addClass('has-success');
                        $(phoneParent).find('.has_tooltip').removeClass('has_error');
                        $('#ORDER_CONFIRM_BUTTON').prop('haserror', false);

                        if ($('#ORDER_CONFIRM_BUTTON').prop('disabled') != false) {
                            $('#ORDER_CONFIRM_BUTTON').prop('disabled', false);
                            console.log('called 4');
                        }

                        $(phoneElt).mask(phoneMask);
                        break;
                }


            };

        };

    };


};

function destroyLocPopover() {
    if (locPopoverShow) {
        try {
            $('#ORDER_FORM input.bx-ui-sls-fake').popover('destroy');
            $('.popover.in').remove();
        } catch (e) {

        };

        locPopoverShow = false;
    };
};

function createLocPopover() {

    var popoverContent = '<div class="message"><p>Это ваш город?</p><div class="buttons text-center"><button class="yes btn-info btn" onclick="itsMyTown(true);">Да</button><button class="no btn btn-danger" onclick="itsMyTown(false);">Нет</button></div></div></div>';

    if (!locPopoverShow
        && $.trim($('#ORDER_FORM input.bx-ui-sls-fake').val()) != "") {

        try {
            locPopoverShow = true;

            $('#ORDER_FORM input.bx-ui-sls-fake').popover({
                content: popoverContent,
                html: true,
                placement: "bottom",
                title: "Выберите город",
                trigger: "manual",
                container: "body"
            });

            $('#ORDER_FORM input.bx-ui-sls-fake').popover('show');
        } catch (e) {

        }

    };
    return false;
};


function afterFormReload() {

    if ($('#changeToLegal').get(0)) {
        if (!$('#PERSON_TYPE_2:checked').get(0)) {
            $('#PERSON_TYPE_2').click();
        }
    }

    if ($('.YD_PVZ select').get(0)) {
        $('.YD_PVZ select').off('change', getSelectYaPVZ);
        $('.YD_PVZ select').on('change', getSelectYaPVZ);
    }

    if ($('body').hasClass('is_authorized')) {
        $.cookie('isCloseHidden', false);
    }

    if ($('#passcodeModal').get(0)
        && !$.cookie('isCloseHidden')) {

        $('.EMAIL input[type="text"]').bind('blur', function () {

            if ($(this).val() != ""
                && $(this).val().indexOf('@') !== -1) {

                var __self = this;

                jQuery.post(
                    location.protocol + '//' + location.hostname + '/include/onetimepassword.php',
                    '&pass_user=' + $(this).val() + '&pass_action=checkemail',
                    function (data) {

                        if (data
                            && data.emailFound) {

                            $('#passcodeModal').modal();
                            $('input[name="pass_user"]').val($(__self).val());

                        }
                    },
                    'json'
                );

            }

        });

        $('#passcodeModal .close').bind('click', function () {

            $.cookie('isCloseHidden', true);

        })



    }

    remapPhoneMask();

    try {
        locPopoverShow = true;
        clearInterval(locInterval);
        //$('#ORDER_FORM input.bx-ui-sls-fake').blur();
        $('#ORDER_FORM input.bx-ui-sls-fake').popover('destroy');
        $('.popover.in').remove();

    } catch (e) {

    }

    locInterval = setInterval(function () {

        if ($('#ORDER_FORM input.bx-ui-sls-fake').get(0)) {

            clearInterval(locInterval);

            try {
                $('#ORDER_FORM input.bx-ui-sls-fake').popover('destroy');
                $('.popover.in').remove();
            } catch (e) {

            };

            var locationSelectedConfirmed = $.cookie('locationSelected');
            locationSelectedConfirmed = $('html').hasClass('mobile') ? 'true' : locationSelectedConfirmed;

            if (locationSelectedConfirmed != 'true') {

                locPopoverShow = false;

                $('#ORDER_FORM input.bx-ui-sls-fake').on("focus", destroyLocPopover);

                //$('#ORDER_FORM input.bx-ui-sls-fake').on("blur", createLocPopover);

                if (!locPopoverShow) {

                    createLocPopover();

                };

            } else {
                if ($('#ORDER_CONFIRM_BUTTON').prop('haserror') != true) {
                    $('#ORDER_CONFIRM_BUTTON').prop('disabled', false);
                    console.log('called 1');
                }
            };
        };

    }, 900);

    if (!$('.LOCATION.order-properties').hasClass('has-success')) {

        $('.order-properties').each(function () {
            if (!$(this).hasClass('LOCATION')) {
                $(this).addClass('hidden');
            };
        });

        //$('#ORDER_CONFIRM_BUTTON').attr('disabled',true);

    } else {

        $('.order-properties').each(function () {
            $(this).removeClass('hidden');
        });

        if (!$('.bx_block.has-success', $('.delivery.order-properties')).get(0)) {

            $('.order-properties').each(function () {
                if (!$(this).hasClass('LOCATION') && !$(this).hasClass('delivery')) {
                    $(this).addClass('hidden');
                };
            });

        } else {

            $('.order-properties').each(function () {
                $(this).removeClass('hidden');
            });

        };

        //$('#ORDER_CONFIRM_BUTTON').attr('disabled',false);

    };

    /* BX.bind(BX('SMS_INFO'), 'click', function(){
    
        jsAjaxUtil.PostData(location.protocol + '//' + location.hostname + '/ajax_cart/change_user_state.php', {SMS_INFO: (this.checked ? 1 : 0)},
            function(data)
            {
            }
        );
    }); */

    if (hideOptionsDelivery.length) {

        for (var i = 0; i < hideOptionsDelivery.length; i++) {

            var cOptions = hideOptionsDelivery[i].split('.');

            if (cOptions && cOptions.length)
                for (var c = 0; c < cOptions.length; c++) {
                    if (cOptions[c] != "" && cOptions[c].indexOf("ORDER_PROP") === 0) {
                        $('input[type="text"],textarea', '.' + cOptions[c]).each(function () {
                            if ($.trim($(this).val()) == "По умолчанию") {
                                $(this).val("");
                            };
                        });
                    };
                };

        };

        for (var i = 0; i < hideOptionsDelivery.length; i++) {

            $('select', hideOptionsDelivery[i]).each(function () {
                if (this.selectedIndex === -1) {
                    this.selectedIndex = 0;
                }
            });


            $('input[type="text"],textarea', hideOptionsDelivery[i]).each(function () {

                if ($.trim($(this).val()) == "") {
                    $(this).val("По умолчанию");
                };


            });


        }


    }

    /* if($("#ORDER_PROP_4").get(0) && $("#ORDER_PROP_4")[0].value == "По умолчанию"){
        $("#ORDER_PROP_4")[0].value = "";
    };
    
    if($("#ORDER_PROP_20").get(0) && $("#ORDER_PROP_20")[0].value == "По умолчанию"){
        $("#ORDER_PROP_20")[0].value = "";
    };
    
    $("#ORDER_FORM .delivery_sdek_samovyvoz .ORDER_PROP_4,#ORDER_FORM .delivery_dostavka_kurerom_po_moskve .ORDER_PROP_4,#ORDER_FORM .delivery_samovyvoz .ORDER_PROP_4").each(function(){
        if($(this).css("display") == "none" && $("#ORDER_PROP_4").get(0) && $("#ORDER_PROP_4")[0].value == ""){
            $("#ORDER_PROP_4")[0].value = "По умолчанию";
        };
    });
    
    $("#ORDER_FORM .delivery_samovyvoz .ORDER_PROP_20,#ORDER_FORM .delivery_sdek_samovyvoz .ORDER_PROP_20").each(function(){
        if($(this).css("display") == "none" && $("#ORDER_PROP_20").get(0) && $("#ORDER_PROP_20")[0].value == ""){
            $("#ORDER_PROP_20")[0].value = "По умолчанию";
        };
    }); */

    if ($('.order_quantity').get(0)) {
        $('.order_quantity').each(function () {
            if (!$(this).attr('number')) {
                $(this).attr('number', true);
                $(this).bootstrapNumber();
            };
        });
    };

    if ($('.DAYOFDELIVERY select').get(0)) {
        $('.DAYOFDELIVERY select').on('change', function () {

            var sIndex = this.selectedIndex;

            if (sIndex > 0) {

                var atClasses = this.options[sIndex].className.split(',');

                $('.TIMEOFDELIVERY select').find('option').each(function () {

                    this.className = this.className.replace('hidden', '');
                    this.selected = false;
                    this.seletedIndex = 0;

                    this.className = $.inArray(this.value, atClasses) == -1 && this.value != "" ? 'hidden' : '';

                });

                try {
                    $('.TIMEOFDELIVERY select').selectpicker('refresh');
                } catch (e) {

                };
            };

        });

        $('.DAYOFDELIVERY select').trigger('change');

    };


    if ($('#sdekHelp').get(0)) {

        $('#sdekHelp').prev('select').on('change', function () {

            var cDataValue = this.options[this.selectedIndex].getAttribute('datavalue');

            var sHost = location.protocol + '//' + location.hostname + '/ajax_cart/ajax_sdek_help.php?id=' + cDataValue;

            $.get(sHost, function (sDataHtml) {
                $('#sdekHelp').html(sDataHtml['message']);

                if (cDataValue == '') {
                    $('#sdekHelp').addClass('hidden');
                } else {
                    $('#sdekHelp').removeClass('hidden');
                };

                $('#SDEK_HELP').val(sDataHtml['address']);

            });

        });

        if ($('.delivery_sdek_samovyvoz.PVZ_CDEK textarea').val()) {
            var pvzCDEK = $('.delivery_sdek_samovyvoz.PVZ_CDEK textarea').val();
            if (pvzCDEK.indexOf('#') !== -1) {
                var pvzCDEKValue = $.trim($('.delivery_sdek_samovyvoz.PVZ_CDEK textarea').val());
                pvzCDEKValueCode = $.trim(pvzCDEKValue.replace(/.*?#S/i, ''));
                $('#sdekHelp').prev('select').each(function () {

                    var cSelect = this;

                    $(cSelect.options).each(function () {
                        if (this.getAttribute('datacode') == pvzCDEKValueCode) {
                            this.selected = true;
                        } else {
                            this.selected = false;
                        }
                    });

                    $(cSelect).trigger('change');

                });
            }
        }

    };

    if ($('#boxberryHelp').get(0)) {

        $('#boxberryHelp').prev('select').on('change', function () {

            var cDataValue = this.options[this.selectedIndex].getAttribute('datavalue');
            var sHost = location.protocol + '//' + location.hostname + '/ajax_cart/ajax_boxberry_help.php?id=' + cDataValue;

            $.get(sHost, function (sDataHtml) {
                $('#boxberryHelp').html(sDataHtml['message']);

                if (cDataValue == '') {
                    $('#boxberryHelp').addClass('hidden');
                } else {
                    $('#boxberryHelp').removeClass('hidden');
                };

                $('#BOXBERRY_HELP').val(sDataHtml['address']);

            });

        });

        if ($('.delivery_boxberry_samovyvoz.HOUSE input[type=text]').val()) {
            var pvzBoxberry = $('.delivery_boxberry_samovyvoz.HOUSE input[type=text]').val();
            if (pvzBoxberry.indexOf('#') !== -1) {
                var pvzBoxberryValue = $.trim($('.delivery_boxberry_samovyvoz.HOUSE input[type=text]').val());
                pvzBoxberryValueCode = $.trim(pvzBoxberryValue.replace(/.*?#/i, ''));
                $('#boxberryHelp').prev('select').each(function () {

                    var cSelect = this;

                    $(cSelect.options).each(function () {
                        if (this.getAttribute('datacode') == pvzBoxberryValueCode) {
                            this.selected = true;
                        } else {
                            this.selected = false;
                        }
                    });

                    $(cSelect).trigger('change');

                });
            }
        }

    };

    if ($('#YANDEXHELP').get(0)) {

        $('#YANDEXHELP').prev('select').on('change', function () {

            var cDataValue = this.options[this.selectedIndex].getAttribute('datavalue');
            var sHost = location.protocol + '//' + location.hostname + '/ajax_cart/ajax_yandex_help.php?id=' + cDataValue;

            $.get(sHost, function (sDataHtml) {
                $('#YANDEXHELP').html(sDataHtml['message']);

                if (cDataValue == '') {
                    $('#YANDEXHELP').addClass('hidden');
                } else {
                    $('#YANDEXHELP').removeClass('hidden');
                };

                $('#YANDEX_HELP').val(sDataHtml['message']);

            });

        });

        $('#YANDEXHELP').prev('select').trigger('change');

    };

    try {
        $('.selectpicker').selectpicker();
    } catch (e) {

    }

    $('td.actions .remove').bind("click", removeFromCart);
    $('#recount').bind("click", doRecount);

    if ($('.has_order_error').get(0)) {
        $('.has_order_error').each(function () {
            var errorMsg = $(this).attr('data:error');
            var errorElt = this;

            $('.has_tooltip', this).each(function () {
                if (errorMsg && !$(this).attr('disabled')) {
                    try {
                        $(this).tooltip({ title: errorMsg, placement: 'bottom', html: true });
                        $(this).tooltip('show');
                        $(this).addClass('has_error');
                    } catch (e) {

                    }

                    $(this).bind('click', function () {
                        try {
                            $(this).removeClass('has_error');
                            $(errorElt).removeClass('has_order_error');
                            $(this).tooltip('destroy');
                        } catch (e) {

                        }
                    });

                };

            })


        });
    };

};

function removeFromCart(event) {

    //ShowWaitWindow();

    try {
        BX.showWait();
    } catch (e) {

    }

    BX.ajax({

        url: this.href,
        method: 'get',
        dataType: 'json',
        async: true,
        processData: true,
        emulateOnload: true,
        start: true,
        onsuccess: function (result) {

            //CloseWaitWindow();
            BX.closeWait();

            if (result.deleted && result.deleted > 0) {
                submitForm();
            } else {
                $("#empty-cart").show();
                $("#order_form_div").hide();
                top.BX.scrollToNode(top.BX('empty-cart'));
            }

        },
        onfailure: function (type, e) {

            //CloseWaitWindow();
            BX.closeWait();
            // on error do nothing
        }

    });

    event.preventDefault();
    return false;
};


BX.addCustomEvent('onAjaxSuccess', afterFormReload);
$(afterFormReload);

function doRecount() {

    var product_id = [];

    $('input[type="text"].order_quantity').each(function () {
        product_id[$(this).attr('data:id')] = this.value;
    });

    var couponValue = $('#coupon').val();

    //ShowWaitWindow();
    try {
        BX.showWait();
    } catch (e) {

    }

    BX.ajax({

        url: '/bitrix/templates/.default/components/bitrix/sale.order.ajax/main_test/sale_order_ajax.php',
        method: 'post',
        dataType: 'json',
        async: true,
        processData: true,
        emulateOnload: true,
        start: true,
        data: { 'action': 'update', 'product_id': product_id, 'coupon': couponValue },
        onsuccess: function (result) {

            if (result && result.msgTxt) {

                if (!result.success) {
                    $('#coupon').val("");
                }

                $('#coupon-result').html(result.msgTxt);
            }

            //CloseWaitWindow();
            BX.closeWait();

            if (result.updated) {
                submitForm();
            }

        },
        onfailure: function (type, e) {

            //CloseWaitWindow();
            BX.closeWait();

            // on error do nothing
        }

    });

}

async function getYDSelectActioins(formData) {

    const response = await fetch('/bitrix/tools/twinpx.yadelivery/ajax.php', {
        method: 'POST',
        body: formData,
        props: {}
    });

    const answer = await response.json();
    return answer;
}


function getSelectOffers(event) {

    console.log('getSelectOffers');

    if (($('.YD_DAYS .form-control').get(0)
        || $('.YD_TIME .form-control').get(0))
        && typeof event != "undefined") {

        $('.row.YD_DAYS').removeClass('hidden-address');
        $('.row.YD_TIME').removeClass('hidden-address');

        if ($('.YD_DAYS .form-control').get(0)) {

            var yddays = $('.YD_DAYS .form-control').get(0);
            var yddaystext = $('.YD_DAYS .form-control').val();
            let name = $('.YD_DAYS .form-control').prop('name');

            var selected = "";

            if (yddaystext != "") {
                selected = '<option selected="selected" value="' + yddaystext + '">' + yddaystext + '</option>';
            }

            var dayOptionsDefault = '<option value="">Выберите дату</option>';

            if (!$('#yddays').get(0)) {

                var dayOptions = '<select class="form-control" name="' + name + '" id="yddays">' + dayOptionsDefault + selected + '</select>';
                $(dayOptions).insertAfter(yddays);
                $(yddays).remove();
                try {
                    $('#yddays').selectpicker();
                } catch (e) {

                }
            } else {

                $('#yddays').html(dayOptionsDefault + selected);
                try {
                    $('#yddays').selectpicker('refresh');
                } catch (e) {

                }
            }

            $('#yddays').attr('disabled', true).prop('disabled', true);

        }

        if ($('.YD_TIME .form-control').get(0)) {

            var ydtime = $('.YD_TIME .form-control').get(0);
            var ydtimetext = $('.YD_TIME .form-control').val();
            let name = $('.YD_TIME .form-control').prop('name');

            var selected = "";

            if (ydtimetext != "") {
                selected = '<option selected="selected" value="' + ydtimetext + '">' + ydtimetext + '</option>';
            }

            var timeOptionsDefault = '<option value="">Выберите время</option>';

            if (!$('#ydtime').get(0)) {

                var timeOptions = '<select class="form-control" name="' + name + '" id="ydtime">' + timeOptionsDefault + selected + '</select>';
                $(timeOptions).insertAfter(ydtime);
                $(ydtime).remove();
                $('#ydtime').attr('disabled', true).prop('disabled', true);

                try {
                    $('#ydtime').selectpicker();
                } catch (e) {

                }
            } else {

                $('#ydtime').html(timeOptionsDefault + selected);
                $('#ydtime').attr('disabled', true).prop('disabled', true);

                try {
                    $('#ydtime').selectpicker('refresh');
                } catch (e) {

                }
            }


        }

        if ($('#ydtime').get(0) || $('#yddays').get(0)) {

            let formData = new FormData(),
                fields = $('#ORDER_FORM').serialize();

            formData.set('action', 'getOffer');
            formData.set('fields', fields);

            if (!ydtimetext || !yddaystext) {
                $('#ORDER_CONFIRM_BUTTON').prop('disabled', true);
            }

            console.log('before 1');

            getYDSelectActioins(formData).then(result => {

                showYDFieldsErrors(result, 'getSelectOffers');
                console.log('after 1');

                if (result
                    && result.OFFERS
                    && !result.ERRORS
                    && result.STATUS == 'Y') {

                    let dates = [];

                    for (let offer in result.OFFERS) {

                        let currentOffer = result.OFFERS[offer];

                        if (typeof dates[currentOffer.date] == 'undefined') {
                            dates[currentOffer.date] = [];
                        }

                        if ($.inArray(currentOffer.time, dates[currentOffer.date]) === -1) {
                            dates[currentOffer.date][currentOffer.time] = currentOffer;
                        }

                    }

                    let dayOptions = '';
                    let timeOptions = '';

                    for (let date in dates) {

                        dayOptions += '<option value="' + date + '" ' + (yddaystext == date ? ' selected="selected" ' : '') + '>' + date + '</option>';

                        for (let time in dates[date]) {

                            timeOptions += '<option value="' + time + '" ' + (ydtimetext == time ? ' selected="selected" ' : '') + ' data-json=\'' + (dates[date][time].json) + '\' data-date="' + date + '">' + time + '</option>';

                        }

                    }

                    if (timeOptions != "" && ydtime) {

                        if ($('#yddays').get(0)) {

                            $('#yddays').html(dayOptionsDefault + dayOptions);
                            $('#ydtime').html(timeOptionsDefault + timeOptions);

                            $('#yddays').attr('disabled', false).prop('disabled', false);
                            $('#ydtime').attr('disabled', false).prop('disabled', false);

                            if (yddaystext) {
                                $('#ydtime option:not([data-date="' + yddaystext + '"])').addClass('hidden');
                            } else {
                                $('#ydtime option').addClass('hidden');
                            }

                            $('#ydtime option').first().removeClass('hidden');

                            $('#ydtime').on('change', function () {
                                let cVal = $(this).val();
                                if (cVal) {
                                    let cOption = this.options[this.selectedIndex];
                                    let jsOrder = $(cOption).attr('data-json');
                                    if (jsOrder) {
                                        sendOffer(jsOrder);
                                        submitForm();
                                    };
                                };

                            });

                            $('#yddays').on('change', function () {

                                if ($('#ydtime').get(0)) {

                                    let cVal = $(this).val();

                                    $('#ydtime option:selected').eq(0).prop('selected', false).attr('selected', false);
                                    $('#ydtime option').addClass('hidden');

                                    if (cVal) {
                                        $('#ydtime option[data-date="' + cVal + '"]').removeClass('hidden');
                                    }

                                    $('#ydtime option')[0].selected = true;
                                    $('#ydtime option').first().removeClass('hidden');


                                    $('#ydtime').selectpicker('refresh');

                                }

                            });

                            try {
                                $('#ydtime').selectpicker('refresh');
                                $('#yddays').selectpicker('refresh');
                            } catch (e) {

                            }

                            setTimeout(function () {
                                try {
                                    $('#ydtime').next('ul').find('a.hidden').each(function () {
                                        $(this).closest('li').remove();
                                    });
                                } catch (e) {

                                }

                            }, 20);

                        }

                        try {
                            $('#yddays').selectpicker('refresh');
                            $('#ydtime').selectpicker('refresh');
                        } catch (e) {

                        }

                        setTimeout(function () {
                            try {
                                $('#ydtime').next('.bootstrap-select').find('a.hidden').each(function () {
                                    $(this).closest('li').remove();
                                });
                            } catch (e) {

                            }
                        }, 20);

                    }

                }

            });

        }

    }

}

function getSelectYaPVZ(event) {

    if ($('.YD_PVZ').get(0)
        && typeof event != "undefined") {

        if (!($('.YD_PVZ').get(0) && $('.YD_PVZ .form-control').get(0))) {
            $('.row.YD_PVZ').addClass('hidden-address');
            $('.row.YD_DAYS').addClass('hidden-address');
        } else {
            $('.row.YD_PVZ').removeClass('hidden-address');
            $('.row.YD_DAYS').removeClass('hidden-address');
        }

    }

    if ($('.YD_PVZ').get(0)
        && $('.YD_PVZ .form-control').get(0)
        && $('.YD_DAYS').get(0)
        && typeof event != "undefined"
    ) {

        $('.row.YD_DAYS').removeClass('hidden-address');
        var timeOptionsDefault = '<option value="">Выберите дату</option>';
        var ydDays = $('.YD_DAYS .form-control').eq(0);

        if (ydDays.get(0)) {

            var ydtime = ydDays.get(0);
            var ydtimetext = ydDays.val();
            let name = ydDays.prop('name');
            let selected = "";

            if (ydtimetext != "") {
                selected += '<option selected="selected" value="' + ydtimetext + '">' + ydtimetext + '</option>';
            }

            if (!$("#ydtime").get(0)) {
                var timeOptions = '<select class="form-control" name="' + name + '" id="ydtime">' + timeOptionsDefault + selected + '</select>';
                $(timeOptions).insertAfter(ydtime);
                $(ydtime).remove();
                $('#ydtime').attr('disabled', true).prop('disabled', true);

                try {
                    $('#ydtime').selectpicker();
                } catch (e) {

                }
            } else {

                $('#ydtime').html(timeOptionsDefault + selected);
                $('#ydtime').attr('disabled', true).prop('disabled', true);

                try {
                    $('#ydtime').selectpicker('refresh');
                } catch (e) {

                }
            }


        }

        var sLat = $('.YD_PVZ select option:selected').data('lat');
        var sLong = $('.YD_PVZ select option:selected').data('long');
        var sPoint = $('.YD_PVZ select option:selected').data('point');

        if (!ydtimetext || !$('.YD_PVZ select').val()) {
            $('#ORDER_CONFIRM_BUTTON').prop('disabled', true);
            setTimeout(function () {
                $('#ORDER_CONFIRM_BUTTON').prop('disabled', true);
            }, 100);
        }

        if (sLat && sLong && sPoint) {

            let formData = new FormData(),
                fields = $('#ORDER_FORM').serialize();

            formData.set('action', 'getRegion');
            formData.set('fields', fields);

            console.log('before 2');
            getYDSelectActioins(formData).then(result => {

                showYDFieldsErrors(result, 'getSelectYaPVZ');
                console.log('after 2');

                if (result
                    && result.STATUS == 'Y'
                    && result.PAYMENT
                    && !result.ERRORS
                ) {
                    let formDataPoints = new FormData(),
                        fields = 'lat-from=' + sLat + '&lat-to=' + sLat + '&lon-from=' + sLong + '&lon-to=' + sLong + '&payment=' + result.PAYMENT;

                    formDataPoints.set('action', 'getPoints');
                    formDataPoints.set('fields', fields);

                    console.log('before 3');

                    getYDSelectActioins(formDataPoints).then(resultPoints => {

                        showYDFieldsErrors(resultPoints, 'getSelectYaPVZ');
                        console.log('after 3');

                        if (resultPoints
                            && resultPoints.STATUS == 'Y'
                            && !resultPoints.ERRORS
                            && resultPoints.POINTS
                        ) {

                            for (let ip = 0; ip < resultPoints.POINTS.length; ip++) {

                                if (resultPoints.POINTS[ip].id == sPoint) {

                                    let formDataPvz = new FormData(),
                                        fieldsPvz = $('#ORDER_FORM').serialize();
                                    fieldsPvz += '&is_ajax_post=Y&json=Y&id=' + resultPoints.POINTS[ip].id + '&address=' + encodeURIComponent(resultPoints.POINTS[ip].address) + '&title=' + encodeURIComponent(resultPoints.POINTS[ip].title);
                                    formDataPvz.set('action', 'pvzOffer');
                                    formDataPvz.set('fields', fieldsPvz);

                                    console.log('before 4');

                                    getYDSelectActioins(formDataPvz).then(resultPVZ => {

                                        showYDFieldsErrors(resultPVZ, 'getSelectYaPVZ');
                                        console.log('after 4');

                                        if (resultPVZ
                                            && resultPVZ.STATUS == 'Y'
                                            && !resultPVZ.ERRORS
                                            && resultPVZ.OFFERS
                                        ) {

                                            let timeOptions = '';

                                            for (let io = 0; io < resultPVZ.OFFERS.length; io++) {
                                                timeOptions += '<option ' + (ydtimetext == resultPVZ.OFFERS[io].date ? ' selected="selected"' : '') + ' value="' + resultPVZ.OFFERS[io].date + '" data-json=\'' + resultPVZ.OFFERS[io].json + '\'>' + resultPVZ.OFFERS[io].date + '</option>';
                                                //json.offer_expire
                                            }

                                            $('#ydtime').html(timeOptionsDefault + timeOptions);
                                            $('#ydtime').attr('disabled', false).prop('disabled', false);

                                            try {
                                                $('#ydtime').selectpicker('refresh');
                                            } catch (e) {

                                            }

                                            try {
                                                $('#ydtime').off('change', selectYDTimeSetOffer);
                                            } catch (e) {

                                            }

                                            $('#ydtime').on('change', selectYDTimeSetOffer);

                                        }

                                    });

                                }
                            }

                        }

                    });

                }

            });

        }

    }
}

function selectYDTimeSetOffer() {

    let val = $(this).val();

    if (val) {

        let jsonData = $('#ydtime option:selected').data('json');

        jsonData = JSON.stringify(jsonData);

        let formDataSetPrice = new FormData();
        formDataSetPrice.set('action', 'setOfferPrice');
        formDataSetPrice.set('fields', jsonData);

        console.log('before 5');

        getYDSelectActioins(formDataSetPrice).then(resultSetPrice => {

            showYDFieldsErrors(resultSetPrice, 'getSelectYaPVZ');
            console.log('after 5');

            if (resultSetPrice
                && resultSetPrice.STATUS == 'Y'
                && !resultSetPrice.ERRORS
            ) {
				//console.log(resultSetPrice);
                setTimeout(function(){
					submitForm();
				},500);
            }

        });

    }

}

function isEmptyField() {
    let fElt = $(this);
    if (fElt.get(0)) {
        if (fElt.val() == '') {
            fElt.removeClass('has-success');
            fElt.addClass('has_error');
        } else {
            fElt.removeClass('has_error');
        }
    }
}

function showYDFieldsErrors(result, execFunction) {

    if ($('.YD_DAYS div.form-control').get(0)) {

        try {
            if ($('.YD_DAYS div.form-control + .tooltip').get(0)) {
                $('.YD_DAYS div.form-control').tooltip('hide');
            }

            $('.YD_DAYS div.form-control').removeClass('has_error');

        } catch (e) {

        }

    }

    if (result && result.FIELDS) {

        for (let fi in result.FIELDS) {

            if (fi && fi != 'PropAddress' && fi != 'PropComment' && result.FIELDS[fi]) {

                let fElt = $('[name=' + result.FIELDS[fi] + ']');

                if (fElt.get(0)) {

                    let fVal = fElt.val();

                    switch (execFunction) {
                        case 'getSelectOffers':
                            fElt.off('change', getSelectOffers);
                            break;
                        default:
                            fElt.off('change', getSelectYaPVZ);
                            break;
                    }

                    fElt.off('change', isEmptyField);
                    fElt.on('change', isEmptyField);

                    fElt.removeClass('has_error');

                    if (!fVal) {

                        fElt.removeClass('has-success');
                        fElt.addClass('has_error');

                        switch (execFunction) {
                            case 'getSelectOffers':
                                fElt.on('change', getSelectOffers);
                                break;
                            default:
                                fElt.on('change', getSelectYaPVZ);
                                break;
                        }

                    }

                }

            }

        }
    }

    if (result
        && result.ERRORS) {

        if ($('.YD_DAYS div.form-control').get(0)) {

            try {

                if (!$('.YD_DAYS div.form-control + .tooltip').get(0)) {

                    $('.YD_DAYS div.form-control').tooltip({
                        title: result.ERRORS,
                        placement: 'bottom',
                        trigger: "manual"
                    });

                }

                $('.YD_DAYS div.form-control').tooltip('show');


            } catch (e) {

            }

            $('.YD_DAYS div.form-control').addClass('has_error');

            $('.YD_DAYS div.form-control').bind('click', function (event) {

                if (event) {

                    try {
                        if ($('.YD_DAYS div.form-control + .tooltip').get(0)) {
                            $('.YD_DAYS div.form-control').tooltip('hide');
                        }
                    } catch (e) {

                    }

                    $('.YD_DAYS div.form-control').removeClass('has_error');

                }
            });

        }

    }

}


function itsMyTown(bMyTown) {

    if (bMyTown) {

        try {
            $.cookie("locationSelected", true);
            $("#ORDER_FORM input.bx-ui-sls-fake").popover("hide");
            $("#ORDER_CONFIRM_BUTTON").prop("disabled", false);
        } catch (e) {

        }

    } else {

        try {

            $(".bx-ui-sls-clear").trigger("click");

            setTimeout(function () {
                $(".bx-ui-sls-fake").trigger("focus");
            }, 300);

            $.cookie("locationSelected", null);
            $("#ORDER_CONFIRM_BUTTON").prop("disabled", "true");

            $("#ORDER_FORM input.bx-ui-sls-fake").popover("hide");
        } catch (e) {

        }

    }

}