<?php

namespace My\Stat;

use Bitrix\Main,
    Bitrix\Main\Entity,
    Bitrix\Main\Localization\Loc as Loc,
    Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem,
    Bitrix\Sale,
    Bitrix\Sale\Order,
    Bitrix\Sale\DiscountCouponsManager,
    Bitrix\Main\Context;

Loc::loadMessages(__FILE__);

/**
 * Class DataTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> CREATED datetime mandatory
 * <li> USER_ID int optional
 * <li> DATA_VALUE string(64) mandatory
 * </ul>
 *
 * @package Bitrix\Data
 **/

class DataTable extends Entity\DataManager{

public static function getFilePath(){
    return __FILE__;
}

public static function displayActPrint(&$list){

    global $APPLICATION;

    if($APPLICATION->GetCurPage() == "/bitrix/admin/sale_order.php"){

        $statusStyle = '
            <style>
                .PROP_rupost_spy_N,
                .PROP_rupost_spy_PE,
                .PROP_rupost_spy_PP,
                .PROP_rupost_spy_BA,
                .PROP_rupost_spy_BO,
                .PROP_rupost_spy_BP,
                .PROP_rupost_spy_BOP,
                .PROP_rupost_spy_BI,
                .PROP_rupost_spy_BZ,
                .PROP_rupost_spy_BAK,
                .PROP_rupost_spy_BBO,
                .PROP_rupost_spy_BOA,
                .PROP_rupost_spy_BCA,
                .PROP_rupost_spy_BNA,
                .PROP_rupost_spy_BOT,
                .PROP_rupost_spy_BSN,
                .PROP_rupost_spy_BIO,
                .PROP_rupost_spy_BHA,
                .PROP_rupost_spy_DPZ,
                .PROP_rupost_spy_DBA,
                .PROP_rupost_spy_DPZA,
                .PROP_rupost_spy_NU,
                .PROP_rupost_spy_NI,
                .PROP_rupost_spy_NZ,
                .PROP_rupost_spy_NRT,
                .PROP_rupost_spy_NO,
                .PROP_rupost_spy_XD,
                .PROP_rupost_spy_XA,
                .PROP_rupost_spy_XU,
                .PROP_rupost_spy_XC,
                .PROP_rupost_spy_XVT,
                .PROP_rupost_spy_VN,
                .PROP_rupost_spy_VNB,
                .PROP_rupost_spy_VXC,
                .PROP_rupost_spy_OC,
                .PROP_rupost_spy_OPP,
                .PROP_rupost_spy_OPM,
                .PROP_rupost_spy_OPC,
                .PROP_rupost_spy_OPPC,
                .PROP_rupost_spy_OMO,
                .PROP_rupost_spy_OPO,
                .PROP_rupost_spy_OPT,
                .PROP_rupost_spy_OPTM,
                .PROP_rupost_spy_OPPP,
                .PROP_rupost_spy_OIC,
                .PROP_rupost_spy_OPR,
                .PROP_rupost_spy_OIP,
                .PROP_rupost_spy_OPTRF,
                .PROP_rupost_spy_OPCV,
                .PROP_rupost_spy_OPK,
                .PROP_rupost_spy_IMP,
                .PROP_rupost_spy_AMP,
                .PROP_rupost_spy_PNT,
                .PROP_rupost_spy_NPV,
                .PROP_rupost_spy_NPD,
                .PROP_rupost_spy_NPN,
                .PROP_rupost_spy_NPA,
                .PROP_rupost_spy_NPAV,
                .PROP_rupost_spy_NPOV,
                .PROP_rupost_spy_NPON,
                .PROP_rupost_spy_NPVI,
                .PROP_rupost_spy_NPAO,
                .PROP_rupost_spy_NPVA,
                .PROP_rupost_spy_NPTA,
                .PROP_rupost_spy_NPXA,
                .PROP_rupost_spy_RO,
                .PROP_rupost_spy_TOV,
                .PROP_rupost_spy_TOVT,
                .PROP_rupost_spy_TOOT,
                .PROP_rupost_spy_TOOV,
                .PROP_rupost_spy_TOTV,
                .PROP_rupost_spy_TUTP,
                .PROP_rupost_spy_PVX,
                .PROP_rupost_spy_UN,
                .PROP_rupost_spy_PVB,
                .PROP_rupost_spy_RU
                {
                    width: 100%;
                    float: left;
                    margin: 0 0 0 -16px;
                    padding: 11px 8px 10px 8px;
                    box-sizing: padding-box;
                    color: #fff;
                    overflow: hidden!important;

                }

                /* Не определено */

                .PROP_rupost_spy_N{
                    background: #5bc0de;
                }

                /* прием # 5bc0de */

                .PROP_rupost_spy_PE,
                .PROP_rupost_spy_PP{
                    background-color: #0066CC;
                }

                /* Вручение */

                .PROP_rupost_spy_BA,
                .PROP_rupost_spy_BO,
                .PROP_rupost_spy_BP,
                .PROP_rupost_spy_BOP,
                .PROP_rupost_spy_BAK{
                    background: #003300;
                }

                /* Возврат */

                .PROP_rupost_spy_BI,
                .PROP_rupost_spy_BZ,
                .PROP_rupost_spy_BBO,
                .PROP_rupost_spy_BOA,
                .PROP_rupost_spy_BCA,
                .PROP_rupost_spy_BNA,
                .PROP_rupost_spy_BOT,
                .PROP_rupost_spy_BSN,
                .PROP_rupost_spy_BIO,
                .PROP_rupost_spy_BHA{
                    background-color: #FFCC00;
                }

                /* Досылка почты */

                .PROP_rupost_spy_DPZ,
                .PROP_rupost_spy_DBA,
                .PROP_rupost_spy_DPZA{
                    background-color: #0066CC;
                }

                /* Невручение */

                .PROP_rupost_spy_NU,
                .PROP_rupost_spy_NI,
                .PROP_rupost_spy_NZ,
                .PROP_rupost_spy_NRT,
                .PROP_rupost_spy_NO{
                    background: #CC0000;
                }

                /* Хранение */

                .PROP_rupost_spy_XD,
                .PROP_rupost_spy_XA,
                .PROP_rupost_spy_XU,
                .PROP_rupost_spy_XC,
                .PROP_rupost_spy_XVT{
                    background-color: #FFCC00;
                }

                /* Временное хранение */

                .PROP_rupost_spy_VN,
                .PROP_rupost_spy_VNB,
                .PROP_rupost_spy_VXC{
                    background-color: #FFCC00;
                }

                /* Обработка */

                .PROP_rupost_spy_OC,
                .PROP_rupost_spy_OPP,
                .PROP_rupost_spy_OPM,
                .PROP_rupost_spy_OPC,
                .PROP_rupost_spy_OPPC,
                .PROP_rupost_spy_OMO,
                .PROP_rupost_spy_OPO,
                .PROP_rupost_spy_OPT,
                .PROP_rupost_spy_OPTM,
                .PROP_rupost_spy_OPPP,
                .PROP_rupost_spy_OIC,
                .PROP_rupost_spy_OPR,
                .PROP_rupost_spy_OIP,
                .PROP_rupost_spy_OPTRF,
                .PROP_rupost_spy_OPCV,
                .PROP_rupost_spy_OPK{
                    background-color: #0066CC;
                }

                /* Импорт международной почты */

                .PROP_rupost_spy_IMP{
                    background-color: #0066CC;
                }

                /* Экспорт международной почты */

                .PROP_rupost_spy_AMP{
                    background-color: #0066CC;
                }

                /* Принято на таможню */
                .PROP_rupost_spy_PNT{
                    background-color: #0066CC;
                }

                /* Неудачная попытка вручения */
                .PROP_rupost_spy_NPV,
                .PROP_rupost_spy_NPD,
                .PROP_rupost_spy_NPN,
                .PROP_rupost_spy_NPA,
                .PROP_rupost_spy_NPAV,
                .PROP_rupost_spy_NPOV,
                .PROP_rupost_spy_NPON,
                .PROP_rupost_spy_NPVI,
                .PROP_rupost_spy_NPAO,
                .PROP_rupost_spy_NPVA,
                .PROP_rupost_spy_NPTA,
                .PROP_rupost_spy_NPXA{
                    background: #CC0000;
                }

                /* Регистрация отправки */
                .PROP_rupost_spy_RO{
                    background-color: #0066CC;
                }

                /* Таможенное оформление */
                .PROP_rupost_spy_TOV,
                .PROP_rupost_spy_TOVT,
                .PROP_rupost_spy_TOOT,
                .PROP_rupost_spy_TOOV,
                .PROP_rupost_spy_TOTV,
                .PROP_rupost_spy_TUTP,
                .PROP_rupost_spy_PVX{
                    background-color: #0066CC;
                }

                /* Передача на временное хранение */
                .PROP_rupost_spy_PVX{
                    background-color: #FFCC00;
                }

                /* Уничтожение */
                .PROP_rupost_spy_UN{
                    background: #CC0000;
                }

                /* Передача вложения на баланс */
                .PROP_rupost_spy_PVB{
                    background-color: #FFCC00;
                }

                /* Регистрация утраты */
                .PROP_rupost_spy_RU{
                    background: #CC0000;
                }
            </style>
            ';

        $APPLICATION->AddHeadString($statusStyle);

        $list->arActions['mystat_order_twig'] = GetMessage("MYSTAT_ORDER_TWIG");
        $list->arActions['mystat_blank'] = GetMessage("MYSTAT_BLANK");
        $list->arActions['mystat_envelope'] = GetMessage("MYSTAT_ENVELOPE");

        $list->arActions['mystat_packed_yes'] = GetMessage("MYSTAT_PACKED_YES");
        $list->arActions['mystat_handed_courier_yes'] = GetMessage("MYSTAT_HANDED_COURIER_YES");
        $list->arActions['mystat_packed_no'] = GetMessage("MYSTAT_PACKED_NO");
        $list->arActions['mystat_handed_courier_no'] = GetMessage("MYSTAT_HANDED_COURIER_NO");

    }

}

public static function OnBeforePrologHandler(){

    global $APPLICATION,$DB;

    if(!array_key_exists('action', $_REQUEST)
        || !array_key_exists('ID', $_REQUEST)
        || !(in_array($_REQUEST['action'],
            array(
                'mystat_order_twig',
                'mystat_blank',
                'mystat_envelope',
                'mystat_print_document',
                'mystat_packed_yes',
                'mystat_handed_courier_yes',
                'mystat_packed_no',
                'mystat_handed_courier_no'))))
        return;

    $uri_params = array();

    switch($_REQUEST['action']){

        case 'mystat_packed_yes':

            $ids = $_REQUEST['ID'];
            $ids = !empty($ids) && !is_array($ids) ? array($ids) : $ids;

            $ids = array_map('intval',$ids);
            $ids = array_unique($ids);
            $ids = array_filter($ids);

            $dbCSaleOrderProps = \CSaleOrderProps::GetList(
                array("SORT" => "ASC"),
                array(
                    "CODE" => "PACKED",
                ),
                false,
                false,
                array()
            );


            if($dbCSaleOrderProps
                && $arOrderProps = $dbCSaleOrderProps->fetch()){

                foreach($ids as $id){

                    $dbCSaleOrderPropsValue = \CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $id, 'ORDER_PROPS_ID' => $arOrderProps['ID']));

                    if ($dbCSaleOrderPropsValue
                        && $arCSaleOrderPropsValue = $dbCSaleOrderPropsValue->fetch()) {

                        \CSaleOrderPropsValue::Update($arCSaleOrderPropsValue['ID'],
                            array(
                                'VALUE' => 'Y'
                            )
                        );


                    } else {

                        $order = \Bitrix\Sale\Order::loadByAccountNumber($id);
                        $propertyCollection = $order->getPropertyCollection();
                        $propertyValue = $propertyCollection->getItemByOrderPropertyId($arOrderProps['ID']);

                        if($propertyValue){
                            $propertyValue->setField('VALUE', "Y");
                            $propertyValue->save();
                        }


                    }

                    $arFields = array(
                        "DATE_UPDATE" => $DB->GetNowFunction(),
                    );

                    \CSaleOrder::Update($id, $arFields);


                }


            }

