<?
/**
 * Orders data synchronization
 */

namespace Acrit\Core\Orders;

use Bitrix\Main,
	Bitrix\Main\Type,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\SiteTable,
	Bitrix\Main\Context,
	Bitrix\Currency\CurrencyManager,
	Bitrix\Sale,
	Bitrix\Sale\Order,
	Bitrix\Sale\Basket,
	Bitrix\Sale\Delivery,
	Bitrix\Sale\PaySystem,
	\Acrit\Core\Log,
	\Acrit\Core\Helper;
use PhpOffice\PhpSpreadsheet\Exception;

Loc::loadMessages(__FILE__);

class OrderSync {

	/**
	 * Run order synchronization
	 */
	public static function runSync($order_id, $ext_order, $profile) {
	    $info = '';
		$result = [];
		$order_data = [];
		// Update order
		$order = false;
		$new_order = false;
		if ($order_id) {
			$order = Order::load($order_id);
			$order_data = self::getOrderInfo($order);
			//Log::getInstance(Controller::$MODULE_ID, 'orders')->add('(OrderSync::runSync) order_data ' . print_r($order_data, true), $profile['ID'], true);
		}
		// Create order
		else {
            $site_id = Controller::getSiteDef();
		    if ($profile['CONNECT_DATA']['LID']) {
                $site_id = $profile['CONNECT_DATA']['LID'];
//                file_put_contents(__DIR__.'/lid.txt', $site_id);
            }
			$currency_code = CurrencyManager::getBaseCurrency();
			$pay_type = $profile['CONNECT_DATA']['pay_type'];
			// Get buyer
			$user_id = BuyerSync::runSync($ext_order, $profile);
			// Create order
//            if ($profile['ID'] != 9 || $profile['ID'] != 10) {
                if ($user_id && $pay_type) {
                    $order = Order::create($site_id, $user_id);
                    $order->setPersonTypeId($pay_type);
                    $order->setField('CURRENCY', $currency_code);
                    $order->setField('XML_ID', $ext_order['ID']);
                    $order->setField('RESPONSIBLE_ID', $profile['CONNECT_DATA']['responsible']);
                    if ($profile['CONNECT_DATA']['number']['on'] === 'Y') {
                        $params = [
                            'prefix' => $profile['CONNECT_DATA']['number']['prefix'],
                            'scheme' => $profile['CONNECT_DATA']['number']['scheme'],
                            'separator' => $profile['CONNECT_DATA']['number']['separator'],
                        ];
                        $info .= 'ACRIT_NUMBER(~';
                        $info .= serialize($params);
                        $info .= '~)';

                    }
                    $order->setField('ADDITIONAL_INFO', $info);
                    $new_order = true;
                }
//            }
		}
		if ($order) {
			try {
				// Update status
				$status_changed = self::updateOrderStatus($ext_order, $order_data, $profile, $order);
				// Update properties
				$props_changed = self::updateOrderProps($ext_order['FIELDS'], $order_data, $profile, $order);
				// Update order products
                if ( $profile['CONNECT_DATA']['cartblock'] !== 'Y' || $new_order ) {
                    if ($profile['ID'] == 9) {
                        $contin = true;
                    }elseif ($profile['ID'] == 10){
                        $contin = true;
                    }else{
                        $products_changed = self::updateOrderProducts($ext_order, $order_data, $profile, $order);
                    }
                }
			} catch (Exception $e) {
				Log::getInstance(Controller::$MODULE_ID, 'orders')->add($e->getMessage() . ' [' . $e->getCode() . ']', $profile['ID'], true);
			}

            Log::getInstance(Controller::$MODULE_ID, 'orders')->add('(OrderSync::runSync) order ' . $order_id . ' changed fields [status:' . $status_changed . ', props:' . $props_changed . ', products:' . $products_changed . ']', $profile['ID'], true);
		}
		else {
			$result = false;
		}

        if ( \Bitrix\Main\Loader::IncludeModule('crm') ) {
            $company_id = $profile['CONNECT_DATA']['company'];
            $contact_id = $profile['CONNECT_DATA']['contact'];
            $communication = $order->getContactCompanyCollection();
            $db = \Bitrix\Sale\Internals\BusinessValuePersonDomainTable::getList([
                'filter' => ['PERSON_TYPE_ID' => $order->getPersonTypeId() ],
                'select' => ['DOMAIN']
            ]);
            $type_payer = $db->Fetch()['DOMAIN'];
            if ( $company_id && $type_payer == 'E' ) {
                foreach ( $communication->getCompanies() as $item )
                {
                    $company = $item;
                    $company->setField('ENTITY_ID', $company_id);
                    break;
                }
                if ( !$company ) {
                    $company = \Bitrix\Crm\Order\Company::create($communication);
                    $company->setField('ENTITY_ID', $company_id);
                    $company->setField('IS_PRIMARY', 'Y');
                    $communication->addItem($company);
                }
            }
            if ( $contact_id ) {
                foreach ( $communication->getContacts() as $item )
                {
                    $contact = $item;
                    $contact->setField('ENTITY_ID', $contact_id);
                    break;
                }
                if ( !$contact ) {
                    $contact = \Bitrix\Crm\Order\Contact::create($communication);
                    $contact->setField('ENTITY_ID', $contact_id);
                    $contact->setField('IS_PRIMARY', 'Y');
                    $communication->addItem($contact);
                }
            }
        }

//		if ($new_order && $products_changed) {
		if ($new_order || $products_changed) {
			// Delivery method
			$shipmentCollection = $order->getShipmentCollection();
//			$shipment = $shipmentCollection->createItem();
            foreach ($shipmentCollection->getNotSystemItems() as $item)
            {
                $shipment = $item ;
                break;
            }
            if ( !$shipment ) {
                $shipment = $shipmentCollection->createItem();
            }
			$deliv_method = (int)$profile['CONNECT_DATA']['deliv_method'];
			if ($deliv_method) {
				$deliveryId = $deliv_method;
			}
			else {
				$deliveryId = Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId();
			}
			$service = Delivery\Services\Manager::getById($deliveryId);
			$shipment->setFields([
				'DELIVERY_ID'   => $service['ID'],
				'DELIVERY_NAME' => $service['NAME'],
				'CURRENCY' => $order->getCurrency(),
			]);
			$shipmentItemCollection = $shipment->getShipmentItemCollection();
			foreach ($order->getBasket() as $item) {
				$shipmentItem = $shipmentItemCollection->createItem($item);
				$shipmentItem->setQuantity($item->getQuantity());
			}
			// Payment method
			$pay_method = (int)$profile['CONNECT_DATA']['pay_method'];
			if ($pay_method) {
				$paymentCollection = $order->getPaymentCollection();
                foreach ($paymentCollection as $item )
                {
                    $payment = $item;
                    break;
                }
                if ( !$payment ) {
                    $payment = $paymentCollection->createItem();
                }
//				$payment = $paymentCollection->createItem();
				$paySystemService = PaySystem\Manager::getObjectById($pay_method);
				$payment->setFields(array(
					'PAY_SYSTEM_ID'   => $paySystemService->getField("PAY_SYSTEM_ID"),
					'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
					'SUM' => $order->getPrice(),
				));
				if ($profile['CONNECT_DATA']['is_paid'] == 'Y') {
                    $payment->setPaid('Y');
                }
			}
		}
		// Save changes
		if ($new_order || $status_changed || $props_changed || $products_changed) {
//			Log::getInstance(Controller::$MODULE_ID, 'orders')->add('(OrderSync::runSync) save order ' . $order_id, $profile['ID'], true);
			if ($new_order || Settings::get('run_save_final_action') != 'disabled') {
				$order->doFinalAction(true);
			}
			$save_res = $order->save();
			if (!$save_res->isSuccess()) {
				$result = false;
                Log::getInstance(Controller::$MODULE_ID, 'orders')->add('(OrderSync::runSync) save order error: ' . print_r($save_res->getErrorMessages(), true), $profile['ID'], true);
			}
			else {
				$order_id = $order->getId();
                if ($profile['ID'] == 9) {
                    $contin = true;
                }elseif ($profile['ID'] == 10){
                    $contin = true;
                }else{
                    Log::getInstance(Controller::$MODULE_ID, 'orders')->add('(OrderSync::runSync) save order ' . $order->getId() . ' success', $profile['ID'], true);
                }
			}
			if ($new_order) {
				$order = Order::load($order_id);
				$order_data = self::getOrderInfo($order);
                if ($profile['ID'] == 9) {
                    $contin = true;
                }elseif ($profile['ID'] == 10){
                    $contin = true;
                }else{
                    Log::getInstance(Settings::getModuleId(), 'orders')->add('(OrderSync::runSync) created order ' . print_r($order_data, true), $profile['ID'], true);
                }
			}
		}
        $result['id'] = $order_id;
		return $result;
	}

