<?

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$updateEl = new CIBlockElement;

$tParams = Array(
    "max_len" => "500",
    "change_case" => "L",
    "replace_space" => "",
    "replace_other" => ""
);

$skipTypes = array('Подшипник');

if(CModule::IncludeModule("iblock")){


    $countStrings = 200;

    $skip = isset($_REQUEST['skip'])
    && !empty($_REQUEST['skip'])
        ? (int)$_REQUEST['skip']
        : 0;

    if($skip == 0){
        $_SESSION['mdcount'] = array();
        $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/doubles_by_type.csv','wb+');
    } else {
        $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/doubles_by_type.csv','ab+');
    }

    $subRange = array(
        "nPageSize" => $countStrings,
        "iNumPage" => $skip,
        "bShowAll" => false
    );

    $arPSelect = Array(
        "ID",
        "IBLOCK_ID",
        "NAME"
    );

    $arPFilter = Array(
        "IBLOCK_ID" => 17,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    );

    $countAllModels = CIBlockElement::GetList(($order = Array("ID" => "DESC")), $arPFilter, array(), false, array('ID'));

    if(($countStrings * $skip)
        <= $countAllModels) {

        $dbPres = CIBlockElement::GetList(($order = Array("ID" => "DESC")), $arPFilter, false, $subRange, $arPSelect);

        if ($dbPres) {

            while ($arResult = $dbPres->GetNext()) {

                $hasDoubles = false;
                $product_types = Array();

                $dbModelProducts = CIBlockElement::GetProperty(
                        $arResult['IBLOCK_ID'],
                        $arResult['ID'],
                        Array("sort" => "asc"),
                        Array("CODE" => "products")
                );

                if($dbModelProducts)
                while($arModelProducts = $dbModelProducts->GetNext()){

                    $modelProductId = isset($arModelProducts['VALUE'])
                            && !empty($arModelProducts['VALUE'])
                            ? (int)trim($arModelProducts['VALUE'])
                            : 0;

                    if(!empty($modelProductId)){

                        $dbCatalogProductType = CIBlockElement::GetProperty(
                            11,
                            $modelProductId,
                            Array("sort" => "asc"),
                            Array("CODE" => "TYPEPRODUCT")
                        );

                        if($dbCatalogProductType){

                            while($arCatalogProductType = $dbCatalogProductType->GetNext()){

                                $productType = isset($arCatalogProductType['VALUE_ENUM'])
                                    &&!empty($arCatalogProductType['VALUE_ENUM'])
                                    ? trim($arCatalogProductType['VALUE_ENUM'])
                                    : '';

                                if(!in_array($productType,$skipTypes)
                                    && in_array($productType,$product_types)){

                                    $hasDoubles = true;

                                }

                                $product_types[] = $productType;

                            }

                        }

                    }

                }

                if($hasDoubles){

                    array_unshift($product_types,$arResult['NAME'],$arResult['ID']);
                    fputcsv($fp,$product_types,";");

                }

            }

        }

        ++$skip;
        fclose($fp);

        echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/check_doubles_by_type.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(150,300).');</script></header></html>';


    } else {

        $_SESSION['mdcount'] = array_filter($_SESSION['mdcount']);
        fclose($fp);

        echo sizeof($_SESSION['mdcount']);
        echo 'done';

    }


}