            break;

        case 'mystat_handed_courier_yes':

            $ids = $_REQUEST['ID'];
            $ids = !empty($ids) && !is_array($ids) ? array($ids) : $ids;

            $ids = array_map('intval',$ids);
            $ids = array_unique($ids);
            $ids = array_filter($ids);

            $dbCSaleOrderProps = \CSaleOrderProps::GetList(
                array("SORT" => "ASC"),
                array(
                    "CODE" => "HANDED_COURIER",
                ),
                false,
                false,
                array()
            );

            if($dbCSaleOrderProps
                && $arOrderProps = $dbCSaleOrderProps->fetch()){

                foreach($ids as $id){

                    $dbCSaleOrderPropsValue = \CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $id, 'ORDER_PROPS_ID' => $arOrderProps['ID']));

                    if ($dbCSaleOrderPropsValue
                        && $arCSaleOrderPropsValue = $dbCSaleOrderPropsValue->fetch()) {

                        \CSaleOrderPropsValue::Update($arCSaleOrderPropsValue['ID'],
                            array(
                                'VALUE' => 'Y'
                            )
                        );


                    } else {

                        $order = \Bitrix\Sale\Order::loadByAccountNumber($id);
                        $propertyCollection = $order->getPropertyCollection();
                        $propertyValue = $propertyCollection->getItemByOrderPropertyId($arOrderProps['ID']);

                        if($propertyValue){
                            $propertyValue->setField('VALUE', "Y");
                            $propertyValue->save();
                        }

                    }

