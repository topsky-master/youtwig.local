<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelWhirpoolDuplicatesModels{

    private static $countStrings = 100;

    public static function checkModels($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkFamiliarModels();

        static::getRedirect($modelLastPropId);

    }

    private static function checkFamiliarModels(){

        $modelLastPropId = 0;
        $modelEl = new CIBlockElement;

        $skip = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_last.txt');

        if(empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_last.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_list.txt', '');
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_deactivate.txt', '');

        }

        ++$skip;

        require_once dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_get.php';

        $WhirlpoolModels = array_slice($WhirlpoolModels, ($skip - 1) * static::$countStrings, static::$countStrings);

        foreach($WhirlpoolModels as $clearName => $modelIds){

            $modelLastPropId = $clearName;

            $modelIds = array_unique($modelIds);
            $modelIds = array_filter($modelIds);

            if(sizeof($modelIds) > 1){

                $firstId = array_shift($modelIds);
                $firstURI = '';

                $arModelSelect = Array(
                    "ID",
                    "DETAIL_PAGE_URL",
                );

                $arModelFilter = Array(
                    "IBLOCK_ID" => 17,
                    //"!PROPERTY_PRODUCTS_REMOVED" => false,
                    "ID" => $firstId
                );

                $resName = CIBlockElement::GetList(
                    (array()),
                    $arModelFilter,
                    false,
                    false,
                    $arModelSelect
                );

                $firstFound = false;

                if($resName){

                    $arName = $resName->GetNext();

                    if(isset($arName['ID'])
                        && !empty($arName['ID'])){

                        $firstURI = $arName['DETAIL_PAGE_URL'];
                        $firstFound = true;

                        foreach($modelIds as $modelId){

                            $arModelSelect = Array(
                                "ID",
                                "DETAIL_PAGE_URL",
                            );

                            $arModelFilter = Array(
                                "IBLOCK_ID" => 17,
                                //"PROPERTY_PRODUCTS_REMOVED" => false,
                                "ID" => $modelId
                            );

                            $resName = CIBlockElement::GetList(
                                (array()),
                                $arModelFilter,
                                false,
                                false,
                                $arModelSelect
                            );

                            $modelFound = false;

                            if($resName){

                                $arName = $resName->GetNext();

                                if(isset($arName['ID'])
                                    && !empty($arName['ID'])){

                                    $modelFound = true;
                                    $secondURI = $arName['DETAIL_PAGE_URL'];

                                    if(!empty($firstURI)
                                        && !empty($secondURI)){



                                        static::addRedirect($secondURI,$firstURI);

                                        if ($modelEl->Update($arName['ID'],
                                            Array(
                                                'ACTIVE' => 'N',
                                                'TIMESTAMP_X' => true)
                                        )
                                        ) {

											//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $arName['ID']);

                                            $link = (CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$arName['ID'].' -- '.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$firstId."\n";
                                            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_deactivate.txt', $link, FILE_APPEND);
                                            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_deactivate.txt', (CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].''.$secondURI.' -- '.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].''.$firstURI."\n", FILE_APPEND);

                                        }


                                    }


                                }


                            }

                            if(!$modelFound){
                                $link = (CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$modelId.' -- '.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$firstId."\n";
                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_list.txt', $link,FILE_APPEND);
                            }

                        }

                    }


                }

                if(!$firstFound){
                    $link = (CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$firstId."\n";
                    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_list.txt', $link,FILE_APPEND);
                }

            }

        }

        return $modelLastPropId ? $skip : 0;

    }

    private static function addRedirect($what,$where){

        $what = trim($what);
        $what = preg_replace('~http(s*?)://[^/]+?/~isu','',$what);
        $what = rtrim($what,'/');

        $where = trim($where);
        $where = preg_replace('~http(s*?)://[^/]+~isu','',$where);
        $where = empty($where) ? "/" : $where;

        $show = false;
        $rsData = CBXShortUri::GetList(
            Array(),
            Array(
                "URI" => '/'.trim($where,'/').'/',
                "SHORT_URI" => trim($what,'/')
            )
        );

        while($arRes = $rsData->Fetch()) {
            $show = true;
            break;
        }

        $rsData = CBXShortUri::GetList(
            Array(),
            Array(
                "URI" => '/'.trim($what,'/').'/',
                "SHORT_URI" => trim($where,'/')
            )
        );

        while($arRes = $rsData->Fetch()) {
            $show = true;
            break;
        }

        if (!$show
            &&
            (
                (trim(mb_strtolower($where),'/') != trim(mb_strtolower($what),'/'))
                || (trim(($where),'/') != trim(($what),'/'))
            )
        ){

            $arShortFields = Array(
                "URI" => '/'.trim($where,'/').'/',
                "SHORT_URI" => trim($what,'/'),
                "STATUS" => "301",
            );

            CBXShortUri::Add($arShortFields);

        }

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_last.txt', $skip);
            echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/whirpool_duplicates_check.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
            die();
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelWhirpoolDuplicatesModels::checkModels();