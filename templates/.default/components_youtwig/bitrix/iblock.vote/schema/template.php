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
$this->setFrameMode(false);

if($arParams["DISPLAY_AS_RATING"] == "vote_avg")
{
    if($arResult["PROPERTIES"]["vote_count"]["VALUE"])
        $votesValue = round($arResult["PROPERTIES"]["vote_sum"]["VALUE"]/$arResult["PROPERTIES"]["vote_count"]["VALUE"], 2);
    else
        $votesValue = 0;
}
else
{
    $votesValue = intval($arResult["PROPERTIES"]["rating"]["VALUE"]);
}

$votesCount = intval($arResult["PROPERTIES"]["vote_count"]["VALUE"]);

global $USER;

if($votesValue > 0){ ?>
    <div class="rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <meta itemprop="ratingValue" content="<?=$votesValue;?>" />
        <meta itemprop="ratingCount" content="<?=$votesCount;?>" />
        <?php if (isset($arParams['VOTE_NAMES']) && !empty($arParams['VOTE_NAMES'])) { ?>
            <?php foreach ($arParams['VOTE_NAMES'] as $value) { ?>
            <i class="fa fa-star<?php if ($value > ceil($votesValue)) { ?> fa-star-o<?php } ?>" aria-hidden="true"></i>
            <?php } ?>
        <?php } ?>
    </div>
<?php }; ?>