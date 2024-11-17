<?

use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED  !== true)
    die();

class ImpelSortComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    public function executeComponent()
    {

        $this->includeComponentTemplate();
    }
}