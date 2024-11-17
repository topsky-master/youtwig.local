<script data-skip-moving="true">
    /* Search for first parent with position:relative */
    function findRelativeParent(obj){
        let calcStyles = getComputedStyle(obj);

        if ((obj.tagName == 'body') || (obj.tagName == 'BODY'))
            return false;
        if (calcStyles.position === 'relative')
            return obj;
        else
            return findRelativeParent(obj.parentNode);
    }

    var test = "test";

    var IPOLapiship_pvz = {
        city:      '<?=$arResult['cityName']?>',
        pvzInputs: [<?=substr($arResult['propAddr'], 0, -1)?>], /* Inputs for PVZ address */
        pvzLabel:  "",
        presizion: 2,
        PVZ: {},
        DefaultVals: {},
        LoadFromAJAX: false,
        LoadInputsFromAJAX: false,
        image_url: "<?=apishipHelper2v::getProviderIconsURL()?>",
        arImages:  <?=CUtil::PHPToJSObject(apishipHelper2v::getProvderIcons())?>,
        newLocation:      false,
        isConverted:      <?=CUtil::PHPToJSObject($arResult['isConverted']);?>,
        widgetSearch:     <?=CUtil::PHPToJSObject($arResult['widgetSearch']);?>,
        widgetSearchMark: <?=CUtil::PHPToJSObject($arResult['widgetSearchMark']);?>,
        widgetPointFeatureType: <?=CUtil::PHPToJSObject($arResult['widgetPointFeatureType']);?>,
        widgetPointFeatureCOD:  <?=CUtil::PHPToJSObject($arResult['widgetPointFeatureCOD']);?>,
        widgetPlacemarkIcon:    <?=CUtil::PHPToJSObject($arResult['widgetPlacemarkIcon']);?>,
        Y_map:          false,
        clusterer:      false,
        currentMark:    false,
        Y_markedCities: {},

        CheckType: function(element){
            if ((typeof element == 'undefined') || (element == 'undefined') || (element == null))
                return false;
            return true;
        },

        GetDefaultVals: function(){
            IPOLapiship_pvz.DefaultVals = <?=CUtil::PHPToJSObject($arResult["defaultVals"])?>;
        },

        GetPVZ: function(){
            IPOLapiship_pvz.PVZ = <?=CUtil::PHPToJSObject($arResult["PVZ"])?>;
        },

        GetAjaxPVZ: function(){
            if (IPOLapiship_pvz.CheckType(document.getElementById('ipolapiship_pvz_list_tag_ajax')))
                IPOLapiship_pvz.PVZ = JSON.parse(document.getElementById('ipolapiship_pvz_list_tag_ajax').innerHTML);

            /* Drop selected PVZ if city changes */
            if (typeof IPOLapiship_pvz.PVZ[IPOLapiship_pvz.pvzId] == "undefined") {
                IPOLapiship_pvz.pvzId = false;
                IPOLapiship_pvz.chosenPVZProviderKey = false;
                IPOLapiship_pvz.chosenTariffID = false;

                IPOLapiship_pvz.UpdateChosenInputs();
            }
        },

        GetAjaxDefaultVals: function(){
            if (IPOLapiship_pvz.CheckType(document.getElementById('ipolapiship_default_vals_tag_ajax')))
                IPOLapiship_pvz.DefaultVals = JSON.parse(document.getElementById('ipolapiship_default_vals_tag_ajax').innerHTML);
        },

        init: function(){
            IPOLapiship_pvz.Y_init();
            IPOLapiship_pvz.onLoad();
        },

        onLoad: function(){
            /* First time get data from component, after that take it from AJAX SOA answers */
            if (!IPOLapiship_pvz.LoadFromAJAX) {
                IPOLapiship_pvz.GetPVZ();
                IPOLapiship_pvz.GetDefaultVals();
                IPOLapiship_pvz.LoadFromAJAX = true;
            }
            IPOLapiship_pvz.initCityPVZ();
        },

        selectPVZ: function(){
            if (!IPOLapiship_pvz.isActive) {
                IPOLapiship_pvz.isActive = true;

                var hndlr = document.getElementById('apiship_pvz');

                var elementStyles = getComputedStyle(hndlr);
                var parent        = findRelativeParent(hndlr);
                var shiftWidth = 0, shiftHeight = 0;
                if (typeof parent == "object") {
                    shiftWidth = parent.offsetLeft;
                    shiftHeight = parent.offsetTop;
                }

                hndlr.style.display = 'block';
                hndlr.style.left = (((window.innerWidth-elementStyles.width)/2) - shiftWidth)+'px';
                hndlr.style.top = ((window.innerHeight-elementStyles.height)/2 + window.scrollY - shiftHeight) + 'px';

                IPOLapiship_pvz.initCityPVZ();
                IPOLapiship_pvz.Y_init();
                IPOLapiship_pvz.Y_map.container.fitToViewport();
            }
        },

        initCityPVZ: function(){
            var city = IPOLapiship_pvz.city;
            var cnt = [];

            IPOLapiship_pvz.cityPVZ = IPOLapiship_pvz.PVZ;
            IPOLapiship_pvz.cityPVZHTML();
            IPOLapiship_pvz.multiPVZ = (IPOLapiship_pvz.PVZ.length !== 1);
        },

        cityPVZHTML: function(){
            let html = '';
            let pickupPoints = {};

            for (let i in IPOLapiship_pvz.cityPVZ) {
                if (typeof pickupPoints[IPOLapiship_pvz.cityPVZ[i].providerKey] === "undefined") {
                    pickupPoints[IPOLapiship_pvz.cityPVZ[i].providerKey] = [];
                }

                pickupPoints[IPOLapiship_pvz.cityPVZ[i].providerKey].push(IPOLapiship_pvz.cityPVZ[i]);
            }

            for (let providerKey in pickupPoints) {
                pickupPoints[providerKey].sort(IPOLapiship_pvz.sortPVZ);
            }

            for (let providerKey in pickupPoints) {
                let headBlock = "";
                let contentBlock = "";

                headBlock += "<div onclick='IPOLapiship_pvz.openListPVZ(this)' class='apiship_delivInfo'>";

                if (typeof IPOLapiship_pvz.arImages[providerKey] != "undefined") {
                    let src = IPOLapiship_pvz.image_url + IPOLapiship_pvz.arImages[providerKey];
                    headBlock += '<img alt="' + providerKey + '" class="apiship_provider_img" src="' + src + '" data-src="' + src + '">';
                } else
                    headBlock += providerKey;

                console.log(pickupPoints[providerKey]);

                headBlock += "<span class='apiship_delivTerms'>" + pickupPoints[providerKey][0].deliveryCost + "<?=GetMessage("IPOLapiship_CURRENCY_RUB")?>, " + IPOLapiship_pvz.getDayEnd(pickupPoints[providerKey][0].daysMin, pickupPoints[providerKey][0].daysMax) + "</span>";
                headBlock += "<div class='apiship_delivInfo_arrow'><span></span></div>";
                headBlock += "</div>";

                html += headBlock;

                for (let i in pickupPoints[providerKey]) {
                    let id = pickupPoints[providerKey][i].id;
                    contentBlock += '<p id="PVZ_' + id + '" onclick="IPOLapiship_pvz.markChosenPVZ(\'' + id + '\')" onmouseover="IPOLapiship_pvz.Y_blinkPVZ(\'' + id + '\', true)" onmouseout="IPOLapiship_pvz.Y_blinkPVZ(\'' + id + '\')">' + IPOLapiship_pvz.paintPVZ(id) + '</p>';
                }

                html += '<div class="apiship_delivContent">' + contentBlock + '</div>';
            }

            document.getElementById('apiship_wrapper_block').innerHTML = '<div id="apiship_wrapper"></div>';
            document.getElementById('apiship_wrapper').innerHTML = html;
        },

        paintPVZ: function(ind){
            var addr = '';
            if (IPOLapiship_pvz.cityPVZ[ind].color && IPOLapiship_pvz.cityPVZ[ind].Address.indexOf(',') !== false) {
                addr = "<span style='color:" + IPOLapiship_pvz.cityPVZ[ind].color + "'>" + IPOLapiship_pvz.cityPVZ[ind].Address.substr(0, IPOLapiship_pvz.cityPVZ[ind].Address.indexOf(',')) + "</span><br>" + IPOLapiship_pvz.cityPVZ[ind].Name;
            } else {
                addr = IPOLapiship_pvz.cityPVZ[ind].Name;
                if (IPOLapiship_pvz.cityPVZ[ind].house)
                    addr += ', ' + IPOLapiship_pvz.cityPVZ[ind].house;
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

        markChosenPVZ: function(id){
            let currentChosenId = '';
            if (IPOLapiship_pvz.CheckType(document.querySelector('.apiship_chosen')))
                currentChosenId = document.querySelector('.apiship_chosen').id;
            if (currentChosenId !== 'PVZ_' + id) {
                if (IPOLapiship_pvz.CheckType(document.querySelector('.apiship_chosen')))
                    document.querySelector('.apiship_chosen').classList.remove('apiship_chosen');
                document.getElementById("PVZ_"+id).classList.add('apiship_chosen');
                IPOLapiship_pvz.Y_selectPVZ(id);
            }

            document.getElementById('apiship_info').classList.remove('expanded');
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

            ymaps.geocode("<?=GetMessage("IPOLapiship_RUSSIA")?>, " + IPOLapiship_pvz.city, {
                results: 1
            }).then(function(res){
                var firstGeoObject = res.geoObjects.get(0);
                var coords = firstGeoObject.geometry.getCoordinates();
                coords[1] -= 0.2;

                if (!IPOLapiship_pvz.Y_map) {
                    IPOLapiship_pvz.Y_map = new ymaps.Map("apiship_map",{
                        zoom: 10,
                        controls: [],
                        center: coords
                    });
                    var ZK = new ymaps.control.ZoomControl({
                        options: {
                            position:{
                                right: 10,
                                top:  146
                            }
                        }
                    });
                    IPOLapiship_pvz.Y_map.controls.add(ZK);

                    if (IPOLapiship_pvz.widgetSearch) {
                        var searchControlParams = {float: 'right', floatIndex: 100, noPlacemark: true};
                        if (IPOLapiship_pvz.widgetSearchMark)
                            searchControlParams.noPlacemark = false;

                        IPOLapiship_pvz.Y_map.controls.add('searchControl', searchControlParams);
                        IPOLapiship_pvz.Y_map.controls.events.add('resultshow', IPOLapiship_pvz.Y_zoomCalibrate, IPOLapiship_pvz.Y_map.controls.get('searchControl'));
                    }
                } else {
                    IPOLapiship_pvz.Y_map.setCenter(coords);
                    IPOLapiship_pvz.Y_map.setZoom(10);
                }

                if (!IPOLapiship_pvz.Y_markedCities[IPOLapiship_pvz.city])
                    IPOLapiship_pvz.Y_markPVZ();
                else
                    IPOLapiship_pvz.cityPVZ = IPOLapiship_pvz.Y_markedCities[IPOLapiship_pvz.city];
            });
        },

        Y_zoomCalibrate: function(){
            while (!ymaps.geoQuery(map.geoObjects).searchInside(IPOLapiship_pvz.Y_map).getLength() && IPOLapiship_pvz.Y_map.getZoom() > 4) {
                IPOLapiship_pvz.Y_map.setZoom(IPOLapiship_pvz.Y_map.getZoom() - 1);
            }
        },

        Y_markPVZ: function(){
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

            for (var i in IPOLapiship_pvz.cityPVZ) {
                var baloonHTML = "";
                var pointText  = "";

                /* Provider logo */
                if (typeof IPOLapiship_pvz.arImages[IPOLapiship_pvz.cityPVZ[i].providerKey] != "undefined") {
                    var src = IPOLapiship_pvz.image_url + IPOLapiship_pvz.arImages[IPOLapiship_pvz.cityPVZ[i].providerKey];
                    baloonHTML += '<img alt="' + IPOLapiship_pvz.cityPVZ[i].providerKey + '" class="apiship_provider_baloon_img" src="' + src + '" data-src="' + src+ '">';
                } else
                    baloonHTML += "<div class='apiship_provider_baloon_img'>" + IPOLapiship_pvz.cityPVZ[i].providerKey + "</div>";

                baloonHTML += "<div id='apiship_baloon'>";
                baloonHTML += "<div class='apiship_iAdress'>";

                if (IPOLapiship_pvz.cityPVZ[i].color)
                    baloonHTML += "<span style='color:"+IPOLapiship_pvz.cityPVZ[i].color+"'>"

                baloonHTML += IPOLapiship_pvz.cityPVZ[i].Address;

                if (IPOLapiship_pvz.cityPVZ[i].color)
                    baloonHTML += "</span>";

                baloonHTML += "</div>";

                if (IPOLapiship_pvz.cityPVZ[i].Phone)
                    baloonHTML += "<div><div class='apiship_iTelephone apiship_icon'></div><div class='apiship_baloonDiv'>"+IPOLapiship_pvz.cityPVZ[i].Phone+"</div><div style='clear:both'></div></div>";
                if (IPOLapiship_pvz.cityPVZ[i].WorkTime)
                    baloonHTML += "<div><div class='apiship_iTime apiship_icon'></div><div class='apiship_baloonDiv'>"+IPOLapiship_pvz.cityPVZ[i].WorkTime+"</div><div style='clear:both'></div></div>";
                if (parseInt(IPOLapiship_pvz.cityPVZ[i].fittingRoom) === 1)
                    baloonHTML += "<div><div class='apiship_iFitting apiship_icon'></div><div class='apiship_baloonDiv'><?=GetMessage("IPOLapiship_BALOON_FITTING")?></div><div style='clear:both'></div></div>";
                if (IPOLapiship_pvz.widgetPointFeatureCOD) {
                    let isPaymentCash = parseInt(IPOLapiship_pvz.cityPVZ[i].paymentCash) === 1;
                    let isPaymentCard = parseInt(IPOLapiship_pvz.cityPVZ[i].paymentCard) === 1;

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
                        switch (parseInt(IPOLapiship_pvz.cityPVZ[i].type)) {
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
                        if (parseInt(IPOLapiship_pvz.cityPVZ[i].cod) === 1)
                            baloonHTML += "<div class='apiship_baloonDiv_feature'><?=GetMessage("IPOLapiship_BALOON_F_COD")?></div>";
                        else
                            baloonHTML += "<div class='apiship_baloonDiv_feature not_available'><?=GetMessage("IPOLapiship_BALOON_F_NO_COD")?></div>";
                    }

                    baloonHTML += "<div style='clear:both'></div></div>";
                }

                console.log(IPOLapiship_pvz.cityPVZ);

                baloonHTML += "<div><div class='apiship_baloonDiv'><b><?=GetMessage("IPOLapiship_BALOON_DELIVERY_COST")?>" + IPOLapiship_pvz.cityPVZ[i].deliveryCost + "<?=GetMessage("IPOLapiship_CURRENCY_RUB")?></b></div><div style='clear:both'></div></div>";

                baloonHTML += "<div><div class='apiship_baloonDiv'><b><?=GetMessage("IPOLapiship_BALOON_DELIVERY_DAYS")?>" + IPOLapiship_pvz.getDayEnd(IPOLapiship_pvz.cityPVZ[i].daysMin, IPOLapiship_pvz.cityPVZ[i].daysMax) + "</b></div><div style='clear:both'></div></div>";

                baloonHTML += "</div>";

                IPOLapiship_pvz.cityPVZ[i].placeMark = new ymaps.Placemark([IPOLapiship_pvz.cityPVZ[i].cY, IPOLapiship_pvz.cityPVZ[i].cX],{
                    balloonContent: baloonHTML
                }, {
                    iconLayout: 'default#image',
                    iconImageHref: IPOLapiship_pvz.getInactiveIcon(IPOLapiship_pvz.PVZ[i].providerKey),
                    iconImageSize: [40, 43],
                    iconImageOffset: [-20, -43]
                });
                IPOLapiship_pvz.cityPVZ[i].placeMark.link = i;
                IPOLapiship_pvz.cityPVZ[i].placeMark.events.add('balloonopen', function(mark){
                    var previousMark = IPOLapiship_pvz.currentMark;

                    IPOLapiship_pvz.currentMark = mark.get('target');
                    /* Add previous mark back to cluster */
                    if (typeof(previousMark) === 'object' && previousMark.link !== IPOLapiship_pvz.currentMark.link && typeof(IPOLapiship_pvz.cityPVZ[previousMark.link]) !== 'undefined') {
                        IPOLapiship_pvz.Y_map.geoObjects.remove(IPOLapiship_pvz.cityPVZ[previousMark.link].placeMark);
                        IPOLapiship_pvz.clusterer.add(IPOLapiship_pvz.cityPVZ[previousMark.link].placeMark);
                    }

                    IPOLapiship_pvz.markChosenPVZ(mark.get('target').link);
                });

                IPOLapiship_pvz.cityPVZ[i].placeMark.events.add('balloonclose', function(mark){
                    if (IPOLapiship_pvz.CheckType(document.querySelectorAll('.apiship_chosen')))
                        document.querySelectorAll('.apiship_chosen').forEach(function(th){
                            th.classList.remove('apiship_chosen');
                        });
                });

                geoMarks.push(IPOLapiship_pvz.cityPVZ[i].placeMark);
            }
            IPOLapiship_pvz.Y_markedCities[IPOLapiship_pvz.city] = IPOLapiship_pvz.cityPVZ;

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
            /* Case: mobile FF doesn't trigger onmouseover and placemark continue to stay clustered */
            var placemarkState = IPOLapiship_pvz.clusterer.getObjectState(IPOLapiship_pvz.cityPVZ[wat].placeMark);
            if (placemarkState.isClustered) {
                IPOLapiship_pvz.clusterer.remove(IPOLapiship_pvz.cityPVZ[wat].placeMark);
                IPOLapiship_pvz.Y_map.geoObjects.add(IPOLapiship_pvz.cityPVZ[wat].placeMark);
            }

            IPOLapiship_pvz.Y_map.setCenter([IPOLapiship_pvz.cityPVZ[wat].cY, IPOLapiship_pvz.cityPVZ[wat].cX]).then(function(res){
                IPOLapiship_pvz.cityPVZ[wat].placeMark.balloon.open();
            });
        },

        Y_blinkPVZ: function(wat, ifOn){
            if (IPOLapiship_pvz.CheckType(document.querySelector('.apiship_chosen'))) {
                if (document.querySelector('.apiship_chosen').attributes.getNamedItem('id').value === 'PVZ_' + wat) {
                    /* Restore placemark icon */
                    if (!(typeof (ifOn) !== 'undefined' && ifOn)) {
                        IPOLapiship_pvz.cityPVZ[wat].placeMark.options.set({iconImageHref: IPOLapiship_pvz.getInactiveIcon(IPOLapiship_pvz.cityPVZ[wat].providerKey)});
                    }
                    return;
                }
            }

            if (typeof(ifOn) != 'undefined' && ifOn) {
                IPOLapiship_pvz.clusterer.remove(IPOLapiship_pvz.cityPVZ[wat].placeMark);
                IPOLapiship_pvz.Y_map.geoObjects.add(IPOLapiship_pvz.cityPVZ[wat].placeMark);
                IPOLapiship_pvz.cityPVZ[wat].placeMark.options.set({iconImageHref: IPOLapiship_pvz.widgetPlacemarkIcon.defaultIcon.active});
            } else {
                IPOLapiship_pvz.cityPVZ[wat].placeMark.options.set({iconImageHref: IPOLapiship_pvz.getInactiveIcon(IPOLapiship_pvz.cityPVZ[wat].providerKey)});
                IPOLapiship_pvz.Y_map.geoObjects.remove(IPOLapiship_pvz.cityPVZ[wat].placeMark);
                IPOLapiship_pvz.clusterer.add(IPOLapiship_pvz.cityPVZ[wat].placeMark);
            }
        },

        showCitySel: function(){
            if (IPOLapiship_pvz.CheckType(document.getElementById('apiship_citySel')))
                document.getElementById('apiship_citySel').style.display = 'block';
            if (IPOLapiship_pvz.CheckType(document.getElementById('apiship_cityName')))
                document.getElementById('apiship_cityName').style.display = 'none';
        },

        hideCitySel: function(){
            if (IPOLapiship_pvz.CheckType(document.getElementById('apiship_citySel')))
                document.getElementById('apiship_citySel').style.display = 'none';
            if (IPOLapiship_pvz.CheckType(document.getElementById('apiship_cityName')))
                document.getElementById('apiship_cityName').style.display = 'block';
        },

        cityChange: function(){
            if (IPOLapiship_pvz.CheckType(document.getElementById('IPOLAPISHIP_FORM')))
                document.getElementById('IPOLAPISHIP_FORM').dispatchEvent(new Event('cityChange'))
        },

        ymapsCheckerCntr: 0,
        ymapsCheck: function(){
            if (IPOLapiship_pvz.ymapsCheckerCntr >= 100) {
                console.log('ipol.apiship2v: Yandex Maps API is still undefined after ' + IPOLapiship_pvz.ymapsCheckerCntr + ' checks.');
                return;
            }
            if ((typeof(ymaps) === 'undefined') || (typeof(ymaps.ready) === 'undefined') || (typeof(ymaps.Map) === 'undefined') || (typeof(ymaps.geocode) === 'undefined')) {
                IPOLapiship_pvz.ymapsCheckerCntr++;
                setTimeout(IPOLapiship_pvz.ymapsCheck, 100);
            } else {
                ymaps.ready(IPOLapiship_pvz.init);
            }
        }
    };
</script>
<?php if ($arResult["isConverted"]) {?>
    <script data-skip-moving="true">
        BX.ready(function(){
            window.apiship2vLocationChange = function(result){
                IPOLapiship_pvz.newLocation = result;
                IPOLapiship_pvz.cityChange();
            }
        });
    </script>
<?php }?>
<div id='apiship_pvz'>
    <form action="" method="post" id="IPOLAPISHIP_FORM">
        <div id='apiship_title'>
            <?php if ($arParams["SHOW_CITY_INPUT"] == "Y") {?>
                <div id='apiship_cityPicker'>
                    <div><?=GetMessage("IPOLapiship_YOURCITY")?></div>
                    <div>
                        <div id='apiship_cityLabel'>
                            <?php if (!$arResult["isConverted"]) {?>
                                <a id='apiship_cityName' onClick="IPOLapiship_pvz.showCitySel();" href='javascript:void(0)'><?=empty($arResult['cityName']) ? GetMessage("IPOLapiship_CITY_NOT_FOUND") : $arResult['cityName']?></a>
                            <?php }?>
                            <div id='<?= ($arResult["isConverted"]) ? "apiship_citySel_converted" : "apiship_citySel"?>'>
                                <?php if ($arResult["isConverted"]) {?>
                                    <?php // Actual location selector for modern SOA ?>
                                    <?php
                                    $GLOBALS["APPLICATION"]->IncludeComponent('bitrix:sale.location.selector.search', '.default', array(
                                        "ID"                     => "",
                                        "CODE"                   => "",
                                        "INPUT_NAME"             => $CityInput,
                                        "PROVIDE_LINK_BY"        => "code",
                                        "SHOW_ADMIN_CONTROLS"    => 'Y',
                                        "SELECT_WHEN_SINGLE"     => 'N',
                                        "FILTER_BY_SITE"         => 'N',
                                        //"FILTER_BY_SITE"         => "Y",
                                        //"FILTER_SITE_ID"         => SITE_ID,
                                        "SHOW_DEFAULT_LOCATIONS" => 'N',
                                        "SEARCH_BY_PRIMARY"      => 'Y',
                                        "JS_CALLBACK"            => 'apiship2vLocationChange',
                                    ),
                                        null,
                                        array('HIDE_ICONS' => 'Y')
                                    );
                                    ?>
                                <?php } else {?>
                                    <?php // Ancient component for old BX ?>
                                    <?php $GLOBALS["APPLICATION"]->IncludeComponent("bitrix:sale.ajax.locations", "popup",
                                        array(
                                            "AJAX_CALL"          => "N",
                                            "COUNTRY_INPUT_NAME" => "COUNTRY",
                                            "ALLOW_EMPTY_CITY"   => "N",
                                            "REGION_INPUT_NAME"  => "REGION",
                                            "CITY_INPUT_NAME"    => $CityInput,
                                            "CITY_OUT_LOCATION"  => "Y",
                                            "ONCITYCHANGE"       => "IPOLapiship_pvz.cityChange();"
                                        ),
                                        null,
                                        array('HIDE_ICONS' => 'Y')
                                    );
                                    ?>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }?>
            <?php if (($arParams["SHOW_COURIER"] == "Y") && (!empty($arResult["bestsTariffs"]["deliveryToDoorShown"]))) {
                $courier = $arResult["bestsTariffs"]["deliveryToDoorShown"];
                foreach ($courier as $provider => $tariff)
                    $courier = $tariff;

                print_r($tariff);

                if ($tariff["daysMin"] == $tariff["daysMax"])
                    $days = $tariff["daysMin"];
                else
                    $days = $tariff["daysMin"]. " - ". $tariff["daysMax"];
                ?>
                <div class='apiship_mark'>
                    <table>
                        <tr><td><strong><?=GetMessage("IPOLapiship_COURIER")?></strong></td><td><span id='apiship_cPrice'><?=$tariff["deliveryCost"].GetMessage("IPOLapiship_CURRENCY_RUB")?></span></td></tr>
                        <tr title='<?=GetMessage("IPOLapiship_HINT")?>'><td><?="<strong>".GetMessage("IPOLapiship_DELTERM")."</strong>"."</td><td id='apiship_cDate'>".$days.GetMessage("IPOLapiship_DAY")?></td></tr>
                    </table>
                </div>
            <?php }?>
            <div style='float:none;clear:both'></div>
        </div>
        <div id='apiship_map'></div>
        <div id='apiship_info'>
            <div id='apiship_sign'><?=GetMessage("IPOLapiship_PVZ_LIST_HEADER")?></div>
            <div id='apiship_delivInfo'></div>
            <div id='apiship_wrapper_block'>
                <div id='apiship_wrapper'></div>
            </div>
            <div id='apiship_ten'></div>
        </div>
        <div id='apiship_head'>
            <div id='apiship_logo'><a href='http://ipol.ru' target='_blank'></a></div>
        </div>
    </form>
</div>
<script data-skip-moving="true">
    IPOLapiship_pvz.ymapsCheck();

    document.getElementById('apiship_sign').onclick = function(){
        if (document.getElementById('apiship_sign').parentNode.classList.contains('expanded'))
            document.getElementById('apiship_sign').parentNode.classList.remove('expanded');
        else
            document.getElementById('apiship_sign').parentNode.classList.add('expanded')
    }

    document.getElementById('IPOLAPISHIP_FORM').addEventListener('cityChange', function(event){
        let altLocationVal = '';
        if (IPOLapiship_pvz.CheckType(event.currentTarget.querySelector('#<?=$CityInput?>')))
            altLocationVal = event.currentTarget.querySelector('#<?=$CityInput?>').value;

        var cityNewID = (IPOLapiship_pvz.isConverted) ? IPOLapiship_pvz.newLocation : altLocationVal;

        async function getNewData(){
            let Data = new FormData();
            Data.append('IPOLAPISHIP_CITY_AJAX_NEW_ID', cityNewID);

            let response = await fetch("<?=$TemplateFolder?>/ajax.php", {
                method: 'POST',
                body:   Data,
            });
            if (!response.ok) {
                const message = `ipol.apiship2v: an error has occured with code ${response.status}: ${response.statusText}`;
                throw new Error(message);
            }
            return response.json()
        }

        getNewData().then(dataAJAX=>{
            /* Change city to selected */
            if (dataAJAX.cityName === "")
                dataAJAX.cityName = "<?=getMessage("IPOLapiship_CITY_NOT_FOUND")?>";
            if (IPOLapiship_pvz.CheckType(document.getElementById('apiship_cityName')))
                document.getElementById('apiship_cityName').innerHTML = dataAJAX.cityName;
            IPOLapiship_pvz.hideCitySel();

            /* Map update */
            IPOLapiship_pvz.city = dataAJAX.cityName;
            IPOLapiship_pvz.PVZ = dataAJAX.PVZ;
            IPOLapiship_pvz.init();

            /* Courier data update */
            var courier = dataAJAX.bestsTariffs.deliveryToDoorShown,
                cost = 0,
                days = 0;

            for (var i in courier) {
                console.log(courier[i]);
                cost = courier[i].deliveryCost;
                if (courier[i].daysMin === courier[i].daysMax)
                    days = courier[i].daysMin;
                else
                    days = courier[i].daysMin + " - " + courier[i].daysMax;
            }

            if (IPOLapiship_pvz.CheckType(document.getElementById('apiship_cPrice')))
                document.getElementById('apiship_cPrice').innerHTML = cost + "<?=GetMessage("IPOLapiship_CURRENCY_RUB")?>";

            if (IPOLapiship_pvz.CheckType(document.getElementById('apiship_cDate')))
                document.getElementById('apiship_cDate').innerHTML = days + "<?=GetMessage("IPOLapiship_DAY")?>";
        }).catch(error=>{
            console.log(error);
        });
    });
</script>