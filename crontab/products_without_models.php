<?

//https://youtwig.ru/local/crontab/products_without_models.php?intestwetrust=1

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);
define("__MAX_MODELS",500);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");


global $USER;

$chained_products = array();

if(CModule::IncludeModule("iblock")){

    $countStrings = 100;

    $skip = isset($_REQUEST['skip'])
    && !empty($_REQUEST['skip'])
        ? (int)$_REQUEST['skip']
        : 0;

    if($skip == 0){
        $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/empty_models.csv','w+');
    } else {
        $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/empty_models.csv','a+');
    }

    $subRange = array(
        "nPageSize" => $countStrings,
        "iNumPage" => $skip,
        "bShowAll" => false
    );

    $arPSelect = Array(
        "ID",
        "IBLOCK_ID",
        "PROPERTY_products",
        "NAME",
        "DETAIL_TEXT"
    );

    $arPFilter = Array(
        "IBLOCK_ID" => 37,
        //"ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y",
        "DETAIL_TEXT" => false
        //"!PROPERTY_HIDE_MODELS" => 56322
    );

    $countAllModels = CIBlockElement::GetList(($order = Array("ID" => "DESC")), $arPFilter, array(), false, array('ID'));

    if(($countStrings * $skip)
        <= $countAllModels) {

        $dbPres = CIBlockElement::GetList(($order = Array("ID" => "DESC")), $arPFilter, false, $subRange, $arPSelect);

        if ($dbPres) {

            while ($arResult = $dbPres->GetNext()) {


                $iProdId = $arResult['PROPERTY_PRODUCTS_VALUE'];

                $iCnt = CIBlockElement::GetList(
                    [],
                    ['ACTIVE' => 'Y', 'IBLOCK_ID' => 11, 'ID' => $iProdId],
                    [],
                    false,
                    ['ID']
                );

                if ($iCnt > 0) {
                    fputcsv($fp,array($arResult['PROPERTY_PRODUCTS_VALUE'],$arResult['NAME'],'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=11&type=catalog&ID='.$arResult['PROPERTY_PRODUCTS_VALUE'].'&lang=ru&find_section_section=&WF=Y'),";");
                }

            }

        }

        ++$skip;
        fclose($fp);

        echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/products_without_models.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(150,300).');</script></header></html>';



    } else {

        fclose($fp);
        die('done');

    }


}
