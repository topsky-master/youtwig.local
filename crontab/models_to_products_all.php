<?

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);
define("__MAX_MODELS",500);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");


global $USER;

$chained_products = array();

if(CModule::IncludeModule("iblock")){

    $countStrings = 1;

    $skip = isset($_REQUEST['skip'])
    && !empty($_REQUEST['skip'])
        ? (int)$_REQUEST['skip']
        : 0;

    $subRange = array(
        "nPageSize" => $countStrings,
        "iNumPage" => $skip,
        "bShowAll" => false
    );

    $arPSelect = Array(
        "ID",
        "IBLOCK_ID",
        "PROPERTY_MODEL",
        "PROPERTY_HIDE_MODELS"
    );

    $arPFilter = Array(
        "IBLOCK_ID" => 11,
        "ACTIVE_DATE" => "Y",

        "ACTIVE" => "Y",
        "ID" => $_SESSION["MODELS_TO_PRODUCTS"]
    );

    $countAllModels = CIBlockElement::GetList(($order = Array("ID" => "DESC")), $arPFilter, array(), false, array('ID'));

    if(($countStrings * $skip)
        <= $countAllModels) {

        $dbPres = CIBlockElement::GetList(($order = Array("ID" => "DESC")), $arPFilter, false, $subRange, $arPSelect);

        if ($dbPres) {

            while ($arResult = $dbPres->GetNext()) {

                $models = "";
                $models_html = "";

                $arMSelect = Array(
                    "ID",
                    "PROPERTY_model_new_link",
                    "DETAIL_PAGE_URL",
                    "NAME",
                    "PROPERTY_manufacturer"
                );

                $arMFilter = Array(
                    "IBLOCK_ID" => 17,
                    "ACTIVE_DATE" => "Y",
                    "ACTIVE" => "Y",
                    "PROPERTY_products" => $arResult['ID'],
                );

                $dbMres = CIBlockElement::GetList(Array(), $arMFilter, false, false, $arMSelect);

                $models_html_arr = array();
                $models_arr = array();

                if ($dbMres
                    && ($arResult["PROPERTY_HIDE_MODELS_VALUE"] != "Скрыть")) {

                    $count = 0;


                    while ($arMRes = $dbMres->GetNext()) {

                        $dbMNNres = CIBlockElement::GetByID($arMRes["PROPERTY_MODEL_NEW_LINK_VALUE"]);

                        if ($dbMNNres
                            && $arMNNRes = $dbMNNres->GetNext()) {

                            ++$count;

                            $models_html .= $arMRes['DETAIL_PAGE_URL'] . "\n";
                            $models .= trim($arMRes["PROPERTY_MANUFACTURER_VALUE"] . ' ' . $arMNNRes['NAME']) . "\n";

                            if ($count == __MAX_MODELS) {
                                $models_html_arr[] = array('VALUE' => array('TEXT' => $models_html));
                                $models_arr[] = array('VALUE' => array('TEXT' => $models));
                                $models_html = '';
                                $models = '';
                                $count = 0;
                            }

                        }

                    }

                    if ($count > 0 && $count != __MAX_MODELS) {
                        $models_html_arr[] = array('VALUE' => array('TEXT' => $models_html));
                        $models_arr[] = array('VALUE' => array('TEXT' => $models));
                    }



                }

                CIBlockElement::SetPropertyValuesEx(
                    $arResult["ID"],
                    $arResult["IBLOCK_ID"],
                    array('MODEL' => empty($models_arr) ? false : $models_arr)
                );
				
				//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($arResult["IBLOCK_ID"], $arResult["ID"]);
				
                CIBlockElement::SetPropertyValuesEx(
                    $arResult["ID"],
                    $arResult["IBLOCK_ID"],
                    array('MODEL_HTML' => empty($models_html_arr) ? false : $models_html_arr)
                );

                \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($arResult["IBLOCK_ID"], $arResult["ID"]);


            }

        }

        ++$skip;
        echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/models_to_products_all.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(150,300).');</script></header></html>';


    } else {

        unset($_SESSION["MODELS_TO_PRODUCTS"]);
        die('done');

    }

}
