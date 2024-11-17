#!/usr/bin/php -q
<?php

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

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

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelCatalogImages{

    private static $countStrings = 200;
    private static $imageFile = 'set_base_image_last.txt';

    public static function checkImages(){
        $skip = static::getNextImage();
        static::getRedirect($skip);
    }

    private static function getNextImage(){

        $baseElt = new CIBlockElement;
        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/'.static::$imageFile));

        $arProductSelect = Array(
            "ID"
        );

        $arProductFilter = Array(
            "IBLOCK_ID" => 16,
            "ACTIVE" => "Y",
            //"PREVIEW_PICTURE" => false
        );

        if(!empty($skip)){

            //$arProductFilter['>ID'] = $skip;

        }

        $resProduct = CIBlockElement::GetList(
            ($order = Array('ID' => 'ASC')),
            $arProductFilter,
            false,
            false,
            $arProductSelect
        );

        $pLastPropId = 0;

        if($resProduct){

            while($arProduct = $resProduct->GetNext()){

				$pLastPropId = $arProduct['ID'];
                $sfImage = static::getFirstImageProd($pLastPropId);

				$arrImage = array('IBLOCK_ID' => 16, 'TIMESTAMP_X' => true);
				
                if($sfImage){

                    $arrImage["DETAIL_PICTURE"] = CFile::MakeFileArray($sfImage);
                    $arrImage["DETAIL_PICTURE"]["COPY_FILE"] = "Y";

                    $arrImage["PREVIEW_PICTURE"] = CFile::MakeFileArray($sfImage);
                    $arrImage["PREVIEW_PICTURE"]["COPY_FILE"] = "Y";
                
                } else {
					
					$arrImage["DETAIL_PICTURE"] = $arrImage["PREVIEW_PICTURE"] = array('del' => 'Y');
				}
				
				$baseElt->Update($pLastPropId, $arrImage);
				

            }

        }

        return $pLastPropId;

    }

    private static function getFirstImageProd($product_id){

        $pImage = false;

        $arProductSelect = Array(
            "ID",
            "PREVIEW_PICTURE"
        );

        $arProductFilter = Array(
            "IBLOCK_ID" => 11,
            "!PREVIEW_PICTURE" => false,
            "PROPERTY_MAIN_PRODUCTS" => $product_id
        );


        $resProduct = CIBlockElement::GetList(
            ($order = Array()),
            $arProductFilter,
            false,
            false,
            $arProductSelect
        );

        if($resProduct){
            $arProduct = $resProduct->GetNext();

            if(isset($arProduct["PREVIEW_PICTURE"])
                && !empty($arProduct["PREVIEW_PICTURE"])
            ){

                $pImage = $arProduct["PREVIEW_PICTURE"];

            }

        }
		
        return $pImage;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/'.static::$imageFile, $skip);
            die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/crontab/base_image.php?intestwetrust=1&skip='.$skip.'&file='.urlencode($_REQUEST['file']).'&time='.time().'";},'.mt_rand(5,30).');</script></header></html>');
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/'.static::$imageFile, 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelCatalogImages::checkImages();