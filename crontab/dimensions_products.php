<?php

//https://youtwig.ru/local/crontab/dimensions_products.php?intestwetrust=1&time=1562030100&PageSpeed=off
//https://twig.d6r.ru/local/crontab/dimensions_products.php?intestwetrust=1&time=1562030100&PageSpeed=off

$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);
define('DIMENSIONS_IBLOCK_ID', 16);
define('DIMENSIONS_LINK_IBLOCK_ID', 11);


set_time_limit(0);
define("LANG", "s1");
define('SITE_ID', 's1');

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if (!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

try {

    class impelGetDimensions
    {
        private static $apCheck = [
            'Длина' => 'LENGTH',
            'Ширина' => 'WIDTH',
            'Высота' => 'HEIGHT',
            'Вес' => 'WEIGHT',
        ];

        private static $iCode = 0;

        private static $apMulti = [
            'LENGTH' => 10,
            'WIDTH' => 10,
            'HEIGHT' => 10,
            'WEIGHT' => 1000
        ];

        public static function getList()
        {
            static::getProducts();
        }

        private static function getProducts():array
        {

            $apOrder = [
                'ID' => 'DESC'
            ];

            $apSelect = [
                "ID"
            ];

            $apFilter = [
                "IBLOCK_ID" => DIMENSIONS_IBLOCK_ID,
            ];

            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/without_shtrihcode.log','');
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/shtrihcode.log','');

            $rProducts = CIBlockElement::GetList(
                $apOrder,
                $apFilter,
                false,
                false,
                $apSelect);

            $aReturn = [];
            $apCheck = array_keys(static::$apCheck);

            if ($rProducts) {

                while ($aProducts = $rProducts->GetNext()) {

                    $aProps = [];
                    $aProps['QUANTITY'] = get_quantity_product_w($aProducts['ID']);

                    $rmpDb = impelCIBlockElement::GetProperty(
                        DIMENSIONS_IBLOCK_ID,
                        $aProducts['ID'],
                        [],
                        ['CODE' => 'CML2_BAR_CODE']
                    );


                    if ($rmpDb) {

                        while ($apFields = $rmpDb->GetNext()) {
                            if (!empty($apFields['VALUE'])) {
                                $aProps['SHTRIHKOD'] = $apFields['VALUE'];
                            }
                        }
                    }


                    $rmpDb = impelCIBlockElement::GetProperty(
                        DIMENSIONS_IBLOCK_ID,
                        $aProducts['ID'],
                        [],
                        ['CODE' => 'CML2_TRAITS']
                    );

                    if ($rmpDb) {

                        while ($apFields = $rmpDb->GetNext()) {

                            if ($apFields['DESCRIPTION'] == 'Код') {
                                $aProps['KOD_BAZOVOGO'] = $apFields['VALUE'];
                            }

                        }

                    }

                    $rmpDb = impelCIBlockElement::GetProperty(
                        DIMENSIONS_IBLOCK_ID,
                        $aProducts['ID'],
                        [],
                        ['CODE' => 'CML2_TRAITS']
                    );

                    $aDimensions = [];

                    if ($rmpDb) {


                        while ($apFields = $rmpDb->GetNext()) {

                            $apFields = array_map('trim',$apFields);

                            if (isset($apFields['DESCRIPTION'])
                                && !empty($apFields['DESCRIPTION'])
                                && in_array($apFields['DESCRIPTION'],$apCheck)
                            ) {
                                $aDimensions[$apFields['DESCRIPTION']] = $apFields['VALUE'];
                            }

                        }

                    }


                    $aUpdate = [];

                    foreach(static::$apCheck as $sFind => $sValue) {

                        if (isset($aDimensions[$sFind])
                            && !empty($aDimensions[$sFind])
                        ) {

                            $aUpdate[$sValue] = $aDimensions[$sFind] * static::$apMulti[$sValue];

                        }

                    }

                    if (!empty($aUpdate)) {
                        CCatalogProduct::Update($aProducts["ID"], $aUpdate);
                    }

                    $icCount = count(array_filter($aDimensions));
                    $icCount += count(array_filter($aProps));

                    if (!empty($icCount)) {
                        $aDimensions = array_map('trim',$aDimensions);
                        $aProps = array_map('trim',$aProps);
                        static::getLinkedProducts($aProducts['ID'],$aDimensions,$aProps);
                    }

                }



            }

            return $aReturn;

        }

        private static function getLinkedProducts(int $iProdId, array $aDimensions, array $aProps)
        {

            $apOrder = [
                'ID' => 'DESC'
            ];

            $apSelect = [
                "ID"
            ];

            $apFilter = [
                "IBLOCK_ID" => DIMENSIONS_LINK_IBLOCK_ID,
                "PROPERTY_MAIN_PRODUCTS" => $iProdId
            ];

            $iProducts = CIBlockElement::GetList(
                $apOrder,
                $apFilter,
                [],
                false,
                $apSelect);

            $rProducts = CIBlockElement::GetList(
                $apOrder,
                $apFilter,
                false,
                false,
                $apSelect);

            if (!isset($aProps['SHTRIHKOD'])) {
                file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/without_shtrihcode.log',$iProdId."\n",FILE_APPEND);
            }

            if ($rProducts) {

                $aParams = array_values(static::$apCheck);
                static::$iCode = 0;


                while ($aProducts = $rProducts->GetNext()) {

                    $rcProducts = CCatalogProduct::GetList(
                        ["ID" => "DESC"],
                        ["ID" => $aProducts["ID"]],
                        false,
                        false,
                        $aParams
                    );

                    if ($rcProducts) {

                        $skip = [];

                        while ($acProducts = $rcProducts->Fetch())
                        {

                            static::$iCode++;

                            foreach ($aProps as $sCode => $sValue) {
                                if (!empty($sValue)) {

                                    if ($sCode == 'SHTRIHKOD') {
                                        $sValue .= ($iProducts < 2 ? '' : ('S'.static::$iCode));
                                    } else if ($sCode != 'QUANTITY') {
                                        $sValue .= 'T'.static::$iCode;
                                    }

                                    $rmpDb = impelCIBlockElement::GetProperty(
                                        DIMENSIONS_LINK_IBLOCK_ID,
                                        $acProducts['ID'],
                                        [],
                                        ['CODE' => $sCode]
                                    );

                                    if ($rmpDb) {

                                        while ($apFields = $rmpDb->GetNext()) {

                                            if($apFields['VALUE'] != $sValue) {
                                                impelCIBlockElement::SetPropertyValuesEx($aProducts["ID"], DIMENSIONS_LINK_IBLOCK_ID, ($aProp = [$sCode => $sValue]));
                                            };

                                        }

                                    }

                                }
                            }

                            $aUpdate = [];

                            $acProducts = array_map('trim',$acProducts);

                            foreach(static::$apCheck as $sFind => $sValue) {

                                if (isset($acProducts[$sValue])
                                    && isset($aDimensions[$sFind])
                                    && !empty($aDimensions[$sFind])
                                    && ($acProducts[$sValue] != $aDimensions[$sFind])
                                ) {

                                    $aUpdate[$sValue] = $aDimensions[$sFind] * static::$apMulti[$sValue];

                                }

                            }

                            if (!empty($aUpdate)) {

                                //echo 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID='.DIMENSIONS_IBLOCK_ID.'&type=catalog&ID='.$iProdId.'&lang=ru&find_section_section=-1&form_element_11_active_tab=edit10'."\n<br />";
                                //echo 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID='.DIMENSIONS_LINK_IBLOCK_ID.'&type=catalog&ID='.$aProducts["ID"].'&lang=ru&find_section_section=-1&form_element_11_active_tab=edit10'."\n<br />";
                                CCatalogProduct::Update($aProducts["ID"], $aUpdate);
                                \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(DIMENSIONS_LINK_IBLOCK_ID, $aProducts["ID"]);

                            }

                            if (isset($aProps['SHTRIHKOD'])) {
                                file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/shtrihcode.log',$aProducts["ID"].' - '.$iProducts."\n",FILE_APPEND);
                            }

                        }

                    }


                }

            }

        }

    }

    if (CModule::IncludeModule("iblock")) {
        impelGetDimensions::getList();
    }

} catch (Exception $oException) {
    echo $oException->getMessage();
}