<?

//https://youtwig.ru/local/crontab/delete_prop_models.php?intestwetrust=1

 
$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";


define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');
//define('LANGUAGE_ID','ru');

//тип продукта;производитель;модель;товар;ком код;инд код;вид код;вид поз;вид изображение;

if ($argc > 0 && $argv[0]) {

    $_REQUEST['intestwetrust'] = 1;

}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

CModule::IncludeModule('iblock');

	$arFilter = array('IBLOCK_ID' => 17);
	$arSelect = array('ID');
	$arOrder = array();
	
    $modelEl = new CIBlockElement;

    $lDBRes = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
	$currentCount = 0;
	
    if($lDBRes)
        while($arFields = $lDBRes->GetNext()){

            ++$currentCount;
 
            $toBaseProducts = array(
				'RESTORE_PRODUCTS' => false,
                'products' => false,
				'VIEW' => false,
                'INDCODE' => false,
                'POSITION' => false
            );

            CIBlockElement::SetPropertyValuesEx($arFields['ID'], 17, $toBaseProducts);
			//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $arFields['ID']);

            if ($modelEl->Update($arFields['ID'], Array('TIMESTAMP_X' => true))) {

            };

        
        }

echo $currentCount.'-';
echo 'done';