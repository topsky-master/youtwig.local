<?php

spl_autoload_register(function ($class) {
    $prefix = 'Api\\';
    $base_dir = __DIR__ . '/classes/';
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

$catalog_included = CModule::IncludeModule("catalog");
$sale_included = CModule::IncludeModule("sale");
$iblock_included =  CModule::IncludeModule("iblock");
$currency_included = CModule::IncludeModule("currency");
$sender_included = CModule::IncludeModule('sender');

define('SMS_TRY',180);

define('CATALOG_INCLUDED', $catalog_included);
define('SALE_INCLUDED', $sale_included);
define('IBLOCK_INCLUDED', $iblock_included);
define('CURRENCY_INCLUDED', $currency_included);
define('SENDER_INCLUDED', $sender_included);

if(class_exists('CMain') && !defined('IMPEL_PROTOCOL')){
    define('IMPEL_PROTOCOL',(\CMain::IsHTTPS() ? 'https://' : 'http://'));
}

if(!defined('IMPEL_PROTOCOL')){
    define('IMPEL_PROTOCOL','https://');
}

global $argv;

if(isset($argv) && !empty($argv)) {
    $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'youtwig.ru';
}

if(defined('IMPEL_PROTOCOL')
    &&!defined('IMPEL_SERVER_NAME')){

    if(filter_var(IMPEL_PROTOCOL. $_SERVER['HTTP_HOST'], FILTER_VALIDATE_URL)){

        define('IMPEL_SERVER_NAME',$_SERVER['HTTP_HOST']);

    } else {

        define('IMPEL_SERVER_NAME','youtwig.ru');

    }

}

$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];

if(!defined('IMPEL_SERVER_NAME')
    && filter_var(IMPEL_PROTOCOL. $_SERVER['SERVER_NAME'], FILTER_VALIDATE_URL)){
    //$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'youtwig.ru';
    define('IMPEL_SERVER_NAME',$_SERVER['SERVER_NAME']);
}

require_once __DIR__.'/sender/handlers.php';
require_once __DIR__.'/extended_classes.php';
require_once __DIR__.'/functions.php';
require_once __DIR__.'/basket.php';
require_once __DIR__.'/order.php';
