<?php

die();

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;


$countStrings = 10000;
$currentCount = 0;
$skip = (int)$_REQUEST['skip'];
$max = 2000;
$count = 0;

/*

if(CModule::IncludeModule("iblock")){

    CIBlockProperty::Add(
        Array(
            'IBLOCK_ID' => '17',
            'NAME' => 'Новая модель',
            'ACTIVE' => 'Y',
            'CODE' => 'model_new_link',
            'PROPERTY_TYPE' => 'E',
          )
    );

};

if(CModule::IncludeModule("iblock")){


            $dbRes = CIBlockPropertyEnum::GetList(
				array(),
				array(
					"PROPERTY_ID" => 90,
                    "IBLOCK_ID" => 17,
                )
			);

            $el = new CIBlockElement;

			while($arRes = $dbRes->Fetch())
			{
                $arModelArray = Array(
                  "MODIFIED_BY"    => $USER->GetID(),
                  "IBLOCK_SECTION_ID" => false,
                  "IBLOCK_ID"      => 27,
                  "NAME"           => $arRes['VALUE'],
                  "ACTIVE"         => "Y",
                  "PREVIEW_TEXT"   => " ",
                  "DETAIL_TEXT"    => " ",
                );

                $el->Add($arModelArray);

			}

}

if(CModule::IncludeModule("iblock")){

    CIBlockProperty::Delete(90);

}

*/

if(CModule::IncludeModule("iblock")){

    $dbRes = CIBlockElement::GetList(
        array(),
        array(
            "IBLOCK_ID" => 27
        ),
        false,
        false,
        array(
            'ID',
            'NAME'
        )
    );

    $tryFindCopies = array();

    if($dbRes){
        while($arRes = $dbRes->GetNext()){

            $tryName = preg_replace('~[\s\-\\\/\_\(\)]+~is','',$arRes['NAME']);
            $tryName = mb_strtolower($tryName);

            if(!isset($tryFindCopies[$tryName])){
                $tryFindCopies[$tryName] = array();

            }

            $tryFindCopies[$tryName][$arRes['NAME']][] = $arRes['ID'];

        }

    }

}

$fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/copies.csv','wb');


foreach($tryFindCopies as $name => $copy){
    if(sizeof($copy) > 1){
        $suggests = array_keys($copy);
        $string = array_merge(array($name),$suggests);
        fputcsv($fp,$string,';');
    }
}

fclose($fp);