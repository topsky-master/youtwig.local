<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?php global $arrFilter; ?>
<?php $result = array(); ?>
<result>
    <?

    $check_rights = checkQuantityRigths();
    twigBuildSectionFilter::buildSectionFilter($arParams);

    if(!empty($arrFilter) && is_array($arrFilter)){

        $arrFilter = array_merge(
            $arrFilter,
            array(
                "INCLUDE_SUBSECTIONS" => "Y",
                "ACTIVE_DATE" => "Y",
                "ACTIVE" => "Y")
        );

        $arElements = array();


        if(is_array($arResult["SEARCH"]) && !empty($arResult["SEARCH"])){
            foreach($arResult["SEARCH"] as $value){
                $arElements[] = $value["ITEM_ID"];
            };
        };

        $arSelect = Array("ID");
        $res = CIBlockElement::GetList(
            Array(
                $arParams["ELEMENT_SORT_FIELD"] => $arParams["ELEMENT_SORT_ORDER"],
                $arParams["ELEMENT_SORT_FIELD2"] => $arParams["ELEMENT_SORT_ORDER2"]
            ),
            $arrFilter,
            false,
            false,
            $arSelect);

        if($res
            && is_object($res)
            &&  method_exists($res,"GetNextElement")){
            while($ob = $res->GetNextElement()){
                $arFields = $ob->GetFields();
                if(isset($arFields["ID"])
                    && !in_array($arFields["ID"],$arElements)){
                    $arElements[] = $arFields["ID"];
                };
            };
        };

    };

    if(sizeof($arElements)>0):?>
        <?

        if(isset($arParams['PAGE_RESULT_COUNT'])
            && !empty($arParams['PAGE_RESULT_COUNT'])){
            $arElements = array_slice($arElements, 0, $arParams['PAGE_RESULT_COUNT']);
        };

        $product_image_width = 50;
        $product_image_height = 50;
        $dir = $_SERVER['DOCUMENT_ROOT'].'/';

        foreach($arElements as $arItem):?>
            <?

            $name = "";
            $sizeof = sizeof($result);

            $arSelect = Array(
                "PREVIEW_PICTURE",
                "DETAIL_PAGE_URL",
                "NAME",
                "PROPERTY_ARTNUMBER"
            );

            $arFilter  = Array(
                "ID" => $arItem);
            $res = CIBlockElement::GetList(
                Array(),
                $arFilter,
                false,
                Array(),
                $arSelect);

            $file_id = "";
            $arFields = array();


            if($res
                && is_object($res)
                && method_exists($res,"GetNext")){
                $arFields			= $res->GetNext();


                if(isset($arFields["PREVIEW_PICTURE"])){
                    $file_id = $arFields["PREVIEW_PICTURE"];
                }

            }

            if(isset($arFields["NAME"])
                && !empty($arFields["NAME"])
                && isset($arFields["DETAIL_PAGE_URL"])
                && !empty($arFields["DETAIL_PAGE_URL"])){

                $arFields["NAME"] = html_entity_decode($arFields["NAME"],ENT_QUOTES,LANG_CHARSET);

                $image = "";

                if(!empty($file_id)){

                    $file = CFile::ResizeImageGet(
                        $file_id,
                        array(
                            'width' => $product_image_width,
                            'height' => $product_image_height),
                            BX_RESIZE_IMAGE_PROPORTIONAL,
                            true);

                    if($file
                        && isset($file["src"])
                        && !empty($file["src"])
                        && file_exists($dir.$file["src"])):
                        $image = $file["src"];
                    endif;

                    if(!empty($image)
                        && function_exists('rectangleImage')):
                        $image = rectangleImage($dir.$image,$product_image_width,$product_image_height,$image);
                    endif;

                    if(isset($arFields["PROPERTY_ARTNUMBER_VALUE"]) && !empty($arFields["PROPERTY_ARTNUMBER_VALUE"])){
                        $arFields["NAME"] .= ' ('.GetMessage("ARTNUMBER").': '.$arFields["PROPERTY_ARTNUMBER_VALUE"].')';
                    };

                    $query = $_REQUEST['q'];
                    $query = urldecode($query);
                    $query = trim($query);
                    $query = html_entity_decode($query,ENT_QUOTES,'UTF-8');
                    $query = strip_tags($query);

                    $arFields["NAME"] = str_replace($query, '<strong>'.$query.'</strong>', $arFields["NAME"]);

                    if(!empty($image)){
                        $arFields["NAME"] = '<img src="'.$image.'" class="thumbnail" />'.$arFields["NAME"];
                    }

                };


                $quantity = get_quantity_product($arItem);
                if($quantity > 0 && $check_rights){
                    $arFields["NAME"] .= ' ('.GetMessage("CRL_QUANTITY").$quantity.')';
                };

                $result[$sizeof] = array(
                    "name" => $arFields["NAME"],
                    "value" => IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$arFields["DETAIL_PAGE_URL"]);

            }

            ?>
        <?endforeach;

        $sizeof	= sizeof($result);

        $result[$sizeof] = array(
            "name" => GetMessage("ALL_RESULTS"),
            "value" => IMPEL_PROTOCOL.IMPEL_SERVER_NAME."/search/?q=".trim(urlencode($_REQUEST["q"]))
        );

        ?>

    <?endif;?>
    <?php echo json_encode(array('result'=>$result)); ?>
</result>