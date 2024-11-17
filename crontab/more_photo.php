#!/usr/bin/php -q
<?php

if (isset($argc) && ($argc > 0) && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) {
    die();
}

//https://youtwig.ru/local/crontab/more_photo.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

ini_set('default_charset','utf-8');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

$aProductSelect = array(
    'ID',
    'IBLOCK_ID',
    'PROPERTY_MORE_PHOTO',
    'PREVIEW_PICTURE',
    'DETAIL_PICTURE'
);

$aProductFilter = array(
    'IBLOCK_ID' => 11,
    '!PROPERTY_MORE_PHOTO' => false
);

$resProduct = CIBlockElement::GetList(
    ($order = Array('ID' => 'DESC')),
    $aProductFilter,
    false,
    false,
    $aProductSelect
);

$iElement = new CIBlockElement;

function checkImage($imgPath):array
{
    $return = [];

    if (is_numeric($imgPath)) {
        $imgPath = \CFile::getPath($imgPath);
    }

    if (!empty($imgPath)) {
        $imgPath = $_SERVER['DOCUMENT_ROOT'] . $imgPath;
    }

    if (!empty($imgPath)
        && is_file($imgPath)
        && file_exists($imgPath)) {

        $isValid = false;

        try {

            if (class_exists('Imagick')) {

                $imagick = new Imagick($imgPath);
                $isValid = $imagick->valid();

            }

        } catch (Exception $exception) {

        }

        if ($imgPath && $isValid) {
            $sizes = getimagesize($imgPath);

            if ($sizes[0] > 0
                && $sizes[1] > 0) {

                $return = ['src' => $imgPath, 'width' => $sizes[0], 'height' => $sizes[1]];

            }

        }

    }

    return $return;
}

file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/more_photos.txt',"");

$moreImage = [];

if($resProduct) {
    while ($arProduct = $resProduct->GetNext()) {

        if (isset($moreImage[$arProduct['ID']])) {
            continue;
        }

        if (isset($arProduct['PROPERTY_MORE_PHOTO_VALUE'])
            && !empty($arProduct['PROPERTY_MORE_PHOTO_VALUE'])) {

            $imgPath = trim($arProduct['PROPERTY_MORE_PHOTO_VALUE']);
            $imgPath = checkImage($imgPath);

            if (isset($imgPath['width']) && isset($imgPath['height'])) {

                $prvPath = trim($arProduct['PREVIEW_PICTURE']);
                $prvPath = checkImage($prvPath);

                $prvReplace = true;

                if (isset($prvPath['width'])
                    && isset($prvPath['height'])
                    && $prvPath['width'] > $imgPath['width']
                    && $prvPath['height'] > $imgPath['height']
                ) {
                    $prvReplace = false;
                }

                $detPath = trim($arProduct['DETAIL_PICTURE']);
                $detPath = checkImage($detPath);

                $detReplace = true;

                if (isset($detPath['width'])
                    && isset($detPath['height'])
                    && $detPath['width'] > $imgPath['width']
                    && $detPath['height'] > $imgPath['height']
                ) {
                    $detReplace = false;
                }

                $aUpdate = [];

                if ($detReplace) {
                    $aUpdate["PREVIEW_PICTURE"] = \CFile::MakeFileArray($imgPath['src']);
                    $aUpdate["PREVIEW_PICTURE"]["COPY_FILE"] = "Y";
                }

                if ($prvReplace) {
                    $aUpdate["DETAIL_PICTURE"] = \CFile::MakeFileArray($imgPath['src']);
                    $aUpdate["DETAIL_PICTURE"]["COPY_FILE"] = "Y";
                }

                $iElement->Update($arProduct['ID'],$aUpdate);

                $moreImage[$arProduct['ID']] = $arProduct['ID'];

                file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/more_photos.txt',$arProduct['ID']."\n",FILE_APPEND);

            }

        }

    }

}