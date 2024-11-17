<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(count($arResult["PERSON_TYPE"]) > 1)
{

    ?>
    <?foreach($arResult["PERSON_TYPE"] as $v):?>
    <div class="hidden">
        <input type="radio" id="PERSON_TYPE_<?=$v["ID"]?>" name="PERSON_TYPE" value="<?=$v["ID"]?>"<?if ($v["CHECKED"]=="Y") echo " checked=\"checked\"";?> onClick="submitForm()">
    </div>
    <?endforeach;?>
    <input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_ID"]?>" />
    <?

}
else
{
    if(IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"]) > 0)
    {
        //for IE 8, problems with input hidden after ajax
        ?>
        <div class="hidden">
		<input type="text" name="PERSON_TYPE" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>" />
		<input type="text" name="PERSON_TYPE_OLD" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>" />
		</div>
        <?
    }
    else
    {
        foreach($arResult["PERSON_TYPE"] as $v)
        {
            ?>
            <input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?=$v["ID"]?>" />
            <input type="hidden" name="PERSON_TYPE_OLD" value="<?=$v["ID"]?>" />
            <?
        }
    }
}
?>