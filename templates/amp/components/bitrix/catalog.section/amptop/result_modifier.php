<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

$canonical_url = isset($arParams['CANONICAL_URL'])
&&!empty($arParams['CANONICAL_URL'])
    ? trim($arParams['CANONICAL_URL'])
    : '';


$arResult['CANONICAL_URL'] = $canonical_url;

$howSort = array(
    "created_date:desc" => GetMessage("SORT_CREATED_DESC"),
    "show_counter:desc" => GetMessage("SORT_SHOW_COUNTER_DESC"),
    //"sort:asc" => GetMessage("SORT_SORT_ASC"),
    //"sort:desc" => GetMessage("SORT_SORT_DESC"),
    "name:asc" => GetMessage("SORT_NAME_ASC"),
    //"name:desc" => GetMessage("SORT_NAME_DESC"),
    //"catalog_QUANTITY:asc" => GetMessage("SORT_QUANTITY_ASC"),
    //"catalog_QUANTITY:desc" => GetMessage("SORT_QUANTITY_DESC"),
    //"show_counter:asc" => GetMessage("SORT_SHOW_COUNTER_ASC"),
    //"created_date:asc" => GetMessage("SORT_CREATED_ASC"),
    //"HAS_PREVIEW_PICTURE:asc" => GetMessage("SORT_PREVIEW_PICTURE_ASC"),
    //"HAS_PREVIEW_PICTURE:desc" => GetMessage("SORT_PREVIEW_PICTURE_DESC")
);

if(isset($arParams["PRICE_CODE"])
    && !empty($arParams["PRICE_CODE"])){

    $obCache = new CPHPCache;
    $cacheID = 'catalog_price_code';
    $cacheTime = isset($arParams['CACHE_TIME']) ? (int)$arParams['CACHE_TIME'] : 3600;
    
    if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

        $tmp = array();
        $tmp = $obCache->GetVars();

        if(isset($tmp[$cacheID])){
            $catalog_price_code = $tmp[$cacheID];
        }

        foreach ($catalog_price_code as $ar_res){
            $howSort["catalog_PRICE_".$ar_res["ID"].":asc"] = (GetMessage("PRICE_ASC"));
            $howSort["catalog_PRICE_".$ar_res["ID"].":desc"] = (GetMessage("PRICE_DESC"));
        }

    } else {

        foreach ($arParams["PRICE_CODE"] as $price_name){

            $db_res = CCatalogGroup::GetList(
                array(
                    "SORT" =>"ASC"
                ),
                array(
                    "NAME" => $price_name
                ),
                false,
                false,
                array("ID")
            );

            $catalog_price_code = array();

            if(is_object($db_res)){
                while ($ar_res = $db_res->Fetch()){
                    $howSort["catalog_PRICE_".$ar_res["ID"].":asc"] = (GetMessage("PRICE_ASC"));
                    $howSort["catalog_PRICE_".$ar_res["ID"].":desc"] = (GetMessage("PRICE_DESC"));
                    $catalog_price_code[] = $ar_res["ID"];
                }
            }

        }


        if($obCache->StartDataCache()){

            $obCache->EndDataCache(
                array(
                    $cacheID => $catalog_price_code
                )
            );

        };

    };

};

$arResult['howSort'] = $howSort;

if (is_object($this->__component))
{

    $resultCacheKeys = array_keys($arResult);

    $this->__component->SetResultCacheKeys(
        $resultCacheKeys
    );

    foreach($resultCacheKeys as $resultCacheKey){

        if (!isset($arResult[$resultCacheKey]))
            $arResult[$resultCacheKey] = $this->__component->arResult[$resultCacheKey];

    };

};