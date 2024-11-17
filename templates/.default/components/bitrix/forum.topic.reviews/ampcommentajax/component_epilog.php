<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @var array $arParams
 * @var array $arResult
 * @var string $strErrorMessage
 * @param CBitrixComponent $component
 * @param CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 */

$return = array();

if(isset($arResult["ERROR_MESSAGE"])
    && !empty($arResult["ERROR_MESSAGE"])) {

    $return = array(
        'ERROR' => trim(strip_tags($arResult["ERROR_MESSAGE"]))
    );

} else if(!empty($arResult["OK_MESSAGE"])) {

    $return = array(
        'SUCCESS' => trim(strip_tags($arResult["OK_MESSAGE"]))
    );

    $redirect_url = isset($_POST['back_page'])
    &&!empty($_POST['back_page'])
        ? trim($_POST['back_page'])
        : '';

    if(!empty($redirect_url)) {

        $redirect_url = sprintf('%s',$redirect_url);
        $redirect_url = (CMain::IsHTTPS() ? 'https' : 'http')
            .'://'. IMPEL_SERVER_NAME
            ."/" . preg_replace('~http(s*)://[^/]*?/~is','',ltrim($redirect_url,'/'));

        $redirect_url = mb_stripos($redirect_url,'?') === false
            ? $redirect_url.'?'
            : $redirect_url;

        $redirect_url = preg_replace('~#.*?$~','',$redirect_url);
        $redirect_url .= '&MID=&result=reply#reviews-reply-form';

        header("AMP-Redirect-To: ".$redirect_url);
        header("Access-Control-Expose-Headers: AMP-Redirect-To, AMP-Access-Control-Allow-Source-Origin");

    }

}

echo json_encode($return);

