<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED  !== true)
    die();

class ImpelLocationComponent extends CBitrixComponent
{
    private $aLocTypes = array();
    private $iLimit = 10;

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    private function getLocationTypes(){

        $types = array();
        $res = \Bitrix\Sale\Location\TypeTable::getList(array('select' => array('ID', 'CODE')));
        while ($item = $res->fetch()) {
            $types[$item['ID']] = $item['CODE'];
        }

        $this->aLocTypes = $types;

    }

    private function getStreet(){

        $this->arResult['STREET'] = '';

        if(isset($this->arResult['LOCATION'])){

            $aLoc = $this->arResult['LOCATION'];

            if(isset($aLoc)
                && isset($aLoc['TYPE_CODE'])
                && $aLoc['TYPE_CODE'] == 'STREET'){

                $this->arResult['STREET'] = $aLoc['LOC_NAME'];
                $this->arResult['STREET_ID'] = $aLoc['ID'];

            }

        }

    }

    private function getCity(){

        $this->arResult['CITY'] = '';
        $this->arResult['HAS_STREETS'] = 0;

        if(isset($this->arResult['LOCATION'])){

            $aLoc = $this->arResult['LOCATION'];

            if(isset($aLoc['TYPE_CODE'])
                && $aLoc['TYPE_CODE'] != 'STREET'){

                if(!empty($this->arResult['CITY'])){

                    $current = ', '.$aLoc['LOC_NAME'];

                } else {

                    $current = $aLoc['LOC_NAME'];

                }

                if(mb_stripos($this->arResult['CITY'],$current) === false)
                    $this->arResult['CITY'] .= $current;


            }

            if(isset($aLoc['TYPE_CODE'])
                && $aLoc['TYPE_CODE'] == 'CITY'){
                $this->arResult['CITY_ID'] = $aLoc['ID'];
            }

            if(isset($aLoc['PARENT_ID'])
                && !empty($aLoc['PARENT_ID'])
            ) {

                $this->getParentLeaf($aLoc['PARENT_ID'],$this->arResult['CITY']);

            }

            if(isset($this->arResult['CITY_ID'])
                && !empty($this->arResult['CITY_ID'])) {

                $this->arResult['HAS_STREETS'] = $this->hasStreets($this->arResult['CITY_ID']);
            }

        }

    }

    private function getParentLeaf($pValue,&$locName){

        $data = array(
            'select' => array(
                '*',
                'LOC_NAME' => 'NAME.NAME',
            ),
            'filter' => array(
                'ID' => $pValue,
                '=NAME.LANGUAGE_ID' => LANGUAGE_ID
            )
        );

        $rLoc = \Bitrix\Sale\Location\LocationTable::getList($data);

        if($rLoc) {

            $aLoc = $rLoc->fetch();
            $aLoc['TYPE_CODE'] = $this->aLocTypes[$aLoc['TYPE_ID']];

            if($aLoc['TYPE_CODE'] != 'STREET'){

                if(!empty($locName)){

                    $current = ', '.$aLoc['LOC_NAME'];

                } else {

                    $current = $aLoc['LOC_NAME'];

                }

                if(mb_stripos($locName,$current) === false)
                    $locName .= $current;

            }

            if(isset($aLoc['TYPE_CODE'])
                && $aLoc['TYPE_CODE'] == 'CITY'){
                $this->arResult['CITY_ID'] = $aLoc['ID'];
            }

            if(isset($aLoc['PARENT_ID'])
                && !empty($aLoc['PARENT_ID'])
            ) {

                $this->getParentLeaf($aLoc['PARENT_ID'],$locName);

            }

        }

    }

    public function getValues(){

        $pValue = $this->arParams['PROPERTY_VALUE'];

        $pValue = empty($pValue) ? 0 : $pValue;

        if($pValue) {

            $data = array(
                'select' => array(
                    '*',
                    'LOC_NAME' => 'NAME.NAME',
                ),
                'filter' => array(
                    'ID' => $pValue,
                    '=NAME.LANGUAGE_ID' => LANGUAGE_ID
                )
            );

            $rLoc = \Bitrix\Sale\Location\LocationTable::getList($data);

            if($rLoc) {

                $aLoc = $rLoc->fetch();
                $aLoc['TYPE_CODE'] = $this->aLocTypes[$aLoc['TYPE_ID']];
                $aLoc = $aLoc;

            }

            $this->arResult['LOCATION'] = $aLoc;

        }

    }

