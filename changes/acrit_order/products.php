<?
/**
 * Products data synchronization
 */

namespace Acrit\Core\Orders;

use Bitrix\Main,
	Bitrix\Main\Type,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\SiteTable,
	Acrit\Core\Log,
	Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class Products {

	/**
	 * Iblocks catalogs list
	 */
	public static function getIblockList($offers=false) {
		$list = [];
		$catalog_iblocks_ids = [];
		$filter = [];
		if (!$offers) {
			$filter['PRODUCT_IBLOCK_ID'] = 0;
		}
		$catalog_iblocks = \Bitrix\Catalog\CatalogIblockTable::getList([
			'filter' => $filter,
		])->fetchAll();
		foreach ($catalog_iblocks as $catalog_iblock) {
			$catalog_iblocks_ids[] = $catalog_iblock['IBLOCK_ID'];
		}
		$res = \Bitrix\Iblock\IblockTable::getList([
			'select' => ['ID', 'NAME'],
		]);
		while ($item = $res->fetch()) {
			if (in_array($item['ID'], $catalog_iblocks_ids)) {
				$list[] = [
					'id' => $item['ID'],
					'name' => $item['NAME'],
				];
			}
		}
		return $list;
	}

	/**
	 * Store products fields
	 */
	public static function getFieldsForID($iblock_id) {
		$list = [];
		if (!$iblock_id) {
			return;
		}
		// IBlock fields
		$list['main'] = [
			'title' => GetMessage("ORDERS_PORTAL_PRODUCTS_OSNOVNYE_PARAMETRY"),
		];
		$list['main']['items'] = [
			[
				'id' => 'ID',
				'name' => GetMessage("ORDERS_PORTAL_PRODUCTS_ID")
			],
			[
				'id' => 'NAME',
				'name' => GetMessage("ORDERS_PORTAL_PRODUCTS_IMA_ELEMENTA")
			],
			[
				'id' => 'CODE',
				'name' => GetMessage("ORDERS_PORTAL_PRODUCTS_KOD_ELEMENTA")
			],
			[
				'id' => 'XML_ID',
				'name' => GetMessage("ORDERS_PORTAL_PRODUCTS_XML_ID")
			],
		];
		// IBlock properties
		$list['props'] = [
			'title' => GetMessage("ORDERS_PORTAL_PRODUCTS_SVOYSTVA"),
		];
		if ($iblock_id) {
			$ob = \CIBlockProperty::GetList(["sort" => "asc", "name" => "asc"], ["ACTIVE" => "Y", "IBLOCK_ID" => $iblock_id]);
			while ($prop = $ob->GetNext()) {
				if ($prop['MULTIPLE'] != 'Y' && !in_array($prop['PROPERTY_TYPE'], ['F'])) {
					$list['props']['items'][] = [
						'id'   => 'PROPERTY_' . $prop['CODE'],
						'name' => GetMessage("ORDERS_PORTAL_PRODUCTS_SVOYSTVO", ['#NAME#' => $prop['NAME']]),
					];
				}
			}
		}
		return $list;
	}

	/**
	 * Search for a store product by its identification code
	 */
	public static function findIblockProduct($find_code, array $profile) {
		$product = false;
		if ($find_code) {
			$iblock_list = self::getIblockList(true);
			$comp_table = $profile['PRODUCTS']['search_fields'];
			foreach ($iblock_list as $iblock) {
				if ($comp_table[$iblock['id']]) {
					$filter = [
						'IBLOCK_ID' => $iblock['id'],
//						'ACTIVE' => 'Y',
						$comp_table[$iblock['id']] => $find_code,
					];
                    if ($profile['ID'] == 9) {
                        $contin = true;
                    }elseif ($profile['ID'] == 10){
                        $contin = true;
                    }else{
                        Log::getInstance(Settings::getModuleId(), 'orders')->add('(findIblockProduct) search filter ' . print_r($filter, true), $profile['ID'], true);
                    }
                    $res = \CIBlockElement::GetList(['SORT' => 'ASC'], $filter, false, ['nTopCount' => 1], ['ID', 'NAME']);
					while ($ob = $res->GetNextElement()) {
						$fields = $ob->GetFields();
						$fields['catalog'] = \Bitrix\Catalog\ProductTable::getById($fields['ID'])->fetch();
                        if ($profile['ID'] == 9) {
                            $contin = true;
                        }elseif ($profile['ID'] == 10){
                            $contin = true;
                        }else{
                            Log::getInstance(Settings::getModuleId(), 'orders')->add('(findIblockProduct) found variant ' . print_r($fields, true), $profile['ID'], true);
                        }
//					    $fields['PROPERTIES'] = $ob->GetProperties();
						$product = $fields;
						break 2;
					}
				}
			}
		}
		return $product;
	}

}
