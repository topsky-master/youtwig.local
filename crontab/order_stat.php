<?php

//https://youtwig.ru/local/crontab/order_stat.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
	$_REQUEST['interval'] = $argv[1];
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelGetArchivePaymentInfo{

    private static $countStrings = 200;
    private static $iblockId = 0;

    public static function getList($antId){

		$elt = new CIBlockElement;

		foreach($antId as $intId) {

			$connection = Bitrix\Main\Application::getConnection();
			$sqlHelper = $connection->getSqlHelper();

			$date = new DateTime();
			$date->modify('-'.$intId.' month');
			$sDate = $date->format('Y-m-d H:i:s');

			$aProducts = array();

			$sql = "SELECT ID, ORDER_ID FROM `b_sale_order_archive` WHERE 
				STATUS_ID = 'FF' 
				AND `DATE_INSERT` > '".$sDate."'";


			$orecordset = $connection->query($sql);

			if($orecordset)
				while ($orecord = $orecordset->fetch()) {

				echo 'ORDER_ID - '.$orecord["ORDER_ID"]."\n";

				$sql = "SELECT PRODUCT_ID, QUANTITY
						  FROM b_sale_basket_archive
						  WHERE 
							ARCHIVE_ID = '".(int)($orecord['ID'])."'";

				$recordset = $connection->query($sql);

				if($recordset)
					while ($record = $recordset->fetch()) {

						echo 'PRODUCT_ID - '.$record['PRODUCT_ID']."\n";
						echo 'QUANTITY - '.$record['QUANTITY']."\n";

						$iCount = $record['QUANTITY'];
						$iProductId = $record['PRODUCT_ID'];

						if(!isset($aProducts[$iProductId])) {
							$aProducts[$iProductId] = 0;
						}	

						$aProducts[$iProductId] += $iCount;

					}

			}

			if(!empty($aProducts)){
				foreach($aProducts as $iProductId => $iCount){
					if($intId == 1 && $iProductId == 747){
						echo $icount.' -- '."\n";
					}
					CIblockElement::SetPropertyValuesEx($iProductId, 16, array("STATISTIC".$intId => $iCount));	
					//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(16, $iProductId);
				
					$elt->Update($iProductId,Array('TIMESTAMP_X' => true));
				}
			}

			$aProducts = array();

			$sql = "SELECT ID, ORDER_ID FROM `b_sale_order_archive` WHERE 
				STATUS_ID = 'D'  
				AND `DATE_INSERT` > '".$sDate."'";

			$orecordset = $connection->query($sql);

			if($orecordset)
				while ($orecord = $orecordset->fetch()) {

				echo 'ORDER_ID - '.$orecord["ORDER_ID"]."\n";

				$sql = "SELECT PRODUCT_ID, QUANTITY
						  FROM b_sale_basket_archive
						  WHERE 
							ARCHIVE_ID = '".(int)($orecord['ID'])."'";

				$recordset = $connection->query($sql);

				if($recordset)
					while ($record = $recordset->fetch()) {
						
						$iCount = $record['QUANTITY'];
						$iProductId = $record['PRODUCT_ID'];
						
						echo 'PRODUCT_ID - '.$record['PRODUCT_ID']."\n";
						echo 'QUANTITY - '.$record['QUANTITY']."\n";
							
						if(!isset($aProducts[$iProductId])) {
							$aProducts[$iProductId] = 0;
						}	
						
						$aProducts[$iProductId] += $iCount;
					
					}
					
			}
			
			if(!empty($aProducts)){
			
				foreach($aProducts as $iProductId => $iCount){
					CIblockElement::SetPropertyValuesEx($iProductId, 16, array("PREORDER".$intId => $iCount));	
					//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(16, $iProductId);
				
					$elt->Update($iProductId,Array('TIMESTAMP_X' => true));
				}
				
			}

		}	

    }


}

if(CModule::IncludeModule("iblock")) {

    impelGetArchivePaymentInfo::getList(array(1,3,12,24));
    //impelFixText::getList(16);

}