                    $arFields = array(
                        "DATE_UPDATE" => $DB->GetNowFunction(),
                    );

                    \CSaleOrder::Update($id, $arFields);

                }


            }



            break;

        case 'mystat_packed_no':



            $ids = $_REQUEST['ID'];
            $ids = !empty($ids) && !is_array($ids) ? array($ids) : $ids;

            $ids = array_map('intval',$ids);
            $ids = array_unique($ids);
            $ids = array_filter($ids);


            $dbCSaleOrderProps = \CSaleOrderProps::GetList(
                array("SORT" => "ASC"),
                array(
                    "CODE" => "PACKED",
                ),
                false,
                false,
                array()
            );

            if($dbCSaleOrderProps
                && $arOrderProps = $dbCSaleOrderProps->fetch()){

                foreach($ids as $id){

                    $dbCSaleOrderPropsValue = \CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $id, 'ORDER_PROPS_ID' => $arOrderProps['ID']));

                    if ($dbCSaleOrderPropsValue
                        && $arCSaleOrderPropsValue = $dbCSaleOrderPropsValue->fetch()) {

                        \CSaleOrderPropsValue::Update($arCSaleOrderPropsValue['ID'],
                            array(
                                'VALUE' => 'N'
                            )
                        );


                    } else {

                        $order = \Bitrix\Sale\Order::loadByAccountNumber($id);
                        $propertyCollection = $order->getPropertyCollection();
                        $propertyValue = $propertyCollection->getItemByOrderPropertyId($arOrderProps['ID']);

                        if($propertyValue){
                            $propertyValue->setField('VALUE', "N");
                            $propertyValue->save();
                        }

                    }

                    $arFields = array(
                        "DATE_UPDATE" => $DB->GetNowFunction(),
                    );

                    \CSaleOrder::Update($id, $arFields);
                }

            }


            break;

        case 'mystat_handed_courier_no':

            $ids = $_REQUEST['ID'];
            $ids = !empty($ids) && !is_array($ids) ? array($ids) : $ids;

            $ids = array_map('intval',$ids);
            $ids = array_unique($ids);
            $ids = array_filter($ids);

            $dbCSaleOrderProps = \CSaleOrderProps::GetList(
                array("SORT" => "ASC"),
                array(
                    "CODE" => "HANDED_COURIER",
                ),
                false,
                false,
                array()
            );

            if($dbCSaleOrderProps
                && $arOrderProps = $dbCSaleOrderProps->fetch()){

                foreach($ids as $id){

                    $dbCSaleOrderPropsValue = \CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $id, 'ORDER_PROPS_ID' => $arOrderProps['ID']));

                    if ($dbCSaleOrderPropsValue
                        && $arCSaleOrderPropsValue = $dbCSaleOrderPropsValue->fetch()) {

                        \CSaleOrderPropsValue::Update($arCSaleOrderPropsValue['ID'],
                            array(
                                'VALUE' => 'N'
                            )
                        );


                    } else {

                        $order = \Bitrix\Sale\Order::loadByAccountNumber($id);
                        $propertyCollection = $order->getPropertyCollection();
                        $propertyValue = $propertyCollection->getItemByOrderPropertyId($arOrderProps['ID']);

                        if($propertyValue){
                            $propertyValue->setField('VALUE', "N");
                            $propertyValue->save();
                        }

                    }

                    $arFields = array(
                        "DATE_UPDATE" => $DB->GetNowFunction(),
                    );

                    \CSaleOrder::Update($id, $arFields);

                }


            }


            break;


        case 'mystat_order_twig':

            $uri_params['what'] = '/bitrix/admin/sale_print.php?PROPS_ENABLE=Y&doc=invoice&ORDER_ID=[ORDER_ID]&SHOW_ALL=Y';
            $uri_params['ID'] = $_REQUEST['ID'];
            $uri_params['action'] = 'mystat_print_document';
            $uri_params['count'] = 1;

            $uri_params = http_build_query($uri_params);

            $uri = '/bitrix/tools/mystat_print.php?'.$uri_params;
            echo '<script type="text/javascript">window.open(\''.$uri.'\', \'_blank\');</script>';


            break;
        case 'mystat_blank':

            $uri_params['what'] = '/bitrix/admin/sale_print.php?PROPS_ENABLE=Y&doc=f112ep-perevod&ORDER_ID=[ORDER_ID]&SHOW_ALL=Y';
            $uri_params['ID'] = $_REQUEST['ID'];
            $uri_params['action'] = 'mystat_print_document';
            $uri_params['count'] = 1;

            $uri_params = http_build_query($uri_params);

            $uri = '/bitrix/tools/mystat_print.php?'.$uri_params;
            echo '<script type="text/javascript">window.open(\''.$uri.'\', \'_blank\');</script>';

            break;
        case 'mystat_envelope':

            $uri_params['what'] = '/bitrix/admin/sale_print.php?PROPS_ENABLE=Y&doc=konvert&ORDER_ID=[ORDER_ID]&SHOW_ALL=Y';
            $uri_params['ID'] = $_REQUEST['ID'];
            $uri_params['action'] = 'mystat_print_document';
            $uri_params['count'] = 3;

            $uri_params = http_build_query($uri_params);

            $uri = '/bitrix/tools/mystat_print.php?'.$uri_params;
            echo '<script type="text/javascript">window.open(\''.$uri.'\', \'_blank\');</script>';

            break;

    }


}