	/**
	 * Search the store order by data of the marketplace order
	 */

	public static function findOrder(array $ext_order, $profile) {
		$order_id = false;
		$filter = [
		    "XML_ID" => $ext_order['ID']
        ];
        if ($profile['CONNECT_DATA']['user_control'] == 'Y') {
            $filter = [
                "XML_ID" => $ext_order['ID'],
                "USER_ID" => BuyerSync::runSync($ext_order, $profile)
            ];
        }
        // file_put_contents(__DIR__.'/filter.txt', var_export($filter, true));
		$res = \Bitrix\Sale\Order::getList([
			'select' => ['ID'],
			'filter' => $filter,
		]);

		if ($order = $res->fetch()){
			$order_id = $order['ID'];
		}
		return $order_id;
	}

	/**
	 * Update order status
	 */

	public static function updateOrderStatus(array $ext_order, array $order_data, $profile, &$order) {
		$has_changes = false;
		// Formation of a table of correspondence fields
		$status_table = [];
		$tmp_table = (array)$profile['STAGES']['table_compare'];
		foreach ($tmp_table as $s_status => $sync_params) {
			$e_statuses = $sync_params;
			$e_statuses = array_diff($e_statuses, ['']);
			foreach ($e_statuses as $e_status) {
				$status_table[$e_status][] = $s_status;
			}
		}
		$cancel_table = (array)$profile['STAGES']['table_cancel'];
		// Formation of a data for saving
		$new_e_stage = $ext_order['STATUS_ID'];
		$new_s_statuses = $status_table[$new_e_stage];
		$cur_s_status = $order_data['STATUS_ID'];
		if ($new_e_stage && !empty($new_s_statuses) && !in_array($cur_s_status, $new_s_statuses)) {
			$order->setField('STATUS_ID', $new_s_statuses[0]);
			$has_changes = true;
		}
		if ($new_e_stage && !empty($cancel_table)) {
			if (in_array($new_e_stage, $cancel_table) && $order_data['IS_CANCELED'] != 'Y') {
//				$order->setField('CANCELED', 'Y');
				\CSaleOrder::CancelOrder($order_data['ID'], 'Y');
//				if (Settings::get('cancel_pays_by_cancel_order')) {
					$payments = $order->getPaymentCollection();
					foreach ($payments as $payment) {
						$payment->setPaid('N');
//						$payment->save();
					}
//				}
				$has_changes = true;
			}
			elseif (!in_array($new_e_stage, $cancel_table) && $order_data['IS_CANCELED'] == 'Y') {
//				$order->setField('CANCELED', 'N');
				\CSaleOrder::CancelOrder($order_data['ID'], 'N');
				$has_changes = true;
			}
		}
		return $has_changes;
	}


