<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?><?php

$rsStore = CCatalogStoreProduct::GetList(
            array('PRODUCT_ID' => 'DESC'),
            array('>AMOUNT' => 0, "STORE_ID" => 9, 10),
            false,
            ['nTopCount' => 1]
        );

        if ($rsStore) {

            while($arStore = $rsStore->Fetch()) {


                $arProductFilter = Array(
                    "IBLOCK_ID" => 11,
                    "PROPERTY_MAIN_PRODUCTS" => $arStore['PRODUCT_ID']
                );


                $resProduct = CIBlockElement::GetList(
                    [],
                    $arProductFilter,
                    false,
                    false,
                    ['ID','NAME']
                );

                if($resProduct){
                    $arProduct = $resProduct->GetNext();
                    print_r($arProduct);
                }
            }

        }<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>