var tf_location_cities_loaded = false,
    tf_location_animation_speed = 200;

if (!window.BX && top.BX)
    window.BX = top.BX;

function TfLocation(params, callback) {
    var self = this;

    this.params = params;
    this.callback = callback;
    this.$body = $('body');
    this.$bodyChildren = this.$body.children(':not(script):not(style):not(noscript)');
    this.mobileWidth = !!this.params.mobile_width ? parseInt(this.params.mobile_width) : 0;

    this.LocationsPopup = new TfLocationsPopup(this);
    this.ConfirmPopup = new TfConfirmPopup(this);

    this.openLocationsPopup = function ($link) {
        this.LocationsPopup.open($link);
        this.ConfirmPopup.close();
    };

    this.openConfirmPopup = function () {
        this.ConfirmPopup.open();
        this.LocationsPopup.close();
    };

    /**
     *
     * @returns {boolean}
     */
    this.isMobile = function () {
        return (window.innerWidth <= this.mobileWidth);
    };

    this.htmlspecialchars = function (str) {
        if (typeof (str) == "string") {
            str = str.replace(/&/g, '&amp;'); /* must do &amp; first */
            str = str.replace(/"/g, '&quot;');
            str = str.replace(/'/g, '&#039;');
            str = str.replace(/</g, '&lt;');
            str = str.replace(/>/g, '&gt;');
        }
        return str;
    };

    this.htmlspecialcharsDecode = function (str) {
        if (typeof (str) == "string") {
            str = str.replace(/&quot;/g, '"');
            str = str.replace(/&#039;/g, '\'');
            str = str.replace(/&lt;/g, '<');
            str = str.replace(/&gt;/g, '>');
            str = str.replace(/&amp;/g, '&'); /* must do &amp; first */

        }
        return str;
    };

    this.removeTags = function (str) {
        if (typeof (str) == "string") {
            str = str.replace(/<[^>]+>/g, '');
        }
        return str;
    };

    this.clearStr = function (str) {
        if (typeof (str) == "string") {
            str = this.htmlspecialcharsDecode(str);
            str = this.removeTags(str);
        }
        return str;
    }

    this.setLocation = function (locationCode, callback, requestUri, update) {
        $.post(self.params.path + '/functions.php', {
            request: 'setcity',
            requestUri: requestUri,
            location_id: locationCode,
        }, function (response) {
            if (typeof callback == 'function') {
                callback(response);
            }
        }, 'json');
    };

    this.updateLinks = function (locationId, locationName, locationCode) {
        $('.tfl__link').html(locationName).data('location-id', locationId).data('location-code', locationCode);
    };

    this.removeURLParameters = function (removeParams) {
        const deleteRegex = new RegExp(removeParams.join('=|') + '=');
        const params = location.search.slice(1).split('&');
        let search = [];

        for (let i = 0; i < params.length; i++) {
            if (deleteRegex.test(params[i]) === false) {
                search.push(params[i]);
            }
        }

        window.history.replaceState({}, document.title, location.pathname + (search.length ? '?' + search.join('&') : '') + location.hash)
    };
}

/**
 *
 * @param TfLocation
 * @constructor
 */
function TfLocationsPopup(TfLocation) {
    var self = this;

    this.TfLocation = TfLocation;
    this.callback = TfLocation.callback;
    this.$body = TfLocation.$body;
    this.$bodyChildren = TfLocation.$bodyChildren;
    this.$overlay = this.$body.find('.tfl-popup-overlay');
    this.$popup = this.$body.find('.tfl-popup');

    if (this.$overlay.length) {
        if (this.$overlay.length > 1) {
            this.$overlay.first().remove();
        } else {
            this.$overlay.appendTo(this.$body);
        }
    }

    this.componentPath = !!this.TfLocation.params.path ? this.TfLocation.params.path : '';
    this.requestUri = !!this.TfLocation.params.request_uri ? this.TfLocation.params.request_uri : '/';
    //this.$container             = this.$popup.find('.tfl-popup__container');
    this.$locationsContainer = this.$popup.find(".tfl-popup__locations");
    this.$defaultsContainer = this.$popup.find(".tfl-popup__defaults");
    this.Search = new TfLocationsPopupSearch(this);
    this.isOrderPage = false;

    /**
     *
     * @param key
     */
    this.onKeyPress = function (key) {
        if (!self.isOpened()) {
            return;
        }

        switch (key.originalEvent.keyCode) {
            case 27:
                self.close();
                break;
            case 38: //up
            case 40: //down
                try {
                    //if (self.canUseNiceScroll())
                    //{
                    let $niceScroll = self.$locationsContainer.getNiceScroll(0),
                        scrollTop = $niceScroll ? $niceScroll.getScrollTop() : 0,
                        newScrollTop = key.originalEvent.keyCode === 38 ? scrollTop -= 500 : scrollTop += 500;

                    if ($niceScroll.length) {
                        self.$locationsContainer.getNiceScroll(0).doScrollTop(newScrollTop, 300);
                    }
                } catch (e) {
                    console.error('twofingers.location niceScroll config error:', e);
                }

                break;
        }
    };

    this.freeze = function () {
        self.$popup.css('height', self.$popup.outerHeight());
    };

    this.unFreeze = function () {
        self.$popup.css('height', 'auto');
    };

    this.open = function ($link) {
        //  if ($('.tfl__link[data-order=true]').length) {
        //      this.isOrderPage = true;

        if (typeof BX != 'undefined'
            && BX.hasOwnProperty('Sale')
            && BX.Sale.hasOwnProperty('OrderAjaxComponent')
            && !!BX.Sale.OrderAjaxComponent) {
            this.isOrderPage = true;

            this.callback += '; BX.Sale.OrderAjaxComponent.sendRequest();';
        }
        // }

        this.$overlay.fadeIn({
            duration: tf_location_animation_speed,
            start: this.onOpenStart,
            // complete: tf_location_cities_loaded ? '' : this.onOpenComplete
        });
    };

    this.onOpenStart = function () {
        self.$bodyChildren.addClass('tfl-body-blur');
        self.$body.addClass('tfl-body-freeze');

        if (!tf_location_cities_loaded) {
            self.$overlay.addClass('tfl-popup-overlay_loading');
            self.loadLocations().then(self.onOpenComplete);
            self.Search.init();
            self.initClose();
            tf_location_cities_loaded = true;
        } else {
            self.reloadScroll(self.$locationsContainer);
            self.reloadScroll(self.$defaultsContainer);
            self.onOpenComplete();
        }
    };

    this.onOpenComplete = function () {
        self.$overlay.removeClass('tfl-body-blur tfl-popup-overlay_loading');
        self.$popup.removeClass('tfl-body-blur');
        self.$popup.addClass('tfl-popup_loaded');
    };

    /**
     * @deprecated
     */
    this.onLocationsLoadStart = function () {
        this.$popup.addClass('tfl-popup_loading');
    }

    /**
     * @deprecated
     */
    this.onLocationsLoadComplete = function () {
        self.$popup.removeClass('tfl-popup_loading');
    }

    this.loadLocations = function () {
        //self.onLocationsLoadStart();

        return new Promise(function (resolve) {
            $.get(self.componentPath + '/functions.php', {
                    request: 'getcities',
                    type: self.TfLocation.params.load_type
                }, function (data) {
                    if (data.CITIES.length) {
                        self.addLocations(self.$locationsContainer, data.CITIES, 'tfl-popup__with-locations');
                    }

                    if (data.DEFAULT_CITIES.length) {
                        self.addLocations(self.$defaultsContainer, data.DEFAULT_CITIES, 'tfl-popup__with-defaults');
                    }

                    self.addLocationsHandler(self.$popup.find('.tfl-popup__location-link'));
                    //self.onLocationsLoadComplete();

                    resolve(data);
                },
                'json');
        })
    };

    /**
     *
     * @param $container
     * @param locations
     * @param popupClass
     * @param source
     * @returns {{length}|*}
     */
    this.addLocations = function ($container, locations, popupClass, source) {
        var $list = $container.find('.tfl-popup__list'), count = locations.length;

        if (!$list.length) {
            return;
        }

        var defaultLocation = $('.tfl__link-container .tfl__link').text().trim();

        $.each(locations, function (key, location) {
            var item = '<li><a class="tfl-popup__location-link" '
                + 'data-id="' + location.id
                + '" data-name="' + self.TfLocation.clearStr(location.name)
                + '" data-code="' + self.TfLocation.clearStr(location.code)
            ;

            if (!!source) {
                item += '" data-source="' + source;
            } else {
                item += '" data-source="base';
            }

            item += '" href="#">' + self.TfLocation.htmlspecialcharsDecode(location.name) + '</a>';

            if (location.hasOwnProperty('description')) {
                item += '<div class="tf-location__region">' + location.description + '</div>';
            }

            item += '</li>';

            if (location.name.toLowerCase() != defaultLocation.toLowerCase()) {
                $list.append(item);
            }

            if (!--count) {
                self.reloadScroll($container);
            }
        });

        if (!!popupClass && popupClass.length) {
            this.$popup.addClass(popupClass);
        }

        return $list;
    };

    /**
     *
     * @returns {boolean}
     */
    this.canUseNiceScroll = function () {
        var jQueryVersion = window.jQuery.fn.jquery.split('.');

        return ((jQueryVersion.shift() > 2) && !self.TfLocation.isMobile())
    }

    /**
     *
     * @param $container
     */
    this.reloadScroll = function ($container) {
        if (window.jQuery) {
            try {
                if ($container.getNiceScroll().length) {
                    $container.getNiceScroll().resize();
                } else {
                    $container.niceScroll({
                        scrollspeed: 60,
                        mousescrollstep: 50,
                        hwacceleration: true,
                        bouncescroll: true,
                        zindex: "50",
                        cursorborderradius: "0px",
                        cursorborder: "none",
                        horizrailenabled: false,
                        cursorcolor: '#666',
                        cursorwidth: '5px',
                        background: "#d5d5d5",
                        autohidemode: 'leave',
                        cursoropacitymin: 0.4,
                        /*emulatetouch: true*/
                    });
                }
            } catch (e) {
                console.error('twofingers.location niceScroll error:', e);
            }
            //if (self.canUseNiceScroll())
            //{

            //}
        }
    };

    /**
     *
     */
    this.initClose = function () {
        self.$overlay.on('click', function (e) {
            if (!self.$popup.is(e.target)
                && self.$popup.has(e.target).length === 0) {
                self.close();
            }
        });

        this.$popup.find('.tfl-popup__close.tfl-popup__close_list').on('click', this.close);
    };

    /**
     *
     * @returns {*}
     */
    this.isOpened = function () {
        return self.$popup.is(':visible')
    }

    /**
     *
     */
    this.close = function () {
        self.$overlay.fadeOut(tf_location_animation_speed);
        self.$bodyChildren.removeClass('tfl-body-blur');
        self.$body.removeClass('tfl-body-freeze');
        self.$popup.removeClass('tfl-popup_loaded');

        return false;
    };

    /**
     *
     * @param $elements
     */
    this.addLocationsHandler = function ($elements) {
        if (!$elements.length) {
            return;
        }

        $elements.on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();

            var location = this,
                locationId = $(location).data('id'),
                locationName = $(location).text(),
                locationCode = $(location).data('code'),
                $orderLocation = self.$body.find('.tfl__link.tfl__link_order'),
                $route = self.$body.find('.bx-ui-sls-route'),
                $saleLocationInput = self.$body.find('.tf_location_city_input')/*,
                $fake               = $('.bx-ui-sls-fake')*/;

            if ($orderLocation.length && $route.length) {
                $route.val(locationName);
            }

            /* if ($fake.length) {
                 $fake.val(selectedCityID);
             }*/
            self.TfLocation.updateLinks(locationId, locationName, locationCode);

            if ($saleLocationInput.length)
                $saleLocationInput.val(locationCode);

            var callback = function (response) {
                var actualCallBackHandler = self.callback;

                if (typeof BX != 'undefined' && !!BX) {
                    BX.onCustomEvent('onTFLocationSetLocation', [response]);
                }

                try {
                    actualCallBackHandler = actualCallBackHandler.replace('#TF_LOCATION_CITY_ID#', locationId);
                    actualCallBackHandler = actualCallBackHandler.replace('#TF_LOCATION_CITY_NAME#', locationName);

                    eval(actualCallBackHandler);
                } catch (e) {
                    console.error('twofingers.location callback error:', e);
                }

                if (!!response.redirect && response.redirect.length && !self.isOrderPage) {
                    window.location.href = response.redirect;
                } else if (!!response.reload && response.reload && !self.isOrderPage) {
                    self.TfLocation.removeURLParameters(['tfl']);
                    window.location.reload();
                } else {
                    self.close();
                }
            }

            self.TfLocation.setLocation(locationCode, callback, self.requestUri);
        });
    };

    this.$body.on('keydown', self.onKeyPress);
}

/**
 *
 * @param LocationsPopup
 * @constructor
 */
function TfLocationsPopupSearch(LocationsPopup) {
    var self = this;
    this.LocationsPopup = LocationsPopup;
    this.$clear = LocationsPopup.$popup.find('.tfl-popup__clear-field');
    this.$searchInput = LocationsPopup.$popup.find('.tfl-popup__search-input');
    this.$list = LocationsPopup.$locationsContainer.find('.tfl-popup__list');
    this.$noFound = LocationsPopup.$locationsContainer.find('.tfl-popup__nofound-mess');

    this.focus = function () {
        this.$searchInput.focus();
    }

    this.init = function () {

        this.initSearch();

        if (this.$clear.length) {
            this.$clear.click(function () {
                self.reset();
            });
        }
    };

    this.showClear = function () {
        if (this.$clear.length) {
            this.$clear.fadeIn(tf_location_animation_speed);
        }
    };

    this.hideClear = function () {
        if (this.$clear.length) {
            this.$clear.fadeOut(tf_location_animation_speed);
        }
    };

    this.showNoFoundAndDefaults = function () {
        if (this.$noFound.length) {
            this.$noFound.addClass('tfl-popup__nofound-mess-show');
            self.LocationsPopup.reloadScroll(self.LocationsPopup.$locationsContainer);
        }

        this.tryToShowDefaults();
    };

    this.hideNoFound = function () {
        if (this.$noFound.length) {
            this.$noFound.removeClass('tfl-popup__nofound-mess-show');
        }
    };

    this.initSearch = function () {
        var delay = 400, timeOutId;

        this.$searchInput.keyup(function () {

            self.LocationsPopup.freeze();

            var q = $(this).val().toUpperCase();

            if (timeOutId) {
                clearTimeout(timeOutId);
            }

            if (!q.length) {
                self.reset();
                return;
            }

            self.showClear();

            timeOutId = setTimeout(function () {

                self.removeResults();
                self.hideNoFound();
                self.$list.find('.tfl-popup__location-link').parent().hide();

                self.LocationsPopup.$popup
                    .addClass('tfl-popup__with-locations')
                    .addClass('tfl-popup_loading');

                self.search(q).done(function (data) {
                    self.updateResults(data.CITIES);
                    self.LocationsPopup.$popup.removeClass('tfl-popup_loading');
                });
            }, delay);
        });
    };

    this.search = function (q) {
        if (!!this.LocationsPopup.TfLocation.params.ajax_search
            && this.LocationsPopup.componentPath.length) {
            return this.ajaxSearch(q);
        } else {
            return this.localSearch(q);
        }
    };

    this.updateResults = function (cities) {
        if (!!cities && cities.length) {
            self.hideNoFound();
            self.hideDefaults();

            self.LocationsPopup.addLocations(self.LocationsPopup.$locationsContainer, cities, 'tfl-popup__with-locations', 'search');
            self.LocationsPopup.addLocationsHandler(self.$list.find('.tfl-popup__location-link'));
        } else {
            self.showNoFoundAndDefaults();
        }
    };

    this.ajaxSearch = function (q) {
        return $.ajax({
            type: "POST",
            url: self.LocationsPopup.componentPath + '/functions.php',
            data: {request: 'find', q: q},
            dataType: 'json'
        });
    };

    this.localSearch = function (q) {
        var result = $.Deferred(),
            data = {CITIES: [], FCITIES: []}, second = [];

        self.$list.find('a[data-source=base]').each(function () {
            var $location = $(this),
                $locationDescription = $location.siblings('.tf-location__region'),
                locationName = $location.html().toUpperCase(),
                locationNameFormatted,
                locationObject;

            if (locationName.indexOf(q) >= 0) {

                locationNameFormatted = $(this).html();
                locationNameFormatted = locationNameFormatted.substring(0, locationName.indexOf(q))
                    + '<b>' + locationNameFormatted.substring(locationName.indexOf(q), locationName.indexOf(q) + q.length)
                    + '</b>' + locationNameFormatted.substring(locationName.indexOf(q) + q.length);

                locationObject = {
                    name: locationNameFormatted,
                    id: $(this).data('id'),
                    code: $(this).data('code'),
                };

                if ($locationDescription.length) {
                    locationObject['description'] = $locationDescription.text();
                }

                if (locationName.indexOf(q) === 0) {
                    data.CITIES.push(locationObject);
                } else {
                    second.push(locationObject);
                }
            }
        });

        if (second.length) {
            data.CITIES = data.CITIES.concat(second);
        }

        // find same names
        data.CITIES = data.CITIES.map(function (location) {

            data.CITIES.forEach(function (location2) {
                if ((location.NAME === location2.NAME)
                    && (location.ID !== location2.ID)) {
                    location.SHOW_REGION = 'Y';
                }

                return true;
            });

            return location;
        });

        return result.resolve(data);
    };

    this.hideDefaults = function () {
        this.LocationsPopup.$popup.removeClass('tfl-popup__with-defaults');
    };

    this.tryToShowDefaults = function () {
        if (this.LocationsPopup.$defaultsContainer.find('.tfl-popup__location-link').length) {
            this.LocationsPopup.$popup.addClass('tfl-popup__with-defaults');
            self.LocationsPopup.reloadScroll(self.LocationsPopup.$defaultsContainer);
        }
    };

    /**
     *
     */
    this.removeResults = function () {
        this.$list.find('.tfl-popup__location-link[data-source=search]').parent().remove();
    };

    this.reset = function () {
        if (self.$searchInput.val.length) {
            self.$searchInput.val('');
        }

        this.hideClear();
        this.hideNoFound();
        this.removeResults();
        self.LocationsPopup.unFreeze();

        var $oldLinks = this.$list.find('.tfl-popup__location-link').parent();
        if ($oldLinks.length) {
            $oldLinks.show();
        } else {
            this.LocationsPopup.$popup.removeClass('tfl-popup__with-locations');
        }

        this.tryToShowDefaults();
        self.LocationsPopup.reloadScroll(self.LocationsPopup.$locationsContainer);
    }
}

function TfConfirmPopup(TfLocation) {

    var self = this;

    this.TfLocation = TfLocation;
    this.params = TfLocation.params;
    this.$body = TfLocation.$body;
    this.$bodyChildren = TfLocation.$bodyChildren;
    this.$popup = this.$body.find('.tfl-define-popup').first();
    this.componentPath = !!this.TfLocation.params.path ? this.TfLocation.params.path : '';

    if (this.$popup.length) {
        //this.$popup = $('.tfl-define-popup');

        if (this.$popup.length > 1) {
            this.$popup.first().remove();
        } else {
            this.$popup.prependTo(this.$body);
        }
    }

    this.close = function (confirm) {
        confirm = !!confirm;

        self.$popup
            .fadeOut(tf_location_animation_speed)
            .data('closed', true);

        if (self.componentPath.length) {
            $.post(
                self.componentPath + '/functions.php',
                {
                    request: 'close_confirm_popup',
                    confirm: confirm ? 'Y' : 'N',
                }, function (response) {
                    if (!confirm) {
                        return;
                    }

                    if (!!response.reload && response.reload) {
                        window.location.reload();
                    }
                }, 'json');
        }

        return false;
    };

    this.open = function () {
        var $close, $confirm, $list;

        if (!this.$popup.length) return;

        this.setPosition();

        $(window).on('resize scroll', function () {
            self.setPosition();
        });

        //if ($popup.is(':visible')) return;

        $close = this.$popup.find('.tfl-popup__close');
        $confirm = this.$popup.find('.tfl-define-popup__yes');
        $list = this.$popup.find('.tfl-define-popup__list');

        this.$popup.fadeIn(tf_location_animation_speed);

        $close.on('click', this.close);
        $confirm.on('click', function () {
            self.close(true)
        });
        $list.on('click', function (e) {
            self.TfLocation.openLocationsPopup();
            e.preventDefault();
            e.stopPropagation();
        });
    };


    this.setPosition = function () {
        if (this.$popup.data('closed')) return;

        var $link = $('.tfl__link:visible').not('.tfl__link_order').first(),
            left;

        if (this.TfLocation.isMobile()) {

            this.$popup
                .removeClass('tfl-define-popup__desktop')
                .addClass('tfl-define-popup__mobile')
                .css('top', 'auto')
                .css('left', 'auto')
                .show()
            ;
        } else {
            this.$popup
                .removeClass('tfl-define-popup__mobile')
                .addClass('tfl-define-popup__desktop');

            if ($link.length && ($link.offset().left + $link.width() >= 0)) {
                left = ($link.offset().left + ($link.width() / 2));

                if (left > ($(window).width() - this.$popup.width() / 2)) {
                    left = ($(window).width() - this.$popup.width() / 2);
                }

                if (left < (this.$popup.width() / 2)) {
                    left = (this.$popup.width() / 2);
                }

                this.$popup
                    .css('left', left + 'px')
                    .css('top', $link.offset().top + $link.outerHeight() + 12)
                    .show();
            } else {
                this.$popup.hide();
            }
        }
    }
}
