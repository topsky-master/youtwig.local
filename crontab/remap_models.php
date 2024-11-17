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

if(CModule::IncludeModule("iblock")){

    $countStrings = 200;

    $skip = isset($_REQUEST['skip'])
    && !empty($_REQUEST['skip'])
        ? (int)$_REQUEST['skip']
        : 0;

    if($skip == 0){
        $_SESSION['mdcount'] = array();
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
        "PROPERTY_manufacturer_VALUE" => array('Indesit', 'Hotpoint-Ariston', 'Ariston' , 'Whirlpool', 'Hotpoint', 'Stinol')
    );

    $countAllModels = CIBlockElement::GetList(($order = Array("ID" => "DESC")), $arPFilter, array(), false, array('ID'));

    if(($countStrings * $skip)
        <= $countAllModels) {

        $dbPres = CIBlockElement::GetList(($order = Array("ID" => "DESC")), $arPFilter, false, $subRange, $arPSelect);

        if ($dbPres) {

            while ($arResult = $dbPres->GetNext()) {

                if(isset($arResult["PROPERTY_MODEL_NEW_LINK_VALUE"])
                    && !empty($arResult["PROPERTY_MODEL_NEW_LINK_VALUE"])){

                    $dbMNNres = CIBlockElement::GetByID($arResult["PROPERTY_MODEL_NEW_LINK_VALUE"]);

                    if ($dbMNNres
                        && $arMNNRes = $dbMNNres->GetNext()) {

                        $model = trim($arMNNRes['NAME']);
                        $model = preg_replace('~[\s\)\(]+~isu', '', $model);
                        $model = trim($model, "/()\\-\t\n\r\0\x0B");

                        if(!isset($_SESSION['mdcount'][$model]))
                            $_SESSION['mdcount'][$model] = 0;
                        else
                            ++$_SESSION['mdcount'][$model];

                        $model = trim(CUtil::translit($model, LANGUAGE_ID, $tParams));

                        CIBlockElement::SetPropertyValuesEx($arMNNRes['ID'], 27, array('ALT_NAME' => $model));

                    }

                }

            }

        }

        ++$skip;

        echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/remap_models.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(150,300).');</script></header></html>';


    } else {

        $_SESSION['mdcount'] = array_filter($_SESSION['mdcount']);

        echo sizeof($_SESSION['mdcount']);
        echo 'done';

    }



}
