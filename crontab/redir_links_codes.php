<?php

if(!isset($_REQUEST['intestwetrust'])) die();

//https://youtwig.ru/local/crontab/redir_links_codes.php?intestwetrust=1

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
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/redir_links.txt','0');
	file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/redir_links.csv','Код;Откуда;Код;Куда;Админка'."\n",FILE_APPEND);
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

    $url = (CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/'.trim($arData['SHORT_URI'],'/').'/';
	$rRule = "".(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME']."/bitrix/admin/short_uri_edit.php?ID=".$arData['ID'].'&lang=ru';
					
    $http_code = 0;
    $http_code = ShortRedirectsCheck::check404Code($url);
	
	$url1 = (CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/'.trim($arData['URI'],'/').'/';
	$http_code1 = 0;
    $http_code1 = ShortRedirectsCheck::check404Code($url1);
	
	file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/redir_links.csv',$http_code.';'.$url.';'.$http_code1.";".$url1.";".$rRule."\n",FILE_APPEND);

}

if(!empty($currentCount)){
    ++$skip;
    $count += $currentCount;
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/redir_links.txt',$count);
	echo '<html><header><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/redir_links_codes.php?skip='.$skip.'&intestwetrust=1&time='.time().'&PageSpeed=off" /></header><body><h1>'.time().'</h1></body></html>';
    die();
} else {
    echo $count.'-';
    echo 'done';
    die();
}



