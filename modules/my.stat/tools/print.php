<?php

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('main');
CModule::IncludeModule('sale');
CModule::IncludeModule('my.stat');

@set_time_limit(30000);
ini_set('max_execution_time', 30000);
ini_set('memory_limit', '512M');

\My\Stat\DataTable::printAction();

?>
