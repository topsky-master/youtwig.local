<?php
$arUrlRewrite=array (
  118 => 
  array (
    'CONDITION' => '#^/bitrix/services/yandexpay.pay/trading/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/yandexpay.pay/trading/index.php',
    'SORT' => 1,
  ),
  112 => 
  array (
    'CONDITION' => '#^={isset($arParams["SEF_URL_TEMPLATES"])?$arParams["SEF_URL_TEMPLATES"]["element"]:$arParams["DETAIL_URL"]}\\??(.*)#',
    'RULE' => '&$1',
    'ID' => 'bitrix:catalog.section',
    'PATH' => '/bitrix/templates/amp/components/bitrix/catalog.element/ampproduct/component_epilog.php',
    'SORT' => 100,
  ),
  106 => 
  array (
    'CONDITION' => '#^/bitrix/services/yandex.market/trading/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/yandex.market/trading/index.php',
    'SORT' => 100,
  ),
  119 => 
  array (
    'CONDITION' => '#^={$bFilterPath."filter/(.+?)/"}\\??(.*)#',
    'RULE' => 'SMART_FILTER_PATH=$1&$2',
    'ID' => 'impel:catalog.smart.filter',
    'PATH' => '/bitrix/templates/.default/components/bitrix/news.detail/brand/component_epilog.php',
    'SORT' => 100,
  ),
  108 => 
  array (
    'CONDITION' => '#^={$filterPath."filter/(.+?)/"}\\??(.*)#',
    'RULE' => 'SMART_FILTER_PATH=$1&$2',
    'ID' => 'impel:catalog.smart.filter',
    'PATH' => '/bitrix/templates/.default/components/bitrix/news.detail/nmodel/component_epilog.php',
    'SORT' => 100,
  ),
  1 => 
  array (
    'CONDITION' => '#^/online/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1',
    'ID' => '',
    'PATH' => '/desktop_app/router.php',
    'SORT' => '100',
  ),
  109 => 
  array (
    'CONDITION' => '#^/video/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1&videoconf',
    'ID' => 'bitrix:im.router',
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  115 => 
  array (
    'CONDITION' => '#^/model/[^/]+?/([0-9]+?)/(\\\\?(.*))?#',
    'RULE' => 'ELEMENT_ID=$1',
    'ID' => '',
    'PATH' => '/mproduct/index.php',
    'SORT' => 100,
  ),
  84 => 
  array (
    'CONDITION' => '#^/amp/catalog/([^/]+?)/\\??(.*)#',
    'RULE' => 'ELEMENT_CODE=$1&$2',
    'ID' => 'bitrix:catalog.element',
    'PATH' => '/amp/catalog/index.php',
    'SORT' => 100,
  ),
  9 => 
  array (
    'CONDITION' => '#^/info/([\\w\\d\\-]+)(\\\\?(.*))?# ',
    'RULE' => 'CODE=$1',
    'ID' => '',
    'PATH' => '/info/detail.php',
    'SORT' => '100',
  ),
  103 => 
  array (
    'CONDITION' => '#^/amp/model/([^/]+?)/\\??(.*)#',
    'RULE' => 'ELEMENT_CODE=$1&$2',
    'ID' => '',
    'PATH' => '/amp/model/index.php',
    'SORT' => 100,
  ),
  11 => 
  array (
    'CONDITION' => '#^/bitrix/services/ymarket/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/ymarket/index.php',
    'SORT' => '100',
  ),
  117 => 
  array (
    'CONDITION' => '#^/acrit.exportproplus/(.*)#',
    'RULE' => 'path=$1',
    'ID' => NULL,
    'PATH' => '/acrit.exportproplus/index.php',
    'SORT' => 100,
  ),
  12 => 
  array (
    'CONDITION' => '#^/model/(.+?)/(\\\\?(.*))?#',
    'RULE' => 'CODE=$1',
    'ID' => '',
    'PATH' => '/model/index.php',
    'SORT' => '100',
  ),
  100 => 
  array (
    'CONDITION' => '#^/brand/(.+?)/(\\\\?(.*))?#',
    'RULE' => 'ELEMENT_CODE=$1&$2',
    'ID' => '',
    'PATH' => '/brand/detail.php',
    'SORT' => 100,
  ),
  14 => 
  array (
    'CONDITION' => '#^/online/(/?)([^/]*)#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/desktop_app/router.php',
    'SORT' => '100',
  ),
  16 => 
  array (
    'CONDITION' => '#^/stssync/calendar/#',
    'RULE' => '',
    'ID' => 'bitrix:stssync.server',
    'PATH' => '/bitrix/services/stssync/calendar/index.php',
    'SORT' => '100',
  ),
  76 => 
  array (
    'CONDITION' => '#^/personal/order/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.order',
    'PATH' => '/personal/order/index.php',
    'SORT' => 100,
  ),
  19 => 
  array (
    'CONDITION' => '#^/amp/news/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/amp/news/index.php',
    'SORT' => 100,
  ),
  20 => 
  array (
    'CONDITION' => '#^/amp/info/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/amp/info/index.php',
    'SORT' => 100,
  ),
  132 => 
  array (
    'CONDITION' => '#^/articles/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/local/site/articles/index.php',
    'SORT' => 100,
  ),
  127 => 
  array (
    'CONDITION' => '#^/novosti/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/local/site/novosti/index.php',
    'SORT' => 100,
  ),
  131 => 
  array (
    'CONDITION' => '#^/catalog/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/search/index.php',
    'SORT' => 100,
  ),
  93 => 
  array (
    'CONDITION' => '#^/forum/#',
    'RULE' => '',
    'ID' => 'bitrix:forum',
    'PATH' => '/forum/index.php',
    'SORT' => 100,
  ),
  113 => 
  array (
    'CONDITION' => '#^/base/#',
    'RULE' => '',
    'ID' => 'impel:catalog',
    'PATH' => '/base/index.php',
    'SORT' => 100,
  ),
  124 => 
  array (
    'CONDITION' => '#^/news/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/news/index.php',
    'SORT' => 100,
  ),
  71 => 
  array (
    'CONDITION' => '#^/amp/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/amp/sections/index.php',
    'SORT' => 100,
  ),
);