	/**
	 * Update order properties
	 */

	public static function updateOrderProps(array $ext_order, array $order_data, $profile, &$order) {
//        file_put_contents(__DIR__.'/site_id.txt', SITE_ID);
		foreach (\Bitrix\Main\EventManager::getInstance()->findEventHandlers(Controller::$MODULE_ID, 'OnUpdateOrderProps') as $arHandler) {
			\ExecuteModuleEventEx($arHandler, [&$ext_order, &$order_data, &$profile, &$order]);
		}
		$has_changes = false;
		$person_type = $order_data['PERSON_TYPE_ID'];
		// Formation of a table of correspondence fields
		$comp_table = [];
		$tmp_table = (array)$profile['FIELDS']['table_compare'];
		foreach ($tmp_table as $o_prop_id => $sync_params) {
			$e_field_code = $sync_params['value'];
			if ($e_field_code && ($sync_params['direction'] == Plugin::SYNC_ALL || $sync_params['direction'] == Plugin::SYNC_CTOS)
				&& $order_data['PROPERTIES'][$o_prop_id]['PERSON_TYPE_ID'] == $person_type) {
				$comp_table[$e_field_code] = $o_prop_id;
			}
		}
		// Formation of a data for saving
		$property_collection = $order->getPropertyCollection();
        foreach ($comp_table as $e_field_code => $o_prop_id) {
			$prop = (array) $order_data['PROPERTIES'][$o_prop_id];
			$prop_value = $property_collection->getItemByOrderPropertyId($o_prop_id);
			if ($prop_value) {
				$new_value = [];
				$ext_field = $ext_order[$e_field_code];
        		$ext_value = $ext_field['VALUE'];
        		if ( !is_array($ext_value)) {
					$ext_value = [$ext_value];
				}
                if ( is_array($profile['OTHER']['FIELDS_DATE']) && in_array($o_prop_id, $profile['OTHER']['FIELDS_DATE']) ) {
                    $ext_value[0] =  date('d.m.Y', strtotime($ext_value[0]));
                }
				// Store types
				if ($prop['TYPE'] == 'LOCATION' || $prop['TYPE'] == 'FILE') {
					continue;
				}
				if ($prop['TYPE'] == 'ENUM') {
					foreach ($ext_value as $e_val_variant) {
						foreach ($prop['OPTIONS'] as $prop_code => $prop_val) {
							if ($prop_val == $e_val_variant) {
								$new_value[] = $prop_code;
							}
						}
					}
				}
//			elseif ($prop['TYPE'] == 'FILE') {
//				foreach ($deal[$d_field_code] as $deal_value) {
//					if ($deal_value['downloadUrl']) {
//						$app_info = Rest::getAppInfo();
//						$file = $app_info['portal'] . $deal_value['downloadUrl'];
//						$arFile = \CFile::MakeFileArray($file);
//						$arFile['name'] = strtolower(base64_encode($deal_value['id']));
//						$all_mimes = '{"png":["image\/png","image\/x-png"],"bmp":["image\/bmp","image\/x-bmp","image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp","image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp","application\/x-win-bitmap"],"gif":["image\/gif"],"jpeg":["image\/jpeg","image\/pjpeg"],"xspf":["application\/xspf+xml"],"vlc":["application\/videolan"],"wmv":["video\/x-ms-wmv","video\/x-ms-asf"],"au":["audio\/x-au"],"ac3":["audio\/ac3"],"flac":["audio\/x-flac"],"ogg":["audio\/ogg","video\/ogg","application\/ogg"],"kmz":["application\/vnd.google-earth.kmz"],"kml":["application\/vnd.google-earth.kml+xml"],"rtx":["text\/richtext"],"rtf":["text\/rtf"],"jar":["application\/java-archive","application\/x-java-application","application\/x-jar"],"zip":["application\/x-zip","application\/zip","application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],"7zip":["application\/x-compressed"],"xml":["application\/xml","text\/xml"],"svg":["image\/svg+xml"],"3g2":["video\/3gpp2"],"3gp":["video\/3gp","video\/3gpp"],"mp4":["video\/mp4"],"m4a":["audio\/x-m4a"],"f4v":["video\/x-f4v"],"flv":["video\/x-flv"],"webm":["video\/webm"],"aac":["audio\/x-acc"],"m4u":["application\/vnd.mpegurl"],"pdf":["application\/pdf","application\/octet-stream"],"pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],"ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office","application\/msword"],"docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],"xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],"xl":["application\/excel"],"xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel","application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],"xsl":["text\/xsl"],"mpeg":["video\/mpeg"],"mov":["video\/quicktime"],"avi":["video\/x-msvideo","video\/msvideo","video\/avi","application\/x-troff-msvideo"],"movie":["video\/x-sgi-movie"],"log":["text\/x-log"],"txt":["text\/plain"],"css":["text\/css"],"html":["text\/html"],"wav":["audio\/x-wav","audio\/wave","audio\/wav"],"xhtml":["application\/xhtml+xml"],"tar":["application\/x-tar"],"tgz":["application\/x-gzip-compressed"],"psd":["application\/x-photoshop","image\/vnd.adobe.photoshop"],"exe":["application\/x-msdownload"],"js":["application\/x-javascript"],"mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],"rar":["application\/x-rar","application\/rar","application\/x-rar-compressed"],"gzip":["application\/x-gzip"],"hqx":["application\/mac-binhex40","application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],"cpt":["application\/mac-compactpro"],"bin":["application\/macbinary","application\/mac-binary","application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],"ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],"wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],"dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php","application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],"swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],"mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],"rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],"jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],"eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],"p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],"p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],"wma":["audio\/x-ms-wma"],"vcf":["text\/x-vcard"],"srt":["text\/srt"],"vtt":["text\/vtt"],"ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],"csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],"json":["application\/json","text\/json"]}';
//						$all_mimes = json_decode($all_mimes, true);
//						foreach ($all_mimes as $ext => $arTypes) {
//							if (array_search($arFile['type'], $arTypes) !== false) {
//								$arFile['name'] .= '.' . $ext;
//							}
//						}
//						$file_id = \CFile::SaveFile($arFile, "sale");
//						if ($file_id) {
//							$new_value[] = $file_id;
//						}
//					}
//				}
//			}
				elseif ($prop['TYPE'] == 'DATE') {
					$new_value = $ext_value;
					if ($new_value[0]) {
						if ($prop['TIME'] == 'Y') {
							$new_value[0] = ConvertTimeStamp(strtotime($new_value[0]), "FULL", SITE_ID);
						} else {
							$new_value[0] = ConvertTimeStamp(strtotime($new_value[0]), "SHORT", SITE_ID);
						}
					}
				} else {
					$new_value = $ext_value;
					if (!Helper::isUtf() && $profile['CONNECT_DATA']['convert'] === 'Y') {
                        $new_value = Helper::convertEncoding($ext_value, 'UTF-8', 'CP1251');
                    }
				}
				// Has new value
				if ( ! self::isEqual($prop['VALUE'], $new_value)) {
					$new_value = count($new_value) == 1 ? $new_value[0] : $new_value;
					$prop_value->setValue($new_value);
					$has_changes = true;
                    if ($profile['ID'] == 9) {
                        $contin = true;
                    }elseif ($profile['ID'] == 10){
                        $contin = true;
                    }else{
                        Log::getInstance(Controller::$MODULE_ID, 'orders')->add('(updateOrderProps) order ' . $order_data['ID'] . ' new ' . $o_prop_id . ': ' . print_r($new_value, true), $profile['ID'], true);
                    }
				}
			}
		}
		return $has_changes;
	}