public static function printAction(){

$what = isset($_REQUEST['what']) && !empty($_REQUEST['what']) ? trim($_REQUEST['what']) : '';
$what = urldecode($what);

$queries = array();
$uriQuery = parse_url($what,PHP_URL_QUERY);
mb_parse_str($uriQuery, $queries);

$iframeClass = ' '.isset($queries['doc']) && !empty($queries['doc']) ? trim($queries['doc']) : '';
$iframeClass.= ' inframe';

$count = isset($_REQUEST['count']) && !empty($_REQUEST['count']) ? abs((int)($_REQUEST['count'])) : 1;
$count = $count > 0 ? $count : 1;

$frameHeight = floor(297 / $count);

$ids = isset($_REQUEST['ID']) && !empty($_REQUEST['ID']) ? ($_REQUEST['ID']) : '';

$ids = !empty($ids) && !is_array($ids) ? array($ids) : $ids;

$ids = array_map('intval',$ids);
$ids = array_unique($ids);
$ids = array_filter($ids);

if(!empty($what) && !empty($ids)){
?><!DOCTYPE>
<html>
<head>
    <meta http-equiv=Content-Type content="text/html; charset=<?=LANG_CHARSET?>" />
    <title langs="ru">
        Печать счета
    </title>
    <style type="text/css">
        html, body, div, span, applet, object, iframe,
        h1, h2, h3, h4, h5, h6, p, blockquote, pre,
        a, abbr, acronym, address, big, cite, code,
        del, dfn, em, img, ins, kbd, q, s, samp,
        small, strike, strong, sub, sup, tt, var,
        b, u, i, center,
        dl, dt, dd, ol, ul, li,
        fieldset, form, label, legend,
        table, caption, tbody, tfoot, thead, tr, th, td,
        article, aside, canvas, details, embed,
        figure, figcaption, footer, header, hgroup,
        menu, nav, output, ruby, section, summary,
        time, mark, audio, video {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
        }
        /* HTML5 display-role reset for older browsers */
        article, aside, details, figcaption, figure,
        footer, header, hgroup, menu, nav, section {
            display: block;
        }
        body {
            line-height: 1;
        }
        ol, ul {
            list-style: none;
        }
        blockquote, q {
            quotes: none;
        }
        blockquote:before, blockquote:after,
        q:before, q:after {
            content: '';
            content: none;
        }

        .print-panel{
            background: #eee;
            border-bottom: 1px dashed #000;
            padding: 30px 0;
            text-align: center;
            margin-bottom: 15px;
        }

        .print-panel .btn-print {
            display: inline-block;
            font-size: 19px;
            padding: 3px 19px;
            width: 100px;
            background-color: #344193;
            color: #fff;
            border: 1px solid #00f;
            cursor:pointer;
        }

        .print-panel .btn-print:hover {
            background-color: #243183;
        }

        table, table td, table tr {
            border-collapse: collapse;
            border-spacing: 0;
            padding: 0;
            margin: 0;
        }
        iframe{
            float: none;
            clear: both;
            display: block;
            margin: 0 0 15px 0;
            padding: 0;
            box-sizing: content-box;
        }

        @media all
        {
            .nextpage {
                display:none;
            }
        }

        @page {
            size: A4;
            margin: 0;
            padding: 0;
        }

        @page :left {

            margin: 0;
            padding: 0;

            @bottom-right {
                content: normal;
                margin: 0;
            }

            @bottom-left {
                content: normal;
                margin: 0;
            }
        }

        @page :right {

            margin: 0;
            padding: 0;

            @bottom-right {
                content: normal;
                margin: 0;
            }

            @bottom-left {
                content: normal;
                margin: 0;
            }
        }


        @media print
        {

            h1, h2, h3, h4, h5 {
                page-break-after: avoid;
            }

            table, figure {
                page-break-inside: avoid;
            }

            .print-panel{
                display: none;
            }

            .nextpage {
                display:block;
                page-break-before:always;
            }

            html, body {
                width: 831px;
                height: 297mm;
                padding: 0;
                margin: 0;
            }

            iframe {
                height: <?php echo $frameHeight;?>mm!important;
                padding: 0;
                margin: 0;
                visibility: hidden;
            }

        }
    </style>
</head>
<body>
<div class="print-panel">
    <button onclick="window.print();" class="btn-print">
        <?php echo GetMessage('MYSTAT_PRINT');?>
    </button>
</div>
<? foreach($ids as $number => $ID): ?>
    <? $link = str_ireplace('[ORDER_ID]',$ID,$what); ?>
    <? if($number % $count == 0 && $number > 0): ?>
        <div class="nextpage"></div>
    <? endif; ?>
    <iframe src="<?=$link;?>" width="100%" frameborder="0" scrolling="no"></iframe>
<? endforeach; ?>
<script>
    //<!--
    function getDocHeight(doc) {
        doc = doc || document;
        var body = doc.body, html = doc.documentElement;
        var height = Math.max( body.scrollHeight, body.offsetHeight,
            html.clientHeight, html.scrollHeight, html.offsetHeight );


        return height;
    }

    function setIframeHeight(ifrm) {

        var doc = ifrm.contentDocument? ifrm.contentDocument:
            ifrm.contentWindow.document;

        var cssLink = document.createElement("link")
        cssLink.href = location.protocol + '//' + location.hostname + "/bitrix/modules/my.stat/assets/style.css";
        cssLink.rel = "stylesheet";
        cssLink.type = "text/css";
        doc.body.appendChild(cssLink);
        doc.body.className += ' <?php echo $iframeClass;?>';

        ifrm.style.height = "10px"; // reset to minimal height ...
        // IE opt. for bing/msn needs a bit added or scrollbar appears

        setTimeout(function(){

            ifrm.style.height = getDocHeight( doc ) + "px";
            ifrm.style.visibility = 'visible';

        },800);

    }

    var iframes = document.getElementsByTagName('iframe');

    for(var i = 0; i < iframes.length; i++){

        iframes[i].onload = function() { // Adjust the Id accordingly
            setIframeHeight(this);
        };

    };

    //-->
</script>
</body>
</html>
<?


}

}

}

