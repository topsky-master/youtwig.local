<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$_SESSION['user_js']  = false;

if(isset($_REQUEST['user_resolution'])
    && !empty($_REQUEST['user_resolution'])
    && preg_match('~^[0-9]+?x[0-9]+?$~is',$_REQUEST['user_resolution']))

{
    $_SESSION['user_resolution'] = trim($_REQUEST['user_resolution']);
    $_SESSION['user_js'] = true;

}

if(isset($_REQUEST['deviceinfo'])
    && !empty($_REQUEST['deviceinfo'])
    && preg_match('~^[a-z0-9\-\_\s]~is',$_REQUEST['deviceinfo']))

{
    $_SESSION['deviceinfo'] = trim($_REQUEST['deviceinfo']);
    $_SESSION['user_js'] = true;

}

session_write_close();

echo json_encode(array('return' => true));

die();