	/**
	 * Update order products
	 */

	public static function updateOrderProducts(array $ext_order, array $order_data, $profile, &$order) {
	    $ext_product_arr = [];
	    $basket_product_arr = [];
//        file_put_contents(__DIR__.'/lidbasket.txt', $order->getField('LID'));
        $ratio_active = $profile['CONNECT_DATA']['ratio'];
		$has_changes = false;
//		$site_id = Controller::getSiteDef();
		$site_id = $order->getField('LID');
		$basket = $order->getBasket();
		if (!$basket) {
			$basket = Basket::create($site_id);
			$fuser = Sale\Fuser::getIdByUserId($order->getUserId());
			$basket->setFUserId($fuser);
			$new_order = true;
		}
		// Get store order products
		$order_products = [];
		foreach ($order_data['PRODUCTS'] as $item_sp) {
			$order_products[$item_sp['PRODUCT_ID']] = $item_sp;
		}
		// Get external order products
		$ext_products = [];
		foreach ($ext_order['PRODUCTS'] as $item_ep) {
			$ext_products[] = $item_ep;
		}
		foreach ($ext_products as $ext_product) {
			// Find products
            if ( $ext_product['SKUS'] && is_array($ext_product['SKUS']) && count($ext_product['SKUS']) > 1 ) {
                foreach ( $ext_product['SKUS'] as $item ) {
                    $ib_product = Products::findIblockProduct(trim($item), $profile);
                    if ($ib_product) {
                        break;
                    }
                }
            } else {
                $ib_product = Products::findIblockProduct(trim($ext_product['PRODUCT_CODE']), $profile);
                if ( !$ib_product) {
//                    file_put_contents(__DIR__ . '/ext_order.txt', date("m.d.y H:i:s") . ' - ' . json_encode($ext_order) . PHP_EOL, FILE_APPEND);
                }
            }
            $ratio = 1;
            if ($ratio_active == 'Y') {
                $measureData = \Bitrix\Catalog\MeasureRatioTable::getCurrentRatio($ib_product['ID']);
                $ratio = $measureData[$ib_product['ID']];
            }
			Log::getInstance(Settings::getModuleId(), 'orders')->add('(updateOrderProducts) existed product ' . $ib_product['ID'], $profile['ID'], true);
			if ($ib_product) {
			    $ext_product_arr[$ib_product['ID']] = $ib_product['ID'];
//                file_put_contents(__DIR__.'/product.txt', var_export($ext_product_arr, true));
				// Search existed product
				$item = false;
				$b_items = $basket->getBasketItems();
				if ($b_items) {
					foreach ($b_items as $b_item) {
						if ($b_item->getField('PRODUCT_ID') == $ib_product['ID']) {
							$item = $b_item;
							break;
						}
					}
				}
				// Update product
				if ($item) {
					if ($item->getField('QUANTITY') && $ext_product['QUANTITY'] && $item->getField('QUANTITY') != $ext_product['QUANTITY'] * $ratio) {
						$item->setField('QUANTITY', $ext_product['QUANTITY'] * $ratio);
						$has_changes = true;
					}
					if ($item->getField('PRICE') != $ext_product['PRICE'] / $ratio) {
						$item->setField('PRICE', $ext_product['PRICE'] / $ratio);
						$has_changes = true;
					}
				}
				// Add product
				else {
					$item = $basket->createItem('catalog', $ib_product['ID']);
					$prod_fields = [
                        'PRODUCT_ID'   => $ib_product['ID'],
                        'NAME'         => $ib_product['NAME'],
						'QUANTITY'     => $ext_product['QUANTITY'] * $ratio,
						'CURRENCY'     => $ext_product['CURRENCY'],
						'LID'          => $site_id,
						'PRODUCT_PROVIDER_CLASS' => \Bitrix\Catalog\Product\Basket::getDefaultProviderName(),
						'PRICE'        => $ext_product['PRICE'] / $ratio,
						'CUSTOM_PRICE' => 'Y',
					];
					if ($ib_product['TYPE'] == \Bitrix\Catalog\ProductTable::TYPE_SET) {
						$prod_fields['TYPE'] = \Bitrix\Sale\BasketItem::TYPE_SET;
					}
					Log::getInstance(Settings::getModuleId(), 'orders')->add('(updateOrderProducts) new product ' . $ib_product['ID'] . ': ' . print_r($prod_fields, true), $profile['ID'], true);
					$item->setFields($prod_fields);
					$has_changes = true;
				}
			}
		}
		// Basket product
        foreach ($basket->getBasketItems() as $prod ) {
            if ( !array_key_exists($prod->getField('PRODUCT_ID'), $ext_product_arr)  ) {
                $prod->delete();
                $has_changes = true;
            }
        }
//        file_put_contents(__DIR__.'/newproduct.txt', var_export($basket_product_arr, true));
		if ($new_order) {
			$order->setBasket($basket);
//			$basket->save();
		}
//		if ($has_changes) {
//            $basket->save();
//        }
		return $has_changes;
	}

