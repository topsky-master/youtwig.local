<?

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");


global $USER;

$same_is_found = array();

if(CModule::IncludeModule("iblock")){

    $countStrings = 1;

    $skip = isset($_REQUEST['skip'])
    && !empty($_REQUEST['skip'])
        ? (int)$_REQUEST['skip']
        : 0;

    if($skip == 0){
        $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/double_models.csv','w+');
    } else {
        $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/double_models.csv','a+');
    }

    $subRange = array(
        "nPageSize" => $countStrings,
        "iNumPage" => $skip,
        "bShowAll" => false
    );

    $arPSelect = Array(
        "ID",
        "IBLOCK_ID",
        "PROPERTY_MODEL",
        "NAME"
    );

    $arPFilter = Array(
        "IBLOCK_ID" => 11,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y",
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

                if ($dbMres) {

                    $models_html_arr = array();
                    $models_arr = array();

                    while ($arMRes = $dbMres->GetNext()) {

                        $dbMNNres = CIBlockElement::GetByID($arMRes["PROPERTY_MODEL_NEW_LINK_VALUE"]);

                        if ($dbMNNres
                            && $arMNNRes = $dbMNNres->GetNext()) {


                            if(!isset($same_is_found[$arMNNRes['NAME']])){

                                $same_is_found[$arMNNRes['NAME']] = array();

                            }

                            $same_is_found[$arMNNRes['NAME']][] = array($arResult['NAME'],$arResult['ID'],$arMRes["NAME"],$arMRes["ID"],$arMNNRes['NAME'],$arMNNRes['ID']);


                        }

                    }

                }

            }

        }

        ++$skip;

        foreach($same_is_found as $modelArray){
            if(sizeof($modelArray) > 1){
                foreach($modelArray as $models){
                    fputcsv($fp,$models,";");
                }
            }
        }

        fclose($fp);
        echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/double_models.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(200,500).');</script></header></html>';


    } else {

        fclose($fp);
        echo 'done';

    }



}
