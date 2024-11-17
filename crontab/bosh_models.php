<?php

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$skip = isset($_REQUEST['skip'])
&& !empty($_REQUEST['skip'])
    ? (int)trim($_REQUEST['skip'])
    : 0;

$maxCount = 750;
$modelNames = file(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_models_log.csv');

if(empty($skip)){
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_models.txt','0');
    $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_models_log.csv','w+');
    $skip = 1;
} else {
    $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_models_log.csv','a+');
}

$arModelSelect = Array(
    "ID",
    "PROPERTY_model_new_link"
);

$arModelFilter = Array(
    "IBLOCK_ID" => 17,
    "PROPERTY_manufacturer_VALUE" => "Indesit",
    "ACTIVE" => "Y"
);


$arNavParams = array(
    'nTopCount' => false,
    'nPageSize' => $maxCount,
    'iNumPage' => $skip,
    'checkOutOfRange' => true
);

$currentCount = 0;

$resModel = CIBlockElement::GetList(
    ($order = Array(
        'PROPERTY_manufacturer' => 'asc',
        'created' => 'desc'
    )),
    $arModelFilter,
    false,
    $arNavParams,
    $arModelSelect
);

$count = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/disabled_models.txt');
if($resModel){
    while($arModel = $resModel->GetNext()){

        $modelName = '';

        if(isset($arModel['PROPERTY_MODEL_NEW_LINK_VALUE'])
            && !empty($arModel['PROPERTY_MODEL_NEW_LINK_VALUE'])){

            $dbNModel = CIBlockElement::GetById($arModel['PROPERTY_MODEL_NEW_LINK_VALUE']);

            if($dbNModel){
                $arNModel = $dbNModel->GetNext();
                $modelName = trim($arNModel['NAME']);
            }
        }

        if(!empty($modelName) && !in_array($modelName,$modelNames)){
            $modelNames[] = $modelName;
            fwrite($fp,$modelName."\n");
        }

        ++$currentCount;
    }

}

fclose($fp);

if(!empty($currentCount)){
    ++$skip;
    $count += $currentCount;
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_models.txt',$count);
    echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/indesit_models.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
    die();
} else {
    echo $count.'-';
    echo 'done';
    die();
}