class CSaleFormatProperties{

    static protected $orderProps = array();

    public static function getPaymentsList(){
        $paymentsArray = array();

        $dbPaySystem = \Bitrix\Sale\PaySystem\Manager::getList(array('filter' => array('ACTIVE' => 'Y'), 'order' => array('SORT' => 'ASC', 'PSA_NAME' => 'ASC')));
        while ($paySystem = $dbPaySystem->fetch()) {
            $paymentsArray[$paySystem["ID"]] = $paySystem["NAME"];
        }

        return $paymentsArray;

    }

    public static function getDeliveriesList(){
        $deliveriesAcitveArray = \Bitrix\Sale\Delivery\Services\Manager::getActiveList();

        $parents = array();
        $deliveriesArray = array();

        foreach($deliveriesAcitveArray as $ID => $deliveryAcitve){

            if(isset($deliveryAcitve['PARENT_ID'])
                && !empty($deliveryAcitve['PARENT_ID'])){

                $parents[] = $deliveryAcitve['PARENT_ID'];
                $deliveriesAcitveArray[$ID]['NAME'] = $deliveriesAcitveArray[$deliveryAcitve['PARENT_ID']]['NAME'].': '.$deliveryAcitve['NAME'];

            }

        }

        foreach($parents as $ID){
            unset($deliveriesAcitveArray[$ID]);
        }

        foreach($deliveriesAcitveArray as $deliveryAcitve){

            $deliveriesArray[$deliveryAcitve['ID']] = $deliveryAcitve['NAME'];
        }

        return $deliveriesArray;

    }

    public static function printPaymentSelect($selected = 0, $name = 'chained[payments][]', $multiple = ''){

        $paymentOptions = CSaleFormatProperties::getPaymentsList();
        $selected = !is_array($selected) ? array($selected) : $selected;

        $select = '<select name="'.$name.'"'.$multiple.'>';

        $select .= '<option value="">'.GetMessage('REFERENCES_PAYMENT_LABEL').'</option>';

        foreach($paymentOptions as $value => $optionText){

            $select .= '<option value="'.$value.'"'.(in_array($value, $selected)? ' selected="selected"' : '').'>'.$optionText.'</option>';

        }

        $select .= '</select>';

        return $select;

    }

    public static function printDeliverySelect($selected = 0, $name = 'chained[deliveries][]', $multiple = ''){


        $deliveryOptions = CSaleFormatProperties::getDeliveriesList();
        $selected = !is_array($selected) ? array($selected) : $selected;

        $select = '<select name="'.$name.'"'.$multiple.'>';
        $select .= '<option value="">'.GetMessage('REFERENCES_DELIVERY_LABEL').'</option>';

        foreach($deliveryOptions as $value => $optionText){

            $select .= '<option value="'.$value.'"'.(in_array($value, $selected) ? ' selected="selected"' : '').'>'.$optionText.'</option>';

        }

        $select .= '</select>';

        return $select;

    }

    public static function printDeliveryProviderCode($chained = array(), $number) {

        $propsSortedByGroup = array();
        $propsSortedByGroup = self::getOrderProperties();

        $poviderCodeKey = "143"; // apiship delivery provider code
        $codeProperty = array();
        
        foreach ($propsSortedByGroup as $arProperty)
        {
            if(isset($chained['prop'])
                && !empty($chained['prop'])){
                self::remapCurrentChainedProps($arProperty,$chained,$number);
            };

            $arProperties = self::getOrderPropFormatted($arProperty);

            if($arProperty['ID'] == $poviderCodeKey) {
                $codeProperty = $arProperties;
                break;
            }
        }

        $select = '<input type="text" maxlength="250" value="'.$codeProperty["VALUE"].'" name="chained[prop]['.$arProperties["FIELD_NAME"].'][]" class="form-control has_tooltip" />';

        if (mb_strlen(trim($codeProperty["DESCRIPTION"])) > 0) {
            $select .='<div class="bx_description">'.$codeProperty["DESCRIPTION"].'</div>';
        }

        return $select;
    }

    protected static function getOrderProperties(){

        if(empty(self::$orderProps)){

            $arProperties = array();
            $arResult = array();

            $siteId = \Bitrix\Main\Context::getCurrent()->getSite();

            $order = Order::create($siteId, \CSaleUser::GetAnonymousUserID());
            $order->setPersonTypeId(1);

            $arOrderProps = $order->getPropertyCollection()->getArray();

            $propsSortedByGroup = array();

            foreach ($arOrderProps['groups'] as $group)
            {
                foreach ($arOrderProps['properties'] as $prop)
                {

                    if ($prop['IS_LOCATION'] == 'Y')
                        continue;

                    if ($group['ID'] == $prop['PROPS_GROUP_ID'])
                    {
                        $prop['GROUP_NAME'] = $group['NAME'];
                        $propsSortedByGroup[] = $prop;
                    }
                }
            }

            self::$orderProps = $propsSortedByGroup;

        }

        return self::$orderProps;
    }

