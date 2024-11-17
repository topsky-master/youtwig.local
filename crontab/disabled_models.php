<?php

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class ShortRedirectsCheck{
    public static function check404Code($url){

        $http_code = '';

        $tuCurl = curl_init();

        if($tuCurl && is_resource($tuCurl)) {


            $opts = array(
                CURLOPT_HTTPGET => 1,
                CURLOPT_CONNECTTIMEOUT => 12,
                CURLOPT_RETURNTRANSFER => 1
            );


            $opts[CURLOPT_HEADER] = 0;

            $opts[CURLOPT_URL] = $url;

            $opts[CURLOPT_FOLLOWLOCATION] = 0;
            $opts[CURLOPT_AUTOREFERER] = 0;

            $opts[CURLOPT_COOKIESESSION] = 1;
            $opts[CURLOPT_VERBOSE] = 0;

            $opts[CURLOPT_SSL_VERIFYHOST] = 0;
            $opts[CURLOPT_SSL_VERIFYPEER] = 0;

            $opts[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)';
            $opts[CURLOPT_ENCODING] = "";


            foreach ($opts as $key => $value) {
                curl_setopt($tuCurl, $key, $value);
            }


            $tuData = curl_exec($tuCurl);
            $http_code = curl_getinfo($tuCurl, CURLINFO_HTTP_CODE);
            curl_close($tuCurl);

        }

        return $http_code;
    }

    public static function checkCycleRedirect($url){

        $http_code = '';

        $tuCurl = curl_init();

        if($tuCurl && is_resource($tuCurl)) {

            $opts = array(
                CURLOPT_HTTPGET => 1,
                CURLOPT_CONNECTTIMEOUT => 12,
                CURLOPT_RETURNTRANSFER => 1
            );


            $opts[CURLOPT_HEADER] = 0;

            $opts[CURLOPT_URL] = $url;

            $opts[CURLOPT_FOLLOWLOCATION] = 1;
            $opts[CURLOPT_MAXREDIRS] = 3;

            $opts[CURLOPT_AUTOREFERER] = 1;

            $opts[CURLOPT_COOKIESESSION] = 1;
            $opts[CURLOPT_VERBOSE] = 0;

            $opts[CURLOPT_SSL_VERIFYHOST] = 0;
            $opts[CURLOPT_SSL_VERIFYPEER] = 0;

            $opts[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)';
            $opts[CURLOPT_ENCODING] = "";


            foreach ($opts as $key => $value) {
                curl_setopt($tuCurl, $key, $value);
            }

            $tuData = curl_exec($tuCurl);

            $http_code = curl_getinfo($tuCurl, CURLINFO_HTTP_CODE);
            $storedReferer = curl_getinfo($tuCurl, CURLINFO_EFFECTIVE_URL);

            curl_close($tuCurl);

        }

        return $http_code;
    }


}

$skip = isset($_REQUEST['skip'])
&& !empty($_REQUEST['skip'])
    ? (int)trim($_REQUEST['skip'])
    : 0;

$maxCount = 30;

if(empty($skip)){
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/disabled_models.txt','0');
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/disabled_models_log.txt','');
    $skip = 1;
}

$arModelSelect = Array(
    "ID",
    "CODE",
    "NAME",
    "DETAIL_PAGE_URL"
);

$arModelFilter = Array(
    "IBLOCK_ID" => 17,
    "ACTIVE" => "N"
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

        $modelSearchName = trim(preg_replace('~\s*?\(.+\)\s*?$~isu','',$arModel['NAME']));

        $short_uri = trim($arModel["DETAIL_PAGE_URL"],'/');

        $rsRData = CBXShortUri::GetList(
            Array(),
            Array(
                'SHORT_URI' => $short_uri,
            )
        );

        $hasRedirect = false;
        $arrData = array();

        if($rsRData){
            while($arrData = $rsRData->GetNext()){

                $hasRedirect = true;
                break;

            }
        }

        if(!$hasRedirect){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/disabled_models_log.txt',$arModel['ID'].' - '.$arModel['NAME']."\n",FILE_APPEND);

        }

    }

    ++$currentCount;

}

if(!empty($currentCount)){
    ++$skip;
    $count += $currentCount;
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/disabled_models.txt',$count);
    echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/disabled_models.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
    die();
} else {
    echo $count.'-';
    echo 'done';
    die();
}



