<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */


if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;


$arParams["ID"] = intval($arParams["ID"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

$arParams["DEPTH_LEVEL"] = intval($arParams["DEPTH_LEVEL"]);
if($arParams["DEPTH_LEVEL"]<=0)
    $arParams["DEPTH_LEVEL"]=1;

$arResult["SECTIONS"] = array();
$arResult["ELEMENT_LINKS"] = array();

$linkProperty = isset($arParams['LINK_PROPERTY'])
&& !empty($arParams['LINK_PROPERTY'])
    ? trim($arParams['LINK_PROPERTY'])
    : '';

if($this->StartResultCache())
{
    if(!CModule::IncludeModule("iblock"))
    {
        $this->AbortResultCache();
    }
    else
    {
        $arFilter = array(
            "IBLOCK_ID"=>$arParams["IBLOCK_ID"],
            "GLOBAL_ACTIVE"=>"Y",
            "IBLOCK_ACTIVE"=>"Y",
            "<="."DEPTH_LEVEL" => $arParams["DEPTH_LEVEL"],
        );
        $arOrder = array(
            "left_margin"=>"asc",
        );

        $whatToSelect = array(
            "ID",
            "DEPTH_LEVEL",
            "NAME",
            "SECTION_PAGE_URL",
            "UF_NOFOLLOW"
        );

        if(!empty($linkProperty)){
            $whatToSelect[] = $linkProperty;
        }

        $rsSections = CIBlockSection::GetList($arOrder, $arFilter, false, $whatToSelect);
        if($arParams["IS_SEF"] !== "Y")
            $rsSections->SetUrlTemplates("", $arParams["SECTION_URL"]);
        else
            $rsSections->SetUrlTemplates("", $arParams["SEF_BASE_URL"].$arParams["SECTION_PAGE_URL"]);
        while($arSection = $rsSections->GetNext())
        {
            $current = sizeof($arResult["SECTIONS"]);

            $arResult["SECTIONS"][$current] = array(
                "ID" => $arSection["ID"],
                "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
                "~NAME" => $arSection["~NAME"],
                "~SECTION_PAGE_URL" => $arSection["~SECTION_PAGE_URL"],
            );

            if(isset($linkProperty)
                && !empty($linkProperty)){
                $arResult["SECTIONS"][$current][$linkProperty] = $arSection[$linkProperty];
            }

            $arResult["SECTIONS"][$current]["UF_NOFOLLOW"] = isset($arSection["UF_NOFOLLOW"])
            &&!empty($arSection["UF_NOFOLLOW"])
                ? true
                : false;

            $arResult["ELEMENT_LINKS"][$arSection["ID"]] = array();
        }

        $this->EndResultCache();
    }
}

//In "SEF" mode we'll try to parse URL and get ELEMENT_ID from it
if($arParams["IS_SEF"] === "Y")
{
    $engine = new CComponentEngine($this);
    if (CModule::IncludeModule('iblock'))
    {
        $engine->addGreedyPart("#SECTION_CODE_PATH#");
        $engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
    }
    $componentPage = $engine->guessComponentPath(
        $arParams["SEF_BASE_URL"],
        array(
            "section" => $arParams["SECTION_PAGE_URL"],
            "detail" => $arParams["DETAIL_PAGE_URL"],
        ),
        $arVariables
    );
    if($componentPage === "detail")
    {
        CComponentEngine::InitComponentVariables(
            $componentPage,
            array("SECTION_ID", "ELEMENT_ID"),
            array(
                "section" => array("SECTION_ID" => "SECTION_ID"),
                "detail" => array("SECTION_ID" => "SECTION_ID", "ELEMENT_ID" => "ELEMENT_ID"),
            ),
            $arVariables
        );
        $arParams["ID"] = intval($arVariables["ELEMENT_ID"]);
    }
}

if(($arParams["ID"] > 0) && (intval($arVariables["SECTION_ID"]) <= 0) && CModule::IncludeModule("iblock"))
{
    $arSelect = array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID");
    $arFilter = array(
        "ID" => $arParams["ID"],
        "ACTIVE" => "Y",
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    );
    $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    if(($arParams["IS_SEF"] === "Y") && (mb_strlen($arParams["DETAIL_PAGE_URL"]) > 0))
        $rsElements->SetUrlTemplates($arParams["SEF_BASE_URL"].$arParams["DETAIL_PAGE_URL"]);
    while($arElement = $rsElements->GetNext())
    {
        $arResult["ELEMENT_LINKS"][$arElement["IBLOCK_SECTION_ID"]][] = $arElement["~DETAIL_PAGE_URL"];
    }
}

$aMenuLinksNew = array();
$menuIndex = 0;
$previousDepthLevel = 1;
foreach($arResult["SECTIONS"] as $arSection)
{
    if($menuIndex > 0){
        $aMenuLinksNew[$menuIndex][3]["IS_PARENT"] = $arSection["DEPTH_LEVEL"] > $previousDepthLevel;
    }

    $previousDepthLevel = $arSection["DEPTH_LEVEL"];

    $arResult["ELEMENT_LINKS"][$arSection["ID"]][] = urldecode($arSection["~SECTION_PAGE_URL"]);

    ++$menuIndex;

    $aMenuLinksNew[$menuIndex] = array(
        htmlspecialcharsbx($arSection["~NAME"]),
        (!empty($linkProperty) ? $arSection[$linkProperty] : $arSection["~SECTION_PAGE_URL"]),
        $arResult["ELEMENT_LINKS"][$arSection["ID"]],
        array(
            "FROM_IBLOCK" => true,
            "IS_PARENT" => false,
            "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
            "ID" => $arSection["ID"]

        ),
    );

    if($arSection["UF_NOFOLLOW"]){
        $aMenuLinksNew[$menuIndex][3]["REL"] = "nofollow";
    }



}

return $aMenuLinksNew;
?>
