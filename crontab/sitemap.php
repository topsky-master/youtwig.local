#!/usr/bin/php -q
<?php

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";
//$_SERVER["DOCUMENT_ROOT"] = "/var/www/sites/data/www/dev.youtwig.ru/";

define('WORK_DIRECTORY',$_SERVER["DOCUMENT_ROOT"].'parser_models/');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');
$_SERVER['SERVER_NAME'] = 'youtwig.ru';
define('P_IBLOCK_ID',11);

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/components/bitrix/catalog/catalog/bitrix/catalog.element/.default/lang/'.LANGUAGE_ID.'/template.php');

class impelSitemap{

    private static $properties = array();
    private static $countStrings = 12000;
    private static $countQueries = 49900;
    
    private static $pCodes = array(
        "TYPEPRODUCT",
        "MANUFACTURER"
    );

    public static function checkModels(){

        global $USER;

        $modelLastPropId = static::checkFamiliarModels();

        //static::getRedirect($modelLastPropId);

    }

    private static function setSections(){

        $sections = array();

        $sectDb = CIBlockSection::GetList(
            $arOrder = Array("SORT"=>"ASC"),
            $arFilter = Array("IBLOCK_ID" => P_IBLOCK_ID),
            false,
            $arSelect = Array("SECTION_PAGE_URL","NAME","ID")
        );

        if($sectDb) {
            while($sectAr = $sectDb->GetNext()) {
                $sections[$sectAr['ID']] = $sectAr['SECTION_PAGE_URL'];
            }
        }

        return $sections;

    }


    private static function setProperties(){

        $properties = array();

        $rP = CIBlockProperty::GetList(
            Array(),
            Array(
                "ACTIVE" => "Y",
                "IBLOCK_ID" => P_IBLOCK_ID
            )
        );


        if($rP) {
            while ($aRp = $rP->GetNext()) {
                if(in_array($aRp["CODE"],static::$pCodes)) {
                    $properties[$aRp["CODE"]] = array(
                        ($aRp["MULTIPLE"] == 'Y' ? true : false)
                    );
                }
            }
        }

        static::$properties = $properties;

    }

    private static function getPropValue($sCode,$iEltId){

        $apFilter = Array("CODE" => $sCode);
        $apValues = false;

        $rDB = CIBlockElement::GetProperty(
            P_IBLOCK_ID,
            $iEltId,
            array(),
            $apFilter);

        if ($rDB) {

            $apValues = array();
            $count = 0;

            while ($apFields = $rDB->GetNext()) {



                if (isset($apFields['VALUE_XML_ID'])
                    && !empty($apFields['VALUE_XML_ID'])
                ) {

                    ++$count;
                    $apValues[$apFields['VALUE_XML_ID']] = $apFields['VALUE_XML_ID'];

                }

            }


        }

        //print_r($apValues);

        return !empty($apValues)
            ? $apValues
            : false;

    }

