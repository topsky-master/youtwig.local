<?php

if (isset($argc) && ($argc > 0) && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) {
    die();
}

//https://youtwig.ru/local/crontab/croncomments.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

ini_set('default_charset','utf-8');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

CModule::IncludeModule('forum');

$dbelt = \CIBlockElement::GetList(
    [],
    ['IBLOCK_ID' => 11],
    false,
    false,
    [
        "IBLOCK_ID",
        "ID",
        "NAME",
        "PROPERTY_FORUM_TOPIC_ID",
    ]
);

while ($element = $dbelt->GetNext()) {

    if (isset($element["PROPERTY_FORUM_TOPIC_ID_VALUE"]) && (int)$element["PROPERTY_FORUM_TOPIC_ID_VALUE"] > 0) {

        $vote_count = 0;
        $vote_sum = 0;
        $vote_rating = 0;

        $element["PROPERTY_FORUM_TOPIC_ID_VALUE"] = (int)$element["PROPERTY_FORUM_TOPIC_ID_VALUE"];

        if ($element["PROPERTY_FORUM_TOPIC_ID_VALUE"] > 0) {

        $topic = Bitrix\Forum\Topic::getById($element["PROPERTY_FORUM_TOPIC_ID_VALUE"]);
        if ($topic && $iTopicId = $topic->getId()) {

            $filter = [
                "FORUM_ID" => 1,
                "TOPIC_ID" => $iTopicId,
                "!PARAM1" => "IB",
                "APPROVED" => "Y"
            ];
            $dbRes = CForumMessage::GetListEx(
                ["ID" => "DESC"],
                $filter,
                false,
                0,
                ['*']
            );

            $arMessages = [];
            if ($dbRes)
            {

                while ($res = $dbRes->GetNext()){

                    if (isset($res['ID']) && !empty($res['ID'])) {

                        echo $res["ID"]."\n";

                        $rating = [];

                        $dbFRes = CIBlockElement::GetList(
                            Array(),
                            Array(
                                "IBLOCK_ID" => 30,
                                "PROPERTY_forum_topic_id" => $res["ID"]
                            ),
                            false,
                            false,
                            Array("ID","NAME","PROPERTY_vote_count","PROPERTY_vote_sum","PROPERTY_rating")
                        );

                        if($dbFRes && $dbFAr = $dbFRes->GetNext()){

                            $rating["vote_count"] = $dbFAr["PROPERTY_VOTE_COUNT_VALUE"];
                            $rating["vote_sum"] = $dbFAr["PROPERTY_VOTE_SUM_VALUE"];
                            $rating["rating"] = $dbFAr["PROPERTY_RATING_VALUE"];
                        }

                        if (empty($rating)) {

                            $rating["vote_count"] = 1;
                            $rating["vote_sum"] = 5;
                            $rating["rating"] = 5;

                            $sName = $element['NAME'] . ', '. $res["AUTHOR_NAME"] . ', '. $res["POST_DATE"] . ' - проставлено автоматически';

                            $oElt = new CIBlockElement;

                            $aPforile = Array(
                                "IBLOCK_SECTION_ID" => false,
                                "IBLOCK_ID" => 30,
                                "NAME" => $sName,
                                "ACTIVE" => "Y",
                                "PREVIEW_TEXT" => " ",
                                "DETAIL_TEXT" => " "
                            );

                            $iEltId = $oElt->Add($aPforile);

                            if ($iEltId) {

                                CIBlockElement::SetPropertyValuesEx(
                                    $iEltId,
                                    30,
                                    array(
                                        'vote_sum' => array('VALUE' => 5),
                                        'rating' => array('VALUE' => 5),
                                        'product_id' => array('VALUE' => $element['ID']),
                                        'vote_count' => array('VALUE' => 1),
                                        'forum_topic_id' => array('VALUE' => $res["ID"]),
                                    )
                                );

                                $rating["vote_count"] = 1;
                                $rating["vote_sum"] = 5;

                                $oElt->Update($iEltId, Array('TIMESTAMP_X' => true));


                            }

                        }

                        $vote_count += $rating["vote_count"];
                        $vote_sum += $rating["vote_sum"];

                    }

                }

            }

        }

        if (!empty($vote_sum) && !empty($vote_count)) {

            CIBlockElement::SetPropertyValuesEx(
                $element['ID'],
                11,
                array(
                    'vote_sum' => array('VALUE' => $vote_sum),
                    'rating' => array('VALUE' => round($vote_sum / $vote_count,2)),
                    'vote_count' => array('VALUE' => $vote_count),
                )
            );

        }

        }

    }
}