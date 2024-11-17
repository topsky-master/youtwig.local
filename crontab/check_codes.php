<?php

//https://youtwig.ru/local/crontab/check_codes.php?intestwetrust=1

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

        echo $url."<br />";

        if($tuCurl && is_resource($tuCurl)) {

            $opts = array(
                CURLOPT_HTTPGET => 1,
                CURLOPT_CONNECTTIMEOUT => 12,
                CURLOPT_RETURNTRANSFER => 1
            );


            $opts[CURLOPT_HEADER] = 0;

            $opts[CURLOPT_URL] = $url;

            $opts[CURLOPT_FOLLOWLOCATION] = 1;
            $opts[CURLOPT_MAXREDIRS] = 5;

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
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/redir_links.txt','0');
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/404_links_log.txt','');
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/cycle_links_log.txt','');
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

$count = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/redir_links.txt');

while($arData = $rsData->GetNext()){

    ++$currentCount;

    $url = $arData['URI'];
    $url = mb_stripos($url,'http') !== 0 ? ((CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME']. '/'.trim($url,'/').'/')  : rtrim($url,'/').'/';

    $http_code = 0;
    $http_code = ShortRedirectsCheck::check404Code($url);

    if($http_code == 404){
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/404_links_log.txt',var_export($arData,true)."\n", FILE_APPEND);
    }

    $http_code = 0;
    $http_code = ShortRedirectsCheck::checkCycleRedirect($url);

    if($http_code == 301){
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/cycle_links_log.txt',var_export($arData,true)."\n", FILE_APPEND);
    }

}

if(!empty($currentCount)){
    ++$skip;
    $count += $currentCount;
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/redir_links.txt',$count);
    echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/check_codes.php?intestwetrust=1&skip='.$skip.'&PageSpeed=off&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
    die();
} else {
    echo $count.'-';
    echo 'done';
    die();
}



