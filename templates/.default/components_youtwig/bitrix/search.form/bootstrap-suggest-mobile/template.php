<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

?>
<div class="search-area">
    <form action="<?=$arResult["FORM_ACTION"]?>" id="searchbox">
        <?if($arParams["USE_SUGGEST"] === "Y"):?>
        <?$APPLICATION->IncludeComponent(
            "bitrix:search.suggest.input",
            "",
            array(
                "NAME" => "q",
                "VALUE" => "",
                "INPUT_SIZE" => 15,
                "DROPDOWN_SIZE" => 10,
            ),
            $component, array("HIDE_ICONS" => "Y")
        );?>
        <?else:?>
        <div class="input-group query_row">
            <input class="form-control autocomplete search_query ac_input" name="q" value="<?php if(isset($_REQUEST['q']) && !empty($_REQUEST['q'])): echo htmlspecialchars(trim(urldecode($_REQUEST['q'])),ENT_QUOTES,'UTF-8'); endif; ?>" autocomplete="off" placeholder="<?php echo GetMessage("SEARCH_IN_SITE"); ?>" />
            <span class="input-group-addon" id="basic-addon2"><button value="<?php echo GetMessage("DO_SEARCH"); ?>"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button></span>
        </div>
        <?endif;?>
    </form>
</div>