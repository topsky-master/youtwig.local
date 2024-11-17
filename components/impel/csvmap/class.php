<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED  !== true)
    die();

require $_SERVER['DOCUMENT_ROOT'].'/local/components/impel/csvmap/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use \Bitrix\Main\Localization\Loc;

/* $skip = isset($_REQUEST['skip']) && !empty($_REQUEST['skip']) ? (int)$_REQUEST['skip'] : 0;
if(file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/result'.$file)
    && filesize(dirname(dirname(__DIR__)).'/bitrix/tmp/result'.$file)){

    $skip = (int)trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/result'.$file));

}*/
//тип продукта;производитель;модель;товар;ком код;инд код;вид код;вид поз;вид изображение;
//text/plain
//application/vnd.ms-excel
//text/x-csv
//application/csv
//application/x-csv
//text/csv
//text/comma-separated-values
//text/x-comma-separated-values
//text/tab-separated-values

class impelReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    private $irSkipNumber = 1;
    private $irmSkipNumber = 0;


    public function setrSkipNumber($irSkipNumber = 1, $irmSkipNumber = 0){

        $this->irSkipNumber = (int)$irSkipNumber;
        $this->irmSkipNumber = (int)$irmSkipNumber;

    }

    public function readCell($column, $row, $worksheetName = '') {

        if ($row < $this->irSkipNumber) {
            return false;
        }

        if(!empty($this->irmSkipNumber) && $this->irmSkipNumber < $row){
            return false;
        }

        return true;
    }
}

class ImpelCsvmapComponent extends CBitrixComponent
{

    private $aValidext = array("csv","xls","xlsx","zip","ods");
    private $aEncodings = array('UTF-8','WINDOWS-1251','CP866','ISO-8859-1');
    private $sDir = '';
    private $processDir = 'process/';
    private $processFile = 'process.txt';
    private $logDir = 'log/';
    private $logFile = 'log.txt';
    private $doneDir = 'done/';
    private $messagesDir = 'messages/';
    private $messagesFile = 'messages.txt';


    private $spLines = 5;
    private $ioBlockId = 40;
    private $arColumns = array('type_of_product','manufacturer','model','product');
    private $iParts = 4;
    private $ipBlockId = 11;
    private $imBlockId = 17;
    private $imnBlockId = 27;

    private $aCsvHeaders = array(
        'type_of_product',
        'manufacturer',
        'model',
        'product',
        'skip',
        'indcode',
        'skip1',
        'position',
        'view'
    );

    //тип продукта;производитель;модель;товар;;инд код;вид код;вид поз;вид изображение;

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    private function getListOfProfiles()
    {

        $aSelect = Array("NAME");
        $aFilter = Array("IBLOCK_ID" => $this->ioBlockId, "ACTIVE" => "Y");

        $dRes = CIBlockElement::GetList(Array(), $aFilter, false, false, $aSelect);

        $this->arResult['saved_profiles'] = array();

        if($dRes){
            while($aRes = $dRes->GetNext()){
                $this->arResult['saved_profiles'][$aRes['NAME']] = $aRes['NAME'];
            }
        }

    }

    private function checkUploads(){

        global $APPLICATION;

        if(isset($_FILES['models'])
            && !empty($_FILES['models'])
        ){

            $aErrors = array();

            foreach($_FILES['models']['name'] as $num => $value){

                if(!$_FILES['models']['error'][$num]
                    && $_FILES['models']['size'][$num] > 0){

                    $sfExtension = pathinfo($_FILES['models']['name'][$num], PATHINFO_EXTENSION);
                    $sfExtension = mb_strtolower($sfExtension);

                    if(in_array($sfExtension,$this->aValidext)){

                        $sPath = $this->sDir.$_FILES['models']['name'][$num];

                        if(move_uploaded_file($_FILES['models']['tmp_name'][$num],$sPath)){

                            if($sfExtension == 'zip'){

                                $this->unzip($sPath);

                            }

                        } else {

                            $aErrors[] = sprintf(GetMessage('TMPL_MOVE_ERROR'),$_FILES['models']['name'][$num]);

                        }

                    } else {

                        $aErrors[] = sprintf(GetMessage('TMPL_NOT_VALID_EXTENSION'),$sfExtension,$_FILES['models']['name'][$num],join($this->aValidext,','));

                    }

                } else {

                    if($_FILES['models']['error'][$num]){
                        $aErrors[] = sprintf(GetMessage('TMPL_FILE_UPLOAD_ERROR'),$_FILES['models']['error'][$num],$_FILES['models']['name'][$num]);
                    }

                    if($_FILES['models']['size'][$num] == 0){
                        $aErrors[] = sprintf(GetMessage('TMPL_FILE_SIZE_ZIRO'),$_FILES['models']['name'][$num]);
                    }

                }

            }

            $this->getFilesList($aErrors);
            $APPLICATION->RestartBuffer();
            $this->includeComponentTemplate('filelist');

            exit;

        }

    }

    private function getFilesList($aErrors = array()){

        $aFlists = glob($this->sDir."*");
        $aPaths = array();

        foreach ($aFlists as $sFilePath) {

            $sfExtension = pathinfo($sFilePath, PATHINFO_EXTENSION);
            $sfExtension = mb_strtolower($sfExtension);

            if(is_file($sFilePath) && mb_stripos($sFilePath,'_preview.') === false){

                if(in_array($sfExtension,$this->aValidext)) {
                    $aPaths[] = str_ireplace($_SERVER['DOCUMENT_ROOT'],'',$sFilePath);
                } else {
                    unlink($sFilePath);
                }

            }

        }

        $this->arResult['paths'] = $aPaths;
        $this->arResult['aerrors'] = $aErrors;

    }

    private function unzip($sFilePathArc){

        $sFilePathDst = $this->sDir;

        $arUnpackOptions = Array(
            "REMOVE_PATH" => $_SERVER["DOCUMENT_ROOT"],
            "UNPACK_REPLACE" => false
        );

        $rArchiver = CBXArchive::GetArchive($sFilePathArc);
        $rArchiver->SetOptions($arUnpackOptions);
        $uRes = $rArchiver->Unpack($sFilePathDst);

        unlink($sFilePathArc);

        if (!$uRes) {
            return $rArchiver->GetErrors();
        } else {
            return true;
        }
    }

