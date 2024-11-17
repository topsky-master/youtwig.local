<?

use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\Extension;
use TwoFingers\Location\Options;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 21.03.2019
 * Time: 11:56
 *
 *
 */
if ($arResult['SETTINGS'][Options::INCLUDE_JQUERY] != '') {
    CJSCore::init([$arResult['SETTINGS'][Options::INCLUDE_JQUERY]]);
}

$asset = Asset::getInstance();
$asset->addJs($templateFolder . '/js/jquery.mousewheel.min.js');
$asset->addJs($templateFolder . '/js/jquery.nicescroll.js');

