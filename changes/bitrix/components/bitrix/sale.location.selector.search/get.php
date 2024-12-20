<?php die(); 
define("NO_KEEP_STATISTIC", true);

define("NO_AGENT_STATISTIC", true);

define("NO_AGENT_CHECK", true);

define("NOT_CHECK_PERMISSIONS", true);



use Bitrix\Main;

use Bitrix\Main\Loader;



require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');



Loader::includeModule('sale');



require_once(__DIR__.'/class.php');



$result = true;

$errors = array();

$data = array();



try

{

	CUtil::JSPostUnescape();



	$request = Main\Context::getCurrent()->getRequest()->getPostList();

	if($request['version'] == '2')

		$data = CBitrixLocationSelectorSearchComponent::processSearchRequestV2($_REQUEST);

	else

		$data = CBitrixLocationSelectorSearchComponent::processSearchRequest();

	

	if(!defined('ADMIN_SECTION')){



        foreach($data['ITEMS'] as $sKey => $aItem){

            if(!in_array($aItem['TYPE_ID'],array(3,6))){

                unset($data['ITEMS'][$sKey]);

            }

        };



        $data['ITEMS'] = array_values($data['ITEMS']);



    }

	

}

catch(Main\SystemException $e)

{

	$result = false;

	$errors[] = $e->getMessage();

}



header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);

print(CUtil::PhpToJSObject(array(

	'result' => $result,

	'errors' => $errors,

	'data' => $data

), false, false, true));