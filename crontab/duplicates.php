<?php

die();

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$countStrings = 400;
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
$file = dirname(dirname(__DIR__)).'/bitrix/tmp/duplicates.csv';
$rights = 'wb+';

if($skip != 0){

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

        $cName = preg_replace(array('~[_]+~','~[^\w\dа-я]+~isu'),array('',''),trim($name));
        $cSearchName = preg_replace(array('~[_]+~','~[^\w\dа-я]+~isu'),array('',''),trim($searchName));

        if($cName == $cSearchName
            && !in_array($searchName,$closest)
            && $searchName != $name){

            $closest[] = $searchName;

            if(!in_array($cSearchName,$closest) 
				&& $cSearchName != $name ){
                $closest[] = $cSearchName;
            }

        }

    }

    $closest = array_unique($closest);

    if(sizeof($closest) > 1){

        if(!empty($ready)){

            $found = false;

            foreach($ready as $next => $rstring){

                $check = array_intersect($closest,$rstring);

                if(sizeof($check)){

                    $found = true;

                    $ready[$next] = array_merge($rstring,$closest);
                    $ready[$next] = array_unique($ready[$next]);

                }

                unset($check);

            }

            if(!$found){
                $closest = array_unique($closest);
                $ready[] = $closest;
            }


        } else {
            $closest = array_unique($closest);
            $ready[] = $closest;
        }

    }

    ++$currentCount;

    if(($countStrings + $skip) <= $currentCount){

        //$ready = array_unique($ready);

        foreach($ready as $next => $rstring){
            fputcsv($pfOpen,$rstring,';');
        }

        fclose($pfOpen);

        $skip += $countStrings;

        echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/duplicates.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(500,2000).');</script></header></html>';
        die();


    }


}

//$ready = array_unique($ready);

foreach($ready as $next => $rstring){
    fputcsv($pfOpen,$rstring,';');
}

fclose($pfOpen);

