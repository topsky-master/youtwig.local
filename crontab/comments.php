<?php

//https://youtwig.ru/local/crontab/comments.php?intestwetrust=1&file=results.csv

if (!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);
define("MAX_SORT",292000);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

setlocale(LC_TIME, 'ru_RU', 'russian');

global $USER;

$sAnswer = "Ответ магазина TWiG";
$file = isset($_REQUEST['file'])
&& !empty($_REQUEST['file'])
&& file_exists(dirname(dirname(__DIR__)) . '/bitrix/tmp/' . urldecode(trim($_REQUEST['file'])))
    ? urldecode(trim($_REQUEST['file']))
    : '';

$aUsed = [];
$aElements = [];

if (!empty($file)) {

    $cElt = new CIBlockElement;

    $file = dirname(dirname(__DIR__)) . '/bitrix/tmp/' . urldecode(trim($_REQUEST['file']));

    $rFile = fopen($file, 'r');

    if (CModule::IncludeModule("iblock")) {

        $aElements = [];

        while ($cData = fgetcsv($rFile, 0, ';')) {
            $cData = array_map('trim', $cData);
            $strings[] = $cData;
        }

        foreach ($strings as $cData) {

            //$xml_id = md5($string);

            $cIsFound = false;

            $cData = array_map('trim', $cData);

            //$cArSelect = Array("ID","SORT");
            //$cArFilter = Array("XML_ID" => $xml_id,'IBLOCK_ID' => 31,);

            //$dbCRes = CIBlockElement::GetList(Array(), $cArFilter, false, false, $cArSelect);

            /* if($dbCRes && ($arCRes = $dbCRes->GetNext())){

                if(isset($arCRes['ID'])
                    && !empty($arCRes['ID'])){

                    $cIsFound = $arCRes['ID'];

                }

                if(isset($arCRes['SORT'])
                    && !empty($arCRes['SORT'])){

                    $sort = $arCRes['SORT'];
                }

            } */

            //Достоинства: -> dl
            //Недостатки: -> dl
            //Комментарий: -> dl
            //• -> ul / ? p

            /* format /images/1305602986.jpeg;;2022-06-17;"Наталья Кривякина";"Достоинства: • Курьер приехал точно в срок
            • Заказ подняли на этаж
            • Курьер был вежлив
            • Курьер позвонил заранее
            • Товары надёжно упакованы
            • Была возможность проверить товар";5 */

            $cData[1] = strip_tags($cData[1]);
            $cData[1] = trim($cData[1]);
            $where = $cData[1];
            $sText = $cData[4];
            $sCheck = '';

            if (!empty($sText)) {
                $sText = trim(strip_tags($sText));
                $sText = preg_replace("~•\s*?([^\n]+)~isu", "<p>• $1</p>", $sText);
                $sText = preg_replace('~(Достоинства|Недостатки|Комментарий)\:~isu', "</dd></dl><dl><dt>$1:</dt><dd>", $sText);
                $sText .= "</dd></dl>";
                $sText = preg_replace("~^</dd></dl>~", "", $sText);
                $sCheck = trim(strip_tags($sCheck));
            }

            $date = $cData[2];

            if (isset($aUsed[$cData[3].'-'.$date])) {
                continue;
            }

            if (!isset($aUsed[$cData[3].'-'.$date])) {
                $aUsed[$cData[3].'-'.$date] = '';
            }

            $topSort += 1000;

            $biComment = mb_stripos($date, 'T') !== false ? true : false;

            if (!empty($date)) {

                $ru_month = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
                $en_month = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
                $date = ConvertTimeStamp(strtotime(str_ireplace($ru_month, $en_month, $date)), "SHORT");

            }

            if (!$biComment) {

                if (!empty($cData[0])) {
                    $cData[0] = str_ireplace('/images/', '', $cData[0]);
                    $cData[0] = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/reviews/" . $cData[0];
                    $cData[0] = CFile::MakeFileArray($cData[0]);
                }

                if ($cData[3] == 'Имя скрыто') {
                    $cData[0] = '';
                }

                $cProperties = array(
                    'IS_COMMENT' => '',
                    'DELIVERY_NAME' => $where,
                    'vote_count' => 1,
                    'vote_sum' => $cData[5],
                    'rating' => $cData[5]
                );

                $cEltAr = [
                    'IBLOCK_ID' => 31,
                    'PREVIEW_PICTURE' => $cData[0],
                    //'XML_ID' => $xml_id,
                    'NAME' => $cData[3],
                    'PREVIEW_TEXT' => $sText,
                    'DATE_ACTIVE_FROM' => $date,
                    'PROPERTY_VALUES' => $cProperties,
                    'REVIEWS' => [],
                    'TIME' => strtotime($date),
                ];
                //echo $sort.'-main<br />';

                $aElements[] = $cEltAr;

                //if ($cIsLast = $cElt->Add($cEltAr)) {

                //};

            } else {

                list($date, $trunc) = explode('T', $date);

                $cProperties = array(
                    'IS_COMMENT' => 1,
                    'DELIVERY_NAME' => $where,
                    'vote_count' => 1,
                    'vote_sum' => $cData[5],
                    'rating' => $cData[5]
                );

                $cEltAr = [
                    'IBLOCK_ID' => 31,
                    'PREVIEW_PICTURE' => '',
                    'NAME' => $sAnswer,
                    'PREVIEW_TEXT' => ('<div>' . $sText . '</div>'),
                    'DATE_ACTIVE_FROM' => $date,
                    'PROPERTY_VALUES' => $cProperties,
                ];

                $iLast = sizeof($aElements) - 1;
                $aElements[$iLast]['REVIEWS'][] = $cEltAr;

                //if ($cIsLast = $cElt->Add($cEltAr)) {

                //};


            }

        }
    }
}

usort($aElements,function($first,$next){
    if ($first['TIME'] == $next['TIME']) {
        return 0;
    }
    return $first['TIME'] > $next['TIME'] ? 1 : -1;
});

$topSort = MAX_SORT;

foreach ($aElements as $cEltAr) {

    $topSort += 1000;
    $cEltAr['SORT'] = $topSort;

    if ($cIsLast = $cElt->Add($cEltAr)) {

        if (isset($cEltAr['REVIEWS'])) {

            //$cEltAr['REVIEWS'] = array_reverse($cEltAr['REVIEWS']);

            foreach ($cEltAr['REVIEWS'] as $aComment) {
                $aComment['PROPERTY_VALUES']['IS_COMMENT'] = $cIsLast;

                $topSort -= 10;
                $aComment['SORT'] = $topSort;

                $cElt->Add($aComment);
            }
        }
    }
}
