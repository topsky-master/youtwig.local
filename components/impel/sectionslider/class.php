<?

use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED  !== true)
    die();

class ImpelSectionSliderComponent extends CBitrixComponent
{
    private $asMap;
    private $aSublinks;
    private $clearUri = false;

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    private function getEnumMap(){

        $typePropertyDB = \CIBlockPropertyEnum::GetList(
            Array(
                "DEF"=>"DESC",
                "SORT"=>"ASC"),
            Array(
                "IBLOCK_ID" => 41,
                "CODE" => 'SECTION'
            )
        );

        if($typePropertyDB){
            while($typePropertyFields = $typePropertyDB->GetNext()){

                if(isset($typePropertyFields["XML_ID"])){

                    $this->asMap[$typePropertyFields["XML_ID"]] = $typePropertyFields["VALUE"];

                }

            }

        }


    }

    private function getSublinks(){

        if(empty($this->aSublinks)) {
            if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/taglinks.php')
                && is_readable($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/taglinks.php')
                && filesize($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/taglinks.php') > 0){

                @require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/taglinks.php';
                $this->aSublinks = $atLinks;
            }
        }

        return $this->aSublinks;

    }

    public function executeComponent()
    {

        global $USER, $APPLICATION;

        $aSublinks = $this->getSublinks();

        $sDir = $_SERVER['ORIG_REQUEST_URI'];
        $sDir = preg_replace('~\?.*?$~is','',$sDir);

        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        $uri = new \Bitrix\Main\Web\Uri($request->getRequestedPageDirectory());
        $currentURL = $uri->GetUri(); 

        foreach($aSublinks as $spLink => $aLinks){
            if(mb_stripos($sDir,$spLink) !== false || @preg_match('~'.$spLink.'~is',$sDir)){
                $this->clearUri = $spLink;
                foreach($aLinks as $aLink){
                    $aLink = array_map('trim',$aLink);
                    if (isset($aLink[0]) 
                        && isset($aLink[1]) 
                        && isset($aLink[2]) // Проверяем наличие третьего элемента
                        && $aLink[2] !== $currentURL // Сравниваем третий элемент с текущим URL
                    ) {
                        $this->arResult['items'][$aLink[1]] = $aLink[0];
                    }
                }
            }
        }

        if(empty($this->arResult['items'])) {

            $iSectId = $this->arParams['FILTER_CATEGORY'];

            if($iSectId > 0){

                $this->getEnumMap();

                if(isset($this->asMap[$iSectId])) {

                    $arSelect = array('PROPERTY_LINK_HREF','PROPERTY_LINK_NAME');
                    $arFilter = array('PROPERTY_SECTION_VALUE' => $this->asMap[$iSectId], 'ACTIVE' => 'Y');

                    $dbRes = CIBlockElement::GetList(Array('SORT' => 'ASC'), $arFilter, false, false, $arSelect);

                    if($dbRes){
                        while($arFields = $dbRes->GetNext()) {
                            if (!empty($arFields['PROPERTY_LINK_NAME_VALUE']) && !empty($arFields['PROPERTY_LINK_HREF_VALUE'])) {
                                $this->arResult['items'][$arFields['PROPERTY_LINK_NAME_VALUE']] = $arFields['PROPERTY_LINK_HREF_VALUE'];
                            }
                        }
                    }
                }
            }
        
        }
        
        if(isset($this->arResult['items']))
        {
            $this->arResult['items'] = array_filter($this->arResult['items']);
            
            if(sizeof($this->arResult['items']) > 0) {           
                $this->arResult['current_uri'] = '/'.trim($currentURL,'/').'/';
                if(!$this->clearUri){
                    $sectRes = CIBlockSection::GetByID($this->arParams['FILTER_CATEGORY']);
                    if($arSection = $sectRes->GetNext()){
                        $this->arResult['clear_uri'] = $arSection['SECTION_PAGE_URL'];
                    }
                }else{
                    $this->arResult['clear_uri'] = $this->clearUri;
                }
    
                $this->includeComponentTemplate();
            }
        }
    }
}