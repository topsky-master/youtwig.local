<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

global $USER;

$in_main = (!preg_match('~^/.+?/~', $_SERVER['REQUEST_URI'])) ? 1 : 0;
define('IN_MAIN', $in_main);

$curPage = $APPLICATION->GetCurPage(true);

$is_personal = false;

if (mb_strpos($_SERVER['REQUEST_URI'], '/personal/') === 0) {
    $is_personal = true;
}

$page_class_add = preg_replace('~' . preg_quote('?', '~') . '.*?$~is', '', $_SERVER['REQUEST_URI']);
$page_class_add = explode('/', $page_class_add);
$page_class_add = !is_array($page_class_add) ? array(
    $page_class_add
) : $page_class_add;

$cssName = '';

foreach ($page_class_add as $page_class_val) {

    $params = Array(
        "max_len" => "200",
        "change_case" => "L",
        "replace_space" => "_",
        "replace_other" => "_",
        "delete_repeat_replace" => "true"
    );

    $page_class .= ' ' . CUtil::translit($page_class_val, LANGUAGE_ID, $params);
    $cssName .= ((trim($page_class) != "") ? ('_' . trim($page_class)) : '');
}

$cssName .= 'index.min.css';
$cssName = SITE_TEMPLATE_PATH.'/css/'.$cssName;
$cssPath = false;

if(file_exists($_SERVER['DOCUMENT_ROOT'].$cssName)){
    $cssPath = $cssName;
}

unset($cssName);

$userName  = '';
$userEmail = '';

if ($USER->IsAuthorized()) {
    $page_class .= ' is_authorized';
    $rsUser = $USER->GetByID($USER->GetId());
    $arUser = $rsUser->Fetch();

    if ($arUser) {
        $userEmail = $arUser['EMAIL'];
        $userName  = CUser::FormatName("#LAST_NAME# #NAME# #SECOND_NAME#", $arUser);
    }
}

if ($APPLICATION->GetCurPage() == '/') {
    $page_class .= ' in_main';
}

if (defined('ERROR_404')) {
    $page_class .= ' error_404';
}


$logoFilter = Array(
    "CODE" => "logotype",
    "ACTIVE" => "Y",
    'PROPERTY_DOMAIN_VALUE' => IMPEL_SERVER_NAME
);

$logoId = false;

$logoResDB = CIBlockElement::GetList(Array(), $logoFilter, false, false, array("ID"));

if($logoResDB){

    while($logoResArr = $logoResDB->Fetch()){

        if(
            isset($logoResArr['ID'])
            && !empty($logoResArr['ID'])
        ){

            $logoId = $logoResArr['ID'];

        };
    };
};

if (empty($logoId)) {

    $logoFilter = Array(
        "CODE" => "logotype",
        "ACTIVE" => "Y",
    );

    $logoResDB = CIBlockElement::GetList(Array(), $logoFilter, false, false, array("ID"));

    while($logoResArr = $logoResDB->Fetch()){

        if(
            isset($logoResArr['ID'])
            && !empty($logoResArr['ID'])
        ){

            $logoId = $logoResArr['ID'];

        };
    };
}
