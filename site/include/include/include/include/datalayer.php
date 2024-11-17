<?php

il::define("STOP_STATISTICS", true);
il::define("ADMIN_SECTION",false);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;

$APPLICATION->IncludeComponent(
    "impel:datalayer",
    "",
    array()
);