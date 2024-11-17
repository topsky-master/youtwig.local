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

//тип продукта;производитель;модель;товар;ком код;инд код;вид код;вид поз;вид изображение;

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])
    || !file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_products.csv')
    || file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/lockremove')){
    die();
}

file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/lockremove',date('Y.m.d H:i:s'));

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

ini_set('max_execution_time',999999);
ignore_user_abort();

if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_products_last.txt')){
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_products_last.txt',0);
}


$skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_products_last.txt'));

$maxCount = 300;
$currentCount = 0;

$arModelNameSelect = Array(
    "ID",
    "NAME"
);

$arModelNameFilter = Array(
    "IBLOCK_ID" => 27,
    'ACTIVE' => 'Y',
);

$lines = file(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_products.csv');
$lines = array_slice($lines,$skip,$maxCount);
$lines = array_map('trim',$lines);
$modelEl = new impelCIBlockElement;


foreach($lines as $line){

    $line = trim($line);
    $line = str_getcsv($line,";");


    if($line
        && isset($line[0])
        && !empty($line[0])){

        ++$currentCount;

        $types = array();

        $modelSearchName = $line[2];
        $modelSearchName = trim(preg_replace('~\s*?\([\d\s]+\s*?$~isu','',$modelSearchName));
        $modelSearchName = trim(preg_replace('~\s*?\([\d\s]+\)\s*?$~isu','',$modelSearchName));
        $modelSearchName = trim(str_ireplace('(','',$modelSearchName));
        $modelSearchName = trim(str_ireplace(')','',$modelSearchName));
        $modelSearchName = trim(preg_replace('~\s+~isu','',$modelSearchName));

        if(!empty($modelSearchName)){

            $productType = trim($line[0]);
            $manufacturer = trim($line[1]);

            $arModelNameFilter['=NAME'] = $modelSearchName;

            //Холодильник
            //Bosch

            $resModelName = impelCIBlockElement::GetList(
                ($order = Array('ID' => 'DESC')),
                $arModelNameFilter,
                false,
                false,
                $arModelNameSelect
            );

            if($resModelName){

                while($arModelNameFields = $resModelName->GetNext()){

                    if(isset($arModelNameFields['ID'])
                        && !empty($arModelNameFields['ID'])){


                        $arModelSelect = array(
                            'ID',
                            'PROPERTY_type_of_product',
                            'PROPERTY_manufacturer',
                        );

                        $arModelFilter = array(
                            'IBLOCK_ID' => 17,
                            'PROPERTY_model_new_link' => $arModelNameFields['ID'],
                            'ACTIVE' => 'Y',
                            'PROPERTY_type_of_product_VALUE' => $productType,
                            'PROPERTY_manufacturer_VALUE' => $manufacturer,
                            '!PROPERTY_VERSION_VALUE' => 'Да',
                            //'PROPERTY_PRODUCTS_REMOVED' => false
                        );

                        $resModel = impelCIBlockElement::GetList(
                            ($order = Array('ID' => 'DESC')),
                            $arModelFilter,
                            false,
                            false,
                            $arModelSelect
                        );

                        if($resModel) {

                            while ($arModelFields = $resModel->GetNext()) {

                                {

                                    $foundModel = $arModelFields['ID'];


                                    $indcodes = array();

                                    $dbProductProps = impelCIBlockElement::GetProperty(
                                        17,
                                        $foundModel,
                                        Array("sort" => "asc"),
                                        Array("CODE" => "SIMPLEREPLACE_INDCODE")
                                    );

                                    if ($dbProductProps) {

                                        while ($arProductProps = $dbProductProps->GetNext()) {
                                            if(isset($arProductProps["VALUE"]) && !empty($arProductProps["VALUE"]))
                                                $indcodes[] = array('VALUE' => $arProductProps["VALUE"], 'DESCRIPTION' => '');
                                        }

                                    }

                                    /* $comcodes = array();

                                    $dbProductProps = impelCIBlockElement::GetProperty(
                                        17,
                                        $foundModel,
                                        Array("sort" => "asc"),
                                        Array("CODE" => "COMCODE")
                                    );

                                    if ($dbProductProps) {

                                        while ($arProductProps = $dbProductProps->GetNext()) {
                                            if(isset($arProductProps["VALUE"]) && !empty($arProductProps["VALUE"]))
                                                $comcodes[] = array('VALUE' => $arProductProps["VALUE"], 'DESCRIPTION' => '');
                                        }

                                    } */

                                    $positions = array();

                                    $dbProductProps = impelCIBlockElement::GetProperty(
                                        17,
                                        $foundModel,
                                        Array("sort" => "asc"),
                                        Array("CODE" => "SIMPLEREPLACE_POSITION")
                                    );

                                    if ($dbProductProps) {

                                        while ($arProductProps = $dbProductProps->GetNext()) {
                                            if(isset($arProductProps["VALUE"]) && !empty($arProductProps["VALUE"]))
                                                $positions[] = array('VALUE' => $arProductProps["VALUE"], 'DESCRIPTION' => '');
                                        }

                                    }

                                    $views = array();

                                    $dbProductProps = impelCIBlockElement::GetProperty(
                                        17,
                                        $foundModel,
                                        Array("sort" => "asc"),
                                        Array("CODE" => "SIMPLEREPLACE_VIEW")
                                    );

                                    if ($dbProductProps) {

                                        while ($arProductProps = $dbProductProps->GetNext()) {
                                            if(isset($arProductProps["VALUE"]) && !empty($arProductProps["VALUE"]))
                                                $views[] = array('VALUE' => $arProductProps["VALUE"], 'DESCRIPTION' => '');
                                        }

                                    }

                                    $dbProductProps = impelCIBlockElement::GetProperty(
                                        17,
                                        $foundModel,
                                        Array("sort" => "asc"),
                                        Array("CODE" => "RESTORE_PRODUCTS")
                                    );

                                    $products = '';

                                    if ($dbProductProps) {

                                        while ($arProductProps = $dbProductProps->GetNext()) {
                                            if(isset($arProductProps["VALUE"]) && !empty($arProductProps["VALUE"]))
                                                $products = $arProductProps["VALUE"];
                                        }

                                    }

                                    $products = explode(',',$products);

                                    if(!empty($products)){
                                        $products = !is_array($products) ? array($products) : $products;
                                        $products = array_map('trim',$products);
                                    } else {
                                        $products = array();
                                    }
                                    $dbProductProps = impelCIBlockElement::GetProperty(
                                        17,
                                        $foundModel,
                                        Array("sort" => "asc"),
                                        Array("CODE" => "SIMPLEREPLACE_PRODUCTS")
                                    );

                                    if ($dbProductProps) {

                                        while ($arProductProps = $dbProductProps->GetNext()) {
                                            $products[] = $arProductProps["VALUE"];
                                        }

                                    }


                                    $restore_products = $toBaseProducts = array();

                                    if(((empty($indcodes)
                                        && empty($positions)
                                        && empty($views)
                                    ))
                                    ) {

                                        $products = array_map('trim',$products);
                                        $products = array_unique($products);
                                        $products = array_filter($products);

                                        if (!empty($products)
                                            && sizeof($products) > 0) {


                                            $products = join(',',$products);
                                            $products = trim($products,',');
                                            $products = trim($products);

                                            $toBaseProducts['RESTORE_PRODUCTS'] = $products;

                                        }



                                        $toBaseProducts['PRODUCTS_REMOVED'] = 56422;
                                        $toBaseProducts['SIMPLEREPLACE_PRODUCTS'] = false;

                                        $toBaseProducts['SIMPLEREPLACE_INDCODE'] = false;
                                        //$toBaseProducts['COMCODE'] = false;
                                        $toBaseProducts['SIMPLEREPLACE_POSITION'] = false;
                                        $toBaseProducts['SIMPLEREPLACE_VIEW'] = false;

                                        impelCIBlockElement::SetPropertyValuesEx($foundModel, 17, $toBaseProducts);
										//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $foundModel);


                                        $modelEl->Update($foundModel, Array('TIMESTAMP_X' => true));


                                    }

                                }

                            }

                        }

                    }

                }

            }

        }

    }

}


if(!empty($currentCount)){
    $skip += $currentCount;
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_products_last.txt', $skip);
    unlink(dirname(dirname(__DIR__)).'/bitrix/tmp/lockremove');

    echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/remove_products.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
    die();
} else {
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_products_last.txt', 0);
    echo 'done';
    CEvent::SendImmediate('REMOVE_MODELS', 's1', array('TIME' => date('Y.m.d H:i:s')));
    unlink(dirname(dirname(__DIR__)).'/bitrix/tmp/lockremove');
    unlink(dirname(dirname(__DIR__)).'/bitrix/tmp/remove_products.csv');
    die();
}