    private function checkDirExists(){

        $this->sDir = rtrim($_SERVER['DOCUMENT_ROOT'],'/').'/bitrix/tmp/models/';

        if(!file_exists($this->sDir)){
            mkdir($this->sDir,true);
        }

        if(!file_exists($this->sDir.$this->doneDir)){
            mkdir($this->sDir.$this->doneDir,true);
        }

        if(!file_exists($this->sDir.$this->processDir)){
            mkdir($this->sDir.$this->processDir,true);
        }

        if(!file_exists($this->sDir.$this->messagesDir)){
            mkdir($this->sDir.$this->messagesDir,true);
        }

        if(!file_exists($this->sDir.$this->logDir)){
            mkdir($this->sDir.$this->logDir,true);
        }

        if(!is_writable($this->sDir)){
            chmod($this->sDir,0775);
        }

        if(!is_writable($this->sDir.$this->doneDir)){
            chmod($this->sDir.$this->doneDir,0775);
        }

        if(!is_writable($this->sDir.$this->processDir)){
            chmod($this->sDir.$this->processDir,0775);
        }

        if(!is_writable($this->sDir.$this->messagesDir)){
            chmod($this->sDir.$this->messagesDir,0775);
        }

        if(!is_writable($this->sDir.$this->logDir)){
            chmod($this->sDir.$this->logDir,0775);
        }

        if(!is_writable($this->sDir)){
            chmod($this->sDir,0777);
        }

        if(!is_writable($this->sDir.$this->doneDir)){
            chmod($this->sDir.$this->doneDir,0777);
        }

        if(!is_writable($this->sDir.$this->processDir)){
            chmod($this->sDir.$this->processDir,0777);
        }

        if(!is_writable($this->sDir.$this->messagesDir)){
            chmod($this->sDir.$this->messagesDir,0777);
        }

        if(!is_writable($this->sDir.$this->logDir)){
            chmod($this->sDir.$this->logDir,0777);
        }

    }

    private function getRigts(){
        global $argc;
        return checkQuantityRigths() || (isset($argc) && !empty($argc));
    }

    private function get_enum_list($code) {

        static $enum_list;

        if (!is_array($enum_list)) {
            $enum_list = [];
        }

        if (!is_array($enum_list[$code])) {
            $enum_list[$code] = [];
        }

        $aList = array();

        if (empty($enum_list[$code])) {

            $rEnum = CIBlockPropertyEnum::GetList(
                Array(
                    "DEF" => "DESC",
                    "SORT" => "ASC"),
                Array(
                    "IBLOCK_ID" => $this->imBlockId,
                    "CODE" => $code)
            );

            if($rEnum){
                while($aFields = $rEnum->GetNext()) {

                    $aList[$aFields["VALUE"]] = $aFields["VALUE"];

                }
            }

            $enum_list[$code] = $aList;

        } else {

            $aList = $enum_list[$code];

        }

        return $aList;
    }

    private function checkEncoding($encoding){

        $encoding = trim(mb_strtoupper($encoding));

        return in_array($encoding,$this->aEncodings) ? $encoding : 'UTF-8';

    }

    private function setDefaultSettings(){

        global $APPLICATION;

        $this->getListOfProfiles();

        $this->arResult['manufacturer'] = isset($_REQUEST['manufacturer']) ? trim($_REQUEST['manufacturer']) : '';
        $this->arResult['type_of_product'] = isset($_REQUEST['type_of_product']) ? trim($_REQUEST['type_of_product']) : '';

        $this->arResult['product'] = isset($_REQUEST['product']) ? trim($_REQUEST['product']) : '';
        $this->arResult['encoding'] = isset($_REQUEST['encoding'])  ? trim($_REQUEST['encoding']) : 'UTF-8';
        $this->arResult['encoding'] = $this->checkEncoding($this->arResult['encoding']);

        $this->arResult['path'] = isset($_REQUEST['path']) ? htmlspecialchars_decode(trim($_REQUEST['path']),ENT_QUOTES) : ';';
        $this->arResult['delimiter'] = isset($_REQUEST['delimiter']) ? htmlspecialchars_decode(trim($_REQUEST['delimiter']),ENT_QUOTES) : ';';
        $this->arResult['check_manufacturer'] = isset($_REQUEST['check_manufacturer']) && !empty($_REQUEST['check_manufacturer']) ? true : false;


        $this->arResult['order_columns'] = isset($_REQUEST['order_columns']) ? ($_REQUEST['order_columns']) : '';
        $this->arResult['skip_strings'] = isset($_REQUEST['skip_strings']) ? (int)trim($_REQUEST['skip_strings']) : 0;

        $this->arResult['preview'] = isset($_REQUEST['preview']) ? (bool)trim($_REQUEST['preview']) : false;

        $this->arResult['encoding_list'] = $this->aEncodings;
        $this->arResult['manufacturer_list'] = $this->get_enum_list('manufacturer');
        $this->arResult['type_of_product_list'] = $this->get_enum_list('type_of_product');

        $this->arResult['path'] = trim($_REQUEST['path']);

    }

    private function checkSettings($bLoadPreview = false){

        if(isset($_REQUEST['path'])
            && !empty($_REQUEST['path'])
            && file_exists($_SERVER['DOCUMENT_ROOT'].$_REQUEST['path'])
        ) {

            global $APPLICATION;

            $this->arResult['load_preview'] = $bLoadPreview;
            $APPLICATION->RestartBuffer();

            $this->setDefaultSettings();
            $this->includeComponentTemplate('settings');

            exit;
        }

    }

    private function checkForErrors($bcAll = false){

        global $APPLICATION;

        $aErrors = array();

        $APPLICATION->RestartBuffer();

        $this->spLines = ($bcAll == false) ? $this->spLines : 0;

        $this->setDefaultSettings();

        $this->arResult['encoding'] = isset($_REQUEST['encoding'])  ? trim($_REQUEST['encoding']) : 'UTF-8';
        $this->arResult['encoding'] = $this->checkEncoding($this->arResult['encoding']);

        $sdata = file_get_contents($_SERVER['DOCUMENT_ROOT'].$_REQUEST['path']);

        if($this->arResult['encoding'] != 'UTF-8'){
            $sdata = iconv($this->arResult['encoding'], 'UTF-8//IGNORE', $sdata);
        }

        $apInfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$_REQUEST['path']);
        $apInfo['extension'] = mb_strtolower($apInfo['extension']);

        $snName = $this->sDir.$apInfo['filename'].'_preview.'.$apInfo['extension'];

        file_put_contents($snName,$sdata);

        $this->arResult['skip_strings'] = isset($_REQUEST['skip_strings']) ? (int)trim($_REQUEST['skip_strings']) : 0;

        $aColumnsNum = array();
        $aSheetData = $this->csvExtract($snName,$aErrors,$aColumnsNum,$this->arResult['skip_strings'],$this->spLines);

