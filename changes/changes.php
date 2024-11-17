<?php 

die();

///bitrix/modules/iblock/lib/propertyindex/storage.php
//if ($this->getTableName() != 'b_iblock_11_index') {


//bitrix/components/bitrix/sale.viewed.product/component.php
//array("ID" => $arViewedId, "ACTIVE" => "Y"),

///bitrix/modules/sale/lib/internals/entity.php
/*
public function setFieldNoDemand($name, $value)
	{
		$allFields = static::getAllFields();
		if (!isset($allFields[$name]))
		{
			//throw new Main\ArgumentOutOfRangeException($name);
		} else {

			$oldValue = $this->fields->get($name);

			if ($oldValue != $value || ($oldValue === null && $value !== null))
			{
				$this->fields->set($name, $value);
				static::addChangesToHistory($name, $oldValue, $value);
			}
		}
	}
*/


//+/bitrix/modules/search/classes/general/search.php

//public static function CheckPermissions($FIELD = "sc.ID")
//$arResult[] = "1=1";

/* /bitrix/modules/iblock/classes/general/iblocksection.php
/bitrix/modules/iblock/classes/general/iblockelement.php

?AND (
					B.ID IN ($stdPermissions)
					OR (B.RIGHTS_MODE = 'E' AND B.ID IN ($extPermissions))
				)
+ return $strResult; return '';

/home/bitrix/ext_www/dn.d6r.ru/bitrix/modules/iblock/classes/mysql/iblock.php

?                     $sqlPermissions = "AND (
                        B.ID IN ($stdPermissions)
                        OR (B.RIGHTS_MODE = 'E' AND B.ID IN ($extPermissions))
                    )";


+ if ( $operation != "'section_read', 'element_read', 'section_element_bind', 'section_section_bind'") {
                    $sqlPermissions = "AND (
                        B.ID IN ($stdPermissions)
                        OR (B.RIGHTS_MODE = 'E' AND B.ID IN ($extPermissions))
                    )";
                } else {
                    $sqlPermissions = " ";
                }
*/

//+/bitrix/modules/iblock/classes/general/iblockelement.php

/*

		$this->getActive($arSqlSearch,$arFilter['IBLOCK_ID']);
  		return $arSqlSearch;
    }

    public function getActive(&$arSqlSearch, $iBlockId) {
        global $DB, $USER;

        $sFind = '(BE.ACTIVE_TO >= now() OR BE.ACTIVE_TO IS NULL) AND (BE.ACTIVE_FROM <= now() OR BE.ACTIVE_FROM IS NULL)';
        $sReplace = '1=1';

        foreach ($arSqlSearch as $iNum => $sWehere) {
            if (stripos($sWehere,$sFind) !== false){
                $arSqlSearch[$iNum] = str_ireplace($sFind,$sReplace,$sWehere);
            }
        }


    } */

////bitrix/modules/sale/handlers/paysystem/bill/template/template.php
////if(stripos($property,'Базовый товар:') !== false) continue;
///bitrix/components/bitrix/sale.location.selector.search/class.php
/*
 $res->addReplacedAliases(array('LNAME' => 'NAME'));

                while($item = $res->Fetch()) {
                    if (!defined('ADMIN_SECTION') && in_array($item['TYPE_ID'], array(3, 6))
                        || defined('ADMIN_SECTION')) {

                        $this->dbResult['PATH'][intval($item['ID'])] = $this->forceToType($item);
                        $this->dbResult['PRECACHED_POOL'][$item['PARENT_ID']][$item['ID']] = $item;

                    }
                }
*/
///bitrix/components/bitrix/sale.location.selector.search/get.php
/* 	$data = CBitrixLocationSelectorSearchComponent::processSearchRequest();

	if(!defined('ADMIN_SECTION')){

        foreach($data['ITEMS'] as $sKey => $aItem){
            if(!in_array($aItem['TYPE_ID'],array(3,6))){
                unset($data['ITEMS'][$sKey]);
            }
        };

        $data['ITEMS'] = array_values($data['ITEMS']);

    }
*/

