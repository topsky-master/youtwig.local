<?php

//https://youtwig.ru/local/crontab/views_images.php?intestwetrust=1


$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelViewImages{

    private static $countStrings = 2000;

    public static function getList(){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        $modelLastPropId = 0;

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_views_images_last.txt'));

        $arProductSelect = Array(
            "ID",
            "NAME",
        );

        $arProductFilter = Array(
            "IBLOCK_ID" => 34,
            "ACTIVE" => "Y",
            "PREVIEW_PICTURE" => false
        );

        $flag = 'w+';
        $viewEl = new CIBlockElement;

        if($skip > 0){

            $flag = 'a+';

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_views_images_last.txt', 0);

        }

        $skip = empty($skip) ? 1 : $skip;
        $countStrings = 0;

        $arProductNavParams = array(
            'nTopCount' => false,
            'nPageSize' => static::$countStrings,
            'iNumPage' => $skip,
            'checkOutOfRange' => true
        );

        $resProduct = CIBlockElement::GetList(
            ($order = Array()),
            $arProductFilter,
            false,
            $arProductNavParams,
            $arProductSelect
        );

        if($resProduct){

			$imgPath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/images/upload/';

            while($arModel = $resProduct->GetNext()) {

                ++$countStrings;

                $modelLastPropId = $arModel['ID'];

                $fileExts = array('jpg','png','gif','jpeg');

                foreach($fileExts as $fileExt) {

                    $cImg = $imgPath.$arModel['NAME'].'.'.$fileExt;

                    if(file_exists($cImg)) {

                        if(class_exists('Imagick')){

                            try{

                                $imagick = new Imagick($cImg);
                                $valid = $imagick->valid();

                                if($valid){

                                    $viewProperties['PREVIEW_PICTURE'] = CFile::MakeFileArray($cImg);
                                    $viewProperties['TIMESTAMP_X'] = true;

                                    if ($viewEl->Update($arModel['ID'], $viewProperties)) {
                                        unlink($cImg);
                                    } else {
                                        if (isset($viewEl->LAST_ERROR)) {
                                            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'Model IB 34: ' . trim($cImg) . ',' . ', ' . $viewEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                        }
                                    }

                                }

                            } catch (Exception $e){

                            }

                        } else {

                            $imginfo = getimagesize($cImg);
                            $spExt = pathinfo($cImg,PATHINFO_EXTENSION);
                            $spExt = mb_strtolower($spExt);

                            if(isset($imginfo[0])
                                && !empty($imginfo[0])
                                && isset($imginfo[1])
                                && !empty($imginfo[1])
                                && (filesize($cImg) > 0)
                                && (in_array($spExt,array('jpg','jpeg','gif','png')))
                            ) {

                                $viewProperties['PREVIEW_PICTURE'] = CFile::MakeFileArray($cImg);
                                $viewProperties['TIMESTAMP_X'] = true;

                                if ($viewEl->Update($arModel['ID'], $viewProperties)) {
                                    unlink($cImg);
                                } else {
                                    if (isset($viewEl->LAST_ERROR)) {
                                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'Model IB 34: ' . trim($cImg) . ',' . ', ' . $viewEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                    }
                                }

                            }


                        }

                    }

                }

            }

        }



        if($countStrings < static::$countStrings){
            $modelLastPropId = 0;
        }

        ++$skip;

        return $modelLastPropId ? $skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_views_images_last.txt', $skip);
			die('<html><header><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/views_images.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></header><body><h1>'.time().'</h1></body></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_views_images_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelViewImages::getList();