        $this->arResult['aerrors'] = array_merge($this->arResult['aerrors'],$aErrors);
        $this->arResult['aSheetData'] = $aSheetData;
        $this->arResult['aColumnsNum'] = $aColumnsNum;
        $this->arResult['aColumnsList'] = $this->getCsvColums();

        if(isset($_REQUEST['order_columns'])){
            $this->arResult['order_columns'] = $_REQUEST['order_columns'];
        } else if(isset($_REQUEST['json_order_columns'])){
            $this->arResult['order_columns'] = json_decode((html_entity_decode($_REQUEST['json_order_columns'],ENT_QUOTES)),true);
        }

        if($bcAll){

            $this->checkColumns();
            $this->checkProudcts();

        }

    }

    private function checkPreview(){

        if(isset($_REQUEST['path'])
            && !empty($_REQUEST['path'])
            && file_exists($_SERVER['DOCUMENT_ROOT'].$_REQUEST['path'])
        ) {

            $this->checkForErrors();
            $this->includeComponentTemplate('preview');

            exit;
        }

    }

    private function getCsvColums(){

        return array(
            GetMessage("TMP_CHOOSE_COLUMN") => '',
            GetMessage("TMP_TYPE_OF_PRODUCT_COLUMN") => 'type_of_product',
            GetMessage("TMP_MANUFACTURER_COLUMN") => 'manufacturer',
            GetMessage("TMP_MODEL_COLUMN") => 'model',
            GetMessage("TMP_PRODUCT_COLUMN") => 'product',
            GetMessage("TMP_INDCODE_COLUMN") => 'indcode',
            GetMessage("TMP_VIEW_COLUMN") => 'view',
            GetMessage("TMP_POSITION_COLUMN") => 'position',
        );

    }

    private function makeReplaces($aSheetRow){

        static $aReplaces;

        $aSheetRow = array_map('trim', $aSheetRow);

        if(!is_array($aReplaces)) {
            $aReplaces = $this->getReplaces();
        }

        if(!empty($aReplaces)){
            foreach($aSheetRow as $shKey => $shValue){
                foreach($aReplaces['from'] as $itKey => $stValue){
                    if(!empty($stValue)
                        && isset($aReplaces['to'][$itKey])){

                        $sFrom = trim($stValue);
                        $sTo = trim($aReplaces['to'][$itKey]);

                        if(mb_stripos($shValue,$sFrom) !== false){
                            $aSheetRow[$shKey] = str_ireplace($sFrom,$sTo,$aSheetRow[$shKey]);
                        } else if(mb_stripos($sFrom,'~') === 0
                            && mb_stripos($sFrom,'~',1) !== false){
                            $aSheetRow[$shKey] = preg_replace($sFrom,$sTo,$aSheetRow[$shKey]);
                        }

                    }
                }
            }
        }

        return $aSheetRow;

    }

    private function csvExtract($sFilePath, &$aErrors, &$aColumnsNum, $iSkipStrings = 0, $iMaxLines = 0){

        $apInfo = pathinfo($sFilePath);
        $apInfo['extension'] = mb_strtolower($apInfo['extension']);

        switch ($apInfo['extension']){
            case 'csv':

                $oReader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();

                break;
            case 'xls':

                $oReader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();

                break;
            case 'xlsx':

                $oReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

                break;
            case 'ods':

                $oReader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();

                break;
        }

        $oReader->setDelimiter($_REQUEST['delimiter']);

        $aSheetData = array();

        if($oReader){

            try {

                $oFilterSubset = new impelReadFilter();
                $oFilterSubset->setrSkipNumber($iSkipStrings,$iMaxLines);
                $oReader->setReadFilter($oFilterSubset);
                $oSpreadsheet = $oReader->load($sFilePath);

                if($oSpreadsheet
                    && is_object($oSpreadsheet)) {

                    $aSheetData = $oSpreadsheet->getActiveSheet()->toArray(null, true, true, true);

                    if (is_array($aSheetData)
                        && sizeof($aSheetData)) {

                        foreach ($aSheetData as $iNum => $aSheetRow) {

                            if (is_array($aSheetRow)) {

                                $aSheetRow = array_filter($aSheetRow);

                                if (empty($aSheetRow)) {
                                    unset($aSheetData[$iNum]);
                                } else {

                                    if(empty($aColumnsNum)){
                                        foreach($aSheetRow as $sKey => $sVal){
                                            $aColumnsNum[] = $sKey;
                                        }
                                    }

                                    $aSheetData[$iNum] = $this->makeReplaces($aSheetRow);

                                }

                            }

                        }


                    }

                }


            } catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                $aErrors[] = $e->getMessage();
            }

        }

        return $aSheetData;

    }

    private function checkDelete(){

        global $APPLICATION;

        if(isset($_REQUEST['path'])
            && !empty($_REQUEST['path'])
            && file_exists($_SERVER['DOCUMENT_ROOT'].$_REQUEST['path'])
        ) {

            $APPLICATION->RestartBuffer();
            @unlink($_SERVER['DOCUMENT_ROOT'].trim($_REQUEST['path']));
            echo 'deleted';
            exit;
        }

    }

    private function getProfile($spName)
    {
        global $USER;

        $spName = trim($spName);
        $iProfile = 0;

        $aSelect = Array("ID");
        $aFilter = Array(
            "IBLOCK_ID" => $this->ioBlockId,
            "ACTIVE" => "Y",
            "NAME" => $spName);

        $dRes = CIBlockElement::GetList(Array(), $aFilter, false, false, $aSelect);

        if($dRes && $aRes = $dRes->GetNext()){
            $iProfile = (int)$aRes['ID'];
        }

        $oElt = new CIBlockElement;

        if(!$iProfile){

            $aPforile = Array(
                "MODIFIED_BY" => $USER->GetID(),
                "IBLOCK_SECTION_ID" => false,
                "IBLOCK_ID" => $this->ioBlockId,
                "NAME" => $spName,
                "ACTIVE" => "Y",
                "PREVIEW_TEXT" => " ",
                "DETAIL_TEXT" => " "
            );

            $iProfile = $oElt->Add($aPforile);
        }

        if($iProfile) {

            $toBaseProducts = array(
                'order_columns' => false,
                'delimiter' => false,
                'encoding' => false,
                'products' => false,
                'manufacturer' => false,
                'skip_strings' => false,
                'type_of_product' => false,
                'check_manufacturer' => false,
            );

            if(isset($_REQUEST['order_columns'])
                && !empty($_REQUEST['order_columns'])){

                $_REQUEST['order_columns'] = json_encode(array_map('trim',$_REQUEST['order_columns']),JSON_UNESCAPED_UNICODE);
                $toBaseProducts['order_columns'] = $_REQUEST['order_columns'];

            }

            $toBaseProducts['delimiter'] = isset($_REQUEST['delimiter']) ? htmlspecialchars_decode(trim($_REQUEST['delimiter']),ENT_QUOTES) : ';';
            $toBaseProducts['check_manufacturer'] = isset($_REQUEST['check_manufacturer']) && !empty($_REQUEST['check_manufacturer']) ? true : false;

            $toBaseProducts['encoding'] = isset($_REQUEST['encoding'])  ? trim($_REQUEST['encoding']) : 'UTF-8';
            $toBaseProducts['skip_strings'] = isset($_REQUEST['skip_strings'])  ? trim($_REQUEST['skip_strings']) : '';

            $toBaseProducts['encoding'] = $this->checkEncoding($toBaseProducts['encoding']);

            $toBaseProducts['products'] = isset($_REQUEST['product']) ? trim($_REQUEST['product']) : '';

            $toBaseProducts['manufacturer'] = isset($_REQUEST['manufacturer']) ? trim($_REQUEST['manufacturer']) : '';
            $toBaseProducts['type_of_product'] = isset($_REQUEST['type_of_product']) ? trim($_REQUEST['type_of_product']) : '';

            impelCIBlockElement::SetPropertyValuesEx($iProfile, $this->ioBlockId, $toBaseProducts);

            if ($oElt->Update($iProfile, Array('TIMESTAMP_X' => true))) {
                $this->arResult['amessages'][] = GetMessage('TMPL_SETTINGS_SAVED');
                $this->arResult['SAVED_PROFILE'] = $spName;

            }

        }

    }

    private function loadProfile(){

        if(isset($_REQUEST['profile_load'])
            && !empty($_REQUEST['profile_load'])
        ){

            $sProfileSave = trim($_REQUEST['profile_load']);

            $aSelect = Array(
                "ID",
                "IBLOCK_ID",
                "NAME",
                "PROPERTY_manufacturer",
                "PROPERTY_type_of_product",
                "PROPERTY_products",
                "PROPERTY_encoding",
                "PROPERTY_delimiter",
                "PROPERTY_order_columns",
                "PROPERTY_skip_strings",
                "PROPERTY_preview"
            );

            $aFilter = Array(
                "IBLOCK_ID" => $this->ioBlockId,
                "ACTIVE" => "Y",
                "NAME" => $sProfileSave);

            $dRes = CIBlockElement::GetList(Array(), $aFilter, false, false, $aSelect);

            if($dRes){

                while($aRes = $dRes->GetNext()){

                    $_REQUEST['manufacturer'] = isset($aRes['PROPERTY_MANUFACTURER_VALUE']) ? trim($aRes['PROPERTY_MANUFACTURER_VALUE']) : '';
                    $_REQUEST['type_of_product'] = isset($aRes['PROPERTY_TYPE_OF_PRODUCT_VALUE']) ? trim($aRes['PROPERTY_TYPE_OF_PRODUCT_VALUE']) : '';

                    $_REQUEST['product'] = isset($aRes['PROPERTY_PRODUCTS_VALUE']) ? trim($aRes['PROPERTY_PRODUCTS_VALUE']) : '';
                    $_REQUEST['encoding'] = isset($aRes['PROPERTY_ENCODING_VALUE']) ? trim($aRes['PROPERTY_ENCODING_VALUE']) : '';
                    $_REQUEST['delimiter'] = isset($aRes['PROPERTY_DELIMITER_VALUE']) ? trim($aRes['PROPERTY_DELIMITER_VALUE']) : ';';
                    $_REQUEST['check_manufacturer'] = isset($aRes['PROPERTY_CHECK_MANUFACTURER_VALUE']) && !empty($aRes['PROPERTY_CHECK_MANUFACTURER_VALUE']) ? true : false;

                    $_REQUEST['order_columns'] = isset($aRes['PROPERTY_ORDER_COLUMNS_VALUE']) ? $aRes['PROPERTY_ORDER_COLUMNS_VALUE'] : '';
                    $_REQUEST['skip_strings'] = isset($aRes['PROPERTY_SKIP_STRINGS_VALUE']) ? trim($aRes['PROPERTY_SKIP_STRINGS_VALUE']) : '';
                    $this->arResult['amessages'][] = GetMessage('TMPL_SETTINGS_LOADED');
                    $this->arResult['SAVED_PROFILE'] = $sProfileSave;

                }

            }


        }

        $this->checkSettings(true);

    }

    private function saveReplaces(){

        global $APPLICATION;

        $sReplaces = $_REQUEST['replaces'];
        $sReplaces = json_encode($sReplaces,JSON_UNESCAPED_UNICODE);

        $aSelect = Array("ID");
        $aFilter = Array("IBLOCK_ID" => $this->ioBlockId, "ACTIVE" => "N");

        $iElt = 0;
        $dRes = CIBlockElement::GetList(Array(), $aFilter, false, false, $aSelect);

        $this->arResult['replaces'] = array();

        if($dRes && $aRes = $dRes->GetNext()){
            $iElt = (int)$aRes['ID'];
        }

        if($iElt > 0){

            $oElt = new CIBlockElement;
            $oElt->Update($iElt, array('DETAIL_TEXT' => $sReplaces));

        }

        $this->arResult['aerrors'][] = GetMessage('TMPL_SAVE_REPLACES');

        $APPLICATION->RestartBuffer();
        $this->getReplaces();
        $this->includeComponentTemplate('replaces');
        exit;

    }

    private function saveProfile(){

        if(isset($_REQUEST['profile_save'])
            && !empty($_REQUEST['profile_save'])){

            $this->getProfile($_REQUEST['profile_save']);

        }

        $this->arResult['manufacturer'] = isset($_REQUEST['manufacturer']) ? trim($_REQUEST['manufacturer']) : '';
        $this->arResult['type_of_product'] = isset($_REQUEST['type_of_product']) ? trim($_REQUEST['type_of_product']) : '';
        $this->arResult['product'] = isset($_REQUEST['product']) ? trim($_REQUEST['product']) : '';

        $this->arResult['encoding'] = isset($_REQUEST['encoding'])  ? trim($_REQUEST['encoding']) : 'UTF-8';
        $this->arResult['encoding'] = $this->checkEncoding($this->arResult['encoding']);

        $this->arResult['path'] = isset($_REQUEST['path']) ? htmlspecialchars_decode(trim($_REQUEST['path']),ENT_QUOTES) : ';';
        $this->arResult['delimiter'] = isset($_REQUEST['delimiter']) ? htmlspecialchars_decode(trim($_REQUEST['delimiter']),ENT_QUOTES) : ';';
        $this->arResult['check_manufacturer'] = isset($_REQUEST['check_manufacturer']) && !empty($_REQUEST['check_manufacturer']) ? true : false;


        $this->arResult['order_columns'] = isset($_REQUEST['order_columns']) ? ($_REQUEST['order_columns']) : '';
        $this->arResult['skip_strings'] = isset($_REQUEST['skip_strings']) ? (int)trim($_REQUEST['skip_strings']) : 0;

        $this->arResult['preview'] = isset($_REQUEST['preview']) ? (bool)trim($_REQUEST['preview']) : false;

        $this->arResult['encoding_list'] = $this->aEncodings;
        $this->arResult['manufacturer_list'] = $this->get_enum_list('manufacturer');
        $this->arResult['type_of_product_list'] = $this->get_enum_list('type_of_product');

        $this->arResult['path'] = trim($_REQUEST['path']);

        $this->checkSettings(true);

    }

    private function getReplaces(){

        $aSelect = Array("DETAIL_TEXT");
        $aFilter = Array("IBLOCK_ID" => $this->ioBlockId, "ACTIVE" => "N");

        $dRes = CIBlockElement::GetList(Array(), $aFilter, false, false, $aSelect);

        $this->arResult['replaces'] = array();

        if($dRes && $aRes = $dRes->GetNext()){
            $this->arResult['replaces'] = json_decode((html_entity_decode($aRes['DETAIL_TEXT'],ENT_QUOTES)),true);
        }

        if(empty($this->arResult['replaces'])){
            $this->arResult['replaces'] = array('from' => array(''), 'to' => array(''));
        }

        return $this->arResult['replaces'];

    }

    private function checkColumns(){

        if (is_array($this->arResult['order_columns'])) {

            $acValues = array_values($this->arResult['order_columns']);

            if(is_array($acValues) && is_array($this->arColumns)) {

                $acDiff = array_diff($this->arColumns,$acValues);

                if(is_array($acDiff) && !empty($acDiff)){

                    foreach($acDiff as $icNum => $scVal){

                        if(isset($_REQUEST[$scVal])
                            && !empty($_REQUEST[$scVal])){
                            unset($acDiff[$icNum]);
                        }

                    }

                }

                if(!empty($acDiff)){

                    $sErrorStr = '';

                    foreach($acDiff as $scName){
                        $sErrorStr .= (!empty($sErrorStr) ? ', ' : '') . GetMessage('TMP_'.mb_strtoupper($scName).'_COLUMN');
                    }

                    $this->arResult['aerrors'][] = sprintf(GetMessage('TMPL_CHOOSE_REQ_COLUMN'),$sErrorStr);
                }

            }

        }

    }

    private function checkProudcts(){

        $sProducts = trim($_REQUEST['product']);

        if(!empty($sProducts)){

            if(mb_stripos($sProducts,',') !== false) {
                $aProducts = explode(',',$sProducts);
            } else {
                $aProducts = array($sProducts);
            }

            $aProducts = array_map('trim',$aProducts);
            $aProducts = array_unique($aProducts);
            $aProducts = array_filter($aProducts);

            foreach($aProducts as $iProductId){
                $this->checkProductId($iProductId);
            }

        }

    }

    private function checkProductId($iProductId){

        $bHasFound = false;

        static $products;

        if (!is_array($products)) {
            $products = [];
        }

        if(preg_match('~[^0-9]+~',$iProductId)){
            $this->arResult['aerrors'][$iProductId] = sprintf(GetMessage('TMPL_WRONG_PRODUCT_ID'),$iProductId);
        } else {

            $iProductId = (int)$iProductId;

            if (!array_key_exists($iProductId,$products)) {

                $aSelect = Array("ID");
                $aFilter = Array(
                    "IBLOCK_ID" => $this->ipBlockId,
                    "ACTIVE" => "Y",
                    "ID" => $iProductId);

                $dRes = CIBlockElement::GetList(Array(), $aFilter, false, array('nTopCount' => 1), $aSelect);

                if ($dRes && ($aRes = $dRes->GetNext())){
                    if (isset($aRes['ID']) && $aRes['ID'] > 0) {
                        $bHasFound = true;
                    }
                }

                if(!$bHasFound) {
                    $this->arResult['aerrors'][$iProductId] = sprintf(GetMessage('TMPL_NOT_FOUND_PRODUCT_ID'),$iProductId);
                }

                $products[$iProductId] = $bHasFound;

            } else {
                return $products[$iProductId];
            }

        }

        return $bHasFound;

    }

    private function cleanModels(){

        $scFound = '';

        foreach($this->arResult['order_columns'] as $scName => $scValue){
            if($scValue == 'model'){
                $scFound = $scName;
                break;
            }
        }

        if($scFound) {

            foreach($this->arResult['aSheetData'] as $iRow => $arData){

                if(isset($arData[$scFound])
                    && !empty($arData[$scFound])){

                    $this->arResult['aSheetData'][$iRow][$scFound] = $this->cleanModel($arData[$scFound]);

                    if(empty($this->arResult['aSheetData'][$iRow][$scFound])){
                        unset($this->arResult['aSheetData'][$iRow][$iRow]);
                    }

                }

            }

        }

    }

    private function cleanModel($sModel){

        $sModel = trim(preg_replace('~\s*?\([\d\s]+\s*?$~isu','',$sModel));
        $sModel = trim(preg_replace('~\s*?\([\d\s]+\)\s*?$~isu','',$sModel));
        $sModel = trim(str_ireplace('(','',$sModel));
        $sModel = trim(str_ireplace(')','',$sModel));
        $sModel = trim(preg_replace('~\s+~isu','',$sModel));
        $sModel = trim($sModel, '/\\-()');

        return $sModel;

    }

    private function replaceColumn($srType = 'manufacturer'){

        $auErrors = array();

        $this->arResult[$srType.'_list'] = $this->get_enum_list($srType);

        if(!empty($this->arResult[$srType.'_list'])){

            $scFound = '';

            foreach($this->arResult['order_columns'] as $scName => $scValue){
                if($scValue == $srType){
                    $scFound = $scName;
                    break;
                }
            }

            if($scFound) {

                foreach($this->arResult['aSheetData'] as $iRow => $arData){

                    if(isset($arData[$scFound])
                        && !empty($arData[$scFound])){

                        $scCheck = mb_strtolower(trim($arData[$scFound]));
                        $bcFound = false;

                        foreach($this->arResult[$srType.'_list'] as $scrValue){

                            $scValue = mb_strtolower(trim($scrValue));

                            if($scValue == $scCheck) {
                                $this->arResult['aSheetData'][$iRow][$scFound] = $scrValue;
                                $bcFound = true;
                            }

                        }

                        if(!$bcFound){

                            $bFind = false;

                            if (!$bFind) {
                                $auErrors[$arData[$scFound]] = $arData[$scFound];
                            }

                        }

                    }

                }

            }

        }

        if(!empty($auErrors)){

            $sErrorStr = '';

            foreach($auErrors as $scName){
                $sErrorStr .= (!empty($sErrorStr) ? ', ' : '') . $scName;
            }

            $this->arResult['aerrors'][] = sprintf(GetMessage('TMPL_REWRITE_'.mb_strtoupper($srType).'_COLUMN'),$sErrorStr);

        }

    }

    private function checkManufacturer() {

        $iCount = 0;
        $auErrors = array();

        $srType = 'manufacturer';
        $this->arResult[$srType.'_list'] = $this->get_enum_list($srType);

        $fChecked = $this->sDir.'checked.txt';
        $fAdditional = $this->sDir.'additional.txt';
        $fErrors = $this->sDir.'errors.txt';

        file_put_contents($fChecked,'');
        file_put_contents($fAdditional,'');
        file_put_contents($fErrors,'');

        $checked = unserialize(file_get_contents($fChecked));
        $checked = !is_array($checked) ? [] : $checked;

        $additional = unserialize(file_get_contents($fAdditional));
        $additional = !is_array($additional) ? [] : $additional;

        $errors = unserialize(file_get_contents($fErrors));
        $errors = !is_array($errors) ? [] : $errors;


        if(!empty($this->arResult[$srType.'_list'])){

            $scFound = '';

            foreach($this->arResult['order_columns'] as $scName => $scValue){
                if($scValue == $srType){
                    $scFound = $scName;
                    break;
                }
            }

            if($scFound) {

                foreach($this->arResult['aSheetData'] as $iRow => $arData){

                    if(isset($arData[$scFound])
                        && !empty($arData[$scFound])){

                        $aNew = $this->tryModel($this->arResult['aSheetData'][$iRow],$this->arResult['order_columns']);

                        if ($aNew) {

                            $additional = array_merge($additional,$aNew);

                        }

                    }

                    $checked[$iRow] = $this->arResult['aSheetData'][$iRow];

                }

            }

        }

        if(!empty($additional)) {
            $this->arResult['aSheetData'] = array_merge($checked,$additional);
        } else {
            $this->arResult['aSheetData'] = $checked;
        }

        if (!empty($errors)) {
            $this->arResult['aerrors'] = array_merge($errors,$this->arResult['aerrors']);
        }

        $this->arResult['aerrors'] = array_unique($this->arResult['aerrors']);

    }

    private function getModelByName($sName){

        static $aModelsNames;

        if (!is_array($aModelsNames)) {
            $aModelsNames = [];
        }

        $iModelId = 0;

        if (empty($sName)) {
            return $iModelId;
        }

        if (!array_key_exists($sName,$aModelsNames)) {

            $aSelect = Array("ID");
            $aFilter = Array(
                "IBLOCK_ID" => $this->imnBlockId,
                "ACTIVE" => "Y",
                "=NAME" => $sName);

            $dRes = CIBlockElement::GetList(Array(), $aFilter, false, array('nTopCount' => 1), $aSelect);

            if($dRes && $aRes = $dRes->GetNext()) {
                $iModelId = (int)$aRes['ID'];
            }

            $aModelsNames[$sName] = $iModelId;

        } else {
            $iModelId = $aModelsNames[$sName];
        }

        return $iModelId;

    }

    private function tryModel(&$aLine,$aColumns){

        static $mReady;

        if (!is_array($mReady)) {
            $mReady = [];
        }

        $aNew = [];
        $aSelect = Array("ID", "PROPERTY_manufacturer");
        $aFilter = Array(
            "IBLOCK_ID" => $this->imBlockId,
            "ACTIVE" => "Y");

        $sModelName = '';
        $shKey = '';

        foreach ($aColumns as $sNumber => $sFilter) {

            $sValue = '';

            switch ($sFilter) {

                case 'model':

                    $sModelName = $aLine[$sNumber];

                    if (!$sModelName) {
                        return false;
                    }

                    $sValue = $this->getModelByName($sModelName);

                    if (!$sValue) {
                        return false;
                    }

                    $aFilter['PROPERTY_model_new_link'] = $sValue;

                    $shKey .= $sValue.';';

                    break;

                case 'type_of_product':
                case 'manufacturer':


                    $sValue = $aLine[$sNumber];

                    if (!empty($sValue)) {
                        $aFilter['PROPERTY_'.$sFilter.'_VALUE'] = $sValue;
                    } else {
                        return false;
                    }

                    $shKey .= $sValue.';';

                    break;

            }

        }

        $iModelId = 0;
        $sOldManufacutrer = $aFilter['PROPERTY_manufacturer_VALUE'];

        if (!isset($mReady[$shKey])) {

            $dRes = CIBlockElement::GetList(Array(), $aFilter, false, array('nTopCount' => 1), $aSelect);

            if($dRes && $aRes = $dRes->GetNext()){
                $iModelId = (int)$aRes['ID'];
            }

            if (!$iModelId) {

                unset($aFilter['PROPERTY_manufacturer_VALUE']);

                $dRes = CIBlockElement::GetList(Array(), $aFilter, false, false, $aSelect);

                if ($dRes) {

                    while ($aRes = $dRes->GetNext()) {

                        $mReady[$shKey][$aRes['ID']] = $aRes;

                    }

                }

            } else {
                $mReady[$shKey] = [];
            }

        }

        $smKey = array_search('manufacturer', $aColumns);
        $bFirst = true;

        foreach($mReady[$shKey] as $aRes) {

            if ($smKey !== false
                && isset($aRes['PROPERTY_MANUFACTURER_VALUE'])
                && !empty($aRes['PROPERTY_MANUFACTURER_VALUE'])) {

                if ($bFirst) {
                    $aLine[$smKey] = $aRes['PROPERTY_MANUFACTURER_VALUE'];
                    $bFirst = false;
                } else {
                    $aCopy = $aLine;
                    $aCopy[$smKey] = $aRes['PROPERTY_MANUFACTURER_VALUE'];
                    $aNew[] = $aCopy;
                }

                $iModelId = (int)$aRes['ID'];
                $this->arResult['aerrors'][] = sprintf(GetMessage('TMPL_MANUFACTURER_REPLACE'),$sOldManufacutrer,$aRes['PROPERTY_MANUFACTURER_VALUE'],$sModelName);

            }
        }

        return $iModelId ? $aNew : false;

    }

    private function cleanProducts(){

        static $apCleaned;

        if(!is_array($apCleaned)){
            $apCleaned = array();
        }

        $scFound = '';

        foreach($this->arResult['order_columns'] as $scName => $scValue){
            if($scValue == 'product'){
                $scFound = $scName;
                break;
            }
        }

        if($scFound) {

            foreach($this->arResult['aSheetData'] as $iRow => $arData){

                if(isset($arData[$scFound])
                    && !empty($arData[$scFound])) {

                    $bHasFound = false;

                    if (isset($apCleaned[$arData[$scFound]])) {
                        $bHasFound = $apCleaned[$arData[$scFound]];
                    } else {
                        $bHasFound = $this->checkProductId($arData[$scFound]);
                    }

                    $apCleaned[$arData[$scFound]] = $bHasFound;

                    if (!$bHasFound) {
                        unset($this->arResult['aSheetData'][$iRow][$scFound]);
                    }

                }

            }

        }

    }

    private function doProcess(){

        $start = time();
        define('P_START',$start);

        $_REQUEST = $this->getCronConfig();
        file_put_contents($this->sDir.$this->messagesDir.$this->messagesFile,'');

        if (!empty($_REQUEST)) {

            $this->checkForErrors(true);

            if(!(sizeof($this->arResult['aerrors']) > 0)) {

                file_put_contents($this->sDir.$this->logDir.$this->logFile,'start - '.(time() - P_START)."\n");

                if (isset($_REQUEST['check_manufacturer'])) {
                    $this->checkManufacturer();
                }

                file_put_contents($this->sDir.$this->logDir.$this->logFile,'checkManufacturer - '.(time() - P_START)."\n", FILE_APPEND);

                $this->replaceColumn('manufacturer');

                file_put_contents($this->sDir.$this->logDir.$this->logFile,'manufacturer - '.(time() - P_START)."\n", FILE_APPEND);

                $this->replaceColumn('type_of_product');

                file_put_contents($this->sDir.$this->logDir.$this->logFile,'type_of_product - '.(time() - P_START)."\n", FILE_APPEND);

                $this->cleanModels();

                file_put_contents($this->sDir.$this->logDir.$this->logFile,'cleanModels - '.(time() - P_START)."\n", FILE_APPEND);

                $this->cleanProducts();

                file_put_contents($this->sDir.$this->logDir.$this->logFile,'cleanProducts - '.(time() - P_START)."\n", FILE_APPEND);

                $this->saveModelsFile();

                file_put_contents($this->sDir.$this->logDir.$this->logFile,'saveModelsFile - '.(time() - P_START)."\n", FILE_APPEND);

            }

            @unlink($this->sDir.$this->processDir.$this->processFile);
            $this->sendMessage($this->arResult);

        }

        file_put_contents($this->sDir.$this->messagesDir.$this->messagesFile,serialize($this->arResult));

    }

    private function sendMessage($message)
    {
        $message = join("\n",$message['amessages']);

        CModule::IncludeModule("main");

        $arrSites = array();
        $objSites = CSite::GetList(($by = "sort"), ($order = "asc"));

        while ($arrSite = $objSites->Fetch()) {
            $arrSites[] = $arrSite["ID"];
        };

        CEvent::SendImmediate('MODELS_DONE', $arrSites, ['MESSAGE' => $message]);

    }

    private function saveModelsFile(){


        $aFiles = array();

        $rArr = array();

        $sPath = $this->sDir.$this->doneDir.'models[part].csv';
        $sdPath = str_ireplace('[part]','',$sPath);

        $rFp = fopen($sdPath,'w+');

        $aCsvHeaders = $this->aCsvHeaders;
        $aDefaults = $_REQUEST;

        if($rFp){

            $aFiles[] = str_ireplace($_SERVER['DOCUMENT_ROOT'],'',$sdPath);

            if(sizeof($this->arResult['aSheetData']) > 0){

                foreach($this->arResult['aSheetData'] as $iRow => $arData){

                    $brHasError = false;
                    $cRow = array();

                    foreach($aCsvHeaders as $aCsvColumn){

                        $scFound = false;

                        foreach($this->arResult['order_columns'] as $scName => $scValue){
                            if($aCsvColumn == $scValue){
                                $scFound = $scName;
                            }
                        }

                        if($scFound
                            && isset($arData[$scFound])
                            && !empty($arData[$scFound])){

                            $cRow[$aCsvColumn] = $arData[$scFound];
                            unset($aDefaults[$aCsvColumn]);

                        } else if(isset($aDefaults[$aCsvColumn])) {

                            $cRow[$aCsvColumn] = $aDefaults[$aCsvColumn];

                        } else {
                            $cRow[$aCsvColumn] = '';
                        }

                        if(in_array($aCsvColumn,$this->arColumns)
                            && !(isset($cRow[$aCsvColumn])
                                && !empty($cRow[$aCsvColumn]))){

                            $brHasError = true;

                        }

                        if(isset($cRow[$aCsvColumn])
                            && !empty($cRow[$aCsvColumn]))
                            $this->checkForExp($cRow[$aCsvColumn]);

                    }

                    if($brHasError)
                        continue;

                    $aRows = array();

                    if(!empty($cRow['model']))
                        $cRow['model'] = $this->modelReplaces($cRow['model']);


                    if(sizeof($cRow) > 4){
                        $rArr[$cRow['type_of_product'].'~'.$cRow['manufacturer'].'~'.$cRow['model']] = array($cRow['type_of_product'],$cRow['manufacturer'],$cRow['model']);
                    }

                    if(mb_stripos($cRow['product'],',') !== false){

                        $sProducts = trim($cRow['product']);

                        if(!empty($sProducts)) {

                            if (mb_stripos($sProducts, ',') !== false) {
                                $aProducts = explode(',', $sProducts);
                            } else {
                                $aProducts = array($sProducts);
                            }

                            $aProducts = array_map('trim', $aProducts);
                            $aProducts = array_unique($aProducts);
                            $aProducts = array_filter($aProducts);

                            foreach ($aProducts as $iProductId) {
                                $cRow['product'] = $iProductId;
                                $aRows[] = $cRow;
                            }
                        }

                    } else {
                        $aRows[] = $cRow;
                    }


                    if(!empty($aRows)){
                        foreach($aRows as $aRow){
                            fputcsv($rFp,$aRow,';','"');
                        }
                    }

                }

            }

            fclose($rFp);

            $srPath = $this->sDir.$this->doneDir.'remove_products.csv';

            $rrFp = fopen($srPath,'w+');

            if($rrFp
                && sizeof($rArr) > 0){

                foreach($rArr as $aStr){
                    fputcsv($rrFp,$aStr,';','"');
                }

                fclose($rrFp);

                if(filesize($srPath) > 0){
                    $aFiles[] = str_ireplace($_SERVER['DOCUMENT_ROOT'],'',$srPath);
                }
            }

            if(filesize($sdPath) > 0){

                $aCsvLines = file($sdPath);
                $iPerPart = ceil(sizeof($aCsvLines)/$this->iParts);

                for($ilCount = 0; $ilCount < $this->iParts; $ilCount++){

                    $aMaxLines = array_slice($aCsvLines,$ilCount * $iPerPart, $iPerPart);
                    $aMaxLines = array_map('trim',$aMaxLines);
                    $spPath = str_ireplace('[part]',$ilCount,$sPath);
                    file_put_contents($spPath, join("\n",$aMaxLines));
                    $aFiles[] = str_ireplace($_SERVER['DOCUMENT_ROOT'],'',$spPath);

                }

            }

        }

        if(sizeof($aFiles) > 0){

            $sFiles = ' ';

            foreach($aFiles as $sPath){
                $sFiles .= '<a href="'.IMPEL_PROTOCOL.IMPEL_SERVER_NAME.'/'.ltrim($sPath,'/').'?time='.time().'" target="_blank">'.$sPath.'</a> ';
            }

            $this->arResult['amessages'][] = sprintf(GetMessage('TMPL_CSV_SAVED'),$sFiles);

        }

    }

    private function modelReplaces($model){

        $model = trim(preg_replace('~\s*?\([\d\s]+\s*?$~isu','',$model));
        $model = trim(preg_replace('~\s*?\([\d\s]+\)\s*?$~isu','',$model));
        $model = trim(str_ireplace('(','',$model));
        $model = trim(str_ireplace(')','',$model));
        $model = trim(preg_replace('~\s+~isu','',$model));
        $model = trim($model, '/\\-()');

        return $model;
    }

    private function checkForExp($svColumn){

        if(preg_match('~e+?[\+\-]+?[\d]+~isu',$svColumn)){
            $this->arResult['aerrors'][$svColumn] = sprintf(GetMessage('TMPL_MAYBE_EXPONENT'),$svColumn);
        }

    }

    private function getProcess(){

        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        if ($this->checkStart()) {
            $this->saveCronConfig();
        }

        if ($this->checkStep()) {
            die('next');
        }

        $mFile = $this->sDir.$this->messagesDir.$this->messagesFile;

        if (file_exists($mFile) && filesize($mFile) > 0) {
            $this->arResult = unserialize(file_get_contents($mFile));
        }

        @unlink($mFile);

        $_REQUEST['order_columns'] = json_encode(array_map('trim',$_REQUEST['order_columns']),JSON_UNESCAPED_UNICODE);
        $this->checkSettings(true);

    }

    public function checkStart()
    {
        return isset($_REQUEST['step']) && $_REQUEST['step'] == 1;

    }

    public function checkStep()
    {
        return isset($_REQUEST['step']) && !empty($_REQUEST['step']) && file_exists($this->sDir.$this->processDir.$this->processFile);

    }

    public function getCronConfig() {

        $scPath = $this->sDir.$this->processDir.$this->processFile;
        $config = [];

        if (file_exists($scPath)) {
            $config = unserialize(file_get_contents($scPath));
        }

        return $config;
    }

    public function saveCronConfig() {

        file_put_contents($this->sDir.$this->processDir.$this->processFile,serialize($_REQUEST));

    }

    public function executeComponent()
    {

        $this->arResult = array(
            'amessages' => array(),
            'aerrors' => array()
        );

		if($this->getRigts()){

            __IncludeLang(dirname(__FILE__)."/lang/".LANGUAGE_ID."/filelist.php");

            $this->checkDirExists();

            if(isset($_REQUEST['action'])){

                switch ($_REQUEST['action']){
                    case 'process_get':
                        $this->getProcess();
                        break;
                    case 'do_process':
                        $this->doProcess();
                        break;
                    case 'replaces_save':
                        $this->saveReplaces();
                        break;
                    case 'settings':
                        $this->checkSettings();
                        break;
                    case 'file_upload':
                        $this->checkUploads();
                        break;
                    case 'preview':
						$this->checkPreview();
                        break;
                    case 'profile_load':
                        $this->loadProfile();
                        break;
                    case 'profile_save':
                        $this->saveProfile();
                        break;
                    case 'delete':
                        $this->checkDelete();
                        break;
                }
            }

            $this->getFilesList();
            $this->getReplaces();

            ob_start();
            $this->includeComponentTemplate('replaces');
            $this->arResult['replaces'] = ob_get_clean();

            ob_start();
            $this->includeComponentTemplate('filelist');
            $this->arResult['filelist'] = ob_get_clean();

            $this->includeComponentTemplate();

        }

    }

    public function isProductType($iCount,$productType,$toType,$typeOfProduct,$toProductTypePreg){

        $isProductType = -1;
        $oldProductType = $productType;

        if(isset($toType[strtolower($productType)])
            && !empty($toType[strtolower($productType)])){

            $productType = $toType[strtolower($productType)];
            $isProductType = $iCount;

        } elseif (isset($typeOfProduct[strtolower($productType)])
            && !empty($typeOfProduct[strtolower($productType)])){

            $productType = $typeOfProduct[strtolower($productType)];
            $isProductType = $iCount;

        } else {

            foreach($toProductTypePreg as $pattern => $replace){

                if(preg_match($pattern,$productType)){

                    $productType = $replace;
                    $isProductType = $iCount;
                    break;

                }
            }
        }

        return $isProductType != -1 ? $productType : false;
    }

    public function isManufacturer($iCount,$manufacturer,$toManufacturer,$manufacturers,$toManufacturerPreg){

        $isManufacturer = -1;

        if(isset($toManufacturer[strtolower($manufacturer)])
            && !empty($toManufacturer[strtolower($manufacturer)])){

            $manufacturer = $toManufacturer[strtolower($manufacturer)];
            $isManufacturer = $iCount;

        } elseif(isset($manufacturers[strtolower($manufacturer)])
            && !empty($manufacturers[strtolower($manufacturer)])){

            $manufacturer = $manufacturers[strtolower($manufacturer)];
            $isManufacturer = $iCount;

        } else {

            foreach($toManufacturerPreg as $pattern => $replace){
                if(preg_match($pattern,$manufacturer)){

                    $manufacturer = $replace;
                    $isManufacturer = $iCount;
                    break;

                }
            }

        }

        return $isManufacturer != -1 ? $manufacturer : false;

    }

    public function isProduct(){
    }

    public function isModel(){
    }

    public function isIndCode(){
    }

    public function isView(){
    }


}