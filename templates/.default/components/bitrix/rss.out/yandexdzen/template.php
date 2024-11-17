<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);
$dir = $_SERVER['DOCUMENT_ROOT'];
?>
<?='<?xml version="1.0" encoding="'.SITE_CHARSET.'"?>'?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:media="http://search.yahoo.com/mrss/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:georss="http://www.georss.org/georss">
    <channel>
        <title><?=$arResult["NAME"].(mb_strlen($arResult["SECTION"]["NAME"])>0?" / ".$arResult["SECTION"]["NAME"]:"")?></title>
        <link><?=IMPEL_PROTOCOL.$arResult["SERVER_NAME"]?>/news/</link>
        <guid><?php echo md5($arResult["ELEMENT"]["ID"]); ?></guid>
        <? if(mb_strlen($arResult["SECTION"]["DESCRIPTION"])>0): ?>
            <description><?=mb_strlen($arResult["SECTION"]["DESCRIPTION"])>0?$arResult["SECTION"]["DESCRIPTION"]:$arResult["DESCRIPTION"]?></description>
        <? endif; ?>
        <?foreach($arResult["ITEMS"] as $arItem):?>
            <?
            $description = html_entity_decode($arItem["description"],ENT_QUOTES,LANG_CHARSET);

            $description = preg_replace('~<style[^>]*?>.*?</style>~isu','',$description);
            $description = preg_replace('~<noindex[^>]*?>.*?</noindex>~isu','',$description);
            $description = preg_replace('~<script[^>]*?>.*?</script>~isu','',$description);
            $description = preg_replace('~<table[^>]*?>.*?</table>~isu','',$description);
            $description = preg_replace('~\s*?style="[^"]+?"~isu','',$description);


            $smallDescription = $description;
            $smallDescription = strip_tags($smallDescription);
            $description = strip_tags($description,'<p><br /><br><div><span><i><b><strong><em>');
            $smallDescription = trim($smallDescription);

            if(mb_strlen($smallDescription) > 250){

                $smallDescription = mb_substr($smallDescription,0,255);

                if(mb_stripos($smallDescription, ' ') !== false){
                    $smallDescription = mb_substr($smallDescription,0,mb_strripos($smallDescription, ' '));
                }

            }


            ?>
            <item>
                <title><?=$arItem["title"]?></title>
                <link><?=$arItem["link"]?></link>
                <pdalink><?=str_ireplace(IMPEL_PROTOCOL,IMPEL_PROTOCOL.'m.',$arItem["link"]);?></pdalink>
                <amplink><?=preg_replace('~[^/]*?//[^/]+?/~is',IMPEL_PROTOCOL.IMPEL_SERVER_NAME.'/amp/',$arItem["link"]);?></amplink>
                <description><![CDATA[<?=$smallDescription;?>]]></description>
                <?if(is_array($arItem["enclosure"])):?>
                    <?

                    $thumb = preg_replace('~.*?//[^/]+?/~isu','/',$arItem["enclosure"]["url"]);

                    if(!empty($thumb)){
                        $arItem["enclosure"]["url"] = IMPEL_PROTOCOL.IMPEL_SERVER_NAME.rectangleImage($dir.$thumb,400,400,$thumb);

                    }

                    if(!empty($arItem["enclosure"]["url"])){

                        ?>
                        <enclosure url="<?=$arItem["enclosure"]["url"]?>" length="<?=$arItem["enclosure"]["length"]?>" type="<?=$arItem["enclosure"]["type"]?>"/>
                        <?php

                    }

                    ?>
                <?endif?>
                <?if($arItem["category"]):?>
                    <category><?=$arItem["category"]?></category>
                <?endif?>
                <pubDate><? echo $arItem["pubDate"];?></pubDate>
                <author>info@youtwig.ru</author>
                <content:encoded><![CDATA[<?=$description;?>]]></content:encoded>
            </item>
        <?endforeach?>
    </channel>
</rss>
