<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->SetAdditionalCSS("/bitrix/templates/.default/components/bitrix/news.list/info/style.css");
$APPLICATION->AddHeadString('<link rel="amphtml" href="'.IMPEL_PROTOCOL . preg_replace('~^m\.~is','',IMPEL_SERVER_NAME) . '/amp/info/'.'" />');
twigSeoSections::printSeoAndSetTitlesSection(0,$arParams);