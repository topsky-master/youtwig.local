<?php

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);
define("ACTIONS_FILE",dirname(dirname(__DIR__)).'/bitrix/tmp/actions.txt');
define("ORDERS_FILE",dirname(dirname(__DIR__)).'/bitrix/tmp/orders.txt');

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('main');
CModule::IncludeModule('sale');

@set_time_limit(30000);
ini_set('max_execution_time', 30000);
ini_set('memory_limit', '512M');

function rusPostTraking(){

    $time_to_check												                = -1;
    $time_to_send                                                               = 3600;
    $max_count                                                                  = 600;

    $course_file												                = $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache/ruspost.last';

    if(!file_exists(ACTIONS_FILE)){
        $fp                                                                     = fopen(ACTIONS_FILE,'w');
        fclose($fp);
    };

    if(!file_exists(ORDERS_FILE)){
        $fp                                                                     = fopen(ORDERS_FILE,'w');
        fclose($fp);
    };

    $statuses                                                                   = array();

    if(file_exists(ACTIONS_FILE)){
        $statuses                                                               = file(ACTIONS_FILE);
        $statuses                                                               = array_map('trim',$statuses);
        $statuses                                                               = array_unique($statuses);
        $statuses                                                               = array_filter($statuses);
    };

    $orders                                                                     = array();

    if(file_exists(ORDERS_FILE)){
        $orders                                                                 = file(ORDERS_FILE);
        $orders                                                                 = array_map('trim',$orders);
        $orders                                                                 = array_unique($orders);
        $orders                                                                 = array_filter($orders);
    };


    if(is_dir(dirname($course_file)) && !is_writable(dirname($course_file))){
        chmod(dirname($course_file), '0777');
    }

    $need_update			= false;

    if(!is_writable($course_file)){
        chmod($course_file, '0777');
    };

    if( (	is_file($course_file)
        && is_writable($course_file)
        && filesize($course_file) > 0
        && ((filemtime($course_file) + $time_to_check) < time())
    )
    ){
        $need_update		                                                    = true;

        $fp 				                                                    = fopen($course_file,'w');
        if($fp && is_resource($fp)){
            fwrite($fp,'1');
            fclose($fp);
        };


    }  else if(!is_file($course_file) || !filesize($course_file)){
        $need_update		                                                    = true;

        if(!is_file($course_file)){
            $fp 													            = fopen($course_file,'w+');
            if($fp && is_resource($fp)){
                fwrite($fp,'1');
                fclose($fp);
            };
        };
    }

    if($need_update){


        if(CModule::IncludeModule("iblock")
            && CModule::IncludeModule("currency")
            && CModule::IncludeModule("sale")
            && CModule::IncludeModule("catalog")){


            require_once ($_SERVER['DOCUMENT_ROOT'].'/rupost/russianpost.lib.php');

            $arFilter = Array(
                //'ID'                                                        =>24540,
                'DELIVERY_ID'									            =>3,
                //'>TRACKING_NUMBER'								        =>'',
                '!@STATUS_ID'	 								            =>array("F", "P", "R", "J","Z"),
                '!CANCELED' 									            =>"Y"

            );


            $db_sales 												            = CSaleOrder::GetList(
                array("DATE_INSERT" => "DESC",
                    "DATE_UPDATE" => "DESC"),
                $arFilter,
                false,
                false,
                array('*')
            );



            if($db_sales
                && is_object($db_sales)
                && method_exists($db_sales,'Fetch')){
                $ar_sales											            = array();
                $at_count											            = 0;

                if($max_count > $at_count)
                    while (($ar_sales									            = $db_sales->Fetch())){

                        if(empty($ar_sales['TRACKING_NUMBER'])){

                            $list = \Bitrix\Sale\Internals\OrderTable::getList(array(
                                "select" => array(
                                    "NEW_TRACKING_NUMBER" => "\Bitrix\Sale\Internals\ShipmentTable:ORDER.TRACKING_NUMBER"
                                ),
                                "filter" => array(
                                    "!=\Bitrix\Sale\Internals\ShipmentTable:ORDER.TRACKING_NUMBER" => "",
                                    "=ID" => $ar_sales["ID"]
                                ),
                                'limit'=> 1
                            ))->fetchAll();


                            if(     isset($list[0])
                                &&  isset($list[0]['NEW_TRACKING_NUMBER']))
                                $ar_sales['TRACKING_NUMBER']                            = trim($list[0]['NEW_TRACKING_NUMBER']);

                        }

                        $tracking_number								            = $ar_sales['TRACKING_NUMBER'];

                        if(!empty($tracking_number)){


                            $db_sales_props										    = CSaleOrderPropsValue::GetList(
                                array("DATE_INSERT" => "ASC",
                                    "DATE_UPDATE" => "ASC"),
                                array(
                                    "CODE"		=> "rupost_spy",
                                    "ORDER_ID"	=> $ar_sales["ID"],
                                )
                            );

                            $previous_status                                        = '';
                            $previous_status_id                                     = '';

                            $ar_sales_props										    = array();
                            if($db_sales_props
                                && is_object($db_sales_props)
                                && method_exists($db_sales_props,'Fetch')
                                && $ar_sales_props									    = $db_sales_props->Fetch()
                            ){


                                if(isset($ar_sales_props['VALUE'])){

                                    $previous_status							    = $ar_sales_props['VALUE'];
                                    $previous_status_id							    = $ar_sales_props['ID'];
                                }
                            }

                            $ar_sales_statuses								= array();

                            $db_sales_statuses								= CSaleOrderPropsVariant::GetList(
                                array("SORT" 		=> "ASC"),
                                array(
                                    "CODE"		=> "rupost_spy",
                                ),
                                false,
                                false,
                                array());

                            if($db_sales_statuses
                                && is_object($db_sales_statuses)
                                && method_exists($db_sales_statuses,'Fetch')
                            ){

                                while($ar_sales_statuses_tmp				= $db_sales_statuses->Fetch()){
                                    $ar_sales_statuses[$ar_sales_statuses_tmp['VALUE']] = $ar_sales_statuses_tmp['NAME'];
                                }
                            }

                            $number										    = $tracking_number;
                            $client 									    = new RussianPostAPI();

                            $history									    = ($client->getOperationHistory($number));

                            if(is_array($history) && sizeof($history)){
                                $last									    = $history[sizeof($history)-1];
                                $first									    = $history[0];

                                if(is_object($last)
                                    && isset($last->operationType)
                                    && isset($last->operationAttribute)
                                ){
                                    $status								    = 'N';
                                    $check_status                           = '';
                                    if(!empty($last->operationType)){
                                        $check_status                       = $last->operationType.(!empty($last->operationAttribute)?('. '.$last->operationAttribute):'');
                                    } elseif(empty($last->operationType) && !empty($last->operationAttribute)){
                                        $check_status                       = $last->operationAttribute;
                                    };

                                    switch($check_status){
                                        case 'Приём. Единичный':
                                        case 'Прием. Единичный':
                                            $status						    = 'PE';
                                            break;

                                        case 'Приём. Партионный':
                                        case 'Прием. Партионный':
                                            $status						    = 'PP';
                                            break;

                                        case 'Вручение. Адресату с контролем ответа':
                                            $status						    = 'BAK';
                                            break;

                                        case 'Вручение':
                                        case 'Вручение. Вручение адресату':
                                            $status						    = 'BA';
                                            break;

                                        case 'Вручение. Вручение отправителю':
                                            $status						    = 'BO';
                                            break;

                                        case 'Вручение. Выдано адресату через почтомат':
                                            $status						    = 'BP';
                                            break;

                                        case 'Вручение. Выдано отправителю через почтомат':
                                            $status						    = 'BOP';
                                            break;

                                        case 'Возврат. Истёк срок хранения':
                                        case 'Возврат. Истек срок хранения':
                                            $status						    = 'BI';
                                            break;

                                        case 'Возврат. Заявление отправителя':
                                            $status						    = 'BZ';
                                            break;

                                        case 'Возврат. Отсутствие адресата по указанному адресу':
                                            $status						    = 'BBO';
                                            break;


                                        case 'Возврат. Отказ адресата':
                                            $status						    = 'BOA';
                                            break;

                                        case 'Возврат. Смерть адресата':
                                            $status						    = 'BCA';
                                            break;

                                        case 'Возврат. Невозможно прочесть адрес адресата':
                                            $status						    = 'BNA';
                                            break;

                                        case 'Возврат. Отказ в выпуске таможней':
                                            $status						    = 'BOT';
                                            break;

                                        case 'Возврат. Адресат, абонирующий абонементный почтовый шкаф, не указан или указан неправильно':
                                            $status						    = 'BSN';
                                            break;

                                        case 'Возврат. Иные обстоятельства':
                                            $status						    = 'BIO';
                                            break;

                                        case 'Возврат. Неверный адрес':
                                            $status						    = 'BHA';
                                            break;

                                        case 'Досылка почты. По заявлению пользователя':
                                            $status						    = 'DPZ';
                                            break;

                                        case 'Досылка почты. Выбытие адресата по новому адресу':
                                            $status						    = 'DBA';
                                            break;

                                        case 'Досылка почты. Засылка':
                                            $status						    = 'DPZA';
                                            break;

                                        case 'Невручение. Утрачено':
                                            $status						    = 'NU';
                                            break;

                                        case 'Невручение. Изъято':
                                            $status						    = 'NI';
                                            break;

                                        case 'Невручение. Засылка':
                                            $status						    = 'NZ';
                                            break;

                                        case 'Невручение. Решение таможни':
                                            $status						    = 'NRT';
                                            break;

                                        case 'Невручение. Доставка по указанному адресу не осуществляется':
                                            $status						    = 'NO';
                                            break;

                                        case 'Хранение. До востребования':
                                            $status						    = 'XD';
                                            break;

                                        case 'Хранение. На абонементный ящик':
                                            $status						    = 'XA';
                                            break;

                                        case 'Хранение. Установленный срок хранения':
                                            $status						    = 'XU';
                                            break;

                                        case 'Хранение. Продление срока хранения по заявлению адресата':
                                            $status						    = 'XC';
                                            break;

                                        case 'Хранение. Продление срока выпуска таможней':
                                            $status						    = 'XVT';
                                            break;

                                        case 'Временное хранение. Нероздано':
                                            $status						    = 'VN';
                                            break;

                                        case 'Временное хранение. Невостребовано':
                                            $status						    = 'VNB';
                                            break;

                                        case 'Временное хранение. Содержимое запрещено к пересылке':
                                            $status						    = 'VXC';
                                            break;

                                        case 'Обработка. Сортировка':
                                            $status						    = 'OC';
                                            break;

                                        case 'Обработка. Покинуло место приёма':
                                            $status						    = 'OPP';
                                            break;

                                        case '. Прибыло в место вручения':
                                        case 'Обработка. Прибыло в место вручения':
                                            $status						    = 'OPM';
                                            break;

                                        case '. Прибыло в сортировочный центр':
                                        case 'Обработка. Прибыло в сортировочный центр':
                                            $status						    = 'OPC';
                                            break;

                                        case '. Покинуло сортировочный центр':
                                        case 'Обработка. Покинуло сортировочный центр':
                                            $status						    = 'OPPC';
                                            break;

                                        case 'Обработка. Прибыло в место международного обмена':
                                            $status						    = 'OMO';
                                            break;

                                        case 'Обработка. Покинуло место международного обмена':
                                            $status						    = 'OPO';
                                            break;

                                        case 'Обработка. Прибыло в место транзита':
                                            $status						    = 'OPT';
                                            break;

                                        case 'Обработка. Покинуло место транзита':
                                            $status						    = 'OPTM';
                                            break;

                                        case 'Обработка. Прибыло в почтомат':
                                            $status						    = 'OPPP';
                                            break;

                                        case 'Обработка. Истекает срок хранения в почтомате':
                                            $status						    = 'OIC';
                                            break;

                                        case 'Обработка. Переадресовано в почтомат':
                                            $status						    = 'OPR';
                                            break;

                                        case 'Обработка. Изъято из почтомата':
                                            $status						    = 'OIP';
                                            break;

                                        case 'Обработка. Прибыло на территорию РФ':
                                            $status						    = 'OPTRF';
                                            break;

                                        case 'Обработка. Прибыло в Центр выдачи посылок':
                                            $status						    = 'OPCV';
                                            break;

                                        case 'Обработка. Передано курьеру':
                                            $status						    = 'OPK';
                                            break;

                                        case 'Импорт международной почты':
                                            $status						    = 'IMP';
                                            break;

                                        case 'Экспорт международной почты':
                                            $status						    = 'AMP';
                                            break;

                                        case 'Принято на таможню':
                                            $status						    = 'PNT';
                                            break;

                                        case 'Неудачная попытка вручения. Временное отсутствие адресата':
                                            $status						    = 'NPV';
                                            break;

                                        case 'Неудачная попытка вручения. Доставка отложена по просьбе адресата':
                                            $status						    = 'NPD';
                                            break;

                                        case 'Неудачная попытка вручения. Неполный адрес':
                                            $status						    = 'NPN';
                                            break;

                                        case 'Неудачная попытка вручения. Неправильный адрес':
                                            $status						    = 'NPA';
                                            break;


                                        case 'Неудачная попытка вручения. Адресат выбыл':
                                            $status						    = 'NPAV';
                                            break;

                                        case 'Неудачная попытка вручения. Отказ от получения':
                                            $status						    = 'NPOV';
                                            break;

                                        case 'Неудачная попытка вручения. Обстоятельства непреодолимой силы':
                                            $status						    = 'NPON';
                                            break;

                                        case 'Неудачная попытка вручения. Иная':
                                            $status						    = 'NPVI';
                                            break;

                                        case 'Неудачная попытка вручения. Адресат заберет отправление сам':
                                            $status						    = 'NPAO';
                                            break;

                                        case 'Неудачная попытка вручения. Нет адресата':
                                            $status						    = 'NPVA';
                                            break;

                                        case 'Неудачная попытка вручения. По техническим причинам':
                                            $status						    = 'NPTA';
                                            break;

                                        case 'Неудачная попытка вручения. Истек срок хранения в почтомате':
                                            $status						    = 'NPXA';
                                            break;

                                        case 'Регистрация отправки':
                                            $status						    = 'RO';
                                            break;

                                        case 'Таможенное оформление. Выпущено таможней':
                                            $status						    = 'TOV';
                                            break;

                                        case 'Таможенное оформление. Возвращено таможней':
                                            $status						    = 'TOVT';
                                            break;

                                        case 'Таможенное оформление. Осмотрено таможней':
                                            $status						    = 'TOOT';
                                            break;

                                        case 'Таможенное оформление. Отказ в выпуске':
                                            $status						    = 'TOOV';
                                            break;

                                        case 'Таможенное оформление. Направлено с таможенным уведомлением':
                                            $status						    = 'TOTV';
                                            break;

                                        case 'Таможенное оформление. Направлено с обязательной уплатой таможенных платежей':
                                            $status						    = 'TUTP';
                                            break;

                                        case 'Передача на временное хранение':
                                            $status						    = 'PVX';
                                            break;

                                        case 'Уничтожение':
                                            $status						    = 'UN';
                                            break;

                                        case 'Передача вложения на баланс':
                                            $status						    = 'PVB';
                                            break;

                                        case 'Регистрация утраты':
                                            $status						    = 'RU';
                                            break;

                                        case 'Вручение. Не определено':
                                            $status						    = 'BNO';
                                            break;
                                        case 'Вручение. Адресату почтальоном':
                                            $status						    = 'BAP';
                                            break;

                                        case 'Обработка. Передача в кладовую хранения':
                                            $status						    = 'OPKL';
                                            break;

                                        case 'Вручение. Вручение получателю':
                                            $status						    = 'BBP';
                                            break;

                                        case 'Вручение. Доставляется курьером':
                                            $status						    = 'BDK';
                                            break;

                                        case 'Обработка. Передано почтальону':
                                            $status						    = 'OPPO';
                                            break;

                                        case 'Хранение. Передано на хранение':
                                            $status						    = 'HPH';
                                            break;

                                        case 'Неудачная попытка вручения. Неудачная доставка':
                                            $status						    = 'NPVD';
                                            break;

                                        case 'Неудачная попытка вручения. Адресат не доступен':
                                            $status						    = 'NPVA';
                                            break;

                                        case 'Неудачная попытка вручения. Нет доставки на дом':
                                            $status						    = 'NPVN';
                                            break;

                                        default:
                                            $status						    = 'N';


                                            $content                        = $last->operationType.(!empty($last->operationAttribute)?('. '.$last->operationAttribute):'');

                                            if(!empty($content)){

                                                if(!in_array($content,$statuses) || empty($statuses)){
                                                    $content                = trim($content);
                                                    if(!empty($content)){
                                                        $statuses[]         = $content;
                                                    };
                                                };

                                                $content                    = 'Order id: '.$ar_sales["ID"].': new status '.$status.' - previous status '.$previous_status.' - operation type: '.$content;

                                                if(!in_array($content,$orders) || empty($orders)){
                                                    $orders[]               =  $content;
                                                };


                                            };


                                            break;
                                    }

                                    if($status 							    !=$previous_status){

                                        ++$at_count;

                                        $arFields					        = array('VALUE'=>$status);



                                        if(!empty($previous_status_id)){
                                            CSaleOrderPropsValue::Update($previous_status_id,$arFields);
                                        } else {

                                            if ($arProp = CSaleOrderProps::GetList(array(), array('CODE' => "rupost_spy"))->Fetch()) {

                                                $order = \Bitrix\Sale\Order::loadByAccountNumber($ar_sales["ID"]);
                                                $propertyCollection = $order->getPropertyCollection();
                                                $propertyValue = $propertyCollection->getItemByOrderPropertyId($arProp['ID']);

                                                if($propertyValue){
                                                    $propertyValue->setField('VALUE', $status);
                                                    $propertyValue->save();
                                                }

                                            };


                                        }

                                        $last_record				        = $last->operationRecord;
                                        $first_record				        = $first->operationRecord;

                                        $arMail								= array();
                                        $arMail['TRACKING_ORIGINAL_SITE']	= 'http://www.russianpost.ru/tracking20/';

                                        $arMail['BARCODE']                  = '';

                                        if(is_object($first_record)
                                            && isset($first_record->ItemParameters)
                                            && is_object($first_record->ItemParameters)
                                            && isset($first_record->ItemParameters->Barcode)
                                        ){

                                            $arMail['BARCODE']			    = (string)$first_record->ItemParameters->Barcode;

                                        }

                                        if(isset($arMail['BARCODE'])){
                                            $arMail['ACCEPTED_FOR_CARRIAGE']= 'Y';
                                        }

                                        if(is_object($first_record)
                                            && isset($first_record->AddressParameters)
                                            && is_object($first_record->AddressParameters)
                                            && isset($first_record->AddressParameters->OperationAddress)
                                            && is_object($first_record->AddressParameters->OperationAddress)
                                            && isset($first_record->AddressParameters->OperationAddress->Description)
                                        ){

                                            $arMail['ACCEPTED_FOR_CARRIAGE']= (string)$first_record->AddressParameters->OperationAddress->Description;

                                        }

                                        if(is_object($first_record)
                                            && isset($first_record->ItemParameters)
                                            && is_object($first_record->ItemParameters)
                                            && isset($first_record->ItemParameters->ComplexItemName)
                                        ){

                                            $arMail['ACCEPTED_FOR_CARRIAGE'].= (string)$first_record->ItemParameters->ComplexItemName." \n";

                                        }

                                        if(is_object($first_record)
                                            && isset($first_record->ItemParameters)
                                            && is_object($first_record->ItemParameters)
                                            && isset($first_record->ItemParameters->MailRank)
                                            && is_object($first_record->ItemParameters->MailRank)
                                            && isset($first_record->ItemParameters->MailRank->Name)
                                        ){

                                            $arMail['ACCEPTED_FOR_CARRIAGE'].= (string)$first_record->ItemParameters->MailRank->Name." \n";

                                        }

                                        if(is_object($first_record)
                                            && isset($first_record->ItemParameters)
                                            && is_object($first_record->ItemParameters)
                                            && isset($first_record->ItemParameters->PostMark)
                                            && is_object($first_record->ItemParameters->PostMark)
                                            && isset($first_record->ItemParameters->PostMark->Name)
                                        ){

                                            $arMail['ACCEPTED_FOR_CARRIAGE'].= (string)$first_record->ItemParameters->PostMark->Name." \n";

                                        }

                                        $arMail['SENDER']                   = '';

                                        if(is_object($first_record)
                                            && isset($first_record->UserParameters)
                                            && is_object($first_record->UserParameters)
                                            && isset($first_record->UserParameters->Sndr)
                                        ){

                                            $arMail['SENDER'] 				= (string)$first_record->UserParameters->Sndr;

                                        }

                                        $arMail['RECIPIENT']                = '';

                                        if(is_object($first_record)
                                            && isset($first_record->UserParameters)
                                            && is_object($first_record->UserParameters)
                                            && isset($first_record->UserParameters->Rcpn)
                                        ){

                                            $arMail['RECIPIENT'] 			= (string)$first_record->UserParameters->Rcpn;

                                        }

                                        $arMail['OPERATION']                = '';

                                        if(is_object($first_record)
                                            && isset($first_record->OperationParameters)
                                            && is_object($first_record->OperationParameters)
                                            && isset($first_record->OperationParameters->OperType)
                                            && is_object($first_record->OperationParameters->OperType)
                                            && isset($first_record->OperationParameters->OperType->Name)
                                        ){

                                            $arMail['OPERATION'] 			= (string)$first_record->OperationParameters->OperType->Name;

                                        }

                                        $arMail['DATE']                     = '';

                                        if(is_object($first_record)
                                            && isset($first_record->OperationParameters)
                                            && is_object($first_record->OperationParameters)
                                            && isset($first_record->OperationParameters->OperDate)
                                        ){

                                            $arMail['DATE'] 			    = (string)$first_record->OperationParameters->OperDate;
                                            $date				  	 	    = strtotime($arMail['DATE']);

                                            if($date){

                                                $arMail['DATE']			    = date('Y.m.d H:i',$date);
                                            }

                                        }

                                        $arMail['VENUE_OPERATIONS_INDEX']   = '';

                                        if(is_object($first_record)
                                            && isset($first_record->AddressParameters)
                                            && is_object($first_record->AddressParameters)
                                            && isset($first_record->AddressParameters->OperationAddress)
                                            && is_object($first_record->AddressParameters->OperationAddress)
                                            && isset($first_record->AddressParameters->OperationAddress->Index)
                                        ){

                                            $arMail['VENUE_OPERATIONS_INDEX']
                                                = (string)$first_record->AddressParameters->OperationAddress->Index;

                                        }

                                        $arMail['VENUE_OPERATIONS_TITLE_OPS'] = '';

                                        if(is_object($first_record)
                                            && isset($first_record->AddressParameters)
                                            && is_object($first_record->AddressParameters)
                                            && isset($first_record->AddressParameters->OperationAddress)
                                            && is_object($first_record->AddressParameters->OperationAddress)
                                            && isset($first_record->AddressParameters->OperationAddress->Description)
                                        ){

                                            $arMail['VENUE_OPERATIONS_TITLE_OPS']
                                                = (string)$first_record->AddressParameters->OperationAddress->Description;

                                        }

                                        $arMail['ATTRIBUTE_OPERATIONS']     = '';

                                        if(is_object($first_record)
                                            && isset($first_record->OperationParameters)
                                            && is_object($first_record->OperationParameters)
                                            && isset($first_record->OperationParameters->OperAttr)
                                            && is_object($first_record->OperationParameters->OperAttr)
                                            && isset($first_record->OperationParameters->OperAttr->Name)
                                        ){

                                            $arMail['ATTRIBUTE_OPERATIONS'] = (string)$first_record->OperationParameters->OperAttr->Name;

                                        }

                                        $arMail['MASS']                     = '';

                                        if(is_object($first_record)
                                            && isset($first_record->ItemParameters)
                                            && is_object($first_record->ItemParameters)
                                            && isset($first_record->ItemParameters->Mass)
                                        ){

                                            $arMail['MASS'] 	            = ((int)((string)$first_record->ItemParameters->Mass)) / 1000;

                                        }

                                        $arMail['DECLARED_VALUE']           = '';

                                        if(is_object($first_record)
                                            && isset($first_record->FinanceParameters)
                                            && is_object($first_record->FinanceParameters)
                                            && isset($first_record->FinanceParameters->Value)
                                        ){

                                            $arMail['DECLARED_VALUE'] 	    = round(floatval(trim($first_record->FinanceParameters->Value)) / 100, 2);

                                        }

                                        $arMail['CASH_ON_DELIVERY']         = '';

                                        if(is_object($first_record)
                                            && isset($first_record->FinanceParameters)
                                            && is_object($first_record->FinanceParameters)
                                            && isset($first_record->FinanceParameters->Payment)
                                        ){

                                            $arMail['CASH_ON_DELIVERY'] 	= round(floatval(trim($first_record->FinanceParameters->Payment)) / 100, 2);

                                        }

                                        $arMail['ADDRESSED_INDEX']          = '';

                                        if(is_object($first_record)
                                            && isset($first_record->AddressParameters)
                                            && is_object($first_record->AddressParameters)
                                            && isset($first_record->AddressParameters->DestinationAddress)
                                            && is_object($first_record->AddressParameters->DestinationAddress)
                                            && isset($first_record->AddressParameters->DestinationAddress->Index)
                                        ){

                                            $arMail['ADDRESSED_INDEX'] 	    = (string)$first_record->AddressParameters->DestinationAddress->Index;

                                        }

                                        $arMail['ADDRESSED_LOCATION']       = '';

                                        if(is_object($first_record)
                                            && isset($first_record->AddressParameters)
                                            && is_object($first_record->AddressParameters)
                                            && isset($first_record->AddressParameters->DestinationAddress)
                                            && is_object($first_record->AddressParameters->DestinationAddress)
                                            && isset($first_record->AddressParameters->DestinationAddress->Description)
                                        ){

                                            $arMail['ADDRESSED_LOCATION']   = (string)$first_record->AddressParameters->DestinationAddress->Description;

                                        }

                                        $arMail['SALE_ORDER_ID']			= $ar_sales["ID"];
                                        $arMail['SALE_EMAIL']			   	= $ar_sales["USER_EMAIL"];
                                        $arMail['SALE_FULL_NAME']			= $ar_sales["USER_NAME"].' '.$ar_sales["USER_LAST_NAME"];

                                        $db_sales_props						= CSaleOrderPropsValue::GetList(
                                            array("SORT" 				=>"ASC"),
                                            array(
                                                "CODE"				=>"FIO",
                                                "ORDER_ID"			=>$ar_sales["ID"]
                                            ),
                                            false,
                                            false,
                                            array()
                                        );

                                        $ar_sales_props						= array();
                                        if($db_sales_props
                                            && is_object($db_sales)
                                            && method_exists($db_sales_props,'Fetch')
                                            && $ar_sales_props					= $db_sales_props->Fetch()
                                        ){

                                            if(isset($ar_sales_props['VALUE'])){

                                                $arMail['SALE_FULL_NAME']	= $ar_sales_props['VALUE'];
                                            }

                                        }

                                        $db_sales_props						= CSaleOrderPropsValue::GetList(
                                            array("SORT" 				=>"ASC"),
                                            array(
                                                "CODE"				=>"EMAIL",
                                                "ORDER_ID"			=>$ar_sales["ID"]
                                            ),
                                            false,
                                            false,
                                            array()
                                        );

                                        $ar_sales_props						= array();
                                        if($db_sales_props
                                            && is_object($db_sales)
                                            && method_exists($db_sales_props,'Fetch')
                                            && $ar_sales_props					= $db_sales_props->Fetch()
                                        ){

                                            if(isset($ar_sales_props['VALUE'])){

                                                $arMail['SALE_EMAIL']		= $ar_sales_props['VALUE'];
                                            }

                                        }

                                        $db_sales_props						= CSaleOrderPropsValue::GetList(
                                            array("SORT" 				=>"ASC"),
                                            array(
                                                "CODE"				=>"PHONE",
                                                "ORDER_ID"			=>$ar_sales["ID"]
                                            ),
                                            false,
                                            false,
                                            array()
                                        );

                                        $arMail['SALE_PHONE']               = '';

                                        $ar_sales_props						= array();
                                        if($db_sales_props
                                            && is_object($db_sales)
                                            && method_exists($db_sales_props,'Fetch')
                                            && $ar_sales_props					= $db_sales_props->Fetch()
                                        ){

                                            if(isset($ar_sales_props['VALUE'])){

                                                $arMail['SALE_PHONE']		= $ar_sales_props['VALUE'];
                                            }

                                        }

                                        $db_sales_props						= CSaleOrderPropsValue::GetList(
                                            array("SORT" 				=>"ASC"),
                                            array(
                                                "CODE"				=>"ADDRESS",
                                                "ORDER_ID"			=>$ar_sales["ID"]
                                            ),
                                            false,
                                            false,
                                            array()
                                        );

                                        $arMail['SALE_ADDRESS']             = '';

                                        $ar_sales_props						= array();
                                        if($db_sales_props
                                            && is_object($db_sales)
                                            && method_exists($db_sales_props,'Fetch')
                                            && $ar_sales_props					= $db_sales_props->Fetch()
                                        ){

                                            if(isset($ar_sales_props['VALUE'])){

                                                $arMail['SALE_ADDRESS']		= $ar_sales_props['VALUE'];
                                            }

                                        }

                                        $arMail['POST_STATUS_OLD']          = '';

                                        if(isset($ar_sales_statuses[$previous_status]) && !empty($ar_sales_statuses[$previous_status])){
                                            $arMail['POST_STATUS_OLD']		= $ar_sales_statuses[$previous_status];
                                        };

                                        $arMail['POST_STATUS']              = '';

                                        if(isset($ar_sales_statuses[$status]) && !empty($ar_sales_statuses[$status])){
                                            $arMail['POST_STATUS']			= $ar_sales_statuses[$status];
                                        };

                                        CEvent::Send('SALE_USER_RUSPOST_SEND', SITE_ID, $arMail);
                                        CEvent::Send('SALE_ADMIN_RUSPOST_SEND', SITE_ID, $arMail);

                                        if(in_array($status,array('OPM','OPPO','HPH','NPVN','NPVA','NPVD'))){
                                            CEvent::Send('SALE_USER_RUSPOST_ARRIVED', SITE_ID, $arMail);
                                            CSaleOrder::StatusOrder($ar_sales["ID"], "A");
                                        }

                                        if(in_array($status,array('OPM','OPPO'))){

                                            if($ar_sales['DEDUCTED'] != 'Y'
                                                && $ar_sales['MARKED'] != 'Y') {
                                                CSaleOrder::DeductOrder($ar_sales["ID"], "Y");
                                            }

                                        }


                                        if(in_array($status,array('BA','BP','BAK','BAP','BBP'))
                                            && isset($ar_sales["ID"])
                                            && !empty($ar_sales["ID"])){

                                            if($ar_sales['DEDUCTED'] != 'Y'
                                                && $ar_sales['MARKED'] != 'Y') {

                                                CSaleOrder::DeductOrder($ar_sales["ID"], "Y");
                                                //CSaleOrder::StatusOrder($ar_sales["ID"], "F");
                                            }

                                            if($ar_sales['PAYED'] != 'Y'
                                            && $ar_sales['MARKED'] != 'Y'){
                                                CSaleOrder::PayOrder($ar_sales["ID"], "Y", false, false, 0, array());
                                            }
                                            CSaleOrder::StatusOrder($ar_sales["ID"], "P");

                                        }

                                        //H
                                        if(in_array($status,array('BI','BOA','BO','BOP','BZ'))
                                            && isset($ar_sales["ID"])
                                            && !empty($ar_sales["ID"])){
                                            CSaleOrder::StatusOrder($ar_sales["ID"], "H");
                                        }

                                        if(in_array($status,array('BNO','BBO','BCA','BNA','BOT','BSN','BIO','BHA','NU','NI','NRT','NO','UN','RU'))
                                            && isset($ar_sales["ID"])
                                            && !empty($ar_sales["ID"])){
                                            CSaleOrder::CancelOrder($ar_sales["ID"],"Y");
                                            CSaleOrder::StatusOrder($ar_sales["ID"], "R");
                                        }

                                    }
                                }

                            }

                        }
                    }
            }
        }
    }

    if(!empty($statuses) && !empty($orders)){

        $statuses                                                               = join("\n",$statuses);
        file_put_contents(ACTIONS_FILE,$statuses);

        $orders                                                                 = join("\n",$orders);
        file_put_contents(ORDERS_FILE,$orders);


        if((filemtime(ACTIONS_FILE) + $time_to_send) < time()){

            $arMail                                                             = array(
                'MISSED_STATUES'                                                =>$statuses,
                'MISSED_ORDERS'                                                 =>$orders
            );

            CEvent::Send('MISSED_ACTIONS', SITE_ID, $arMail);

            $fp                                                                 = fopen(ACTIONS_FILE,'w');
            fclose($fp);

            $fp                                                                 = fopen(ORDERS_FILE,'w');
            fclose($fp);

        };

    };

}