    private function jsonRequest(){

        $iCityId = isset($_REQUEST['cityid']) ? (int)trim($_REQUEST['cityid']) : 0;
        $sPrase = isset($_REQUEST['phrase']) ? trim(urldecode($_REQUEST['phrase'])) : '';

        if(mb_stripos($sPrase,',') !== false) {
            $sPrases = explode(',',$sPrase);
            $sPrases = array_map('trim',$sPrases);
            $sPrases = array_filter($sPrases);
            $sPrases = !is_array($sPrases) ? array($sPrase) : $sPrases;
            $sPrase = array_pop($sPrases);
            $sPrase = trim($sPrase);
        }

        $sPrase = str_ireplace('%','',$sPrase);

        $data = false;

        if(mb_strlen($sPrase) > 1){
            $data = $this->searchLocation($sPrase, $iCityId);
            $data = empty($data) ? false : $data;
        }

        echo json_encode($data,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        die();

    }

    private function hasStreetsJson(){

        $locId = isset($_REQUEST['locid'])
            ? trim((int)$_REQUEST['locid'])
            : 0;
        $cnt = $this->hasStreets($locId);
        echo json_encode(array('CNT' => $cnt),JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        die();
    }

    private function hasStreets($locId){

        $data = array(
            'select' => array(
                'CNT',
            ),
            'runtime' => array(
                new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)')
            ),
            'filter' => array(
                'PARENT_ID' => $locId,
            ),
            'limit' => 1,
            'order' => array(
                'LEFT_MARGIN' => 'asc'
            )
        );

        $rLoc = \Bitrix\Sale\Location\LocationTable::getList($data);

        $cnt = 0;

        if($rLoc) {

            $aLoc = $rLoc->fetch();
            $cnt = $aLoc['CNT'];

        }

        return $cnt;

    }

    private function searchLocation($sPrase, $iCityId = 0){

        $offset = isset($_REQUEST['offset']) ? (int)trim($_REQUEST['offset']) : 0;
        $locType = isset($_REQUEST['loctype']) ? trim($_REQUEST['loctype']) : 'city';

        $data = array(
            'select' => array(
                '*',
                'LOC_NAME' => 'NAME.NAME'
            ),
            'filter' => array(
                '%NAME.NAME' => $sPrase,
                '=NAME.LANGUAGE_ID' => LANGUAGE_ID
            ),
            'limit' => $this->iLimit,
            'offset' => $offset,
            'order' => array(
                'LEFT_MARGIN' => 'asc'
            )
        );

        $aTypes = array_flip($this->aLocTypes);

        if($iCityId){

            $data['filter']['PARENT_ID'] = $iCityId;
            $data['filter']['=TYPE_ID'] = $aTypes['STREET'];

        } else {

            $data['filter'] = array(
                array(
                    '%NAME.NAME' => $sPrase,
                    '=NAME.LANGUAGE_ID' => LANGUAGE_ID,
                    '=TYPE_ID' => ($locType == 'city' ? $aTypes['CITY'] : $aTypes['VILLAGE'])
                )

            );

            if(!$offset && $locType == 'city'){

                $rLoc = \Bitrix\Sale\Location\LocationTable::getList($data);

                if(!($rLoc  && $rLoc->fetch())) {

                    $data['filter'] = array(
                        array(
                            '%NAME.NAME' => $sPrase,
                            '=NAME.LANGUAGE_ID' => LANGUAGE_ID,
                            '=TYPE_ID' => $aTypes['VILLAGE']
                        )

                    );

                    $locType = 'village';

                }

            }

        }

        $rLoc = \Bitrix\Sale\Location\LocationTable::getList($data);

        $data = array();

        if($rLoc) {

            while($aLoc = $rLoc->fetch()){

                $locName = $aLoc['LOC_NAME'];

                if(!$iCityId) {

                    $locName = Bitrix\Sale\Location\Admin\LocationHelper::getLocationPathDisplay($aLoc['CODE']);

                }

                $locName = explode(',',$locName);
                $locName = !empty($locName) && !is_array($locName) ? array($locName) : $locName;
                $locName = array_reverse($locName);
                $locName = array_map('trim',$locName);
                $locName = array_unique($locName);
                $locName = join(', ',$locName);

                $data[$aLoc['ID']]['name'] = $locName;
                $data[$aLoc['ID']]['value'] = $aLoc['ID'];
                $data[$aLoc['ID']]['loctype'] = $locType;

            }

        }

        return $data;
    }



    private function checkJsonRequest(){

        return isset($_REQUEST['rjson']) && $_REQUEST['rjson'] ? true : false;

    }

    private function getZipCode(){

        $locId = isset($_REQUEST['locid'])
            ? trim((int)$_REQUEST['locid'])
            : 0;

        $ZIP = '';

        if($locId){

            $data = array(
                'select' => array(
                    'ZIP' => 'EXTERNAL.XML_ID'
                ),
                'filter' => array(
                    'ID' => $locId,
                    '=NAME.LANGUAGE_ID' => LANGUAGE_ID
                )
            );

            $rLoc = \Bitrix\Sale\Location\LocationTable::getList($data);

            if($rLoc) {

                $aLoc = $rLoc->fetch();

                if(isset($aLoc['ZIP'])
                    && !empty($aLoc['ZIP'])) {

                    $ZIP = $aLoc['ZIP'];

                }

            }

        }

        echo json_encode(array('ZIP' => $ZIP),JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        die();

    }

    public function executeComponent()
    {

        $this->arResult = array();
        $this->iLimit = $this->arParams['LIMIT'] ? (int)$this->arParams['LIMIT'] : 10;

        CModule::IncludeModule('sale');
        $this->getLocationTypes();

        if(!$this->checkJsonRequest()){

            $this->getValues();
            $this->getStreet();
            $this->getCity();

            $this->includeComponentTemplate();

        } else {

            $action = isset($_REQUEST['action'])
                ? trim($_REQUEST['action'])
                : '';

            switch ($action) {
                case 'hasstreets':
                    $this->hasStreetsJson();
                    break;
                case 'zipcode':
                    $this->getZipCode();
                    break;
                default:
                    $this->jsonRequest();
                    break;
            }

        }

    }
}