    public static function getCSaleProperties($chained = array(), $number){

        global $USER;
  
        $poviderCodeKey = "143"; // apiship delivery provider code
        $propsSortedByGroup = array();
        $propsSortedByGroup = self::getOrderProperties();
        
        foreach ($propsSortedByGroup as $arProperty)
        {
            if($arProperty['ID'] == $poviderCodeKey) {
                continue;
            }

            if(isset($chained['prop'])
                && !empty($chained['prop'])){
                self::remapCurrentChainedProps($arProperty,$chained,$number);
            };

            $arProperties = self::getOrderPropFormatted($arProperty);
            $flag = $arProperties["USER_PROPS"] == "Y" ? 'Y' : 'N';
            $arResult["ORDER_PROP"]["USER_PROPS_".$flag][$arProperties["ID"]] = $arProperties;

        }

        return $arResult;
    }

    protected static function remapCurrentChainedProps(&$arProperty,$chained,$number){

        foreach($chained['prop'] as $propID => $propValue){

            if($arProperty['ID'] == $propID){

                $arProperty['VALUE'] =  is_array($propValue[$number])
                    ? $propValue[$number]
                    : array($propValue[$number]);

            }

        }

    }

    public static function getOrderPropFormatted($arProperty, &$arDeleteFieldLocation = array())
    {
        static $propertyGroupID = 0;
        static $propertyUSER_PROPS = '';

        $arProperty['FIELD_NAME'] = ''.$arProperty['ID'];

        if ($arProperty['CODE'] != '')
        {
            $arProperty['FIELD_ID'] = 'ORDER_PROP_'.$arProperty['CODE'];
        }
        else
        {
            $arProperty['FIELD_ID'] = 'ORDER_PROP_'.$arProperty['ID'];
        }

        if (intval($arProperty['PROPS_GROUP_ID']) != $propertyGroupID || $propertyUSER_PROPS != $arProperty['USER_PROPS'])
        {
            $arProperty['SHOW_GROUP_NAME'] = 'Y';
        }

        $propertyGroupID = $arProperty['PROPS_GROUP_ID'];
        $propertyUSER_PROPS = $arProperty['USER_PROPS'];

        if ($arProperty['REQUIRED'] === 'Y' || $arProperty['IS_PROFILE_NAME'] === 'Y'
            || $arProperty['IS_LOCATION'] === 'Y' || $arProperty['IS_LOCATION4TAX'] === 'Y'
            || $arProperty['IS_PAYER'] === 'Y' || $arProperty['IS_ZIP'] === 'Y')
        {
            $arProperty['REQUIED'] = 'Y';
            $arProperty['REQUIED_FORMATED'] = 'Y';
        }

        switch ($arProperty['TYPE'])
        {
            case 'Y/N': self::formatYN($arProperty); break;
            case 'STRING': self::formatString($arProperty); break;
            case 'NUMBER': self::formatNumber($arProperty); break;
            case 'ENUM': self::formatEnum($arProperty); break;
            case 'LOCATION':
                break;
            case 'FILE': self::formatFile($arProperty); break;
            case 'DATE': self::formatDate($arProperty); break;
        }

        return $arProperty;
    }

    public static function formatYN(array &$arProperty)
    {
        $curVal = $arProperty['VALUE'];

        if (current($curVal) == "Y")
        {
            $arProperty["CHECKED"] = "Y";
            $arProperty["VALUE_FORMATED"] = Loc::getMessage("SOA_Y");
        }
        else
            $arProperty["VALUE_FORMATED"] = Loc::getMessage("SOA_N");

        $arProperty["SIZE1"] = (intval($arProperty["SIZE1"]) > 0) ? $arProperty["SIZE1"] : 30;

        $arProperty["VALUE"] = current($curVal);
        $arProperty["TYPE"] = 'CHECKBOX';
    }

    public static function formatString(array &$arProperty)
    {
        $curVal = $arProperty['VALUE'];

        if (!empty($arProperty["MULTILINE"]) && $arProperty["MULTILINE"] == 'Y')
        {
            $arProperty["TYPE"] = 'TEXTAREA';
            $arProperty["SIZE2"] = (intval($arProperty["ROWS"]) > 0) ? $arProperty["ROWS"] : 4;
            $arProperty["SIZE1"] = (intval($arProperty["COLS"]) > 0) ? $arProperty["COLS"] : 40;
        }
        else
            $arProperty["TYPE"] = 'TEXT';

        $arProperty["SOURCE"] = current($curVal) == $arProperty['DEFAULT_VALUE'] ? 'DEFAULT' : 'FORM';
        $arProperty["VALUE"] = current($curVal);
        $arProperty["VALUE_FORMATED"] = $arProperty["VALUE"];
    }

    public static function formatNumber(array &$arProperty)
    {
        $curVal = $arProperty['VALUE'];
        $arProperty["TYPE"] = 'TEXT';
        $arProperty["VALUE"] = current($curVal);
        $arProperty["VALUE_FORMATED"] = $arProperty["VALUE"];
    }