    private static function getElementValues() {

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_last.txt'));

        $aSelect = Array(
            "ID",
            "IBLOCK_SECTION_ID"
        );

        $sitemapFilter = Array(
            "IBLOCK_ID" => P_IBLOCK_ID,
            "ACTIVE" => "Y",

        );

        $iLastProdId = 0;

        if($skip > 0){


        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_last.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_get.php','<?php $aSitemap = array(); ?>');
        }

        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_get.php')){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_get.php','<?php $aSitemap = array(); ?>');
        }

        require_once dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_get.php';

        $skip = empty($skip) ? 1 : $skip;
		
		$aNavParams = array(
			'nTopCount' => false,
			'nPageSize' => static::$countStrings,
			'iNumPage' => $skip,
			'checkOutOfRange' => true
		);
			
		$rSitemap = CIBlockElement::GetList(
			($aOrder = Array('ID' => 'DESC')),
			$sitemapFilter,
			false,
			$aNavParams,
			$aSelect
		);
			
		
        if($rSitemap){

            while($dSitemap = $rSitemap->GetNext()){
				
		        $iLastProdId = $dSitemap['ID'];

                $props = array();
                $typeProduct = '';
				
				foreach(static::$pCodes as $sCode) {

                    if(!isset($aSitemap[$dSitemap["IBLOCK_SECTION_ID"]])){

                        $aSitemap[$dSitemap["IBLOCK_SECTION_ID"]] = array();

                    }

                    if($sCode == "TYPEPRODUCT"){

                        if(!isset($aSitemap[$dSitemap["IBLOCK_SECTION_ID"]])){

                            $aSitemap[$dSitemap["IBLOCK_SECTION_ID"]] = array();

                        }

                        $props = static::getPropValue($sCode,$iLastProdId);
						
						if($props !== false) {

                            foreach($props as $prop){
								
								$aSitemap[$dSitemap["IBLOCK_SECTION_ID"]][$prop] = array();
                                $typeProduct = $prop;
                            }

                        }

                    } else {

                        $props = static::getPropValue($sCode,$iLastProdId);

                        if($props !== false) {

                            foreach($props as $prop){

                                $aSitemap[$dSitemap["IBLOCK_SECTION_ID"]][$typeProduct][$prop] = $prop;

                            }

                        }

                    }

				}

            }

        }
		
		if(!$iLastProdId) {

            require_once dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_get.php';
            return $aSitemap;

        } else {

            ++$skip;
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_get.php','<?php $aSitemap = '.var_export($aSitemap,true).'; ?>');
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_last.txt',$skip);
            header("Refresh: 1; url=".('https'.'://'.$_SERVER['SERVER_NAME'].'/local/crontab/sitemap.php?intestwetrust=1&time='.time()).'');
            echo '<html><header></header><body>'.time().'<script type="text/javascript">window.onload = function(){setTimeout(function(){location.href="'.'https'.'://'.$_SERVER['SERVER_NAME'].'/local/crontab/sitemap.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');};</script></body></html>';
            die();


        }

    }
	 
    private static function checkFamiliarModels(){

        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_done.txt',  'start');

        $sections = static::setSections();
        static::setProperties();
        $variations = static::getElementValues();
        static::getSectionPropLinks($variations);

        return 0;

    }

    private static function insertIntoDB($pCode,$sublink,$sCode){

        try{

            Bitrix\Main\Application::getConnection()->query('INSERT INTO `b_meta_sitemap`(`id`,`code`,`sublink`,`section_code`) VALUES(\'NULL\',\''.$pCode.'\',\''.$sublink.'\',\''.$sCode.'\')');

        } catch (Exception $e){


        }
    }

    private static function getSubSectionLinks(){

        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_subsection.php')) {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_subsection.php', '<?php $sSub = array(); ?>');
        };

        require_once dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_subsection.php';

        if(empty($sSub)){

            $asF = Array(
                'IBLOCK_ID' => P_IBLOCK_ID
            );

            $rsDb = CIBlockSection::GetList(Array(), $asF);

            if($rsDb) {

                while($sArr = $rsDb->GetNext())
                {
                    $sSub[$sArr['ID']] = $sArr['SECTION_PAGE_URL'];
                }

                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_subsection.php', '<?php $sSub = '.var_export($sSub,true).'; ?>');
            }

        }

        return $sSub;

    }

    public static function checkProccessed(){
        ob_start();
        passthru('ps aux | grep sitemap.php');
        $text = ob_get_clean();

        $aText = explode("\n", $text);

        $pCount = 0;

        foreach($aText as $sText){
			if(mb_stripos($sText,'/opt/php72/bin/php -f /var/www/twig/data/www/youtwig.ru/local/crontab/sitemap.php') !== false){
                ++$pCount;
            }

        }
		
		if($pCount > 1){
            die('processed');
        }
    }

    private static function createSitemap(){
		
		
        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_subcount.txt')) {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_subcount.txt', '0');
        };

        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_qsubcount.txt')) {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_qsubcount.txt', '0');
        };

        $iLastLink = 0;

        $sCount = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_subcount.txt'));
        $sLimit = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_qsubcount.txt'));

        if($sCount == 0
            && $sLimit == 0){

			foreach(glob(dirname(dirname(__DIR__)).'/sitemap_filters/sitemap_filters*.xml') as $file){
                
				@unlink($file);
            }
			

        }
		
		$sXmlName = dirname(dirname(__DIR__)).'/sitemap_filters/sitemap_filters_'.$sCount.'.xml';

        if(!file_exists($sXmlName)){
            file_put_contents($sXmlName,'<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n");
        }

        $fs = fopen($sXmlName,'a+');

        $sQuery = 'SELECT `id`,`sublink` FROM `b_meta_sitemap`';
        $rsDb = Bitrix\Main\Application::getConnection()->query($sQuery, $sLimit, static::$countQueries);

        if($rsDb){

            while($aSm = $rsDb->Fetch()){

                $iLastLink = $aSm['id'];
                $url = 'https'.'://'.$_SERVER['SERVER_NAME'].$aSm['sublink'];

                $sXml = "\t".'<url>'."\n".
                    "\t\t".'<loc>'.$url.'</loc>'."\n".
                    "\t\t".'<lastmod>'.date('c', filemtime($sXmlName)).'</lastmod>'."\n".
                    "\t".'</url>'."\n";
                fwrite($fs,$sXml);

            }
			
            $xmlLastLine = static::getLastLine($sXmlName);

            if($xmlLastLine != '</urlset>'){
                fwrite($fs,'</urlset>'."\n");
            }

            fclose($fs);
            ++$sCount;

        }
		
        if(!$iLastLink) {

            $xmlLastLine = static::getLastLine($sXmlName);

            if($xmlLastLine != '</urlset>'){
                fwrite($fs,'</urlset>'."\n");
            }

            fclose($fs);

            static::getSitemapCopy();

        } else {

            fclose($fs);

            $sLimit += static::$countQueries;

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_subcount.txt', $sCount);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_qsubcount.txt', $sLimit);

            header("Refresh: 1; url=".('https'.'://'.$_SERVER['SERVER_NAME'].'/local/crontab/sitemap.php?intestwetrust=1&time='.time()).'');
            echo '<html><header></header><body>'.time().'<script type="text/javascript">window.onload = function(){setTimeout(function(){location.href="'.'https'.'://'.$_SERVER['SERVER_NAME'].'/local/crontab/sitemap.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');};</script></body></html>';
            die();
		
        }

        //} catch (Exception $e){


        //}



    }

    private static function getLastLine($filePath, $lastPos = -1)
    {

        $fp = fopen($filePath, 'r');
        $pos = $lastPos;
        $t = " ";
        while ($t != "\n") {
            fseek($fp, $pos, SEEK_END);
            $t = fgetc($fp);
            $pos = $pos - 1;
        }
        $t = fgets($fp);

        if (trim($t) == "") {
            $t = static::getLastLine($filePath, $pos);
        }

        fclose($fp);
        return trim($t);

    }

    private static function getSitemapCopy()
    {

		{

			file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_last.txt', 0);
			file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_get.php','<?php $aSitemap = array(); ?>');
			file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_subcount.txt', '0');
			file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_qsubcount.txt', '0');
			file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_subsection.php', '<?php $sSub = array(); ?>');
			file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_get.php','<?php $aSitemap = array(); ?>');
			file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_vlast.txt', 0);
			file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_plast.txt', 0);

            $xmlPaths = glob(dirname(dirname(__DIR__)).'/sitemap_filters/*.xml');

            if(sizeof($xmlPaths) > 0) {

                $xmlContent =  '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

                foreach($xmlPaths as $xmlPath){

                    $url = str_ireplace(rtrim($_SERVER['DOCUMENT_ROOT'],'/'),'',$xmlPath);
                    $url = 'https'.'://'.$_SERVER['SERVER_NAME'].$url;

                    $sXml = '<sitemap>'.
                        '<loc>'.$url.'</loc>'.
                        '<lastmod>'.date('c', filemtime($xmlPath)).'</lastmod>'.
                        '</sitemap>';

                    $xmlContent .= $sXml;


                }

                $xmlContent .= '</sitemapindex>';
                file_put_contents($_SERVER['DOCUMENT_ROOT'].'/sitemap_filters.xml',$xmlContent);

                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_done.txt',  'done');

                $linesCount = static::getLinesCount();

                CEvent::SendImmediate(
                    'FILTERSITEMAP',
                    SITE_ID,
                    array(
                        'DATE' => date('Y.m.d H:i:s'),
                        'FILES_COUNT' => sizeof($xmlPaths),
                        'LINES_COUNT' => $linesCount
                    )
                );

            }

        }

    }

    private static function getSectionPropLinks($variations){
		
        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_vlast.txt')) {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_vlast.txt', 0);
        }

        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_plast.txt')) {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_plast.txt', 0);
        }
		
        $vlast = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_vlast.txt');
        $plast = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_plast.txt');

        $vcount = 0;
        $pcount = 0;
        $pFound = false;
        $sFound = false;
        $sSub = static::getSubSectionLinks();
		
		foreach($variations as $sId => $props){

            if($plast > $pcount){
                ++$pcount;
                continue;

            }

            $sLink = $sSub[$sId];

            $props = array_filter($props);

            foreach($props as $pCode => $pValues){

                if($vlast > $vcount){
                    ++$vcount;
                    continue;

                }

                $tLink = $sLink.'filter/typeproduct-is-'.$pCode.'/';
                static::insertIntoDB('TYPEPRODUCT',$tLink,$sId);

                $pFound = true;
                $sLevel = array();

				foreach($pValues as $vLink){

                    $sublink = $tLink.'manufacturer-is-'.$vLink.'/';
					static::insertIntoDB('MANUFACTURER',$sublink,$sId);
					
                }				

                ++$vcount;
                break;

			}


            if(!$pFound) {
                ++$pcount;
                $vcount = 0;
            }

            $sFound = true;

            break;

        }
		
		if($sFound) {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_vlast.txt', $vcount);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_plast.txt', $pcount);

            header("Refresh: 1; url=".('https'.'://'.$_SERVER['SERVER_NAME'].'/local/crontab/sitemap.php?intestwetrust=1&time='.time()).'');
            echo '<html><header></header><body>'.time().'<script type="text/javascript">window.onload = function(){setTimeout(function(){location.href="'.'https'.'://'.$_SERVER['SERVER_NAME'].'/local/crontab/sitemap.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');};</script></body></html>';
            die();

        } else {



            static::createSitemap();


        }

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            die ('<html><header><script>setTimeout(function(){location.href="'.'https'.'://'.$_SERVER['SERVER_NAME'].'/local/crontab/seoemptymodels.php?intestwetrust=1&time='.time().'";},'.mt_rand(50,70).');</script></header></html>');

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sitemap_filters_log_last.txt', 0);
            echo 'done';
            die();
        }

    }

    private static function getLinesCount()
    {

        $sQuery = 'SELECT COUNT(`id`) FROM `b_meta_sitemap`';
        $rsDb = Bitrix\Main\Application::getConnection()->query(
            $sQuery
        );

        $sCount = 0;

        if ($rsDb) {
            $aSm = $rsDb->Fetch();
            $sCount = current($aSm);
        }

        return $sCount;
    }

}

if(CModule::IncludeModule("iblock")){

    impelSitemap::checkProccessed();
	impelSitemap::checkModels();

}