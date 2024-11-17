<?php

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/link_doubles.csv','r');

if($fp)
    while ($linkArr = fgetcsv($fp,0,";")){

		
        $what = trim($linkArr[0]);
        $what = preg_replace('~http(s*?)://[^/]+?/~isu','',$what);
        $what = rtrim($what,'/');

        $where = trim($linkArr[1]);
        $where = preg_replace('~http(s*?)://[^/]+~isu','',$where);
		$where = empty($where) ? "/" : $where;


        $show = false;
        $rsData = CBXShortUri::GetList(
            Array(),
            Array("SHORT_URI" => $what));

        while($arRes = $rsData->Fetch()) {

            $show = true;
            break;
        }

        if (!$show
            && trim(mb_strtolower($where),'/') != trim(mb_strtolower($what),'/')){

            $arFields = Array(
                "URI" => $where,
                "SHORT_URI" => $what,
                "STATUS" => "301",
            );

            $ID = CBXShortUri::Add($arFields);

        }

    }

fclose($fp);