    public static function formatEnum(array &$arProperty)
    {
        $curVal = $arProperty['VALUE'];

        if ($arProperty["MULTIELEMENT"] == 'Y')
        {
            if ($arProperty["MULTIPLE"] == 'Y')
            {
                $setValue = array();
                $arProperty["FIELD_NAME"] = "".$arProperty["ID"].'[]';
                $arProperty["SIZE1"] = (intval($arProperty["SIZE1"]) > 0) ? $arProperty["SIZE1"] : 5;

                $i = 0;
                foreach ($arProperty["OPTIONS"] as $val => $name)
                {
                    $arVariants = array(
                        'VALUE' => $val,
                        'NAME' => $name
                    );
                    if ((is_array($curVal) && in_array($arVariants["VALUE"], $curVal)))
                    {
                        $arVariants["SELECTED"] = "Y";
                        if ($i > 0)
                            $arProperty["VALUE_FORMATED"] .= ", ";
                        $arProperty["VALUE_FORMATED"] .= $arVariants["NAME"];
                        $setValue[] = $arVariants["VALUE"];
                        $i++;
                    }
                    $arProperty["VARIANTS"][] = $arVariants;
                }

                $arProperty["TYPE"] = 'MULTISELECT';
            }
            else
            {
                foreach ($arProperty['OPTIONS'] as $val => $name)
                {
                    $arVariants = array(
                        'VALUE' => $val,
                        'NAME' => $name
                    );
                    if ($arVariants["VALUE"] == current($curVal))
                    {
                        $arVariants["CHECKED"] = "Y";
                        $arProperty["VALUE_FORMATED"] = $arVariants["NAME"];
                    }

                    $arProperty["VARIANTS"][] = $arVariants;
                }
                $arProperty["TYPE"] = 'RADIO';
            }
        }
        else
        {
            if ($arProperty["MULTIPLE"] == 'Y')
            {
                $setValue = array();
                $arProperty["FIELD_NAME"] = "".$arProperty["ID"].'[]';
                $arProperty["SIZE1"] = ((intval($arProperty["SIZE1"]) > 0) ? $arProperty["SIZE1"] : 5);

                $i = 0;
                foreach ($arProperty["OPTIONS"] as $val => $name)
                {
                    $arVariants = array(
                        'VALUE' => $val,
                        'NAME' => $name
                    );
                    if (is_array($curVal) && in_array($arVariants["VALUE"], $curVal))
                    {
                        $arVariants["SELECTED"] = "Y";
                        if ($i > 0)
                            $arProperty["VALUE_FORMATED"] .= ", ";
                        $arProperty["VALUE_FORMATED"] .= $arVariants["NAME"];
                        $setValue[] = $arVariants["VALUE"];
                        $i++;
                    }
                    $arProperty["VARIANTS"][] = $arVariants;
                }

                $arProperty["TYPE"] = 'MULTISELECT';
            }
            else
            {
                $arProperty["SIZE1"] = ((intval($arProperty["SIZE1"]) > 0) ? $arProperty["SIZE1"] : 1);
                $flagDefault = "N";
                $nameProperty = "";
                foreach ($arProperty["OPTIONS"] as $val => $name)
                {
                    $arVariants = array(
                        'VALUE' => $val,
                        'NAME' => $name
                    );
                    if ($flagDefault == "N" && $nameProperty == "")
                    {
                        $nameProperty = $arVariants["NAME"];
                    }
                    if ($arVariants["VALUE"] == current($curVal))
                    {
                        $arVariants["SELECTED"] = "Y";
                        $arProperty["VALUE_FORMATED"] = $arVariants["NAME"];
                        $flagDefault = "Y";
                    }
                    $arProperty["VARIANTS"][] = $arVariants;
                }
                if ($flagDefault == "N")
                {
                    $arProperty["VARIANTS"][0]["SELECTED"]= "Y";
                    $arProperty["VARIANTS"][0]["VALUE_FORMATED"] = $nameProperty;
                }
                $arProperty["TYPE"] = 'SELECT';
            }
        }
    }

    public static function formatFile(array &$arProperty)
    {
        $curVal = $arProperty['VALUE'];

        $arProperty["SIZE1"] = intval($arProperty["SIZE1"]);
        if ($arProperty['MULTIPLE'] == 'Y')
        {
            $arr = array();
            $curVal = isset($curVal) ? $curVal : $arProperty["DEFAULT_VALUE"];
            foreach ($curVal as $file)
            {
                $arr[] = $file['ID'];
            }
            $arProperty["VALUE"] = serialize($arr);
        }
        else
        {
            $arFile = isset($curVal) && is_array($curVal) ? current($curVal) : $arProperty["DEFAULT_VALUE"];
            if (is_array($arFile))
                $arProperty["VALUE"] = $arFile['ID'];
        }
    }

    public static function formatDate(array &$arProperty)
    {
        $arProperty["VALUE"] = current($arProperty['VALUE']);
        $arProperty["VALUE_FORMATED"] = $arProperty["VALUE"];
    }

    public static function properties_usort($a, $b){
        if ($a['SORT'] == $b['SORT']) {
            return 0;
        }
        return ($a['SORT'] < $b['SORT']) ? -1 : 1;
    }

    public static function showFilePropertyField($name, $property_fields, $values, $max_file_size_show=50000)
    {
        $res = "";

        if (!is_array($values) || empty($values))
            $values = array(
                "n0" => 0,
            );

        if ($property_fields["MULTIPLE"] == "N")
        {
            $res = "<label><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" class=\"has_tooltip\"></label>";
        }
        else
        {
            $res = '
            <script type="text/javascript">
                function addControl(item)
                {
                    var current_name = item.id.mb_split("[")[0],
                        current_id = item.id.mb_split("[")[1].replace("[", "").replace("]", ""),
                        next_id = parseInt(current_id) + 1;

                    var newInput = document.createElement("input");
                    newInput.type = "file";
                    newInput.name = current_name + "[" + next_id + "]";
                    newInput.id = current_name + "[" + next_id + "]";
                    newInput.onchange = function() { addControl(this); };
                    var br = document.createElement("br");
                    var br2 = document.createElement("br");

                    BX(item.id).parentNode.appendChild(br);
                    BX(item.id).parentNode.appendChild(br2);
                    BX(item.id).parentNode.appendChild(newInput);
                }
            </script>
            ';

            $res .= "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" class=\"has_tooltip\" /></label>";
            $res .= "<br/><br/>";
            $res .= "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[1]\" onChange=\"javascript:addControl(this);\"></label>";
        }

        return $res;
    }

