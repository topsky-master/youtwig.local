<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php

        global $delivery_disable;

        $delivery_disable																=  false;

        if(empty($arResult['DELIVERY']) && empty($arResult['DELIVERY_LOCATION']) && false){

                $delivery_disable															=  true;


                $arFilter 																	=  array(
                                                "COMPABILITY" 												=> array(
                                                "WEIGHT" 													=> $arResult["ORDER_WEIGHT"],
                                                "PRICE" 													=> $arResult["ORDER_PRICE"],
                                                "LOCATION_FROM" 											=> COption::GetOptionString('sale', 'location', false, SITE_ID),
                                                //"LOCATION_TO" 												=> $arUserResult["DELIVERY_LOCATION"],
                                                //"LOCATION_ZIP" 												=> $arUserResult["DELIVERY_LOCATION_ZIP"],
                                                "MAX_DIMENSIONS" 											=> $arResult["MAX_DIMENSIONS"],
                                                "ITEMS" 													=> $arResult["BASKET_ITEMS"]
                                )
                );

                $bFirst 																	= true;
                $arDeliveryServiceAll 														= array();
                $bFound 																	= false;

                $rsDeliveryServicesList 													= CSaleDeliveryHandler::GetList(
                                        array("SORT" 													=> "ASC"), $arFilter);

                while ($arDeliveryService 													= $rsDeliveryServicesList->Fetch())
                {
                        if (!is_array($arDeliveryService) || !is_array($arDeliveryService["PROFILES"])) continue;

                        if(!empty($arUserResult["DELIVERY_ID"])
                                && mb_strpos($arUserResult["DELIVERY_ID"], ":") 						!== false)
                        {
                                foreach ($arDeliveryService["PROFILES"] as $profile_id 				=> $arDeliveryProfile)
                                {
                                        if($arDeliveryProfile["ACTIVE"] 								== "Y")
                                        {
                                                $delivery_id 												= $arDeliveryService["SID"];
                                                if($arUserResult["DELIVERY_ID"] 							== $delivery_id.":".$profile_id)
                                                        $bFound 												= true;
                                        }
                                }
                        }

                        $arDeliveryServiceAll[] 												= $arDeliveryService;
                }

                if(!$bFound && !empty($arUserResult["DELIVERY_ID"])
                        && mb_strpos($arUserResult["DELIVERY_ID"], ":") 							!== false)
                {
                        $arUserResult["DELIVERY_ID"] 											= "";
                        $arResult["DELIVERY_PRICE"] 											= 0;
                        $arResult["DELIVERY_PRICE_FORMATED"] 									= "";
                }

                //select delivery to paysystem
                $arUserResult["PAY_SYSTEM_ID"] 												= IntVal($arUserResult["PAY_SYSTEM_ID"]);
                $arUserResult["DELIVERY_ID"] 												= trim($arUserResult["DELIVERY_ID"]);
                $bShowDefaultSelected 														= True;
                $arD2P 																		= array();
                $arP2D 																		= array();
                $delivery 																	= "";
                $bSelected 																	= false;

                $dbRes 																		= CSaleDelivery::GetDelivery2PaySystem(array());
                while ($arRes 																= $dbRes->Fetch())
                {
                        $arD2P[$arRes["DELIVERY_ID"]][$arRes["PAYSYSTEM_ID"]] 					= $arRes["PAYSYSTEM_ID"];
                        $arP2D[$arRes["PAYSYSTEM_ID"]][$arRes["DELIVERY_ID"]] 					= $arRes["DELIVERY_ID"];
                        $bShowDefaultSelected 													= False;
                }

                if ($arParams["DELIVERY_TO_PAYSYSTEM"] 										== "d2p")
                        $arP2D 																	= array();

                if ($arParams["DELIVERY_TO_PAYSYSTEM"] 										== "p2d")
                {
                        if(IntVal($arUserResult["PAY_SYSTEM_ID"]) 								<= 0)
                        {
                                $bFirst 															= True;
                                $arFilter 															= array(
                                                "ACTIVE" 													=> "Y",
                                                "PERSON_TYPE_ID" 											=> $arUserResult["PERSON_TYPE_ID"],
                                                "PSA_HAVE_PAYMENT" 											=> "Y"
                                );
                                $dbPaySystem 														= CSalePaySystem::GetList(
                                                array("SORT" 												=> "ASC",
                                                            "PSA_NAME" 											=> "ASC"),
                                                $arFilter
                                );
                                while ($arPaySystem 												= $dbPaySystem->Fetch())
                                {
                                        if (IntVal($arUserResult["PAY_SYSTEM_ID"]) 						<= 0 && $bFirst)
                                        {
                                                $arPaySystem["CHECKED"] 									= "Y";
                                                $arUserResult["PAY_SYSTEM_ID"] 								= $arPaySystem["ID"];
                                        }
                                        $bFirst 														= false;
                                }
                        }
                }

                $bFirst 																	= True;
                $bFound 																	= false;
                $_SESSION["SALE_DELIVERY_EXTRA_PARAMS"] 									= array(); // here we will store params for params dialog

                //select calc delivery
                foreach($arDeliveryServiceAll as $arDeliveryService)
                {
                        foreach ($arDeliveryService["PROFILES"] as $profile_id 					=> $arDeliveryProfile)
                        {
                                if ($arDeliveryProfile["ACTIVE"] 									== "Y"
                                                && (count($arP2D[$arUserResult["PAY_SYSTEM_ID"]]) 			<= 0
                                                                || in_array($arDeliveryService["SID"], $arP2D[$arUserResult["PAY_SYSTEM_ID"]])
                                                                || empty($arD2P[$arDeliveryService["SID"]])
                                                ))
                                {
                                        $delivery_id 													= $arDeliveryService["SID"];
                                        $arProfile 														= array(
                                                        "SID" 													=> $profile_id,
                                                        "TITLE" 												=> $arDeliveryProfile["TITLE"],
                                                        "DESCRIPTION" 											=> $arDeliveryProfile["DESCRIPTION"],
                                                        "FIELD_NAME" 											=> "DELIVERY_ID",
                                        );


                                        if((mb_strlen($arUserResult["DELIVERY_ID"]) > 0
                                                        && $arUserResult["DELIVERY_ID"] 						== $delivery_id.":".$profile_id))
                                        {
                                                $arProfile["CHECKED"] 										= "Y";
                                                $arUserResult["DELIVERY_ID"] 								= $delivery_id.":".$profile_id;
                                                $bSelected 													= true;

                                                $arOrderTmpDel 												= array(
                                                                "PRICE" 											=> $arResult["ORDER_PRICE"],
                                                                "WEIGHT" 											=> $arResult["ORDER_WEIGHT"],
                                                                "DIMENSIONS" 										=> $arResult["ORDER_DIMENSIONS"],
                                                                "LOCATION_FROM" 									=> COption::GetOptionInt('sale', 'location'),
                                                                //"LOCATION_TO" 										=> $arUserResult["DELIVERY_LOCATION"],
                                                                //"LOCATION_ZIP" 										=> $arUserResult["DELIVERY_LOCATION_ZIP"],
                                                                "ITEMS" 											=> $arResult["BASKET_ITEMS"],
                                                                "EXTRA_PARAMS" 										=> $arResult["DELIVERY_EXTRA"]
                                                );

                                                $arDeliveryPrice 											= CSaleDeliveryHandler::CalculateFull($delivery_id, $profile_id, $arOrderTmpDel, $arResult["BASE_LANG_CURRENCY"]);

                                                if ($arDeliveryPrice["RESULT"] 								== "ERROR")
                                                {
                                                        $arResult["ERROR"][] 									= $arDeliveryPrice["TEXT"];
                                                }
                                                else
                                                {
                                                        $arResult["DELIVERY_PRICE"] 							= roundEx($arDeliveryPrice["VALUE"], SALE_VALUE_PRECISION);
                                                        $arResult["PACKS_COUNT"] 								= $arDeliveryPrice["PACKS_COUNT"];
                                                }
                                        }

                                        if (empty($arResult["DELIVERY"][$delivery_id]))
                                        {
                                                $arResult["DELIVERY"][$delivery_id] 						= array(
                                                                "SID" 												=> $delivery_id,
                                                                "SORT" 												=> $arDeliveryService["SORT"],
                                                                "TITLE" 											=> $arDeliveryService["NAME"],
                                                                "DESCRIPTION" 										=> $arDeliveryService["DESCRIPTION"],
                                                                "PROFILES" 											=> array(),
                                                );
                                        }

                                        $arDeliveryExtraParams 											= CSaleDeliveryHandler::GetHandlerExtraParams($delivery_id, $profile_id, $arOrderTmpDel, SITE_ID);

                                        if(!empty($arDeliveryExtraParams))
                                        {
                                                $_SESSION["SALE_DELIVERY_EXTRA_PARAMS"][$delivery_id.":".$profile_id] = $arDeliveryExtraParams;
                                                $arResult["DELIVERY"][$delivery_id]["ISNEEDEXTRAINFO"] 		= "Y";
                                        }
                                        else
                                        {
                                                $arResult["DELIVERY"][$delivery_id]["ISNEEDEXTRAINFO"] 		= "N";
                                        }

                                        if(!empty($arUserResult["DELIVERY_ID"])
                                                && mb_strpos($arUserResult["DELIVERY_ID"], ":") 				!== false)
                                        {
                                                if($arUserResult["DELIVERY_ID"] 							== $delivery_id.":".$profile_id)
                                                        $bFound 												= true;
                                        }

                                        $arResult["DELIVERY"][$delivery_id]["LOGOTIP"] 					= $arDeliveryService["LOGOTIP"];
                                        $arResult["DELIVERY"][$delivery_id]["PROFILES"][$profile_id] 	= $arProfile;
                                        $bFirst 														= false;
                                }
                        }
                }
                if(!$bFound
                        && !empty($arUserResult["DELIVERY_ID"])
                        && mb_strpos($arUserResult["DELIVERY_ID"], ":") 							!== false)
                        $arUserResult["DELIVERY_ID"] 											= "";

                /*Old Delivery*/
                $arStoreId 																	= array();
                $arDeliveryAll 																= array();
                $bFound 																	= false;
                $bFirst 																	= true;

                $dbDelivery 																= CSaleDelivery::GetList(
                                array("SORT"														=>"ASC",
                                            "NAME"														=>"ASC"),
                                array(
                                                "LID" 														=> SITE_ID,
                                                "+<=WEIGHT_FROM" 											=> $arResult["ORDER_WEIGHT"],
                                                "+>=WEIGHT_TO" 												=> $arResult["ORDER_WEIGHT"],
                                                "+<=ORDER_PRICE_FROM" 										=> $arResult["ORDER_PRICE"],
                                                "+>=ORDER_PRICE_TO" 										=> $arResult["ORDER_PRICE"],
                                                "ACTIVE" 													=> "Y",
                                                //"LOCATION" 													=> $arUserResult["DELIVERY_LOCATION"],
                                )
                );
                while ($arDelivery 															= $dbDelivery->Fetch())
                {
                        $arStore 																= array();
                        if (mb_strlen($arDelivery["STORE"]) > 0)
                        {
                                $arStore 															= unserialize($arDelivery["STORE"]);
                                foreach ($arStore as $val)
                                        $arStoreId[$val] 												= $val;
                        }

                        $arDelivery["STORE"] 													= $arStore;

                        if (isset($_POST["BUYER_STORE"]) && in_array($_POST["BUYER_STORE"], $arStore))
                        {
                                $arUserResult['DELIVERY_STORE'] 									= $arDelivery["ID"];
                        }

                        $arDeliveryDescription 													= CSaleDelivery::GetByID($arDelivery["ID"]);
                        $arDelivery["DESCRIPTION"] 												= htmlspecialcharsbx($arDeliveryDescription["DESCRIPTION"]);

                        $arDeliveryAll[] 														= $arDelivery;

                        if(!empty($arUserResult["DELIVERY_ID"])
                                && mb_strpos($arUserResult["DELIVERY_ID"], ":") 						=== false)
                        {
                                if(IntVal($arUserResult["DELIVERY_ID"]) 							== IntVal($arDelivery["ID"]))
                                        $bFound 														= true;
                        }
                        if(IntVal($arUserResult["DELIVERY_ID"]) 								== IntVal($arDelivery["ID"]))
                        {
                                $arResult["DELIVERY_PRICE"] 										= roundEx(CCurrencyRates::ConvertCurrency($arDelivery["PRICE"], $arDelivery["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
                        }
                }
                if(!$bFound && !empty($arUserResult["DELIVERY_ID"])
                        && mb_strpos($arUserResult["DELIVERY_ID"], ":") 							=== false)
                {
                        $arUserResult["DELIVERY_ID"] 											= "";
                }

                $arStore 																	= array();
                $dbList 																	= CCatalogStore::GetList(
                                array("SORT" 														=> "DESC",
                                            "ID" 															=> "DESC"),
                                array("ACTIVE" 														=> "Y",
                                            "ID" 															=> $arStoreId,
                                            "ISSUING_CENTER" 												=> "Y",
                                            "+SITE_ID" 													=> SITE_ID),
                                false,
                                false,
                                array("ID", "TITLE", "ADDRESS", "DESCRIPTION", "IMAGE_ID", "PHONE", "SCHEDULE", "GPS_N", "GPS_S", "ISSUING_CENTER", "SITE_ID")
                );
                while ($arStoreTmp 															= $dbList->Fetch())
                {
                        if ($arStoreTmp["IMAGE_ID"] > 0)
                                $arStoreTmp["IMAGE_ID"] 											= CFile::GetFileArray($arStoreTmp["IMAGE_ID"]);

                        $arStore[$arStoreTmp["ID"]] 											= $arStoreTmp;
                }

                $arResult["STORE_LIST"] 													= $arStore;

                if(!$bFound && !empty($arUserResult["DELIVERY_ID"])
                        && mb_strpos($arUserResult["DELIVERY_ID"], ":") 							=== false)
                        $arUserResult["DELIVERY_ID"] 											= "";

                foreach($arDeliveryAll as $arDelivery)
                {
                        if (count($arP2D[$arUserResult["PAY_SYSTEM_ID"]]) 						<= 0 ||
                                                        in_array($arDelivery["ID"], $arP2D[$arUserResult["PAY_SYSTEM_ID"]]))
                        {
                                $arDelivery["FIELD_NAME"] 											= "DELIVERY_ID";
                                if ((IntVal($arUserResult["DELIVERY_ID"]) 							== IntVal($arDelivery["ID"])))
                                {
                                        $arDelivery["CHECKED"] 											= "Y";
                                        $arUserResult["DELIVERY_ID"] 									= $arDelivery["ID"];
                                        $arResult["DELIVERY_PRICE"] 									= roundEx(CCurrencyRates::ConvertCurrency($arDelivery["PRICE"], $arDelivery["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
                                        $bSelected 														= true;
                                }
                                if (IntVal($arDelivery["PERIOD_FROM"]) > 0 || IntVal($arDelivery["PERIOD_TO"]) > 0)
                                {

                                        $arDelivery["PERIOD_TEXT"] 										= GetMessage("SALE_DELIV_PERIOD");
                                        if (IntVal($arDelivery["PERIOD_FROM"]) > 0)
                                                $arDelivery["PERIOD_TEXT"] 									.= " ".GetMessage("SOA_FROM")." ".IntVal($arDelivery["PERIOD_FROM"]);
                                        if (IntVal($arDelivery["PERIOD_TO"]) > 0)
                                                $arDelivery["PERIOD_TEXT"] 									.= " ".GetMessage("SOA_TO")." ".IntVal($arDelivery["PERIOD_TO"]);
                                        if ($arDelivery["PERIOD_TYPE"] 									== "H")
                                                $arDelivery["PERIOD_TEXT"] 									.= " ".GetMessage("SOA_HOUR")." ";
                                        elseif ($arDelivery["PERIOD_TYPE"]								=="M")
                                        $arDelivery["PERIOD_TEXT"] 										.= " ".GetMessage("SOA_MONTH")." ";
                                        else
                                                $arDelivery["PERIOD_TEXT"] 									.= " ".GetMessage("SOA_DAY")." ";

                                }

                                if (intval($arDelivery["LOGOTIP"]) > 0)
                                        $arDelivery["LOGOTIP"] 											= CFile::GetFileArray($arDelivery["LOGOTIP"]);

                                $arDelivery["PRICE_FORMATED"] 										= SaleFormatCurrency($arDelivery["PRICE"], $arDelivery["CURRENCY"]);
                                $arResult["DELIVERY"][$arDelivery["ID"]] 							= $arDelivery;
                                $bFirst 															= false;
                        }
                }

                uasort($arResult["DELIVERY"], array('CSaleBasketHelper', 'cmpBySort')); // resort delivery arrays according to SORT value

                if(!$bSelected && !empty($arResult["DELIVERY"]))
                {
                        $bf 																	= true;
                        foreach($arResult["DELIVERY"] as $k 									=> $v)
                        {
                                if($bf)
                                {
                                        if(IntVal($k) > 0)
                                        {
                                                $arResult["DELIVERY"][$k]["CHECKED"] 						= "Y";
                                                $arUserResult["DELIVERY_ID"] 								= $k;
                                                $bf 														= false;

                                                $arResult["DELIVERY_PRICE"] 								= roundEx(CCurrencyRates::ConvertCurrency($arResult["DELIVERY"][$k]["PRICE"], $arResult["DELIVERY"][$k]["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
                                        }
                                        else
                                        {
                                                foreach($v["PROFILES"] as $kk 								=> $vv)
                                                {
                                                        if($bf)
                                                        {
                                                                $arResult["DELIVERY"][$k]["PROFILES"][$kk]["CHECKED"]= "Y";
                                                                $arUserResult["DELIVERY_ID"] 						= $k.":".$kk;
                                                                $bf = false;

                                                                $arOrderTmpDel 										= array(
                                                                                "PRICE" 									=> $arResult["ORDER_PRICE"],
                                                                                "WEIGHT" 									=> $arResult["ORDER_WEIGHT"],
                                                                                "DIMENSIONS" 								=> $arResult["ORDER_DIMENSIONS"],
                                                                                "LOCATION_FROM" 							=> COption::GetOptionInt('sale', 'location'),
                                                                                //"LOCATION_TO" 								=> $arUserResult["DELIVERY_LOCATION"],
                                                                                //"LOCATION_ZIP" 								=> $arUserResult["DELIVERY_LOCATION_ZIP"],
                                                                                "ITEMS" 									=> $arResult["BASKET_ITEMS"],
                                                                                "EXTRA_PARAMS" 								=> $arResult["DELIVERY_EXTRA"]
                                                                );

                                                                $arDeliveryPrice 									= CSaleDeliveryHandler::CalculateFull($k, $kk, $arOrderTmpDel, $arResult["BASE_LANG_CURRENCY"]);

                                                                if ($arDeliveryPrice["RESULT"] 						== "ERROR")
                                                                {
                                                                        $arResult["ERROR"][] 							= $arDeliveryPrice["TEXT"];
                                                                }
                                                                else
                                                                {
                                                                        $arResult["DELIVERY_PRICE"] 					= roundEx($arDeliveryPrice["VALUE"], SALE_VALUE_PRECISION);
                                                                        $arResult["PACKS_COUNT"] 						= $arDeliveryPrice["PACKS_COUNT"];
                                                                }
                                                                break;
                                                        }
                                                }
                                        }
                                }
                        }
                }

                if ($arUserResult["PAY_SYSTEM_ID"] > 0 || mb_strlen($arUserResult["DELIVERY_ID"]) > 0)
                {
                        if (mb_strlen($arUserResult["DELIVERY_ID"]) > 0
                                && $arParams["DELIVERY_TO_PAYSYSTEM"] 								== "d2p")
                        {
                                if (mb_strpos($arUserResult["DELIVERY_ID"], ":"))
                                {
                                        $tmp 															= explode(":", $arUserResult["DELIVERY_ID"]);
                                        $delivery 														= trim($tmp[0]);
                                }
                                else
                                        $delivery 														= intval($arUserResult["DELIVERY_ID"]);
                        }
                }

                if(DoubleVal($arResult["DELIVERY_PRICE"]) > 0)
                        $arResult["DELIVERY_PRICE_FORMATED"] 									= SaleFormatCurrency($arResult["DELIVERY_PRICE"], $arResult["BASE_LANG_CURRENCY"]);

                foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepDelivery", true) as $arEvent)
                        ExecuteModuleEventEx($arEvent, array(&$arResult, &$arUserResult, &$arParams));

        };



        if(is_array($arResult['DELIVERY']) && !empty($arResult['DELIVERY'])){

                foreach ($arResult['DELIVERY'] as $key	=>$value){
                        if(isset($value['NAME'])
                                && !empty($value['NAME'])
                                && $value['NAME'] 				== "Купить в один клик"
                                || $value['NAME'] 				== "Без доставки"
                                || $value['NAME'] 				== "Предзаказ"
                                ){
                                        unset($arResult['DELIVERY'][$key]);

                                };
                };

        };


?>