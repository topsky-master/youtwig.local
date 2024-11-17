<?php

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$skip = isset($_REQUEST['skip'])
    && !empty($_REQUEST['skip'])
    ? (int)trim($_REQUEST['skip'])
    : 0;

$maxCount = 300;

if(empty($skip)){
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_links.txt','0');
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_links_log.txt','');
    $skip = 1;
}

$arNavParams = array(
    'nTopCount' => false,
    'nPageSize' => $maxCount,
    'iNumPage' => $skip,
    'checkOutOfRange' => true
);

$currentCount = 0;

$rsData = CBXShortUri::GetList(
    Array(
        'ID' => 'DESC'
    ),
    $arFilter,
    $arNavParams
);

$count = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_links.txt');

while($arData = $rsData->GetNext()){

    ++$currentCount;

    $uri = trim($arData['SHORT_URI'],'/');
    $short_uri = trim($arData['URI'],'/');

    $rsRData = CBXShortUri::GetList(
        Array(),
        Array(
            'SHORT_URI' => $short_uri,
            'URI' => $uri
        )
    );

    while($arrData = $rsRData->GetNext()){
        ++$count;

        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_links_log.txt','Было: '.$arData['URI'].'-'.$arData['SHORT_URI']."\n",FILE_APPEND);
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_links_log.txt','Дубль: '.$arrData['URI'].'-'.$arrData['SHORT_URI']."\n",FILE_APPEND);

    }

}

if(!empty($currentCount)){
    ++$skip;
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_links.txt',$count);
    echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/remove_links.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
    die();
} else {
    echo $count.'-';
    echo 'done';
    die();
}

