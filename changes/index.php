<?php

die();

$changes = '/bitrix/modules/iblock/lib/propertyindex/storage.php
/bitrix/components/bitrix/sale.viewed.product/component.php
/bitrix/modules/sale/lib/internals/entity.php
/bitrix/modules/search/classes/general/search.php
/bitrix/modules/iblock/classes/general/iblocksection.php
/bitrix/modules/iblock/classes/general/iblockelement.php
/bitrix/modules/iblock/classes/mysql/iblock.php
/bitrix/modules/iblock/classes/general/iblockelement.php
/bitrix/modules/sale/handlers/paysystem/bill/template/template.php
/bitrix/components/bitrix/sale.location.selector.search/class.php
/bitrix/components/bitrix/sale.location.selector.search/get.php
/bitrix/modules/sale/handlers/delivery/additional/ruspost/reliability/reliability.php
/bitrix/modules/sale/handlers/delivery/additional/ruspost/reliability/service.php
/bitrix/modules/catalog/mysql/currency.php
/bitrix/modules/currency/general/currency_rate.php
/bitrix/modules/sale/mysql/currency.php
/bitrix/modules/sale/admin/report_edit.php
/bitrix/modules/sale/handlers/paysystem/bill/template/template.php
/bitrix/modules/sale/admin/report_edit.php
/bitrix/modules/sale/lang/en/admin/report_edit.php
/bitrix/modules/sale/lang/ru/admin/report_edit.php
/bitrix/admin/reports/invoice.php
/bitrix/modules/sale/lib/helpers/admin/orderedit.php
/bitrix/js/ipol.sdek/ajax.php
/bitrix/header.php
/bitrix/modules/zixo.blanks/classes/tools.php
/bitrix/modules/sale/lib/tradingplatform/ebay/feed/data/converters/inventory.php
/bitrix/modules/sale/lib/tradingplatform/ebay/feed/data/converters/product.php
/bitrix/modules/sale/lib/tradingplatform/vk/feed/data/converters/product.php
/bitrix/modules/ipol.sdek/classes/general/sdekoption.php
/bitrix/modules/iblock/lib/component/elementlist.php
/bitrix/components/bitrix/sale.location.selector.search/class.php
/bitrix/components/bitrix/sale.order.ajax/class.php
/bitrix/components/ipol/ipol.sdekPickup/component.php
/bitrix/modules/ipol.kladr/classes/general/CKladr.php';

$changes = explode("\n",$changes);
$changes = array_map('trim',$changes);
$changes = array_unique($changes);

foreach ($changes as $path) {

    $fDir = dirname($path);

    if (!file_exists(__DIR__.'/'.$fDir)) {
        mkdir(__DIR__.'/'.$fDir,0775,true);
    }

    $fPath = '/'.pathinfo($path,PATHINFO_BASENAME);
    $fPath = '/'.$fDir.$fPath;

    copy($_SERVER['DOCUMENT_ROOT'].$path,__DIR__.$fPath);
    if (file_exists(__DIR__.$fPath)) {
        $content = file(__DIR__.$fPath);
        $content[0] = '<?php die(); ';
        $content = trim(join($content,"\n"));
        file_put_contents(__DIR__.$fPath,$content);
    };
}

