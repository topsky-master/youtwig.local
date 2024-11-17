<?php

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelFixText{

    private static $countStrings = 200;
    private static $iblockId = 0;

    public static function getList($iblockId){

        global $USER;

        static::$iblockId = $iblockId;
        $iLastProdId = 0;
        $iLastProdId = static::checkList();

        static::getRedirect($iLastProdId);

    }

    private static function checkList(){

        $prodEl = new CIBlockElement;

        $iLastProdId = 0;


        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_fix_text_last.txt'));

        $arProductSelect = Array(
            "ID",
            "PREVIEW_TEXT",
            "DETAIL_TEXT",
        );

        $arProductFilter = Array(
            "IBLOCK_ID" => static::$iblockId
        );

        if($skip > 0){

            $sMod = 'a+';

        } else {

            $sMod = 'w+';
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_fix_text_last.txt', 0);

        }

        $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/fix_text'.static::$iblockId.'.csv',$sMod);

        $skip = empty($skip) ? 1 : $skip;
        $countStrings = 0;

        $arProductNavParams = array(
            'nTopCount' => false,
            'nPageSize' => static::$countStrings,
            'iNumPage' => $skip,
            'checkOutOfRange' => true
        );

        $resProduct = CIBlockElement::GetList(
            ($order = Array()),
            $arProductFilter,
            false,
            $arProductNavParams,
            $arProductSelect
        );

        if($resProduct){

            while($arProduct = $resProduct->GetNext()) {

                ++$countStrings;
                $iLastProdId = $arProduct['ID'];

                /* if(isset($arProduct['PREVIEW_TEXT'])
                    && !empty($arProduct['PREVIEW_TEXT'])
                    && ($arProduct['PREVIEW_TEXT_TYPE'] == 'text')
                ) {

                    $arProduct['PREVIEW_TEXT'] = htmlspecialchars_decode($arProduct['PREVIEW_TEXT'],ENT_QUOTES,LANG_CHARSET);

                    if(preg_match('~<[^>]+?>~isu',$arProduct['PREVIEW_TEXT'])) {

                        $updProd = Array(
                            'TIMESTAMP_X' => true,
                            'PREVIEW_TEXT' => $arProduct['PREVIEW_TEXT'],
                            'PREVIEW_TEXT_TYPE' => 'html',
                        );

                        $prodEl->Update($arProduct['ID'],$updProd);
                        \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(static::$iblockId, $arProduct['ID']);

                    }
                } */


                if(isset($arProduct['DETAIL_TEXT'])
                    && !empty($arProduct['DETAIL_TEXT'])
                    && ($arProduct['DETAIL_TEXT_TYPE'] != 'html')
                ) {

                    $link = (CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID='.static::$iblockId.'&type=catalog&ID='.$arProduct['ID'].'&lang=ru&find_section_section=-1';
                    fputcsv($fp,array($arProduct['ID'],$link),';');

                    /* $arProduct['DETAIL_TEXT'] = htmlspecialchars_decode($arProduct['DETAIL_TEXT'],ENT_QUOTES,LANG_CHARSET);

                    if(preg_match('~<[^>]+?>~isu',$arProduct['DETAIL_TEXT'])) {

                        $updProd = Array(
                            'TIMESTAMP_X' => true,
                            'DETAIL_TEXT' => $arProduct['DETAIL_TEXT'],
                            'DETAIL_TEXT_TYPE' => 'html',
                        );

                        $prodEl->Update($arProduct['ID'],$updProd);
                        \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(static::$iblockId, $arProduct['ID']);

                    } */
                }

            }

        }

        fclose($fp);

        ++$skip;

        return $iLastProdId ? $skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_fix_text_last.txt', $skip);
            //header('Location: '.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/fix_models.php?intestwetrust=1&time='.time());
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/fix_text.php?intestwetrust=1&time='.time().'" /></head></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_fix_text_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock")) {

    impelFixText::getList(16);
    //impelFixText::getList(16);

}