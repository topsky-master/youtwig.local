<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelWhirpoolDuplicatesModels{

    private static $countStrings = 200;

    public static function checkModels($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkFamiliarModels();

        static::getRedirect($modelLastPropId);

    }

    private static function checkFamiliarModels(){

        $modelLastPropId = 0;

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_last.txt'));

        $arModelNameSelect = Array(
            "ID",
            "NAME",
            "PROPERTY_CLEAR_NAME",
            "PROPERTY_PRODUCTS_REMOVED"
        );

        $arModelNameFilter = Array(
            "IBLOCK_ID" => 17,
            "ACTIVE" => "Y",
            //"PROPERTY_manufacturer_VALUE" => "Whirlpool"
        );



        if($skip > 0){


        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_last.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates.txt', "");
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_get.txt',0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_get.php','<?php $WhirlpoolModels = array(); ?>');
        }

        require_once dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_get.php';

        $skip = empty($skip) ? 1 : $skip;

        $arNameNavParams = array(
            'nTopCount' => false,
            'nPageSize' => static::$countStrings,
            'iNumPage' => $skip,
            'checkOutOfRange' => true
        );

        $resModelName = CIBlockElement::GetList(
            ($order = Array('PROPERTY_CLEAR_NAME' => 'DESC', 'timestamp_x' => 'ASC')),
            $arModelNameFilter,
            false,
            $arNameNavParams,
            $arModelNameSelect
        );

        if($resModelName){

            while($arModelNameFields = $resModelName->GetNext()){

                $modelLastPropId = $arModelNameFields['ID'];
                $clearNameValue = $arModelNameFields['PROPERTY_CLEAR_NAME_VALUE'];

                if(!isset($WhirlpoolModels[$clearNameValue])){
                    $WhirlpoolModels[$clearNameValue] = array();
                }


                if(!in_array($arModelNameFields['ID'],$WhirlpoolModels)){

                    if(isset($arModelNameFields['PROPERTY_PRODUCTS_REMOVED_VALUE'])
                        && !empty($arModelNameFields['PROPERTY_PRODUCTS_REMOVED_VALUE'])) {

                        array_unshift($WhirlpoolModels[$clearNameValue], $modelLastPropId);

                    } else {

                        $WhirlpoolModels[$clearNameValue][] = $modelLastPropId;

                    }


                }

            }
        }

        ++$skip;

        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_get.php','<?php $WhirlpoolModels = '.var_export($WhirlpoolModels,true).'; ?>');

        return $modelLastPropId ? $skip : 0;

    }

    private static function addRedirect($what,$where){

        $what = trim($what);
        $what = preg_replace('~http(s*?)://[^/]+?/~isu','',$what);
        $what = rtrim($what,'/');

        $where = trim($where);
        $where = preg_replace('~http(s*?)://[^/]+~isu','',$where);
        $where = empty($where) ? "/" : $where;

        $show = false;
        $rsData = CBXShortUri::GetList(
            Array(),
            Array(
                "URI" => '/'.trim($where,'/').'/',
                "SHORT_URI" => trim($what,'/')
            )
        );

        while($arRes = $rsData->Fetch()) {
            $show = true;
            break;
        }

        $rsData = CBXShortUri::GetList(
            Array(),
            Array(
                "URI" => '/'.trim($what,'/').'/',
                "SHORT_URI" => trim($where,'/')
            )
        );

        while($arRes = $rsData->Fetch()) {
            $show = true;
            break;
        }

        if (!$show
            &&
            (
                (trim(mb_strtolower($where),'/') != trim(mb_strtolower($what),'/'))
                || (trim(($where),'/') != trim(($what),'/'))
            )
        ){

            $arShortFields = Array(
                "URI" => '/'.trim($where,'/').'/',
                "SHORT_URI" => trim($what,'/'),
                "STATUS" => "301",
            );

            CBXShortUri::Add($arShortFields);

        }

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_last.txt', $skip);
            echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/whirpool_duplicates_array.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
            die();
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelWhirpoolDuplicatesModels::checkModels();