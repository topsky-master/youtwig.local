<?
require($_SERVER["DOCUMENT_ROOT"]."/local/site/prepend_functions.php");
CartFix::checkFix();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Корзина");
?><?
$APPLICATION->IncludeComponent(
	"bitrix:sale.order.ajax", 
	"new", 
	array(
		"ACTION_VARIABLE" => "soa-action",
		"ADDITIONAL_PICT_PROP_11" => "MORE_PHOTO",
		"ADDITIONAL_PICT_PROP_16" => "MORE_PHOTO",
		"ALLOW_APPEND_ORDER" => "Y",
		"ALLOW_AUTO_REGISTER" => "Y",
		"ALLOW_NEW_PROFILE" => "N",
		"ALLOW_USER_PROFILES" => "N",
		"BASKET_IMAGES_SCALING" => "standard",
		"BASKET_POSITION" => "before",
		"COMPATIBLE_MODE" => "N",
		"COUNT_DELIVERY_TAX" => "N",
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"DELIVERIES_PER_PAGE" => "8",
		"DELIVERY_FADE_EXTRA_SERVICES" => "N",
		"DELIVERY_NO_AJAX" => "Y",
		"DELIVERY_NO_SESSION" => "N",
		"DELIVERY_TO_PAYSYSTEM" => "d2p",
		"DISABLE_BASKET_REDIRECT" => "Y",
		"DISPLAY_IMG_HEIGHT" => "90",
		"DISPLAY_IMG_WIDTH" => "90",
		"EMPTY_BASKET_HINT_PATH" => "/",
		"HIDE_ORDER_DESCRIPTION" => "N",
		"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
		"PATH_TO_AUTH" => "/auth/",
		"PATH_TO_BASKET" => "/personal/cart/",
		"PATH_TO_PAYMENT" => "/personal/order/payment/",
		"PATH_TO_PERSONAL" => "/personal/order/",
		"PAY_FROM_ACCOUNT" => "N",
		"PAY_SYSTEMS_PER_PAGE" => "8",
		"PICKUPS_PER_PAGE" => "5",
		"PICKUP_MAP_TYPE" => "yandex",
		"PRODUCT_COLUMNS" => "",
		"PRODUCT_COLUMNS_HIDDEN" => array(
			0 => "PROPERTY_ARTNUMBER",
		),
		"PRODUCT_COLUMNS_VISIBLE" => array(
			0 => "PREVIEW_PICTURE",
			1 => "PROPS",
		),
		"PROPS_FADE_LIST_1" => array(
		),
		"PROPS_FADE_LIST_2" => array(
		),
		"PROP_1" => array(
			0 => "27",
			1 => "28",
			2 => "26",
		),
		"PROP_2" => "",
		"SEND_NEW_USER_NOTIFY" => "N",
		"SERVICES_IMAGES_SCALING" => "standard",
		"SET_DEFAULT" => ".delivery_yandeks_dostavka_samovyvoz.ZIP,.delivery_boxberry_samovyvoz.ZIP,.delivery_yandeks_dostavka_samovyvoz.FLAT,.delivery_yandeks_dostavka_samovyvoz.HOUSE,.delivery_yandeks_dostavka_samovyvoz.STREET,.delivery_boxberry_samovyvoz.FLAT,.delivery_boxberry_samovyvoz.HOUSE,.delivery_boxberry_samovyvoz.STREET,.delivery_dostavka_do_metro.FLAT,.delivery_dostavka_do_metro.HOUSE,.delivery_dostavka_do_metro.STREET,.delivery_samovyvoz.STREET,.delivery_samovyvoz.FLAT,.delivery_samovyvoz.HOUSE,.delivery_sdek_samovyvoz.STREET,.delivery_sdek_samovyvoz.HOUSE,.delivery_sdek_samovyvoz.FLAT,.delivery_samovyvoz.ORDER_PROP_20,.delivery_samovyvoz.ORDER_PROP_4,.delivery_dostavka_kurerom_po_moskve.ORDER_PROP_4,.delivery_dostavka_do_metro.ORDER_PROP_4,.delivery_sdek_dostavka_kurerom.ORDER_PROP_4,.delivery_sdek_samovyvoz.ORDER_PROP_20,.delivery_sdek_samovyvoz.ORDER_PROP_4,.paysystem_nalichnyy_raschet.ORDER_PROP_68,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_68,.paysystem_sberbank_onlayn.ORDER_PROP_68,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_68,.paysystem_yandeks_dengi.ORDER_PROP_68,.paysystem_paypal.ORDER_PROP_68,.paysystem_masterpass.ORDER_PROP_68,.paysystem_qiwi.ORDER_PROP_68,.paysystem_webmoney.ORDER_PROP_68,.paysystem_nalichnyy_raschet.ORDER_PROP_67,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_67,.paysystem_sberbank_onlayn.ORDER_PROP_67,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_67,.paysystem_yandeks_dengi.ORDER_PROP_67,.paysystem_paypal.ORDER_PROP_67,.paysystem_masterpass.ORDER_PROP_67,.paysystem_qiwi.ORDER_PROP_67,.paysystem_webmoney.ORDER_PROP_67,.paysystem_nalichnyy_raschet.ORDER_PROP_66,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_66,.paysystem_sberbank_onlayn.ORDER_PROP_66,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_66,.paysystem_yandeks_dengi.ORDER_PROP_66,.paysystem_paypal.ORDER_PROP_66,.paysystem_masterpass.ORDER_PROP_66,.paysystem_qiwi.ORDER_PROP_66,.paysystem_webmoney.ORDER_PROP_66,.paysystem_nalichnyy_raschet.ORDER_PROP_65,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_65,.paysystem_sberbank_onlayn.ORDER_PROP_65,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_65,.paysystem_yandeks_dengi.ORDER_PROP_65,.paysystem_paypal.ORDER_PROP_65,.paysystem_masterpass.ORDER_PROP_65,.paysystem_qiwi.ORDER_PROP_65,.paysystem_webmoney.ORDER_PROP_65,.paysystem_nalichnyy_raschet.ORDER_PROP_64,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_64,.paysystem_sberbank_onlayn.ORDER_PROP_64,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_64,.paysystem_yandeks_dengi.ORDER_PROP_64,.paysystem_paypal.ORDER_PROP_64,.paysystem_masterpass.ORDER_PROP_64,.paysystem_qiwi.ORDER_PROP_64,.paysystem_webmoney.ORDER_PROP_64,.paysystem_nalichnyy_raschet.ORDER_PROP_63,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_63,.paysystem_sberbank_onlayn.ORDER_PROP_63,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_63,.paysystem_yandeks_dengi.ORDER_PROP_63,.paysystem_paypal.ORDER_PROP_63,.paysystem_masterpass.ORDER_PROP_63,.paysystem_qiwi.ORDER_PROP_63,.paysystem_webmoney.ORDER_PROP_63,.paysystem_nalichnyy_raschet.ORDER_PROP_69,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_69,.paysystem_sberbank_onlayn.ORDER_PROP_69,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_69,.paysystem_yandeks_dengi.ORDER_PROP_69,.paysystem_paypal.ORDER_PROP_69,.paysystem_masterpass.ORDER_PROP_69,.paysystem_qiwi.ORDER_PROP_69,.paysystem_webmoney.ORDER_PROP_69,.paysystem_nalichnyy_raschet.ORDER_PROP_8,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_8,.paysystem_sberbank_onlayn.ORDER_PROP_8,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_8,.paysystem_yandeks_dengi.ORDER_PROP_8,.paysystem_paypal.ORDER_PROP_8,.paysystem_masterpass.ORDER_PROP_8,.paysystem_qiwi.ORDER_PROP_8,.paysystem_webmoney.ORDER_PROP_8,.paysystem_nalichnyy_raschet.ORDER_PROP_9,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_9,.paysystem_sberbank_onlayn.ORDER_PROP_9,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_9,.paysystem_yandeks_dengi.ORDER_PROP_9,.paysystem_paypal.ORDER_PROP_9,.paysystem_masterpass.ORDER_PROP_9,.paysystem_qiwi.ORDER_PROP_9,.paysystem_webmoney.ORDER_PROP_9,.paysystem_nalichnyy_raschet.ORDER_PROP_10,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_10,.paysystem_sberbank_onlayn.ORDER_PROP_10,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_10,.paysystem_yandeks_dengi.ORDER_PROP_10,.paysystem_paypal.ORDER_PROP_10,.paysystem_masterpass.ORDER_PROP_10,.paysystem_qiwi.ORDER_PROP_10,.paysystem_webmoney.ORDER_PROP_10,.paysystem_nalichnyy_raschet.ORDER_PROP_11,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_11,.paysystem_sberbank_onlayn.ORDER_PROP_11,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_11,.paysystem_yandeks_dengi.ORDER_PROP_11,.paysystem_paypal.ORDER_PROP_11,.paysystem_masterpass.ORDER_PROP_11,.paysystem_qiwi.ORDER_PROP_11,.paysystem_webmoney.ORDER_PROP_11,.paysystem_nalichnyy_raschet.ORDER_PROP_12,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_12,.paysystem_sberbank_onlayn.ORDER_PROP_12,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_12,.paysystem_yandeks_dengi.ORDER_PROP_12,.paysystem_paypal.ORDER_PROP_12,.paysystem_masterpass.ORDER_PROP_12,.paysystem_qiwi.ORDER_PROP_12,.paysystem_webmoney.ORDER_PROP_12,.paysystem_nalichnyy_raschet.ORDER_PROP_15,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_15,.paysystem_sberbank_onlayn.ORDER_PROP_15,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_15,.paysystem_yandeks_dengi.ORDER_PROP_15,.paysystem_paypal.ORDER_PROP_15,.paysystem_masterpass.ORDER_PROP_15,.paysystem_qiwi.ORDER_PROP_15,.paysystem_webmoney.ORDER_PROP_15,.paysystem_nalichnyy_raschet.ORDER_PROP_15,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_15,.paysystem_sberbank_onlayn.ORDER_PROP_15,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_15,.paysystem_yandeks_dengi.ORDER_PROP_15,.paysystem_paypal.ORDER_PROP_15,.paysystem_masterpass.ORDER_PROP_15,.paysystem_qiwi.ORDER_PROP_15,.paysystem_webmoney.ORDER_PROP_15",
		"SET_TITLE" => "Y",
		"SHOW_BASKET_HEADERS" => "N",
		"SHOW_COUPONS" => "N",
		"SHOW_COUPONS_BASKET" => "Y",
		"SHOW_COUPONS_DELIVERY" => "N",
		"SHOW_COUPONS_PAY_SYSTEM" => "N",
		"SHOW_DELIVERY_INFO_NAME" => "Y",
		"SHOW_DELIVERY_LIST_NAMES" => "Y",
		"SHOW_DELIVERY_PARENT_NAMES" => "N",
		"SHOW_MAP_IN_PROPS" => "N",
		"SHOW_NEAREST_PICKUP" => "Y",
		"SHOW_NOT_CALCULATED_DELIVERIES" => "N",
		"SHOW_ORDER_BUTTON" => "always",
		"SHOW_PAYMENT_SERVICES_NAMES" => "Y",
		"SHOW_PAY_SYSTEM_INFO_NAME" => "Y",
		"SHOW_PAY_SYSTEM_LIST_NAMES" => "Y",
		"SHOW_PICKUP_MAP" => "Y",
		"SHOW_STORES_IMAGES" => "N",
		"SHOW_TOTAL_ORDER_BUTTON" => "N",
		"SHOW_VAT_PRICE" => "N",
		"SKIP_PAYSYSTEM" => "39,40",
		"SKIP_USELESS_BLOCK" => "Y",
		"SPOT_LOCATION_BY_GEOIP" => "Y",
		"TEMPLATE_LOCATION" => "popup",
		"TEMPLATE_THEME" => "blue",
		"USER_CONSENT" => "N",
		"USER_CONSENT_ID" => "0",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N",
		"USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",
		"USE_CUSTOM_ERROR_MESSAGES" => "N",
		"USE_CUSTOM_MAIN_MESSAGES" => "N",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_PHONE_NORMALIZATION" => "Y",
		"USE_PRELOAD" => "Y",
		"USE_PREPAYMENT" => "N",
		"USE_YM_GOALS" => "Y",
		"COMPONENT_TEMPLATE" => "new",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"YM_GOALS_COUNTER" => "",
		"YM_GOALS_INITIALIZE" => "BX-order-init",
		"YM_GOALS_EDIT_REGION" => "BX-region-edit",
		"YM_GOALS_EDIT_DELIVERY" => "BX-delivery-edit",
		"YM_GOALS_EDIT_PICKUP" => "BX-pickUp-edit",
		"YM_GOALS_EDIT_PAY_SYSTEM" => "BX-paySystem-edit",
		"YM_GOALS_EDIT_PROPERTIES" => "BX-properties-edit",
		"YM_GOALS_EDIT_BASKET" => "BX-basket-edit",
		"YM_GOALS_NEXT_REGION" => "BX-region-next",
		"YM_GOALS_NEXT_DELIVERY" => "BX-delivery-next",
		"YM_GOALS_NEXT_PICKUP" => "BX-pickUp-next",
		"YM_GOALS_NEXT_PAY_SYSTEM" => "BX-paySystem-next",
		"YM_GOALS_NEXT_PROPERTIES" => "BX-properties-next",
		"YM_GOALS_NEXT_BASKET" => "BX-basket-next",
		"YM_GOALS_SAVE_ORDER" => "BX-order-save"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>