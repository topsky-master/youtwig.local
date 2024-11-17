<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'my.stat');


CJSCore::Init(array("jquery"));

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/my.stat/lib/data.php';

$mid = ADMIN_MODULE_NAME;

//use Bitrix\Main\Text\String;
\CModule::IncludeModule("fileman");


if (!$USER->isAdmin()) {
    $APPLICATION->authForm('Nope');
}


$app                                                = Application::getInstance();
$context                                            = $app->getContext();
$request                                            = $context->getRequest();
Loc::loadMessages($context->getServer()->getDocumentRoot()."/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);

if ((!empty($save) || !empty($restore)) && $request->isPost() && check_bitrix_sessid()) {

    if (!empty($restore)) {
        Option::delete(ADMIN_MODULE_NAME);
        CAdminMessage::showMessage(array(
            "MESSAGE"                               =>Loc::getMessage("REFERENCES_OPTIONS_RESTORED"),
            "TYPE"                                  =>"OK",
        ));

    }

    if(isset($_POST['per_dvr_price']))
        Option::set(
            ADMIN_MODULE_NAME,
            "per_dvr_price",
            ($request->getPost('per_dvr_price'))
        );

    if(isset($_POST['min_dvr_price']))
        Option::set(
            ADMIN_MODULE_NAME,
            "min_dvr_price",
            ($request->getPost('min_dvr_price'))
        );

    if(isset($_POST['pagenav_filter_manufacturer_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_filter_manufacturer_description",
            ($request->getPost('pagenav_filter_manufacturer_description'))
        );

    if(isset($_POST['pagenav_filter_manufacturer_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_filter_manufacturer_title",
            ($request->getPost('pagenav_filter_manufacturer_title'))
        );

    if(isset($_POST['filter_manufacturer_keywords']))
        Option::set(
            ADMIN_MODULE_NAME,
            "filter_manufacturer_keywords",
            ($request->getPost('filter_manufacturer_keywords'))
        );

    if(isset($_POST['filter_manufacturer_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "filter_manufacturer_description",
            ($request->getPost('filter_manufacturer_description'))
        );

    if(isset($_POST['filter_manufacturer_h1']))
        Option::set(
            ADMIN_MODULE_NAME,
            "filter_manufacturer_h1",
            ($request->getPost('filter_manufacturer_h1'))
        );

    if(isset($_POST['filter_manufacturer_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "filter_manufacturer_title",
            ($request->getPost('filter_manufacturer_title'))
        );

    if(isset($_POST['filters_glue_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "filters_glue_description",
            ($request->getPost('filters_glue_description'))
        );

    if(isset($_POST['pagenav_comments_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_comments_title",
            ($request->getPost('pagenav_comments_title'))
        );

    if(isset($_POST['pagenav_comments_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_comments_description",
            ($request->getPost('pagenav_comments_description'))
        );

    if(isset($_POST['pagenav_instructions_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_instructions_title",
            ($request->getPost('pagenav_instructions_title'))
        );

    if(isset($_POST['pagenav_instructions_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_instructions_description",
            ($request->getPost('pagenav_instructions_description'))
        );

    if(isset($_POST['pagenav_news_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_news_title",
            ($request->getPost('pagenav_news_title'))
        );

    if(isset($_POST['pagenav_news_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_news_description",
            ($request->getPost('pagenav_news_description'))
        );

    if(isset($_POST['pagenav_brand_detail_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_brand_detail_title",
            ($request->getPost('pagenav_brand_detail_title'))
        );

    if(isset($_POST['pagenav_brand_detail_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_brand_detail_description",
            ($request->getPost('pagenav_brand_detail_description'))
        );

    if(isset($_POST['pagenav_brand_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_brand_title",
            ($request->getPost('pagenav_brand_title'))
        );

    if(isset($_POST['pagenav_brand_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_brand_description",
            ($request->getPost('pagenav_brand_description'))
        );

    if(isset($_POST['models_keywords']))
        Option::set(
            ADMIN_MODULE_NAME,
            "models_keywords",
            ($request->getPost('models_keywords'))
        );

    if(isset($_POST['models_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "models_description",
            ($request->getPost('models_description'))
        );

    if(isset($_POST['models_h1']))
        Option::set(
            ADMIN_MODULE_NAME,
            "models_h1",
            ($request->getPost('models_h1'))
        );

    if(isset($_POST['models_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "models_title",
            ($request->getPost('models_title'))
        );

    if(isset($_POST['text_for_models']))
        Option::set(
            ADMIN_MODULE_NAME,
            "text_for_models",
            ($request->getPost('text_for_models'))
        );

    if(isset($_POST['models_version_keywords']))
        Option::set(
            ADMIN_MODULE_NAME,
            "models_version_keywords",
            ($request->getPost('models_version_keywords'))
        );

    if(isset($_POST['models_version_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "models_version_description",
            ($request->getPost('models_version_description'))
        );

    if(isset($_POST['models_version_h1']))
        Option::set(
            ADMIN_MODULE_NAME,
            "models_version_h1",
            ($request->getPost('models_version_h1'))
        );


    if(isset($_POST['models_version_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "models_version_title",
            ($request->getPost('models_version_title'))
        );

    if(isset($_POST['text_for_models_version']))
        Option::set(
            ADMIN_MODULE_NAME,
            "text_for_models_version",
            ($request->getPost('text_for_models_version'))
        );

    if(isset($_POST['tags_replaces']))
        Option::set(
            ADMIN_MODULE_NAME,
            "tags_replaces",
            serialize($request->getPost('tags_replaces'))
        );


    if(isset($_POST['declension_models']))
        Option::set(
            ADMIN_MODULE_NAME,
            "declension_models",
            serialize($request->getPost('declension_models'))
        );

    if (isset($_POST['text_for_elt_models']))
        Option::set(
            ADMIN_MODULE_NAME,
            "text_for_elt_models",
            ($request->getPost('text_for_elt_models'))
        );


    if (isset($_POST['elt_models_h1']))
        Option::set(
            ADMIN_MODULE_NAME,
            "elt_models_h1",
            ($request->getPost('elt_models_h1'))
        );


    if (isset($_POST['elt_models_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "elt_models_title",
            ($request->getPost('elt_models_title'))
        );


    if (isset($_POST['elt_models_keywords']))
        Option::set(
            ADMIN_MODULE_NAME,
            "elt_models_keywords",
            ($request->getPost('elt_models_keywords'))
        );


    if (isset($_POST['elt_models_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "elt_models_description",
            ($request->getPost('elt_models_description'))
        );

    if(isset($_POST['references_domain_props']))
        Option::set(
            ADMIN_MODULE_NAME,
            "references_domain_props",
            serialize($request->getPost('references_domain_props'))
        );

    if(isset($_POST['declension_products']))
        Option::set(
            ADMIN_MODULE_NAME,
            "declension_products",
            serialize($request->getPost('declension_products'))
        );

    if(isset($_POST['declension_series_models']))
        Option::set(
            ADMIN_MODULE_NAME,
            "declension_series_models",
            serialize($request->getPost('declension_series_models'))
        );

    if(isset($_POST['filters_preg']))
        Option::set(
            ADMIN_MODULE_NAME,
            "filters_preg",
            trim($request->getPost('filters_preg'))
        );

    if(isset($_POST['filter_parameter'])){

        $filter_parameter_sizeof = Option::get(ADMIN_MODULE_NAME, "filter_parameter_sizeof", "");

        for($i = 0; $i < $filter_parameter_sizeof; $i ++){

            Option::delete(ADMIN_MODULE_NAME,array("name" => "filter_parameter_id".$i));
            Option::delete(ADMIN_MODULE_NAME,array("name" => "filter_parameter_value".$i));

        }

        $filter_parameter = $request->getPost('filter_parameter');

        $filter_parameter_sizeof = sizeof($filter_parameter['id']);

        for($i = 0; $i < $filter_parameter_sizeof; $i ++){

            Option::set(ADMIN_MODULE_NAME, "filter_parameter_id".$i, $filter_parameter['id'][$i]);
            Option::set(ADMIN_MODULE_NAME, "filter_parameter_value".$i, $filter_parameter['value'][$i]);

        }

        Option::set(
            ADMIN_MODULE_NAME,
            "filter_parameter_sizeof",
            ($filter_parameter_sizeof)
        );

    }

    if(isset($_POST['references_timeintervals'])){
        Option::set(
            ADMIN_MODULE_NAME,
            "references_timeintervals",
            serialize($request->getPost('references_timeintervals'))
        );
    }

    if(isset($_POST['paymentid_sao_renderoptin'])){

        $paymentid_sao_renderoptin = join(',',$request->getPost('paymentid_sao_renderoptin'));

        Option::set(
            ADMIN_MODULE_NAME,
            "paymentid_sao_renderoptin",
            $paymentid_sao_renderoptin
        );

    }

    if(isset($_POST['sao_payment_id'])){

        $sao_typeid_sizeof = Option::get(ADMIN_MODULE_NAME, "sao_typeid_sizeof", "");

        for($i = 0; $i < $sao_typeid_sizeof; $i ++){

            Option::delete(ADMIN_MODULE_NAME,array("name" => "sao_persontype_id".$i));
            Option::delete(ADMIN_MODULE_NAME,array("name" => "sao_payment_id".$i));

        }

        $sao_persontype_id = $request->getPost('sao_persontype_id');
        $sao_payment_id = $request->getPost('sao_payment_id');

        $sao_typeid_sizeof = sizeof($sao_payment_id);

        for($i = 0; $i < $sao_typeid_sizeof; $i ++){

            Option::set(ADMIN_MODULE_NAME, "sao_persontype_id".$i, $sao_persontype_id[$i]);
            Option::set(ADMIN_MODULE_NAME, "sao_payment_id".$i, $sao_payment_id[$i]);

        }

        Option::set(
            ADMIN_MODULE_NAME,
            "sao_typeid_sizeof",
            ($sao_typeid_sizeof)
        );

    }


    if(isset($_POST['sao_delivery_id'])){

        $sao_delivery_sizeof = Option::get(ADMIN_MODULE_NAME, "sao_delivery_sizeof", "");

        for($i = 0; $i < $sao_delivery_sizeof; $i ++){

            Option::delete(ADMIN_MODULE_NAME,array("name" => "sao_delivery_id".$i));
            Option::delete(ADMIN_MODULE_NAME,array("name" => "sao_delivery_days".$i));

        }

        $sao_delivery_id = $request->getPost('sao_delivery_id');
        $sao_delivery_days = $request->getPost('sao_delivery_days');

        $sao_delivery_sizeof = sizeof($sao_delivery_id);

        for($i = 0; $i < $sao_delivery_sizeof; $i ++){

            Option::set(ADMIN_MODULE_NAME, "sao_delivery_id".$i, $sao_delivery_id[$i]);
            Option::set(ADMIN_MODULE_NAME, "sao_delivery_days".$i, $sao_delivery_days[$i]);

        }

        Option::set(
            ADMIN_MODULE_NAME,
            "sao_delivery_sizeof",
            ($sao_delivery_sizeof)
        );

    }

    if(isset($_POST['nofollow_parameter'])){

        $nofollow_parameter_sizeof = Option::get(ADMIN_MODULE_NAME, "nofollow_parameter_sizeof", "");

        for ($i = 0; $i < $nofollow_parameter_sizeof; $i++) {

            Option::delete(ADMIN_MODULE_NAME, array("name" => "nofollow_parameter_chain" . $i));
            Option::delete(ADMIN_MODULE_NAME, array("name" => "nofollow_parameter_section" . $i));

        }

        $nofollow_parameter = $request->getPost('nofollow_parameter');

        $nofollow_parameter_sizeof = sizeof($nofollow_parameter['chain']);

        for ($i = 0; $i < $nofollow_parameter_sizeof; $i++) {

            Option::set(ADMIN_MODULE_NAME, "nofollow_parameter_chain" . $i, $nofollow_parameter['chain'][$i]);
            Option::set(ADMIN_MODULE_NAME, "nofollow_parameter_section" . $i, (is_numeric($nofollow_parameter['section'][$i]) ? $nofollow_parameter['section'][$i] : 0));

        }

        Option::set(
            ADMIN_MODULE_NAME,
            "nofollow_parameter_sizeof",
            ($nofollow_parameter_sizeof)
        );

    }


    if(isset($_POST['main_parameter'])){

        $main_parameter_sizeof = Option::get(ADMIN_MODULE_NAME, "main_parameter_sizeof", "");

        for ($i = 0; $i < $main_parameter_sizeof; $i++) {

            Option::delete(ADMIN_MODULE_NAME, array("name" => "main_parameter_id" . $i));
            Option::delete(ADMIN_MODULE_NAME, array("name" => "main_parameter_chain" . $i));
            Option::delete(ADMIN_MODULE_NAME, array("name" => "main_parameter_section" . $i));
            Option::delete(ADMIN_MODULE_NAME, array("name" => "main_parameter_value" . $i));

        }

        $main_parameter = $request->getPost('main_parameter');

        $main_parameter_sizeof = sizeof($main_parameter['id']);

        for ($i = 0; $i < $main_parameter_sizeof; $i++) {

            Option::set(ADMIN_MODULE_NAME, "main_parameter_id" . $i, $main_parameter['id'][$i]);
            Option::set(ADMIN_MODULE_NAME, "main_parameter_chain" . $i, $main_parameter['chain'][$i]);
            Option::set(ADMIN_MODULE_NAME, "main_parameter_section" . $i, (is_numeric($main_parameter['section'][$i]) ? $main_parameter['section'][$i] : 0));
            Option::set(ADMIN_MODULE_NAME, "main_parameter_value" . $i, $main_parameter['value'][$i]);

        }

        Option::set(
            ADMIN_MODULE_NAME,
            "main_parameter_sizeof",
            ($main_parameter_sizeof)
        );

    }

    if(isset($_POST['pagenav_description_default']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_description_default",
            ($request->getPost('pagenav_description_default'))
        );

    if(isset($_POST['pagenav_title_default']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_title_default",
            ($request->getPost('pagenav_title_default'))
        );

    if(isset($_POST['pagenav_filter_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_filter_description",
            ($request->getPost('pagenav_filter_description'))
        );

    if(isset($_POST['pagenav_filter_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_filter_title",
            ($request->getPost('pagenav_filter_title'))
        );

    if(isset($_POST['pagenav_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_description",
            ($request->getPost('pagenav_description'))
        );

    if(isset($_POST['pagenav_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "pagenav_title",
            ($request->getPost('pagenav_title'))
        );

    if(isset($_POST['chained'])){

        $chained_sizeof = Option::get(ADMIN_MODULE_NAME, "chained_sizeof", "");

        for($i = 0; $i < $chained_sizeof; $i ++){

            Option::delete(ADMIN_MODULE_NAME,array("name" => "chained_prop".$i));
            Option::delete(ADMIN_MODULE_NAME,array("name" => "chained_payments".$i));
            Option::delete(ADMIN_MODULE_NAME,array("name" => "chained_deliveries".$i));

        }

        $chained = $request->getPost('chained');
        $props = array();

        $chained_sizeof = sizeof($chained['payments']);

        for($i = 0; $i < $chained_sizeof; $i ++){

            foreach($chained['prop'] as $propKey => $propValue){

                $props[$i][$propKey] = $propValue[$i];

            }

            Option::set(ADMIN_MODULE_NAME, "chained_prop".$i, serialize($props[$i]));
            Option::set(ADMIN_MODULE_NAME, "chained_payments".$i, $chained['payments'][$i]);
            Option::set(ADMIN_MODULE_NAME, "chained_deliveries".$i, $chained['deliveries'][$i]);

        }

        Option::set(
            ADMIN_MODULE_NAME,
            "chained_sizeof",
            ($chained_sizeof)
        );

    }

    if(isset($_POST['provider_percent']))
        Option::set(
            ADMIN_MODULE_NAME,
            "provider_percent",
            ($request->getPost('provider_percent'))
        );


    if(isset($_POST['order_weekends']))
        Option::set(
            ADMIN_MODULE_NAME,
            "order_weekends",
            ($request->getPost('order_weekends'))
        );

    if(isset($_POST['sms_pay_template']))
        Option::set(
            ADMIN_MODULE_NAME,
            "sms_pay_template",
            ($request->getPost('sms_pay_template'))
        );

    if(isset($_POST['consent_processing_link']))
        Option::set(
            ADMIN_MODULE_NAME,
            "consent_processing_link",
            ($request->getPost('consent_processing_link'))
        );

    if(isset($_POST['manufacturer_stop']))
        Option::set(
            ADMIN_MODULE_NAME,
            "manufacturer_stop",
            $request->getPost('manufacturer_stop')
        );

    if(isset($_POST['manufacturer_filter_h1']))
        Option::set(
            ADMIN_MODULE_NAME,
            "manufacturer_filter_h1",
            $request->getPost('manufacturer_filter_h1')
        );

    if(isset($_POST['manufacturer_filter_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "manufacturer_filter_title",
            $request->getPost('manufacturer_filter_title')
        );

    if(isset($_POST['manufacturer_filter_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "manufacturer_filter_description",
            $request->getPost('manufacturer_filter_description')
        );

    if(isset($_POST['manufacturer_pagenav_title_default']))
        Option::set(
            ADMIN_MODULE_NAME,
            "manufacturer_pagenav_title_default",
            $request->getPost('manufacturer_pagenav_title_default')
        );


    if(isset($_POST['other_filter_h1']))
        Option::set(
            ADMIN_MODULE_NAME,
            "other_filter_h1",
            $request->getPost('other_filter_h1')
        );

    if(isset($_POST['other_filter_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "other_filter_title",
            $request->getPost('other_filter_title')
        );

    if(isset($_POST['other_filter_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "other_filter_description",
            $request->getPost('other_filter_description')
        );

    if(isset($_POST['other_pagenav_title_default']))
        Option::set(
            ADMIN_MODULE_NAME,
            "other_pagenav_title_default",
            $request->getPost('other_pagenav_title_default')
        );


    if(isset($_POST['filter_title']))
        Option::set(
            ADMIN_MODULE_NAME,
            "filter_title",
            $request->getPost('filter_title')
        );

    if(isset($_POST['filter_h1']))
        Option::set(
            ADMIN_MODULE_NAME,
            "filter_h1",
            $request->getPost('filter_h1')
        );

    if(isset($_POST['filter_description']))
        Option::set(
            ADMIN_MODULE_NAME,
            "filter_description",
            $request->getPost('filter_description')
        );

    if(isset($_POST['filter_keywords']))
        Option::set(
            ADMIN_MODULE_NAME,
            "filter_keywords",
            $request->getPost('filter_keywords')
        );

    CAdminMessage::showMessage(array(
        "MESSAGE"                               =>Loc::getMessage("REFERENCES_OPTIONS_SAVED"),
        "TYPE"                                  =>"OK",
    ));

    //CAdminMessage::showMessage(Loc::getMessage("REFERENCES_INVALID_VALUE"));

}

$sao_payment_id = array();
$sao_persontype_id = array();

$saotypeidcounter = 1;

if(SALE_INCLUDED){

    $sao_typeid_sizeof = Option::get(ADMIN_MODULE_NAME, "sao_typeid_sizeof", "");

    for($i = 0; $i < $sao_typeid_sizeof; $i ++){
        $sao_payment_id[$i] = Option::get(ADMIN_MODULE_NAME, "sao_payment_id".$i, "");
        $sao_persontype_id[$i] = Option::get(ADMIN_MODULE_NAME, "sao_persontype_id".$i, "");
    }

    if($sao_payment_id
        && isset($sao_payment_id)){
        $saotypeidcounter = sizeof($sao_payment_id);
    };

}


$saocounter = 1;
$sao_delivery = array();

if(SALE_INCLUDED){

    $sao_delivery_sizeof = Option::get(ADMIN_MODULE_NAME, "sao_delivery_sizeof", "");

    for($i = 0; $i < $sao_delivery_sizeof; $i ++){
        $sao_delivery_id[$i] = Option::get(ADMIN_MODULE_NAME, "sao_delivery_id".$i, "");
        $sao_delivery_days[$i] = Option::get(ADMIN_MODULE_NAME, "sao_delivery_days".$i, "");
    }

    if($sao_delivery_id
        && isset($sao_delivery_id)){
        $saocounter = sizeof($sao_delivery_id);
    };

}

$pccounter = 1;
$arSaleProperties = array();
$chained = array();

if(SALE_INCLUDED){

    $chained_sizeof = Option::get(ADMIN_MODULE_NAME, "chained_sizeof", "");

    $chained = array();
    $props = array();

    for($i = 0; $i < $chained_sizeof; $i ++){

        $props[$i] = unserialize(Option::get(ADMIN_MODULE_NAME, "chained_prop".$i, ""));
        $chained['payments'][$i] = Option::get(ADMIN_MODULE_NAME, "chained_payments".$i, "");
        $chained['deliveries'][$i] = Option::get(ADMIN_MODULE_NAME, "chained_deliveries".$i, "");
    }

    foreach($props as $number => $parr){
        foreach($parr as $propid => $value){
            $chained['prop'][$propid][$number] = $value;
        }
    }

    if($chained && isset($chained['payments'])){
        $pccounter = sizeof($chained['payments']);
    };

}

$aTabs = array(
    array(
        "DIV"                                       =>"edit1",
        "TAB"                                       =>Loc::getMessage("MAIN_TAB_SET"),
        "TITLE"                                     =>Loc::getMessage("MAIN_TAB_TITLE_SET"),
    ),
);


$tabControl                                         = new CAdminTabControl("tabControl1", $aTabs);
$tabControl->begin();

?>
<style>
    .sao-row,
    .chained-row{
        margin-bottom: 30px;
        padding: 20px 20px 20px 20px;
        box-sizing: border-box;
    }

    .sao-row .cart-fields,
    .chained-row .cart-fields{
        display: flex;
        margin-bottom: 10px;
        position: relative;
        overflow: hidden;
        flex-wrap: wrap;
    }

    .sao-row .cart-fields > label,
    .chained-row .cart-fields > label{
        display: block;
        width: 50%;
        width: calc(50%-14px);
        text-align: right;
        font-weight: bold;
        padding: 5px 10px 7px 4px;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        cursor: pointer;

    }

    .sao-row .remove-sao,
    .chained-row .remove-chain{
        position: absolute;
        top: 5px;
        right: 5px;
    }

    .sao-row  .cart-fields > label + div,
    .chained-row .cart-fields > label + div{
        padding: 5px 0 7px 10px;
        width: 50%;
        width: calc(50% - 10px);
    }

    #filters_preg{
        width: 100%;
    }

    .sao-row .cart-fields > label + div textarea,
    .sao-row .cart-fields > label + div input[type="text"],
    .sao-row .cart-fields > label + div input[type="password"],
    .sao-row .cart-fields > label + div select,
    .chained-row .cart-fields > label + div textarea,
    .chained-row .cart-fields > label + div input[type="text"],
    .chained-row .cart-fields > label + div input[type="password"],
    .chained-row .cart-fields > label + div select{
        max-width: 100%;
    }

    .cart-props-title{
        text-align: center;
    }

    .declension_series_models_param,
    .declension_products_param,
    .declension_models_param,
    .main_parameter{
        width: 25%;
        margin: 0 5px;
        display: inline-block;
    }

    .nofollow_parameter_row select{
        width: 40%;
        margin-right: 5px;
        display: block;
    }

    .freplaces_replaces_row,
    .nofollow_parameter_row,
    .filter_parameter_row,
    .declension_series_models_row,
    .declension_products_row,
    .declension_models_row,
    .main_parameter_row{
        margin-bottom: 5px;
        display: flex;
        align-items: center;
    }

    .filter_parameter_row input[type="text"]{
        width: 55%;
    }

    .filter_parameter_row select{
        width: 33%;
    }

    .main_parameter_row textarea{
        min-height: 120px;
        width: 40%;
    }

    #sms_pay_template{
        width: 97%;
        max-width: 100%;
        min-height: 120px;
    }

</style>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <tr>
        <td width="50%" align="right">
            <label for="provider_percent">
                <?=Loc::getMessage("REFERENCES_PROVIDER_PERCENT") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="provider_percent"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "provider_percent", ""));?>"
                   id="provider_percent"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="consent_processing_link">
                <?=Loc::getMessage("REFERENCES_CONSENT_PROCESSING_LINK") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="consent_processing_link"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "consent_processing_link", ""));?>"
                   id="consent_processing_link"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="sms_pay_template">
                <?=Loc::getMessage("REFERENCES_SMS_PAY_TEMPLATE") ?>:
            </label>
        </td>
        <td width="50%">
            <textarea
                    name="sms_pay_template"
                    id="sms_pay_template"><?
                echo (Option::get(ADMIN_MODULE_NAME, "sms_pay_template", ""));
                ?></textarea>
        </td>
    </tr>
    <? $tabControl->Buttons(); ?>
    <button type="submit"
            name="save"
            value="options"
            class="adm-btn-save adm-btn">
        <?=Loc::getMessage("MAIN_SAVE") ?>
    </button>
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
</form>
<?php

$tabControl->end();

$aTabs = array(
    array(
        "DIV"                                       =>"edit2",
        "TAB"                                       =>Loc::getMessage("REFERENCES_TAB_TEMPLATES_OF_TITLES"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_TAB_TEMPLATES_OF_TITLES"),
    ),
);

$tabControl                                         = new CAdminTabControl("tabControl2", $aTabs);

$tabControl->begin();
?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>


    <tr>
        <td width="50%" align="right">
            <label for="other_filter_title">
                <?=Loc::getMessage("REFERENCES_OTHER_FILTER_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="other_filter_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "other_filter_title", ""));?>"
                   id="other_filter_title"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="other_filter_description">
                <?=Loc::getMessage("REFERENCES_OTHER_FILTER_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="other_filter_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "other_filter_description", ""));?>"
                   id="other_filter_description"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="other_filter_h1">
                <?=Loc::getMessage("REFERENCES_OTHER_FILTER_H1") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="other_filter_h1"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "other_filter_h1", ""));?>"
                   id="other_filter_h1"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="other_pagenav_title_default">
                <?=Loc::getMessage("REFERENCES_OTHER_PAGENAV_TITLE_DEFAULT") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="other_pagenav_title_default"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "other_pagenav_title_default", ""));?>"
                   id="other_pagenav_title_default"
            />
        </td>
    </tr>




    <tr>
        <td width="50%" align="right">
            <label for="manufacturer_filter_title">
                <?=Loc::getMessage("REFERENCES_MANUFACTURER_FILTER_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="manufacturer_filter_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "manufacturer_filter_title", ""));?>"
                   id="manufacturer_filter_title"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="manufacturer_filter_description">
                <?=Loc::getMessage("REFERENCES_MANUFACTURER_FILTER_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="manufacturer_filter_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "manufacturer_filter_description", ""));?>"
                   id="manufacturer_filter_description"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="manufacturer_filter_h1">
                <?=Loc::getMessage("REFERENCES_MANUFACTURER_FILTER_H1") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="manufacturer_filter_h1"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "manufacturer_filter_h1", ""));?>"
                   id="manufacturer_filter_h1"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="manufacturer_pagenav_title_default">
                <?=Loc::getMessage("REFERENCES_MANUFACTURER_PAGENAV_TITLE_DEFAULT") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="manufacturer_pagenav_title_default"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "manufacturer_pagenav_title_default", ""));?>"
                   id="manufacturer_pagenav_title_default"
            />
        </td>
    </tr>


    <tr>
        <td width="50%" align="right">
            <label for="manufacturer_stop">
                <?=Loc::getMessage("REFERENCES_MANUFACTURER_STOP") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="manufacturer_stop"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "manufacturer_stop", ""));?>"
                   id="manufacturer_stop"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="filter_title">
                <?=Loc::getMessage("REFERENCES_FILTER_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="filter_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "filter_title", ""));?>"
                   id="filter_title"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="filter_h1">
                <?=Loc::getMessage("REFERENCES_FILTER_H1") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="filter_h1"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "filter_h1", ""));?>"
                   id="filter_h1"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="filter_description">
                <?=Loc::getMessage("REFERENCES_FILTER_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="filter_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "filter_description", ""));?>"
                   id="filter_description"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="filter_keywords">
                <?=Loc::getMessage("REFERENCES_FILTER_KEYWORDS") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="filter_keywords"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "filter_keywords", ""));?>"
                   id="filter_keywords"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="pagenav_title_default">
                <?=Loc::getMessage("REFERENCES_PAGENAV_TITLE_DEFAULT") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_title_default"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_title_default", ""));?>"
                   id="pagenav_title_default"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="pagenav_description_default">
                <?=Loc::getMessage("REFERENCES_PAGENAV_DESCRIPTION_DEFAULT") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_description_default"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_description_default", ""));?>"
                   id="pagenav_description_default"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="pagenav_title">
                <?=Loc::getMessage("REFERENCES_PAGENAV_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_title", ""));?>"
                   id="pagenav_title"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="pagenav_description">
                <?=Loc::getMessage("REFERENCES_PAGENAV_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_description", ""));?>"
                   id="pagenav_description"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="pagenav_filter_title">
                <?=Loc::getMessage("REFERENCES_PAGENAV_FILTER_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_filter_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_filter_title", ""));?>"
                   id="pagenav_filter_title"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="pagenav_filter_description">
                <?=Loc::getMessage("REFERENCES_PAGENAV_FILTER_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_filter_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_filter_description", ""));?>"
                   id="pagenav_filter_description"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="pagenav_brand_title">
                <?=Loc::getMessage("REFERENCES_PAGENAV_BRAND_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_brand_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_brand_title", ""));?>"
                   id="pagenav_brand_title"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="pagenav_brand_description">
                <?=Loc::getMessage("REFERENCES_PAGENAV_BRAND_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_brand_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_brand_description", ""));?>"
                   id="pagenav_brand_description"
            />
        </td>
    </tr>


    <tr>
        <td width="50%" align="right">
            <label for="pagenav_brand_detail_title">
                <?=Loc::getMessage("REFERENCES_PAGENAV_BRAND_DETAIL_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_brand_detail_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_brand_detail_title", ""));?>"
                   id="pagenav_brand_detail_title"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="pagenav_brand_detail_description">
                <?=Loc::getMessage("REFERENCES_PAGENAV_BRAND_DETAIL_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_brand_detail_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_brand_detail_description", ""));?>"
                   id="pagenav_brand_detail_description"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="filter_manufacturer_title">
                <?=Loc::getMessage("REFERENCES_FILTER_MANUFACTURER_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="filter_manufacturer_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "filter_manufacturer_title", ""));?>"
                   id="filter_manufacturer_title"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="filter_manufacturer_h1">
                <?=Loc::getMessage("REFERENCES_FILTER_MANUFACTURER_H1") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="filter_manufacturer_h1"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "filter_manufacturer_h1", ""));?>"
                   id="filter_manufacturer_h1"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="filter_manufacturer_description">
                <?=Loc::getMessage("REFERENCES_FILTER_MANUFACTURER_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="filter_manufacturer_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "filter_manufacturer_description", ""));?>"
                   id="filter_manufacturer_description"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="filter_manufacturer_keywords">
                <?=Loc::getMessage("REFERENCES_FILTER_MANUFACTURER_KEYWORDS") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="filter_manufacturer_keywords"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "filter_manufacturer_keywords", ""));?>"
                   id="filter_manufacturer_keywords"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="pagenav_filter_manufacturer_title">
                <?=Loc::getMessage("REFERENCES_PAGENAV_FILTER_MANUFACTURER_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_filter_manufacturer_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_filter_manufacturer_title", ""));?>"
                   id="pagenav_filter_manufacturer_title"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="pagenav_filter_manufacturer_description">
                <?=Loc::getMessage("REFERENCES_PAGENAV_FILTER_MANUFACTURER_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_filter_manufacturer_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_filter_manufacturer_description", ""));?>"
                   id="pagenav_filter_manufacturer_description"
            />
        </td>
    </tr>


    <tr>
        <td width="50%" align="right">
            <label for="text_for_models">
                <?=Loc::getMessage("REFERENCES_TEXT_FOR_MODELS") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="text_for_models"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "text_for_models", ""));?>"
                   id="text_for_models"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="models_title">
                <?=Loc::getMessage("REFERENCES_MODELS_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="models_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "models_title", ""));?>"
                   id="models_title"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="models_h1">
                <?=Loc::getMessage("REFERENCES_MODELS_H1") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="models_h1"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "models_h1", ""));?>"
                   id="models_h1"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="models_description">
                <?=Loc::getMessage("REFERENCES_MODELS_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="models_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "models_description", ""));?>"
                   id="models_description"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="models_keywords">
                <?=Loc::getMessage("REFERENCES_MODELS_KEYWORDS") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="models_keywords"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "models_keywords", ""));?>"
                   id="models_keywords"
            />
        </td>
    </tr>

    <!-- start: models elts -->
    <tr>
        <td width="50%" align="right">
            <label for="text_for_elt_models">
                <?= Loc::getMessage("REFERENCES_TEXT_FOR_ELT_MODELS") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="text_for_elt_models"
                   value="<?= htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "text_for_elt_models", "")); ?>"
                   id="text_for_elt_models"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="elt_models_title">
                <?= Loc::getMessage("REFERENCES_ELT_MODELS_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="elt_models_title"
                   value="<?= htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "elt_models_title", "")); ?>"
                   id="elt_models_title"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="elt_models_h1">
                <?= Loc::getMessage("REFERENCES_ELT_MODELS_H1") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="elt_models_h1"
                   value="<?= htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "elt_models_h1", "")); ?>"
                   id="elt_models_h1"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="elt_models_description">
                <?= Loc::getMessage("REFERENCES_ELT_MODELS_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="elt_models_description"
                   value="<?= htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "elt_models_description", "")); ?>"
                   id="elt_models_description"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="elt_models_keywords">
                <?= Loc::getMessage("REFERENCES_ELT_MODELS_KEYWORDS") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="elt_models_keywords"
                   value="<?= htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "elt_models_keywords", "")); ?>"
                   id="elt_models_keywords"
            />
        </td>
    </tr>
    <!-- end: models elts -->


    <tr>
        <td width="50%" align="right">
            <label for="text_for_models_version">
                <?=Loc::getMessage("REFERENCES_TEXT_FOR_MODELS_VERSION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="text_for_models_version"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "text_for_models_version", ""));?>"
                   id="text_for_models_version"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="models_version_title">
                <?=Loc::getMessage("REFERENCES_MODELS_VERSION_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="models_version_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "models_version_title", ""));?>"
                   id="models_version_title"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="models_version_h1">
                <?=Loc::getMessage("REFERENCES_MODELS_VERSION_H1") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="models_version_h1"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "models_version_h1", ""));?>"
                   id="models_version_h1"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="models_version_description">
                <?=Loc::getMessage("REFERENCES_MODELS_VERSION_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="models_version_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "models_version_description", ""));?>"
                   id="models_version_description"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="models_version_keywords">
                <?=Loc::getMessage("REFERENCES_MODELS_VERSION_KEYWORDS") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="models_version_keywords"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "models_version_keywords", ""));?>"
                   id="models_version_keywords"
            />
        </td>
    </tr>

    <tr>
        <td width="50%" align="right">
            <label for="pagenav_news_title">
                <?=Loc::getMessage("REFERENCES_PAGENAV_NEWS_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_news_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_news_title", ""));?>"
                   id="pagenav_news_title"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="pagenav_news_description">
                <?=Loc::getMessage("REFERENCES_PAGENAV_NEWS_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_news_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_news_description", ""));?>"
                   id="pagenav_news_description"
            />
        </td>
    </tr>


    <tr>
        <td width="50%" align="right">
            <label for="pagenav_instructions_title">
                <?=Loc::getMessage("REFERENCES_PAGENAV_INSTRUCTIONS_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_instructions_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_instructions_title", ""));?>"
                   id="pagenav_instructions_title"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="pagenav_instructions_description">
                <?=Loc::getMessage("REFERENCES_PAGENAV_INSTRUCTIONS_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_instructions_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_instructions_description", ""));?>"
                   id="pagenav_instructions_description"
            />
        </td>
    </tr>


    <tr>
        <td width="50%" align="right">
            <label for="pagenav_comments_title">
                <?=Loc::getMessage("REFERENCES_PAGENAV_COMMENTS_TITLE") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_comments_title"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_comments_title", ""));?>"
                   id="pagenav_comments_title"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="pagenav_comments_description">
                <?=Loc::getMessage("REFERENCES_PAGENAV_COMMENTS_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="pagenav_comments_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "pagenav_comments_description", ""));?>"
                   id="pagenav_comments_description"
            />
        </td>
    </tr>
    <tr>
        <td width="50%" align="right">
            <label for="filters_glue_description">
                <?=Loc::getMessage("REFERENCES_FILTERS_GLUE_DESCRIPTION") ?>:
            </label>
        </td>
        <td width="50%">
            <input type="text"
                   size="50"
                   name="filters_glue_description"
                   value="<?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "filters_glue_description", ""));?>"
                   id="filters_glue_description"
            />
        </td>
    </tr>
    <? $tabControl->Buttons(); ?>
    <button type="submit"
            name="save"
            value="templates_titles"
            class="adm-btn-save adm-btn">
        <?=Loc::getMessage("MAIN_SAVE") ?>
    </button>
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
</form>
<?php

