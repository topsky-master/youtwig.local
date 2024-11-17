<?php

//https://youtwig.ru/local/crontab/merge_doubles_indcode_files.php?intestwetrust=1
//https://youtwig.ru/local/crontab/merge_doubles_indcode_files.php?intestwetrust=1&delete=1
///bitrix/tmp/merge_doubles_indcode.csv разделитель ;
///bitrix/tmp/impel_getdoubles_error.csv ошибки запишутся сюда (если например не определен основной товар)
///bitrix/tmp/impel_getdoublesu.csv результаты будут записаны сюда

define('IMPEL_TYPE_OF_MAIN_PRODUCT','');//тип главного товара?
define('IMPEL_TYPE_OF_DOUBLE_PRODUCT','');//тип ошибочного товар?

define('IMPEL_TYPE_OF_MAIN_MANUFACTURER','GRUNDIG');//производитель главного товара?
define('IMPEL_TYPE_OF_DOUBLE_MANUFACTURER','Grunding');//производитель ошибочного товар?

define('IMPEL_COLUMNS_MODEL_NUMBER',1);//какой номер столбца в csv это модель? (начинаем с 1го)
define('IMPEL_TEST',0);//режим теста? обработает 1 модель и выведет все предупреждения, отключить: 0



define('IMPEL_MERGE',1);//нужен ли мерж, или просто редирект?
define('IMPEL_SKIP_MANUFACTURER','');//пропустить производителей

define('IMPEL_SET_TYPE_OF_PRODUCT',''); //заменить главному товару тип товара, или оставьте пустым
define('IMPEL_SET_MANUFACTURER','GRUNDIG'); //заменить главному товару производителя, или оставьте пустым

define('IMPEL_CHECK_INTERSECT',0); //проверить, есть ли общие индкоды у дублей
define('IMPEL_CHECK_EMPTY',0); //проверить, есть ли не пустые индкоды у дублей
define('IMPEL_STOP_CHECK',0); //если нужно брать все дубли моделей без проверки дополнительной по производителю/типу товара

//если требуется уточнить производителя, тип товара или (например) товар и т.п. для списка моделей, дополнительно
//приведу пример, или оставить пустым

/* $aFilter = array(
    'PROPERTY_manufacturer_VALUE' => array('Bosch'),
    'PROPERTY_type_of_product_VALUE' => array('Стиральная машина'),
    'PROPERTY_SIMPLEREPLACE_PRODUCTS' => array(10771)
);

$aFilter = array(
    'PROPERTY_manufacturer_VALUE' => array('IKEA'),
);
 */
function errx() {
    print_r(error_get_last());
}

if (IMPEL_TEST) {
    register_shutdown_function('errx');
}

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);
define('WORKING_DIR',dirname(dirname(__DIR__)).'/bitrix/tmp/');

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) {
    die();
};