//bitrix/modules/sale/handlers/delivery/additional/ruspost/reliability/reliability.php
//bitrix/modules/sale/handlers/delivery/additional/ruspost/reliability/service.php
//(string)$address
///bitrix/modules/catalog/mysql/currency.php - correctCurrencyRate return DoubleVal( ceilRubPrice
///bitrix/modules/currency/general/currency_rate.php - correctCurrencyRate return $curFromRate ceilRubPrice
///bitrix/modules/sale/mysql/currency.php - correctCurrencyRate return DoubleVal(DoubleVal( ceilRubPrice
///bitrix/modules/sale/admin/report_edit.php USER_TITLE_AT_TOP USER_WHERE_THE_STORE_IS USER_COMMENT_1 USER_COMMENT_2 USER_COMMENT_3
/*
 * "USER_TITLE_AT_TOP" => Array("NAME" => GetMessage("SPS_USER_TITLE_AT_TOP"), "TYPE" => "", "VALUE" => ""),
    "USER_WHERE_THE_STORE_IS" => Array("NAME" => GetMessage("SPS_USER_WHERE_THE_STORE_IS"), "TYPE" => "", "VALUE" => ""),
    "USER_COMMENT_1" => Array("NAME" => GetMessage("SPS_USER_COMMENT_1"), "TYPE" => "", "VALUE" => ""),
    "USER_COMMENT_2" => Array("NAME" => GetMessage("SPS_USER_COMMENT_2"), "TYPE" => "", "VALUE" => ""),
    "USER_COMMENT_3" => Array("NAME" => GetMessage("SPS_USER_COMMENT_3"), "TYPE" => "", "VALUE" => ""),

 */
///bitrix/modules/sale/handlers/paysystem/bill/template/template.php
///bitrix/modules/sale/admin/report_edit.php (lang) /bitrix/admin/sale_report_edit.php?lang=ru
///bitrix/modules/sale/lang/en/admin/report_edit.php
///bitrix/modules/sale/lang/ru/admin/report_edit.php
///bitrix/admin/reports/invoice.php USER_TITLE_AT_TOP USER_WHERE_THE_STORE_IS USER_COMMENT_1 USER_COMMENT_2 USER_COMMENT_3
///bitrix/modules/sale/lib/helpers/admin/orderedit.php OrderEdit::setProductDetails( array_merge((array)$product, (array)$productData)
///bitrix/js/ipol.sdek/ajax.php $action = $_POST['action'];

///bitrix/header.php

///bitrix/modules/zixo.blanks/classes/tools.php $c = $arOrder['PRICE'] - $arOrder['SUM_PAID']; $c = $arOrder['PRICE'];

//bitrix/modules/sale/lib/tradingplatform/ebay/feed/data/converters/inventory.php
/*

protected function getItemData($data, $skuPrefix = "")
	{
        if(!isset($data["PRICES"]["MIN"]) || $data["PRICES"]["MIN"] <= 0)
			throw new SystemException("Can't find the price for product id: ".$data["ID"]." ! ".__METHOD__);

        $quantity = (float)get_quantity_product($data["ID"]);

		if(((float)$quantity <= 0) || !canYouBuy($data["ID"]))
			return '';

		if($this->maxProductQuantity !== null && $quantity > $this->maxProductQuantity)
			$quantity = $this->maxProductQuantity;

		$result = "\t<Inventory>\n";
		$result .= "\t\t<SKU>".$skuPrefix.$data["ID"]."</SKU>\n";
		$result .= "\t\t<Price>".$data["PRICES"]["MIN"]."</Price>\n";
		$result .= "\t\t<Quantity>".$quantity."</Quantity>\n";
		$result .= "\t</Inventory>\n";

		return $result;
	}

*/
//bitrix/modules/sale/lib/tradingplatform/ebay/feed/data/converters/product.php
//protected function getItemData($data)
//if(!empty($this->ebayCategories)){

