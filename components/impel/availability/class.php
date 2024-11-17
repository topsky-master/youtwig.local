<?

use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED  !== true)
    die();

class ImpelAvailabilityComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    private function canPreorderItem($product_id){
        $isPreodered = Application::getInstance()->getContext()->getRequest()->getCookie("ispreodered".$product_id);
        return (bool)$isPreodered;
    }

    private function getAvailability(){
        global $USER;

        $result = array();
        $product_id = $this->arParams['PRODUCT_ID'];
        $product_url = isset($this->arParams['PRODUCT_URL'])
        && !empty($this->arParams['PRODUCT_URL'])
            ? $this->arParams['PRODUCT_URL']
            : '';

        $max_quantity = get_quantity_product($product_id);

        $bHasProvider = false;

        if(!empty($product_id)){

            $product_id = intval($product_id);
            $product_id = abs($product_id);
            $stores_tooltip = '';

            $has_price = !is_null($this->arParams['HAS_PRICE'])
                ? $this->arParams['HAS_PRICE']
                : $this->getPrice($product_id);

            $can_buy = false;
            $quantity = 0;
            $in_stock_label = '';

            if($has_price) {

                $obCache = new CPHPCache;
                $cacheTime = 86400;
                $cacheID = 'catalog_onstock';
                $acOnStock = array();
                $can_buy = null;

                $cacheFile = $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache/'.md5('on_stock').'.php';

                if(file_exists($cacheFile)) {

                    require $cacheFile;

                    if(isset($acOnStock[$product_id])) {
                        $can_buy = $acOnStock[$product_id];
                    }
                }

                if(is_null($can_buy)){
                    $can_buy = canYouBuy($product_id, $has_price);
                }

                if($can_buy){

                    $buy_id = getBondsProduct($product_id);

                    $outnumber = get_quantity_product($buy_id);
                    $poutnumber = get_quantity_product_provider($buy_id);

                    $provider_percent = COption::GetOptionString("my.stat", "provider_percent", 0);

                    if($poutnumber > 0 && $poutnumber == $outnumber && $provider_percent > 0){
                        $bHasProvider = true;
                    }

                    $rsStore = CCatalogStoreProduct::GetList(
                        array(),
                        array('PRODUCT_ID' => $buy_id, "!STORE_ID" => array(3,6,10)),
                        false,
                        false
                    );

                    $in_stock_labels = array();

                    if ($rsStore){

                        while($arStore = $rsStore->Fetch()){

                            $amount = (float)$arStore['AMOUNT'];
                            $quantity += $amount;

                            $in_stock_label = '<b>' . (GetMessage('CT_BCE_CATALOG_STORE_'.$arStore['STORE_ID']) != "" ? GetMessage('CT_BCE_CATALOG_STORE_'.$arStore['STORE_ID']) : ($arStore['STORE_NAME'])).'</b>';

                            if($amount <= 0){
                                $in_stock_label .= ' '.GetMessage('CT_BCE_CATALOG_NOT_AVAILABLE');
                            } elseif($amount <= 10){
                                $in_stock_label .= ' '.GetMessage('CT_BCS_CATALOG_IN_STOCK_NOT_MUCH_LABEL');
                            } elseif($amount > 10){
                                $in_stock_label .= ' '.GetMessage('CT_BCS_CATALOG_IN_STOCK_MUCH_LABEL');
                            }

                            $in_stock_labels[] = $in_stock_label;

                        }

                    }

                    $can_buy = $quantity > 0 ? $can_buy : false;

                    if($this->arParams['STORES_TOOLTIP'] == 'Y') {

                        $stores_tooltip = $in_stock_labels;

                    }

                }

            }

            //CIBlockElement::CounterInc($this->arParams['PRODUCT_ID']);

            $result = array(
                'MAX_QUANTITY' => $max_quantity,
                'HAS_PROVIDER' => $bHasProvider,
                'HAS_PRICE' => $has_price,
                'PRODUCT_ID' => $product_id,
                'CAN_BUY' => $can_buy,
                'PRINT_QUANTITY' => $quantity,
                'IN_STOCK_LABEL' => $in_stock_label,
                'IS_PREODERED' => $this->canPreorderItem($product_id),
                'STORES_TOOLTIP' => $stores_tooltip,
                'PRODUCT_URL' => $product_url
            );

        }

        return $result;
    }

    private function getPrice($product_id){

        $price = CCatalogProduct::GetOptimalPrice($product_id,1);

        return (isset($price['PRICE'])
            && isset($price['PRICE']['PRICE'])
            && $price['PRICE']['PRICE'] > 0
            && isset($price['PRICE']['CURRENCY']))
            ? true
            : false;
    }

    public function executeComponent()
    {
        $this->arResult = array();
        $this->arResult = $this->getAvailability();
        $this->includeComponentTemplate();
    }
}