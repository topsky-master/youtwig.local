<?php

$notInclude = false;

if(!isset($_SERVER["DOCUMENT_ROOT"]) || empty($_SERVER["DOCUMENT_ROOT"]))
    $_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";


$sReqFile = preg_replace('~\?.*?$~is','',$_SERVER['REQUEST_URI']);
$sReqFile = trim($sReqFile);

if(!preg_match('~\.[^/]+?~is',$sReqFile) && substr($sReqFile,-1) != '/') {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: https://".$_SERVER['SERVER_NAME'].$sReqFile.'/');
    exit();
}

$sReqFile = trim($sReqFile,'/');

if(!preg_match('~\.[^/]+?~is',$sReqFile) && mb_stripos($sReqFile,'~') === false) {
    $sReqFile = $sReqFile . '/';

    $slReqFile = strtolower($sReqFile);

    if($slReqFile != $sReqFile && !headers_sent()){
        header("Location: /".$slReqFile, true, 301);
        die();
    }
}

if(mb_stripos($sReqFile,'/filter/clear') !== false) {
    
	$sReqFile = $sReqFile . '/';
	$slReqFile = strtolower($sReqFile);
	
	if(mb_stripos($sReqFile,'/filter/clear/') !== false) {
		$slReqFile = str_ireplace('/filter/clear/','',$slReqFile);
	} else {
		$slReqFile = str_ireplace('/filter/clear','',$slReqFile);
	}
	
    if($slReqFile != $sReqFile && !headers_sent()){
        header("Location: /".$slReqFile, true, 301);
        die();
    }
}


$sReqFile = sprintf('%s', $sReqFile);

if(file_exists($_SERVER['DOCUMENT_ROOT'].$sReqFile)){

    $realpath = realpath($_SERVER['DOCUMENT_ROOT'].$sReqFile);

    if($realpath && stripos($realpath, $_SERVER['DOCUMENT_ROOT']) !== false) {

        include_once $realpath;

    }

    $notInclude = true;

} else {

    if(preg_match('~/pages-[0-9]+?/~is',$sReqFile)){

        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/local/site/prepend_functions.php')){
            include_once $_SERVER['DOCUMENT_ROOT'].'/local/site/prepend_functions.php';
        }

    }

    $sReqFile = preg_replace('~\?.*?$~is','',$_SERVER['REQUEST_URI']);

    if(!preg_match('~\.php~is',$sReqFile)){
        $sReqFile .= 'index.php';
    }

    if(file_exists($_SERVER['DOCUMENT_ROOT'].$sReqFile)
        && is_file($_SERVER['DOCUMENT_ROOT'].$sReqFile)
        && is_readable($_SERVER['DOCUMENT_ROOT'].$sReqFile)){

        $realpath = realpath($_SERVER['DOCUMENT_ROOT'].$sReqFile);

        if($realpath && stripos($realpath, $_SERVER['DOCUMENT_ROOT']) !== false){

            include_once $realpath;
            $notInclude = true;

        }

    }



}

if(!$notInclude){
    include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/urlrewrite.php');
}