/*

protected function getItemData($data)
    {

        static $products;

        if(is_null($products))
            $products = array();


			if(!isset($products[$data["IBLOCK_ID"]."_".$data["ID"]])){

            $products[$data["IBLOCK_ID"]."_".$data["ID"]] = array();

*/

//getNotSkuItemData($data)
///bitrix/modules/sale/lib/tradingplatform/vk/feed/data/converters/product.php
/* $photoMainUrlFile = preg_replace('~http(s*?)://[^/]+?/~i','/',$photoMainUrl);
        $photoMainUrlFile = rectangleImage($_SERVER['DOCUMENT_ROOT'].$photoMainUrlFile,400,400,$photoMainUrlFile);
        $photoMainUrl = $photoMainUrlFile;

        $checkFileArr = \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$photoMainUrlFile);

        if($checkFileArr
            && isset($checkFileArr['name'])
            && !empty($checkFileArr['name'])){

            $checkFileRes = \CFile::GetList(array("ID"=>"asc"),array("ORIGINAL_NAME" => $checkFileArr['name']));

            if($checkFileRes
                && $checkFileDbArr = $checkFileRes->GetNext()) {

                $photoMainBxId = $checkFileDbArr['ID'];

            } else {

                $photoMainBxId = \CFile::SaveFile($checkFileArr);

            }

        }
*/
/*
search: $ph
$photoMainUrlFile = \CFile::getPath($ph);
                $photoMainUrlFile = rectangleImage($_SERVER['DOCUMENT_ROOT'].$photoMainUrlFile,800,800,$photoMainUrlFile);
                $checkFileArr = \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/'.$photoMainUrlFile);

                if($checkFileArr
                    && isset($checkFileArr['name'])
                    && !empty($checkFileArr['name'])){

                    $checkFileRes = \CFile::GetList(array("ID"=>"asc"),array("ORIGINAL_NAME" => $checkFileArr['name']));

                    if($checkFileRes
                        && $checkFileDbArr = $checkFileRes->GetNext()) {

                        $ph = $checkFileDbArr['ID'];

                    } else {

                        $ph = \CFile::SaveFile($checkFileArr);

                    }

                }

///bitrix/modules/ipol.sdek/classes/general/sdekoption.php
if($order['STATUS_ID'] != $newStat

if($order['STATUS_ID'] != $newStat
                                        && !in_array(
                                                $order['STATUS_ID'],
                                                array(
                                                    COption::GetOptionString('ipol.sdek','statusPVZ','S'),
                                                    COption::GetOptionString('ipol.sdek','statusDELIVD','P'),
                                                    COption::GetOptionString('ipol.sdek','statusOTKAZ','O'),
                                                    'F'
                                                )
                                            )
                                        && $order['CANCELED'] != 'Y'
                                        && $order['MARKED'] != 'Y'
                                    ){

///bitrix/modules/iblock/lib/component/elementlist.php

if(!defined('ADMIN_SECTION')){
                    if(!empty($propertyCodes))
                        \CIBlockElement::GetPropertyValuesArray($iblockElements, $iblock, $propFilter, array('CODE' => $propertyCodes));
                } else {

                    \CIBlockElement::GetPropertyValuesArray($iblockElements, $iblock, $propFilter);
                }


*/

//bitrix/components/bitrix/sale.location.selector.search/class.php

