<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED  !== true)
    die();

CModule::IncludeModule("iblock");

class ImpelAmprelComponent extends CBitrixComponent
{

    protected static $elementId = 0;
    protected static $elementCode = '';

    protected static $sectionId = 0;
    protected static $sectionCode = '';
    protected static $iblock_id = 0;

    protected static $checkPath = '';


    public static function setCheckPath($checkPath){
        static::$checkPath = $checkPath;
    }

    public static function getCheckPath(){
        return static::$checkPath;
    }

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    public static function tryToSuggestComponentPageAtNews($arParams){

        if($arParams["USE_FILTER"]=="Y") {
            if(mb_strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
                $arParams["FILTER_NAME"] = "arrFilter";
        }
        else
            $arParams["FILTER_NAME"] = "";

        $arParams["USE_CATEGORIES"]=$arParams["USE_CATEGORIES"]=="Y";
        if($arParams["USE_CATEGORIES"])
        {
            if(!is_array($arParams["CATEGORY_IBLOCK"]))
                $arParams["CATEGORY_IBLOCK"] = array();
            $ar = array();
            foreach($arParams["CATEGORY_IBLOCK"] as $key=>$value)
            {
                $value=intval($value);
                if($value>0)
                    $ar[$value]=true;
            }
            $arParams["CATEGORY_IBLOCK"] = array_keys($ar);
        }
        $arParams["CATEGORY_CODE"]=trim($arParams["CATEGORY_CODE"]);
        if(mb_strlen($arParams["CATEGORY_CODE"])<=0)
            $arParams["CATEGORY_CODE"]="CATEGORY";
        $arParams["CATEGORY_ITEMS_COUNT"]=intval($arParams["CATEGORY_ITEMS_COUNT"]);
        if($arParams["CATEGORY_ITEMS_COUNT"]<=0)
            $arParams["CATEGORY_ITEMS_COUNT"]=5;

        if(!is_array($arParams["CATEGORY_IBLOCK"]))
            $arParams["CATEGORY_IBLOCK"] = array();
        foreach($arParams["CATEGORY_IBLOCK"] as $iblock_id)
            if($arParams["CATEGORY_THEME_".$iblock_id]!="photo")
                $arParams["CATEGORY_THEME_".$iblock_id]="list";

        $arDefaultUrlTemplates404 = array(
            "news" => "",
            "search" => "search/",
            "rss" => "rss/",
            "rss_section" => "#SECTION_ID#/rss/",
            "detail" => "#ELEMENT_ID#/",
            "section" => "",
        );

        $arDefaultVariableAliases404 = array();

        $arDefaultVariableAliases = array();

        $arComponentVariables = array(
            "SECTION_ID",
            "SECTION_CODE",
            "ELEMENT_ID",
            "ELEMENT_CODE",
        );

        if($arParams["USE_SEARCH"] != "Y")
        {
            unset($arDefaultUrlTemplates404["search"]);
            unset($arParams["SEF_URL_TEMPLATES"]["search"]);
        }
        else
        {
            $arComponentVariables[] = "q";
            $arComponentVariables[] = "tags";
        }

        if($arParams["USE_RSS"] != "Y")
        {
            unset($arDefaultUrlTemplates404["rss"]);
            unset($arDefaultUrlTemplates404["rss_section"]);
            unset($arParams["SEF_URL_TEMPLATES"]["rss"]);
            unset($arParams["SEF_URL_TEMPLATES"]["rss_section"]);
        }
        else
        {
            $arComponentVariables[] = "rss";
        }

        /* Compatibility with deleted DETAIL_STRICT_SECTION_CHECK */
        if (isset($arParams["STRICT_SECTION_CHECK"]))
            $arParams["DETAIL_STRICT_SECTION_CHECK"] = $arParams["STRICT_SECTION_CHECK"];
        else
            $arParams["STRICT_SECTION_CHECK"] = $arParams["DETAIL_STRICT_SECTION_CHECK"];

        if($arParams["SEF_MODE"] == "Y")
        {

            $arVariables = array();

            $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
            $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

            $component = new \CBitrixComponent();
            $component->InitComponent('bitrix:news');
            $component->arParams = $arParams;

            $engine = new CComponentEngine($component);
            if (CModule::IncludeModule('iblock'))
            {
                $engine->addGreedyPart("#SECTION_CODE_PATH#");
                $engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
            }
            $componentPage = $engine->guessComponentPath(
                $arParams["SEF_FOLDER"],
                $arUrlTemplates,
                $arVariables
            );

            if(!$componentPage)
            {
                $componentPage = "news";
            }

        }
        else
        {
            $arVariables = array();
            $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
            CComponentEngine::initComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

            $componentPage = "";

            if(isset($arVariables["ELEMENT_ID"]) && intval($arVariables["ELEMENT_ID"]) > 0)
                $componentPage = "detail";
            elseif(isset($arVariables["ELEMENT_CODE"]) && mb_strlen($arVariables["ELEMENT_CODE"]) > 0)
                $componentPage = "detail";
            elseif(isset($arVariables["SECTION_ID"]) && intval($arVariables["SECTION_ID"]) > 0)
            {
                if(isset($arVariables["rss"]) && $arVariables["rss"]=="y")
                    $componentPage = "rss_section";
                else
                    $componentPage = "section";
            }
            elseif(isset($arVariables["SECTION_CODE"]) && mb_strlen($arVariables["SECTION_CODE"]) > 0)
            {
                if(isset($arVariables["rss"]) && $arVariables["rss"]=="y")
                    $componentPage = "rss_section";
                else
                    $componentPage = "section";
            }
            elseif(isset($arVariables["q"]) && mb_strlen(trim($arVariables["q"])) > 0)
                $componentPage = "search";
            elseif(isset($arVariables["tags"]) && mb_strlen(trim($arVariables["tags"])) > 0)
                $componentPage = "search";
            elseif(isset($arVariables["rss"]) && $arVariables["rss"]=="y")
                $componentPage = "rss";
            else
                $componentPage = "news";


        }

        if(isset($arVariables['ELEMENT_ID'])
            && !empty($arVariables['ELEMENT_ID'])){

            static::$elementId = $arVariables['ELEMENT_ID'];

        }

        if(isset($arVariables['ELEMENT_CODE'])
            && !empty($arVariables['ELEMENT_CODE'])){

            static::$elementCode = $arVariables['ELEMENT_CODE'];

        }

        if(isset($arVariables['SECTION_ID'])
            && !empty($arVariables['SECTION_ID'])){

            static::$sectionId = $arVariables['SECTION_ID'];

        }

        if(isset($arVariables['SECTION_CODE'])
            && !empty($arVariables['SECTION_CODE'])){

            static::$sectionCode = $arVariables['SECTION_CODE'];

        }

        return $componentPage;

    }

    public static function tryToSuggestComponentPageAtCatalog($arParams){

        if (isset($arParams["USE_FILTER"]) && $arParams["USE_FILTER"]=="Y")
        {
            $arParams["FILTER_NAME"] = trim($arParams["FILTER_NAME"]);
            if ($arParams["FILTER_NAME"] === '' || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
                $arParams["FILTER_NAME"] = "arrFilter";
        }
        else
            $arParams["FILTER_NAME"] = "";

        $smartBase = ($arParams["SEF_URL_TEMPLATES"]["section"]? $arParams["SEF_URL_TEMPLATES"]["section"]: "#SECTION_ID#/");
        $arDefaultUrlTemplates404 = array(
            "sections" => "",
            "section" => "#SECTION_ID#/",
            "element" => "#SECTION_ID#/#ELEMENT_ID#/",
            "compare" => "compare.php?action=COMPARE",
            "smart_filter" => $smartBase."filter/#SMART_FILTER_PATH#/apply/"
        );

        $arDefaultVariableAliases404 = array();

        $arDefaultVariableAliases = array();

        $arComponentVariables = array(
            "SECTION_ID",
            "SECTION_CODE",
            "ELEMENT_ID",
            "ELEMENT_CODE",
            "action",
        );

        if($arParams["SEF_MODE"] == "Y")
        {
            $arVariables = array();

            $component = new \CBitrixComponent();
            $component->InitComponent('bitrix:catalog');
            $component->arParams = $arParams;
            $engine = new CComponentEngine($component);

            if (\Bitrix\Main\Loader::includeModule('iblock'))
            {
                $engine->addGreedyPart("#SECTION_CODE_PATH#");
                $engine->addGreedyPart("#SMART_FILTER_PATH#");
                $engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
            }

            $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);

            $componentPage = $engine->guessComponentPath(
                $arParams["SEF_FOLDER"],
                $arUrlTemplates,
                $arVariables
            );

            if ($componentPage === "smart_filter")
                $componentPage = "section";

            if(!$componentPage && isset($_REQUEST["q"]))
                $componentPage = "search";

            if(!$componentPage)
            {
                $componentPage = "sections";
            }


        }
        else
        {
            $arVariables = array();

            $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
            CComponentEngine::initComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

            $componentPage = "";

            $arCompareCommands = array(
                "COMPARE",
                "DELETE_FEATURE",
                "ADD_FEATURE",
                "DELETE_FROM_COMPARE_RESULT",
                "ADD_TO_COMPARE_RESULT",
                "COMPARE_BUY",
                "COMPARE_ADD2BASKET",
            );

            if(isset($arVariables["action"]) && in_array($arVariables["action"], $arCompareCommands))
                $componentPage = "compare";
            elseif(isset($arVariables["ELEMENT_ID"]) && intval($arVariables["ELEMENT_ID"]) > 0)
                $componentPage = "element";
            elseif(isset($arVariables["ELEMENT_CODE"]) && mb_strlen($arVariables["ELEMENT_CODE"]) > 0)
                $componentPage = "element";
            elseif(isset($arVariables["SECTION_ID"]) && intval($arVariables["SECTION_ID"]) > 0)
                $componentPage = "section";
            elseif(isset($arVariables["SECTION_CODE"]) && mb_strlen($arVariables["SECTION_CODE"]) > 0)
                $componentPage = "section";
            elseif(isset($_REQUEST["q"]))
                $componentPage = "search";
            else
                $componentPage = "sections";


        }

        if(isset($arVariables['ELEMENT_ID'])
            && !empty($arVariables['ELEMENT_ID'])){

            static::$elementId = $arVariables['ELEMENT_ID'];

        }

        if(isset($arVariables['ELEMENT_CODE'])
            && !empty($arVariables['ELEMENT_CODE'])){

            static::$elementCode = $arVariables['ELEMENT_CODE'];

        }

        if(isset($arVariables['SECTION_ID'])
            && !empty($arVariables['SECTION_ID'])){

            static::$sectionId = $arVariables['SECTION_ID'];

        }

        if(isset($arVariables['SECTION_CODE'])
            && !empty($arVariables['SECTION_CODE'])){

            static::$sectionCode = $arVariables['SECTION_CODE'];

        }

        return $componentPage;
    }

    public static function tryToCollectVarsAtCatalogSection($arParams)
    {

        if(isset($arParams['SECTION_ID'])
            && !empty($arParams['SECTION_ID'])){

            static::$sectionId = $arParams['SECTION_ID'];

        }

        if(isset($arParams['SECTION_CODE'])
            && !empty($arParams['SECTION_CODE'])){

            static::$sectionCode = $arParams['SECTION_CODE'];

        }

    }

    public static function tryToCollectVarsAtCatalogElement($arParams)
    {

        if(isset($arParams['ELEMENT_ID'])
            && !empty($arParams['ELEMENT_ID'])){

            static::$elementId = $arParams['ELEMENT_ID'];

        }

        if(isset($arParams['ELEMENT_CODE'])
            && !empty($arParams['ELEMENT_CODE'])){

            static::$elementCode = $arParams['ELEMENT_CODE'];

        }


    }

    private static function returnRealPath(){


        $path = static::getCheckPath();

        if(empty($path)){

            $path = isset($_SERVER['REAL_FILE_PATH'])
            &&!empty($_SERVER['REAL_FILE_PATH'])
                ? trim($_SERVER['REAL_FILE_PATH'])
                : $_SERVER['PHP_SELF'];

        }

        return $path;

    }

    public static function checkPageComponent(){
        global $APPLICATION;

        $componentPage = '';

        $realFilePath = $_SERVER['DOCUMENT_ROOT'].static::returnRealPath();

        if(!empty(static::returnRealPath())
            && is_file($realFilePath)
        ){

            $content = file_get_contents($realFilePath);
            $components = array();
            $previous = array();

            preg_match_all('~\$APPLICATION->IncludeComponent\((.*?)\);~isu',$content,$components);

            if(isset($components[1]) && !empty($components[1])){
                foreach($components[1] as $number => $component){

                    $parts = explode(',',$component,3);

                    if(is_array($parts)
                        && sizeof($parts)){

                        $componentName = trim($parts[0]);
                        $componentName = preg_replace('~^[\s\'"]+~','',$componentName);
                        $componentName = preg_replace('~[\s\'"]+?$~','',$componentName);

                        if($componentName == 'impel:amprel'
                            && !empty($previous)){

                            $componentName = trim($previous[0]);
                            $componentName = preg_replace('~^[\s\'"]+~','',$componentName);
                            $componentName = preg_replace('~[\s\'"]+?$~','',$componentName);

                            preg_match('~"IBLOCK_ID"\s*?\=\>\s*?"([^"]+?)"~isu',$previous[2],$iblock_matches);

                            if(isset($iblock_matches[1])
                                && !empty($iblock_matches[1])
                            ){
                                static::$iblock_id = (int)trim($iblock_matches[1]);

                            }

                            switch($componentName){

                                case 'bitrix:catalog.section.list':
                                case 'bitrix:catalog.section':

                                    $content = '<?php $arCatalogParams = array('.trim($previous[2]).');'.'?>';

                                    $fileDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache/';

                                    $filePath = $fileDir.md5(static::returnRealPath()).'.php';

                                    if(!(file_exists($fileDir)
                                        && is_writable($fileDir)
                                        && file_exists($filePath)
                                        && filemtime($filePath) > filemtime($realFilePath))){
                                        file_put_contents($filePath,$content);
                                    }

                                    if(file_exists($filePath)){

                                        require_once $filePath;

                                        if (isset($arCatalogParams)
                                            && isset($arCatalogParams[0])
                                            && !empty($arCatalogParams[0])
                                            && is_array($arCatalogParams[0])){

                                            static::tryToCollectVarsAtCatalogSection($arCatalogParams[0]);

                                            switch($componentName){
                                                case 'bitrix:catalog.section.list':
                                                    $componentPage = 'sections';
                                                    break;

                                                case 'bitrix:catalog.section':
                                                    $componentPage = 'section';
                                                    break;
                                            }

                                            break;
                                        }

                                    }

                                    break;

                                case 'bitrix:catalog':

                                    $content = '<?php $arCatalogParams = array('.trim($previous[2]).');'.'?>';

                                    $fileDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache/';

                                    $filePath = $fileDir.md5(static::returnRealPath()).'.php';

                                    if(!(file_exists($fileDir)
                                        && is_writable($fileDir)
                                        && file_exists($filePath)
                                        && filemtime($filePath) > filemtime($realFilePath))){
                                        file_put_contents($filePath,$content);
                                    }

                                    if(file_exists($filePath)){

                                        require_once $filePath;

                                        if (isset($arCatalogParams)
                                            && isset($arCatalogParams[0])
                                            && !empty($arCatalogParams[0])
                                            && is_array($arCatalogParams[0])){

                                            $componentPage = static::tryToSuggestComponentPageAtCatalog($arCatalogParams[0]);
                                            break;
                                        }

                                    }

                                    break;

                                case 'bitrix:catalog.element':

                                    $content = '<?php $arCatalogParams = array('.trim($previous[2]).');'.'?>';

                                    $fileDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache/';

                                    $filePath = $fileDir.md5(static::returnRealPath()).'.php';

                                    if(!(file_exists($fileDir)
                                        && is_writable($fileDir)
                                        && file_exists($filePath)
                                        && filemtime($filePath) > filemtime($realFilePath))){
                                        file_put_contents($filePath,$content);
                                    }

                                    if(file_exists($filePath)){

                                        require_once $filePath;

                                        if (isset($arCatalogParams)
                                            && isset($arCatalogParams[0])
                                            && !empty($arCatalogParams[0])
                                            && is_array($arCatalogParams[0])){

                                            static::tryToCollectVarsAtCatalogElement($arCatalogParams[0]);
                                            $componentPage = 'element';
                                            break;
                                        }

                                    }


                                    break;

                                case 'bitrix:news.detail':

                                    $content = '<?php $arNewsParams = array('.trim($previous[2]).');'.'?>';

                                    $fileDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache/';

                                    $filePath = $fileDir.md5(static::returnRealPath()).'.php';

                                    if(!(file_exists($fileDir)
                                        && is_writable($fileDir)
                                        && file_exists($filePath)
                                        && filemtime($filePath) > filemtime($realFilePath))){
                                        file_put_contents($filePath,$content);
                                    }

                                    if(file_exists($filePath)){

                                        require_once $filePath;

                                        if (isset($arNewsParams)
                                            && isset($arNewsParams[0])
                                            && !empty($arNewsParams[0])
                                            && is_array($arNewsParams[0])){

                                            static::tryToCollectVarsAtCatalogElement($arNewsParams[0]);
                                            $componentPage = 'element';

                                            break;
                                        }

                                    }

                                    break;

                                case 'bitrix:news.list':

                                    $content = '<?php $arNewsParams = array('.trim($previous[2]).');'.'?>';

                                    $fileDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache/';

                                    $filePath = $fileDir.md5(static::returnRealPath()).'.php';

                                    if(!(file_exists($fileDir)
                                        && is_writable($fileDir)
                                        && file_exists($filePath)
                                        && filemtime($filePath) > filemtime($realFilePath))){
                                        file_put_contents($filePath,$content);
                                    }

                                    if(file_exists($filePath)){

                                        require_once $filePath;

                                        if (isset($arNewsParams)
                                            && isset($arNewsParams[0])
                                            && !empty($arNewsParams[0])
                                            && is_array($arNewsParams[0])){

                                            static::tryToCollectVarsAtCatalogSection($arNewsParams[0]);
                                            $componentPage = 'news.list';

                                        }

                                    }

                                    break;

                                case 'bitrix:news':



                                    $content = '<?php $arNewsParams = array('.trim($previous[2]).');'.'?>';

                                    $fileDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache/';

                                    $filePath = $fileDir.md5(static::returnRealPath()).'.php';

                                    if(!(file_exists($fileDir)
                                        && is_writable($fileDir)
                                        && file_exists($filePath)
                                        && filemtime($filePath) > filemtime($realFilePath))){
                                        file_put_contents($filePath,$content);
                                    }

                                    if(file_exists($filePath)){

                                        require_once $filePath;

                                        if (isset($arNewsParams)
                                            && isset($arNewsParams[0])
                                            && !empty($arNewsParams[0])
                                            && is_array($arNewsParams[0])){

                                            $componentPage = static::tryToSuggestComponentPageAtNews($arNewsParams[0]);


                                            break;
                                        }

                                    }



                                    break;

                            }

                        }

                    }

                    $previous = $parts;

                }

            }

        }

        return $componentPage;
    }

    public function createAMPElementUrl($elementID,$fieldCode = 'ID'){

        global $APPLICATION;

        $detailURLTemplate = $this->arParams['AMP_DETAIL_URL'];

        if(!empty($detailURLTemplate)){

            $arSelect = Array('DETAIL_PAGE_URL');
            $arFilter = Array($fieldCode => trim($elementID));

            if(!empty(static::$iblock_id)){
                $arFilter['IBLOCK_ID'] = static::$iblock_id;
            }

            $dbRes = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            $dbRes->SetUrlTemplates($detailURLTemplate);

            if($dbRes && ($arFields = $dbRes->GetNext())){

               // $APPLICATION->AddHeadString('<link rel="amphtml" href="'.((CMain::IsHTTPS() ? 'https' : 'http') . '://' . $_SERVER['SERVER_NAME'] . ''. $arFields['DETAIL_PAGE_URL'] .'" />'));

            }

        }

    }

    public function createAMPSectionsUrl(){

        global $APPLICATION;

        $armpSectionsUrlTemplate = $this->arParams['AMP_SECTIONS_URL'];

        if(!empty($armpSectionsUrlTemplate)){

            $armpSectionsUrlTemplate = preg_replace('~http.*?:[^/]+?/~is','',$armpSectionsUrlTemplate);
            $armpSectionsUrlTemplate = ltrim($armpSectionsUrlTemplate,'/');
            $armpSectionsUrlTemplate = '/'.$armpSectionsUrlTemplate;

            $APPLICATION->AddHeadString('<link rel="amphtml" href="'.((CMain::IsHTTPS() ? 'https' : 'http') . '://' . $_SERVER['SERVER_NAME'] . ''. $armpSectionsUrlTemplate .'" />'));

        }

    }

    public function createAMPSectionUrl($elementID,$fieldCode = 'ID'){

        global $APPLICATION;

        $detailURLTemplate = $this->arParams['AMP_SECTION_URL'];

        if(!empty($detailURLTemplate)){

            $arSelect = Array('SECTION_PAGE_URL');
            $arFilter = Array($fieldCode => trim($elementID));

            if(!empty(static::$iblock_id)){
                $arFilter['IBLOCK_ID'] = static::$iblock_id;
            }

            $dbRes = CIBlockSection::GetList(Array(), $arFilter, false, false, $arSelect);

            $dbRes->SetUrlTemplates($detailURLTemplate);

            if($dbRes && ($arFields = $dbRes->GetNext())){

                $APPLICATION->AddHeadString('<link rel="amphtml" href="'.((CMain::IsHTTPS() ? 'https' : 'http') . '://' . $_SERVER['SERVER_NAME'] . ''. $arFields['DETAIL_PAGE_URL'] .'" />'));

            }

        }
    }

    public function executeComponent()
    {

        $componentPage = static::checkPageComponent();

        switch ($componentPage){
            case 'detail':
            case 'element':

                if(!empty(static::returnRealPath())
                    && (static::$elementId || static::$elementCode)
                ){

                    if(static::$elementId){
                        $this->createAMPElementUrl(static::$elementId);
                    } else {
                        $this->createAMPElementUrl(static::$elementCode,'CODE');
                    }

                }

                break;

            case 'news':
            case 'sections':



                if(!empty(static::returnRealPath())
                    && (static::$sectionId || static::$sectionCode)
                ){

                    if(static::$sectionId){
                        $this->createAMPSectionUrl(static::$sectionId);
                    } else {
                        $this->createAMPSectionUrl(static::$sectionCode,'CODE');
                    }

                } else {
                    $this->createAMPSectionsUrl();
                }

                break;

			case 'news.list':
            case 'section':

                if(!empty(static::returnRealPath())
                    && (static::$sectionId || static::$sectionCode)
                ){

                    if(static::$sectionId){
                        $this->createAMPSectionUrl(static::$sectionId);
                    } else {
                        $this->createAMPSectionUrl(static::$sectionCode,'CODE');
                    }

                } else {
					$this->createAMPSectionsUrl();
                }


                break;
        }


    }



}