	/**
	 * Order data
	 */

	public static function getOrderInfo($order) {
		$order_data = false;
		if ($order) {
			$order_data['ID'] = $order->getId();
			$order_data['SITE_ID'] = $order->getSiteId();
			if ($order->getDateInsert()) {
				$order_data['DATE_INSERT'] = $order->getDateInsert()->getTimestamp();
			}
			$order_data['STATUS_ID'] = $order->getField('STATUS_ID');
			$res = \Bitrix\Sale\Internals\StatusLangTable::getList(array(
				'filter' => [
					'STATUS.ID'=>$order_data['STATUS_ID'],
					'LID'=>LANGUAGE_ID,
				],
				'select' => ['NAME'],
			));
			if ($status_lang = $res->fetch()) {
				$order_data['STATUS_NAME'] = $status_lang['NAME'];
			}
			$order_data['PERSON_TYPE_ID'] = $order->getPersonTypeId();
			$persons_types = \Bitrix\Sale\PersonType::load(false, $order->getPersonTypeId());
			$order_data['PERSON_TYPE_NAME'] = $persons_types[$order->getPersonTypeId()]['NAME'];
			$order_data['USER_ID'] = $order->getUserId();
			$order_data['USER_GROUPS_ID'] = [];
			$db = \Bitrix\Main\UserGroupTable::getList(array(
				'filter' => array('USER_ID'=>$order->getUserId(), 'GROUP.ACTIVE'=>'Y'),
				'select' => array('GROUP_ID', 'GROUP_CODE'=>'GROUP.STRING_ID'),
				'order' => array('GROUP.C_SORT'=>'ASC'),
			));
			while ($item = $db->fetch()) {
				$order_data['USER_GROUPS_ID'][] = $item['GROUP_ID'];
			}
			$order_data['RESPONSIBLE_ID'] = $order->getField('RESPONSIBLE_ID');
			$order_data['PRICE'] = $order->getPrice();
			$order_data['DISCOUNT_PRICE'] = $order->getDiscountPrice();
			$order_data['DELIVERY_PRICE'] = $order->getDeliveryPrice();
			$order_data['SUM_PAID'] = $order->getSumPaid();
			$order_data['CURRENCY'] = $order->getCurrency();
			$order_data['IS_PAID'] = $order->isPaid();
			$order_data['ID_ALLOW_DELIVERY'] = $order->isAllowDelivery();
			$order_data['IS_SHIPPED'] = $order->isShipped();
			$order_data['IS_CANCELED'] = $order->isCanceled();
			$order_data['ACCOUNT_NUMBER'] = $order->getField('ACCOUNT_NUMBER');
			if ($order->getField('DATE_UPDATE')) {
				$order_data['DATE_UPDATE'] = $order->getField('DATE_UPDATE')->getTimestamp();
			}
			$order_data['COMMENTS'] = $order->getField('COMMENTS');
			$order_data['USER_DESCRIPTION'] = $order->getField('USER_DESCRIPTION');
			if (\Bitrix\Sale\Helpers\Order::isAllowGuestView($order)) {
				$order_data['PUBLIC_LINK'] = \Bitrix\Sale\Helpers\Order::getPublicLink($order);
			}
			// Properties
			$property_collection = $order->getPropertyCollection();
			$property_data = $property_collection->getArray();
			$order_data['PROPERTIES'] = [];
			foreach ($property_data['properties'] as $prop) {
				$order_data['PROPERTIES'][$prop['ID']] = $prop;
			}
			$order_data['PROP_GROUPS'] = $property_data['groups'];
			// Delivery data
			$shipment_collection = $order->getShipmentCollection();
			$shipment = $shipment_collection->current();
			if (is_object($shipment)) {
				$order_data['DELIVERY_TYPE_ID'] = $shipment->getField('DELIVERY_ID');
				$order_data['DELIVERY_TYPE'] = $shipment->getField('DELIVERY_NAME');
				$order_data['DELIVERY_STATUS'] = $shipment->getField('STATUS_ID');
				$stat_res = \Bitrix\Sale\StatusLangTable::getList([
					'filter' => [
						'STATUS_ID' => $shipment->getField('STATUS_ID'),
						'LID' => LANGUAGE_ID,
					]
				]);
				$order_data['DELIVERY_STATUS_NAME'] = '';
				if ($item = $stat_res->fetch()) {
					$order_data['DELIVERY_STATUS_NAME'] = $item['NAME'];
				}
				$order_data['DELIVERY_ALLOW'] = $shipment->getField('ALLOW_DELIVERY');
				$order_data['DELIVERY_DEDUCTED'] = $shipment->getField('DEDUCTED');
				$order_data['TRACKING_NUMBER'] = $shipment->getField('TRACKING_NUMBER');
				$order_data['STORE_ID'] = $shipment->getStoreId();
				if ($order_data['STORE_ID']) {
					$res = \Bitrix\Catalog\StoreTable::getById($order_data['STORE_ID']);
					$store = $res->fetch();
					$order_data['STORE_NAME'] = $store['TITLE'];
				}
			}
			$order_data['DELIVERY_COMPANY_NAME'] = '';
			if ($order->getField('COMPANY_ID')) {
				$res = \Bitrix\Sale\CompanyTable::getById($order->getField('COMPANY_ID'));
				$company = $res->fetch();
				$order_data['DELIVERY_COMPANY_NAME'] = $company['NAME'];
			}
			// Payment data
//			$order_data['IS_PAID'] = false;
			$payment_collection = $order->getPaymentCollection();
			if (is_object($payment_collection->current())) {
				$order_data['PAY_TYPE'] = $payment_collection->current()->getPaymentSystemName();
				$order_data['PAY_ID'] = $payment_collection->current()->getId();
//				if ($payment_collection->isPaid()) {
//					$order_data['IS_PAID'] = true;
//				}
			}
			$order_data['PAYMENT_NUM'] = $order->getField("PAY_VOUCHER_NUM");
			$order_data['PAYMENT_DATE'] = $order->getField("PAY_VOUCHER_DATE");
			// Paid sum
			$order_data['PAYMENT_SUM'] = $payment_collection->getSum();
			$order_data['PAYMENT_FACT'] = $payment_collection->getPaidSum();
			$order_data['PAYMENT_LEFT'] = $order_data['PAYMENT_SUM'] - $order_data['PAYMENT_FACT'];
			// Coupons
			$discount = $order->getDiscount()->getApplyResult();
			$coupons = [];
			if (!empty($discount['COUPON_LIST'])) {
				foreach ($discount['COUPON_LIST'] as $coupon) {
					$coupons[] = $coupon['COUPON'];
				}
			}
			$order_data['COUPONS'] = $coupons;
			// Products (with properties)
			$prod_res = \Bitrix\Sale\Basket::getList([
				'filter' => [
					'=ORDER_ID' => $order->getId(),
				]
			]);
			$product_items = [];
			while ($item = $prod_res->fetch()) {
				$bskt_res = \Bitrix\Sale\Internals\BasketPropertyTable::getList([
					'order' => [
						"SORT" => "ASC",
						"ID" => "ASC"
					],
					'filter' => [
						"BASKET_ID" => $item['ID'],
					],
				]);
				$item['PROPS'] = [];
				while ($property = $bskt_res->fetch()) {
					$k = $property['CODE'] ? $property['CODE'] : $property['ID'];
					$item['PROPS'][$k] = $property['VALUE'];
				}
				$product_items[] = $item;
			}
			$order_data['PRODUCTS'] = [];
			foreach ($product_items as $item) {
				if (!$item['SET_PARENT_ID']) {
					// Name of product
					$order_data['PRODUCTS'][] = [
						'PRODUCT_ID'   => $item['PRODUCT_ID'],
						'PRODUCT_NAME' => $item['NAME'],
						'PRICE'        => $item['PRICE'],
						'DISCOUNT_SUM' => $item['DISCOUNT_PRICE'],
						'QUANTITY'     => $item['QUANTITY'],
						'MEASURE_NAME' => $item['MEASURE_NAME'],
						'MEASURE_CODE' => $item['MEASURE_CODE'],
						'VAT_RATE'     => $item['VAT_RATE'] * 100,
						'VAT_INCLUDED' => $item['VAT_INCLUDED'],
						'PROPS'        => $item['PROPS'],
					];
				}
			}
		}
		return $order_data;
	}

