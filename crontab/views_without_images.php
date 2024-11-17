<?php

function perrx(){
	print_r(error_get_last());
}

register_shutdown_function('perrx');

//https://youtwig.ru/local/crontab/views_without_images.php?intestwetrust=1&PageSpeed=off

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

class impelViewsWI{

    private static $countStrings = 500000;

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        $iwView = 241718;
        $modelLastPropId = 0;

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_views_wi_last.txt'));

        $aModelSelect = Array(
            "ID",
            "NAME",
            "PROPERTY_SIMPLEREPLACE_VIEW",
            "PROPERTY_MANUFACTURER"
        );

        $aModelFilter = Array(
            "IBLOCK_ID" => 17,
            "ACTIVE" => "Y",
            "!PROPERTY_SIMPLEREPLACE_VIEW" => false,
            "PROPERTY_MANUFACTURER_VALUE" => array("Indesit","Whirlpool")
            //"PREVIEW_PICTURE" => false
        );

        //Индезит/стинол

        if($skip > 0){

            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_views_wi.csv','a+');
            $fp1 = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_uviews_wi.csv','r');

            while($avLine = fgetcsv($fp1,0,';')){
                $avLine = array_map('trim',$avLine);
                $avLines[$avLine[0]] = $avLine;

            }

            fclose($fp1);


        } else {

            $avLines = array();

            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_views_wi.csv','w+');
            $fp1 = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_uviews_wi.csv','w+');
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_views_wi_last.txt', 0);

            fputcsv($fp,array(
                'ID вида',
                'Имя вида',
                'ID модели',
                'Имя модели',
                'Производитель',
                'Ссылка в админке на вид',
                'Ссылка в админке на модель'
            ),';');
        }


        $skip = empty($skip) ? 1 : $skip;
        $countStrings = 0;
		
        $rModel = impelCIBlockElement::GetList(
            ($order = Array()),
            $aModelFilter,
            false,
            false,
            $aModelSelect
        );

        if($rModel){

            while($aModel = $rModel->GetNext()){

                if(isset($aModel['PROPERTY_SIMPLEREPLACE_VIEW_VALUE'])
                    && !empty($aModel['PROPERTY_SIMPLEREPLACE_VIEW_VALUE'])
                ){

                    foreach($aModel['PROPERTY_SIMPLEREPLACE_VIEW_VALUE'] as $spViewValue){

                        if($spViewValue != $iwView){

                            $rView = \CIBlockElement::GetList(
                                array(),
                                ($avFilter = array(
                                    'PREVIEW_PICTURE' => false,
                                    'IBLOCK_ID' => 34,
                                    'ID' => $spViewValue
                                )),
                                false,
                                false,
                                ($avSelect = array('ID','NAME'))
                            );

                            if($rView){
                                while($aView = $rView->GetNext()){

                                    fputcsv($fp,
                                        array(
                                            $aView['ID'],
                                            $aView['NAME'],
                                            $aModel['ID'],
                                            $aModel['NAME'],
                                            $aModel['PROPERTY_MANUFACTURER_VALUE'],
                                            ((CMain::IsHTTPS() ? 'https' : 'http').'://'.'youtwig.ru'.'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=34&type=catalog&ID='.$aView['ID'].'&lang=ru&find_section_section=0&WF=Y'),
                                            ((CMain::IsHTTPS() ? 'https' : 'http').'://'.'youtwig.ru'.'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$aModel['ID'].'&lang=ru&find_section_section=0&WF=Y')
                                        ),
                                        ';');

                                    $avLines[$aView['ID']] = array($aView['ID'],$aView['NAME'],((CMain::IsHTTPS() ? 'https' : 'http').'://'.'youtwig.ru'.'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=34&type=catalog&ID='.$aView['ID'].'&lang=ru&find_section_section=0&WF=Y'));

                                }

                            }

                        }

                    }


                }



                ++$countStrings;
                $modelLastPropId = $aModel['ID'];

            }

        }

        if($countStrings < static::$countStrings){
            $modelLastPropId = 0;
        }

        fclose($fp);

        $fp1 = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_uviews_wi.csv','w+');

        if(!empty($avLines)){
            foreach($avLines as $avLine){
                fputcsv($fp1,$avLine,';');
            }
        }

        fclose($fp1);

        ++$skip;

        return $modelLastPropId ? $skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_views_wi_last.txt', $skip);
            die('<html><header><meta http-equiv="refresh" content="'.mt_rand(0,1).';URL=\''.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/views_without_images.php?intestwetrust=1&skip='.$lastId.'&time='.time().'&PageSpeed=off\'" /><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.'youtwig.ru'.'/local/crontab/views_without_images.php?intestwetrust=1&PageSpeed=off&time='.time().'";},'.mt_rand(500,700).');</script></header><body><h1>'.time().'</h1></body></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_views_wi_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelViewsWI::getList();