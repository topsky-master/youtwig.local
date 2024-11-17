<?php

error_reporting(E_ERROR);
ini_set('dislay_errors',0);

$request_uri_parts                                                                         = $_SERVER['REQUEST_URI'];

if(mb_stripos($request_uri_parts,'?')                                                        !==false){

    $request_uri_parts                                                                     = explode('?',$request_uri_parts);

} else {
    $request_uri_parts                                                                     = array($request_uri_parts,'');
}

$request_uri_parts[0]                                                                      = mb_strtolower($request_uri_parts[0]);

$request_uri                                                                               = $request_uri_parts[0] . (!empty($request_uri_parts[1]) ? ('?'.$request_uri_parts[1]) : '');

if(!headers_sent())
header("Location: ".$request_uri, true, 301);

?>