	// Values equal check
	public static function isEqual($order_value, $ext_value) {
		$res = false;
		if ($order_value == [false]) {
			$order_value = [];
		}
		if ($ext_value == [false]) {
			$ext_value = [];
		}
		if ( !is_array($order_value) && !is_array($ext_value)) {
			if ($order_value == $ext_value) {
				$res = true;
			}
		}
		elseif (is_array($order_value) && is_array($ext_value)) {
			if (count($order_value) == count($ext_value)) {
				$res = true;
				foreach ($order_value as $k => $value) {
					if ($value != $ext_value[$k]) {
						$res = false;
					}
				}
				foreach ($ext_value as $k => $value) {
					if ($value != $order_value[$k]) {
						$res = false;
					}
				}
			}
		}
		return $res;
	}

    public static function runSyncOrderStatus($order_id, $ext_order, $profile){
		$result = false;
		$order_data = [];
		// Update order
		$order = false;
		$new_order = false;
		if ($order_id){
			$order = Order::load($order_id);
			$order_data = self::getOrderInfo($order);
			$status_changed = self::updateOrderStatus($ext_order, $order_data, $profile, $order);
            self::updateOrderProps($ext_order['FIELDS'], $order_data, $profile, $order);
			$result = $order->save();
            if($order_data['ID']){
                return ['id'=>$order_id];
            }
		}
	}
}
