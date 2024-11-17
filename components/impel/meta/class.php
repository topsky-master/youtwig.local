<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED  !== true)
    die();

class ImpelMetaComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    public function executeComponent()
    {

        global $APPLICATION;

        $page_keywords = $APPLICATION->GetPageProperty('page_keywords','');
        $page_title = $APPLICATION->GetPageProperty('page_title','');

        if(!empty($page_title)){

            if(mb_stripos($page_title, '[title]') !== false){

                if(!empty($APPLICATION->GetPageProperty('title',''))){
                    $page_title = str_ireplace('[title]',$APPLICATION->GetPageProperty('title',''),$page_title);
                } else {
                    $page_title = str_ireplace('[title]',$APPLICATION->GetTitle(),$page_title);
                }

            }

            if(mb_stripos($page_title, '[pag_title]') !== false){

                if(!empty($APPLICATION->GetPageProperty('title',''))){
                    $page_title = str_ireplace('[pag_title]',$APPLICATION->GetPageProperty('title',''),$page_title);
                } else {
                    $page_title = str_ireplace('[pag_title]',$APPLICATION->GetTitle(),$page_title);
                }

            }

            $APPLICATION->SetPageProperty("title", $page_title);
            $APPLICATION->SetTitle($page_title);
        }

        $page_description = $APPLICATION->GetPageProperty('page_description','');

        if(!empty($page_description)){

            if(mb_stripos($page_description, '[description]') !== false) {
                $page_description = str_ireplace('[description]', $APPLICATION->GetPageProperty('description', ''), $page_description);
            }

            if(mb_stripos($page_description, '[pag_description]') !== false) {
                $page_description = str_ireplace('[pag_description]', $APPLICATION->GetPageProperty('description', ''), $page_description);
            }

            $APPLICATION->SetPageProperty("description", $page_description);
        }

        if(!empty($page_keywords)) {
            $APPLICATION->SetPageProperty("keywords", $page_keywords);
        }

        $APPLICATION->SetPageProperty('page_keywords', '');
        $APPLICATION->SetPageProperty('page_title', '');
        $APPLICATION->SetPageProperty('page_description', '');

    }

}