$tabControl->end();

$aTabs = array(
    array(
        "DIV"                                       =>"edit3",
        "TAB"                                       =>Loc::getMessage("REFERENCES_TAB_DECLENSION_OF_WORDS"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_TAB_DECLENSION_OF_WORDS"),
    ),
);

$tabControl                                         = new CAdminTabControl("tabControl3", $aTabs);

$tabControl->begin();
?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <tr>
        <td colspan="2" width="100%">
            <h2 class="models-declension-title">
                <?=GetMessage('REFERENCES_CHAIN_DECLENSION');?>
            </h2>
            <?php

            $tpDB = CIBlockPropertyEnum::GetList(
                Array(
                    "DEF" => "DESC",
                    "SORT" => "ASC"),
                Array(
                    "IBLOCK_ID" => 17,
                    "CODE" => 'type_of_product'
                )
            );

            $type_of_product_enum = array();

            if($tpDB){
                while($tpFields = $tpDB->GetNext()){

                    if(isset($tpFields["ID"])){

                        $type_of_product_enum[$tpFields["ID"]] = $tpFields["VALUE"];

                    }

                }

            }

            $declension_models = unserialize(Option::get(ADMIN_MODULE_NAME, "declension_models", array()) || "");
            $declensionSizeof = (!empty($declension_models)
                && isset($declension_models['type_of_product']))
                ?  (sizeof($declension_models['type_of_product'])) : 1;

            if(!empty($type_of_product_enum)){

                for($counter = 0; $counter < $declensionSizeof; $counter++){
                    ?>
                    <div class="declension_models_row">
                        <select name="declension_models[type_of_product][]" class="declension_models_param">
                            <option><?=GetMessage('REFERENCES_CHOOSE_TYPE_OF_PRODUCT');?></option>
                            <?php foreach($type_of_product_enum as $tid => $tname): ?>
                                <option value="<?=$tid;?>"<?php if(isset($declension_models['type_of_product'][$counter]) && $declension_models['type_of_product'][$counter] == $tid):?> selected="selected"<? endif; ?>><?=$tname;?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="declension_models[declension][]" value="<?=isset($declension_models['declension'][$counter])?htmlspecialcharsbx($declension_models['declension'][$counter]):'';?>" class="declension_models_param" />
                        <input type="button" value="x"<?php if($counter == 0): ?> style="display: none;"<?php endif; ?> class="remove-chain" onclick="$(this).parent().remove();return false;" />
                    </div>
                    <?
                }?>
                <div>
                    <input type="button" id="clone-declension-models" value="<?php echo GetMessage('REFERENCES_DECLENSION_COPY');?>" />
                </div>
                <?php
            };

            ?>

        </td>
    </tr>
    <? $tabControl->Buttons(); ?>
    <button type="submit"
            name="save"
            value="declension_of_words"
            class="adm-btn-save adm-btn">
        <?=Loc::getMessage("MAIN_SAVE") ?>
    </button>
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
</form>
<?php

$tabControl->end();

$aTabs = array(
    array(
        "DIV"                                       =>"edit4",
        "TAB"                                       =>Loc::getMessage("REFERENCES_TAB_FILTER_BUILDER"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_TAB_FILTER_BUILDER"),
    ),
);

$tabControl                                         = new CAdminTabControl("tabControl4", $aTabs);

$tabControl->begin();
?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <?php

    $sProperties = array();
    $filter_parameter = array();

    if(IBLOCK_INCLUDED){

        $filter_parameter_sizeof = Option::get(ADMIN_MODULE_NAME, "filter_parameter_sizeof", "");

        for($i = 0; $i < $filter_parameter_sizeof; $i ++){

            $filter_parameter['id'][$i] = Option::get(ADMIN_MODULE_NAME, "filter_parameter_id".$i, "");
            $filter_parameter['value'][$i] = Option::get(ADMIN_MODULE_NAME, "filter_parameter_value".$i, "");

        }

        $sArFilter = array(
            'IBLOCK_ID' => 11,
            'ACTIVE' => 'Y',
        );

        $rsProperty = CIBlockProperty::GetList(
            array(),
            $sArFilter
        );

        if($rsProperty){
            while($sElement = $rsProperty->Fetch()){
                $sProperties[(mb_strtolower($sElement['CODE']))] = trim($sElement['NAME']);
            }
        }

        $pMinLength = is_array($filter_parameter['id']) && sizeof($filter_parameter['id']) ? sizeof($filter_parameter['id']) : 1;

        ?>
        <tr>
            <td colspan="2" width="100%">
                <h2 class="cart-props-title">
                    <?=GetMessage('REFERENCES_FILTERS_TITLES');?>
                </h2>
                <?php for($counter = 0; $counter < $pMinLength; $counter++): ?>
                    <div class="filter_parameter_row">
                        <select name="filter_parameter[id][]" class="filter_parameter">
                            <option><?php echo GetMessage('REFERENCES_CHOOSE_FILTER_PARAMETER');?></option>
                            <?php foreach($sProperties as $sPropId => $sPropName): ?>
                                <option<?php if(isset($filter_parameter['id'][$counter]) && $filter_parameter['id'][$counter] == $sPropId): ?> selected="selected"<?php endif; ?> value="<?=$sPropId;?>"><?=$sPropName;?></option>
                            <?php endforeach; ?>
                        </select>
                        <input class="filter_parameter" name="filter_parameter[value][]" type="text" placeholder="<?php echo GetMessage('REFERENCES_FILTER_VALUE');?>" value="<?php if(isset($filter_parameter['value'][$counter])): ?><?=htmlspecialcharsbx($filter_parameter['value'][$counter]);?><?php endif; ?>" />
                        <input type="button" value="x"<?php if($counter == 0): ?> style="display: none;"<?php endif; ?> class="remove-chain" onclick="$(this).parent().remove();return false;" />
                    </div>
                <?php endfor; ?>
                <div>
                    <input type="button" id="clone-filter-parameters" value="<?php echo GetMessage('REFERENCES_FILTER_COPY');?>" />
                </div>
            </td>
        </tr>
        <? $tabControl->Buttons(); ?>
        <button type="submit"
                name="save"
                value="filter_builder"
                class="adm-btn-save adm-btn">
            <?=Loc::getMessage("MAIN_SAVE") ?>
        </button>
        <input type="submit"
               name="restore"
               title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
               onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
               value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
        />
    <?php   } ?>
</form>
<?php

$tabControl->end();

$aTabs = array(
    array(
        "DIV"                                       =>"edit5",
        "TAB"                                       =>Loc::getMessage("REFERENCES_TAB_FILTER_DECLENSION"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_TAB_FILTER_DECLENSION"),
    ),
);

$tabControl                                         = new CAdminTabControl("tabControl5", $aTabs);

$tabControl->begin();
?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <?php

    $sProperties = array();
    $main_parameter = array();

    if(IBLOCK_INCLUDED){

        $main_parameter_sizeof = Option::get(ADMIN_MODULE_NAME, "main_parameter_sizeof", "");

        for($i = 0; $i < $main_parameter_sizeof; $i ++){

            $main_parameter['id'][$i] = Option::get(ADMIN_MODULE_NAME, "main_parameter_id".$i, "");
            $main_parameter['chain'][$i] = Option::get(ADMIN_MODULE_NAME, "main_parameter_chain".$i, "");
            $main_parameter['section'][$i] = Option::get(ADMIN_MODULE_NAME, "main_parameter_section".$i, "");
            $main_parameter['value'][$i] = Option::get(ADMIN_MODULE_NAME, "main_parameter_value".$i, "");

        }

        $sArFilter = array(
            'IBLOCK_ID' => 11,
            'ACTIVE' => 'Y',
        );

        $rsProperty = CIBlockProperty::GetList(
            array(),
            $sArFilter
        );

        if($rsProperty){
            while($sElement = $rsProperty->Fetch()){
                $sProperties[$sElement['ID']] = trim($sElement['NAME']);
            }
        }

        $pMinLength = is_array($main_parameter['id']) && sizeof($main_parameter['id']) ? sizeof($main_parameter['id']) : 1;

        $rSect = CIBlockSection::GetList(
            ($asOrder = array()),
            ($asFilter = array(
                'IBLOCK_ID' => 11,
                'ACTIVE' => 'Y')),
            false,
            ($asSelect = array('ID','NAME'))
        );

        $aSections = array();

        if($rSect){

            while($aSect = $rSect->GetNext()){
                $aSections[$aSect['ID']] = $aSect['NAME'];
            }

        }

        ?>
        <tr>
            <td colspan="2" width="100%">
                <h2 class="cart-props-title">
                    <?=GetMessage('REFERENCES_CHAIN_FILTERS');?>
                </h2>
                <?php for($counter = 0; $counter < $pMinLength; $counter++): ?>
                    <div class="main_parameter_row">
                        <select name="main_parameter[id][]" class="main_parameter">
                            <option><?php echo GetMessage('REFERENCES_CHOOSE_MAIN_PARAMETER');?></option>
                            <?php foreach($sProperties as $sPropId => $sPropName): ?>
                                <option<?php if(isset($main_parameter['id'][$counter]) && $main_parameter['id'][$counter] == $sPropId): ?> selected="selected"<?php endif; ?> value="<?=$sPropId;?>"><?=$sPropName;?></option>
                            <?php endforeach; ?>
                        </select>
                        <textarea class="main_parameter" name="main_parameter[value][]" type="text" placeholder="<?php echo GetMessage('REFERENCES_MAIN_VALUE');?>"><?php if(isset($main_parameter['value'][$counter])): ?><?=($main_parameter['value'][$counter]);?><?php endif; ?></textarea>
                        <select name="main_parameter[chain][]" class="main_parameter">
                            <option><?php echo GetMessage('REFERENCES_CHOOSE_CHAIN_PARAMETER');?></option>
                            <?php foreach($sProperties as $sPropId => $sPropName): ?>
                                <option<?php if(isset($main_parameter['id'][$counter]) && $main_parameter['chain'][$counter] == $sPropId): ?> selected="selected"<?php endif; ?> value="<?=$sPropId;?>"><?=$sPropName;?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="main_parameter[section][]" class="main_parameter">
                            <option><?php echo GetMessage('REFERENCES_CHOOSE_SECTION_PARAMETER');?></option>
                            <?php foreach($aSections as $sSectId => $sSectName): ?>
                                <option<?php if(isset($main_parameter['id'][$counter]) && $main_parameter['section'][$counter] == $sSectId): ?> selected="selected"<?php endif; ?> value="<?=$sSectId;?>"><?=$sSectName;?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="button" value="x"<?php if($counter == 0): ?> style="display: none;"<?php endif; ?> class="remove-chain" onclick="$(this).parent().remove();return false;" />
                    </div>
                <?php endfor; ?>
                <div>
                    <input type="button" id="clone-parameters" value="<?php echo GetMessage('REFERENCES_CHAIN_COPY');?>" />
                </div>
            </td>
        </tr>
        <? $tabControl->Buttons(); ?>
        <button type="submit"
                name="save"
                value="filter_declension"
                class="adm-btn-save adm-btn">
            <?=Loc::getMessage("MAIN_SAVE") ?>
        </button>
        <input type="submit"
               name="restore"
               title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
               onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
               value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
        />
    <?php   } ?>
</form>
<?php

$tabControl->end();

$aTabs = array(
    array(
        "DIV"                                       =>"edit6",
        "TAB"                                       =>Loc::getMessage("REFERENCES_TAB_BINDING_PROPS"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_TAB_BINDING_PROPS"),
    ),
);

$tabControl                                         = new CAdminTabControl("tabControl6", $aTabs);

$tabControl->begin();
?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <tr>
        <td colspan="2" width="100%">
            <h2 class="cart-props-title">
                <?=GetMessage('REFERENCES_ORDER_PROP_OPTIONS');?>
            </h2>
            <br />
            <?php for($current = 0; $current < $pccounter; $current++): ?>
                <div class="chained-row adm-bus-table-container caption border sale-order-props-group">
                    <div class="adm-bus-table-caption-title"><?=GetMessage('REFERENCES_CHAIN_PROPERTIES');?></div>
                    <div class="order-properties margin-bottom">
                        <div class="payments">
                            <div class="payments">
                                <div class="cart-fields">
                                    <label class="property-title">
                                        <?php echo GetMessage('REFERENCES_PAYMENT_LABEL');?>
                                    </label>
                                    <div class="property-value">
                                        <?php echo \My\Stat\CSaleFormatProperties::printPaymentSelect($chained['payments'][$current]);?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="payments">
                            <div class="payments">
                                <div class="cart-fields">
                                    <label class="property-title">
                                        <?php echo GetMessage('REFERENCES_DELIVERY_LABEL');?>                                                                    </label>
                                    <div class="property-value">
                                        <?php echo \My\Stat\CSaleFormatProperties::printDeliverySelect($chained['deliveries'][$current]);?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="payments">
                            <div class="payments">
                                <div class="cart-fields">
                                    <label class="property-title">
                                        <?php echo GetMessage('REFERENCES_APISHIP_DELIVERY_CODE_LABEL');?>                                                                    </label>
                                    <div class="property-value">
                                        <?php echo \My\Stat\CSaleFormatProperties::printDeliveryProviderCode($chained, $current);?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <hr />
                    <br />
                    <h3 class="cart-props-title">
                        <?php echo GetMessage('REFERENCES_SET_PROPERTIES'); ?>
                    </h3>
                    <br />
                    <br />
                    <?php

                    $arSaleProperties = \My\Stat\CSaleFormatProperties::getCSaleProperties($chained, $current);
                    
                    // $reflector = new \ReflectionClass('My\\Stat\\CSaleFormatProperties');
                    // var_dump($reflector->getFileName());

                    // var_dump($arSaleProperties["ORDER_PROP"]["USER_PROPS_N"]);
                    // var_dump("========N/Y========");
                    // var_dump($arSaleProperties["ORDER_PROP"]["USER_PROPS_Y"]);

                    if(isset($arSaleProperties["ORDER_PROP"])
                        && isset($arSaleProperties["ORDER_PROP"]["USER_PROPS_N"]))
                        \My\Stat\CSaleFormatProperties::PrintPropsForm($arSaleProperties["ORDER_PROP"]["USER_PROPS_N"]);

                    if(isset($arSaleProperties["ORDER_PROP"])
                        && isset($arSaleProperties["ORDER_PROP"]["USER_PROPS_Y"]))
                        \My\Stat\CSaleFormatProperties::PrintPropsForm($arSaleProperties["ORDER_PROP"]["USER_PROPS_Y"]);

                    ?>
                    <button class="remove-chain adm-btn adm-btn-remove"<?php if($current == 0): ?> style="display: none;"<?php endif; ?> onclick="$(this).parents('.chained-row').remove(); return false;">
                        <?=GetMessage('REFERENCES_PAYMENT_REMOVE');?>
                    </button>
                </div>
            <?php endfor; ?>
            <div class="buttons">
                <button id="clone-chain" class="adm-btn">
                    <?=GetMessage('REFERENCES_PAYMENT_ADD_MORE');?>
                </button>
            </div>
        </td>
    </tr>
    <? $tabControl->Buttons(); ?>
    <button type="submit"
            name="save"
            value="binding_props"
            class="adm-btn-save adm-btn">
        <?=Loc::getMessage("MAIN_SAVE") ?>
    </button>
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
</form>
<?php
$tabControl->end();
?>
<?php

$aTabs = array(
    array(
        "DIV"                                       =>"edit7",
        "TAB"                                       =>Loc::getMessage("REFERENCES_TAB_SAO_PROPS"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_TAB_SAO_PROPS"),
    ),
);

$tabControl                                         = new CAdminTabControl("tabControl7", $aTabs);

$tabControl->begin();
?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <tr>
        <td width="50%" align="right">
            <label for="paymentid_sao_renderoptin">
                <?=Loc::getMessage("REFERENCES_PAYMENTID_SAO_RENDEROPTIN") ?>:
            </label>
        </td>
        <td width="50%">
            <?php

            $paymentid_sao_renderoptin = Option::get(ADMIN_MODULE_NAME, "paymentid_sao_renderoptin", "");
            $paymentid_sao_renderoptin = explode(',',$paymentid_sao_renderoptin);
            $paymentid_sao_renderoptin = !is_array($paymentid_sao_renderoptin) ? array($paymentid_sao_renderoptin) : $paymentid_sao_renderoptin;
            echo \My\Stat\CSaleFormatProperties::printPaymentSelect($paymentid_sao_renderoptin,"paymentid_sao_renderoptin[]",'multiple="multiple"');?>
        </td>
    </tr>
    <tr>
        <td colspan="2" width="100%">
            <h2 class="cart-props-title">
                <?=GetMessage('REFERENCES_TAB_SAO_PROPS');?>
            </h2>
            <br />
            <?php for($current = 0; $current < $saocounter; $current++): ?>
                <div class="sao-row adm-bus-table-container caption border sale-order-props-group">
                    <div class="order-properties margin-bottom">
                        <div class="payments">
                            <div class="payments">
                                <div class="cart-fields">
                                    <label class="property-title">
                                        <?php echo GetMessage('REFERENCES_TAB_SAO_DELIVERY_LABEL');?>                                                                    </label>
                                    <div class="property-value">
                                        <?php echo \My\Stat\CSaleFormatProperties::printDeliverySelect($sao_delivery_id[$current],'sao_delivery_id[]');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="payments">
                            <div class="payments">
                                <div class="cart-fields">
                                    <label class="property-title">
                                        <?php echo GetMessage('REFERENCES_TAB_SAO_DELIVERY_DATE_LABEL');?>                                                                    </label>
                                    <div class="property-value">
                                        <input type="text" name="sao_delivery_days[]" value="<?php echo htmlspecialcharsbx($sao_delivery_days[$current]); ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="remove-sao adm-btn adm-btn-remove"<?php if($current == 0): ?> style="display: none;"<?php endif; ?> onclick="$(this).parents('.sao-row').remove(); return false;">
                        <?=GetMessage('REFERENCES_SAO_REMOVE');?>
                    </button>
                </div>
            <?php endfor; ?>
            <div class="buttons">
                <button id="clone-sao" class="adm-btn">
                    <?=GetMessage('REFERENCES_PAYMENT_ADD_MORE');?>
                </button>
            </div>
        </td>
    </tr>
    <? $tabControl->Buttons(); ?>
    <button type="submit"
            name="save"
            value="binding_props"
            class="adm-btn-save adm-btn">
        <?=Loc::getMessage("MAIN_SAVE") ?>
    </button>
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
</form>
<?php
$tabControl->end();

?>
<?php

$aTabs = array(
    array(
        "DIV"                                       =>"edit8",
        "TAB"                                       =>Loc::getMessage("REFERENCES_PAYMENTID_SAO_RENDEROPTIN"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_PAYMENTID_SAO_RENDEROPTIN"),
    ),
);

$tabControl                                         = new CAdminTabControl("tabControl8", $aTabs);

$tabControl->begin();

?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <tr>
        <td colspan="2" width="100%">
            <?php for($current = 0; $current < $saotypeidcounter; $current++): ?>
                <div class="sao-row adm-bus-table-container caption border sale-order-props-group">
                    <div class="order-properties margin-bottom">
                        <div class="payments">
                            <div class="payments">
                                <div class="cart-fields">
                                    <label>
                                        <?=Loc::getMessage("REFERENCES_PAYMENTID_SAO_RENDEROPTIN") ?>:
                                    </label>
                                    <?php
                                    echo \My\Stat\CSaleFormatProperties::printPaymentSelect($sao_payment_id[$current],"sao_payment_id[]");?>
                                </div>
                            </div>
                        </div>
                        <div class="payments">
                            <div class="payments">
                                <div class="cart-fields">
                                    <label class="property-title">
                                        <?php echo GetMessage('REFERENCES_TAB_SAO_PERSONTYPEID_LABEL');?>
                                    </label>
                                    <div class="property-value">
                                        <?php
                                        echo \My\Stat\CSaleFormatProperties::printPersonType($sao_persontype_id[$current],"sao_persontype_id[]");?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="remove-sao adm-btn adm-btn-remove"<?php if($current == 0): ?> style="display: none;"<?php endif; ?> onclick="$(this).parents('.sao-row').remove(); return false;">
                        <?=GetMessage('REFERENCES_SAO_REMOVE');?>
                    </button>
                </div>
            <?php endfor; ?>
            <div class="buttons">
                <button id="clone-sao-typeid" class="adm-btn">
                    <?=GetMessage('REFERENCES_PAYMENT_ADD_MORE');?>
                </button>
            </div>
        </td>
    </tr>
    <? $tabControl->Buttons(); ?>
    <button type="submit"
            name="save"
            value="binding_props"
            class="adm-btn-save adm-btn">
        <?=Loc::getMessage("MAIN_SAVE") ?>
    </button>
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
</form>
<?php
$tabControl->end();

$aTabs = array(
    array(
        "DIV"                                       =>"edit9",
        "TAB"                                       =>Loc::getMessage("REFERENCES_TAB_DECLENSION_SERIES_OF_WORDS"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_TAB_DECLENSION_SERIES_OF_WORDS"),
    ),
);

$tabControl                                         = new CAdminTabControl("tabControl3", $aTabs);

$tabControl->begin();
?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <tr>
        <td colspan="2" width="100%">
            <h2 class="models-declension-series-title">
                <?=GetMessage('REFERENCES_CHAIN_DECLENSION_SERIES');?>
            </h2>
            <?php

            $tpDB = CIBlockPropertyEnum::GetList(
                Array(
                    "DEF" => "DESC",
                    "SORT" => "ASC"),
                Array(
                    "IBLOCK_ID" => 17,
                    "CODE" => 'type_of_product'
                )
            );

            $type_of_product_enum = array();

            if($tpDB){
                while($tpFields = $tpDB->GetNext()){

                    if(isset($tpFields["ID"])){

                        $type_of_product_enum[$tpFields["ID"]] = $tpFields["VALUE"];

                    }

                }

            }

            $declension_series_models = unserialize(Option::get(ADMIN_MODULE_NAME, "declension_series_models", array()) || "");
            $declension_seriesSizeof = (!empty($declension_series_models)
                && isset($declension_series_models['type_of_product']))
                ?  (sizeof($declension_series_models['type_of_product'])) : 1;

            if(!empty($type_of_product_enum)){

                for($counter = 0; $counter < $declension_seriesSizeof; $counter++){
                    ?>
                    <div class="declension_series_models_row">
                        <select name="declension_series_models[type_of_product][]" class="declension_series_models_param">
                            <option><?=GetMessage('REFERENCES_CHOOSE_TYPE_OF_PRODUCT');?></option>
                            <?php foreach($type_of_product_enum as $tid => $tname): ?>
                                <option value="<?=$tid;?>"<?php if(isset($declension_series_models['type_of_product'][$counter]) && $declension_series_models['type_of_product'][$counter] == $tid):?> selected="selected"<? endif; ?>><?=$tname;?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="declension_series_models[declension][]" value="<?=isset($declension_series_models['declension'][$counter])?htmlspecialcharsbx($declension_series_models['declension'][$counter]):'';?>" class="declension_series_models_param" />
                        <input type="button" value="x"<?php if($counter == 0): ?> style="display: none;"<?php endif; ?> class="remove-chain" onclick="$(this).parent().remove();return false;" />
                    </div>
                    <?
                }?>
                <div>
                    <input type="button" id="clone-declension-series-models" value="<?php echo GetMessage('REFERENCES_DECLENSION_SERIES_COPY');?>" />
                </div>
                <?php
            };

            ?>

        </td>
    </tr>
    <? $tabControl->Buttons(); ?>
    <button type="submit"
            name="save"
            value="declension_series_of_words"
            class="adm-btn-save adm-btn">
        <?=Loc::getMessage("MAIN_SAVE") ?>
    </button>
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
</form>
<?php

$tabControl->end();

$aTabs = array(
    array(
        "DIV"                                       =>"edit9",
        "TAB"                                       =>Loc::getMessage("REFERENCES_CHAIN_DECLENSION_PRODUCTS"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_CHAIN_DECLENSION_PRODUCTS"),
    ),
);

$tabControl                                         = new CAdminTabControl("tabControl10", $aTabs);

$tabControl->begin();
?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <tr>
        <td colspan="2" width="100%">
            <h2 class="models-declension-title">
                <?=GetMessage('REFERENCES_CHAIN_DECLENSION_PRODUCTS');?>
            </h2>
            <?php

            $tpDB = CIBlockPropertyEnum::GetList(
                Array(
                    "DEF" => "DESC",
                    "SORT" => "ASC"),
                Array(
                    "IBLOCK_ID" => 11,
                    "CODE" => 'TYPEPRODUCT'
                )
            );

            $type_of_product_enum = array();

            if($tpDB){
                while($tpFields = $tpDB->GetNext()){

                    if(isset($tpFields["ID"])){

                        $type_of_product_enum[$tpFields["ID"]] = $tpFields["VALUE"];

                    }

                }

            }

            $declension_products = unserialize(Option::get(ADMIN_MODULE_NAME, "declension_products", array()) || "");
            $declensionSizeof = (!empty($declension_products)
                && isset($declension_products['type_of_product']))
                ?  (sizeof($declension_products['type_of_product'])) : 1;

            if(!empty($type_of_product_enum)){

                for($counter = 0; $counter < $declensionSizeof; $counter++){
                    ?>
                    <div class="declension_products_row">
                        <select name="declension_products[type_of_product][]" class="declension_products_param">
                            <option><?=GetMessage('REFERENCES_CHOOSE_TYPE_OF_PRODUCT');?></option>
                            <?php foreach($type_of_product_enum as $tid => $tname): ?>
                                <option value="<?=$tid;?>"<?php if(isset($declension_products['type_of_product'][$counter]) && $declension_products['type_of_product'][$counter] == $tid):?> selected="selected"<? endif; ?>><?=$tname;?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="declension_products[declension][]" value="<?=isset($declension_products['declension'][$counter])?htmlspecialcharsbx($declension_products['declension'][$counter]):'';?>" class="declension_products_param" />
                        <input type="button" value="x"<?php if($counter == 0): ?> style="display: none;"<?php endif; ?> class="remove-chain" onclick="$(this).parent().remove();return false;" />
                    </div>
                    <?
                }?>
                <div>
                    <input type="button" id="clone-declension-products" value="<?php echo GetMessage('REFERENCES_DECLENSION_COPY');?>" />
                </div>
                <?php
            };

            ?>

        </td>
    </tr>
    <? $tabControl->Buttons(); ?>
    <button type="submit"
            name="save"
            value="declension_of_words"
            class="adm-btn-save adm-btn">
        <?=Loc::getMessage("MAIN_SAVE") ?>
    </button>
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
</form>
<?php

$tabControl->end();

$aTabs = array(
    array(
        "DIV"                                       =>"edit9",
        "TAB"                                       =>Loc::getMessage("REFERENCES_MIN_DVR_PRICE"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_MIN_DVR_PRICE"),
    ),
);

$tabControl = new CAdminTabControl("tabControl11", $aTabs);

$per_dvr_price = Option::get(ADMIN_MODULE_NAME, "per_dvr_price", "");
$min_dvr_price = Option::get(ADMIN_MODULE_NAME, "min_dvr_price", "");

$tabControl->begin();
?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <tr>
        <td width="50%" class="adm-detail-content-cell-l" align="right">
            <label class="property-title" for="min_dvr_price">
                <?=GetMessage('REFERENCES_MIN_DVR_PRICE');?>
            </label>
        </td>
        <td width="50%" class="adm-detail-content-cell-r">
            <input type="text" id="min_dvr_price" name="min_dvr_price" value="<?=isset($min_dvr_price)?htmlspecialcharsbx($min_dvr_price):'';?>" class="declension_products_param" />
        </td>
    </tr>
    <tr>
        <td width="50%" class="adm-detail-content-cell-l" align="right">
            <label class="property-title" for="per_dvr_price">
                <?=GetMessage('REFERENCES_PER_DVR_PRICE');?>
            </label>
        </td>
        <td width="50%" class="adm-detail-content-cell-r">
            <input type="text" id="per_dvr_price" name="per_dvr_price" value="<?=isset($per_dvr_price)?htmlspecialcharsbx($per_dvr_price):'';?>" class="declension_products_param" />
        </td>
    </tr>
    <? $tabControl->Buttons(); ?>
    <button type="submit"
            name="save"
            value="declension_of_words"
            class="adm-btn-save adm-btn">
        <?=Loc::getMessage("MAIN_SAVE") ?>
    </button>
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
</form>
<?php

$tabControl->end();

$aTabs = array(
    array(
        "DIV"                                       =>"edit14",
        "TAB"                                       =>Loc::getMessage("REFERENCES_DATES_WEEKEND"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_DATES_WEEKEND"),
    ),
);

$tabControl                                         = new CAdminTabControl("tabControl14", $aTabs);

$tabControl->begin();
?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <tr>
        <td colspan="2" width="100%">
            <p><?=GetMessage('REFERENCES_DATES_WEEKEND_DESC');?></p>
            <textarea
                    style="width: 100%; max-width: 100%; width: 100%; min-height: 240px;"
                    name="order_weekends"
                    id="order_weekends"><?
                echo (Option::get(ADMIN_MODULE_NAME, "order_weekends", ""));
                ?></textarea>
        </td>
    </tr>
    <? $tabControl->Buttons(); ?>
    <button type="submit"
            name="save"
            value="declension_series_of_words"
            class="adm-btn-save adm-btn">
        <?=Loc::getMessage("MAIN_SAVE") ?>
    </button>
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
</form>
<?php

$tabControl->end();

$aTabs = array(
    array(
        "DIV"                                       =>"edit9",
        "TAB"                                       =>Loc::getMessage("REFERENCES_TAGS_PRODUCTS"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_TAGS_PRODUCTS"),
    ),
);

$tabControl                                         = new CAdminTabControl("tabControl15", $aTabs);

$tabControl->begin();
?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <tr>
        <td colspan="2" width="100%">
            <h2 class="models-declension-title">
                <?=GetMessage('REFERENCES_TAGS_PRODUCTS');?>
            </h2>
            <?php

            $tags_replaces = unserialize(Option::get(ADMIN_MODULE_NAME, "tags_replaces", array()) || "");
            $tagsSizeof = (isset($tags_replaces['name']) && !empty($tags_replaces['name']))
                ?  (sizeof($tags_replaces['name'])) : 1;

            for($counter = 0; $counter < $tagsSizeof; $counter++){
                ?>
                <div class="tags_replaces_row">
                    <input type="text" name="tags_replaces[name][]" value="<?=isset($tags_replaces['name'][$counter])?htmlspecialcharsbx($tags_replaces['name'][$counter]):'';?>" class="declension_products_param" />
                    <input type="text" name="tags_replaces[replace][]" value="<?=isset($tags_replaces['replace'][$counter])?htmlspecialcharsbx($tags_replaces['replace'][$counter]):'';?>" class="declension_products_param" />
                    <input type="button" value="x"<?php if($counter == 0): ?> style="display: none;"<?php endif; ?> class="remove-chain" onclick="$(this).parent().remove();return false;" />
                </div>
                <?
            }?>
            <div>
                <input type="button" id="clone-tags" value="<?php echo GetMessage('REFERENCES_TAGS_COPY');?>" />
            </div>
            <?php

            ?>

        </td>
    </tr>
    <? $tabControl->Buttons(); ?>
    <button type="submit"
            name="save"
            value="declension_of_words"
            class="adm-btn-save adm-btn">
        <?=Loc::getMessage("MAIN_SAVE") ?>
    </button>
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
</form>
<?php

$tabControl->end();

$aTabs = array(
    array(
        "DIV"                                       =>"edit19",
        "TAB"                                       =>Loc::getMessage("REFERENCES_NOFOLLOW_FILTERS"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_NOFOLLOW_FILTERS"),
    ),
);

$tabControl                                         = new CAdminTabControl("tabControl19", $aTabs);

$tabControl->begin();
?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <?php

    $sProperties = array();
    $nofollow_parameter = array();

    if(IBLOCK_INCLUDED){

        $nofollow_parameter_sizeof = Option::get(ADMIN_MODULE_NAME, "nofollow_parameter_sizeof", "");

        for($i = 0; $i < $nofollow_parameter_sizeof; $i ++){

            $nofollow_parameter['chain'][$i] = Option::get(ADMIN_MODULE_NAME, "nofollow_parameter_chain".$i, "");
            $nofollow_parameter['section'][$i] = Option::get(ADMIN_MODULE_NAME, "nofollow_parameter_section".$i, "");

        }

        $sArFilter = array(
            'IBLOCK_ID' => 11,
            'ACTIVE' => 'Y',
        );

        $rsProperty = CIBlockProperty::GetList(
            array('NAME' => 'ASC'),
            $sArFilter
        );

        if($rsProperty){
            while($sElement = $rsProperty->Fetch()){
                $sProperties[$sElement['ID']] = trim($sElement['NAME']).' ('.$sElement['CODE'].')';
            }
        }

        $pMinLength = is_array($nofollow_parameter['chain']) && sizeof($nofollow_parameter['chain']) ? sizeof($nofollow_parameter['chain']) : 1;

        $rSect = CIBlockSection::GetList(
            ($asOrder = array('NAME' => 'ASC')),
            ($asFilter = array(
                'IBLOCK_ID' => 11,
                'ACTIVE' => 'Y')),
            false,
            ($asSelect = array('ID','NAME','CODE'))
        );

        $aSections = array();

        if($rSect){

            while($aSect = $rSect->GetNext()){
                $aSections[$aSect['ID']] = $aSect['NAME'].' ('.$aSect['CODE'].')';
            }

        }

        ?>
        <tr>
            <td colspan="2" width="100%">
                <h2 class="cart-props-title">
                    <?=GetMessage('REFERENCES_NOFOLLOW_FILTERS');?>
                </h2>
                <?php for($counter = 0; $counter < $pMinLength; $counter++): ?>
                    <div class="nofollow_parameter_row">
                        <select name="nofollow_parameter[chain][]" class="nofollow_parameter">
                            <option><?php echo GetMessage('REFERENCES_CHOOSE_CHAIN_PARAMETER');?></option>
                            <?php foreach($sProperties as $sPropId => $sPropName): ?>
                                <option<?php if(isset($nofollow_parameter['chain'][$counter]) && $nofollow_parameter['chain'][$counter] == $sPropId): ?> selected="selected"<?php endif; ?> value="<?=$sPropId;?>"><?=$sPropName;?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="nofollow_parameter[section][]" class="nofollow_parameter">
                            <option><?php echo GetMessage('REFERENCES_CHOOSE_SECTION_PARAMETER');?></option>
                            <?php foreach($aSections as $sSectId => $sSectName): ?>
                                <option<?php if(isset($nofollow_parameter['section'][$counter]) && $nofollow_parameter['section'][$counter] == $sSectId): ?> selected="selected"<?php endif; ?> value="<?=$sSectId;?>"><?=$sSectName;?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="button" value="x"<?php if($counter == 0): ?> style="display: none;"<?php endif; ?> class="remove-chain" onclick="$(this).parent().remove();return false;" />
                    </div>
                <?php endfor; ?>
                <div>
                    <input type="button" id="clone-nofollow-parameters" value="<?php echo GetMessage('REFERENCES_CHAIN_COPY');?>" />
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%" align="right">
                <label for="filters_preg">
                    <?=Loc::getMessage("REFERENCES_NOFOLLOW_FILTERS_PREG") ?>:
                </label>
            </td>
            <td width="60%" align="left">
				<textarea
                        rows="10"
                        name="filters_preg"
                        id="filters_preg"
                ><?=htmlspecialcharsbx(Option::get(ADMIN_MODULE_NAME, "filters_preg", ""));?></textarea>
            </td>
        </tr>
        <? $tabControl->Buttons(); ?>
        <button type="submit"
                name="save"
                value="nofollow"
                class="adm-btn-save adm-btn">
            <?=Loc::getMessage("MAIN_SAVE") ?>
        </button>
        <input type="submit"
               name="restore"
               title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
               onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
               value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
        />
    <?php   } ?>
</form>
<?php

$tabControl->end();

?>
<?php

$aTabs = array(
    array(
        "DIV"                                       =>"edit21",
        "TAB"                                       =>Loc::getMessage("REFERENCES_TIMEINTERVALS"),
        "TITLE"                                     =>Loc::getMessage("REFERENCES_TIMEINTERVALS"),
    ),
);

$tabControl                                         = new CAdminTabControl("tabControl21", $aTabs);

$tabControl->begin();
?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <? $tabControl->beginNextTab(); ?>
    <? echo bitrix_sessid_post(); ?>
    <tr>
        <td colspan="2" width="100%">
            <h2 class="models-declension-title">
                <?=GetMessage('REFERENCES_TIMEINTERVALS');?>
            </h2>
            <?php

            $timeInterval = impelDeliveryInterval::getTimeInterval();

            $aTimeInterval = unserialize(Option::get(ADMIN_MODULE_NAME, "references_timeintervals", array()) || "");
            $iTimeIntervalSizeof = (isset($aTimeInterval['times']) && !empty($aTimeInterval['times']))
                ?  count($aTimeInterval['times']) : 1;

            for($counter = 0; $counter < $iTimeIntervalSizeof; $counter++){
                ?>
                <div class="freplaces_replaces_row">
                    <select class="declension_models_param"
                            name="references_timeintervals[times][]">
                        <option value=""><?= GetMessage('SOA_TIMEOFDELIVERY'); ?></option>
                        <?php foreach ($timeInterval as $timetext => $timevalue): ?>
                            <option<?php if ((isset($aTimeInterval['times'][$counter]) && $aTimeInterval['times'][$counter] == $timevalue)): ?> selected="selected"<? endif; ?>
                                    value="<?= $timevalue; ?>"><?= $timetext; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="references_timeintervals[counts][]" value="<?=isset($aTimeInterval['counts'][$counter])?htmlspecialcharsbx($aTimeInterval['counts'][$counter]):'';?>" class="declension_products_param" />
                    <input type="button" value="x"<?php if($counter == 0): ?> style="display: none;"<?php endif; ?> class="remove-chain" onclick="$(this).parent().remove();return false;" />
                </div>
                <?
            }?>
            <div>
                <input type="button" id="clone-timeintervals" value="<?php echo GetMessage('REFERENCES_FREPLACES_COPY');?>" />
            </div>
        </td>
    </tr>
    <? $tabControl->Buttons(); ?>
    <button type="submit"
            name="save"
            value="declension_of_words"
            class="adm-btn-save adm-btn">
        <?=Loc::getMessage("MAIN_SAVE") ?>
    </button>
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
</form>
<?php

$tabControl->end();

?>
<?php

$propertyDB = CIBlockPropertyEnum::GetList(
    Array(
        "DEF"=>"DESC",
        "SORT"=>"ASC"
    ),
    Array(
        "IBLOCK_ID" => 45,
        "CODE" => 'DOMAIN'
    )
);

$aDomains = [];

if($propertyDB) {
    while ($propertyFields = $propertyDB->GetNext()) {

        if (isset($propertyFields["XML_ID"])
            && isset($propertyFields["VALUE"])
        ) {
            $aDomains[] = $propertyFields["VALUE"];
        }
    }
}

$rsProps = CIBlockProperty::GetList(
    array('SORT' => 'ASC', 'NAME' => 'ASC'),
    array('IBLOCK_ID' => 11, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N', 'USER_TYPE' => 'ElementSiteCity')
);

$aDomainCodes = [];

if ($rsProps) {
    while ($arProp = $rsProps->Fetch()) {
        $aDomainCodes[] = $arProp['CODE'];
    }
}

if (count($aDomainCodes) && count($aDomains)) {

    $aTabs = array(
        array(
            "DIV"                                       =>"edit22",
            "TAB"                                       =>Loc::getMessage("REFERENCES_DOMAIN_PROPS"),
            "TITLE"                                     =>Loc::getMessage("REFERENCES_DOMAIN_PROPS"),
        ),
    );

    $tabControl                                         = new CAdminTabControl("tabControl22", $aTabs);

    $tabControl->begin();

    ?>
    <form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
        <? $tabControl->beginNextTab(); ?>
        <? echo bitrix_sessid_post(); ?>
        <tr>
            <td colspan="2" width="100%">
                <h2 class="models-declension-title">
                    <?=GetMessage('REFERENCES_DOMAIN_PROPS');?>
                </h2>
                <?php

                $aDomainProps = unserialize(Option::get(ADMIN_MODULE_NAME, "references_domain_props", array()) || "");
                $iDomainPropsSizeof = (isset($aDomainProps['props']) && !empty($aDomainProps['props']))
                    ?  count($aDomainProps['props']) : 1;

                for($counter = 0; $counter < $iDomainPropsSizeof; $counter++){
                    ?>
                    <div class="domain_props_replaces_row">
                        <select class="declension_models_param"
                                name="references_domain_props[domains][]">
                            <option value=""><?=GetMessage('REFERENCES_CHOOSE_DOMAINS_PARAMETER'); ?></option>
                            <?php foreach ($aDomains as $proptext): ?>
                                <option<?php if ((isset($aDomainProps['domains'][$counter]) && $aDomainProps['domains'][$counter] == $proptext)): ?> selected="selected"<? endif; ?>
                                        value="<?= $proptext; ?>"><?= $proptext; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <select class="declension_models_param"
                                name="references_domain_props[props][]">
                            <option value=""><?=GetMessage('REFERENCES_CHOOSE_DOMAIN_PROPS_PARAMETER'); ?></option>
                            <?php foreach ($aDomainCodes as $proptext): ?>
                                <option<?php if ((isset($aDomainProps['props'][$counter]) && $aDomainProps['props'][$counter] == $proptext)): ?> selected="selected"<? endif; ?>
                                        value="<?= $proptext; ?>"><?= $proptext; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="references_domain_props[codes][]" value="<?=isset($aDomainProps['codes'][$counter])?htmlspecialcharsbx($aDomainProps['codes'][$counter]):'';?>" class="declension_products_param" />
                        <input type="button" value="x"<?php if($counter == 0): ?> style="display: none;"<?php endif; ?> class="remove-chain" onclick="$(this).parent().remove();return false;" />
                    </div>
                    <?
                }?>
                <div>
                    <input type="button" id="clone-domain-props" value="<?php echo GetMessage('REFERENCES_FREPLACES_COPY');?>" />
                </div>
            </td>
        </tr>
        <? $tabControl->Buttons(); ?>
        <button type="submit"
                name="save"
                value="declension_of_words"
                class="adm-btn-save adm-btn">
            <?=Loc::getMessage("MAIN_SAVE") ?>
        </button>
        <input type="submit"
               name="restore"
               title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
               onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
               value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
        />
    </form>
    <?php

    $tabControl->end();

}

$tabControl->end();

?>
<script type="text/javascript">
    //<!--

    $("#clone-timeintervals").click(function(event){
        $("#clone-timeintervals").parent().prev().clone().insertBefore($("#clone-timeintervals")).find('.remove-chain').css('display','block');
        event.preventDefault();
        return false;
    });

    $("#clone-sao-typeid").click(function(event){
        $("#clone-sao-typeid").parent().prev().clone().insertBefore($("#clone-sao-typeid")).find('.remove-sao').css('display','block');
        event.preventDefault();
        return false;
    });

    $("#clone-sao").click(function(event){
        $("#clone-sao").parent().prev().clone().insertBefore($("#clone-sao")).find('.remove-sao').css('display','block');
        event.preventDefault();
        return false;
    });

    $("#clone-tags").click(function(event){
        $("#clone-tags").parent().prev().clone().insertBefore($("#clone-tags")).find('.remove-chain').css('display','block');
        event.preventDefault();
        return false;
    });

    $("#clone-declension-products").click(function(event){
        $("#clone-declension-products").parent().prev().clone().insertBefore($("#clone-declension-products")).find('.remove-chain').css('display','block');
        event.preventDefault();
        return false;
    });

    $("#clone-chain").click(function(event){
        $("#clone-chain").parent().prev().clone().insertBefore($("#clone-chain")).find('.remove-chain').css('display','block');
        event.preventDefault();
        return false;
    });

    $("#clone-nofollow-parameters").click(function(event){
        $("#clone-nofollow-parameters").parent().prev().clone().insertBefore($("#clone-nofollow-parameters")).find('.remove-chain').css('display','block');
        event.preventDefault();
        return false;
    });

    $("#clone-parameters").click(function(event){
        $("#clone-parameters").parent().prev().clone().insertBefore($("#clone-parameters")).find('.remove-chain').css('display','block');
        event.preventDefault();
        return false;
    });

    $("#clone-declension-models").click(function(event){
        $("#clone-declension-models").parent().prev().clone().insertBefore($("#clone-declension-models")).find('.remove-chain').css('display','block');
        event.preventDefault();
        return false;
    });

    $("#clone-declension-series-models").click(function(event){
        $("#clone-declension-series-models").parent().prev().clone().insertBefore($("#clone-declension-series-models")).find('.remove-chain').css('display','block');
        event.preventDefault();
        return false;
    });

    $("#clone-filter-parameters").click(function(event){
        $("#clone-filter-parameters").parent().prev().clone().insertBefore($("#clone-filter-parameters")).find('.remove-chain').css('display','block');
        event.preventDefault();
        return false;
    });

    $("#clone-domain-props").click(function(event){
        $("#clone-domain-props").parent().prev().clone().insertBefore($("#clone-domain-props")).find('.remove-chain').css('display','block');
        event.preventDefault();
        return false;
    });

    //-->
</script>