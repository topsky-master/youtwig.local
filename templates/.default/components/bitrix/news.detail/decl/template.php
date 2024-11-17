<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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


function checkDelensions($sCheck) {

    static $aForm;

    $iCount = 0;

    $sAppend = iconv('utf-8','windows-1251//IGNORE','Сноска %s');

    if (!is_array($aForm)) {
        $sContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/local/crontab/declensions/results/ready_declensions.txt');
        $aForm = unserialize($sContent);
    }

    $sCheck = iconv('utf-8','windows-1251//IGNORE',$sCheck);

    $aFound = [];

    foreach ($aForm as $sSearch) {

        if (stripos($sCheck,$sSearch) !== false) {
            if (stripos($sCheck,$sSearch.'*') === false) {

                $bFound = false;
                foreach ($aFound as $sFind) {
                    if (stripos($sFind,$sSearch) !== false) {
                        $bFound = true;
                    }
                }

                if (!$bFound) {

                    $aFound[] = $sSearch;
                    
                    if (preg_match('~(?<![a-zа-я0-9]]{1})'.preg_quote($sSearch,'~').'(?![a-zа-я0-9]{1})~is',$sCheck,$aMatches)) {
                        ++$iCount;
                        $sReplace = $sSearch.str_pad('',$iCount,'*');
                        $sCheck = str_ireplace($sSearch,$sReplace,$sCheck);
                        $sCurr = sprintf($sAppend,$iCount);
                        $sCheck .= '<hr /><p>'. str_pad('',$iCount,'*').sprintf($sCurr,$iCount).' '.$sSearch.'</p>';
                    }

                }
            }
        }
    }

    $sCheck = iconv('windows-1251//IGNORE','utf-8', $sCheck);

    return $sCheck;
}

$arResult["DETAIL_TEXT"] = checkDelensions($arResult["DETAIL_TEXT"]);
$arResult["PREVIEW_TEXT"] = checkDelensions($arResult["PREVIEW_TEXT"]);


?>
<div class="news-detail">
    <?php if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
        <h3><?=$arResult["NAME"]?></h3>
    <?php endif;?>
    <br />
    <br />
    <?php  if($arResult["DETAIL_TEXT"] <> ''):?>
        <?=$arResult["DETAIL_TEXT"];?>
    <?php endif; ?>
    <br />
    <br />
    <?php  if($arResult["PREVIEW_TEXT"] <> ''):?>
        <?=$arResult["PREVIEW_TEXT"];?>
    <?php endif?>
    <br />
    <br />

</div>