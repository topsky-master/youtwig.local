<?php

die();

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$countStrings = 60;
$currentCount = 0;
$skip = isset($_REQUEST['skip']) && !empty($_REQUEST['skip']) ? (int)$_REQUEST['skip'] : 0;

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

    $tryFindClosest = array();

    if($dbRes){
        while($arRes = $dbRes->GetNext()){

            $tryFindClosest[] = trim(mb_strtolower(preg_replace('~\s+~isu','',$arRes['NAME'])));

        }

    }

}

$ready = array();
$file = dirname(dirname(__DIR__)).'/bitrix/tmp/closest.csv';

if($skip == 0){
    $rights = 'wb';
} else {
    $rights = 'ab';

    $ready = file($file);

    foreach($ready as $number => $string){
        $ready[$number] = str_getcsv($string,';');
    }

}

$pfOpen = fopen($file,$rights);

foreach($tryFindClosest as $number => $name) {

    if($skip > $currentCount){
        ++$currentCount;
        continue;
    };

	$shortest  = -1;
    $closest = array();
    $closest[] = $name;

    foreach ($tryFindClosest as $searchName) {

        $lev = levenshtein(trim(mb_strtolower($name)), trim(mb_strtolower($searchName)), 1,2,1);

        if($lev == 1
            && !isset($closest[$searchName])
            && $name != $searchName){

            $closest[$searchName] = $searchName;

        }


    }

    if(sizeof($closest) > 1){

        $already = false;

        if(!empty($ready)){

            foreach($ready as $rstring){

                $check = array_intersect($closest,$rstring);

                if(sizeof($check) == sizeof($closest)){
                    $already = true;
                    break;
                }

            }


        }

        if(!$already)
            fputcsv($pfOpen,$closest,';');
    }

    ++$currentCount;

    if(($countStrings + $skip) <= $currentCount){

        $skip += $countStrings;
        fclose($pfOpen);

        echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/closest.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(500,2000).');</script></header></html>';
        die();

    }


}

fclose($pfOpen);

