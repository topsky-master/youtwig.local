<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED  !== true)
    die();

class ImpelModelComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    protected static function getForSelectData($enumPropertyCode)
    {
        global $arParams;

        $enumProperty = array();

        $cacheTime = $arParams['CACHE_TIME'] ? (int)$arParams['CACHE_TIME'] : 360000000;
        $cacheTime = $cacheTime > 0 ? $cacheTime : 360000000;

        $obCache = new CPHPCache;
        $cacheID = 'instruction_'.$enumPropertyCode;

        if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

            $tmp = array();
            $tmp = $obCache->GetVars();

            if(isset($tmp[$cacheID])){
                $enumProperty = $tmp[$cacheID];
            }

        } else {

            if($obCache->StartDataCache()){

                if(CModule::IncludeModule("iblock")){

                    $typeProperties = CIBlockPropertyEnum::GetList(
                        Array(
                            $enumPropertyCode."_VALUE" => "ASC"
                        ),
                        Array(
                            "IBLOCK_ID" => 17,
                            "CODE" => $enumPropertyCode
                        )
                    );


                    if($typeProperties){

                        while ($typeFields = $typeProperties->GetNext()){

                            $enumProperty[$typeFields['ID']] = $typeFields['VALUE'];

                        }

                    }

                }


                $obCache->EndDataCache(
                    array(
                        $cacheID => $enumProperty
                    )
                );

            }


        }

        return $enumProperty;

    }

    public function executeComponent()
    {
        $this->arResult = array();

        $this->arResult["TYPE_OF_PRODUCT"] = self::getForSelectData("TYPE_OF_PRODUCT");
        $this->arResult["MANUFACTURER"] = self::getForSelectData("MANUFACTURER");

        $this->includeComponentTemplate();
    }
}