/* if(!defined('ADMIN_SECTION')){
				unset(
					$parameters['filter']['NAME.LANGUAGE_ID'],
					$parameters['select']['SHORT_NAME'],
					$parameters['select']['LNAME']
				);
			}

			if($this->arParams['ID']){
				$toBeFound = true;
				$res = Location\LocationTable::getPathToNode($this->arParams['ID'], $parameters);
			} elseif(mb_strlen($this->arParams['CODE'])) {
				$toBeFound = true;
				$res = Location\LocationTable::getPathToNodeByCode($this->arParams['CODE'], $parameters);
			}

			if(defined('ADMIN_SECTION')){

				if($res)
				{
					$res->addReplacedAliases(array('LNAME' => 'NAME'));

					while($item = $res->Fetch())
					{
						if ((!defined('ADMIN_SECTION') && in_array($item['TYPE_ID'], array(3, 6)))
							|| defined('ADMIN_SECTION')) {

							$this->dbResult['PATH'][intval($item['ID'])] = $this->forceToType($item);
							$this->dbResult['PRECACHED_POOL'][$item['PARENT_ID']][$item['ID']] = $item;

						}
					}

					end($this->dbResult['PATH']);
					$this->dbResult['LOCATION'] = current($this->dbResult['PATH']);
				}

			} else {

				if($res)
				{

					global $DB;

					$aItems = array();
					$sJoin = '';

					while($item = $res->Fetch())
					{

						$aItems[$item['ID']] = $item;
						$sJoin .= (!empty($sJoin) ? ',' : '') . $item['ID'];

					}

					$sSql = 'SELECT
								`sale_location_location_name`.`LOCATION_ID` AS `ID`,
								`sale_location_location_name`.`NAME`,
								`sale_location_location_name`.`SHORT_NAME` AS `SHORT_NAME`,
								`sale_location_location_name`.`ID` AS `UALIAS_0` FROM
								`b_sale_loc_name` `sale_location_location_name`
								WHERE
									UPPER(`sale_location_location_name`.`LANGUAGE_ID`)=UPPER(\''.LANGUAGE_ID.'\')
								AND
									`sale_location_location_name`.`LOCATION_ID` IN ('.$sJoin.')';

					$rDb = $DB->Query($sSql);

					if($rDb)
					while($aItem = $rDb->Fetch()){
						$aItems[$aItem['ID']] = array_merge($aItems[$aItem['ID']],$aItem);
					}

					foreach($aItems as $item){

						if ((!defined('ADMIN_SECTION') && in_array($item['TYPE_ID'], array(3, 6)))
							|| defined('ADMIN_SECTION')) {

							$this->dbResult['PATH'][intval($item['ID'])] = $this->forceToType($item);
							$this->dbResult['PRECACHED_POOL'][$item['PARENT_ID']][$item['ID']] = $item;

						}
					}

					end($this->dbResult['PATH']);
					$this->dbResult['LOCATION'] = current($this->dbResult['PATH']);
				}

			}
*/

///bitrix/components/bitrix/sale.order.ajax/class.php

/*if(!is_array($this->usersList)){
	$this->usersList = array();
}*/

//$usersList = [70765];

/*if(empty($this->usersList)){

	foreach ($responsibleGroups as $groupId)
	{
		$usersList[] = CGroup::GetGroupUser($groupId);
	}

	$this->usersList = $usersList = array_merge(...$usersList);

	} else {

		$usersList = $this->usersList;
	}
*/

//bitrix/components/ipol/ipol.sdekPickup/
/*
$arList['PVZ'] = CDeliverySDEK::weightPVZ((CDeliverySDEK::$orderWeight)?false:COption::GetOptionString(CDeliverySDEK::$MODULE_ID,'weightD',1000),$arList['PVZ']);
$_SESSION['PVZ'] = $arList['PVZ'];
*/

