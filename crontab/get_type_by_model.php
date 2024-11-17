<?php

//https://youtwig.ru/local/crontab/get_type_by_model.php
//тип продукта;производитель;модель;товар;

$maxCount = 5000;

if(!isset($_REQUEST['intestwetrust'])){

    ?>
    <html>
    <head>
        <title>Собираем файл для обновления моделей</title>
        <script
                src="https://code.jquery.com/jquery-3.4.1.min.js"
                integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
                crossorigin="anonymous"></script>
    </head>
    <body>
    <button id="request">Запустить</button>
	<ol>
		<li>Залить файл /bitrix/tmp/indesit_models.csv, каждая строка: это название модеи из иб 27</li>
		<li>В результате работы получаем csv файл: /bitrix/tmp/indesit_modeltypes.csv модель;тип товара;производитель из иб 17</li>
	</ol>
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
                        url: "/local/crontab/get_type_by_model.php?intestwetrust=1&skip="+iskip
                    }).done(function(iData) {

                        iNum += <?php echo $maxCount;?>;

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
    <?php

    die();
}

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_types_last.txt')){
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_types_last.txt',0);
}

$skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_types_last.txt'));

$currentCount = 0;

$fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_models.csv','r');

if(empty($skip)){
    $fp1 = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_modeltypes.csv','w+');
} else {
    $fp1 = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_modeltypes.csv','a+');
}

if($fp){

    $arModelNameSelect = Array(
        "ID",
        "NAME"
    );

    $arModelNameFilter = Array(
        "IBLOCK_ID" => 27,
        "ACTIVE" => "Y"
    );

    $lines = file(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_models.csv');
    $lines = array_slice($lines,$skip,$maxCount);
    $lines = array_map('trim',$lines);

    foreach($lines as $modelName){

        ++$currentCount;
        $types = array();

        if(!empty($modelName)){

            $arModelNameFilter['=NAME'] = trim($modelName);

            $resModelName = CIBlockElement::GetList(
                ($order = Array('ID' => 'DESC')),
                $arModelNameFilter,
                false,
                false,
                $arModelNameSelect
            );

            if(!$resModelName) {
                $arModelNameFilter['=NAME'] = trim($modelName);

                $resModelName = CIBlockElement::GetList(
                    ($order = Array('ID' => 'DESC')),
                    $arModelNameFilter,
                    false,
                    false,
                    $arModelNameSelect
                );
            }

            if($resModelName){

                $typeProduct = '';

                while($arModelNameFields = $resModelName->GetNext()){

                    if(isset($arModelNameFields['ID'])
                        && !empty($arModelNameFields['ID'])){

                        $arModelSelect = array(
                            'ID',
                            'PROPERTY_type_of_product',
                            'PROPERTY_manufacturer',

                        );

                        $arModelFilter = array(
                            'IBLOCK_ID' => 17,
                            'PROPERTY_model_new_link' => $arModelNameFields['ID'],
                            'ACTIVE' => 'Y'
                        );

                        $resModel = CIBlockElement::GetList(
                            ($order = Array('ID' => 'DESC')),
                            $arModelFilter,
                            false,
                            false,
                            $arModelSelect
                        );

                        if($resModel) {

                            while ($arModelFields = $resModel->GetNext()) {

                                if(isset($arModelFields['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                                    && !empty($arModelFields['PROPERTY_TYPE_OF_PRODUCT_VALUE'])){

                                    $typeProduct = $arModelFields['PROPERTY_TYPE_OF_PRODUCT_VALUE'];
                                    $manProduct = $arModelFields['PROPERTY_MANUFACTURER_VALUE'];

                                    if(!isset($types[$typeProduct])){
                                        $types[$typeProduct] = array();
                                    }

                                    if(!in_array($manProduct,$types[$typeProduct])){
                                        $types[$typeProduct][] = $manProduct;
                                    }

                                }

                            }

                        }

                    }

                }

                if(!empty($types)){

                    foreach($types as $type => $manufacturers){

                        foreach($manufacturers as $manufacturer){

                            fputcsv($fp1,array($modelName,$type,$manufacturer),";");

                        }

                    }
                }

            }

        }

    }

}

fclose($fp);
fclose($fp1);


if(!empty($currentCount)){
    $skip += $currentCount;
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_types_last.txt', $skip);
    echo $currentCount;
    die();
} else {
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/indesit_types_last.txt', 0);
    echo 'done';
    die();
}
