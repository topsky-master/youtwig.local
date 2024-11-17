<?

die();

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");


global $USER;

$chained_products = array();

if(CModule::IncludeModule("iblock")){

    $arPSelect = Array(
        "ID",
        "IBLOCK_ID",
        "PROPERTY_MODEL"
    );

    $arPFilter = Array(
        "IBLOCK_ID" => 11,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    );

    $dbPres = CIBlockElement::GetList(($order = Array("timestamp_x" => "ASC")), $arPFilter, false, false, $arPSelect);

    if($dbPres){

        while($arResult = $dbPres->GetNext()){

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

            if($dbMres){

                $count = 0;
                $models_html_arr = array();
                $models_arr = array();

                while($arMRes = $dbMres->GetNext()){

                    $dbMNNres = CIBlockElement::GetByID($arMRes["PROPERTY_MODEL_NEW_LINK_VALUE"]);

                    if($dbMNNres
                        && $arMNNRes = $dbMNNres->GetNext()){

                        ++$count;

                        $models_html .= $arMRes['DETAIL_PAGE_URL']."\n";
                        $models .= trim($arMRes["PROPERTY_MANUFACTURER_VALUE"].' '.$arMNNRes['NAME'])."\n";

                        if($count == 1000){
                            $models_html_arr[] = array('VALUE' => array('TEXT' => $models_html));
                            $models_arr[] = array('VALUE' => array('TEXT' => $models));
                            $models_html = '';
                            $models = '';
                            $count = 0;
                        }

                    }

                }

                if($count > 0 && $count != 1000){
                    $models_html_arr[] = array('VALUE' => array('TEXT' => $models_html));
                    $models_arr[] = array('VALUE' => array('TEXT' => $models));
                }


                if(!empty($models)){

                    CIBlockElement::SetPropertyValuesEx(
                        $arResult["ID"],
                        $arResult["IBLOCK_ID"],
                        array('MODEL' => $models_arr)
                    );

					//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($arResult["IBLOCK_ID"], $arResult["ID"]);

                    CIBlockElement::SetPropertyValuesEx(
                        $arResult["ID"],
                        $arResult["IBLOCK_ID"],
                        array('MODEL_HTML' => $models_html_arr)
                    );

                    \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($arResult["IBLOCK_ID"], $arResult["ID"]);

                }

            }

        }

    }

}
