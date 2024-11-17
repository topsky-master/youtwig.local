<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @var array $arResult
 * @global object $APPLICATION
 */

/**
 * Формируем данные для добавления типа продукта в хлебные крошки
 */
foreach ($arResult["ITEMS"] as $item)
{
   if($item['CODE'] == "TYPEPRODUCT")
   {
       foreach ($item['VALUES'] as $value)
       {
            if($value['CHECKED'])
            {
                $GLOBALS['FILTER']['TYPEPRODUCT']['NAME'] = $value['VALUE'];
                $GLOBALS['FILTER']['TYPEPRODUCT']['URL'] = "/filter/typeproduct-is-".$value['URL_ID']."/";
            }
       }
   }
}
