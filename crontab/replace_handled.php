<?php

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

CModule::IncludeModule("sale");

$lastId = isset($_REQUEST['skip']) ? (int)trim($_REQUEST['skip']) : 0;

if(empty($lastId)){
    $arFilter = array();
} else {
    $arFilter = array('<ID' => $lastId);
}


$rsOrders = CSaleOrder::GetList(array('ID' => 'DESC'), $arFilter);

$count = 0;
$max = 200;

if($rsOrders){

    while($ar_sales = $rsOrders->Fetch())
    {

        ++$count;
        $orderId = $ar_sales['ID'];
        $lastId = $orderId;
        $order = \Bitrix\Sale\Order::load($orderId);
        $propertyCollection = $order->getPropertyCollection();

        $packedProperty = $propertyCollection->getItemByOrderPropertyId(32);

        //if($packedProperty->getValue() == 'Y' || $packedProperty->getValue() == 'N') {
            $newValue = ($packedProperty->getValue() == 'Y' || $packedProperty->getValue() == 'Да') ? 'Да' : 'Нет';
            $packedProperty->setValue($newValue);
            $packedProperty->save();
        //}

        $handedProperty = $propertyCollection->getItemByOrderPropertyId(33);

        //if($handedProperty->getValue() == 'Y' || $handedProperty->getValue() == 'N'){
            $newValue = ($handedProperty->getValue() == 'Y' || $handedProperty->getValue() == 'Да') ? 'Да' : 'Нет';
            $handedProperty->setValue($newValue);
            $handedProperty->save();
        //}

        if($count == $max){
            echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/replace_handled.php?intestwetrust=1&skip='.$lastId.'&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
            die();
        }

    }

}

