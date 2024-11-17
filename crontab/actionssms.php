<?php

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('main');
CModule::IncludeModule('sale');

@set_time_limit(30000);
ini_set('max_execution_time', 30000);
ini_set('memory_limit', '150M');

function rusPostTrakingArrivedSMS(){

		global $USER;

	 	if(CModule::IncludeModule("iblock")
		&& CModule::IncludeModule("currency")
		&& CModule::IncludeModule("sale")
		&& CModule::IncludeModule("catalog")){

			$currentHour											= date('H');

			if(($currentHour >= 6 && $currentHour <= 8) || true){


			$time 													= time() - 3 * 86400;


			$time													= date("d.m.Y",$time);

			$arFilter = Array(
					'DELIVERY_ID'									=>3,
					'STATUS_ID'	 									=>"A",
					'!CANCELED' 									=>"Y",
					'ID'											=>2432,
					//'<=DATE_STATUS'									=> $time
			);


			$db_sales 												= CSaleOrder::GetList(
					array("DATE_INSERT" => "ASC",
						  "DATE_UPDATE" => "ASC"),
					$arFilter,
					false,
					false,
					array('*')
			);
			if($db_sales
			&& is_object($db_sales)
			&& method_exists($db_sales,'Fetch')){
				$ar_sales											= array();
				$at_count											= 0;


				while (($ar_sales									= $db_sales->Fetch())){

					//фио мыло трек номер статус у заказа дата изменения статуса заказа
					//$ar_sales["ID"] USER_NAME USER_EMAIL USER_LAST_NAME TRACKING_NUMBER STATUS_ID DATE_STATUS

					$about_product									= array();

					$USER_ID										= $ar_sales["USER_ID"];

					$userDB					  						= $USER->GetByID($USER_ID);
					$userFields				  						= $userDB->Fetch();

					$old_send_sms									= (isset($userFields["UF_SMS_INFORM"]) && !empty($userFields["UF_SMS_INFORM"])) ? 1 : 0;

					$fields 										= Array(
						"UF_SMS_INFORM" 							=> 1,
					);
                    
					$order_id										= $ar_sales["ID"];

					$status_id										= $ar_sales["STATUS_ID"];
					$date_update									= $ar_sales["DATE_STATUS"];

					$USER->Update($USER_ID, $fields);

					CSaleOrder::StatusOrder($order_id,'Z');
					CSaleOrder::StatusOrder($order_id,$status_id);

					$fields 										= Array(
						"UF_SMS_INFORM" 							=> ($old_send_sms) ? 1 : 0,
					);

					$USER->Update($USER_ID, $fields);

					CEvent::SendImmediate('RUS_POST_RECIVED', SITE_ID, $about_product);

				}
			}
		}
	 }

}

//rusPostTrakingArrivedSMS();
