<?

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$updateEl = new CIBlockElement;

if(CModule::IncludeModule("iblock")){

    $countStrings = 20;

    $skip = isset($_REQUEST['skip'])
    && !empty($_REQUEST['skip'])
        ? (int)$_REQUEST['skip']
        : 0;

    if($skip == 0){

        $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/link_doubles.csv','w+');
        $fp1 = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/check_doubles.csv','w+');

    } else {

        $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/link_doubles.csv','a+');
        $fp1 = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/check_doubles.csv','a+');
    }

    $subRange = array(
        "nPageSize" => $countStrings,
        "iNumPage" => $skip,
        "bShowAll" => false
    );

    $arMNSelect = Array(
        "ID",
        "IBLOCK_ID",
        "PROPERTY_ALT_NAME",
        "ACTIVE"
    );

    $arMNFilter = Array(
        "IBLOCK_ID" => 27,
        "!PROPERTY_ALT_NAME" => false
    );

    $countAllModels = CIBlockElement::GetList(($order = Array("ID" => "DESC")), $arMNFilter, array(), false, array('ID'));

    if(($countStrings * $skip)
        <= $countAllModels) {

        $dbMNRes = CIBlockElement::GetList(($order = Array("ID" => "DESC")), $arMNFilter, false, $subRange, $arMNSelect);

        if ($dbMNRes) {

            while ($arMNResult = $dbMNRes->GetNext()) {

                if(isset($arMNResult["PROPERTY_ALT_NAME_VALUE"])
                    && !empty($arMNResult["PROPERTY_ALT_NAME_VALUE"])
                    && isset($arMNResult["ACTIVE"])
                    && !empty($arMNResult["ACTIVE"])
                    && $arMNResult["ACTIVE"] == "Y"){

                    $arCMNSelect = Array(
                        "ID",
                        "NAME"
                    );

                    $arCMNFilter = Array(
                        "IBLOCK_ID" => 27,
                        "PROPERTY_ALT_NAME" => $arMNResult["PROPERTY_ALT_NAME_VALUE"]
                    );

                    $dbcMNRes = CIBlockElement::GetList(($order = Array("TIMESTAMP_X" => "DESC")), $arCMNFilter, false, false, $arCMNSelect);

                    $subIDs = array();
                    $subNames = array();

                    if ($dbcMNRes) {

                        while ($arcMNResult = $dbcMNRes->GetNext()) {

                            if(isset($arcMNResult['ID'])
                                && !empty($arcMNResult['ID'])){
                                $subIDs[] = $arcMNResult['ID'];

                            }
                        }

                    }

                    if(sizeof($subIDs) > 1){



                        $arRMFilter = Array(
                            "IBLOCK_ID" => 17,
                            "PROPERTY_model_new_link" => $subIDs
                        );


                        $arRMSelect = array(
                            "DETAIL_PAGE_URL",
                            "ID",
                            "NAME",
                            "PROPERTY_MODEL_NEW_LINK"
                        );

                        $dbRMRes = CIBlockElement::GetList(
                            ($order = Array("TIMESTAMP_X" => "DESC")),
                            $arRMFilter,
                            false,
                            false,
                            $arRMSelect);

                        if ($dbRMRes) {

                            $subCount = 0;
                            $where = '';

                            while($arRMResult = $dbRMRes->GetNext()){

                                if(empty($where)){
                                    $where = $arRMResult['DETAIL_PAGE_URL'];
                                } else {
                                    $from = $arRMResult['DETAIL_PAGE_URL'];

                                    if(!empty($from)
                                        && !empty($where))
                                        fputcsv($fp,array($from,$where),';');

                                }

                                if($subCount != 0){

                                    $updateEl->Update($arRMResult["ID"],
                                        array(
                                            "ACTIVE" => "N",
                                            "TIMESTAMP_X" => false
                                        )
                                    );

                                }

                                ++$subCount;


                                fputcsv($fp1,array($arRMResult['ID'],$arRMResult['NAME'],$arRMResult['DETAIL_PAGE_URL'],$arRMResult["PROPERTY_MODEL_NEW_LINK_VALUE"]),';');

                            }

                            $firstId = array_shift($subIDs);

                            foreach ($subIDs as $subID){
                                $updateEl->Update($subID,
                                    array(
                                        "ACTIVE" => "N",
                                        "TIMESTAMP_X" => false
                                    )
                                );
                            }



                        }

                    }

                }

            }

        }


        ++$skip;

        fclose($fp);
        fclose($fp1);

        echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/remove_duplicates.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(150,300).');</script></header></html>';


    } else {

        fclose($fp);
        fclose($fp1);

        echo 'done';

    }



}