    public static function PrintPropsForm($arSource = array())
    {

        if (!empty($arSource))
        {

            // usort($arSource, array("CSaleFormatProperties", "properties_usort"));

            usort($arSource, [CSaleFormatProperties::class, "properties_usort"]);
            foreach ($arSource as $arProperties){


                list($usec, $sec) = explode(" ", microtime());
                $uniqueID =  preg_replace('~[\D]+~','',((float)$usec + (float)$sec));
                $uniqueID = trim($uniqueID);

                if (!($arProperties["TYPE"] == "CHECKBOX")){

                    ?>
                    <div class="order-properties <?=$arProperties["FIELD_NAME"]?> <?=$arProperties["CODE"]?>">
                        <div class="<?=$arProperties["FIELD_NAME"]?> <?=$arProperties["CODE"]?>">
                            <div class="cart-fields" data-property-id-row="<?=intval($arProperties["ID"])?>">
                                <?
                                if ($arProperties["TYPE"] == "CHECKBOX" && false)
                                {


                                    ?>
                                    <label class="property-title">
                                        <?=$arProperties["NAME"]?> Y
                                    </label>
                                    <div class="property-value">
                                        <input type="checkbox" name="chained[prop][<?=$arProperties["FIELD_NAME"]?>][]" value="Y"<?if ($arProperties["CHECKED"]=="Y"){ echo " checked"; };?> class="has_tooltip" />
                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>
                                    <label class="property-title">
                                        <?=$arProperties["NAME"]?> N
                                    </label>
                                    <div class="property-value">
                                        <input type="checkbox" name="chained[prop][<?=$arProperties["FIELD_NAME"]?>][]" value="N"<?if ($arProperties["VALUE"]=="N"){ echo " checked"; };?> class="has_tooltip" />
                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>
                                    <?
                                }
                                elseif ($arProperties["TYPE"] == "TEXT")
                                {


                                    ?>
                                    <label class="property-title">
                                        <?=$arProperties["NAME"]?>
                                    </label>
                                    <div class="property-value">
                                        <input type="text" maxlength="250" value="<?=$arProperties["VALUE"]?>" name="chained[prop][<?=$arProperties["FIELD_NAME"]?>][]" class="form-control has_tooltip" />

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                }
                                elseif ($arProperties["TYPE"] == "SELECT")
                                {
                                    ?>
                                    <label class="property-title">
                                        <?=$arProperties["NAME"]?>
                                    </label>
                                    <div class="property-value">
                                        <select name="chained[prop][<?=$arProperties["FIELD_NAME"]?>][]" size="<?=$arProperties["SIZE1"]?>" class="selectpicker form-control has_tooltip">
                                            <option value=""><?=GetMessage('REFERENCE_NONE');?></option>
                                            <?
                                            foreach($arProperties["VARIANTS"] as $arVariants):
                                                ?>
                                                <option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y"){ echo " selected"; };?>><?=$arVariants["NAME"]?></option>
                                            <?
                                            endforeach;
                                            ?>
                                        </select>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                }
                                elseif ($arProperties["TYPE"] == "MULTISELECT")
                                {
                                    ?>
                                    <label class="property-title">
                                        <?=$arProperties["NAME"]?>
                                    </label>
                                    <div class="property-value">
                                        <select multiple="multiple" name="chained[prop][<?=$arProperties["FIELD_NAME"]?>][]" size="<?=$arProperties["SIZE1"]?>" class="selectpicker form-control has_tooltip">
                                            <option value=""><?=GetMessage('REFERENCE_NONE');?></option>
                                            <?
                                            foreach($arProperties["VARIANTS"] as $arVariants):
                                                ?>
                                                <option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y"){ echo " selected"; };?>><?=$arVariants["NAME"]?></option>
                                            <?
                                            endforeach;
                                            ?>
                                        </select>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                }
                                elseif ($arProperties["TYPE"] == "TEXTAREA")
                                {
                                    $rows = ($arProperties["SIZE2"] > 10) ? 4 : $arProperties["SIZE2"];

                                    ?>
                                    <label class="property-title">
                                        <?=$arProperties["NAME"]?>
                                    </label>
                                    <div class="property-value">
                                        <textarea class="form-control has_tooltip" name="chained[prop][<?=$arProperties["FIELD_NAME"]?>][]"><?=$arProperties["VALUE"]?></textarea>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description clearfix clear">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                }
                                elseif ($arProperties["TYPE"] == "RADIO")
                                {
                                    ?>
                                    <label class="property-title">
                                        <?=$arProperties["NAME"]?>
                                    </label>
                                    <div class="property-value">
                                        <?
                                        if (is_array($arProperties["VARIANTS"]))
                                        {
                                            foreach($arProperties["VARIANTS"] as $arVariants):
                                                ?>
                                                <label>
                                                    <input
                                                            type="radio"
                                                            name="chained[prop][<?=$arProperties["FIELD_NAME"]?>][]"
                                                            value="<?=$arVariants["VALUE"]?>" <?if($arVariants["CHECKED"] == "Y"){ echo " checked"; };?>
                                                            class="has_tooltip" />

                                                    <?=$arVariants["NAME"]?>
                                                </label></br>
                                            <?
                                            endforeach;
                                        }
                                        ?>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                }
                                elseif ($arProperties["TYPE"] == "FILE")
                                {

                                    ?>
                                    <label class="property-title">
                                        <?=$arProperties["NAME"]?>
                                    </label>
                                    <div class="property-value">
                                        <?=selft::showFilePropertyField("ORDER_PROP_".$arProperties["ID"], $arProperties, $arProperties["VALUE"], $arProperties["SIZE1"])?>
                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>


                                    <?
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <?

                }

            }
            ?>

            <?
        }



    }

    public static function printPersonType($selected = false, $name = 'person_type[]'){

        $returnHTML = '';

        $siteId = \Bitrix\Main\Context::getCurrent()->getSite();

        if(empty($siteId)) {

            $rsSites = \CSite::GetList(
                $by = "sort",
                $order = "desc",
                Array(
                    'DEFAULT' => 'Y',
                    'ACTIVE' => 'Y')
            );

            while ($arSite = $rsSites->Fetch()) {
                $siteId = $arSite['ID'];
            }

        }

        if($siteId) {

            $personTypes = \Bitrix\Sale\PersonType::load($siteId);

            $bFirst = true;

            foreach ($personTypes as $type) {

                $returnHTML .= '<option value="'.$type["ID"].'"'.((!empty($selected) && $selected == $type["ID"]) ? ' selected="selected"' : '').'>'.$type["NAME"].'</option>';
                $bFirst = false;

            }

            if(!empty($returnHTML)){
                $returnHTML = '<select name="'.$name.'"><option value="">'.GetMessage('REFERENCES_TAB_SAO_PERSONTYPEID_CHOOSE').'</option>'.$returnHTML.'</select>';
            }

        }

        return $returnHTML;

    }

}

?>