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

class impelAvailProductsWI{

    private static $countStrings = 1000;
    private static $modelEl = null;
    private static $avail_products_wi_get = null;
    private static $avail_products_wi_names = null;


    private static function getNameById($product_id){

        $dRes = CIBlockElement::GetByID($product_id);

        if($dRes
            && $aRes = $dRes->GetNext()){

            if(isset($aRes['NAME'])){

                return $aRes['NAME'];

            }

        }

    }

    public static function checkModels($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkFamiliarModels();

        static::getRedirect($modelLastPropId);

    }

    private static function checkFamiliarModels(){

        if(is_null(static::$modelEl )){
            static::$modelEl = new CIBlockElement;
        }

        $modelEl = static::$modelEl;

        $modelLastPropId = 0;

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_last.txt'));

        $arProductSelect = Array(
            "ID",
            "NAME",
        );

        $arProductFilter = Array(
            "IBLOCK_ID" => 11,
            "ACTIVE" => "Y",
            "PREVIEW_PICTURE" => false
        );

        if($skip > 0){


        } else {

            $rsStore = CCatalogStore::GetList(
                array(),
                array(),
                false,
                false
            );

            if ($rsStore){

                while($arStore = $rsStore->Fetch()){

                    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_get'.$arStore['ID'].'.txt','');

                }

            }

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_last.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_get.txt','');
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_get.php','<?php $avail_products_wi_get = array(); ?>');
        }

        include_once(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_get.php');

        static::$avail_products_wi_get = $avail_products_wi_get;

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

            while($arProduct = $resProduct->GetNext()){

                static::hasAvailProduct($arProduct['ID']);
                $modelLastPropId = $arProduct['ID'];
                ++$countStrings;

            }

        }

        if($countStrings < static::$countStrings){
            $modelLastPropId = 0;
        }

        ++$skip;

        return $modelLastPropId ? $skip : 0;

    }

    private static function hasAvailProduct($product_id){

        $product_ib11_id = $product_id;
        $product_id = getBondsProduct($product_id);

        $in_stock_label = '';
        $can_buy = canYouBuy($product_id);
        $quantity = get_quantity_product($product_id);
        $can_buy = $quantity > 0 ? $can_buy : false;

        if($can_buy){

            $rsStore = CCatalogStoreProduct::GetList(
                array(),
                array(
                    'PRODUCT_ID' => $product_id
                ),
                false,
                false
            );

            if ($rsStore){

                $foundAmount = false;

                while($arStore = $rsStore->Fetch()){

                    $amount = (float)$arStore['AMOUNT'];

                    if($amount > 0){

                        $in_stock_label = (CMain::IsHTTPS() ? 'https' : 'http').'://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=11&type=catalog&ID='.$product_ib11_id."\n";
                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_get'.$arStore['STORE_ID'].'.txt',$in_stock_label,FILE_APPEND);

                        if(!isset(static::$avail_products_wi_get[$product_id])){
                            $product_name = static::getNameById($product_id);
                            if($product_name){
                                static::$avail_products_wi_get[$product_id] = array($product_name,0,array());
                            }
                        }

                        $foundAmount = true;

                        static::$avail_products_wi_get[$product_id][2][$arStore['STORE_ID']] = $arStore['STORE_ID'];


                    }

                }

                if($foundAmount)
                    ++static::$avail_products_wi_get[$product_id][1];

            }

        }

        return $in_stock_label;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_last.txt', $skip);

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_get.php','<?php $avail_products_wi_get = '.var_export(static::$avail_products_wi_get,true).'; ?>');

            //echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/products_without_images.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
            die();
        } else {

            $rsStore = CCatalogStore::GetList(
                array(),
                array(),
                false,
                false
            );

            if ($rsStore){

                while($arStore = $rsStore->Fetch()){

                    if(file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_get'.$arStore['ID'].'.txt')){

                        $content = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_get'.$arStore['ID'].'.txt');
                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_get.txt',"\n\n".$arStore['TITLE']."\n\n", FILE_APPEND);


                        foreach(static::$avail_products_wi_get as $product_id => $productInfo){
                            if(isset($productInfo[2][$arStore['ID']])){
                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_get.txt',$productInfo[0].' ['.$product_id.'] x '.$productInfo[1]."\n", FILE_APPEND);
                            }

                        }





                    }

                }




            }






            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_products_wi_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelAvailProductsWI::checkModels();