///bitrix/modules/ipol.kladr/classes/general/CKladr.php
/*

if(array_key_exists("VILLAGE_NAME",$arCity) && !array_key_exists("CITY_NAME",$arCity)){

				$sAbbr = '
				г. — город;
				пгт — посёлок городского типа;
				рп — рабочий посёлок;
				кп — курортный посёлок;
				к. — кишлак;
				дп — дачный посёлок (дачный поселковый совет);
				п. — посёлок сельского типа;
				нп — населённый пункт;
				п.ст. — посёлок при станции (посёлок станции);
				ж/д ст. — железнодорожная станция;
				ж/д будка — железнодорожная будка;
				ж/д казарма — железнодорожная казарма;
				ж/д платформа — железнодорожная платформа;
				ж/д рзд — железнодорожный разъезд;
				ж/д остановочный пункт — железнодорожный остановочный пункт;
				ж/д путевой пост — железнодорожный путевой пост;
				ж/д блокпост — железнодорожный блокпост;
				с. — село;
				м. — местечко;
				д. — деревня;
				сл. — слобода;
				ст. — станция;
				ст-ца — станица;
				х. — хутор;
				у. — улус;
				рзд — разъезд;
				клх — колхоз (коллективное хозяйство);
				свх — совхоз (советское хозяйство);
				г — город;
				пгт — поселок городского типа;
				рп — рабочий поселок;
				кп — курортный поселок;
				дп — дачный поселок;
				гп — городской поселок;
				п — посёлок;
				к — кишлак;
				нп — населенный пункт;
				п.ст — поселок при станции (поселок станции);
				п ж/д ст — поселок при железнодорожной станции;
				ж/д блокпост — железнодорожный блокпост;
				ж/д будка — железнодорожная будка;
				ж/д ветка — железнодорожная ветка;
				ж/д казарма — железнодорожная казарма;
				ж/д комбинат — железнодорожный комбинат;
				ж/д платформа — железнодорожная платформа;
				ж/д площадка — железнодорожная площадка;
				ж/д путевой пост — железнодорожный путевой пост;
				ж/д остановочный пункт — железнодорожный остановочный пункт;
				ж/д рзд — железнодорожный разъезд;
				ж/д ст — железнодорожная станция;
				м — местечко;
				д — деревня;
				с — село;
				сл — слобода;
				ст — станция;
				ст-ца — станица;
				у — улус
				х — хутор;
				рзд — разъезд;
				зим — зимовье';

				$aAbbr = explode("\n",$sAbbr);
				$aAbbr = array_map(function($sVal){
					$sVal = trim($sVal);
					$sVal = trim($sVal,';');
					return $sVal;
				},$aAbbr);

				$arAbbr = array();

				foreach($aAbbr as $sVal){

					list($sVal1,$sVal2) = explode(' — ',$sVal,2);
					$sVal1 = trim($sVal1);
					$sVal2 = trim($sVal2);

					if(!empty($sVal1))
					$arAbbr['^'.$sVal1] = '~^'.preg_quote($sVal1,'~').' ~isu';

					if(!empty($sVal2))
					$arAbbr['^'.$sVal2] = '~^'.preg_quote($sVal2,'~').' ~isu';

					if(mb_stripos($sVal1,'ё') !== false){
						$sVal1 = str_ireplace('ё','е',$sVal1);
						$arAbbr['^'.$sVal1] = '~^'.preg_quote($sVal1,'~').' ~isu';
					}

					if(mb_stripos($sVal2,'ё') !== false){
						$sVal2 = str_ireplace('ё','е',$sVal2);
						$arAbbr['^'.$sVal2] = '~^'.preg_quote($sVal2,'~').' ~isu';
					}

					list($sVal1,$sVal2) = explode(' — ',$sVal,2);
					$sVal1 = trim($sVal1);
					$sVal2 = trim($sVal2);

					if(!empty($sVal1))
					$arAbbr['$'.$sVal1] = '~ '.preg_quote($sVal1,'~').'$~isu';

					if(!empty($sVal2))
					$arAbbr['$'.$sVal2] = '~ '.preg_quote($sVal2,'~').'$~isu';

					if(mb_stripos($sVal1,'ё') !== false){
						$sVal1 = str_ireplace('ё','е',$sVal1);
						$arAbbr['$'.$sVal1] = '~ '.preg_quote($sVal1,'~').'$~isu';
					}

					if(mb_stripos($sVal2,'ё') !== false){
						$sVal2 = str_ireplace('ё','е',$sVal2);
						$arAbbr['$'.$sVal2] = '~ '.preg_quote($sVal2,'~').'$~isu';
					}
				}

				$arCity["CITY_NAME"]=$arCity["VILLAGE_NAME"];
				$arCity["CITY_NAME"]=preg_replace($arAbbr,'',$arCity["CITY_NAME"]);
				unset($arCity["VILLAGE_NAME"]);
			}
*/


