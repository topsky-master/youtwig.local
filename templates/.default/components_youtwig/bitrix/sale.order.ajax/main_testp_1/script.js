BX.saleOrderAjax = {

    BXCallAllowed: false,

    options: {},
    indexCache: {},
    controls: {},

    modes: {},
    properties: {},

    // called once, on component load
    init: function(options)
    {
        var ctx = this;
        this.options = options;

        window.submitFormProxy = BX.proxy(function(){
            ctx.submitFormProxy.apply(ctx, arguments);
        }, this);

        BX(function(){
            ctx.initDeferredControl();
        });
        BX(function(){
            ctx.BXCallAllowed = true; // unlock form refresher
        });

        this.controls.scope = BX('order_form_div');

        // user presses "add location" when he cannot find location in popup mode
        BX.bindDelegate(this.controls.scope, 'click', {className: '-bx-popup-set-mode-add-loc'}, function(){

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

    cleanUp: function(){

        for(var k in this.properties){
            if(typeof this.properties[k].input != 'undefined'){
                BX.unbindAll(this.properties[k].input);
                this.properties[k].input = null;
            }

            if(typeof this.properties[k].control != 'undefined'){
                BX.unbindAll(this.properties[k].control);
            }
        }

        this.properties = {};
    },

    addPropertyDesc: function(desc){
        this.properties[desc.id] = desc.attributes;
        this.properties[desc.id].id = desc.id;
    },

    // called each time form refreshes
    initDeferredControl: function()
    {
        var ctx = this;

        // first, init all controls
        if(typeof window.BX.locationsDeferred != 'undefined'){

            this.BXCallAllowed = false;

            for(var k in window.BX.locationsDeferred){

                window.BX.locationsDeferred[k].call(this);
                window.BX.locationsDeferred[k] = null;
                delete(window.BX.locationsDeferred[k]);

                if(typeof window.BX.locationSelectors[k] != "undefined"
                    && typeof this.properties[k] != "undefined"){
                    this.properties[k].control = window.BX.locationSelectors[k];
                    delete(window.BX.locationSelectors[k]);
                }
            }
        }

        for(var k in this.properties){

            // zip input handling
            if(this.properties[k].isZip){
                var row = this.controls.scope.querySelector('[data-property-id-row="'+k+'"]');
                if(BX.type.isElementNode(row)){

                    var input = row.querySelector('input[type="text"]');
                    if(BX.type.isElementNode(input)){
                        this.properties[k].input = input;

                        // set value for the first "location" property met
                        var locPropId = false;
                        for(var m in this.properties){
                            if(this.properties[m].type == 'LOCATION'){
                                locPropId = m;
                                break;
                            }
                        }

                        if(locPropId !== false){
                            BX.bindDebouncedChange(input, function(value){

                                input = null;
                                row = null;

                                if(/^\s*\d{6}\s*$/.test(value)){

                                    ctx.getLocationByZip(value, function(locationId){
                                        ctx.properties[locPropId].control.setValueById(locationId);
                                    }, function(){
                                        try{
                                            ctx.properties[locPropId].control.clearSelected(locationId);
                                        }catch(e){}
                                    });


                                }
                            });
                        }
                    }
                }
            }

            if(this.checkAbility(k, 'canHaveAltLocation')){

                //this.checkMode(k, 'altLocationChoosen');

                var control = this.properties[k].control;

                // control can have "select other location" option
                control.setOption('pseudoValues', ['other']);

                // when control tries to search for items
                control.bindEvent('before-control-item-discover-done', function(knownItems, adapter){

                    control = null;

                    var parentValue = adapter.getParentValue();

                    // you can choose "other" location only if parentNode is not root and is selectable
                    if(parentValue == this.getOption('rootNodeValue') || !this.checkCanSelectItem(parentValue))
                        return;

                    knownItems.unshift({DISPLAY: ctx.options.messages.otherLocation, VALUE: 'other', CODE: 'other', IS_PARENT: false});
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
                if(this.checkMode(k, 'altLocationChoosen')){

                    var altLocProp = this.getAltLocPropByRealLocProp(k);
                    this.toggleProperty(altLocProp.id, true);

                    var adapter = control.getAdapterAtPosition(control.getStackSize() - 1);

                    // also restore "other location" label on the last control
                    if(typeof adapter != 'undefined' && adapter !== null)
                        adapter.setValuePair('other', ctx.options.messages.otherLocation); // a little hack
                }else{

                    var altLocProp = this.getAltLocPropByRealLocProp(k);
                    this.toggleProperty(altLocProp.id, false);

                }
            }else{

                var altLocProp = this.getAltLocPropByRealLocProp(k);
                if(altLocProp && altLocProp !== false){

                    // replace default boring "nothing found" label for popup with "-bx-popup-set-mode-add-loc" inside
                    if(this.properties[k].type == 'LOCATION' && typeof this.properties[k].control != 'undefined' && this.properties[k].control.getSysCode() == 'sls')
                        this.properties[k].control.replaceTemplate('nothing-found', this.options.messages.notFoundPrompt);

                    this.toggleProperty(altLocProp.id, false);
                }
            }

            if(typeof this.properties[k].control != 'undefined' && this.properties[k].control.getSysCode() == 'slst'){

                var control = this.properties[k].control;

                // if a children of CITY is shown, we must replace label for 'not selected' variant
                var adapter = control.getAdapterAtPosition(control.getStackSize() - 1);
                var node = this.getPreviousAdapterSelectedNode(control, adapter);

                if(node !== false && node.TYPE_ID == ctx.options.cityTypeId){

                    var selectBox = adapter.getControl();
                    if(selectBox.getValue() == false){

                        adapter.getControl().replaceMessage('notSelected', ctx.options.messages.moreInfoLocation);
                        adapter.setValuePair('', ctx.options.messages.moreInfoLocation);
                    }
                }
            }

        }

        this.BXCallAllowed = true;
    },

    checkMode: function(propId, mode){

        //if(typeof this.modes[propId] == 'undefined')
        //	this.modes[propId] = {};

        //if(typeof this.modes[propId] != 'undefined' && this.modes[propId][mode])
        //	return true;

        if(mode == 'altLocationChoosen'){

            if(this.checkAbility(propId, 'canHaveAltLocation')){

                var input = this.getInputByPropId(this.properties[propId].altLocationPropId);
                var altPropId = this.properties[propId].altLocationPropId;

                if(input !== false && input.value.length > 0 && !input.disabled && this.properties[altPropId].valueSource != 'default'){

                    //this.modes[propId][mode] = true;
                    return true;
                }
            }
        }

        return false;
    },

    checkAbility: function(propId, ability){

        if(typeof this.properties[propId] == 'undefined')
            this.properties[propId] = {};

        if(typeof this.properties[propId].abilities == 'undefined')
            this.properties[propId].abilities = {};

        if(typeof this.properties[propId].abilities != 'undefined' && this.properties[propId].abilities[ability])
            return true;

        if(ability == 'canHaveAltLocation'){

            if(this.properties[propId].type == 'LOCATION'){

                // try to find corresponding alternate location prop
                if(typeof this.properties[propId].altLocationPropId != 'undefined' && typeof this.properties[this.properties[propId].altLocationPropId]){

                    var altLocPropId = this.properties[propId].altLocationPropId;

                    if(typeof this.properties[propId].control != 'undefined' && this.properties[propId].control.getSysCode() == 'slst'){

                        if(this.getInputByPropId(altLocPropId) !== false){
                            this.properties[propId].abilities[ability] = true;
                            return true;
                        }
                    }
                }
            }

        }

        return false;
    },

    getInputByPropId: function(propId){
        if(typeof this.properties[propId].input != 'undefined')
            return this.properties[propId].input;

        var row = this.getRowByPropId(propId);
        if(BX.type.isElementNode(row)){
            var input = row.querySelector('input[type="text"]');
            if(BX.type.isElementNode(input)){
                this.properties[propId].input = input;
                return input;
            }
        }

        return false;
    },

    getRowByPropId: function(propId){

        if(typeof this.properties[propId].row != 'undefined')
            return this.properties[propId].row;

        var row = this.controls.scope.querySelector('[data-property-id-row="'+propId+'"]');
        if(BX.type.isElementNode(row)){
            this.properties[propId].row = row;
            return row;
        }

        return false;
    },

    getAltLocPropByRealLocProp: function(propId){
        if(typeof this.properties[propId].altLocationPropId != 'undefined')
            return this.properties[this.properties[propId].altLocationPropId];

        return false;
    },

    toggleProperty: function(propId, way, dontModifyRow){

        var prop = this.properties[propId];

        if(typeof prop.row == 'undefined')
            prop.row = this.getRowByPropId(propId);

        if(typeof prop.input == 'undefined')
            prop.input = this.getInputByPropId(propId);

        if(!way){
            if(!dontModifyRow)
                BX.hide(prop.row);
            prop.input.disabled = true;
        }else{
            if(!dontModifyRow)
                BX.show(prop.row);
            prop.input.disabled = false;
        }
    },

    submitFormProxy: function(item, control)
    {
        var propId = false;
        for(var k in this.properties){
            if(typeof this.properties[k].control != 'undefined' && this.properties[k].control == control){
                propId = k;
                break;
            }
        }

        if(item != 'other'){

            if(this.BXCallAllowed){

                // drop mode "other"
                if(propId != false){
                    if(this.checkAbility(propId, 'canHaveAltLocation')){

                        if(typeof this.modes[propId] == 'undefined')
                            this.modes[propId] = {};

                        this.modes[propId]['altLocationChoosen'] = false;

                        var altLocProp = this.getAltLocPropByRealLocProp(propId);
                        if(altLocProp !== false){

                            this.toggleProperty(altLocProp.id, false);
                        }
                    }
                }

                this.BXCallAllowed = false;
                submitForm();
            }

        }else{ // only for sale.location.selector.steps

            if(this.checkAbility(propId, 'canHaveAltLocation')){

                var adapter = control.getAdapterAtPosition(control.getStackSize() - 2);
                if(adapter !== null){
                    var value = adapter.getValue();
                    control.setTargetInputValue(value);

                    // set mode "other"
                    if(typeof this.modes[propId] == 'undefined')
                        this.modes[propId] = {};

                    this.modes[propId]['altLocationChoosen'] = true;

                    var altLocProp = this.getAltLocPropByRealLocProp(propId);
                    if(altLocProp !== false){

                        this.toggleProperty(altLocProp.id, true, true);
                    }

                    this.BXCallAllowed = false;
                    submitForm();
                }
            }
        }
    },

    getPreviousAdapterSelectedNode: function(control, adapter){

        var index = adapter.getIndex();
        var prevAdapter = control.getAdapterAtPosition(index - 1);

        if(typeof prevAdapter !== 'undefined' && prevAdapter != null){
            var prevValue = prevAdapter.getControl().getValue();

            if(typeof prevValue != 'undefined'){
                var node = control.getNodeByValue(prevValue);

                if(typeof node != 'undefined')
                    return node;

                return false;
            }
        }

        return false;
    },
    getLocationByZip: function(value, successCallback, notFoundCallback)
    {
        if(typeof this.indexCache[value] != 'undefined')
        {
            successCallback.apply(this, [this.indexCache[value]]);
            return;
        }

        //ShowWaitWindow();
        BX.showWait();

        var ctx = this;

        BX.ajax({

            url: this.options.source,
            method: 'post',
            dataType: 'json',
            async: true,
            processData: true,
            emulateOnload: true,
            start: true,
            data: {'ACT': 'GET_LOC_BY_ZIP', 'ZIP': value},
            //cache: true,
            onsuccess: function(result){


                //try{

                //CloseWaitWindow();
                BX.closeWait();

                if(result.result){

                    ctx.indexCache[value] = result.data.ID;
                    successCallback.apply(ctx, [result.data.ID]);

                }else
                    notFoundCallback.call(ctx);

                //}catch(e){console.dir(e);}

            },
            onfailure: function(type, e){

                //CloseWaitWindow();
                BX.closeWait();
                // on error do nothing
            }

        });
    }

}
function getPlaceholder(country) {

    var placeholder = '';

    for(var nm = 0; nm < orderCountries.length; nm++){

        if(orderCountries[nm] == country){
            placeholder = orderCountriesPhonePlaceholder[nm];
            break;
        }

    }

    return placeholder;


};

function phoneMaskBehavior(country) {

    var mask = '';

    for(var nm = 0; nm < orderCountries.length; nm++){

        if(orderCountries[nm] == country){
            mask = orderCountriesPhoneMask[nm];
            break;
        }

    }

    return mask;

};

orderCountries = ['Россия','Казахстан','Украина','Беларусь'];
orderCountriesPhonePlaceholder = ['+79_________','+77_________','+380_________','+375_________'];
orderCountriesPhoneMask = ['+79000000000','+77000000000','+380000000000','+375000000000'];
checkmodelHidden = false;

function remapPhoneMask(){

    console.log("reinit");

    setTimeout(function(){
        if($('#orderError').get(0)) {
            $([document.documentElement, document.body]).animate({
                scrollTop: $('#orderError').offset().top
            }, 300);
        }
    },300);

    var phoneElt = $('#ORDER_PROP_3').get(0) ? $('#ORDER_PROP_3').get(0) : $('#ORDER_PROP_14').get(0);
    var phoneParent = $('.ORDER_PROP_3').get(0) ? $('.ORDER_PROP_3').get(0) : $('.ORDER_PROP_14').get(0);


    if($(phoneElt).get(0)){

        console.log("found");

        var idDefaultRussia = true;
        var countrySelected = $.trim($('#countryName').val());

        if(countrySelected){

            countrySelected = ($.inArray( countrySelected, orderCountries) === -1) ?  'Россия' : countrySelected;

            if($.inArray( countrySelected, orderCountries) !== -1){

                var phoneMask = phoneMaskBehavior(countrySelected);
                var phonePlaceholder = getPlaceholder(countrySelected);

                try{

                    $(phoneElt).cleanVal();
                    $(phoneElt).unmask();
                    $(phoneElt).unbind('keydown');

                    $(phoneElt).tooltip('destroy');
                    $(phoneElt).removeClass('has_error');
                    $(phoneElt).removeClass('has_order_error');

                } catch(e){

                }


                $(phoneElt).attr('autocomplete',false);

                $(phoneElt).attr('placeholder', phonePlaceholder);

                switch(countrySelected){
                    case 'Россия':



                        var spValue = $(phoneElt).val();
                        var iMaxLength = 12;
                        iMaxLength = spValue[0] == '+' ? 12 : 11;

                        if($(phoneElt).val().length < iMaxLength){

                            $(phoneParent).find('.has-success').removeClass('has-success').addClass('has_order_error');
                            $(phoneParent).find('.has_tooltip').addClass('has_error');
                            $('#ORDER_CONFIRM_BUTTON').prop('haserror',true);

                            if($('#ORDER_CONFIRM_BUTTON').prop('disabled') == false){
                                console.log('disabled');
                                $('#ORDER_CONFIRM_BUTTON').prop('disabled',true);
                            }
                        }

                        if($(phoneElt).val().length > (iMaxLength - 1)){

                            $(phoneParent).find('.has_order_error').removeClass('has_order_error').addClass('has-success');
                            $(phoneParent).find('.has_tooltip').removeClass('has_error');
                            $('#ORDER_CONFIRM_BUTTON').prop('haserror',false);

                            if($('#ORDER_CONFIRM_BUTTON').prop('disabled') != false){
                                console.log('enabled');
                                $('#ORDER_CONFIRM_BUTTON').prop('disabled',false);
                            }
                        }


                        var maskOptions =  {

                            onComplete: function(cep) {

                                $(phoneParent).find('.has_order_error').removeClass('has_order_error').addClass('has-success');
                                $(phoneParent).find('.has_tooltip').removeClass('has_error');
                                $('#ORDER_CONFIRM_BUTTON').prop('haserror',false);

                                if($('#ORDER_CONFIRM_BUTTON').prop('disabled') != false){
                                    $('#ORDER_CONFIRM_BUTTON').prop('disabled',false);
                                }

                            },

                            onKeyPress: function(cep, event, currentField, options){

                                var spValue = $(phoneElt).val();
                                var iMaxLength = 12;

                                iMaxLength = spValue[0] == '+' ? 12 : 11;

                                if($(currentField).get(0)
                                    && $(currentField).val().indexOf('+78') === 0){
                                    $(currentField).val($(currentField).val().replace('+78','+79'));
                                }

                                if($(currentField).get(0)
                                    && $(currentField).val().indexOf('99') !== -1
                                    && $(currentField).attr('id') == phoneElt.id
                                    && !$('#checkPhoneModal').hasClass('in')
                                    && !checkmodelHidden
                                    && $(currentField).val().length == iMaxLength
                                ){

                                    $('#checkphone').val($(currentField).val());
                                    $('#checkphone').mask(phoneMask);

                                    try{
                                        $('#checkPhoneModal').modal('hide');
                                        checkmodelHidden = false;
                                    } catch(e){

                                    }

                                    $('#checkPhoneModal').modal();
                                    checkmodelHidden = true;
                                    $('#checkPhoneModal button').unbind('click');
                                    $('#checkPhoneModal button').on('click',function(event){

                                        $(phoneElt).val($('#checkphone').val());

                                        $.cookie('phonechecked',$('#checkphone').val());

                                        try{
                                            $('#checkPhoneModal').modal('hide');
                                            checkmodelHidden = false;
                                        } catch(e){

                                        }

                                        event.preventDefault();
                                        return false;
                                    })

                                }


                                if($(currentField).val().length < iMaxLength){

                                    $(phoneParent).find('.has-success').removeClass('has-success').addClass('has_order_error');
                                    $(phoneParent).find('.has_tooltip').addClass('has_error');
                                    $('#ORDER_CONFIRM_BUTTON').prop('haserror',true);

                                    if($('#ORDER_CONFIRM_BUTTON').prop('disabled') == false){
                                        $('#ORDER_CONFIRM_BUTTON').prop('disabled',true);
                                    }
                                }

                                if($(currentField).val().length > (iMaxLength - 1)){

                                    $(phoneParent).find('.has_order_error').removeClass('has_order_error').addClass('has-success');
                                    $(phoneParent).find('.has_tooltip').removeClass('has_error');
                                    $('#ORDER_CONFIRM_BUTTON').prop('haserror',false);

                                    if($('#ORDER_CONFIRM_BUTTON').prop('disabled') != false){
                                        $('#ORDER_CONFIRM_BUTTON').prop('disabled',false);
                                    }
                                }

                            }

                        };

                        $(phoneElt).mask(phoneMask, maskOptions);
                        break;
                    default:

                        $(phoneParent).find('.has_order_error').removeClass('has_order_error').addClass('has-success');
                        $(phoneParent).find('.has_tooltip').removeClass('has_error');
                        $('#ORDER_CONFIRM_BUTTON').prop('haserror',false);

                        console.log('skipped');

                        if($('#ORDER_CONFIRM_BUTTON').prop('disabled') != false){
                            $('#ORDER_CONFIRM_BUTTON').prop('disabled',false);
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

    var popoverContent = '<div class="message"><p>Это ваш город?</p><div class="buttons text-center"><button class="yes btn-info btn" onclick="$.cookie(\'locationSelected\',true);$(\'#ORDER_FORM input.bx-ui-sls-fake\').popover(\'hide\');$(\'#ORDER_CONFIRM_BUTTON\').prop(\'disabled\',false);">Да</button><button class="no btn btn-danger" onclick="$(\'.bx-ui-sls-clear\').trigger(\'click\');setTimeout(function(){$(\'.bx-ui-sls-fake\').trigger(\'focus\');},300);$.cookie(\'locationSelected\',null);$(\'#ORDER_FORM input.bx-ui-sls-fake\').popover(\'hide\');$(\'#ORDER_CONFIRM_BUTTON\').prop(\'disabled\',\'true\');">Нет</button></div></div></div>';

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
};


function afterFormReload() {

    if($('body').hasClass('is_authorized')){
        $.cookie('isCloseHidden',false);
    }


    if($('#passcodeModal').get(0)
        && !$.cookie('isCloseHidden')){

        $('.EMAIL input[type="text"]').bind('blur',function(){

            if($(this).val() != ""
                && $(this).val().indexOf('@') !== -1){

                var __self = this;

                jQuery.post(
                    location.protocol + '//' + location.hostname + '/include/onetimepassword.php',
                    '&pass_user='+$(this).val()+'&pass_action=checkemail',
                    function(data){

                        if(data
                            && data.emailFound){

                            $('#passcodeModal').modal();
                            $('input[name="pass_user"]').val($(__self).val());

                        }
                    },
                    'json'
                );

            }

        });

        $('#passcodeModal .close').bind('click',function(){

            $.cookie('isCloseHidden',true);

        })



    }

    remapPhoneMask();

    try{
        locPopoverShow = true;
        //$('#ORDER_FORM input.bx-ui-sls-fake').blur();
        $('#ORDER_FORM input.bx-ui-sls-fake').popover('destroy');
        $('.popover.in').remove();
        clearInterval(locInterval);

    } catch(e){

    }

    locInterval = setInterval(function(){

        if($('#ORDER_FORM input.bx-ui-sls-fake').get(0)){

            clearInterval(locInterval);

            try {
                $('#ORDER_FORM input.bx-ui-sls-fake').popover('destroy');
                $('.popover.in').remove();
            } catch (e) {

            };

            var locationSelectedConfirmed = $.cookie('locationSelected');
            locationSelectedConfirmed = $('html').hasClass('mobile') ? 'true' : locationSelectedConfirmed;

            if(locationSelectedConfirmed != 'true') {

                locPopoverShow = false;

                $('#ORDER_FORM input.bx-ui-sls-fake').on("focus",destroyLocPopover);

                //$('#ORDER_FORM input.bx-ui-sls-fake').on("blur", createLocPopover);

                if (!locPopoverShow) {

                    createLocPopover();

                };

            } else {
                if($('#ORDER_CONFIRM_BUTTON').prop('haserror') != true){
                    $('#ORDER_CONFIRM_BUTTON').prop('disabled',false);
                }
            };
        };

    },900);

    if(!$('.LOCATION.order-properties').hasClass('has-success')){

        $('.order-properties').each(function(){
            if(!$(this).hasClass('LOCATION')){
                $(this).addClass('hidden');
            };
        });

        //$('#ORDER_CONFIRM_BUTTON').attr('disabled',true);

    } else {

        $('.order-properties').each(function(){
            $(this).removeClass('hidden');
        });

        if(!$('.bx_block.has-success',$('.delivery.order-properties')).get(0)){

            $('.order-properties').each(function(){
                if(!$(this).hasClass('LOCATION') && !$(this).hasClass('delivery')){
                    $(this).addClass('hidden');
                };
            });

        } else {

            $('.order-properties').each(function(){
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

    if(hideOptionsDelivery.length){

        for(var i = 0; i < hideOptionsDelivery.length; i++){

            var cOptions = hideOptionsDelivery[i].split('.');

            if(cOptions && cOptions.length)
                for(var c = 0; c < cOptions.length; c++){
                    if(cOptions[c] != "" && cOptions[c].indexOf("ORDER_PROP") === 0){
                        $('input[type="text"],textarea','.' + cOptions[c]).each(function(){
                            if($.trim($(this).val()) == "По умолчанию"){
                                $(this).val("");
                            };
                        });
                    };
                };

        };

        for(var i = 0; i < hideOptionsDelivery.length; i++){

            $('select',hideOptionsDelivery[i]).each(function(){
                if(this.selectedIndex === -1){
                    this.selectedIndex = 0;
                }
            });


            $('input[type="text"],textarea',hideOptionsDelivery[i]).each(function(){

                if($.trim($(this).val()) == ""){
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

    if($('.order_quantity').get(0)){
        $('.order_quantity').each(function(){
            if(!$(this).attr('number')){
                $(this).attr('number',true);
                $(this).bootstrapNumber();
            };
        });
    };

    $('.selectpicker').selectpicker();

    $('td.actions .remove').bind("click",removeFromCart);
    $('#recount').bind("click",doRecount);

    if($('.has_order_error').get(0)){
        $('.has_order_error').each(function(){
            var errorMsg            = $(this).attr('data:error');
            var errorElt            = this;

            $('.has_tooltip',this).each(function(){
                if(errorMsg && !$(this).attr('disabled')){

                    $(this).tooltip({title: errorMsg, placement: 'bottom', html: true});
                    $(this).tooltip('show');
                    $(this).addClass('has_error');

                    $(this).bind('click',function(){
                        $(this).tooltip('destroy');
                        $(this).removeClass('has_error');
                        $(errorElt).removeClass('has_order_error');
                    });

                };

            })


        });
    };
};

function removeFromCart(event){

    //ShowWaitWindow();
    BX.showWait();

    BX.ajax({

        url: this.href,
        method: 'get',
        dataType: 'json',
        async: true,
        processData: true,
        emulateOnload: true,
        start: true,
        onsuccess: function(result){

            //CloseWaitWindow();
            BX.closeWait();

            if(result.deleted && result.deleted > 0){
                submitForm();
            } else {
                $("#empty-cart").show();
                $("#order_form_div").hide();
                top.BX.scrollToNode(top.BX('empty-cart'));
            }

        },
        onfailure: function(type, e){

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

function doRecount(){

    var product_id = [];

    $('input[type="text"].order_quantity').each(function(){
        product_id[$(this).attr('data:id')] = this.value;
    });

    var couponValue = $('#coupon').val();

    //ShowWaitWindow();
    BX.showWait();

    BX.ajax({

        url: '/bitrix/templates/.default/components/bitrix/sale.order.ajax/main_test/sale_order_ajax.php',
        method: 'post',
        dataType: 'json',
        async: true,
        processData: true,
        emulateOnload: true,
        start: true,
        data: {'action': 'update', 'product_id': product_id, 'coupon': couponValue},
        onsuccess: function(result){

            if(result && result.msgTxt){

                if(!result.success){
                    $('#coupon').val("");
                }

                $('#coupon-result').html(result.msgTxt);
            }

            //CloseWaitWindow();
            BX.closeWait();

            if(result.updated){

                submitForm();

            }

        },
        onfailure: function(type, e){

            //CloseWaitWindow();
            BX.closeWait();

            // on error do nothing
        }

    });


}

