<?php

//https://youtwig.ru/local/crontab/merge_products.php?intestwetrust=1&time=1562030100&PageSpeed=off
//https://twig.d6r.ru/local/crontab/merge_products.php?intestwetrust=1&time=1562030100&PageSpeed=off

$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);
define('MERGE_IBLOCK_ID', 11);

set_time_limit(0);
define("LANG", "s1");
define('SITE_ID', 's1');

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if (!isset($_REQUEST['intestwetrust'])) die();

$aFind = array(786747); //удалить: например: $aFind = array(665173,655498)
$aMerge = array(689010); //объединить картинки с товаром, сделать редирект на товар: например: $aMerge = array(752);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

try {

    class impelGetMerge
    {

        public static function getList($aFind, $aMerge)
        {

            if (empty($aFind) || empty($aMerge)) {
                throw new Exception('Заполните массивы $aFind, $aMerge');
            }
            static::checkList($aFind, $aMerge);
        }

        private static function getProducts($aFind)
        {

            $apOrder = [
                'ID' => 'DESC'
            ];

            $apSelect = [
                "ID",
                "DETAIL_PAGE_URL"
            ];

            $apFilter = [
                "IBLOCK_ID" => MERGE_IBLOCK_ID,
                "ID" => $aFind
            ];

            $rProducts = CIBlockElement::GetList(
                $apOrder,
                $apFilter,
                false,
                false,
                $apSelect);

            $aReturn = [];

            if ($rProducts) {

                while ($aProducts = $rProducts->GetNext()) {

                    $rmpDb = impelCIBlockElement::GetProperty(
                        MERGE_IBLOCK_ID,
                        $aProducts['ID'],
                        array(),
                        array('CODE' => 'MORE_PHOTO')
                    );

                    if ($rmpDb) {

                        $aReturn[$aProducts['ID']] = [
                            'ID' => $aProducts['ID'],
                            'IMAGES' => [],
                            'DETAIL_PAGE_URL' => $aProducts['DETAIL_PAGE_URL']];

                        while ($apFields = $rmpDb->GetNext()) {

                            $sPath = '';

                            if ($apFields['VALUE'] > 0) {
                                $sPath = CFile::getPath($apFields['VALUE']);
                            }

                            if (!empty($sPath)) {
                                $aReturn[$aProducts['ID']]['IMAGES'][] = $sPath;
                            }

                        }

                    }

                }

            }

            return $aReturn;

        }

        private static function productsMerge($aFind, $aDelete)
        {
            static $pElt;

            if (!is_object($pElt)) {
                $pElt = new CIBlockElement;
            }

            $aImages = $aFind['IMAGES'];
            foreach ($aDelete as $aProduct) {

                echo 'Удалено ' . $aProduct['ID'] . "<br />\n";
                $aImages = array_merge($aImages, $aProduct['IMAGES']);

                $updProduct = Array('TIMESTAMP_X' => true, 'ACTIVE' => 'N');

                //if (CIBlockElement::Delete($aProduct['ID'])) {
                if ($pElt->Update($aProduct['ID'],$updProduct)) {
                    static::addRedirect($aProduct['DETAIL_PAGE_URL'], $aFind['DETAIL_PAGE_URL']);
                    echo 'Добавлен redirect с ' . $aProduct['DETAIL_PAGE_URL'] . ' на ' . $aFind['DETAIL_PAGE_URL'] . "<br />\n";
                }

            }

            $aProductImages = [];

            foreach ($aImages as $sImage) {

                $aImage = CFile::MakeFileArray($sImage);

                if ($aImage
                    && is_array($aImage)
                    && !empty($aImage)) {
                    $aImage['MODULE_ID'] = 'iblock';
                    $aImage['COPY_FILE'] = 'Y';
                    $aProductImages['MORE_PHOTO'][] = ['VALUE' => $aImage];
                }
            }

            if (!empty($aProductImages['MORE_PHOTO'])) {

                CIBlockElement::SetPropertyValuesEx($aFind['ID'], MERGE_IBLOCK_ID, $aProductImages);
                \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(MERGE_IBLOCK_ID, $aFind['ID']);

                echo 'Объединены изображения для ' . $aFind['ID'] . "<br />\n";

                $updProduct = array('TIMESTAMP_X' => true);

                if ($pElt->Update($aFind['ID'], $updProduct)) {
                    echo 'Обновлена дата изменения товара для ' . $aFind['ID'] . "<br />\n";

                }

            }

        }

        private static function checkList($aFind, $aMerge)
        {
            $iCount = 0;

            $aDelete = static::getProducts($aFind);
            $aResult = static::getProducts($aMerge);

            foreach ($aResult as $aProduct) {
                static::productsMerge($aProduct, $aDelete);
                $iCount += count($aDelete);
            }

            echo 'Процесс закончен, обработано ' . $iCount . ' товаров<br />';
            echo 'Перейдите в скрипт /local/crontab/remap_protucts_to_models.php и задайте следующие данные перед выполнением:
        <pre>
            $toFind = array(' . join(',', $aFind) . ');
            $toDelete = array(' . join(',', $aFind) . ');
            $toAdd = array(' . join(',', $aMerge) . ');
        </pre>';
        }

        private static function addRedirect($what, $where)
        {

            $what = trim($what);
            $what = preg_replace('~http(s*?)://[^/]+?/~isu', '', $what);
            $what = rtrim($what, '/');

            $where = trim($where);
            $where = preg_replace('~http(s*?)://[^/]+~isu', '', $where);
            $where = empty($where) ? "/" : $where;

            $show = false;
            $rsData = CBXShortUri::GetList(
                array(),
                array(
                    "URI" => '/' . trim($where, '/') . '/',
                    "SHORT_URI" => trim($what, '/')
                )
            );

            while ($arRes = $rsData->Fetch()) {
                $show = true;
                break;
            }

            $rsData = CBXShortUri::GetList(
                array(),
                array(
                    "URI" => '/' . trim($what, '/') . '/',
                    "SHORT_URI" => trim($where, '/')
                )
            );

            while ($arRes = $rsData->Fetch()) {
                $show = true;
                break;
            }

            if (!$show
                &&
                (
                    (trim(mb_strtolower($where), '/') != trim(mb_strtolower($what), '/'))
                    || (trim(($where), '/') != trim(($what), '/'))
                )
            ) {

                $arShortFields = array(
                    "URI" => '/' . trim($where, '/') . '/',
                    "SHORT_URI" => trim($what, '/'),
                    "STATUS" => "301",
                );

                CBXShortUri::Add($arShortFields);

            }

        }

    }

    if (CModule::IncludeModule("iblock")) {
        impelGetMerge::getList($aFind, $aMerge);
    }

} catch (Exception $oException) {
    echo $oException->getMessage();
}