//rusPostTraking();

function rusPostTrakingArrivedSMS(){

    global $USER;

    if(CModule::IncludeModule("iblock")
        && CModule::IncludeModule("currency")
        && CModule::IncludeModule("sale")
        && CModule::IncludeModule("catalog")){

        $currentHour											                = date('H');

        if(($currentHour                                                        >=6
            && $currentHour                                                 <=8)){


            $time 													            = time() - 3 * 86400;


            $time													            = date("d.m.Y",$time);

            $arFilter                                                           = Array(
                'DELIVERY_ID'									                =>3,
                'STATUS_ID'	 									                =>"A",
                '!CANCELED' 									                =>"Y",
                //'ID'											                =>2432,
                '<=DATE_STATUS'									                =>$time
            );


            $db_sales 												            = CSaleOrder::GetList(
                array("DATE_INSERT"                                             =>"ASC",
                    "DATE_UPDATE"                                               =>"ASC"),
                $arFilter,
                false,
                false,
                array('*')
            );
            if($db_sales
                && is_object($db_sales)
                && method_exists($db_sales,'Fetch')){
                $ar_sales											            = array();
                $at_count											            = 0;


                while (($ar_sales									            = $db_sales->Fetch())){

                    //фио мыло трек номер статус у заказа дата изменения статуса заказа
                    //$ar_sales["ID"] USER_NAME USER_EMAIL USER_LAST_NAME TRACKING_NUMBER STATUS_ID DATE_STATUS

                    $about_product									            = array();

                    $USER_ID										            = $ar_sales["USER_ID"];

                    $userDB					  						            = $USER->GetByID($USER_ID);
                    $userFields				  						            = $userDB->Fetch();

                    $old_send_sms									            = (isset($userFields["UF_SMS_INFORM"]) && !empty($userFields["UF_SMS_INFORM"])) ? 1 : 0;

                    $fields 										            = Array(
                        "UF_SMS_INFORM" 							            =>1,
                    );

                    $order_id										            = $ar_sales["ID"];

                    $status_id										            = $ar_sales["STATUS_ID"];
                    $date_update									            = $ar_sales["DATE_STATUS"];

                    $USER->Update($USER_ID, $fields);

                    CSaleOrder::StatusOrder($order_id,'Z');
                    CSaleOrder::StatusOrder($order_id,$status_id);

                    $fields 										            = Array(
                        "UF_SMS_INFORM" 							            =>($old_send_sms) ? 1 : 0,
                    );

                    $USER->Update($USER_ID, $fields);

                    CEvent::SendImmediate('RUS_POST_RECIVED', SITE_ID, $about_product);

                }
            }
        }
    }

}

//rusPostTrakingArrivedSMS();