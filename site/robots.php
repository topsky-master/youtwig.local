<?php

$host = filter_var($_SERVER['HTTP_HOST'], FILTER_VALIDATE_DOMAIN);
$content = '';
$host = str_ireplace('www.','',$host);

if(($host !== false)
    && file_exists($_SERVER['DOCUMENT_ROOT'].'/robots/'.$host.'.txt')){
    $content = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/robots/'.$host.'.txt');
} else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/robots/youtwig.ru.txt')) {
    $content = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/robots/youtwig.ru.txt');
}
header("Content-Type: text/plain");
echo $content;