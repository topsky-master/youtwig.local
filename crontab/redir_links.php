#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/redir_links.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";
$_SERVER["SERVER_NAME"] = "youtwig.ru";


$bSkipMan = false;

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if (isset($argv) && !empty($argv)) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

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

    public static function sendMail() {
        CModule::IncludeModule("main");

        $event_name = 'MAIL_REDIRLINKS_END';

        $arrSites = array();
        $objSites = CSite::GetList(($by = "sort"), ($order = "asc"));

        while ($arrSite = $objSites->Fetch()) {
            $arrSites[] = $arrSite["ID"];
        };

        CEvent::SendImmediate($event_name, $arrSites, []);

    }

}

$skip = isset($_REQUEST['skip'])
&& !empty($_REQUEST['skip'])
    ? (int)trim($_REQUEST['skip'])
    : 0;

if (isset($argv) && !empty($argv)) {

    $arNavParams = false;

    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/redir_links.txt','0');
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/404_links_log.csv','');
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/cycle_links_log.csv','');
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/links_log.csv','');

} else {

    $maxCount = 30;

    if(empty($skip)){
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/redir_links.txt','0');
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/404_links_log.csv','');
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/cycle_links_log.csv','');
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/links_log.csv','');
		$skip = 1;
    }

    $arNavParams = array(
        'nTopCount' => false,
        'nPageSize' => $maxCount,
        'iNumPage' => $skip,
        'checkOutOfRange' => true
    );

}

$currentCount = 0;

$rsData = CBXShortUri::GetList(
    Array(
        'ID' => 'DESC'
    ),
    [],
    $arNavParams
);

$count = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/redir_links.txt');

while($arData = $rsData->GetNext()){

    ++$currentCount;

    $url = (CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/'.trim($arData['SHORT_URI'],'/').'/';

    $http_code = 0;
    $http_code = ShortRedirectsCheck::check404Code($url);

	file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/links_log.csv',$url.';'.$http_code."\n", FILE_APPEND);

    if($http_code == 404){
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/404_links_log.csv',var_export($arData,true)."\n", FILE_APPEND);
    }

    $url = (CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/'.trim($arData['URI'],'/').'/';

    $http_code = 0;
    $http_code = ShortRedirectsCheck::check404Code($url);
	
	file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/links_log.csv',$url.';'.$http_code."\n", FILE_APPEND);
	
    if($http_code == 404){
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/404_links_log.csv',var_export($arData,true)."\n", FILE_APPEND);
    }

    $http_code = 0;
    $http_code = ShortRedirectsCheck::checkCycleRedirect($url);
	
	file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/links_log.csv',$url.';'.$http_code."\n", FILE_APPEND);
	
    if($http_code == 301){
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/cycle_links_log.csv',var_export($arData,true)."\n", FILE_APPEND);
    }

}

$bEnded = false;

if (isset($argv) && !empty($argv)) {
    $bEnded = true;
} else {
    if(!empty($currentCount)){
        ++$skip;
        $count += $currentCount;
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/redir_links.txt',$count);
        echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/redir_links.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
        die();
    } else {
        $bEnded = true;
    }

}

if ($bEnded) {
    ShortRedirectsCheck::sendMail();
    echo $count;
    die();
}
