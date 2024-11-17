<?php

//https://dev.youtwig.ru/local/crontab/clear_models.php?intestwetrust=1
//https://youtwig.ru/local/crontab/clear_models.php?intestwetrust=1
$iMaxString = 20;

if(!isset($_REQUEST['intestwetrust'])){

    ?>
    <html>
    <head>
        <title>Чистим модели</title>
        <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
    </head>
    <body>
    <button id="request">Запустить</button>
    <input type="number" id="skip" value="0" />
    <div id="result"></div>
    <script>
        //<!--

        updInterval = false;
        inUpd = false;
        iskip = 0;
        iNum = 0;

        $("#request").on("click",function(){

            inUpd = false;
            iNum = 0;

            if(updInterval){
                clearInterval(updInterval);
            }

            updInterval = setInterval(function(){

                if(!inUpd){

                    iskip = $("#skip").val();

                    inUpd = true;

                    $.ajax({
                        url: "/local/crontab/clear_models.php?intestwetrust=1&skip="+iskip
                    }).done(function(iData) {

                        iNum += <?php echo $iMaxString;?>;

                        $("#result").html(iData + ':' + iNum);

                        if(iData != "done"){
                            if(!isNaN(iData))
                                $("#skip").val(iData);
                            inUpd = false;
                        } else {
                            clearInterval(updInterval);
                        }

                    }).fail(function(){
                        $("#result").html("Ошибка...");
                        inUpd = false;
                    });

                }

            },200);

        });

        //-->
    </script>

    </body>
    </html>
    <?

    die();
}

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

$bSkipMan = false;

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('iblock');

$iSkipProdId = 0;

$arProdFilter = Array(
    "CODE" => "bez_tovara",
    "IBLOCK_ID" => 11
);

$arProdSelect = Array("ID");

$resProdDB = CIBlockElement::GetList(Array(), $arProdFilter, false, false, $arProdSelect);

$resProdArr = Array();

if($resProdDB) {

    $resProdArr = $resProdDB->GetNext();

    if(isset($resProdArr['ID'])
        && !empty($resProdArr['ID'])){

        $iSkipProdId = $resProdArr['ID'];

    }
}

$oModelEl = new CIBlockElement;

$aFilter = array(
    'IBLOCK_ID' => 17,
    '!PROPERTY_POSITION' => false
);

$iSkip = isset($_REQUEST['skip']) && !empty($_REQUEST['skip']) ? (int)$_REQUEST['skip'] : 0;
$iSkip = trim($iSkip);
$iSkip = (int)$iSkip;
$iSkip = !is_numeric($iSkip) ? 0 : $iSkip;

if($iSkip > 0){
    $aFilter['>ID'] = $iSkip;
};

$aOrder = Array("ID" => "asc");
$aSelect = array('ID');
$iModelId = 0;

$rDB = CIBlockElement::GetList(
    $aOrder,
    $aFilter,
    false,
    ($aNavs = array(
        'nTopCount' => $iMaxString
    )),
    $aSelect);

$indexes = array();

if($rDB)
    while($aFields = $rDB->GetNext()){

        $aPos = array();
        $aIndcode = array();
        $aComcode = array();
        $aViews = array();
        $aProducts = array();

        $aModelPosFilter = Array("CODE" => "POSITION");

        $rPosModel = CIBlockElement::GetProperty(
            17,
            $aFields["ID"],
            array(),
            $aModelPosFilter
        );

        if ($rPosModel) {

            while ($aPosModel = $rPosModel->GetNext()) {

                if (isset($aPosModel['VALUE'])
                    && !empty($aPosModel['VALUE'])) {

                    $aPos[] = $aPosModel['VALUE'];

                }

            }

        }

        if(!empty($aPos)){


            $aModelIndcodeFilter = Array("CODE" => "INDCODE");

            $rIndcodeModel = CIBlockElement::GetProperty(
                17,
                $aFields["ID"],
                array(),
                $aModelIndcodeFilter
            );

            if ($rIndcodeModel) {

                while ($aIndcodeModel = $rIndcodeModel->GetNext()) {

                    if (isset($aIndcodeModel['VALUE'])
                        && !empty($aIndcodeModel['VALUE'])
                    ) {

                        $aIndcode[] = $aIndcodeModel['VALUE'];

                    }

                }

            }

            $aModelViewsFilter = Array("CODE" => "VIEW");
            $rViewsModel = CIBlockElement::GetProperty(
                17,
                $aFields["ID"],
                array(),
                $aModelViewsFilter
            );

            if ($rViewsModel) {

                while ($aViewsModel = $rViewsModel->GetNext()) {

                    if (isset($aViewsModel['VALUE'])
                        && !empty($aViewsModel['VALUE'])
                    ) {

                        $aViews[] = $aViewsModel['VALUE'];

                    }

                }

            }

            $rProductsModel = CIBlockElement::GetProperty(
                17,
                $aFields["ID"],
                array("sort" => "asc"),
                Array("CODE" => "products")
            );

            if($rProductsModel){

                while($aProductsModel = $rProductsModel->GetNext()){

                    if(isset($aProductsModel['VALUE'])
                        && !empty($aProductsModel['VALUE'])
                    ){
                        $aProducts[] = $aProductsModel['VALUE'];
                    }
                }

            }

            $aUniques = array();

            foreach($aPos as $pNum => $pValue){

                if(isset($aProducts[$pNum])
                    && !empty($aProducts[$pNum])
                    && isset($aViews[$pNum])
                    && !empty($aViews[$pNum])
                    && isset($aIndcode[$pNum])
                    && !empty($aIndcode[$pNum])
                    && $iSkipProdId != $aProducts[$pNum]
                ){

                    $suKey = $aProducts[$pNum].';'.$aViews[$pNum].';'.$aIndcode[$pNum].';'.$aPos[$pNum];

                    if(!isset($aUniques[$suKey])){
                        $aUniques[$suKey] = array(
                            'products' => $aProducts[$pNum],
                            'VIEW' => $aViews[$pNum],
                            'INDCODE' => $aIndcode[$pNum],
                            'POSITION' => $aPos[$pNum]
                        );
                    }

                }

            }

            $aFieldProps = array(
                'products' => array(),
                'VIEW' => array(),
                'INDCODE' => array(),
                'POSITION' => array(),
                'COMCODE' => false
            );

            foreach($aUniques as $aUnique){

                $aFieldProps['products'][] = array('VALUE' => $aUnique['products'], 'DESCRIPTION' => '');
                $aFieldProps['POSITION'][] = array('VALUE' => $aUnique['POSITION'], 'DESCRIPTION' => '');
                $aFieldProps['INDCODE'][] = array('VALUE' => $aUnique['INDCODE'], 'DESCRIPTION' => '');
                $aFieldProps['VIEW'][] = array('VALUE' => $aUnique['VIEW'], 'DESCRIPTION' => '');

            }

            if(!empty($aFieldProps)){

                CIBlockElement::SetPropertyValuesEx($aFields['ID'], 17, $aFieldProps);
				//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $aFields['ID']);
                if ($oModelEl->Update($aFields['ID'], Array('TIMESTAMP_X' => true))) {

                };

            }

            $iModelId = $aFields['ID'];

        }


    }

if(!empty($iModelId)){
    echo $iModelId;
} else {
    echo 'done';
}