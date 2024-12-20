    <? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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

    use Bitrix\Main\Localization\Loc;


    $buy_id = isset($arParams["BUY_ID"]) && !empty($arParams["BUY_ID"]) ? (int)$arParams["BUY_ID"] : $arResult['PRODUCT_ID'];
    $strMainId = $this->getEditAreaId($arResult['PRODUCT_ID']);
    $jsObject = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainId);
    $paramsForJs = array(
    	'buttonId' => $arResult['BUTTON_ID'],
    	'jsObject' => $jsObject,
    	'alreadySubscribed' => $arResult['ALREADY_SUBSCRIBED'],
    	'productId' => $arResult['PRODUCT_ID'],
    	'buttonClass' => htmlspecialcharsbx($arResult['BUTTON_CLASS']),
    	'urlListSubscriptions' => '/',
    );

    $templateData = $paramsForJs;
    ?>
    <span id="<?=htmlspecialcharsbx($arResult['BUTTON_ID'])?>" class="<?=htmlspecialcharsbx($arResult['BUTTON_CLASS'])?><?=($arResult['DEFAULT_DISPLAY']?'':' display-none')?>" data-item="<?=htmlspecialcharsbx($arResult['PRODUCT_ID'])?>" <? if($buy_id != $arResult['PRODUCT_ID']): ?> data-buy-id="<?=$buy_id;?>"<? endif; ?>>
    	<?=Loc::getMessage('CPST_SUBSCRIBE_BUTTON_NAME')?>
    </span>
    <input type="hidden" id="<?=htmlspecialcharsbx($arResult['BUTTON_ID'])?>_hidden">

    <script type="text/javascript">
    	var <?=$jsObject?> = new JCCatalogProductSubscribe(<?=CUtil::phpToJSObject($paramsForJs, false, true)?>);

    	BX.message({
    		CPST_SUBSCRIBE_POPUP_TITLE: '<?=GetMessageJS('CPST_SUBSCRIBE_POPUP_TITLE');?>',
    		CPST_SUBSCRIBE_BUTTON_NAME: '<?=GetMessageJS('CPST_SUBSCRIBE_BUTTON_NAME');?>',
    		CPST_SUBSCRIBE_BUTTON_CLOSE: '<?=GetMessageJS('CPST_SUBSCRIBE_BUTTON_CLOSE');?>',
    		CPST_SUBSCRIBE_MANY_CONTACT_NOTIFY: '<?=GetMessageJS('CPST_SUBSCRIBE_MANY_CONTACT_NOTIFY');?>',
    		CPST_SUBSCRIBE_LABLE_CONTACT_INPUT: '<?=GetMessageJS('CPST_SUBSCRIBE_LABLE_CONTACT_INPUT');?>',
    		CPST_SUBSCRIBE_VALIDATE_UNKNOW_ERROR: '<?=GetMessageJS('CPST_SUBSCRIBE_VALIDATE_UNKNOW_ERROR');?>',
    		CPST_SUBSCRIBE_VALIDATE_ERROR_EMPTY_FIELD: '<?=GetMessageJS('CPST_SUBSCRIBE_VALIDATE_ERROR_EMPTY_FIELD');?>',
    		CPST_SUBSCRIBE_VALIDATE_ERROR: '<?=GetMessageJS('CPST_SUBSCRIBE_VALIDATE_ERROR');?>',
    		CPST_SUBSCRIBE_CAPTCHA_TITLE: '<?=GetMessageJS('CPST_SUBSCRIBE_CAPTCHA_TITLE');?>',
    		CPST_STATUS_SUCCESS: '<?=GetMessageJS('CPST_STATUS_SUCCESS');?>',
    		CPST_STATUS_ERROR: '<?=GetMessageJS('CPST_STATUS_ERROR');?>',
    		CPST_ENTER_WORD_PICTURE: '<?=GetMessageJS('CPST_ENTER_WORD_PICTURE');?>',
    		CPST_TITLE_ALREADY_SUBSCRIBED: '<?=GetMessageJS('CPST_TITLE_ALREADY_SUBSCRIBED');?>',
    		CPST_POPUP_SUBSCRIBED_TITLE: '<?=GetMessageJS('CPST_POPUP_SUBSCRIBED_TITLE');?>',
    		CPST_POPUP_SUBSCRIBED_TEXT: '<?=GetMessageJS('CPST_POPUP_SUBSCRIBED_TEXT');?>'
    	});
    </script>