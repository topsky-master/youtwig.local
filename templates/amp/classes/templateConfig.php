<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

require_once dirname(__FILE__)."/templateTools.php";
require_once dirname(__FILE__)."/processing/class-amp-content.php";

$pos 							= '';

if(mb_strpos($_SERVER['REQUEST_URI'],'/'.LANGUAGE_ID.'/') !== false){
    $pos						= LANGUAGE_ID;
}

CUtil::InitJSCore();

$curPage 						= $APPLICATION->GetCurPage(true);

global $USER;

if(is_object($USER) && $USER->IsAuthorized()){
    $panel   					= $USER->IsAuthorized();
} else {
    $panel						= false;
};

$count_offset					= 1;

$dir 							= SITE_DIR;
$dir							= $dir[0] == '/' ? mb_substr($dir,1,mb_strlen($dir)) : $dir;
$dir							= $dir[mb_strlen($dir) - 1] == '/' ? mb_substr($dir,0,-1) : $dir;


$in_main 						= (!preg_match('~^/.+?/~',$_SERVER['REQUEST_URI'])) ? 1 : 0;
$in_main 						= (!empty($dir) && (preg_match('~^/'.preg_quote($dir,'~').'/[^/]*?$~',$_SERVER['REQUEST_URI']))) ? 1 : $in_main;
$in_main						= defined('PAGE_404_PHP') ? 0 : $in_main;


define('IN_MAIN',$in_main);

if(!$in_main){
    $count_offset			  = 51;
}

if($panel){
    $count_offset			   += 147;
}

$page_class						= $APPLICATION->GetTitle();

$params 						= Array(
    "max_len" 				=> "200",
    "change_case" 			=> "L",
    "replace_space" 		=> "_",
    "replace_other" 		=> "_",
    "delete_repeat_replace" => "true",
);

$page_class						= CUtil::translit($page_class, LANGUAGE_ID, $params);

if(LANG_CHARSET 				== 'UTF-8'
    && LANGUAGE_ID 			!= 'en'){

    $tmp						= newsListMainTemplateTools::translit($APPLICATION->GetTitle(), $params);
    if(mb_strlen($tmp) > mb_strlen($page_class)){
        $page_class 			= $tmp;
    }
}

$page_class_add				    = preg_replace('~'.preg_quote('?','~').'.*?$~is','',$_SERVER['REQUEST_URI']);

$params 						= Array(
    "max_len" 				    => "200",
    "change_case" 			    => "L",
    "replace_space" 		    => " ",
    "replace_other" 		    => " ",
    "delete_repeat_replace"     => "true",
);

$page_class						.= ' '.CUtil::translit($page_class_add, LANGUAGE_ID, $params);

$lang_id						= mb_strtolower(LANGUAGE_ID);

if($USER->IsAuthorized()){
    $page_class				   .= ' isauthorized';
}
