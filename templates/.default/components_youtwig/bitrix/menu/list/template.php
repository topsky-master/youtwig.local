<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

$this->setFrameMode(true);

if (!empty($arResult)):?>
<ul class="menu list">
<?

$params 						= Array(
    "max_len" 				    => "200",
    "change_case" 			    => "L",
    "replace_space" 		    => " ",
    "replace_other" 		    => " ",
    "delete_repeat_replace"     => "true",
);



foreach($arResult as $arItem):
    if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
        continue;

		$additional = "";

        if(isset($arItem['PARAMS']) && !empty($arItem['PARAMS'])){
            foreach($arItem['PARAMS'] as $key => $value){
                $additional .=  (!empty($additional) ? ' ' : '').$key.'="'.htmlspecialcharsbx($value).'"';
            }
        }

        $menu_class = CUtil::translit($arItem["TEXT"], LANGUAGE_ID, $params);
?>
    <?if($arItem["SELECTED"]):?>
    <li class="<?=$menu_class;?>">
        <a href="<?=$arItem["LINK"]?>" <?=$additional;?> class="selected">
            <?=$arItem["TEXT"]?>
        </a>
    </li>
    <?else:?>
    <li class="<?=$menu_class;?>">
        <a href="<?=$arItem["LINK"]?>" <?=$additional;?>>
            <?=$arItem["TEXT"]?>
        </a>
    </li>
    <?endif?>
<?endforeach?>
</ul>
<?endif?>