define('IMPEL_CSV_FILE',WORKING_DIR.'merge_doubles_indcode.csv');

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelGetDoublesEmpty{

    private static $maxCount = 2;
    private static $rdFp = false;
    private static $rFp = false;

    private static $check_intersect = IMPEL_CHECK_INTERSECT;
    private static $check_empty = IMPEL_CHECK_EMPTY;

    private static $product_merge = IMPEL_MERGE;

    private static $type_of_product = IMPEL_TYPE_OF_MAIN_PRODUCT;
    private static $type_of_double = IMPEL_TYPE_OF_DOUBLE_PRODUCT;

    private static $skip_manufacturer = IMPEL_SKIP_MANUFACTURER;

    private static $manufacturer_of_product = IMPEL_TYPE_OF_MAIN_MANUFACTURER;
    private static $manufacturer_of_double = IMPEL_TYPE_OF_DOUBLE_MANUFACTURER;

    private static $set_type_of_product = IMPEL_SET_TYPE_OF_PRODUCT;
    private static $models_number = IMPEL_COLUMNS_MODEL_NUMBER;
    private static $test = IMPEL_TEST;
    private static $not_check = IMPEL_STOP_CHECK;

    private static $set_manufacturer = IMPEL_SET_MANUFACTURER;

    private static $modelname = '';
    private static $aModelLink = array();

    private static $aNames = array();
    private static $aCodes = array();
    private static $aFilter = array();

    public static function getList($aFilter){

        static::$aFilter = $aFilter;

        if (!empty(static::$skip_manufacturer)) {

            static::$skip_manufacturer = explode(',',static::$skip_manufacturer);
            static::$skip_manufacturer = array_map('trim',static::$skip_manufacturer);
            static::$skip_manufacturer = array_map('mb_strtolower',static::$skip_manufacturer);
            static::$skip_manufacturer = array_unique(static::$skip_manufacturer);
            static::$skip_manufacturer = array_filter(static::$skip_manufacturer);
        }

        static::checkParams();

        global $USER;

        $modelLastPropId = 0;

        $rFp = fopen(IMPEL_CSV_FILE,'r');
        $aStrings = array();

        $skip = trim(file_get_contents(WORKING_DIR.'impel_getdoublesu_last.txt'));
        $mFound = 0;

        if (isset($_REQUEST['delete'])) {

            file_put_contents(WORKING_DIR.'impel_getdoublesu_last.txt', 0);
            static::$rFp = fopen(WORKING_DIR.'impel_getdoublesu.csv', 'w+');
            static::$rdFp = fopen(WORKING_DIR.'impel_getdoubles_error.csv', 'w+');

        } elseif ($skip > 0) {

            static::$rFp = fopen(WORKING_DIR.'impel_getdoublesu.csv', 'a+');
            static::$rdFp = fopen(WORKING_DIR.'impel_getdoubles_error.csv', 'a+');

        } else {

            file_put_contents(WORKING_DIR.'impel_getdoublesu_last.txt', 0);
            static::$rFp = fopen(WORKING_DIR.'impel_getdoublesu.csv', 'w+');
            static::$rdFp = fopen(WORKING_DIR.'impel_getdoubles_error.csv', 'w+');

            $skip = 1;
        }

        if ($rFp && is_resource($rFp)) {
            $iCount = 0;
            --static::$models_number;

            while ($aStr = fgetcsv($rFp,0,';')) {
                ++$iCount;
                $aStr = array_map('trim',$aStr);
                $sModel = $aStr[static::$models_number];
                if (isset($sModel) && !empty($sModel)) {

                    if ($iCount < $skip) {
                        continue;
                    }

                    ++$skip;

                    static::getModelName($sModel);

                    static::checkList($sModel);

                    static::getRedirect($skip);

                }

            }


        }

        fclose($rFp);

        file_put_contents(WORKING_DIR.'impel_getdoublesu_last.txt', 0);
        echo 'done';
    }

    public static function getDefaults () {

        static $aDefaults;

        if (empty($aDefaults)) {

            $skipProdId = 0;

            $arProdFilter = Array(
                "CODE" => "bez_tovara",
                "IBLOCK_ID" => 11
            );

            $arProdSelect = Array("ID");

            $resProdDB = impelCIBlockElement::GetList(Array(), $arProdFilter, false, false, $arProdSelect);

            $resProdArr = Array();

            if($resProdDB) {
                $resProdArr = $resProdDB->GetNext();

                if(isset($resProdArr['ID'])
                    && !empty($resProdArr['ID'])){

                    $skipProdId = $resProdArr['ID'];
                }
            }

            $skipViewId = 0;

            $arViewFilter = Array(
                "CODE" => "bez_vida",
                "IBLOCK_ID" => 34
            );

            $arViewSelect = Array("ID");

            $resViewDB = impelCIBlockElement::GetList(Array(), $arViewFilter, false, false, $arViewSelect);

            $resViewArr = Array();

            if($resViewDB) {
                $resViewArr = $resViewDB->GetNext();

                if(isset($resViewArr['ID'])
                    && !empty($resViewArr['ID'])){

                    $skipViewId = $resViewArr['ID'];
                }
            }

            $skipIndCodeId = 0;

            $arCodeFilter = Array(
                "CODE" => "bez_ind_koda",
                "IBLOCK_ID" => 35
            );

            $arCodeSelect = Array("ID");

            $resCodeDB = impelCIBlockElement::GetList(Array(), $arCodeFilter, false, false, $arCodeSelect);

            $resCodeArr = Array();

            if($resCodeDB) {
                $resCodeArr = $resCodeDB->GetNext();

                if(isset($resCodeArr['ID'])
                    && !empty($resCodeArr['ID'])){

                    $skipIndCodeId = $resCodeArr['ID'];

                }
            }

            $aDefaults = ['product_id' => $skipProdId,'view_id' => $skipViewId,'indcode_id' => $skipIndCodeId, 'posiiton' => '-'];

        }

        return $aDefaults;

    }

    private static function checkParams() {

        $message = '';

        if (!empty(static::$type_of_product)) {
            $type_id = static::get_param(static::$type_of_product);
            if (!$type_id) {
                $message .= 'Тип основного товара задан не верно. Вы задали '.static::$type_of_product.'. Проверьте <a href="https://youtwig.ru/bitrix/admin/iblock_edit.php?type=catalog&lang=ru&ID=17&admin=Y" target="blank">в настройках свойства Тип товара</a><br />';
            }
        }

        if (!empty(static::$type_of_double)) {
            $type_id = static::get_param(static::$type_of_double);
            if (!$type_id) {
                $message .= 'Тип дублей по типу товара задан не верно. Вы задали '.static::$type_of_double.'. Проверьте <a href="https://youtwig.ru/bitrix/admin/iblock_edit.php?type=catalog&lang=ru&ID=17&admin=Y" target="blank">в настройках свойства Тип товара</a><br />';
            }
        }

        if (!empty(static::$manufacturer_of_product)) {
            $man_id = static::get_param(static::$manufacturer_of_product,'manufacturer');
            if (!$man_id) {
                $message .= 'Тип основного производителя задан не верно. Вы задали '.static::$manufacturer_of_product.'. Проверьте <a href="https://youtwig.ru/bitrix/admin/iblock_edit.php?type=catalog&lang=ru&ID=17&admin=Y" target="blank">в настройках свойства Производитель</a><br />';
            }
        }

        if (!empty(static::$manufacturer_of_double)) {
            $man_id = static::get_param(static::$manufacturer_of_double,'manufacturer');
            if (!$man_id) {
                $message .= 'Тип дублей по производителю задан не верно. Вы задали '.static::$manufacturer_of_double.'. Проверьте <a href="https://youtwig.ru/bitrix/admin/iblock_edit.php?type=catalog&lang=ru&ID=17&admin=Y" target="blank">в настройках свойства Производитель</a><br />';
            }
        }

        if (!empty(static::$manufacturer_of_product) && !empty(static::$type_of_product)) {
            $message .= 'Одновременно производитель и тип товара для поиска главного товара не может быть задан, это может привести к ошибке, для уточнения дополнительных параметров см. $aFilter';
        }

        if (!empty($message)) {
            die($message);

        }

    }

    private static function checkList($sModel) {

        if (!isset(static::$aModelLink[$sModel]) || empty(static::$aModelLink[$sModel])) {
            return ;
        }

        $aMSelect = Array(
            "ID",
            "NAME",
            "PROPERTY_model_new_link",
            "PROPERTY_type_of_product"
        );

        $iModels = array_values(static::$aModelLink[$sModel]);

        if (!empty($iModels)) {

            $aMFilter = Array(
                "IBLOCK_ID" => 17,
                "ACTIVE" => "Y",
                "ID" => $iModels
            );

            if (!empty(static::$aFilter)) {
                $aMFilter = array_merge($aMFilter,static::$aFilter);
            }

            $rModels = impelCIBlockElement::GetList(
                Array(
                    'ID' => 'ASC'
                ),
                $aMFilter,
                false,
                false,
                $aMSelect);

            if($rModels) {

                $auModels = array(
                    'VALUES' => array()
                );

                while ($aModels = $rModels->GetNext()) {

                    $mFound = $aModels['ID'];

                    if (!empty($mFound)) {

                        static::getModel($aModels['ID'], $auModels);

                    }

                }

                static::checkDoubles($auModels);

            }

        }

        fclose(static::$rFp);
        fclose(static::$rdFp);

    }

    private static function getIndcodeId($sName)
    {

        $return = [];

        $aMSelect = Array(
            "ID",
        );

        $aMFilter = Array(
            "IBLOCK_ID" => 35,
            "=NAME" => $sName,
            "ACTIVE" => "Y",
        );

        $rIndCode = impelCIBlockElement::GetList(
            Array(
                'ID' => 'ASC'
            ),
            $aMFilter,
            false,
            false,
            $aMSelect);

        if($rIndCode){

            while($aIndCode = $rIndCode->GetNext()){
                $return[$aIndCode['ID']] = $aIndCode['ID'];
            }

        }

        return $return;

    }

    private static function getModelName($sName){

        if(!isset(static::$aModelLink[$sName])){

            $indCodes = static::getIndcodeId($sName);
            //static::$aModelLink[$sName] = [];

            if (!empty($indCodes)) {

                foreach ($indCodes as $indCode) {

                    if (!empty($indCode) && $indCode > 0) {

                        $aMSelect = Array(
                            "ID",
                            "NAME",
                        );

                        $aMFilter = Array(
                            "IBLOCK_ID" => 17,
                            "PROPERTY_SIMPLEREPLACE_INDCODE" => $indCode,
                            "ACTIVE" => "Y",
                        );

                        $rModels = impelCIBlockElement::GetList(
                            Array(
                                'ID' => 'ASC'
                            ),
                            $aMFilter,
                            false,
                            false,
                            $aMSelect);

                        if($rModels){

                            while($aModels = $rModels->GetNext()){

                                if(isset($aModels['ID'])
                                    && !empty($aModels['ID'])){

                                    static::$aModelLink[$sName][$aModels['ID']] = $aModels['ID'];

                                }

                            }

                        }

                    }

                }

            }

        }

    }

    private static function getModel($modelId,&$auModels)
    {
        $aMSelect = Array(
            "ID",
            "NAME",
            "CODE",
            "PROPERTY_SIMPLEREPLACE_PRODUCTS",
            "PROPERTY_SIMPLEREPLACE_POSITION",
            "PROPERTY_SIMPLEREPLACE_VIEW",
            "PROPERTY_SIMPLEREPLACE_INDCODE",
            "PROPERTY_MANUFACTURER",
            "PROPERTY_TYPE_OF_PRODUCT"
        );

        //MANUFACTURER
        //TYPE_OF_PRODUCT
        //codes?

        $aMFilter = Array(
            "IBLOCK_ID" => 17,
            "ID" => $modelId,
            "ACTIVE" => "Y",
        );

        if (!empty(static::$aFilter)) {
            $aMFilter = array_merge($aMFilter,static::$aFilter);
        }

        $rModels = impelCIBlockElement::GetList(
            Array(
                'ID' => 'ASC'
            ),
            $aMFilter,
            false,
            false,
            $aMSelect);

        if($rModels){

            while ($aModels = $rModels->GetNext()) {
                $auModels['VALUES'][$modelId][$aModels['ID']] = $aModels;
                static::$aCodes[$aModels['ID']] = trim($aModels['CODE']);
            }

        }


    }


    private static function mergeModeles($aModels) {

        $indCodes = ['hash' => [],'intersect' => [],'id' => []];

        foreach ($aModels as $sProp => $aValues) {

            if ($sProp == 'PROPERTY_SIMPLEREPLACE_INDCODE_VALUE' && !empty($aValues)) {

                foreach ($aValues as $sKey => $sValue) {

                    $sHash = $aModels["PROPERTY_SIMPLEREPLACE_PRODUCTS_VALUE"][$sKey].';'.$aModels["PROPERTY_SIMPLEREPLACE_POSITION_VALUE"][$sKey].';'.$aModels["PROPERTY_SIMPLEREPLACE_VIEW_VALUE"][$sKey].';'.$aModels["PROPERTY_SIMPLEREPLACE_INDCODE_VALUE"][$sKey];
                    $indCodes['hash'][$sHash] = $sHash;
                    $indCodes['id'][$aModels["PROPERTY_SIMPLEREPLACE_INDCODE_VALUE"][$sKey]] = $aModels["PROPERTY_SIMPLEREPLACE_INDCODE_VALUE"][$sKey];

                }

            }

        }

        if (isset($indCodes['id'])) {
            $indCodes['intersect'] = array_filter($indCodes['id'],function($strval){
                $adefaults = static::getDefaults();
                return $strval == $adefaults['indcode_id'] ? false : true;
            });
        }

        return $indCodes;

    }

    private static function checkDoubles($aCheck) {

        $oiElt = new CIBlockElement;

        $aDoubles = array(
            'ORIGINAL' => array(),
            'REDIRECT' => array()
        );

        foreach($aCheck['VALUES'] as $sNameId => $aProps){

            $aHash = array();

            foreach($aProps as $imId => $aProds) {

                if(!empty(static::$skip_manufacturer)
                    && isset($aProds["PROPERTY_MANUFACTURER_VALUE"])
                    && in_array(mb_strtolower($aProds["PROPERTY_MANUFACTURER_VALUE"]),static::$skip_manufacturer)) {
                    continue;
                }

                $indCodes = static::mergeModeles($aProds);
                $bIsMain = false;

                if (empty($aDoubles['ORIGINAL'])) {

                    if (!empty($indCodes['id'])) {

                        if(isset($aProds["PROPERTY_TYPE_OF_PRODUCT_VALUE"])
                            && !empty(static::$type_of_product)
                            && $aProds["PROPERTY_TYPE_OF_PRODUCT_VALUE"] == static::$type_of_product) {

                            $bIsMain = true;

                        }

                        if(isset($aProds["PROPERTY_MANUFACTURER_VALUE"])
                            && !empty(static::$manufacturer_of_product)
                            && $aProds["PROPERTY_MANUFACTURER_VALUE"] == static::$manufacturer_of_product) {

                            $bIsMain = true;

                        }

                        if ($bIsMain) {

                            $aDoubles['ORIGINAL']['ID'] = $imId;
                            $aDoubles['ORIGINAL']['CODE'] = static::$aCodes[$imId];

                            $aDoubles['ORIGINAL']['INDCODE'] = $indCodes;

                            fputcsv(
                                static::$rFp,
                                array(
                                    $sNameId,
                                    $imId,
                                    $aProds['NAME'],
                                    ((CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$imId.'&lang=ru&find_section_section=0&WF=Y'),
                                    ''
                                ), ';');


                        }

                    }

                }

                if (!$bIsMain) {

                    if((!empty(static::$type_of_double)
                            && isset($aProds["PROPERTY_TYPE_OF_PRODUCT_VALUE"])
                            && $aProds["PROPERTY_TYPE_OF_PRODUCT_VALUE"] == static::$type_of_double)
                        || (!empty(static::$manufacturer_of_double)
                            && isset($aProds["PROPERTY_MANUFACTURER_VALUE"])
                            && $aProds["PROPERTY_MANUFACTURER_VALUE"] == static::$manufacturer_of_double)
                        || static::$not_check
                    ) {

                        $aDoubles['REDIRECT']['CODE'][$imId] = static::$aCodes[$imId];
                        $aDoubles['REDIRECT']['INDCODE'][$imId] = $indCodes['hash'];
                        $aDoubles['REDIRECT']['intersect'][$imId] = $indCodes['intersect'];

                    }

                }

            }

            if(!empty($aDoubles['ORIGINAL'])
                && !empty($aDoubles['REDIRECT'])
            ){

                $aHash = array_merge($aHash,$aDoubles['ORIGINAL']['INDCODE']['hash']);

                foreach ($aDoubles['REDIRECT']['CODE'] as $iDoubles => $sRedirect) {

                    if (((static::$check_intersect
                                && !empty($aDoubles['REDIRECT']['intersect'][$iDoubles])
                                && array_intersect($aDoubles['ORIGINAL']['INDCODE']['intersect'],$aDoubles['REDIRECT']['intersect'][$iDoubles]))
                            || !static::$check_intersect)
                        && ((static::$check_empty
                                && !empty($aDoubles['REDIRECT']['INDCODE'][$iDoubles]))
                            || !static::$check_empty)
                    ) {

                        $aHash = array_merge($aHash,$aDoubles['REDIRECT']['INDCODE'][$iDoubles]);

                        fputcsv(static::$rFp,[$aDoubles['ORIGINAL']['ID'], $iDoubles, 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&lang=ru&ID='.$aDoubles['ORIGINAL']['ID'].'&find_section_section=0&WF=Y','https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&lang=ru&ID='.$iDoubles.'&find_section_section=0&WF=Y'],';');
                        fputcsv(static::$rFp,[$aDoubles['ORIGINAL']['ID'], $iDoubles, 'https://youtwig.ru/model/'.$sRedirect.'/','https://youtwig.ru/model/'.$aDoubles['ORIGINAL']['CODE'].'/'],';');

                        static::addRedirect(
                            '/model/'.$sRedirect.'/',
                            '/model/'.$aDoubles['ORIGINAL']['CODE'].'/'
                        );

                        $oiElt->Update($iDoubles,Array('ACTIVE' => 'N', 'TIMESTAMP_X' => true));

                        if (static::$set_type_of_product) {

                            $acType = [];

                            $type_of_product_id = static::get_param(static::$set_type_of_product);

                            if ($type_of_product_id) {
                                $acType['type_of_product'] = Array("VALUE" => $type_of_product_id);
                                impelCIBlockElement::SetPropertyValuesEx($aDoubles['ORIGINAL']['ID'], 17, $acType);
                            }
                        }

                        if (static::$set_manufacturer) {

                            $acType = [];
                            $manufacturer_id = static::get_param(static::$set_manufacturer,'MANUFACTURER');

                            if ($manufacturer_id) {
                                $acType['manufacturer'] = Array("VALUE" => $manufacturer_id);
                                impelCIBlockElement::SetPropertyValuesEx($aDoubles['ORIGINAL']['ID'], 17, $acType);
                            }
                        }

                        echo 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&lang=ru&ID='.$aDoubles['ORIGINAL']['ID'].'&find_section_section=0&WF=Y - основной<br />';
                        echo 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&lang=ru&ID='.$iDoubles.'&find_section_section=0&WF=Y - дубль<br />';

                    }
                }

                if (!empty($aHash)) {
                    static::setMergeProps($aDoubles['ORIGINAL']['ID'],$aHash);
                }

                if (!empty(static::$test)) {
                    die();
                }


            } else if (!empty($aDoubles['REDIRECT']) && (static::$set_type_of_product  || static::$set_manufacturer)) {

                foreach ($aDoubles['REDIRECT']['CODE'] as $iDoubles => $sRedirect) {

                    fputcsv(static::$rdFp,[$aDoubles['ORIGINAL']['ID'], $iDoubles, 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&lang=ru&ID='.$aDoubles['ORIGINAL']['ID'].'&find_section_section=0&WF=Y','https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&lang=ru&ID='.$iDoubles.'&find_section_section=0&WF=Y'],';');
                    fputcsv(static::$rdFp,[$aDoubles['ORIGINAL']['ID'], $iDoubles, 'https://youtwig.ru/model/'.$sRedirect.'/','https://youtwig.ru/model/'.$aDoubles['ORIGINAL']['CODE'].'/'],';');

                    if (static::$set_type_of_product) {

                        $acType = [];

                        $type_of_product_id = static::get_param(static::$set_type_of_product);

                        if ($type_of_product_id) {
                            $acType['type_of_product'] = Array("VALUE" => $type_of_product_id);
                            impelCIBlockElement::SetPropertyValuesEx($iDoubles, 17, $acType);
                        }
                    }

                    if (static::$set_manufacturer) {

                        $acType = [];

                        $manufacturer_id = static::get_param(static::$set_manufacturer,'MANUFACTURER');

                        if ($manufacturer_id) {
                            $acType['manufacturer'] = Array("VALUE" => $manufacturer_id);
                            impelCIBlockElement::SetPropertyValuesEx($iDoubles, 17, $acType);

                        }
                    }



                    $oiElt->Update($iDoubles,Array('TIMESTAMP_X' => true));

                    echo 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&lang=ru&ID='.$iDoubles.'&find_section_section=0&WF=Y - замена<br />';

                }

                if (!empty(static::$test)) {
                    die();
                }

            }

        }


    }

    private static function get_param($type_of_product, $PROPERTY_CODE = 'type_of_product') {

        $typeProperties = CIBlockProperty::GetList(
            Array(
                "sort"=>"asc",
                "name"=>"asc"
            ),
            Array(
                "ACTIVE" => "Y",
                "IBLOCK_ID" => 17,
                "CODE" => $PROPERTY_CODE
            )
        );

        $typePropID = 0;

        if($typeProperties){

            while ($typeFields = $typeProperties->GetNext()){

                $typePropertyID = $typeFields["ID"];
                $enumTypeNew = new CIBlockPropertyEnum;

                $typePropertyDB = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID" => 17, "VALUE" => $type_of_product, "CODE" => $PROPERTY_CODE));

                if($typePropertyDB){
                    while($typePropertyFields = $typePropertyDB->GetNext()){

                        if(isset($typePropertyFields["ID"])){
                            $typePropID = $typePropertyFields["ID"];
                        }

                    }
                }


            }

        }

        return $typePropID;

    }

    private static function setMergeProps ($id,$props) {

        if (static::$product_merge) {
            $aProps = [];

            foreach ($props as $str) {
                $arr = explode(';',$str);
                $aProps['SIMPLEREPLACE_PRODUCTS'][] = $arr[0];
                $aProps['SIMPLEREPLACE_POSITION'][] = $arr[1];
                $aProps['SIMPLEREPLACE_VIEW'][] = $arr[2];
                $aProps['SIMPLEREPLACE_INDCODE'][] = $arr[3];
            }

            impelCIBlockElement::SetPropertyValuesEx($id, 17, $aProps);
            //\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $id);

        }

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

        global $argv;

        if (!(isset($argv) && !empty($argv))) {

            file_put_contents(WORKING_DIR.'impel_getdoublesu_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/merge_doubles_indcode_files.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head><body><h1>'.time().'</h1></body></html>');

        }

    }

}

if(CModule::IncludeModule("iblock")) {
    impelGetDoublesEmpty::getList($aFilter);
}