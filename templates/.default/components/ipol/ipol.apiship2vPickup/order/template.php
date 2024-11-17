<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

// This is da specific widget template for SOA page
CModule::IncludeModule('ipol.apiship2v');

if (!(isset($arParams['NOMAPS']) && $arParams['NOMAPS'] === 'Y'))
    apishipHelper2v::addYMapsScript();

global $APPLICATION;

$deliveryProfiles = [];
foreach (apishipHelper2v::getActualProfiles() as $id => $profile) {
    switch(true) {
        case $profile['CODE'] === 'apiship:courier':
            break;
        default:
            $deliveryProfiles[$id] = array(
                'tag'   => false,
                'price' => false,
                'self'  => false,
            );
            break;
    }
}
?>
<script data-skip-moving="true">
var IPOLapiship_pvz = {
	city:   '<?=$arResult['cityName']?>',
	cityID: '<?=$arResult['city']?>',
	button: '<a href="javascript:void(0);" id="apiship_selectPVZ" onclick="IPOLapiship_pvz.selectPVZ(); return false;"><?=GetMessage("IPOLapiship_FRNT_CHOOSEPICKUP")?></a>',
	pvzInputs:    [<?=substr($arResult['propAddr'], 0, -1)?>], /* Inputs for PVZ address */
    deliveries:   <?=CUtil::PHPToJSObject($deliveryProfiles)?>,
	pvzLabel:  "",
	presizion: 2,
	pvzId:     false,
    pvzAdress: '',
	chosenPVZProviderKey:   false,
	chosenPVZProviderID:    false,
	chosenTariffID:         false,
	chosenTariffPickupType: false,
	PVZ: {},
    pointTariffs: {},
	DefaultVals: {},
	LoadFromAJAX: false,
	LoadInputsFromAJAX: false,
	image_url: "<?=apishipHelper2v::getProviderIconsURL()?>",
	arImages:  <?=CUtil::PHPToJSObject(apishipHelper2v::getProvderIcons())?>,
	relativeParent: false,
	minWindowWidth: 600,
	widgetSearch:     <?=CUtil::PHPToJSObject($arResult['widgetSearch']);?>,
	widgetSearchMark: <?=CUtil::PHPToJSObject($arResult['widgetSearchMark']);?>,
    widgetNoTariffSel:      <?=CUtil::PHPToJSObject($arResult['widgetNoTariffSel']);?>,
	widgetPointFeatureType: <?=CUtil::PHPToJSObject($arResult['widgetPointFeatureType']);?>,
	widgetPointFeatureCOD:  <?=CUtil::PHPToJSObject($arResult['widgetPointFeatureCOD']);?>,
    widgetPlacemarkIcon:    <?=CUtil::PHPToJSObject($arResult['widgetPlacemarkIcon']);?>,
    Y_map:       false,
    clusterer:   false,
    currentMark: false,
    currentZoom: false,
    isConverted: <?=CUtil::PHPToJSObject(apishipHelper2v::isConverted());?>,
    isPickUpChecked: false, /* Flag for pickup check */
    isMobile: false,
    isList: true,

    CheckType: function(element){
        if ((typeof element == 'undefined') || (element == 'undefined') || (element == null))
            return false;
        return true;
    },

	GetDefaultVals: function(){
		IPOLapiship_pvz.DefaultVals = <?=CUtil::PhpToJSObject($arResult["defaultVals"])?>;
	},
	
	GetPVZ: function(){
		IPOLapiship_pvz.PVZ = <?=CUtil::PhpToJSObject($arResult["PVZ"])?>;
		IPOLapiship_pvz.pointTariffs = <?=CUtil::PhpToJSObject($arResult['pointTariffs'])?>;
	},
	
	GetAjaxPVZ: function(ajaxAns, newTemplateAjax){
		if (IPOLapiship_pvz.oldTemplate) {
			if (IPOLapiship_pvz.CheckType(document.getElementById('ipolapiship_pvz_list_tag_ajax'))) {
				IPOLapiship_pvz.PVZ = JSON.parse(document.getElementById('ipolapiship_pvz_list_tag_ajax').innerHTML);
                document.getElementById('ipolapiship_pvz_list_tag_ajax').parentNode.removeChild(document.getElementById('ipolapiship_pvz_list_tag_ajax'));
			}

            let tagPointTariffs = document.getElementById('ipolapiship_point_tariffs_tag_ajax');
            if (IPOLapiship_pvz.CheckType(tagPointTariffs)) {
                IPOLapiship_pvz.pointTariffs = JSON.parse(tagPointTariffs.innerHTML);
                tagPointTariffs.parentNode.removeChild(tagPointTariffs);
            }
		} else if (newTemplateAjax && typeof ajaxAns.apiship.ipolapiship_pvz_list_tag_ajax != "undefined") {
            IPOLapiship_pvz.PVZ = ajaxAns.apiship.ipolapiship_pvz_list_tag_ajax;
            IPOLapiship_pvz.pointTariffs = ajaxAns.apiship.ipolapiship_point_tariffs_tag_ajax;
        }
		
		/* Drop selected PVZ if city changes */
		if (typeof IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId] == "undefined") {
			IPOLapiship_pvz.pvzId                  = false;
			IPOLapiship_pvz.chosenPVZProviderKey   = false;
			IPOLapiship_pvz.chosenPVZProviderID    = false;
			IPOLapiship_pvz.chosenTariffID         = false;
			IPOLapiship_pvz.chosenTariffPickupType = false;
			IPOLapiship_pvz.UpdateChosenInputs();
		}
	},
	
	GetAjaxDefaultVals: function(ajaxAns, newTemplateAjax){
		if (IPOLapiship_pvz.oldTemplate) {
			if (IPOLapiship_pvz.CheckType(document.getElementById('ipolapiship_default_vals_tag_ajax'))) {
				IPOLapiship_pvz.DefaultVals = JSON.parse(document.getElementById('ipolapiship_default_vals_tag_ajax').innerHTML);
                document.getElementById('ipolapiship_default_vals_tag_ajax').parentNode.removeChild(document.getElementById('ipolapiship_default_vals_tag_ajax'));
			}
		}
		else if (newTemplateAjax && typeof ajaxAns.apiship.ipolapiship_default_vals_tag_ajax != "undefined")
			IPOLapiship_pvz.DefaultVals = ajaxAns.apiship.ipolapiship_default_vals_tag_ajax;
	},

    makeHTMLId: function(id){
        return 'ID_DELIVERY_' + ((id === 'apiship_pickup') ?  id : 'ID_' + id);
    },
	
	init: function(){
        IPOLapiship_pvz.orderForm = "ORDER_FORM";
		
		if (IPOLapiship_pvz.CheckType(document.getElementById(IPOLapiship_pvz.orderForm))) {
            IPOLapiship_pvz.oldTemplate = true;
        } else {
			IPOLapiship_pvz.oldTemplate = false;
			IPOLapiship_pvz.orderForm   = "bx-soa-order-form";

            /* Crutch for order form with custom id */
            if (!document.getElementById(IPOLapiship_pvz.orderForm) && document.querySelector('[name="ORDER_FORM"]')) {
                IPOLapiship_pvz.orderForm = document.querySelector('[name="ORDER_FORM"]').getAttribute('id');
            }
		}

        /* Subscribe to modern SOA form reloading event */
		if (typeof BX !== 'undefined' && BX.addCustomEvent)
			BX.addCustomEvent('onAjaxSuccess', IPOLapiship_pvz.onLoad);

        /* Subscribe to ancient SOA form reloading event */
        if (window.jsAjaxUtil) {
			jsAjaxUtil._CloseLocalWaitWindow = jsAjaxUtil.CloseLocalWaitWindow;
			jsAjaxUtil.CloseLocalWaitWindow = function (TID, cont){
				jsAjaxUtil._CloseLocalWaitWindow(TID, cont);
				IPOLapiship_pvz.onLoad();
			}
		}
		
		IPOLapiship_pvz.onLoad();

        let mask = document.createElement('div');
        mask.id = 'apiship_mask';
        document.body.append(mask);
        document.body.append(document.getElementById('apiship_pvz'));
	},
	
	onLoad: function(ajaxAns){

        /* Tag for button */
		var tag = false;
		var previousCityID  = IPOLapiship_pvz.cityID;
		var previousPvzID   = IPOLapiship_pvz.pvzId;
		var newTemplateAjax = (typeof (ajaxAns) != 'undefined' && ajaxAns !== null && typeof (ajaxAns.apiship) == 'object');

		IPOLapiship_pvz.CheckChosenPickUp(ajaxAns);

        /* First time get data from component, after that take it from AJAX SOA answers */
		if (!IPOLapiship_pvz.LoadFromAJAX) {
			IPOLapiship_pvz.GetPVZ();
			IPOLapiship_pvz.GetDefaultVals();
			IPOLapiship_pvz.getDefaultPVZ();
			IPOLapiship_pvz.LoadFromAJAX = true;
		} else {
			IPOLapiship_pvz.GetAjaxPVZ(ajaxAns, newTemplateAjax);
			IPOLapiship_pvz.GetAjaxDefaultVals(ajaxAns, newTemplateAjax);
		}
		
        var isPickupInp = document.getElementById('apiship_isPickup');
        if (IPOLapiship_pvz.CheckType(isPickupInp))
            isPickupInp.value = IPOLapiship_pvz.isPickUpChecked;
        else {
            isPickupInp = document.createElement('input');
            isPickupInp.type = 'hidden';
            isPickupInp.name = 'apiship_isPickup';
            isPickupInp.id = 'apiship_isPickup';
            isPickupInp.value = IPOLapiship_pvz.isPickUpChecked;
            document.getElementById(IPOLapiship_pvz.orderForm).append(isPickupInp);
        }

		tag = document.getElementById('IPOLapiship_injectHere_pickup');
        if (IPOLapiship_pvz.CheckType(tag) && tag.innerHTML.indexOf(IPOLapiship_pvz.button) === -1) {
			IPOLapiship_pvz.pvzLabel = tag;
		}
		
		if (IPOLapiship_pvz.oldTemplate) {
			if (IPOLapiship_pvz.CheckType(document.getElementById('apiship_city'))) {
				IPOLapiship_pvz.city   = document.getElementById('apiship_city').value;
				IPOLapiship_pvz.cityID = document.getElementById('apiship_city_id').value;
				
				/* Selected city changes */
				if (previousCityID !== IPOLapiship_pvz.cityID)
					IPOLapiship_pvz.unmakePVZAddress(previousPvzID);
			}
			
			if (IPOLapiship_pvz.CheckType(document.getElementById('apiship_dostav')) && (document.getElementById('apiship_dostav').value === 'apiship:pickup'))
			{
				/* Set readonly on address input field */
				IPOLapiship_pvz.markUnable(false);
				
				if (IPOLapiship_pvz.pvzId)
					IPOLapiship_pvz.choozePVZ(IPOLapiship_pvz.pvzId, true);
			}
		} else {
			if (newTemplateAjax) {
				if (ajaxAns.apiship.apiship_city)
					IPOLapiship_pvz.city = ajaxAns.apiship.apiship_city;
				
				if (ajaxAns.apiship.apiship_city_id) {
					IPOLapiship_pvz.cityID = ajaxAns.apiship.apiship_city_id;
					
					/* Selected city changes */
					if (previousCityID !== IPOLapiship_pvz.cityID)
						IPOLapiship_pvz.unmakePVZAddress(previousPvzID);
				}
				
				if (ajaxAns.apiship.apiship_dostav)					
					if (ajaxAns.apiship.apiship_dostav === 'apiship:pickup' || IPOLapiship_pvz.isPickUpChecked)
					{
						/* Set readonly on address input field */
						IPOLapiship_pvz.markUnable(false);
						
						if (IPOLapiship_pvz.pvzId)
							IPOLapiship_pvz.choozePVZ(IPOLapiship_pvz.pvzId, true);
					}
			}
		}
		
		/* IPOLapiship_pvz.UpdateChosenInputs(); */
		IPOLapiship_pvz.ChangeLabelHTML();
	},
	
	/* Try to get default PVZ, set tariff data if PVZ founded (actual for users with previous orders via Apiship) */
	getDefaultPVZ: function(){
		var chznPnkt = false;
		var possiblePvzID = false;
	
		for (var i in IPOLapiship_pvz.pvzInputs) {
			if (typeof(IPOLapiship_pvz.pvzInputs[i]) === 'function') 
				continue;
			chznPnkt = document.getElementById('ORDER_PROP_' + IPOLapiship_pvz.pvzInputs[i]);
            if (!IPOLapiship_pvz.CheckType(chznPnkt) || chznPnkt.tagName !== 'INPUT')
				chznPnkt = document.querySelector('[name="ORDER_PROP_' + IPOLapiship_pvz.pvzInputs[i] + '"]');

			if (IPOLapiship_pvz.CheckType(chznPnkt)) {
				/* Get address with possible PVZ code */
				var address = chznPnkt.value;
				
				if (address.length > 0 && address.indexOf('#S') !== -1) {
					possiblePvzID = address.slice((address.indexOf('#S') + 2));					
					if (possiblePvzID) {
					    if (typeof (IPOLapiship_pvz.PVZ[possiblePvzID]) !== 'undefined') {
							/* PVZ founded */
							IPOLapiship_pvz.pvzId = possiblePvzID;
							
							IPOLapiship_pvz.pvzAdress = IPOLapiship_pvz.city + ", " + IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId]['Address'] + " #S" + IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId].id;

                            IPOLapiship_pvz.chosenPVZProviderKey = IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId].providerKey;
							IPOLapiship_pvz.chosenPVZProviderID = IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId].code;

                            let tariffKey = (typeof(IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId].tariffSelected) !== 'undefined') ? IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId].tariffSelected : IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId].tariffBest;
                            let tariff = IPOLapiship_pvz.pointTariffs.tariffs[IPOLapiship_pvz.chosenPVZProviderKey][tariffKey];

							IPOLapiship_pvz.chosenTariffID = tariff.tariffId;
							IPOLapiship_pvz.chosenTariffPickupType = tariff.tariffPickupType;

							IPOLapiship_pvz.UpdateChosenInputs();
							IPOLapiship_pvz.ChangeLabelHTML();
									
							if (IPOLapiship_pvz.isPickUpChecked)
								IPOLapiship_pvz.markUnable(IPOLapiship_pvz.pvzId);
						} else {
							/* Drop address string, PVZ may be closed or unavailable for current order */
							IPOLapiship_pvz.pvzAdress = '';
							chznPnkt.value = '';
							chznPnkt.innerHTML = '';
						}
					}
				}
			}
		}
	},
	
	/* Clear PVZ address if city changes to prevent creating order with PVZ selection from previous city */
	unmakePVZAddress: function(previousPvzID){
		var chznPnkt = false;
		var possiblePvzID = false;
	
		for (var i in IPOLapiship_pvz.pvzInputs) {
			if (typeof(IPOLapiship_pvz.pvzInputs[i]) === 'function') 
				continue;
			
			chznPnkt = document.getElementById('ORDER_PROP_' + IPOLapiship_pvz.pvzInputs[i]);
			if (!IPOLapiship_pvz.CheckType(chznPnkt) || chznPnkt.tagName !== 'INPUT')
				chznPnkt = document.querySelector('[name="ORDER_PROP_'+IPOLapiship_pvz.pvzInputs[i]+'"]');
	
			if (IPOLapiship_pvz.CheckType(chznPnkt)) {
				/* Get address with possible PVZ code */
				var address = chznPnkt.value;
				
				if (address.length > 0 && address.indexOf('#S') !== -1) {
					possiblePvzID = address.slice((address.indexOf('#S') + 2));					
					if (possiblePvzID && previousPvzID === possiblePvzID) {
						/* Drop address string cause there are PVZ data from previous city */
						IPOLapiship_pvz.pvzAdress = '';
                        chznPnkt.value = '<?=GetMessage("IPOLapiship_MESS_CHOOZE_PVZ_TEXT")?>';
                        chznPnkt.innerHTML = '<?=GetMessage("IPOLapiship_MESS_CHOOZE_PVZ_TEXT")?>';
					}
				}
			}
		}
	},

    selectPVZ: function(){
		if ((typeof ymaps == "undefined") || (typeof ymaps.Map == "undefined")) {
            console.log('ipol.apiship2v: Yandex Maps API undefined, probably because nothing on this page loads it.');
            return;
        }

        if (!IPOLapiship_pvz.isActive) {
			IPOLapiship_pvz.isActive = true;

			IPOLapiship_pvz.getWinPosition();
			
            document.getElementById('apiship_mask').style.display = 'block';

			IPOLapiship_pvz.initCityPVZ();

			if (IPOLapiship_pvz.Y_map)
               IPOLapiship_pvz.Y_clearPVZ();

			IPOLapiship_pvz.Y_init();
            IPOLapiship_pvz.Y_map.container.fitToViewport();

            if (IPOLapiship_pvz.pvzId && typeof (IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId]) !== 'undefined') {
                /* Cause placemark can be clustered */
                var placemarkState = IPOLapiship_pvz.clusterer.getObjectState(IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId].placeMark);
                if (placemarkState.isClustered) {
                    IPOLapiship_pvz.clusterer.remove(IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId].placeMark);
                    IPOLapiship_pvz.Y_map.geoObjects.add(IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId].placeMark);
                }

                if (IPOLapiship_pvz.currentZoom)
                    IPOLapiship_pvz.Y_map.setZoom(IPOLapiship_pvz.currentZoom);

                IPOLapiship_pvz.markChosenPVZ(IPOLapiship_pvz.pvzId);
            }
		}
	},

    getWinPosition: function(){
		var hndlr       = document.getElementById('apiship_pvz');
        var shiftWidth  = 0;
        var shiftHeight = 0;

        if(window.innerWidth < 850) {
            IPOLapiship_pvz.isMobile = true;
            hndlr.style.display = 'block';
            hndlr.style.width = window.innerWidth+'px';
            hndlr.style.height = window.innerHeight + 'px';
            hndlr.style.left = '0px';
            hndlr.style.top = (window.scrollY - shiftHeight)+'px';

        } else {
            IPOLapiship_pvz.isMobile = false;
            let width       = window.innerWidth * 0.98;
            let height = window.innerHeight * 0.96;
            let hhdlr_height = getComputedStyle(hndlr).height;

            hhdlr_height = Number(hhdlr_height.replace('px', ''));

            if (width < IPOLapiship_pvz.minWindowWidth)
                width = IPOLapiship_pvz.minWindowWidth;

            hndlr.style.display = 'block';
            hndlr.style.width = width+'px';
            hndlr.style.height = height + 'px';
            hndlr.style.left = (((window.innerWidth - width) / 2) - shiftWidth)+'px';
            hndlr.style.top = (((window.innerHeight - height) / 2) + window.scrollY - shiftHeight)+'px';
            
            let deliveryItems = document.getElementById('apiship_deliveryitems');
            let yandexMap = document.getElementById('apiship_map');

            if(deliveryItems && deliveryItems.style && yandexMap && yandexMap.style) {
                deliveryItems.style.height = (height - 170) + 'px';
                yandexMap.style.height = height + 'px';
            }
        }
	},

	resize: function(){
		if (IPOLapiship_pvz.isActive)
			IPOLapiship_pvz.getWinPosition();
	},
	
	initCityPVZ: function() {
		var city = IPOLapiship_pvz.city;
		var cnt = [];
		
		IPOLapiship_pvz.PVZHTML();
		IPOLapiship_pvz.multiPVZ = (IPOLapiship_pvz.PVZ.length !== 1);
        
        var hndlr = document.getElementById('apiship_pvz');
        var deliveryItems = document.getElementById('apiship_deliveryitems');
        var yandexMap = document.getElementById('apiship_map');

        if(hndlr && deliveryItems && deliveryItems.style && yandexMap && yandexMap.style) {
            var hhdlr_height = getComputedStyle(hndlr).height;
            hhdlr_height = Number(hhdlr_height.replace('px', ''));

            deliveryItems.style.height = (hhdlr_height - 170) + 'px';
            yandexMap.style.height = hhdlr_height + 'px';
        }

        document.body.style.overflow = 'hidden';

	},

    selectChangeHandler: function(selectElement) {
        let selectedProvider = selectElement.value;
        let pickupPoints = {};
        let html = '';

        for (let i in IPOLapiship_pvz.PVZ) {
            if (typeof pickupPoints[IPOLapiship_pvz.PVZ[i].providerKey] === "undefined") {
                pickupPoints[IPOLapiship_pvz.PVZ[i].providerKey] = [];
            }

            pickupPoints[IPOLapiship_pvz.PVZ[i].providerKey].push(IPOLapiship_pvz.PVZ[i]);
        }

        let selectedPoints = pickupPoints[selectedProvider];

        if(selectedPoints == null || selectedPoints == undefined ) {
            selectedProvider = '';
            selectedPoints = pickupPoints[Object.keys(pickupPoints)[0]];
        }

        let contentBlock = '';

        for (let i in selectedPoints) {
            let id = selectedPoints[i].id;
            contentBlock += '<div id="PVZ_' + id + '" onclick="IPOLapiship_pvz.markChosenPVZ(\'' + id + '\')" onmouseover="IPOLapiship_pvz.Y_blinkPVZ(\'' + id + '\', true)" onmouseout="IPOLapiship_pvz.Y_blinkPVZ(\'' + id + '\')" class="address-item address-list__item _viewed _d">' + IPOLapiship_pvz.paintPVZ(id) + '</div>';
        }

        html += contentBlock;

        document.getElementById('apiship_deliveryitems').innerHTML = html;

        // filter map depending on delivery service
        
		IPOLapiship_pvz.Y_clearPVZ();
		IPOLapiship_pvz.Y_markPVZ(selectedProvider);
    },

    updatePickupPointsHandler: function(inputElement) {
        const key = inputElement.value;

        console.log(`key => ${key}`);
        
        let pickupPoints = {};
        let html = '';

        for (let i in IPOLapiship_pvz.PVZ) {
            if (typeof pickupPoints[IPOLapiship_pvz.PVZ[i].providerKey] === "undefined") {
                pickupPoints[IPOLapiship_pvz.PVZ[i].providerKey] = [];
            }

            pickupPoints[IPOLapiship_pvz.PVZ[i].providerKey].push(IPOLapiship_pvz.PVZ[i]);
        }

        let contentBlock = '';

        // Add Pick up List
        for (let providerKey in pickupPoints) {

            for (let i in pickupPoints[providerKey]) {
                let id = pickupPoints[providerKey][i].id;
                let street = pickupPoints[providerKey][i].street;

                if(street == 0 || key.trim().length !== 0 && street !== 0 && street != null && !(street.toLowerCase()).includes(key.toLowerCase()))
                {
                    continue;
                }

                contentBlock += '<div id="PVZ_' + id + '" onclick="IPOLapiship_pvz.markChosenPVZ(\'' + id + '\')" onmouseover="IPOLapiship_pvz.Y_blinkPVZ(\'' + id + '\', true)" onmouseout="IPOLapiship_pvz.Y_blinkPVZ(\'' + id + '\')" class="address-item address-list__item _viewed _d">' + IPOLapiship_pvz.paintPVZ(id) + '</div>';
            }
        }

        html += contentBlock;

        document.getElementById('apiship_deliveryitems').innerHTML = html;
    },

    MobileExchangeButtonHandler: function() {
        if(IPOLapiship_pvz.isList == true) {
            document.getElementById('apiship_deliveryitems').style.display = 'block';
            document.getElementById('apiship_map').style.display = 'none';
        } else {
            document.getElementById('apiship_deliveryitems').style.display = 'none';
            document.getElementById('apiship_map').style.display = 'block';
        }

        IPOLapiship_pvz.isList = !IPOLapiship_pvz.isList;

    },

	PVZHTML: function(){
        let html = '';
        let pickupPoints = {};
        var cityID = IPOLapiship_pvz.cityID;

        for (let i in IPOLapiship_pvz.PVZ) {
            if (typeof pickupPoints[IPOLapiship_pvz.PVZ[i].providerKey] === "undefined") {
                pickupPoints[IPOLapiship_pvz.PVZ[i].providerKey] = [];
            }

            pickupPoints[IPOLapiship_pvz.PVZ[i].providerKey].push(IPOLapiship_pvz.PVZ[i]);
        }

        for (let providerKey in pickupPoints) {
            pickupPoints[providerKey].sort(IPOLapiship_pvz.sortPVZ);
        }
        
        const getProviderCounts = () => {
            const keys = Object.keys(pickupPoints);
            const result = {};

            (keys).forEach(key => {
                result[key] = pickupPoints[key].length;
            });

            return result;
        }

        const providerCounts = getProviderCounts();

        const totalCount = Object.values(providerCounts).reduce((acc, value) => acc + value, 0);

        // Add html element
        if(IPOLapiship_pvz.isMobile) {
            let headBlockContent = '';

            let headTitle = `<button aria-label="Back" type="button" class="modal-pvz-header__back" onclick="IPOLapiship_pvz.close()"><svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="modal-pvz-header__back-icon"><path fill-rule="evenodd" clip-rule="evenodd" d="M3 8l1.4-1.485 4.915-5.213a.931.931 0 011.372 0l.029.03a1.074 1.074 0 010 1.455L5.8 8l4.915 5.213a1.074 1.074 0 010 1.456l-.029.03a.931.931 0 01-1.372 0L4.401 9.485 3 8z" ></path></svg></button>`
            
            headTitle += `<h2 class="modal-pvz-header__title"><font style="vertical-align: inherit"><font style="vertical-align: inherit"> Пункты выдачи </font></font></h2>`;
            
            headTitle += `<button id="model_pvz_header_switch" class="modal-pvz-header__switch" onclick="IPOLapiship_pvz.MobileExchangeButtonHandler()"><font style="vertical-align: inherit"><font style="vertical-align: inherit"> Списком </font></font></button>`;

            headBlockContent = `<div class="modal-pvz-header" >${headTitle}</div>`;
            
            // Add service provider list
            let selectBoxContent = `<option value="total"><span style="text-align:Left">Все службы  </span><span style="text-align:Right">(${totalCount})</span></option>`;
            
            Object.keys(providerCounts).forEach(key => {
                selectBoxContent +=`<option value="${key}"><span style="text-align:Left">${key}  </span><span style="text-align:Right">(${providerCounts[key]})</span></option>`;
            });

            selectBoxContent = `<select id="items" name="points" class="pvz-header-block__select-list" onChange="IPOLapiship_pvz.selectChangeHandler(this)">${selectBoxContent}</select>`;

            headBlockContent += `<div class="pvz-header-block__select-type-pvz">${selectBoxContent}</div>`;

            headBlock = `<div class="pvz-header-block checkout-modal-map__header">${headBlockContent}</div>`;

            html += headBlock;
            
            let deliveryList = '';
            let contentBlock = '';

            // Add Pick up List
            for (let providerKey in pickupPoints) {

                for (let i in pickupPoints[providerKey]) {
                    let id = pickupPoints[providerKey][i].id;
                    contentBlock += '<div id="PVZ_' + id + '" class="address-item address-list__item _viewed _d">' + IPOLapiship_pvz.paintPVZ(id) + '</div>';
                }
            }

            html += '<div id="apiship_deliveryitems" class="apiship_delivContent">' + contentBlock + '</div>';
        } else {
            // Add h3 header title
            let headBlockContent = `<h3 class="title-box-checkout pvz-header-block__title _no-container"><font style="vertical-align: inherit">Пункты выдачи</font></h3>`;
    
            // Add searchbox bar
            // headBlockContent += `<div class="pvz-header-block__select-type-pvz"><div class="apiship-input-control__controls-list _after"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" heigth="16" width="16" class="apiship-input-control__control-icon"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.533 7.267c0 1.182-.39 2.274-1.048 3.153l2.157 2.158a.948.948 0 01-1.34 1.34l-2.208-2.207a5.267 5.267 0 112.439-4.444zm-5.24 3.574a3.596 3.596 0 100-7.192 3.596 3.596 0 000 7.192z"></path></svg></div><input type="text" name="apiship-search" class="pvz-header-block__select-list pvz-header-search pvz-header-block__select-list-input" placeholder="Введите название станции метро" onChange="IPOLapiship_pvz.updatePickupPointsHandler(this)"></div>`;

            // Add service provider list
            let selectBoxContent = `<option value="total"><span style="text-align:Left">Все службы  </span><span style="text-align:Right">(${totalCount})</span></option>`;
            
            Object.keys(providerCounts).forEach(key => {
                selectBoxContent +=`<option value="${key}"><span style="text-align:Left">${key}  </span><span style="text-align:Right">(${providerCounts[key]})</span></option>`;
            });

            selectBoxContent = `<select id="items" name="points" class="pvz-header-block__select-list" onChange="IPOLapiship_pvz.selectChangeHandler(this)">${selectBoxContent}</select>`;

            headBlockContent += `<div class="pvz-header-block__select-type-pvz">${selectBoxContent}</div>`;

            // Add CheckBox
            // let checkBoxContent = `<span class="filter-list-pvz__tag-desc" ><font style="vertical-align: inherit">Показать:</font></span><label class="apiship-checkbox-control filter-list-pvz__tag-item" style="cursor: default; text-align: left">`;
            
            // checkBoxContent += `<input type="checkbox" name="cheap" class="apiship-checkbox" /><span class="apiship-checkbox-control__caption"><font>${cityID==84? 'бесплатные':'дешовые'}</font></span>`;
            
            // checkBoxContent += `<input type="checkbox" name="nearest" class="apiship-checkbox"/><span class="apiship-checkbox-control__caption"><font>близжайщие</font></span></label>`;

            // headBlockContent += `<div class="filter-list-pvz pvz-header-block__filter-list">${checkBoxContent}</div>`;

            headBlock = `<div class="pvz-header-block checkout-modal-map__header">${headBlockContent}</div>`;

            html += headBlock;
        
            let deliveryList = '';
            let contentBlock = '';

            // Add Pick up List
            for (let providerKey in pickupPoints) {

                for (let i in pickupPoints[providerKey]) {
                    let id = pickupPoints[providerKey][i].id;
                    contentBlock += '<div id="PVZ_' + id + '" onclick="IPOLapiship_pvz.markChosenPVZ(\'' + id + '\')" onmouseover="IPOLapiship_pvz.Y_blinkPVZ(\'' + id + '\', true)" onmouseout="IPOLapiship_pvz.Y_blinkPVZ(\'' + id + '\')" class="address-item address-list__item _viewed _d">' + IPOLapiship_pvz.paintPVZ(id) + '</div>';
                }
            }

            html += '<div id="apiship_deliveryitems" class="apiship_delivContent">' + contentBlock + '</div>';
        }

        html = `<div class="checkout-modal-map__side-bar">${html}</div>`;
        // html = `<div class="checkout-modal-map">${html}</div>`;

        document.getElementById('apiship_sidebar').innerHTML = html;

        if(IPOLapiship_pvz.isMobile) {
            document.getElementById('apiship_map').style.display = 'block';
            document.getElementById('apiship_closer').style.display = 'none';
        }
	},
	
	paintPVZ: function(ind){
        let addr = '';
		let metro = '';
        const pvz = IPOLapiship_pvz.PVZ[ind];

		if (pvz.color && pvz.Address.indexOf(',') !== false) {
            metro = "<span style='color:" + pvz.color + "'>" + pvz.Address.substr(0, pvz.Address.indexOf(',')) + "</span><br>" + pvz.Name;
        } else {
            metro = pvz.Name;
            if (pvz.house) {
                metro += ', ' + pvz.house;
            }
        }

        addr += `<div class="address-item__metro-text">${metro}</div>`;

        // Get the current date
        const date = new Date();

        // Add 3 days to the current date
        date.setDate(date.getDate() + pvz.availableOperation);

        // Format the date using Russian locale and options
        const formattedDate = date.toLocaleDateString('ru-RU', {
            day: 'numeric',    // Day without leading zero
            month: 'long'      // Full month name in lowercase
        });

        // let delivery = `<div class="address-item__delivery">We will deliver on<strong class="address-item__delivery-text">${formattedDate}th, </strong><strong class="address-item__delivery-text _free">free of charge</strong></div>`;

        // addr += delivery;

        let point = `<div class="address-item__point"><div class="address-item__point-type">Пункты выдачи ${pvz.providerKey}</div><div class="address-item__point-work-hours">График работы:${pvz.WorkTime}</div></div>`;

        addr += point;

        // Add 
        // let selectButton = `<div class="address-item__btn-box"><button type="button" class="apiship-btn address-list__item-btn _small"><span class="apiship-btn__content">Выбрать пункт</span></button></div>`;

        let selectButton = `<div class="address-item__btn-box"><a id='apiship_button' href='javascript:void(0)' onclick='IPOLapiship_pvz.choozePVZ(\""+${ind}+"\")'></a></div>`;

        addr += selectButton;

        if(IPOLapiship_pvz.isMobile) {
            addr = `<div style="padding: 0px 15px;">${addr}</div>`;
        }

		return addr;
	},

    sortPVZ: function(a, b){
        if (a.Name === b.Name) {
            try {
                return a.house.localeCompare(b.house, undefined, {
                    numeric: true,
                    sensitivity: 'base'
                });
            } catch(e) {
                if (a.house === b.house) {
                    return 0;
                }
                return (a.house < b.house) ? -1 : 1;
            }
        }
        return (a.Name < b.Name) ? -1 : 1;
    },

    selectTariff: function(pvzId){
        IPOLapiship_pvz.PVZ[pvzId].tariffSelected = document.getElementById('apiship_selectPvzTariff_' + pvzId).value;
    },

    choozePVZ: function(pvzId,isAjax){
		if (typeof IPOLapiship_pvz.PVZ[pvzId] == 'undefined')
			return;

		/* Case: widget reopening with same pvz selected */
        if (IPOLapiship_pvz.Y_map) {
            IPOLapiship_pvz.currentZoom = IPOLapiship_pvz.Y_map.getZoom();
        }

		IPOLapiship_pvz.pvzAdress = IPOLapiship_pvz.city + ", " + IPOLapiship_pvz.PVZ[pvzId]['Address'] + " #S" + IPOLapiship_pvz.PVZ[pvzId].id;

        /* Store info to hidden inputs */
        IPOLapiship_pvz.pvzId                = pvzId;
		IPOLapiship_pvz.chosenPVZProviderKey = IPOLapiship_pvz.PVZ[pvzId].providerKey;
		IPOLapiship_pvz.chosenPVZProviderID  = IPOLapiship_pvz.PVZ[pvzId].code;

        let tariffKey = (typeof(IPOLapiship_pvz.PVZ[pvzId].tariffSelected) !== 'undefined') ? IPOLapiship_pvz.PVZ[pvzId].tariffSelected : IPOLapiship_pvz.PVZ[pvzId].tariffBest;
        let tariff = IPOLapiship_pvz.pointTariffs.tariffs[IPOLapiship_pvz.chosenPVZProviderKey][tariffKey];

        //console.log({pvzId, tariffKey, tariff});

		IPOLapiship_pvz.chosenTariffID         = tariff.tariffId;
        IPOLapiship_pvz.chosenTariffPickupType = tariff.tariffPickupType;
		
		IPOLapiship_pvz.UpdateChosenInputs();
		IPOLapiship_pvz.ChangeLabelHTML();

		/* Unmake KLADR module address form if shown */
		if (typeof(KladrJsObj) != 'undefined')
			KladrJsObj.FuckKladr();		
				
		IPOLapiship_pvz.markUnable(pvzId);
		
		/* Order form reload if this is not AJAX call */
		if (typeof isAjax == 'undefined') {
			if (typeof IPOLapiship_DeliveryChangeEvent == 'function') {
                IPOLapiship_DeliveryChangeEvent();
            } else {
                if (IPOLapiship_pvz.oldTemplate) {
                    let deliveryId;
                    if (IPOLapiship_pvz.isConverted) {
                        for (let i in IPOLapiship_pvz.deliveries) {
                            /* Crutch */
                            deliveryId = IPOLapiship_pvz.makeHTMLId(i);
                            break;
                        }
                    } else {
                        deliveryId = IPOLapiship_pvz.makeHTMLId('apiship_pickup');
                    }
                    document.getElementById(deliveryId).checked = true;
                    document.getElementById(deliveryId).dispatchEvent(new Event('click'));
                } else {
                    BX.Sale.OrderAjaxComponent.sendRequest();
                }
            }
		}

		IPOLapiship_pvz.close(true);
	},
	
	markUnable: function(pvzId){
		var chznPnkt = false;
		
		for (var i in IPOLapiship_pvz.pvzInputs) {
			if (typeof(IPOLapiship_pvz.pvzInputs[i]) === 'function') 
				continue;

            chznPnkt = document.getElementById('ORDER_PROP_' + IPOLapiship_pvz.pvzInputs[i]);
			if (!IPOLapiship_pvz.CheckType(chznPnkt) || chznPnkt.tagName !== 'INPUT')
                chznPnkt = document.querySelector('[name="ORDER_PROP_' + IPOLapiship_pvz.pvzInputs[i] + '"]');
	
			if (IPOLapiship_pvz.CheckType(chznPnkt)) {
				if (pvzId) {
					/* PVZ has been selected earlier - set it address */
					chznPnkt.value = IPOLapiship_pvz.pvzAdress;
                    chznPnkt.innerHTML = IPOLapiship_pvz.pvzAdress;
				}

                chznPnkt.style["background-color"] = '#eee';
                chznPnkt.setAttribute('readonly','readonly');
				break;
			}
		}		
	},
	
	ChangeLabelHTML: function(){
		var tmpHTML = "<div class='apiship_pvzLair'>" + IPOLapiship_pvz.button;

		if (IPOLapiship_pvz.pvzId) {
			tmpHTML += "<br><span class='apiship_pvzAddr'>" + IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId].Address + "</span>";
		}
		tmpHTML += "</div>";

		if (typeof IPOLapiship_pvz.pvzLabel == "object") {
            if (IPOLapiship_pvz.oldTemplate) {
                IPOLapiship_pvz.pvzLabel.innerHTML = tmpHTML;
            } else if (IPOLapiship_pvz.isPickUpChecked) {
                IPOLapiship_pvz.pvzLabel.innerHTML = tmpHTML;
                document.getElementById('apiship_selectPVZ').classList.add('btn', 'btn-default');
            }
        }
	},
	
	UpdateChosenInputs: function(){
        var orderForm = document.getElementById(IPOLapiship_pvz.orderForm);
		if (!IPOLapiship_pvz.LoadInputsFromAJAX) {
            let inputs = [
                /* name, value */
                ['apiship_providerKey_first',      IPOLapiship_pvz.chosenPVZProviderKey],
                ['apiship_pvzProviderID_first',    IPOLapiship_pvz.chosenPVZProviderID],
                ['apiship_pvzID_first',            IPOLapiship_pvz.pvzId],
                ['apiship_tariffId_first',         IPOLapiship_pvz.chosenTariffID],
                ['apiship_tariffPickupType_first', IPOLapiship_pvz.chosenTariffPickupType],
                ['soa-property-75', IPOLapiship_pvz.chosenPVZProviderKey]
            ];

            for (let i in inputs) {
                if (!IPOLapiship_pvz.CheckType(document.getElementById(inputs[i][0]))) {
                    let input = document.createElement('input');
                    input.id    = inputs[i][0];
                    input.type  = 'hidden';
                    input.name  = inputs[i][0];
                    input.value = inputs[i][1];
                    orderForm.append(input);
                } else {
                    document.getElementById(inputs[i][0]).value = inputs[i][1];
                }
            }
			IPOLapiship_pvz.LoadInputsFromAJAX = true;
		} else {
			if (IPOLapiship_pvz.oldTemplate) {
				let inputs = [
                    ['apiship_providerKey',      IPOLapiship_pvz.chosenPVZProviderKey],
                    ['apiship_pvzProviderID',    IPOLapiship_pvz.chosenPVZProviderID],
                    ['apiship_pvzID',            IPOLapiship_pvz.pvzId],
                    ['apiship_tariffId',         IPOLapiship_pvz.chosenTariffID],
                    ['apiship_tariffPickupType', IPOLapiship_pvz.chosenTariffPickupType],
                    ['soa-property-75', IPOLapiship_pvz.chosenPVZProviderKey]
                ];

                for (let i in inputs) {
                    if (!IPOLapiship_pvz.CheckType(document.getElementById(inputs[i][0]))) {
                        let input   = document.createElement('input');
                        input.type  = 'hidden';
                        input.id    = inputs[i][0];
                        input.name  = inputs[i][0];
                        input.value = inputs[i][1];
                        orderForm.append(input);
                    }
                    /* else document.getElementById(inputs[i][0]).value = inputs[i][1]; */
                }

				if (IPOLapiship_pvz.chosenPVZProviderKey)
                {
					document.getElementById('apiship_providerKey').value = IPOLapiship_pvz.chosenPVZProviderKey;
					document.getElementById('soa-property-75').value = IPOLapiship_pvz.chosenPVZProviderKey;
                }
				else
                {
                    document.getElementById('apiship_providerKey').value = '';
					document.getElementById('soa-property-75').value = '';
                }

				if (IPOLapiship_pvz.pvzId) {
                    document.getElementById('apiship_pvzID').value = IPOLapiship_pvz.pvzId;
                    document.getElementById('apiship_pvzProviderID').value = IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId].code;
				} else {
                    document.getElementById('apiship_pvzID').value = '';
                    document.getElementById('apiship_pvzProviderID').value = '';
				}

				if (IPOLapiship_pvz.chosenTariffID)
                    document.getElementById('apiship_tariffId').value = IPOLapiship_pvz.chosenTariffID;
				else
                    document.getElementById('apiship_tariffId').value = '';

                if (IPOLapiship_pvz.chosenTariffPickupType)
                    document.getElementById('apiship_tariffPickupType').value = IPOLapiship_pvz.chosenTariffPickupType;
                else
                    document.getElementById('apiship_tariffPickupType').value = '';
			} else {
                let inputs = [
                    ['apiship_providerKey',      IPOLapiship_pvz.chosenPVZProviderKey],
                    ['apiship_pvzProviderID',    IPOLapiship_pvz.chosenPVZProviderID],
                    ['apiship_pvzID',            IPOLapiship_pvz.pvzId],
                    ['apiship_tariffId',         IPOLapiship_pvz.chosenTariffID],
                    ['apiship_tariffPickupType', IPOLapiship_pvz.chosenTariffPickupType],
                    ['soa-property-75', IPOLapiship_pvz.chosenPVZProviderKey]
                ];

                for (let i in inputs) {
                    if (!IPOLapiship_pvz.CheckType(document.getElementById(inputs[i][0]))) {
                        let input = document.createElement('input');
                        input.id    = inputs[i][0];
                        input.type  = 'hidden';
                        input.name  = inputs[i][0];
                        input.value = inputs[i][1];
                        orderForm.append(input);
                    } else {
                        document.getElementById(inputs[i][0]).value = inputs[i][1];
                    }
                }
			}
			document.querySelector('[name=apiship_providerKey_first]').value = '';
			document.querySelector('[name=apiship_pvzProviderID_first]').value = '';
			document.querySelector('[name=apiship_pvzID_first]').value = '';
			document.querySelector('[name=apiship_tariffId_first]').value = '';
			document.querySelector('[name=apiship_tariffPickupType_first]').value = '';
		}
	},
	
	markChosenPVZ: function(id){
        let chosen = document.querySelector('.apiship_chosen');
        let chosenId = IPOLapiship_pvz.CheckType(chosen) ? chosen.id : '';

        if (chosenId !== 'PVZ_' + id) {
            IPOLapiship_pvz.Y_selectPVZ(id);
        }
    },

    openListPVZ: function(el){
        if (el.classList.contains('active')) {
            el.classList.remove('active');
            el.nextSibling.classList.remove('active');
        } else {
            el.classList.add('active');
            el.nextSibling.classList.add('active');
        }
    },
	
	close: function(){
        document.body.style.overflow = 'auto';
		document.getElementById('apiship_pvz').style.display = 'none';
        document.getElementById('apiship_mask').style.display = 'none';
		IPOLapiship_pvz.isActive = false;
	},

    getDayEnd: function(daysMin, daysMax){
        var days, label, lst;

        daysMin = parseInt(daysMin);
        daysMax = parseInt(daysMax);

        if (daysMin === 0)
            daysMin = 1;
        if (daysMax === 0)
            daysMax = 1;
        if (daysMin === daysMax)
            days = daysMin;
        else
            days = daysMin + " - " + daysMax;

        if (daysMax > 4 && daysMax < 21 || daysMax === 0) {
            label = '<?=GetMessage('IPOLapiship_DELIVERY_PERIOD_DAYS')?>';
        } else {
            lst = daysMax % 10;
            if (lst === 1) {
                label = '<?=GetMessage('IPOLapiship_DELIVERY_PERIOD_DAY')?>';
            } else if (lst < 5) {
                label = '<?=GetMessage('IPOLapiship_DELIVERY_PERIOD_DAYA')?>';
            } else {
                label = '<?=GetMessage('IPOLapiship_DELIVERY_PERIOD_DAYS')?>';
            }
        }

        return days + ' ' + label;
    },

	Y_init: function(){
		if (typeof IPOLapiship_pvz.city == 'undefined')
			IPOLapiship_pvz.city = '<?=GetMessage('IPOLapiship_FRNT_MOSCOW')?>';
		
		var pvzCoords = IPOLapiship_pvz.Y_getPVZCenters();

        if (pvzCoords) {
            IPOLapiship_pvz.Y_initCityMap(pvzCoords);
		} else {		
			ymaps.geocode("<?=GetMessage("IPOLapiship_RUSSIA")?>, " + IPOLapiship_pvz.city, {
				results: 1
			}).then(function (res){
                var firstGeoObject = res.geoObjects.get(0);
				var coords = firstGeoObject.geometry.getCoordinates();

				IPOLapiship_pvz.Y_initCityMap(coords);
			});
		}
	},
	
	Y_initCityMap: function(coords){
		coords[1] -= 0.2;
		if (!IPOLapiship_pvz.Y_map) {
			IPOLapiship_pvz.Y_map = new ymaps.Map("apiship_map", {
				zoom: 10,
				controls: [],
				center: coords
			});
			var ZK = new ymaps.control.ZoomControl({
				options: {
					position: {
						right: 10,
						top:  146
					}
				}
			});
			IPOLapiship_pvz.Y_map.controls.add(ZK);

			if (IPOLapiship_pvz.widgetSearch) {
				var searchControlParams = {float: 'left', floatIndex: 100, top: 30, noPlacemark: true};
				if (IPOLapiship_pvz.widgetSearchMark)
					searchControlParams.noPlacemark = false;
			
				IPOLapiship_pvz.Y_map.controls.add('searchControl', searchControlParams);
				IPOLapiship_pvz.Y_map.controls.events.add('resultshow', IPOLapiship_pvz.Y_zoomCalibrate, IPOLapiship_pvz.Y_map.controls.get('searchControl'));
			}
		} else {
			IPOLapiship_pvz.Y_map.setCenter(coords);
			IPOLapiship_pvz.Y_map.setZoom(10);
		}
		
		IPOLapiship_pvz.Y_clearPVZ();
		IPOLapiship_pvz.Y_markPVZ();		
	},
	
	Y_getPVZCenters: function(){
		var ret = [0,0,0];

		for (var i in IPOLapiship_pvz.PVZ) {
			if (
				typeof(IPOLapiship_pvz.PVZ[i].cX) !== 'undefined' &&
				typeof(IPOLapiship_pvz.PVZ[i].cY) !== 'undefined' &&
				IPOLapiship_pvz.PVZ[i].cX && IPOLapiship_pvz.PVZ[i].cY
			) {
				ret[0] += parseFloat(IPOLapiship_pvz.PVZ[i].cY);
				ret[1] += parseFloat(IPOLapiship_pvz.PVZ[i].cX);
				ret[2]++;
			}
		}

		if (ret[2]) {
			ret[0] /= ret[2];
			ret[1] /= ret[2];
			ret.pop();
			return ret;
		} else {
			return false;
		}
	},
	
	Y_zoomCalibrate: function(){
		while (!ymaps.geoQuery(map.geoObjects).searchInside(IPOLapiship_pvz.Y_map).getLength() && IPOLapiship_pvz.Y_map.getZoom() > 4) {
			IPOLapiship_pvz.Y_map.setZoom(IPOLapiship_pvz.Y_map.getZoom() - 1);
		}
	},

	Y_markPVZ: function(selectedProvider=''){
        var geoMarks = [];

        IPOLapiship_pvz.clusterer = new ymaps.Clusterer({
            gridSize: 64,
            preset: 'islands#ClusterIcons',
            clusterIconColor: '#337AB7',
            hasBalloon: false,
            groupByCoordinates: false,
            clusterDisableClickZoom: false,
            maxZoom: 13,
            zoomMargin: [45],
            clusterHideIconOnBalloonOpen: false,
            geoObjectHideIconOnBalloonOpen: false
        });

		for (var i in IPOLapiship_pvz.PVZ) {
			var baloonHTML = "";
			var pointText  = "";
            let providerKey = IPOLapiship_pvz.PVZ[i].providerKey;

            if(selectedProvider != '' && selectedProvider != providerKey) {
                continue;
            }

            /* Provider logo */
			if (typeof IPOLapiship_pvz.arImages[providerKey] != "undefined") {
			    var src = IPOLapiship_pvz.image_url + IPOLapiship_pvz.arImages[providerKey];
                baloonHTML += '<img alt="' + providerKey + '" class="apiship_provider_baloon_img" src="' + src + '" data-src="' + src + '">';
            } else
				baloonHTML += "<div class='apiship_provider_baloon_img'>" + providerKey + "</div>";
			
			baloonHTML += "<div id='apiship_baloon'>";
			baloonHTML += "<div class='apiship_iAdress'>";
			
			if (IPOLapiship_pvz.PVZ[i].color)
				baloonHTML += "<span style='color:" + IPOLapiship_pvz.PVZ[i].color + "'>"
			
			baloonHTML += IPOLapiship_pvz.PVZ[i].Address;
			
			if (IPOLapiship_pvz.PVZ[i].color)
				baloonHTML += "</span>";
			
			baloonHTML += "</div>";

			if (IPOLapiship_pvz.PVZ[i].Phone)
				baloonHTML += "<div><div class='apiship_iTelephone apiship_icon'></div><div class='apiship_baloonDiv'>"+IPOLapiship_pvz.PVZ[i].Phone+"</div><div style='clear:both'></div></div>";
			if (IPOLapiship_pvz.PVZ[i].WorkTime)
				baloonHTML += "<div><div class='apiship_iTime apiship_icon'></div><div class='apiship_baloonDiv'>"+IPOLapiship_pvz.PVZ[i].WorkTime+"</div><div style='clear:both'></div></div>";
            if (parseInt(IPOLapiship_pvz.PVZ[i].fittingRoom) === 1)
                baloonHTML += "<div><div class='apiship_iFitting apiship_icon'></div><div class='apiship_baloonDiv'><?=GetMessage("IPOLapiship_BALOON_FITTING")?></div><div style='clear:both'></div></div>";
            if (IPOLapiship_pvz.widgetPointFeatureCOD) {
                let isPaymentCash = parseInt(IPOLapiship_pvz.PVZ[i].paymentCash) === 1;
                let isPaymentCard = parseInt(IPOLapiship_pvz.PVZ[i].paymentCard) === 1;

                if (isPaymentCash || isPaymentCard) {
                    baloonHTML += "<div>";

                    if (isPaymentCash)
                        baloonHTML += "<div class='apiship_iCash apiship_icon'></div><div class='apiship_baloonDiv'><?=GetMessage("IPOLapiship_BALOON_PAYMENT_CASH")?></div><div style='clear:both'></div>";
                    if (isPaymentCard)
                        baloonHTML += "<div class='apiship_iCard apiship_icon'></div><div class='apiship_baloonDiv'><?=GetMessage("IPOLapiship_BALOON_PAYMENT_CARD")?></div><div style='clear:both'></div>";

                    baloonHTML += "</div>";
                }
            }
			
			if (IPOLapiship_pvz.widgetPointFeatureType || IPOLapiship_pvz.widgetPointFeatureCOD) {
				baloonHTML += "<div class='apiship_baloonDiv_features'>";			
				/* Point type */
				if (IPOLapiship_pvz.widgetPointFeatureType) {
					switch (parseInt(IPOLapiship_pvz.PVZ[i].type)) {
						case 1: pointText = '<?=GetMessage("IPOLapiship_BALOON_F_PVZ")?>'; break;
						case 2: pointText = '<?=GetMessage("IPOLapiship_BALOON_F_POSTAMAT")?>'; break;
						case 3: pointText = '<?=GetMessage("IPOLapiship_BALOON_F_POST")?>'; break;
						case 4: pointText = '<?=GetMessage("IPOLapiship_BALOON_F_TERMINAL")?>'; break;				
					}
					if (pointText)
						baloonHTML += "<div class='apiship_baloonDiv_feature'>" + pointText + "</div>";
				}
				/* COD */
				if (IPOLapiship_pvz.widgetPointFeatureCOD) {
					if (parseInt(IPOLapiship_pvz.PVZ[i].cod) === 1)
						baloonHTML += "<div class='apiship_baloonDiv_feature'><?=GetMessage("IPOLapiship_BALOON_F_COD")?></div>";
					else
						baloonHTML += "<div class='apiship_baloonDiv_feature not_available'><?=GetMessage("IPOLapiship_BALOON_F_NO_COD")?></div>";
				}
								
				baloonHTML += "<div style='clear:both'></div></div>";
			}

            let selectedTariffKey = (typeof(IPOLapiship_pvz.PVZ[i].tariffSelected) !== 'undefined') ? IPOLapiship_pvz.PVZ[i].tariffSelected : IPOLapiship_pvz.PVZ[i].tariffBest;
            let selectedTariff = IPOLapiship_pvz.pointTariffs.tariffs[providerKey][selectedTariffKey];
            if (IPOLapiship_pvz.widgetNoTariffSel) {
                /* Just text, best tariff selected */
                baloonHTML += "<div><div class='apiship_baloonDiv'><b><?=GetMessage("IPOLapiship_BALOON_DELIVERY_COST")?>" + selectedTariff.deliveryCost + "<?=GetMessage("IPOLapiship_CURRENCY_RUB")?></b></div><div style='clear:both'></div></div>";
                baloonHTML += "<div><div class='apiship_baloonDiv'><b><?=GetMessage("IPOLapiship_BALOON_DELIVERY_DAYS")?>" + IPOLapiship_pvz.getDayEnd(selectedTariff.daysMin, selectedTariff.daysMax) + "</b></div><div style='clear:both'></div></div>";
            } else {
                /* Tariff selector */
                baloonHTML += "<div><div class='apiship_baloonDiv'><p><b><?=GetMessage('IPOLapiship_BALOON_DELIVERY_TERMS')?></b></p><select id='apiship_selectPvzTariff_" + i + "' onchange='IPOLapiship_pvz.selectTariff(\"" + i + "\")'>";

                for (let t in IPOLapiship_pvz.PVZ[i].tariffs) {
                    let tariff = IPOLapiship_pvz.pointTariffs.tariffs[providerKey][IPOLapiship_pvz.PVZ[i].tariffs[t]];

                    baloonHTML += "<option value = '"+ IPOLapiship_pvz.PVZ[i].tariffs[t] +"'";
                    if (IPOLapiship_pvz.PVZ[i].tariffs[t] === selectedTariffKey)
                        baloonHTML += " selected";
                    baloonHTML += ">" + tariff.deliveryCost + "<?=GetMessage('IPOLapiship_CURRENCY_RUB')?>" + " - " + tariff.tariffName + ", " + IPOLapiship_pvz.getDayEnd(tariff.daysMin, tariff.daysMax);
                    baloonHTML += "</option>";
                }
                baloonHTML += "</select></div><div style='clear:both'></div></div>";
            }
			
			baloonHTML += "<div><a id='apiship_button' href='javascript:void(0)' onclick='IPOLapiship_pvz.choozePVZ(\""+i+"\")'></a></div>";
			baloonHTML += "</div>";

			IPOLapiship_pvz.PVZ[i].placeMark = new ymaps.Placemark([IPOLapiship_pvz.PVZ[i].cY,IPOLapiship_pvz.PVZ[i].cX], {
				balloonContent: baloonHTML
			}, {
				iconLayout: 'default#image',
                iconImageHref: IPOLapiship_pvz.getInactiveIcon(providerKey),
				iconImageSize: [40, 43],
				iconImageOffset: [-20, -43],
			});
			IPOLapiship_pvz.PVZ[i].placeMark.link = i;
			IPOLapiship_pvz.PVZ[i].placeMark.events.add('balloonopen', function(mark){
			    var previousMark = IPOLapiship_pvz.currentMark;

                IPOLapiship_pvz.currentMark = mark.get('target');
                /* Add previous mark back to cluster */
                if (typeof(previousMark) === 'object' && previousMark.link !== IPOLapiship_pvz.currentMark.link && typeof(IPOLapiship_pvz.PVZ[previousMark.link]) !== 'undefined') {
                    IPOLapiship_pvz.Y_map.geoObjects.remove(IPOLapiship_pvz.PVZ[previousMark.link].placeMark);
                    IPOLapiship_pvz.clusterer.add(IPOLapiship_pvz.PVZ[previousMark.link].placeMark);
                }

                document.querySelectorAll('.apiship_chosen').forEach(function(el){
                    el.classList.remove('apiship_chosen');
                });
                document.getElementById('PVZ_' + IPOLapiship_pvz.currentMark.link).classList.add('apiship_chosen');
                // document.getElementById('apiship_info').classList.remove('expanded');

                /* Restore selected tariff if chosen before */
                if (typeof(IPOLapiship_pvz.PVZ[IPOLapiship_pvz.currentMark.link].tariffSelected) !== 'undefined' && !IPOLapiship_pvz.widgetNoTariffSel) {
                    document.getElementById('apiship_selectPvzTariff_' + IPOLapiship_pvz.currentMark.link).value = IPOLapiship_pvz.PVZ[IPOLapiship_pvz.currentMark.link].tariffSelected;
                }
			});

            IPOLapiship_pvz.PVZ[i].placeMark.events.add('balloonclose', function(mark){
                document.querySelectorAll('.apiship_chosen').forEach(function(th){
                    th.classList.remove('apiship_chosen');
                });
            });

            geoMarks.push(IPOLapiship_pvz.PVZ[i].placeMark);
		}
        IPOLapiship_pvz.clusterer.add(geoMarks);
        IPOLapiship_pvz.Y_map.geoObjects.add(IPOLapiship_pvz.clusterer);
    },

    getInactiveIcon: function(provider){
        var href;

        switch(IPOLapiship_pvz.widgetPlacemarkIcon.mode) {
            case 'I':
            default:
                href = IPOLapiship_pvz.widgetPlacemarkIcon.defaultIcon.inactive;
                break;
            case 'C':
                if (typeof(IPOLapiship_pvz.widgetPlacemarkIcon.icons[provider]) !== 'undefined' && typeof(IPOLapiship_pvz.widgetPlacemarkIcon.icons[provider].inactive) !== 'undefined')
                    href = IPOLapiship_pvz.widgetPlacemarkIcon.icons[provider].inactive;
                else
                    href = IPOLapiship_pvz.widgetPlacemarkIcon.defaultIcon.inactive;
                break;
        }

        return href;
    },

	Y_selectPVZ: function(wat){
        try {
            /* Case: mobile FF doesn't trigger onmouseover and placemark continue to stay clustered */
            var placemarkState = IPOLapiship_pvz.clusterer.getObjectState(IPOLapiship_pvz.PVZ[wat].placeMark);
            if (placemarkState.isClustered) {
                IPOLapiship_pvz.clusterer.remove(IPOLapiship_pvz.PVZ[wat].placeMark);
                IPOLapiship_pvz.Y_map.geoObjects.add(IPOLapiship_pvz.PVZ[wat].placeMark);
            }

            IPOLapiship_pvz.PVZ[wat].placeMark.balloon.open([IPOLapiship_pvz.PVZ[wat].cY, IPOLapiship_pvz.PVZ[wat].cX]);
        } catch(ex) {
            console.log(ex);
        }
	},
	
	Y_blinkPVZ: function(wat, ifOn){
        if (IPOLapiship_pvz.CheckType(document.querySelector('.apiship_chosen'))) {
            if (document.querySelector('.apiship_chosen').attributes.getNamedItem('id').value === 'PVZ_' + wat) {
                /* Restore placemark icon */
                if (!(typeof(ifOn) !== 'undefined' && ifOn)) {
                    IPOLapiship_pvz.PVZ[wat].placeMark.options.set({iconImageHref: IPOLapiship_pvz.getInactiveIcon(IPOLapiship_pvz.PVZ[wat].providerKey)});
                }
                return;
            }
        }

        if (typeof(ifOn) != 'undefined' && ifOn) {
            IPOLapiship_pvz.clusterer.remove(IPOLapiship_pvz.PVZ[wat].placeMark);
            IPOLapiship_pvz.Y_map.geoObjects.add(IPOLapiship_pvz.PVZ[wat].placeMark);
            IPOLapiship_pvz.PVZ[wat].placeMark.options.set({iconImageHref: IPOLapiship_pvz.widgetPlacemarkIcon.defaultIcon.active});
        } else {
            IPOLapiship_pvz.PVZ[wat].placeMark.options.set({iconImageHref: IPOLapiship_pvz.getInactiveIcon(IPOLapiship_pvz.PVZ[wat].providerKey)});
            IPOLapiship_pvz.Y_map.geoObjects.remove(IPOLapiship_pvz.PVZ[wat].placeMark);
            IPOLapiship_pvz.clusterer.add(IPOLapiship_pvz.PVZ[wat].placeMark);
        }
	},
	
	Y_clearPVZ: function(){
		if (typeof(IPOLapiship_pvz.Y_map.geoObjects.removeAll) !== 'undefined' && false) {
            IPOLapiship_pvz.Y_map.geoObjects.removeAll();
        } else {
			do {
				IPOLapiship_pvz.Y_map.geoObjects.each(function(e){
					IPOLapiship_pvz.Y_map.geoObjects.remove(e);
				});
			} while(IPOLapiship_pvz.Y_map.geoObjects.getBounds());
		}
	},

	CheckChosenPickUp: function(ajaxAns){
        let deliveryId, deliveryInput;
        if (IPOLapiship_pvz.isConverted) {
            IPOLapiship_pvz.isPickUpChecked = false;

            for (let i in IPOLapiship_pvz.deliveries) {
                deliveryId = IPOLapiship_pvz.makeHTMLId(i);
                deliveryInput = document.getElementById(deliveryId);
                if (IPOLapiship_pvz.CheckType(deliveryInput)) {
                    if (deliveryInput.checked) {
                        IPOLapiship_pvz.isPickUpChecked = true;
                    }
                }
            }
        } else {
            deliveryId = IPOLapiship_pvz.makeHTMLId('apiship_pickup');
            deliveryInput = document.getElementById(deliveryId);
            if (IPOLapiship_pvz.CheckType(deliveryInput))
                IPOLapiship_pvz.isPickUpChecked = deliveryInput.checked;
        }

        if (typeof ajaxAns != "undefined" && ajaxAns !== null) {
			if (typeof ajaxAns.order != "undefined")  {
                if (typeof ajaxAns.order.DELIVERY != "undefined") {
                    let deliveries = ajaxAns.order.DELIVERY,
                        pickUpFinded = false;
                    for (let i in deliveries) {
                        for (let j in IPOLapiship_pvz.deliveries) {
                            if (deliveries[i]["ID"] === j)
                                pickUpFinded = true;
                        }
                    }

                    if (!pickUpFinded)
                        IPOLapiship_pvz.isPickUpChecked = false;
                }
            }
        }
	},
	
	ChooseFirstPVZ: function(){
		if (IPOLapiship_pvz.pvzId !== false) {
            IPOLapiship_pvz.choozePVZ(IPOLapiship_pvz.pvzId);
        } else {
			var firstPVZ = false;
			for (var i in IPOLapiship_pvz.PVZ) {
				firstPVZ = i;
				break;
			}
			if (firstPVZ !== false)
				IPOLapiship_pvz.choozePVZ(firstPVZ);
		}	
	},

    ymapsCheckerCntr: 0,
    ymapsCheck: function(){
        if (IPOLapiship_pvz.ymapsCheckerCntr >= 50) {
            console.log('ipol.apiship2v: Yandex Maps API is still undefined after ' + IPOLapiship_pvz.ymapsCheckerCntr + ' checks.');
            return;
        }
        if ((typeof(ymaps) === 'undefined') || (typeof(ymaps.ready) === 'undefined')) {
            IPOLapiship_pvz.ymapsCheckerCntr++;
            setTimeout(IPOLapiship_pvz.ymapsCheck, 100);
        } else {
            ymaps.ready(IPOLapiship_pvz.init);
        }
    },

	checkReady: function(){
        IPOLapiship_pvz.ymapsCheck();
        window.onresize = function(){IPOLapiship_pvz.resize();};
	}
};

IPOLapiship_pvz.checkReady();
</script>

<div id='apiship_pvz'>
    <div id='apiship_pvz_container'>
        <div id='apiship_sidebar'></div>
        <div id='apiship_map'></div>
        <!-- <div class="apiship-closer-container">   -->
            <div id='apiship_closer' onclick='IPOLapiship_pvz.close()'></div>
        <!-- </div> -->
    </div>
</div>
<!-- <script data-skip-moving="true">
    document.getElementById('apiship_sign').onclick = function(){
        if (document.getElementById('apiship_sign').parentNode.classList.contains('expanded'))
            document.getElementById('apiship_sign').parentNode.classList.remove('expanded');
        else
            document.getElementById('apiship_sign').parentNode.classList.add('expanded')
    }
</script> -->