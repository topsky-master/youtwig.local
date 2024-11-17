<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?><?
$APPLICATION->IncludeComponent(
    "bitrix:sale.order.ajax",
    "main_testp",
    array(
        "PAY_FROM_ACCOUNT" => "N",
        "COUNT_DELIVERY_TAX" => "N",
        "COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
        "ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
        "ALLOW_AUTO_REGISTER" => "Y",
        "SEND_NEW_USER_NOTIFY" => "Y",
        "DELIVERY_NO_AJAX" => "Y",
        "DELIVERY_NO_SESSION" => "N",
        "TEMPLATE_LOCATION" => "popup",
        "DELIVERY_TO_PAYSYSTEM" => "d2p",
        "USE_PREPAYMENT" => "N",
        "PROP_1" => array(
            0 => "27",
            1 => "28",
            2 => "26",
        ),
        "PROP_2" => "",
        "PATH_TO_BASKET" => "/personal/provider/",
        "PATH_TO_PERSONAL" => "/personal/order/",
        "PATH_TO_PAYMENT" => "/personal/order/payment/",
        "PATH_TO_AUTH" => "/auth/",
        "SET_TITLE" => "Y",
        "SKIP_PAYSYSTEM" => "39,40,18,19",
        "DISABLE_BASKET_REDIRECT" => "Y",
        "PRODUCT_COLUMNS" => "",
        "ALLOW_NEW_PROFILE" => "N",
        "SHOW_PAYMENT_SERVICES_NAMES" => "Y",
        "SHOW_STORES_IMAGES" => "N",
        "DISPLAY_IMG_WIDTH" => "90",
        "DISPLAY_IMG_HEIGHT" => "90",
        "COMPONENT_TEMPLATE" => "main",
        "COMPATIBLE_MODE" => "Y",
        "ALLOW_USER_PROFILES" => "N",
        "TEMPLATE_THEME" => "site",
        "SHOW_ORDER_BUTTON" => "always",
        "SHOW_TOTAL_ORDER_BUTTON" => "Y",
        "SHOW_PAY_SYSTEM_LIST_NAMES" => "Y",
        "SHOW_PAY_SYSTEM_INFO_NAME" => "Y",
        "SHOW_DELIVERY_LIST_NAMES" => "Y",
        "SHOW_DELIVERY_INFO_NAME" => "Y",
        "SHOW_DELIVERY_PARENT_NAMES" => "Y",
        "SKIP_USELESS_BLOCK" => "Y",
        "BASKET_POSITION" => "before",
        "SHOW_BASKET_HEADERS" => "N",
        "DELIVERY_FADE_EXTRA_SERVICES" => "N",
        "SHOW_COUPONS_BASKET" => "Y",
        "SHOW_COUPONS_DELIVERY" => "Y",
        "SHOW_COUPONS_PAY_SYSTEM" => "Y",
        "SHOW_NEAREST_PICKUP" => "N",
        "DELIVERIES_PER_PAGE" => "8",
        "PAY_SYSTEMS_PER_PAGE" => "8",
        "PICKUPS_PER_PAGE" => "5",
        "SHOW_MAP_IN_PROPS" => "N",
        "PROPS_FADE_LIST_1" => "",
        "PRODUCT_COLUMNS_VISIBLE" => array(
            0 => "PREVIEW_PICTURE",
            1 => "PROPS",
        ),
        "ADDITIONAL_PICT_PROP_11" => "-",
        "ADDITIONAL_PICT_PROP_16" => "-",
        "BASKET_IMAGES_SCALING" => "standard",
        "SERVICES_IMAGES_SCALING" => "standard",
        "PRODUCT_COLUMNS_HIDDEN" => "",
        "USE_YM_GOALS" => "N",
        "USE_CUSTOM_MAIN_MESSAGES" => "N",
        "USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",
        "USE_CUSTOM_ERROR_MESSAGES" => "N",
        "SET_DEFAULT" => ".delivery_yandeks_dostavka_samovyvoz.ZIP,.delivery_boxberry_samovyvoz.ZIP,.delivery_boxberry_samovyvoz.FLAT,.delivery_boxberry_samovyvoz.HOUSE,.delivery_boxberry_samovyvoz.STREET,.delivery_dostavka_do_metro.FLAT,.delivery_dostavka_do_metro.HOUSE,.delivery_dostavka_do_metro.STREET,.delivery_samovyvoz.STREET,.delivery_samovyvoz.FLAT,.delivery_samovyvoz.HOUSE,.delivery_sdek_samovyvoz.STREET,.delivery_sdek_samovyvoz.HOUSE,.delivery_sdek_samovyvoz.FLAT,.delivery_samovyvoz.ORDER_PROP_20,.delivery_samovyvoz.ORDER_PROP_4,.delivery_dostavka_kurerom_po_moskve.ORDER_PROP_4,.delivery_dostavka_do_metro.ORDER_PROP_4,.delivery_sdek_dostavka_kurerom.ORDER_PROP_4,.delivery_sdek_samovyvoz.ORDER_PROP_20,.delivery_sdek_samovyvoz.ORDER_PROP_4,.paysystem_nalichnyy_raschet.ORDER_PROP_68,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_68,.paysystem_sberbank_onlayn.ORDER_PROP_68,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_68,.paysystem_yandeks_dengi.ORDER_PROP_68,.paysystem_paypal.ORDER_PROP_68,.paysystem_masterpass.ORDER_PROP_68,.paysystem_qiwi.ORDER_PROP_68,.paysystem_webmoney.ORDER_PROP_68,.paysystem_nalichnyy_raschet.ORDER_PROP_67,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_67,.paysystem_sberbank_onlayn.ORDER_PROP_67,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_67,.paysystem_yandeks_dengi.ORDER_PROP_67,.paysystem_paypal.ORDER_PROP_67,.paysystem_masterpass.ORDER_PROP_67,.paysystem_qiwi.ORDER_PROP_67,.paysystem_webmoney.ORDER_PROP_67,.paysystem_nalichnyy_raschet.ORDER_PROP_66,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_66,.paysystem_sberbank_onlayn.ORDER_PROP_66,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_66,.paysystem_yandeks_dengi.ORDER_PROP_66,.paysystem_paypal.ORDER_PROP_66,.paysystem_masterpass.ORDER_PROP_66,.paysystem_qiwi.ORDER_PROP_66,.paysystem_webmoney.ORDER_PROP_66,.paysystem_nalichnyy_raschet.ORDER_PROP_65,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_65,.paysystem_sberbank_onlayn.ORDER_PROP_65,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_65,.paysystem_yandeks_dengi.ORDER_PROP_65,.paysystem_paypal.ORDER_PROP_65,.paysystem_masterpass.ORDER_PROP_65,.paysystem_qiwi.ORDER_PROP_65,.paysystem_webmoney.ORDER_PROP_65,.paysystem_nalichnyy_raschet.ORDER_PROP_64,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_64,.paysystem_sberbank_onlayn.ORDER_PROP_64,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_64,.paysystem_yandeks_dengi.ORDER_PROP_64,.paysystem_paypal.ORDER_PROP_64,.paysystem_masterpass.ORDER_PROP_64,.paysystem_qiwi.ORDER_PROP_64,.paysystem_webmoney.ORDER_PROP_64,.paysystem_nalichnyy_raschet.ORDER_PROP_63,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_63,.paysystem_sberbank_onlayn.ORDER_PROP_63,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_63,.paysystem_yandeks_dengi.ORDER_PROP_63,.paysystem_paypal.ORDER_PROP_63,.paysystem_masterpass.ORDER_PROP_63,.paysystem_qiwi.ORDER_PROP_63,.paysystem_webmoney.ORDER_PROP_63,.paysystem_nalichnyy_raschet.ORDER_PROP_69,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_69,.paysystem_sberbank_onlayn.ORDER_PROP_69,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_69,.paysystem_yandeks_dengi.ORDER_PROP_69,.paysystem_paypal.ORDER_PROP_69,.paysystem_masterpass.ORDER_PROP_69,.paysystem_qiwi.ORDER_PROP_69,.paysystem_webmoney.ORDER_PROP_69,.paysystem_nalichnyy_raschet.ORDER_PROP_8,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_8,.paysystem_sberbank_onlayn.ORDER_PROP_8,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_8,.paysystem_yandeks_dengi.ORDER_PROP_8,.paysystem_paypal.ORDER_PROP_8,.paysystem_masterpass.ORDER_PROP_8,.paysystem_qiwi.ORDER_PROP_8,.paysystem_webmoney.ORDER_PROP_8,.paysystem_nalichnyy_raschet.ORDER_PROP_9,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_9,.paysystem_sberbank_onlayn.ORDER_PROP_9,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_9,.paysystem_yandeks_dengi.ORDER_PROP_9,.paysystem_paypal.ORDER_PROP_9,.paysystem_masterpass.ORDER_PROP_9,.paysystem_qiwi.ORDER_PROP_9,.paysystem_webmoney.ORDER_PROP_9,.paysystem_nalichnyy_raschet.ORDER_PROP_10,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_10,.paysystem_sberbank_onlayn.ORDER_PROP_10,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_10,.paysystem_yandeks_dengi.ORDER_PROP_10,.paysystem_paypal.ORDER_PROP_10,.paysystem_masterpass.ORDER_PROP_10,.paysystem_qiwi.ORDER_PROP_10,.paysystem_webmoney.ORDER_PROP_10,.paysystem_nalichnyy_raschet.ORDER_PROP_11,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_11,.paysystem_sberbank_onlayn.ORDER_PROP_11,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_11,.paysystem_yandeks_dengi.ORDER_PROP_11,.paysystem_paypal.ORDER_PROP_11,.paysystem_masterpass.ORDER_PROP_11,.paysystem_qiwi.ORDER_PROP_11,.paysystem_webmoney.ORDER_PROP_11,.paysystem_nalichnyy_raschet.ORDER_PROP_12,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_12,.paysystem_sberbank_onlayn.ORDER_PROP_12,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_12,.paysystem_yandeks_dengi.ORDER_PROP_12,.paysystem_paypal.ORDER_PROP_12,.paysystem_masterpass.ORDER_PROP_12,.paysystem_qiwi.ORDER_PROP_12,.paysystem_webmoney.ORDER_PROP_12,.paysystem_nalichnyy_raschet.ORDER_PROP_15,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_15,.paysystem_sberbank_onlayn.ORDER_PROP_15,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_15,.paysystem_yandeks_dengi.ORDER_PROP_15,.paysystem_paypal.ORDER_PROP_15,.paysystem_masterpass.ORDER_PROP_15,.paysystem_qiwi.ORDER_PROP_15,.paysystem_webmoney.ORDER_PROP_15,.paysystem_nalichnyy_raschet.ORDER_PROP_15,.paysystem_oplata_bankovskoy_kartoy.ORDER_PROP_15,.paysystem_sberbank_onlayn.ORDER_PROP_15,.paysystem_kvitantsiya_sberbanka.ORDER_PROP_15,.paysystem_yandeks_dengi.ORDER_PROP_15,.paysystem_paypal.ORDER_PROP_15,.paysystem_masterpass.ORDER_PROP_15,.paysystem_qiwi.ORDER_PROP_15,.paysystem_webmoney.ORDER_PROP_15",
    ),
    false
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>