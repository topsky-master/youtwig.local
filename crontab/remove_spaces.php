<?

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");



global $USER;

$updateEl = new CIBlockElement;

if(CModule::IncludeModule("iblock")){

    $countStrings = 200;

    $skip = isset($_REQUEST['skip'])
    && !empty($_REQUEST['skip'])
        ? (int)$_REQUEST['skip']
        : 0;

    if($skip == 0){
        $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/withspaces.csv', 'w+');
    } else {
        $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/withspaces.csv', 'a+');
    }

    $subRange = array(
        "nPageSize" => $countStrings,
        "iNumPage" => $skip,
        "bShowAll" => false
    );

    $arPSelect = Array(
        "ID",
        "IBLOCK_ID",
        "PROPERTY_model_new_link",
        "PROPERTY_manufacturer",
        "PROPERTY_type_of_product",
        "NAME"
    );

    $arPFilter = Array(
        "IBLOCK_ID" => 17,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y",
        "PROPERTY_manufacturer_VALUE" => array('Indesit', 'Hotpoint-Ariston', 'Ariston' , 'Whirlpool', 'Hotpoint', 'Bosch', 'Stinol')
    );

    $countAllModels = CIBlockElement::GetList(($order = Array("ID" => "DESC")), $arPFilter, array(), false, array('ID'));

    if(($countStrings * $skip)
        <= $countAllModels) {

        $dbPres = CIBlockElement::GetList(($order = Array("ID" => "DESC")), $arPFilter, false, $subRange, $arPSelect);

        if ($dbPres) {

            while ($arResult = $dbPres->GetNext()) {

                if(isset($arResult["PROPERTY_MODEL_NEW_LINK_VALUE"])
                    && !empty($arResult["PROPERTY_MODEL_NEW_LINK_VALUE"])){

                    $model = '';

                    $dbMNNres = CIBlockElement::GetByID($arResult["PROPERTY_MODEL_NEW_LINK_VALUE"]);

                    if ($dbMNNres
                        && $arMNNRes = $dbMNNres->GetNext()) {

                        $model = trim($arMNNRes['NAME']);
                        $model = preg_replace('~(?<!\))\s+(?!\()~isu', '', $model);
                        $model = trim($model, "/\\-\t\n\r\0\x0B");

                        if($model != $arMNNRes['NAME']) {

                            fputcsv($fp,array($arMNNRes['NAME'], $model), ';');

                            $updateEl->Update($arMNNRes['ID'],
                                array(
                                    'NAME' => $model,
                                    'TIMESTAMP_X' => false)
                            );

                        } else {
                            $model = '';
                        }

                    }

                    if(!empty($model)
                        && isset($arResult["PROPERTY_TYPE_OF_PRODUCT_VALUE"])
                        && !empty($arResult["PROPERTY_TYPE_OF_PRODUCT_VALUE"])
                        && isset($arResult["PROPERTY_MANUFACTURER_VALUE"])
                        && !empty($arResult["PROPERTY_MANUFACTURER_VALUE"])){

                        $modelName = $arResult["PROPERTY_TYPE_OF_PRODUCT_VALUE"]
                            .' '.$arResult["PROPERTY_MANUFACTURER_VALUE"]
                            .' '.$model;

                        $updateEl->Update($arResult['ID'],
                            array(
                                'NAME' => $modelName,
                                'TIMESTAMP_X' => false)
                        );

                    }


                }

            }

        }

        ++$skip;

        fclose($fp);

        echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/remove_spaces.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(150,300).');</script></header></html>';


    } else {

        fclose($fp);

        echo 'done';

    }



}
