<?php

if(!defined('CATALOG_INCLUDED')) die();

/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage iblock
 */

if(!class_exists('CIBlockPropertySiteCity')) {
    class CIBlockPropertySiteCity
    {
        const USER_TYPE = 'ElementSiteCity';

        public static function GetUserTypeDescription()
        {
            return array(
                "PROPERTY_TYPE" => Bitrix\Iblock\PropertyTable::TYPE_STRING,
                "USER_TYPE" => "ElementSiteCity",
                "DESCRIPTION" => "Город/замена",
                "GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
                "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
                "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
                "GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
            );
        }


        public function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
        {
            static $cache = array();

            if (isset($strHTMLControlName['MODE']) && $strHTMLControlName["MODE"] == "CSV_EXPORT") {
                if (!isset($cache[$value["VALUE"]])) {
                    $values = static::getManValueTxt($value["VALUE"]);

                    if ($values[0] == IMPEL_SERVER_NAME) {
                        $cache[$value["VALUE"]] = $values[1];
                    }

                }

                return $cache[$value["VALUE"]];

            } elseif (mb_strlen($value["VALUE"]) > 0) {
                if (!isset($cache[$value["VALUE"]])) {
                    $values = static::getManValueTxt($value["VALUE"]);

                    if ($values[0] == IMPEL_SERVER_NAME) {
                        $cache[$value["VALUE"]] = $values[1];
                    }

                }

                return $cache[$value["VALUE"]];

            } else {
                return '';
            }
        }

        public function GetAdminListViewHTML($arProperty, $value)
        {
            static $cache = array();
            if (mb_strlen($value["VALUE"]) > 0) {
                if (is_array($cache) && !array_key_exists($value["VALUE"], $cache)) {

                    $values = static::getManValueTxt($value["VALUE"]);
                    $cache[$value["VALUE"]] = htmlspecialcharsbx($values[0] . ': ' . $values[1]);

                }
                return $cache[$value["VALUE"]];
            } else {
                return '&nbsp;';
            }
        }

        private static function getManOptions($value)
        {

            static $cache;

            list($id, $text) = explode(':', $value);

            $sOptions = '';
            $sOptions .= '<option value="">Выберите сайт</option>';

            if (empty($cache)) {

                $re = CIBlockPropertyEnum::GetList(
                    array("SORT" => "ASC"),
                    array("IBLOCK_ID" => 45,
                        "CODE" => "DOMAIN")
                );

                if ($re) {
                    while ($ef = $re->GetNext()) {

                        $cache[$ef['ID']] = $ef['VALUE'];

                    }
                }

            }

            foreach ($cache as $eid => $evalue) {

                if (empty($eid) || empty($evalue))
                    continue;

                $sOptions .= '<option' . (($eid == $id) ? ' selected="selected"' : '') . ' value="' . $eid . '">' . htmlspecialcharsbx($evalue) . '</option>';
            }

            return $sOptions;

        }

        public static function getManValueTxt($value)
        {

            list($id, $text) = explode(':', $value);
            $id = (int)trim($id);
            $name = '';

            if ($id) {

                $re = CIBlockPropertyEnum::GetList(
                    array("SORT" => "ASC"),
                    array("IBLOCK_ID" => 45,
                        "CODE" => "DOMAIN",
                        "ID" => $id)
                );

                if ($re) {
                    while ($ef = $re->GetNext()) {
                        $name = trim($ef['VALUE']);
                    }
                }

            }

            return array($name, $text);

        }

        public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
        {

            static $iCounter;

            if (!$iCounter) {
                $iCounter = 0;
            }

            ++$iCounter;

            $value["VALUE"] = isset($value["VALUE"]) ? $value["VALUE"] : '';

            $values = static::getManValueTxt($value["VALUE"]);
            $options = static::getManOptions($value["VALUE"]);

            return '<input name="' . htmlspecialcharsbx($strHTMLControlName["VALUE"]) . '" id="hc' . $iCounter . '" value="' . ((!empty($value["VALUE"]) && trim($value["VALUE"]) != ':') ? htmlspecialcharsbx($value["VALUE"]) : '') . '" type="hidden" />'
                . '<input id="tc' . $iCounter . '" dataid="' . $iCounter . '" value="' . htmlspecialcharsbx($values[1]) . '" type="text" onchange="this.dataid = this.getAttribute(\'dataid\');document.getElementById(\'hc\' + this.dataid).value = document.getElementById(\'sc\' + this.dataid).options[document.getElementById(\'sc\' + this.dataid).selectedIndex].value  + \': \' + document.getElementById(\'tc\' + this.dataid).value" />'
                . '<select id="sc' . $iCounter . '" dataid="' . $iCounter . '" onchange="this.dataid = this.getAttribute(\'dataid\');document.getElementById(\'hc\' + this.dataid).value = document.getElementById(\'sc\' + this.dataid).options[document.getElementById(\'sc\' + this.dataid).selectedIndex].value  + \': \' + document.getElementById(\'tc\' + this.dataid).value">' . $options . '</select>'
                . '<input type="button" value="x" onclick="if(this.parentNode.parentNode.previousSibling || (this.parentNode.parentNode.nextSibling && this.parentNode.parentNode.nextSibling.nextSibling)) this.parentNode.parentNode.remove();return false;" />';
        }

        public function GetSettingsHTML($arProperty, $strHTMLControlName, $arPropertyFields)
        {
            $arPropertyFields = array(
                "HIDE" => array("ROW_COUNT", "COL_COUNT", "WITH_DESCRIPTION"),
            );
            return '';
        }
    }

}

AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertySiteCity', 'GetUserTypeDescription'));

if(!class_exists('CIBlockPropertySMPFMulti')) {

    class CIBlockPropertySMPFMulti
    {
        private static $aSectionsList = array();
        private static $aPropsList = array();

        public static function GetUserTypeDescription()
        {
            return array(
                "PROPERTY_TYPE" => Bitrix\Iblock\PropertyTable::TYPE_STRING,
                "USER_TYPE" => 'ElementSMPFMulti',
                "DESCRIPTION" => "Конструктор для фильтра (тест)",
                "GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
                "GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
                "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
                "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
                "AddFilterFields" => array(__CLASS__,"AddFilterFields"),
                "ConvertToDB" => array(__CLASS__,"ConvertToDB"),
                "ConvertFromDB" => array(__CLASS__,"ConvertFromDB"),
            );
        }

        private static function getFilterValue($control,&$arFilter)
        {
            $filterValue = null;

            $controlName = $control["VALUE"];

            if(isset($arFilter[$controlName]) && !empty($arFilter[$controlName])){
                $filterValue = $arFilter[$controlName];
                unset($arFilter[$controlName]);
            }

            return $filterValue;
        }

        public static function AddFilterFields($arProperty, $control, &$arFilter, &$filtered)
        {
            $filtered = false;
            $filterValue = static::getFilterValue($control,$arFilter);

            if ($filterValue && file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/map_restore_products_'.mb_strtolower($arProperty["CODE"]).'.php'))
            {

                require $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/map_restore_products_'.mb_strtolower($arProperty["CODE"]).'.php';

                if(isset($aMaps)
                    && !empty($aMaps)
                    && isset($aMaps[$arProperty["CODE"]])
                    && !empty($aMaps[$arProperty["CODE"]])
                    && isset($aMaps[$arProperty["CODE"]][$filterValue])
                    && !empty($aMaps[$arProperty["CODE"]][$filterValue])
                ){

                    $arFilter["ID"] = $aMaps[$arProperty["CODE"]][$filterValue];
                    $filtered = true;

                }

            }

            if(!$filtered){
                $arFilter["ID"] = -1;
            }

        }

        public static function ConvertToDB($arProperty, $value) // сохранение в базу
        {
            
            $return = false;
            $sValue = 0;

            if(isset($value['VALUE']) && isset($value['VALUE']['p'])){

                $mSize = sizeof($value['VALUE']['p']);

                foreach($value['VALUE']['p'] as $iCount => $iValue) {
                    $value['VALUE']['o'][$iCount] = isset($value['VALUE']['o'][$iCount]) && !empty($value['VALUE']['o'][$iCount]) ? 1 : 0;
                }

                if(is_array($value) && array_key_exists("VALUE", $value))
                {

                    if(is_array($value["VALUE"])
                        && !empty($value["VALUE"])){

                        $value['VALUE'] = array_filter($value['VALUE'],function($value){
                            if(is_string($value)){
                                return trim($value) !== "";
                            }else{
                                return is_array($value);
                            }
                            
                        });

                        $value["VALUE"] = json_encode($value["VALUE"]);
                    }
                    
                    $return = $value;
                }

            }

            return $return ? $return : $value;
        }

        public static function ConvertFromDB($arProperty, $value) //извлечение из БД
        {
            $return = $value;

            if(!is_array($value['VALUE']))
            {
                $value['VALUE'] = json_decode($value['VALUE'],true);
                $value['VALUE'] = !empty($value['VALUE']) && !is_array($value['VALUE']) ? array($value['VALUE']) : $value['VALUE'];
                if(null !== $value['VALUE']) {
                    $value['VALUE'] = array_filter($value['VALUE'],function($value){

                        if(is_string($value)){
                            return trim($value) !== "";
                        }
                        else{
                            return is_array($value);
                        }
                    });
                    
                }
                $return = $value;
            }

            return $return;
        }

        private static function valueToArray($value){
            try {
                
                $avalues = is_array($value["VALUE"]) && sizeof($value["VALUE"]) ? $value["VALUE"] : (is_string($value["VALUE"]) ? json_decode($value["VALUE"], true): array());
                $avalues = !empty($avalues) && !is_array($avalues) ? array($avalues) : $avalues;
                if(empty($avalues)){
                    $avalues = array('s' => array(0 => ''), 'p' => array(0 => ''), 'v' => array(0 => ''), 'o' => array(0 => 0, 1 => 0));
                }

                return $avalues;
            } catch(Exception $e) {
                return array('s' => array(0 => ''), 'p' => array(0 => ''), 'v' => array(0 => ''), 'o' => array(0 => 0, 1 => 0));
            }
        }

        public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
        {
            $value["VALUE"] = is_array($value["VALUE"]) ? json_encode($value["VALUE"]) : $value["VALUE"];

            if(mb_strlen($value["VALUE"])>0) {
                return $value["VALUE"];
            } else {
                return '';
            }
        }

        public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
        {
            if(!empty($value["VALUE"])
                && is_array($value["VALUE"]))
            {
                $avalues = $value["VALUE"];

                $sreturn = '';

                foreach($avalues as $inum => $ivalue) {

                    $sreturn .= (!empty($sreturn) ? ', ' : '').htmlspecialcharsbx($ivalue);

                }

                return $sreturn;
            }
            else
            {
                return '&nbsp;';
            }
        }

        //PARAMETERS:
        //$arProperty - b_iblock_property.*
        //$value - array("VALUE","DESCRIPTION") -- here comes HTML form value
        //strHTMLControlName - array("VALUE","DESCRIPTION")
        //return:
        //safe html

        private static function GetPropsList(){

            if(empty(static::$aPropsList)){

                $dProps = CIBlockProperty::GetList(
                    Array('NAME' => 'ASC'),
                    Array(
                        "IBLOCK_ID" => 11,
                        "PROPERTY_TYPE" => "L"
                    )
                );

                if($dProps){

                    while ($aProps = $dProps->GetNext()) {
                        {
                            static::$aPropsList[mb_strtolower($aProps["CODE"])] = array('NAME' => $aProps["NAME"], 'VALUES' => array());

                            $dEnums = CIBlockPropertyEnum::GetList(
                                Array('NAME' => 'ASC'),
                                Array(
                                    "PROPERTY_ID" => $aProps["ID"]
                                )
                            );

                            if($dEnums){
                                while($aEnums = $dEnums->GetNext()){
                                    if(isset($aEnums["VALUE"])){
                                        static::$aPropsList[mb_strtolower($aProps["CODE"])]['VALUES'][mb_strtolower($aEnums["XML_ID"])] = trim($aEnums["VALUE"]);
                                    }
                                }
                            }

                        }

                    }

                }

            }

            return static::$aPropsList;
        }

        private static function GetSectionsList(){
            if(empty(static::$aSectionsList)){

                $dSectionList = \CIBlockSection::GetList(array('NAME' => 'ASC'), array(
                    "IBLOCK_ID" => 11,
                    "ACTIVE" => "Y"
                ), false, array("ID", "IBLOCK_ID", "NAME", "CODE"));

                if($dSectionList){
                    while($aSection = $dSectionList->GetNext()){

                        $sName = $aSection['NAME'].' ('.$aSection['ID'].')';
                        static::$aSectionsList[mb_strtolower($aSection['CODE'])] = $sName;

                    }
                }
            }

            return static::$aSectionsList;
        }

        public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
        {
            $sreturn = '';

            $aSectionsList = static::GetSectionsList();
            $aPropsList = static::GetPropsList();

            $avalues = static::valueToArray($value);

            $sreturn .= '<style>
				.filters .filter:first-of-type + .filter .remove-button, 
				tr:first-of-type .filters + .buttons .remove-button,
				.filters .filter:first-of-type .remove-button{ display: none!important;}
                .filters .filter label,
                .filters + .buttons,
                .filters .filter select{display:block;clear:both;float:none;margin-bottom: 10px; width: 100%}
                .filters .filter button,.filters .filter {margin-bottom: 15px}
                
                </style>';
            $sreturn .= '<script> 

                        function bindSelection(elt){
                             var cVal = elt.options[elt.selectedIndex].value;
                             var nElt = elt.nextSibling;
                             var nOptions = nElt.options;
                             for(var i = 0; i < nOptions.length; i++){
                                 if(nOptions[i].className == cVal){
                                     nOptions[i].hidden = false;
                                     nOptions[i].disabled = false;
                                 } else {
                                     nOptions[i].hidden = true;
                                     nOptions[i].disabled = true;
                                 }
                             }
                        }
                        
                        function copyFilter(elt){
							var inum = parseInt(elt.getAttribute("inum"));
                            var cParent = elt.parentNode.previousSibling;
                            var cNodes = elt.parentNode.previousSibling.childNodes;
                            var cLast = cNodes[cNodes.length-1];
                            var cClone = cLast.cloneNode(true);
							var isnum = parseInt(cLast.getAttribute("inum"));
                            cClone.setAttribute("inum",inum);
                            cParent.appendChild(cClone);
							var rExp = new RegExp("\\\["+isnum+"\\\]","gi");
							cClone.outerHTML = cClone.outerHTML.replace(rExp,"["+inum+"]");
							elt.setAttribute("inum",inum+1);
                            return false;
                        }</script>';
            $sreturn .= '<div class="filters">';

            {
                $inum = 0;
                $ivalue = ($avalues['s']);

                $sreturn .= '<div class="filter">';

                {

                    $sreturn .= '<select size="5" multiple="multiple" name="' . htmlspecialcharsbx($strHTMLControlName["VALUE"]) .'[s]['.$inum.']'. '" id="' . htmlspecialcharsbx($strHTMLControlName["VALUE"]) . ''. '">';
                    $sreturn .= '<option value=""'.(empty($ivalue) ? ' selected="selected"' : '').'>(категория не выбрана)</option>';

                    foreach($aSectionsList as $iId => $aSectVal) {
                        $sreturn .= '<option value="'.$iId.'"'.(!empty($ivalue) && in_array($iId,$ivalue) ? ' selected="selected"' : '').'>'.$aSectVal.'</option>';
                    }

                    $sreturn .= '</select>';

                }

                $sreturn .= '</div>';

                foreach($avalues['p'] as $isCount => $ivalue) {

                    $sreturn .= '<div class="filter" inum="'.$inum.'">';

                    $sreturn .= '<select onchange="bindSelection(this)" size="1" name="' . htmlspecialcharsbx($strHTMLControlName["VALUE"]) .'[p]['.$inum.']'. '" id="' . htmlspecialcharsbx($strHTMLControlName["VALUE"]) . ''. '">';
                    $sreturn .= '<option value=""'.($ivalue == '' ? ' selected="selected"':'').'>(параметр не выбран)</option>';

                    foreach($aPropsList as $iId => $aSectVal) {
                        $sreturn .= '<option value="'.$iId.'"'.($ivalue == $iId ? ' selected="selected"':'').'>'.$aSectVal['NAME'].'</option>';
                    }

                    $sreturn .= '</select>';
                    $bdvSel = !empty($ivalue) ? false: true;

                    $sreturn .= '<select size="5" class="vparams" multiple="multiple" name="' . htmlspecialcharsbx($strHTMLControlName["VALUE"]) .'[v]['.$inum.'][]'. '" id="' . htmlspecialcharsbx($strHTMLControlName["VALUE"]) . ''. '">';
                    $sreturn .= '<option value=""'.($bdvSel ? ' selected="selected"':' disabled="disabled" hidden="hidden"').'>(выберите параметр)</option>';

                    foreach($aPropsList as $iId => $aSectVal) {
                        foreach($aSectVal["VALUES"] as $ipId => $apSectVal) {
                            $sreturn .= '<option class="'.$iId.'" '.($ivalue == $iId ? (isset($avalues['v'][$inum]) && !empty($avalues['v'][$inum]) && in_array($ipId,$avalues['v'][$inum]) ? ' selected="selected"' : '') : ' hidden="hidden" disabled="disabled" ').' value="' . $ipId . '">' . $apSectVal . '</option>';
                        }

                    }

                    $sreturn .= '</select>';
                    $sreturn .= '<label><input type="checkbox" name="' . htmlspecialcharsbx($strHTMLControlName["VALUE"]) .'[o]['.$inum.']'. '"'.($avalues['o'][$inum] ? ' checked="checked"' : '').' /> не содержит</label>';
                    $sreturn .= '<input type="button" class="remove-button" value="✕" onclick="this.parentNode.remove(); return false;"  />';
                    $sreturn .= '</div>';

                    ++$inum;

                }


            }

            $sreturn .= '</div>';
            $sreturn .= '<div class="buttons"><input inum="'.$inum.'" type="button" value="+условие" onclick="return copyFilter(this);" /><input class="remove-button" type="button" value="✕" onclick="this.parentNode.parentNode.remove(); return false;" /></div>';

            return $sreturn;

        }

    }
}

AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertySMPFMulti', 'GetUserTypeDescription'));

if(!class_exists('CIBlockPropertyChanges')){
    class CIBlockPropertyChanges
    {
        const USER_TYPE = 'ElementChanges';
        protected static $aPropsList = array();

        public static function GetUserTypeDescription()
        {

            return array(
                "PROPERTY_TYPE" => Bitrix\Iblock\PropertyTable::TYPE_STRING,
                "USER_TYPE" => "ElementChanges",
                "DESCRIPTION" => "Замены (тест)",
                "GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
                "GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
                "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
                "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
            );
        }

        public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
        {
            static $cache = array();
            if(isset($strHTMLControlName['MODE']) && $strHTMLControlName["MODE"] == "CSV_EXPORT")
            {
                if(!isset($cache[$value["VALUE"]]))
                {
                    $values = static::getManValueTxt($value["VALUE"]);
                    $cache[$value["VALUE"]] = htmlspecialcharsbx($values[0].': '.$values[1]);
                }

                return $cache[$value["VALUE"]];
            }
            elseif(mb_strlen($value["VALUE"]) > 0)
            {
                if(!isset($cache[$value["VALUE"]]))
                {
                    $values = static::getManValueTxt($value["VALUE"]);
                    $cache[$value["VALUE"]] = htmlspecialcharsbx($values[0].': '.$values[1]);
                }

                return $cache[$value["VALUE"]];

            }
            else
            {
                return '';
            }
        }

        public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
        {
            static $cache = array();
            if(mb_strlen($value["VALUE"]) > 0)
            {
                if(is_array($cache) && !array_key_exists($value["VALUE"], $cache))
                {

                    $values = static::getManValueTxt($value["VALUE"]);
                    $cache[$value["VALUE"]] = htmlspecialcharsbx($values[0].': '.$values[1]);

                }
                return $cache[$value["VALUE"]];
            }
            else
            {
                return '&nbsp;';
            }
        }

        protected static function GetPropsList(){

            if(empty(static::$aPropsList)){

                $dProps = CIBlockProperty::GetList(
                    Array('NAME' => 'ASC'),
                    Array(
                        "IBLOCK_ID" => 11,
                        "PROPERTY_TYPE" => "L"
                    )
                );

                if($dProps){
                    while ($aProps = $dProps->GetNext()) {
                        {
                            static::$aPropsList[trim(mb_strtolower($aProps["CODE"]))] = trim($aProps["NAME"]);
                        }
                    }
                }

            }

            return static::$aPropsList;
        }

        private static function getManOptions($value){

            static $cache;

            list($id,$text) = explode(':', $value);

            $sOptions = '';
            $sOptions .= '<option value="">Выберите параметр</option>';

            if(empty($cache)) {
                $cache = static::GetPropsList();
            }

            foreach($cache as $eid => $evalue){
                if(empty($eid) || empty($evalue))
                {
                    continue;
                }
                $sOptions .= '<option' . (($eid == $id) ? ' selected="selected"' : '') . ' value="' . $eid . '">' . htmlspecialcharsbx($evalue) . '</option>';
            }

            return $sOptions;

        }

        private static function getManValueTxt($value) {

            static $cache;

            list($id,$text) = explode(':', $value);
            $id = (int)trim($id);
            $name = '';

            if($id) {
                if(empty($cache)) {
                    $cache = static::GetPropsList();
                }

                $name = trim($cache[$id]);
            }

            return array($name, $text);

        }

        public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
        {

            static $iCounter;

            if(!$iCounter) {
                $iCounter = 0;
            }

            ++$iCounter;

            $value["VALUE"] = isset($value["VALUE"]) ? $value["VALUE"] : '';

            $values = static::getManValueTxt($value["VALUE"]);
            $options = static::getManOptions($value["VALUE"]);

            $sreturn = '<script>
        if(typeof fallbackCopyTextToClipboard == "undefined") {
            function fallbackCopyTextToClipboard(text) {
              var textArea = document.createElement("textarea");
              textArea.value = text;
              
              // Avoid scrolling to bottom
              textArea.style.top = "0";
              textArea.style.left = "0";
              textArea.style.position = "fixed";
            
              document.body.appendChild(textArea);
              textArea.focus();
              textArea.select();
            
              try {
                var successful = document.execCommand("copy");
                var msg = successful ? "successful" : "unsuccessful";
                console.log("Fallback: Copying text command was " + msg);
              } catch (err) {
                console.error("Fallback: Oops, unable to copy", err);
              }
            
              document.body.removeChild(textArea);
            }
            
            function copyTextToClipboard(text,elt) {
              text = "["+text+"]";  
                
              if (!navigator.clipboard) {
                fallbackCopyTextToClipboard(text);
                return;
              }
              navigator.clipboard.writeText(text).then(function() {
                console.log("Async: Copying to clipboard was successful!");
              }, function(err) {
                console.error("Async: Could not copy text: ", err);
              });
              
              elt.value = "Скопировано...";
              
              setTimeout(function(){
                  elt.value = "Скопировать в буфер";
              },3000);
              
            }
        }

        </script>';

            $sreturn .= '<input name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" id="h'.$iCounter.'" value="'.((!empty($value["VALUE"]) && trim($value["VALUE"]) != ':') ? htmlspecialcharsbx($value["VALUE"]) : '').'" type="hidden" />'
                .'<input id="t'.$iCounter.'" dataid="'.$iCounter.'" value="'.htmlspecialcharsbx($values[1]).'" type="text" onchange="this.previousSibling.value = this.nextSibling.options[this.nextSibling.selectedIndex].value  + \': \' + this.value" />'
                .'<select id="s'.$iCounter.'" dataid="'.$iCounter.'" onchange="this.previousSibling.previousSibling.value = this.options[this.selectedIndex].value  + \': \' + this.previousSibling.value">'.$options.'</select>'
                .'<input type="button" value="Скопировать в буфер" onclick="copyTextToClipboard(this.previousSibling.previousSibling.previousSibling.value,this);return false;" />'
                .'<input type="button" value="x" onclick="if(this.parentNode.parentNode.previousSibling || (this.parentNode.parentNode.nextSibling && this.parentNode.parentNode.nextSibling.nextSibling)) this.parentNode.parentNode.remove();return false;" />';


            return $sreturn;
        }

        public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
        {
            $arPropertyFields = array(
                "HIDE" => array("ROW_COUNT", "COL_COUNT", "WITH_DESCRIPTION"),
            );
            return '';
        }
    }
}

AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertyChanges', 'GetUserTypeDescription'));

if(!class_exists('CIBlockPropertyText')){
    class CIBlockPropertyText extends CIBlockPropertyChanges
    {
        const USER_TYPE = 'ElementText';

        public static function GetUserTypeDescription()
        {
            return array(
                "PROPERTY_TYPE" => Bitrix\Iblock\PropertyTable::TYPE_STRING,
                "USER_TYPE" => "ElementText",
                "DESCRIPTION" => "Замены в detail (тест)",
                "GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
                "GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
                "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
                "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
            );
        }


        protected static function GetPropsList(){

            if(empty(static::$aPropsList)) {

                $dProps = CIBlockProperty::GetList(
                    Array('NAME' => 'ASC'),
                    Array("IBLOCK_ID" => 11)
                );

                if($dProps) {

                    while ($aProps = $dProps->GetNext()) {
                        {
                            static::$aPropsList[trim(mb_strtolower($aProps["CODE"]))] = trim($aProps["NAME"]);
                        }
                    }
                }
            }

            return static::$aPropsList;
        }

    }
}

AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertyText', 'GetUserTypeDescription'));

if(!class_exists('impelCIBlockElement') && class_exists('CIBlockElement')){

    class impelCIBlockElementProperty{

        private $aValues = array();

        public function setValues($aValues){
            $this->aValues = $aValues;
        }

        public function getNext(){

            $rValue = false;

            if(is_array($this->aValues) && (current($this->aValues) !== FALSE)){
                $rValue['VALUE'] = current($this->aValues);
                next($this->aValues);
            }

            return $rValue;

        }
    }

    class impelCIBlockElement extends CIBlockElement
    {

        public static function SetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUES, $FLAGS = array()) {

            foreach($PROPERTY_VALUES as $pKey => $pValues) {

                $pKey = trim($pKey);

                if(mb_stripos($pKey, 'SIMPLEREPLACE') === 0) {

                    if(is_array($pValues) && !empty($pValues)) {

                        $aValues = array();

                        foreach($pValues as $pValue) {
                            $aValues[] = isset($pValue['VALUE']) ? $pValue['VALUE'] : $pValue;
                        }

                        $PROPERTY_VALUES[$pKey] = array('VALUE' => join(',', $aValues));

                    } else {

                        $PROPERTY_VALUES[$pKey] = '';

                    }

                }

            }

            return parent::SetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUES, $FLAGS);

        }

        public static function GetElementSimpleReplace($IBLOCK_ID, $ELEMENT_ID, $aOrder = array(), $arFilter = Array()){

            $ioReturn = new impelCIBlockElementProperty;

            $rDb = parent::GetProperty($IBLOCK_ID, $ELEMENT_ID, $aOrder, $arFilter);

            $aValues = false;


            if ($rDb) {

                while ($aDb = $rDb->GetNext()) {

                    if (isset($aDb['VALUE']) && !empty($aDb['VALUE'])) {
                        $aValues = $aDb['VALUE'];
                    }

                }

            }

            $ioReturn->setValues($aValues);

            return $ioReturn;

        }

        public static function GetListSimpleReplace($arOrder = array("SORT" => "ASC"), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array()) {

            static $included;
            global $argv,$argc;

            foreach($arFilter as $aCode => $aValue) {

                if(mb_stripos($aCode, 'PROPERTY_SIMPLEREPLACE') === 0) {

                    if (!is_array($included)) {
                        $included = [];
                    }

                    $bFound = false;
                    unset($arFilter[$aCode]);

                    $aCode = str_ireplace('PROPERTY_', '', $aCode);

                    $aValue = !empty($aValue) && !is_array($aValue) ? array($aValue) : $aValue;

                    if ($argv && isset($argv[0])) {
                        if (!(isset($included[$aCode]) && !empty($included[$aCode]))
                            && file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/map_restore_products_'.mb_strtolower($aCode).'.txt')
                            && (filesize($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/map_restore_products_'.mb_strtolower($aCode).'.txt') > 0)
                        ) {

                            $content = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/map_restore_products_'.mb_strtolower($aCode).'.txt');
                            $aMaps = @unserialize($content);

                            if (isset($aMaps)
                                && !empty($aMaps)
                                && isset($aMaps[$aCode])
                                && !empty($aMaps[$aCode])
                            ) {

                                $included[$aCode] = $aMaps[$aCode];

                            }

                        }

                    }

                    if (!(isset($included[$aCode]) && !empty($included[$aCode]))
                        && file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/map_restore_products_'.mb_strtolower($aCode).'.php')
                        && (filesize($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/map_restore_products_'.mb_strtolower($aCode).'.php') > 0)
                    ) {

                        @require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/map_restore_products_' . mb_strtolower($aCode) . '.php';

                        if (isset($aMaps)
                            && !empty($aMaps)
                            && isset($aMaps[$aCode])
                            && !empty($aMaps[$aCode])
                        ) {

                            $included[$aCode] = $aMaps[$aCode];

                        }

                    }

                    if (isset($included[$aCode])) {

                        foreach($aValue as $filterValue) {

                            if(isset($included[$aCode][$filterValue])) {

                                if(!isset($arFilter['ID'])) {
                                    $arFilter['ID'] = array();
                                }

                                foreach($included[$aCode][$filterValue] as $iModelId) {
                                    $arFilter['ID'][$iModelId] = $iModelId;
                                    $bFound = true;
                                }

                            }

                        }

                    }

                    if(!$bFound){
                        return false;
                    }

                }

            }

            if(isset($arFilter['ID']) && !empty($arFilter['ID'])){
                $arFilter['ID'] = array_map('trim',$arFilter['ID']);
                $arFilter['ID'] = array_map('intval',$arFilter['ID']);
                $arFilter['ID'] = array_values($arFilter['ID']);
                $arFilter['ID'] = array_unique($arFilter['ID']);
            }

            return parent::GetList($arOrder,$arFilter,$arGroupBy,$arNavStartParams,$arSelectFields);

        }

        public static function checkSimpleReplace($arFilter){
            $bSimpleReplace = false;

            foreach($arFilter as $aCode => $aValue) { 
                if(is_string($aValue)) {
                    $aValue = trim($aValue);
                }
                if(is_string($aCode)) {
                    $aCode = trim($aCode);
                }
                if($aCode == 'CODE' && mb_stripos($aValue,'SIMPLEREPLACE') === 0) {
                    $bSimpleReplace = true;
                }

                if($aCode != 'CODE' && mb_stripos($aCode,'PROPERTY_SIMPLEREPLACE') === 0) {
                    $bSimpleReplace = true;
                }

            }

            return $bSimpleReplace;

        }

        public static function GetList($arOrder = array("SORT" => "ASC"), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
        {
            if(!static::checkSimpleReplace($arFilter)){
                return parent::GetList($arOrder,$arFilter,$arGroupBy,$arNavStartParams,$arSelectFields);
            } else {
                return static::GetListSimpleReplace($arOrder,$arFilter,$arGroupBy,$arNavStartParams,$arSelectFields);
            }

        }

        // public static function GetProperty($IBLOCK_ID, $ELEMENT_ID, $aOrder = array(), $arFilter = Array())
        // {

        //     if(!static::checkSimpleReplace($arFilter)) {
        //         return parent::GetProperty($IBLOCK_ID, $ELEMENT_ID, $aOrder, $arFilter);
        //     } else {
        //         return static::GetElementSimpleReplace($IBLOCK_ID, $ELEMENT_ID, $aOrder, $arFilter);
        //     }

        // }

        public static function GetProperty($IBLOCK_ID, $ELEMENT_ID, $by = 'sort', $order = 'asc', $arFilter = [])
        {
            if (is_array($by)) {
                $aOrder = $by;
            }

            if (is_array($order)) {
                $arFilter = $order;
            }

            if(!static::checkSimpleReplace($arFilter)) {
                return parent::GetProperty($IBLOCK_ID, $ELEMENT_ID, $aOrder, $arFilter);
            } else {
                return static::GetElementSimpleReplace($IBLOCK_ID, $ELEMENT_ID, $aOrder, $arFilter);
            }

        }

    }

}

/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage iblock
 */

class FacetMod extends \Bitrix\Iblock\PropertyIndex\Facet
{

    /**
     * Returns filter join with index tables.
     * <p>
     * $filter parameters same as for CIBlockElement::getList
     * <p>
     * $facetTypes allows to get only "checkboxes" or "prices" and such.
     *
     * @param array $filter Filter to apply additionally to filter elements.
     * @param array $facetTypes Which facet types will be used.
     * @param integer $facetId Which facet category filter should not be applied.
     *
     * @return \Bitrix\Main\DB\Result|false
     */
    public function query(array $filter, array $facetTypes = array(), $facetId = 0)
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();

        $facetFilter = $this->getFacetFilter($facetTypes);
        if (!$facetFilter)
        {
            return false;
        }

        if ($filter)
        {
            $filter["IBLOCK_ID"] = $this->iblockId;

            if(isset($GLOBALS["arrFilter"]) && is_array($GLOBALS["arrFilter"])){

                $filter = array_merge((array)$filter,(array)$GLOBALS["arrFilter"]);
                unset($filter['OFFERS']);
                
                if (is_array($GLOBALS["arrFilter"]["OFFERS"]))
                    $filter["ID"] = CIBlockElement::SubQuery('PROPERTY_CML2_LINK', $GLOBALS["arrFilter"]["OFFERS"]);
            }

            $element = new \CIBlockElement;
            $element->strField = "ID";
            $element->prepareSql(array("ID"), $filter, false, false);
            $elementFrom = $element->sFrom;
            $elementWhere = $element->sWhere;
        }
        else
        {
            $elementFrom = "";
            $elementWhere = "";
        }

        $facets = array();
        if ($facetId)
        {
            $facets[] = array(
                "where" => $this->getWhere($facetId),
                "facet" => array($facetId),
            );
        }
        else
        {
            foreach ($facetFilter as $facetId)
            {
                $where = $this->getWhere($facetId);
                $found = false;

                foreach ($facets as $i => $facetWhereAndFacets)
                {
                    if ($facetWhereAndFacets["where"] == $where)
                    {
                        $facets[$i]["facet"][] = $facetId;
                        $found = true;
                        break;
                    }
                }

                if (!$found)
                {
                    $facets[] = array(
                        "where" => $where,
                        "facet" => array($facetId),
                    );
                }
            }
        }

        $sqlUnion = array();
        foreach ($facets as $facetWhereAndFacets)
        {
            $where = $facetWhereAndFacets["where"];
            $facetFilter = $facetWhereAndFacets["facet"];

            $sqlSearch = array("1=1");

            if (empty($where))
            {
                $sqlUnion[] = "
					SELECT
						F.FACET_ID
						,F.VALUE
						,MIN(F.VALUE_NUM) MIN_VALUE_NUM
						,MAX(F.VALUE_NUM) MAX_VALUE_NUM
						".($connection instanceof \Bitrix\Main\DB\MysqlCommonConnection
                        ?",MAX(case when LOCATE('.', F.VALUE_NUM) > 0 then LENGTH(SUBSTRING_INDEX(F.VALUE_NUM, '.', -1)) else 0 end)"
                        :",MAX(".$sqlHelper->getLengthFunction("ABS(F.VALUE_NUM) - FLOOR(ABS(F.VALUE_NUM))")."+1-".$sqlHelper->getLengthFunction("0.1").")"
                    )." VALUE_FRAC_LEN
						,COUNT(DISTINCT F.ELEMENT_ID) ELEMENT_COUNT
					FROM
						".($elementFrom
                        ?$elementFrom."
							INNER JOIN ".$this->storage->getTableName()." F ON BE.ID = F.ELEMENT_ID"
                        :$this->storage->getTableName()." F"
                    )."
					WHERE
						F.SECTION_ID = ".$this->sectionId."
						and F.FACET_ID in (".implode(",", $facetFilter).")
						".$elementWhere."
					GROUP BY
						F.FACET_ID, F.VALUE
				";
                continue;
            }
            elseif (count($where) == 1)
            {
                $fcJoin = "INNER JOIN ".$this->storage->getTableName()." FC on FC.ELEMENT_ID = BE.ID";
                foreach ($where as $facetWhere)
                {
                    $sqlWhere = $this->whereToSql($facetWhere, "FC");
                    if ($sqlWhere)
                        $sqlSearch[] = $sqlWhere;
                }
            }
            elseif (count($where) <= 5)
            {
                $subJoin = "";
                $subWhere = "";
                $i = 0;
                foreach ($where as $facetWhere)
                {
                    if ($i == 0)
                        $subJoin .= "FROM ".$this->storage->getTableName()." FC$i\n";
                    else
                        $subJoin .= "INNER JOIN ".$this->storage->getTableName()." FC$i ON FC$i.ELEMENT_ID = FC0.ELEMENT_ID\n";

                    $sqlWhere = $this->whereToSql($facetWhere, "FC$i");
                    if ($sqlWhere)
                    {
                        if ($subWhere)
                            $subWhere .= "\nAND ".$sqlWhere;
                        else
                            $subWhere .= $sqlWhere;
                    }

                    $i++;
                }
                $fcJoin = "
					INNER JOIN (
						SELECT FC0.ELEMENT_ID
						$subJoin
						WHERE
						$subWhere
					) FC on FC.ELEMENT_ID = BE.ID
				";
            }
            else
            {
                $condition = array();
                foreach ($where as $facetWhere)
                {
                    $sqlWhere = $this->whereToSql($facetWhere, "FC0");
                    if ($sqlWhere)
                        $condition[] = $sqlWhere;
                }
                $fcJoin = "
						INNER JOIN (
							SELECT FC0.ELEMENT_ID
							FROM ".$this->storage->getTableName()." FC0
							WHERE FC0.SECTION_ID = ".$this->sectionId."
							AND (
							(".implode(")OR(", $condition).")
							)
						GROUP BY FC0.ELEMENT_ID
						HAVING count(DISTINCT FC0.FACET_ID) = ".count($condition)."
						) FC on FC.ELEMENT_ID = BE.ID
					";
            }

            $sqlUnion[] = "
				SELECT
					F.FACET_ID
					,F.VALUE
					,MIN(F.VALUE_NUM) MIN_VALUE_NUM
					,MAX(F.VALUE_NUM) MAX_VALUE_NUM
					".($connection instanceof \Bitrix\Main\DB\MysqlCommonConnection
                    ?",MAX(case when LOCATE('.', F.VALUE_NUM) > 0 then LENGTH(SUBSTRING_INDEX(F.VALUE_NUM, '.', -1)) else 0 end)"
                    :",MAX(".$sqlHelper->getLengthFunction("ABS(F.VALUE_NUM) - FLOOR(ABS(F.VALUE_NUM))")."+1-".$sqlHelper->getLengthFunction("0.1").")"
                )." VALUE_FRAC_LEN
					,COUNT(DISTINCT F.ELEMENT_ID) ELEMENT_COUNT
				FROM
					".$this->storage->getTableName()." F
					INNER JOIN (
						SELECT BE.ID
						FROM
							".($elementFrom
                    ?$elementFrom
                    :"b_iblock_element BE"
                )."
							".$fcJoin."
						WHERE ".implode(" AND ", $sqlSearch)."
						".$elementWhere."
					) E ON E.ID = F.ELEMENT_ID
				WHERE
					F.SECTION_ID = ".$this->sectionId."
					and F.FACET_ID in (".implode(",", $facetFilter).")
				GROUP BY
					F.FACET_ID, F.VALUE
			";
        }

        $result = $connection->query(implode("\nUNION ALL\n", $sqlUnion));

        return $result;
    }

}

if (class_exists('CCatalogProductProvider')) {

    class CCatalogProductProviderCustom extends CCatalogProductProvider{

        public static function OrderProduct($arParams)
        {

            $rewrite_fields			  = array();
            $arParams["CHECK_PRICE"]  = "N";
            if(isset($arParams['BASKET_ID']) && !empty($arParams['BASKET_ID'])){
                $arParams['BASKET_ID']= (int)$arParams['BASKET_ID'];
                $rewrite_fields		  = CSaleBasket::GetByID($arParams['BASKET_ID']);
                $rewrite_keys		  = array('PRICE','CURRENCY','DETAIL_PAGE_URL','NAME','CALLBACK_FUNC');

                foreach ($rewrite_fields as $key=>$value){
                    if(!in_array($key,$rewrite_keys)){
                        unset($rewrite_fields[$key]);
                    }
                }
            }

            $arResult = parent::GetProductData($arParams);

            if($arResult && is_array($arResult) && is_array($rewrite_fields)){
                $arResult 			  = array_merge($arResult,$rewrite_fields);
            }

            return $arResult;
        }

        public static function GetProductData($arParams)
        {

            $rewrite_fields			  = array();
            $arParams["CHECK_PRICE"]  = "N";
            if(isset($arParams['BASKET_ID']) && !empty($arParams['BASKET_ID'])){
                $arParams['BASKET_ID']= (int)$arParams['BASKET_ID'];
                $rewrite_fields		  = CSaleBasket::GetByID($arParams['BASKET_ID']);
                $rewrite_keys		  = array('PRICE','CURRENCY','DETAIL_PAGE_URL','NAME','CALLBACK_FUNC');

                foreach ($rewrite_fields as $key=>$value){
                    if(!in_array($key,$rewrite_keys)){
                        unset($rewrite_fields[$key]);
                    }
                }
            }

            $arResult = parent::GetProductData($arParams);

            if($arResult && is_array($arResult) && is_array($rewrite_fields)){
                $arResult 			  = array_merge($arResult,$rewrite_fields);
            }

            return $arResult;

        }


    }

}

if(!defined('MB_ARRAY_TO_CONVERT'))
    define('MB_ARRAY_TO_CONVERT','ь|,ъ|,а|a, б|b, в|v, г|g, д|d, е|e, ё|yo, з|z, и|i, й|j, к|k, л|l, м|m, н|n, о|o, п|p, р|r, с|s, т|t, у|u, ф|f, х|x, ъ|, ы|y, э|e, _|-,  |-, ж|zh, ц|c, ч|ch, ш|sh, щ|shh, ь|, ю|yu, я|ya, ї|yi, є|ye, ґ|g, ў|u'); //массив перекодировки, вида: б|b - где, б - буква для конвертации в транслит, разделитель |, b - буква, в которую будет перекодирован симнвол (английские буквы от a до z, можете указывать несколько символов до или после, после каждой строчки обязательно ставить , для указания следующей

if(!defined("defined_color_nameds")){
    define("defined_color_nameds","indianred|lightcoral|salmon|darksalmon|lightsalmon|crimson|red|firebrick|darkred|pink|lightpink|hotpink|deeppink|mediumvioletred|palevioletred|lightsalmon|coral|tomato|orangered|darkorange|orange|gold|yellow|lightyellow|lemonchiffon|lightgoldenrodyellow|papayawhip|moccasin|peachpuff|palegoldenrod|khaki|darkkhaki|lavender|thistle|plum|violet|orchid|fuchsia|magenta|mediumorchid|mediumpurple|blueviolet|darkviolet|darkorchid|darkmagenta|purple|indigo|slateblue|darkslateblue|cornsilk|blanchedalmond|bisque|navajowhite|wheat|burlywood|tan|rosybrown|sandybrown|goldenrod|darkgoldenrod|peru|chocolate|saddlebrown|sienna|brown|maroon|black|gray|silver|white|fuchsia|purple|red|maroon|yellow|olive|lime|green|aqua|teal|blue|navy|greenyellow|chartreuse|lawngreen|lime|limegreen|palegreen|lightgreen|mediumspringgreen|springgreen|mediumseagreen|seagreen|forestgreen|green|darkgreen|yellowgreen|olivedrab|olive|darkolivegreen|mediumaquamarine|darkseagreen|lightseagreen|darkcyan|teal|aqua|cyan|lightcyan|paleuoise|aquamarine|turquoise|mediumturquoise|darkturquoise|cadetblue|steelblue|lightsteelblue|powderblue|lightblue|skyblue|lightskyblue|deepskyblue|dodgerblue|cornflowerblue|mediumslateblue|royalblue|blue|mediumblue|darkblue|navy|midnightblue|white|snow|honeydew|mintcream|azure|aliceblue|ghostwhite|whitesmoke|seashell|beige|oldlace|floralwhite|ivory|antiquewhite|linen|lavenderblush|mistyrose|gainsboro");
};

if(!class_exists('newsListMainTemplateTools')){
    class newsListMainTemplateTools {

        function rgb2hex($rgb) {
            $hex = "#";
            $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
            $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
            $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

            return $hex; // returns the hex value including the number sign (#)
        }

        function prepareTitle($title) {

            $title = newsListMainTemplateTools::cleanText($title);
            $title = htmlspecialchars($title,ENT_QUOTES,LANG_CHARSET);
            return $title;
        }

        function hex2rgb($hex) {
            $hex = str_replace("#", "", $hex);

            if(mb_strlen($hex) == 3) {
                $r = hexdec(mb_substr($hex,0,1).mb_substr($hex,0,1));
                $g = hexdec(mb_substr($hex,1,1).mb_substr($hex,1,1));
                $b = hexdec(mb_substr($hex,2,1).mb_substr($hex,2,1));
            } else {
                $r = hexdec(mb_substr($hex,0,2));
                $g = hexdec(mb_substr($hex,2,2));
                $b = hexdec(mb_substr($hex,4,2));
            }
            $rgb = array($r, $g, $b);
            //return implode(",", $rgb); // returns the rgb values separated by commas
            return $rgb; // returns an array with the rgb values
        }

        function rectangleImage(
            $image,
            $width 							= "",
            $height 						= "",
            $url 							= "",
            $background_color 				= "",
            $watermark_file 				= "",
            $watermark_position 			= "BR",
            $image_ratio_no_zoom_out 		= false,
            $image_crop 					= false,
            $image_resize 					= false,
            $image_crop_position_h 			= false,
            $image_crop_position_v 			= false,
            $image_pre_crop 				= false,
            $image_text  					= false,
            $image_text_direction 			= false,
            $image_text_color 				= false,
            $image_text_opacity 			= false,
            $image_text_background 			= false,
            $image_text_background_opacity 	= false,
            $image_text_font 				= false,
            $image_text_x 					= false,
            $image_text_y 					= false,
            $image_text_position 			= false,
            $image_text_padding_x 			= false,
            $image_text_padding_y 			= false
        ){
            $image_crop = ($image_resize == 'CROP') ? true : $image_crop;
            $image_resize = ($image_crop == true) ?  'CROP' : $image_resize;

            $return												= $url;
            $return_uri											= "/upload/resize_cache/";
            $watermark											= $watermark_file;

            if(empty($width) && empty($height)) {
                $width							                = 256;
            }

            if(is_file($image)) {

                if(class_exists('Upload') && !is_writable($image)) {
                    @chmod($image,0777);
                };

                //$filesize											= filesize($image);

                if(class_exists('Upload') && is_writable($image)) { //&& $filesize){

                    $info										= array();
                    $info 										= pathinfo($image);

                    if(isset($info["extension"]) && in_array(mb_strtolower($info["extension"]),array('jpeg','jpg','gif','png'))){

                        $path	     								= $info['dirname'];
                        $image_name	 								= false;

                        $base_name									= isset($info['filename']) ? $info['filename'] : str_replace('.'.$info['extension'],'',$info['basename']);
                        $base_name				   					.= '_'.$filesize.'_'.$width.'_'.$height;

                        if(!empty($background_color)){
                            $base_name								.= preg_replace('~[^a-z0-9\-\_]~isu','',$background_color);
                        };

                        $is_wm										= false;

                        if(is_file($watermark) && is_readable($watermark)) {
                            $wSizes = getimagesize($watermark);
                            if(isset($wSizes[0]) && !empty($wSizes[0])
                                && isset($wSizes[1]) && !empty($wSizes[1])
                            ){
                                $base_name						.= '_w'.mb_strtolower($watermark_position);
                                $is_wm							= true;
                            }
                        }

                        if(!$is_wm) {
                            $watermark							= false;
                        }

                        $cache										= $_SERVER['DOCUMENT_ROOT'].$return_uri;

                        if(!is_dir($cache.'/impel_landing/')){
                            @mkdir($cache.'/impel_landing/',0777);
                        };

                        if(is_dir($cache.'/impel_landing/')){
                            $cache									.= 'impel_landing/';
                            $return_uri								.= 'impel_landing/';
                        };

                        $cache_exists								= true;

                        $image_extension							= '.'.$info["extension"];
                        $image_convert								= false;

                        if(!empty($background_color)){
                            $image_extension						= '.jpg';
                        };

                        $image_resize								= $image_resize === false ? 'RESIZE' : $image_resize;


                        switch($image_resize):
                            case 'STRETCH':
                                $base_name						.= '_st';
                                break;
                            case 'CROP':
                                $base_name						.= '_crop';
                                break;
                            case 'RESIZE':
                            default:
                                $base_name						.= '_rs';
                                break;
                        endswitch;


                        switch ($image_crop_position_h){
                            case 'L':
                            case 'R':
                                $base_name						.= '_cph'.$image_crop_position_h;
                                break;
                            default:
                                $base_name						.= '_cphC';
                                break;
                        }

                        switch ($image_crop_position_v){
                            case 'T':
                            case 'B':
                                $base_name						.= '_cpv'.$image_crop_position_v;
                                break;
                            default:
                                $base_name						.= '_cpvM';
                                break;
                        }

                        if($image_ratio_no_zoom_out){
                            $base_name								.= '_nz';
                        };


                        if($image_pre_crop){
                            $base_name								.= '_precrop';
                        };

                        if(!empty($image_text)){
                            $base_name								.= '_tv_'.md5($image_text);

                            if(!empty($image_text_direction)){
                                switch($image_text_direction):
                                    case 'V':
                                    case 'H':
                                        $base_name					.= '_td'.$image_text_direction;
                                        break;
                                    default:
                                        $base_name					.= '_tdH';
                                        break;
                                endswitch;
                            };

                            if(!empty($image_text_color)){
                                $base_name							.= '_tc'.preg_replace('~[^a-z0-9\-\_]~isu','',$image_text_color);
                            };

                            if(!empty($image_text_opacity)){
                                $image_text_opacity					= (int)$image_text_opacity;

                                if($image_text_opacity > 0 && $image_text_opacity < 101){
                                    $base_name						.= '_to'.$image_text_opacity;
                                };
                            };

                            if(!empty($image_text_background)){
                                $base_name							.= '_tb'.preg_replace('~[^a-z0-9\-\_]~isu','',$image_text_background);
                            };

                            if(!empty($image_text_background_opacity)){
                                $image_text_background_opacity		= (int)$image_text_background_opacity;

                                if($image_text_background_opacity > 0 && $image_text_background_opacity < 101){
                                    $base_name						.= '_tbo'.$image_text_background_opacity;
                                };
                            };

                            if(!empty($image_text_font)){
                                $image_text_font					= (int)$image_text_font;

                                if(in_array($image_text_font,array(1,2,3,4,5))){
                                    $base_name						.= '_tf'.$image_text_font;
                                };

                            };

                            if(!empty($image_text_x)){
                                $base_name							.= '_tx'.$image_text_x;
                            };

                            if(!empty($image_text_y)){
                                $base_name							.= '_ty'.$image_text_y;
                            };

                            if(!empty($image_text_padding_x)){
                                $base_name							.= '_tpx'.$image_text_padding_x;
                            };

                            if(!empty($image_text_padding_y)){
                                $base_name							.= '_tpy'.$image_text_padding_y;
                            };

                            if(!empty($image_text_position)){
                                if(in_array($image_text_position,array('L','C','R'))){
                                    $base_name						.= '_tp'.$image_text_position;
                                };
                            };

                        };

                        $base_name					= mb_strtolower($base_name);

                        if(!is_file($cache.$base_name.$image_extension)){
                            $cache_exists			= false;
                        };

                        $cache_imagesize			= array();

                        /* if(is_file($cache.$base_name.$image_extension)){
                            $cache_filesieze		= filesize($cache.$base_name.$image_extension);

                            if(!$cache_filesieze){
                                $cache_exists		= false;
                            } else {
                                $cache_imagesize	= getimagesize($cache.$base_name.$image_extension);
                                if(empty($cache_imagesize[0]) || empty($cache_imagesize[1])){
                                    $cache_exists	= false;
                                }
                            };
                        }; */

                        if($cache_exists){
                            $return				= $return_uri.$base_name.$image_extension;

                        } elseif(!$cache_exists){

                            /* $sizes										= array();
                            $sizes 										= getimagesize($image);

                            if(isset($sizes[0]) 						&& !empty($sizes[0])
                            && isset($sizes[1]) 						&& !empty($sizes[1])
                            ){ */

                            $image_convert								= false;

                            if(!empty($background_color)){
                                $image_convert			  				= true;
                            };

                            $handle	 									= new Upload($image);

                            if(!empty($width))
                                $handle->image_x	 						= $width;

                            if(!empty($height))
                                $handle->image_y	 						= $height;

                            $handle->image_resize						= true;


                            switch($image_resize):
                                case 'STRETCH':
                                    $handle->image_ratio			= false;
                                    $handle->image_ratio_y			= false;
                                    $handle->image_ratio_x			= false;

                                    break;
                                case 'CROP':

                                    switch ($image_crop_position_h){
                                        case 'L':
                                        case 'R':
                                            break;
                                        default:
                                            $image_crop_position_h	= '';
                                            break;
                                    }

                                    switch ($image_crop_position_v){
                                        case 'T':
                                        case 'B':
                                            break;
                                        default:
                                            $image_crop_position_v	= '';
                                            break;
                                    }

                                    $image_crop						= $image_crop_position_v.$image_crop_position_h;
                                    $image_crop						= trim($image_crop);
                                    $image_crop						= empty($image_crop) ? true : $image_crop;

                                    $handle->image_ratio_crop		= $image_crop;


                                    break;
                                case 'RESIZE':
                                default:

                                    if(!empty($width) && !empty($height)){
                                        $handle->image_ratio		= true;
                                    } else if(!empty($width)){
                                        $handle->image_ratio_y		= true;
                                    } else if(!empty($height)){
                                        $handle->image_ratio_x		= true;
                                    };

                                    break;
                            endswitch;

                            if($image_pre_crop){
                                $handle->image_precrop					= $image_pre_crop;
                            };

                            if(!empty($image_text)){
                                $handle->image_text						= $image_text;

                                if(!empty($image_text_direction)){
                                    switch($image_text_direction):
                                        case 'V':
                                        case 'H':
                                            $handle->image_text_direction = $image_text_direction;
                                            break;
                                        default:
                                            $handle->image_text_direction = 'H';
                                            break;
                                    endswitch;
                                };

                                if(!empty($image_text_color)){
                                    $handle->image_text_color			= $image_text_color;
                                };

                                if(!empty($image_text_opacity)){
                                    $image_text_opacity					= (int)$image_text_opacity;

                                    if($image_text_opacity > 0 && $image_text_opacity < 101){
                                        $handle->image_text_opacity		= $image_text_opacity;
                                    };
                                };

                                if(!empty($image_text_background)){
                                    $handle->image_text_background		= $image_text_background;
                                };

                                if(!empty($image_text_background_opacity)){
                                    $image_text_background_opacity		= (int)$image_text_background_opacity;

                                    if($image_text_background_opacity > 0 && $image_text_background_opacity < 101){

                                        $handle->image_text_background_opacity = $image_text_background_opacity;

                                    };
                                };

                                if(!empty($image_text_font)){
                                    $image_text_font					= (int)$image_text_font;

                                    if(in_array($image_text_font,array(1,2,3,4,5))){
                                        $handle->image_text_font		= $image_text_font;
                                    };

                                };

                                if(!empty($image_text_x)){
                                    $handle->image_text_x 				= (int)$image_text_x;
                                };

                                if(!empty($image_text_y)){
                                    $handle->image_text_y 				= (int)$image_text_y;
                                };

                                if(!empty($image_text_padding_x)){
                                    $handle->image_text_padding_x		= (int)$image_text_padding_x;
                                };

                                if(!empty($image_text_padding_y)){
                                    $handle->image_text_padding_y		= (int)$image_text_padding_y;
                                };

                                if(!empty($image_text_position)){
                                    if(in_array($image_text_position,array('L','C','R'))){
                                        $handle->image_text_position	= $image_text_position;
                                    };
                                };

                            };

                            $handle->image_ratio_no_zoom_in 			= $image_ratio_no_zoom_out;
                            $handle->jpeg_quality 						= 99;
                            $handle->file_new_name_body 				= $base_name;

                            $handle->file_auto_rename 					= false;
                            $handle->file_overwrite 					= true;

                            if($image_convert){
                                $handle->image_convert					= 'jpg';
                            };

                            if(!empty($background_color)){

                                $handle->image_background_color			= $background_color;

                                switch ($image_crop_position_h){
                                    case 'L':
                                    case 'R':
                                        break;
                                    default:
                                        $image_crop_position_h			= '';
                                        break;
                                }

                                switch ($image_crop_position_v){
                                    case 'T':
                                    case 'B':
                                        break;
                                    default:
                                        $image_crop_position_v			= '';
                                        break;
                                }

                                $image_fill								= $image_crop_position_v.$image_crop_position_h;
                                $image_fill								= trim($image_fill);
                                $image_fill								= empty($image_fill) ? true : $image_fill;

                                $handle->image_ratio_fill				= $image_fill;
                                $handle->image_convert					= 'jpg';
                            };

                            if($watermark
                                && is_file($watermark)
                                && is_readable($watermark)){
                                $handle->image_watermark 			= $watermark;
                                $handle->image_watermark_position 	= $watermark_position;
                            }


                            $handle->allowed 							= array('image/*');
                            $handle->Process($cache);



                            if($handle->processed){ //&& is_file($cache.$base_name.$image_extension)){
                                /* $imagesize				= getimagesize($cache.$base_name.$image_extension);
                                   if(isset($imagesize[0]) && !empty($imagesize[0])
                                   && isset($imagesize[1]) && !empty($imagesize[1])
                                   ){ */

                                $return				= $return_uri.$base_name.$image_extension;

                                /*}*/
                            }

                            /*}*/

                        }

                    }
                }

            }

            return $return;
        }

        function checkColor(&$color){

            $defined_color_nameds	= explode("|",defined_color_nameds);


            if(!empty($color)
                && mb_stripos($color,"rgb(") === false
                && mb_stripos($color,"rgba(") === false
                && !in_array(mb_strtolower($color),$defined_color_nameds)
            ){

                if(mb_strlen($color) 	== 4){
                    $color		 	= str_ireplace("#", "", $color);
                };

                if(mb_strlen($color) 	== 3){
                    $color 			.= $color;
                };

                if(mb_strlen($color) 	== 6 && $color[0] != "#"){
                    $color 			= "#".$color;
                };

                if(!(mb_strlen($color) 	== 7)){
                    $color 		= mb_substr($color,0,7);
                };

            };


        }


        function CleanText($text,$substr_tags = true, $remove_qoutes = true) {

            $text 					= str_ireplace("\r\n",' ', $text);
            $text 					= str_ireplace("\r",' ', $text);
            $text 					= str_ireplace("\n",' ', $text);
            $text 					= str_ireplace("\t",' ', $text);
            $text 					= str_ireplace("\p",' ', $text);
            $text 					= str_ireplace("\b",' ', $text);


            $regex 					= "~<noindex[^>]*?>.*?</noindex>~si";
            $text 					= preg_replace($regex, ' ', $text);


            $regex 					= "~<head>.*?</head>~si";
            $text 					= preg_replace($regex, ' ', $text);


            $regex 					= "~<script[^>]*?>.*?</script>~si";
            $text 					= preg_replace($regex, ' ', $text);

            $regex 					= "~<style[^>]*?>.*?</style>~si";
            $text 					= preg_replace($regex, ' ', $text);

            $regex 					= "~<noscript[^>]*?>.*?</noscript>~si";
            $text 					= preg_replace($regex, ' ', $text);

            $regex 					= "~ ~si";
            $text 					= preg_replace($regex, ' ', $text);


            if($substr_tags){


                $regex 				= "~<table[^>]*?>.*?</table>~si";
                $text 				= preg_replace($regex, ' ', $text);

                $text 				= html_entity_decode($text,ENT_QUOTES);

                $text 				= strip_tags($text);

                if($remove_qoutes){
                    $text 				= str_ireplace("\"", "", $text);
                    $text 				= str_ireplace('\'', "", $text);
                };

            };

            $text 					= str_ireplace("/**/",'',$text);

            $regex 					= '~( )+?( )+~';
            $text  					= preg_replace($regex, ' ', $text);

            return trim($text);
        }


        function mb_substr($description,$maxlength = 0, $clean_text = true){

            if(!empty($maxlength)){

                $description			= trim($description);


                $description			= newsListMainTemplateTools::cleanText($description,$clean_text);


                if(LANG_CHARSET 		== 'UTF-8'
                    && ini_get('mbstring.func_overload') != 2){
                    $substr 			= 'mb_substr';
                    $strlen 			= 'mb_strlen';
                    $strrpos			= 'mb_strrpos';
                } else {
                    $substr 			= 'substr';
                    $strlen 			= 'strlen';
                    $strrpos			= 'strrpos';
                }

                $maxlength				= (int)$maxlength;
                $description			= (string)$description;

                if($maxlength > 0 && $strlen($description) > $maxlength ){
                    $description 		= $substr($description,0,$maxlength);

                    $max				= array();
                    $max[]				= (int)$strrpos($description," ");
                    $max[]				= (int)$strrpos($description,"!");
                    $max[]				= (int)$strrpos($description,"?");
                    $max[]				= (int)$strrpos($description,",");
                    $max[]				= (int)$strrpos($description,".");
                    $max[]				= (int)$strrpos($description,"-");
                    $max[]				= (int)$strrpos($description,"+");

                    $max				= max($max);

                    if($max > 0){
                        $description	= $substr($description,0,$max);
                    }

                }

            }

            return $description;
        }

        function resort($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        }

        static function translit($st, $params = array()){
            $convert_array				= array();


            if(!isset($params["max_len"])){
                $params["max_len"]		= 100;
            }

            if(!isset($params["change_case"])){
                $params["change_case"]	= "L";
            }

            if(!isset($params["replace_space"])){
                $params["replace_space"]= "_";
            }

            if(!isset($params["replace_other"])){
                $params["replace_other"]= "_";
            }

            if(!isset($params["delete_repeat_replace"])){
                $params["delete_repeat_replace"]= true;
            }

            $strings 					= array();
            $values  					= array();


            if(function_exists('mb_http_input'))
                mb_internal_encoding('UTF-8');

            if(function_exists('mb_http_output'))
               mb_internal_encoding('UTF-8');

            if(function_exists('mb_internal_encoding'))
                mb_internal_encoding("UTF-8");

            $convert_array				= MB_ARRAY_TO_CONVERT;



            $convert 				= array();
            $convert 				= MB_ARRAY_TO_CONVERT;


            if(mb_strpos($convert,',') !== false){
                $strings = explode(',',$convert);

                foreach ($strings as $key => $value){
                    $value				=   trim($value);


                    if(mb_strpos($value,'|') !== false){
                        $from 			= '';
                        $to 			= '';

                        list($from,$to) = explode('|',$value);

                        $from			=  trim($from);
                        $to				=  trim($to);

                        if(!empty($from)){
                            $values[$from] = $to;

                        }

                    }


                }
            }

            $convert_array			= $values;

            if(!sizeof($convert_array)){
                $convert_array		= false;
            }

            unset($values);

            if($convert_array === false || $st === false || !preg_match('~[^a-z0-9\_\-]~',$st)){
                return $st;
            }


            $st			=   trim($st);
            $st			=   mb_strtolower($st);


            if(mb_strlen($st) > $params["max_len"]){
                $st 	= mb_substr($st,0,$params["max_len"]);

                $pos 	= array();

                $pos[] = (int)mb_strpos($st,' ');
                $pos[] = (int)mb_strpos($st,',');
                $pos[] = (int)mb_strpos($st,'.');
                $pos[] = (int)mb_strpos($st,'?');
                $pos[] = (int)mb_strpos($st,',');
                $pos[] = (int)mb_strpos($st,':');
                $pos[] = (int)mb_strpos($st,';');

                $max   = max($pos);

                if($max){
                    $st = mb_substr($st,0,$max);
                }

            }


            $return 	= 	'';


            $return		= 	newsListMainTemplateTools::mb_strtr($st,$convert_array);




            $return 	= 	mb_strlen($return) > $params["max_len"] ? mb_substr($return, 0, $params["max_len"]) : $return;

            if($params["change_case"] == "L"){
                $return= mb_strtolower($return);
            } else {
                $return= mb_strtoupper($return);
            }

            $return			=   preg_replace('~[^a-z0-9'.preg_quote($params["replace_other"],'~').''.preg_quote($params["replace_space"],'~').']~i',$params["replace_other"],$return);

            if($params["delete_repeat_replace"]){
                $return		=   preg_replace('~(['.preg_quote($params["replace_space"],'~').'])?(['.preg_quote($params["replace_space"],'~').']+)~',$params["replace_space"],$return);
            };

            if(mb_strlen($return)){
                $return		=   $return[mb_strlen($return)-1] == $params["replace_other"] ? (mb_substr($return,0,-1)) : $return;
            }

            if(mb_strlen($return)){
                $return		=   $return[0] == $params["replace_other"] ? (mb_substr($return,1,mb_strlen($return))) : $return;
            }

            if(mb_strlen($return)){
                $return		=   $return[mb_strlen($return)-1] == $params["replace_space"] ? (mb_substr($return,0,-1)) : $return;
            }

            if(mb_strlen($return)){
                $return		=   $return[0] == $params["replace_space"] ? (mb_substr($return,1,mb_strlen($return))) : $return;
            }

            return $return;
        }


        function mb_strtr($str, $from, $to = '') {


            if(function_exists('mb_http_input'))
                mb_http_input('UTF-8');

            if(function_exists('mb_http_output'))
                mb_http_output('UTF-8');

            if(function_exists('mb_internal_encoding'))
                mb_internal_encoding("UTF-8");


            if(empty($str) || empty($from)){
                return $str;
            }

            if(is_array($from)){

                foreach ($from as $key => $value){
                    $keys = array();
                    $values = array();
                    preg_match_all('/./u', $key, $keys);

                    foreach ($keys[0] as $next){

                        $str = preg_replace('~'.preg_quote($next,'~').'~ui',$value,$str);
                    }
                }

            } else if(isset($from) && is_string($from) && $from != ''
                && isset($to) && is_string($to) && $to != ''
            ) {

                $keys 			= array();
                $values 		= array();
                preg_match_all('/./u', $from, $keys);
                preg_match_all('/./u', $to, $values);
                $mapping 		= array_combine($keys[0], $values[0]);
                $str			= strtr($str, $mapping);
            }





            return $str;
        }

        function getURICurrentPath($current_uri){
            $root_path				=  $_SERVER['DOCUMENT_ROOT'];

            $current_uri			= str_replace('\\','/',$current_uri);
            $root_path				= str_replace('\\','/',$root_path);

            if(mb_stripos($current_uri,$root_path) !== false){
                $current_uri		= str_ireplace($root_path,'',$current_uri);
            } else {
                $current_uri		= preg_replace('~^.*?/bitrix/~is','/bitrix/',$current_uri);
            };

            $current_uri			= $current_uri[0] == '/' ? $current_uri : '/'.$current_uri;
            $current_uri			= $current_uri[mb_strlen($current_uri)-1] == '/' ? $current_uri : $current_uri.'/';


            return $current_uri;
        }

        function getCacheURI($id, $file_name = 'scripts', $inline_scripts= '', $file_type = 'js', $prefix = ''){

            $dir_name						= md5($inline_scripts);
            $link_file						= '';

            if(!empty($id) && !empty($inline_scripts)):

                $check_dir  			= '/bitrix/cache/'.$file_type.'/';
                $check_path 			= $_SERVER['DOCUMENT_ROOT'].$check_dir;

                if(!is_dir($check_path)):
                    @mkdir($check_path,0777);
                endif;

                if(is_dir($check_path) && !is_writable($check_path)):
                    @chmod($check_path,0777);
                endif;

                $check_dir  			.= SITE_ID.'/';
                $check_path 			= $_SERVER['DOCUMENT_ROOT'].$check_dir;

                if(!is_dir($check_path)):
                    @mkdir($check_path,0777);
                endif;

                if(is_dir($check_path) && !is_writable($check_path)):
                    @chmod($check_path,0777);
                endif;

                $check_dir  			.= 'impel/';
                $check_path 			= $_SERVER['DOCUMENT_ROOT'].$check_dir;

                if(!is_dir($check_path)):
                    @mkdir($check_path,0777);
                endif;

                if(is_dir($check_path) && !is_writable($check_path)):
                    @chmod($check_path,0777);
                endif;

                $check_dir  			.= $id.(!empty($prefix) ? ('_'.preg_replace('~[^0-9a-z\-_]+~i','',$prefix)) : '').'/';
                $check_path 			= $_SERVER['DOCUMENT_ROOT'].$check_dir;

                if(!is_dir($check_path)):
                    @mkdir($check_path,0777);
                endif;

                if(is_dir($check_path) && !is_writable($check_path)):
                    @chmod($check_path,0777);
                endif;

                $check_dir  			.= $dir_name.'/';
                $check_path 			= $_SERVER['DOCUMENT_ROOT'].$check_dir;

                if(!is_dir($check_path)):
                    @mkdir($check_path,0777);
                endif;

                if(is_dir($check_path) && !is_writable($check_path)):
                    @chmod($check_path,0777);
                endif;

                if(is_dir($check_path) && is_writable($check_path)):

                    if(
                        file_exists($check_path.$file_name.'.'.$file_type)
                    ):

                        if(!is_readable($check_path.$file_name.'.'.$file_type)):
                            @chmod($check_path.$file_name.'.'.$file_type,0777);
                        endif;

                        $link_file			= $check_dir.$file_name.'.'.$file_type;

                    else:


                        file_put_contents($check_path.$file_name.'.'.$file_type, $inline_scripts);

                        if(is_file($check_path.$file_name.'.'.$file_type)
                            && !is_readable($check_path.$file_name.'.'.$file_type)):
                            @chmod($check_path.$file_name.'.'.$file_type,0777);
                        endif;

                        if(is_file($check_path.$file_name.'.'.$file_type)
                            && is_readable($check_path.$file_name.'.'.$file_type)):
                            $link_file 	= $check_dir.$file_name.'.'.$file_type;
                        endif;

                    endif;
                endif;

            endif;

            return $link_file;

        }


        function getListElementsById($arParams){

            global $USER;

            $arResult													= array();
            $only_news_id 												= (string)$arParams['ONLY_NEWS_ID'];
            $arTmpResultID												= array();
            $arTmpResultCODE											= array();

            if(!empty($only_news_id)):
                $only_news_id											= (mb_strpos($only_news_id,',') === false) ? (array((string)$only_news_id)) : (explode(',',$only_news_id));
                $only_news_id											= array_unique($only_news_id);
                $only_news_id											= array_filter($only_news_id);

                if(!empty($only_news_id)):


                    foreach($only_news_id		as $arItem):

                        if(preg_match('~^[0-9]+?$~',$arItem)):
                            $arTmpResultID[]							= $arItem;
                        elseif(preg_match('~^[a-z0-9_\-]*$~i',$arItem)):
                            $arTmpResultID[]							= $arItem;
                        endif;

                    endforeach;

                endif;

            endif;


            if(empty($arTmpResultID)) return $arResult;



            if(!isset($arParams["CACHE_TIME"]))
                $arParams["CACHE_TIME"] 								= 36000000;


            $arParams["IBLOCK_TYPE"] 									= trim($arParams["IBLOCK_TYPE"]);
            if(mb_strlen($arParams["IBLOCK_TYPE"])							<=0)
                $arParams["IBLOCK_TYPE"] 								= "news";
            $arParams["IBLOCK_ID"] 										= trim($arParams["IBLOCK_ID"]);
            $arParams["PARENT_SECTION"] 								= intval($arParams["PARENT_SECTION"]);
            $arParams["INCLUDE_SUBSECTIONS"] 							= $arParams["INCLUDE_SUBSECTIONS"]!="N";

            $arParams["SORT_BY1"] 										= trim($arParams["SORT_BY1"]);
            if(mb_strlen($arParams["SORT_BY1"])							<=0)
                $arParams["SORT_BY1"] 									= "ACTIVE_FROM";
            if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
                $arParams["SORT_ORDER1"]								= "DESC";

            if(mb_strlen($arParams["SORT_BY2"])							<=0)
                $arParams["SORT_BY2"] 									= "SORT";
            if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
                $arParams["SORT_ORDER2"]								= "ASC";

            if(mb_strlen($arParams["FILTER_NAME"])							<=0 ||
                !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
            {
                $arrFilter 												= array();
            }
            else
            {
                $arrFilter 												= $GLOBALS[$arParams["FILTER_NAME"]];
                if(!is_array($arrFilter))
                    $arrFilter 											= array();
            }


            $arParams["CHECK_DATES"] 									= $arParams["CHECK_DATES"]!="N";

            if(!is_array($arParams["FIELD_CODE"]))
                $arParams["FIELD_CODE"] 								= array();
            foreach($arParams["FIELD_CODE"] 							as $key=>$val)
                if(!$val)
                    unset($arParams["FIELD_CODE"][$key]);

            if(!is_array($arParams["PROPERTY_CODE"]))
                $arParams["PROPERTY_CODE"] 								= array();
            foreach($arParams["PROPERTY_CODE"] 							as $key=>$val)
                if($val														==="")
                    unset($arParams["PROPERTY_CODE"][$key]);

            $arParams["DETAIL_URL"]										= trim($arParams["DETAIL_URL"]);

            $arParams["NEWS_COUNT"] 									= intval($arParams["NEWS_COUNT"]);
            if($arParams["NEWS_COUNT"]									<=0)
                $arParams["NEWS_COUNT"] 								= 20;

            $arParams["CACHE_FILTER"] 									= $arParams["CACHE_FILTER"]=="Y";
            if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
                $arParams["CACHE_TIME"] 								= 0;

            $arParams["SET_TITLE"] 										= $arParams["SET_TITLE"]!="N";
            $arParams["SET_BROWSER_TITLE"] 								= (isset($arParams["SET_BROWSER_TITLE"]) && $arParams["SET_BROWSER_TITLE"] === 'N' ? 'N' : 'Y');
            $arParams["SET_META_KEYWORDS"] 								= (isset($arParams["SET_META_KEYWORDS"]) && $arParams["SET_META_KEYWORDS"] === 'N' ? 'N' : 'Y');
            $arParams["SET_META_DESCRIPTION"] 							= (isset($arParams["SET_META_DESCRIPTION"]) && $arParams["SET_META_DESCRIPTION"] === 'N' ? 'N' : 'Y');
            $arParams["ADD_SECTIONS_CHAIN"] 							= $arParams["ADD_SECTIONS_CHAIN"]!="N"; //Turn on by default
            $arParams["INCLUDE_IBLOCK_INTO_CHAIN"] 						= $arParams["INCLUDE_IBLOCK_INTO_CHAIN"]!="N";
            $arParams["ACTIVE_DATE_FORMAT"] 							= trim($arParams["ACTIVE_DATE_FORMAT"]);
            if(mb_strlen($arParams["ACTIVE_DATE_FORMAT"])<=0)
                $arParams["ACTIVE_DATE_FORMAT"] 						= $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));
            $arParams["PREVIEW_TRUNCATE_LEN"] 							= intval($arParams["PREVIEW_TRUNCATE_LEN"]);
            $arParams["HIDE_LINK_WHEN_NO_DETAIL"] 						= $arParams["HIDE_LINK_WHEN_NO_DETAIL"]=="Y";

            $arParams["DISPLAY_TOP_PAGER"] 								= $arParams["DISPLAY_TOP_PAGER"]=="Y";
            $arParams["DISPLAY_BOTTOM_PAGER"] 							= $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
            $arParams["PAGER_TITLE"]									= trim($arParams["PAGER_TITLE"]);
            $arParams["PAGER_SHOW_ALWAYS"] 								= $arParams["PAGER_SHOW_ALWAYS"]=="Y";
            $arParams["PAGER_TEMPLATE"] 								= trim($arParams["PAGER_TEMPLATE"]);
            $arParams["PAGER_DESC_NUMBERING"] 							= $arParams["PAGER_DESC_NUMBERING"]=="Y";
            $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] 				= intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
            $arParams["PAGER_SHOW_ALL"] 								= $arParams["PAGER_SHOW_ALL"]=="Y";

            $minCount													= sizeof($arTmpResultID);
            $arParams["NEWS_COUNT"]										= $arParams["NEWS_COUNT"] < $minCount ? $minCount : $arParams["NEWS_COUNT"];

            if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
            {
                $arNavParams 											= array(
                    "nPageSize" 									=> $arParams["NEWS_COUNT"],
                    "bDescPageNumbering" 							=> $arParams["PAGER_DESC_NUMBERING"],
                    "bShowAll" 										=> $arParams["PAGER_SHOW_ALL"],
                );
                $arNavigation 											= CDBResult::GetNavParams($arNavParams);
                if($arNavigation["PAGEN"]								==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
                    $arParams["CACHE_TIME"] 							= $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
            }
            else
            {
                $arNavParams = array(
                    "nTopCount" 									=> $arParams["NEWS_COUNT"],
                    "bDescPageNumbering" 							=> $arParams["PAGER_DESC_NUMBERING"],
                );
                $arNavigation 											= false;
            }

            $eltsFilterID												= sizeof($arrFilter);
            $arrFilter[$eltsFilterID]['LOGIC']							= 'OR';

            foreach ($arTmpResultID as $key=>$value){
                if(preg_match('~^[0-9]+$~is',$value)){
                    $arrFilter[$eltsFilterID][]							= array('ID' => $value);
                } else {
                    $arrFilter[$eltsFilterID][]							= array('CODE' => $value);
                };
            }



            $arParams["USE_PERMISSIONS"] 								= $arParams["USE_PERMISSIONS"]=="Y";
            if(!is_array($arParams["GROUP_PERMISSIONS"]))
                $arParams["GROUP_PERMISSIONS"] 							= array(1);

            $bUSER_HAVE_ACCESS 											= !$arParams["USE_PERMISSIONS"];
            if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
            {
                $arUserGroupArray = $USER->GetUserGroupArray();
                foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
                {
                    if(in_array($PERM, $arUserGroupArray))
                    {
                        $bUSER_HAVE_ACCESS 								= true;
                        break;
                    }
                }
            }

            if(CModule::IncludeModule("iblock")){

                if(is_numeric($arParams["IBLOCK_ID"]))
                {
                    $rsIBlock 											= CIBlock::GetList(array(), array(
                        "ACTIVE" 									=> "Y",
                        "ID" 										=> $arParams["IBLOCK_ID"],
                    ));
                }
                else
                {
                    $rsIBlock 											= CIBlock::GetList(array(), array(
                        "ACTIVE" 									=> "Y",
                        "CODE" 										=> $arParams["IBLOCK_ID"],
                        "SITE_ID"	 								=> SITE_ID,
                    ));
                }
                if($arResult 											= $rsIBlock->GetNext())
                {
                    $arResult["USER_HAVE_ACCESS"] 						= $bUSER_HAVE_ACCESS;
                    //SELECT
                    $arSelect 											= array_merge($arParams["FIELD_CODE"], array(
                        "ID",
                        "IBLOCK_ID",
                        "IBLOCK_SECTION_ID",
                        "NAME",
                        "ACTIVE_FROM",
                        "DETAIL_PAGE_URL",
                        "DETAIL_TEXT",
                        "DETAIL_TEXT_TYPE",
                        "PREVIEW_TEXT",
                        "PREVIEW_TEXT_TYPE",
                        "PREVIEW_PICTURE",
                    ));
                    $bGetProperty 										= count($arParams["PROPERTY_CODE"])>0;
                    if($bGetProperty)
                        $arSelect[]										= "PROPERTY_*";
                    //WHERE
                    $arFilter 											= array (
                        "IBLOCK_ID" 								=> $arResult["ID"],
                        "IBLOCK_LID" 								=> SITE_ID,
                        "ACTIVE" 									=> "Y",
                        "CHECK_PERMISSIONS" 						=> "Y",
                    );

                    if($arParams["CHECK_DATES"])
                        $arFilter["ACTIVE_DATE"] 						= "Y";

                    $arParams["PARENT_SECTION"] 						= CIBlockFindTools::GetSectionID(
                        $arParams["PARENT_SECTION"],
                        $arParams["PARENT_SECTION_CODE"],
                        array(
                            "GLOBAL_ACTIVE" 					=> "Y",
                            "IBLOCK_ID" 						=> $arResult["ID"],
                        )
                    );

                    if($arParams["PARENT_SECTION"]>0)
                    {
                        $arFilter["SECTION_ID"] 						= $arParams["PARENT_SECTION"];
                        if($arParams["INCLUDE_SUBSECTIONS"])
                            $arFilter["INCLUDE_SUBSECTIONS"] 			= "Y";

                        $arResult["SECTION"]							= array("PATH" => array());
                        $rsPath = CIBlockSection::GetNavChain($arResult["ID"], $arParams["PARENT_SECTION"]);
                        $rsPath->SetUrlTemplates("", $arParams["SECTION_URL"], $arParams["IBLOCK_URL"]);
                        while($arPath = $rsPath->GetNext())
                        {
                            $ipropValues 								= new \Bitrix\Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $arPath["ID"]);
                            $arPath["IPROPERTY_VALUES"] 				= $ipropValues->getValues();
                            $arResult["SECTION"]["PATH"][] 				= $arPath;
                        }

                        $ipropValues 									= new \Bitrix\Iblock\InheritedProperty\SectionValues($arResult["ID"], $arParams["PARENT_SECTION"]);
                        $arResult["IPROPERTY_VALUES"] 					= $ipropValues->getValues();
                    }
                    else
                    {
                        $arResult["SECTION"]							= false;
                    }
                    //ORDER BY
                    $arSort = array(
                        $arParams["SORT_BY1"]						=> $arParams["SORT_ORDER1"],
                        $arParams["SORT_BY2"]						=> $arParams["SORT_ORDER2"],
                    );
                    if(is_array($arSort) && !array_key_exists("ID", $arSort))
                        $arSort["ID"] 									= "DESC";

                    $obParser 											= new CTextParser;
                    $arResult["ITEMS"] 									= array();
                    $arResult["ELEMENTS"] 								= array();
                    $rsElement 											= CIBlockElement::GetList($arSort, array_merge($arFilter, $arrFilter), false, $arNavParams, $arSelect);

                    $rsElement->SetUrlTemplates($arParams["DETAIL_URL"], "", $arParams["IBLOCK_URL"]);

                    while($obElement 									= $rsElement->GetNextElement())
                    {
                        $arItem 										= $obElement->GetFields();

                        $arButtons 										= CIBlock::GetPanelButtons(
                            $arItem["IBLOCK_ID"],
                            $arItem["ID"],
                            0,
                            array("SECTION_BUTTONS"					=>false, "SESSID"=>false)
                        );
                        $arItem["EDIT_LINK"] 							= $arButtons["edit"]["edit_element"]["ACTION_URL"];
                        $arItem["DELETE_LINK"] 							= $arButtons["edit"]["delete_element"]["ACTION_URL"];

                        if($arParams["PREVIEW_TRUNCATE_LEN"] > 0)
                            $arItem["PREVIEW_TEXT"] 					= $obParser->html_cut($arItem["PREVIEW_TEXT"], $arParams["PREVIEW_TRUNCATE_LEN"]);

                        if(mb_strlen($arItem["ACTIVE_FROM"])>0)
                            $arItem["DISPLAY_ACTIVE_FROM"] 				= CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
                        else
                            $arItem["DISPLAY_ACTIVE_FROM"] 				= "";

                        $ipropValues 									= new \Bitrix\Iblock\InheritedProperty\ElementValues($arItem["IBLOCK_ID"], $arItem["ID"]);
                        $arItem["IPROPERTY_VALUES"] 					= $ipropValues->getValues();

                        if(isset($arItem["PREVIEW_PICTURE"]))
                        {
                            $arItem["PREVIEW_PICTURE"] 					= (0 < $arItem["PREVIEW_PICTURE"] ? CFile::GetFileArray($arItem["PREVIEW_PICTURE"]) : false);
                            if ($arItem["PREVIEW_PICTURE"])
                            {
                                $arItem["PREVIEW_PICTURE"]["ALT"] 		= $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"];
                                if ($arItem["PREVIEW_PICTURE"]["ALT"] 	== "")
                                    $arItem["PREVIEW_PICTURE"]["ALT"] 	= $arItem["NAME"];
                                $arItem["PREVIEW_PICTURE"]["TITLE"] 	= $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"];
                                if ($arItem["PREVIEW_PICTURE"]["TITLE"] == "")
                                    $arItem["PREVIEW_PICTURE"]["TITLE"] = $arItem["NAME"];
                            }
                        }
                        if(isset($arItem["DETAIL_PICTURE"]))
                        {
                            $arItem["DETAIL_PICTURE"] 					= (0 < $arItem["DETAIL_PICTURE"] ? CFile::GetFileArray($arItem["DETAIL_PICTURE"]) : false);
                            if ($arItem["DETAIL_PICTURE"])
                            {
                                $arItem["DETAIL_PICTURE"]["ALT"] 		= $arItem["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"];
                                if ($arItem["DETAIL_PICTURE"]["ALT"] 	== "")
                                    $arItem["DETAIL_PICTURE"]["ALT"] 	= $arItem["NAME"];
                                $arItem["DETAIL_PICTURE"]["TITLE"] 		= $arItem["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"];
                                if ($arItem["DETAIL_PICTURE"]["TITLE"] 	== "")
                                    $arItem["DETAIL_PICTURE"]["TITLE"] 	= $arItem["NAME"];
                            }
                        }

                        $arItem["FIELDS"] 								= array();
                        foreach($arParams["FIELD_CODE"] as $code)
                            if(is_array($arItem) && array_key_exists($code, $arItem))
                                $arItem["FIELDS"][$code] 					= $arItem[$code];

                        if($bGetProperty)
                            $arItem["PROPERTIES"] 						= $obElement->GetProperties();
                        $arItem["DISPLAY_PROPERTIES"]					= array();
                        foreach($arParams["PROPERTY_CODE"] as $pid)
                        {
                            $prop 										= &$arItem["PROPERTIES"][$pid];
                            if(
                                (is_array($prop["VALUE"]) && count($prop["VALUE"])>0)
                                || (!is_array($prop["VALUE"]) && mb_strlen($prop["VALUE"])>0)
                            )
                            {
                                $arItem["DISPLAY_PROPERTIES"][$pid] 	= CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "news_out");
                            }
                        }

                        $arResult["ITEMS"][] 							= $arItem;
                        $arResult["ELEMENTS"][] 						= $arItem["ID"];
                    }

                    $arResult["NAV_STRING"] 							= $rsElement->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
                    $arResult["NAV_CACHED_DATA"]						= $navComponentObject->GetTemplateCachedData();
                    $arResult["NAV_RESULT"]								= $rsElement;

                }

            }

            return $arResult;
        }

    }
}

if(!class_exists('CIBlockPropertyORCODE')){
    class CIBlockPropertyORCODE
    {

        const USER_TYPE = 'ElementORCODE';

        public static function GetUserTypeDescription()
        {
            return array(
                "PROPERTY_TYPE" => Bitrix\Iblock\PropertyTable::TYPE_STRING,
                "USER_TYPE" => "ElementORCODE",
                "DESCRIPTION" => "Оригинальный код",
                "GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
                "GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
                "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
                "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
            );
        }

        public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
        {
            static $cache = array();
            if(isset($strHTMLControlName['MODE']) && $strHTMLControlName["MODE"] == "CSV_EXPORT")
            {
                if(!isset($cache[$value["VALUE"]]))
                {
                    $values = static::getManValueTxt($value["VALUE"]);
                    $cache[$value["VALUE"]] = htmlspecialcharsbx($values[0].': '.$values[1]);
                }

                return $cache[$value["VALUE"]];
            }
            elseif(mb_strlen($value["VALUE"])>0)
            {
                if(!isset($cache[$value["VALUE"]]))
                {
                    $values = static::getManValueTxt($value["VALUE"]);
                    $cache[$value["VALUE"]] = htmlspecialcharsbx($values[0].': '.$values[1]);
                }

                return $cache[$value["VALUE"]];

            }
            else
            {
                return '';
            }
        }

        public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
        {
            static $cache = array();
            if(mb_strlen($value["VALUE"])>0)
            {
                if(is_array($cache) && !array_key_exists($value["VALUE"], $cache))
                {

                    $values = static::getManValueTxt($value["VALUE"]);
                    $cache[$value["VALUE"]] = htmlspecialcharsbx($values[0].': '.$values[1]);

                }
                return $cache[$value["VALUE"]];
            }
            else
            {
                return '&nbsp;';
            }
        }

        private static function getManOptions($value){

            static $cache;

            list($id,$text) = explode(':', $value);

            $sOptions = '';
            $sOptions .= '<option value="">Выберите совместимость</option>';

            if(empty($cache)) {

                $re = CIBlockPropertyEnum::GetList(
                    Array("SORT" => "ASC"),
                    Array("IBLOCK_ID" => 11,
                        "CODE" => "MANUFACTURER")
                );

                if ($re) {
                    while ($ef = $re->GetNext()) {

                        $cache[$ef['ID']] = $ef['VALUE'];

                    }
                }

            }

            foreach($cache as $eid => $evalue){

                if(empty($eid) || empty($evalue))
                    continue;

                $sOptions .= '<option' . (($eid == $id) ? ' selected="selected"' : '') . ' value="' . $eid . '">' . htmlspecialcharsbx($evalue) . '</option>';
            }

            return $sOptions;

        }

        private static function getManValueTxt($value){

            list($id,$text) = explode(':', $value);
            $id = (int)trim($id);
            $name = '';

            if($id){

                $re = CIBlockPropertyEnum::GetList(
                    Array("SORT"=>"ASC"),
                    Array("IBLOCK_ID"=>11,
                        "CODE"=>"MANUFACTURER",
                        "ID" => $id)
                );

                if($re){
                    while($ef = $re->GetNext()) {
                        $name = trim($ef['VALUE']);
                    }
                }

            }

            return array($name, $text);

        }

        public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
        {

            static $iCounter;

            if(!$iCounter){
                $iCounter = 0;
            }

            ++$iCounter;

            $value["VALUE"] = isset($value["VALUE"]) ? $value["VALUE"] : '';

            $values = static::getManValueTxt($value["VALUE"]);
            $options = static::getManOptions($value["VALUE"]);

            return  '<input name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" id="h'.$iCounter.'" value="'.((!empty($value["VALUE"]) && trim($value["VALUE"]) != ':') ? htmlspecialcharsbx($value["VALUE"]) : '').'" type="hidden" />'
                .'<input id="t'.$iCounter.'" dataid="'.$iCounter.'" value="'.htmlspecialcharsbx($values[1]).'" type="text" onchange="this.dataid = this.getAttribute(\'dataid\');document.getElementById(\'h\' + this.dataid).value = document.getElementById(\'s\' + this.dataid).options[document.getElementById(\'s\' + this.dataid).selectedIndex].value  + \': \' + document.getElementById(\'t\' + this.dataid).value" />'
                .'<select id="s'.$iCounter.'" dataid="'.$iCounter.'" onchange="this.dataid = this.getAttribute(\'dataid\');document.getElementById(\'h\' + this.dataid).value = document.getElementById(\'s\' + this.dataid).options[document.getElementById(\'s\' + this.dataid).selectedIndex].value  + \': \' + document.getElementById(\'t\' + this.dataid).value">'.$options.'</select>'
                .'<input type="button" value="x" onclick="if(this.parentNode.parentNode.previousSibling || (this.parentNode.parentNode.nextSibling && this.parentNode.parentNode.nextSibling.nextSibling)) this.parentNode.parentNode.remove();return false;" />';
        }

        public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
        {
            $arPropertyFields = array(
                "HIDE" => array("ROW_COUNT", "COL_COUNT", "WITH_DESCRIPTION"),
            );
            return '';
        }
    }

    class CIBlockPropertyRManufacturer
    {
        const USER_TYPE = 'ElementRManufacturer';

        public static function GetUserTypeDescription()
        {
            return array(
                "PROPERTY_TYPE" => Bitrix\Iblock\PropertyTable::TYPE_STRING,
                "USER_TYPE" => "ElementRManufacturer",
                "DESCRIPTION" => "Замена производителя (тест)",
                "GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
                "GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
                "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
                "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
            );
        }

        public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
        {
            static $cache = array();
            if(isset($strHTMLControlName['MODE']) && $strHTMLControlName["MODE"] == "CSV_EXPORT")
            {
                if(!isset($cache[$value["VALUE"]]))
                {
                    $cache[$value["VALUE"]] = htmlspecialcharsbx($value["VALUE"]);
                }

                return $cache[$value["VALUE"]];
            }
            elseif(mb_strlen($value["VALUE"])>0)
            {
                if(!isset($cache[$value["VALUE"]]))
                {
                    $cache[$value["VALUE"]] = htmlspecialcharsbx($value["VALUE"]);
                }

                return $cache[$value["VALUE"]];

            }
            else
            {
                return '';
            }
        }

        public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
        {
            static $cache = array();
            if(mb_strlen($value["VALUE"])>0)
            {
                if(is_array($cache) && !array_key_exists($value["VALUE"], $cache))
                {

                    $cache[$value["VALUE"]] = htmlspecialcharsbx($value["VALUE"]);

                }
                return $cache[$value["VALUE"]];
            }
            else
            {
                return '&nbsp;';
            }
        }

        private static function getExport($id){
            static $cache;

            if (!is_array($cache)) {
                $cache = [];

                $db_profile = CCatalogExport::GetList(
                    array(),
                    array()
                );
                if ($db_profile) {
                    while ($ar_profile = $db_profile->Fetch())
                    {
                        if (!empty($ar_profile['SETUP_VARS'])) {
                            $cache[$ar_profile['ID']] .= $ar_profile['NAME'] . ' (' . $ar_profile['FILE_NAME'] . ', ' . $ar_profile['ID'] .')';
                        }
                    }
                }

            }

            $sOptions = '';
            $sOptions .= '<option value="">Выберите профиль</option>';

            foreach($cache as $eid => $evalue){

                if(empty($evalue))
                    continue;

                $sOptions .= '<option' . (($eid == $id) ? ' selected="selected"' : '') . ' value="' . $eid . '">' . htmlspecialcharsbx($evalue) . '</option>';
            }

            return $sOptions;

        }

        private static function getManOptions($id){

            static $cache;

            $sOptions = '';
            $sOptions .= '<option value="">Выберите производителя</option>';

            if(empty($cache)) {

                $re = CIBlockPropertyEnum::GetList(
                    Array("SORT" => "ASC"),
                    Array("IBLOCK_ID" => 11,
                        "CODE" => "MANUFACTURER_DETAIL")
                );

                if ($re) {
                    while ($ef = $re->GetNext()) {

                        $cache[$ef['ID']] = $ef['VALUE'];

                    }
                }

            }

            foreach($cache as $evalue){

                if(empty($evalue))
                    continue;

                $sOptions .= '<option' . (($evalue == $id) ? ' selected="selected"' : '') . ' value="' . $evalue . '">' . htmlspecialcharsbx($evalue) . '</option>';
            }

            return $sOptions;

        }

        public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
        {

            static $iCounter;

            if(!$iCounter){
                $iCounter = 0;
            }

            ++$iCounter;

            $value["VALUE"] = isset($value["VALUE"]) ? $value["VALUE"] : '';

            list($id,$id1,$id2) = explode(':', $value["VALUE"]);
            $id = trim($id);
            $id1 = trim($id1);
            $id2 = trim($id2);

            $options = static::getManOptions($id);
            $options1 = static::getManOptions($id1);
            $options2 = static::getExport($id2);

            return  '<input name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" id="rh'.$iCounter.'" value="'.((!empty($value["VALUE"]) && trim($value["VALUE"]) != ':') ? htmlspecialcharsbx($value["VALUE"]) : '').'" type="hidden" />'
                .'<select id="rt'.$iCounter.'" dataid="'.$iCounter.'" onchange="this.dataid = this.getAttribute(\'dataid\');document.getElementById(\'rh\' + this.dataid).value = document.getElementById(\'rt\' + this.dataid).options[document.getElementById(\'rt\' + this.dataid).selectedIndex].value  + \': \' + document.getElementById(\'rs\' + this.dataid).options[document.getElementById(\'rs\' + this.dataid).selectedIndex].value + \': \' + document.getElementById(\'re\' + this.dataid).options[document.getElementById(\'re\' + this.dataid).selectedIndex].value">'.$options.'</select>'
                .'<select id="rs'.$iCounter.'" dataid="'.$iCounter.'" onchange="this.dataid = this.getAttribute(\'dataid\');document.getElementById(\'rh\' + this.dataid).value = document.getElementById(\'rt\' + this.dataid).options[document.getElementById(\'rt\' + this.dataid).selectedIndex].value  + \': \' + document.getElementById(\'rs\' + this.dataid).options[document.getElementById(\'rs\' + this.dataid).selectedIndex].value + \': \' + document.getElementById(\'re\' + this.dataid).options[document.getElementById(\'re\' + this.dataid).selectedIndex].value">'.$options1.'</select>'
                .'<select id="re'.$iCounter.'" dataid="'.$iCounter.'" onchange="this.dataid = this.getAttribute(\'dataid\');document.getElementById(\'rh\' + this.dataid).value = document.getElementById(\'rt\' + this.dataid).options[document.getElementById(\'rt\' + this.dataid).selectedIndex].value  + \': \' + document.getElementById(\'rs\' + this.dataid).options[document.getElementById(\'rs\' + this.dataid).selectedIndex].value + \': \' + document.getElementById(\'re\' + this.dataid).options[document.getElementById(\'re\' + this.dataid).selectedIndex].value">'.$options2.'</select>'
                .'<input type="button" value="x" onclick="if(this.parentNode.parentNode.previousSibling || (this.parentNode.parentNode.nextSibling && this.parentNode.parentNode.nextSibling.nextSibling)) this.parentNode.parentNode.remove();return false;" />';

            /* return  '<input name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" id="h'.$iCounter.'" value="'.((!empty($value["VALUE"]) && trim($value["VALUE"]) != ':') ? htmlspecialcharsbx($value["VALUE"]) : '').'" type="hidden" />'
                .'<input id="t'.$iCounter.'" dataid="'.$iCounter.'" value="'.htmlspecialcharsbx($values[1]).'" type="text" onchange="this.dataid = this.getAttribute(\'dataid\');document.getElementById(\'h\' + this.dataid).value = document.getElementById(\'s\' + this.dataid).options[document.getElementById(\'s\' + this.dataid).selectedIndex].value  + \': \' + document.getElementById(\'t\' + this.dataid).value" />'
                .'<select id="'.$iCounter.'" dataid="'.$iCounter.'" onchange="this.dataid = this.getAttribute(\'dataid\');document.getElementById(\'h\' + this.dataid).value = document.getElementById(\'s\' + this.dataid).options[document.getElementById(\'s\' + this.dataid).selectedIndex].value  + \': \' + document.getElementById(\'t\' + this.dataid).value">'.$options.'</select>'

                .'<select id="s'.$iCounter.'" dataid="'.$iCounter.'" onchange="this.dataid = this.getAttribute(\'dataid\');document.getElementById(\'h\' + this.dataid).value = document.getElementById(\'s\' + this.dataid).options[document.getElementById(\'s\' + this.dataid).selectedIndex].value  + \': \' + document.getElementById(\'t\' + this.dataid).value">'.$options.'</select>'
                .'<input type="button" value="x" onclick="if(this.parentNode.parentNode.previousSibling || (this.parentNode.parentNode.nextSibling && this.parentNode.parentNode.nextSibling.nextSibling)) this.parentNode.parentNode.remove();return false;" />';
            */
        }

        public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
        {
            $arPropertyFields = array(
                "HIDE" => array("ROW_COUNT", "COL_COUNT", "WITH_DESCRIPTION"),
            );
            return '';
        }
    }

    class CIBlockPropertyMPIDMulti
    {
        public static function GetUserTypeDescription()
        {
            return array(
                "PROPERTY_TYPE" => Bitrix\Iblock\PropertyTable::TYPE_STRING,
                "USER_TYPE" => "ElementMPIDMulti",
                "DESCRIPTION" => "Привязка к элементам по множественным Id (тест)",
                "GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
                "GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
                "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
                "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
                "AddFilterFields" => array(__CLASS__,"AddFilterFields"),
                "ConvertToDB" => array(__CLASS__,"ConvertToDB"),
                "ConvertFromDB" => array(__CLASS__,"ConvertFromDB"),
            );
        }

        private static function getFilterValue($control,&$arFilter)
        {
            $filterValue = null;

            $controlName = $control["VALUE"];

            if(isset($arFilter[$controlName]) && !empty($arFilter[$controlName])){
                $filterValue = $arFilter[$controlName];
                unset($arFilter[$controlName]);
            }

            return $filterValue;
        }

        public static function AddFilterFields($arProperty, $control, &$arFilter, &$filtered)
        {
            $filtered = false;
            $filterValue = static::getFilterValue($control,$arFilter);

            if ($filterValue && file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/map_restore_products_'.mb_strtolower($arProperty["CODE"]).'.php'))
            {

                require $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/map_restore_products_'.mb_strtolower($arProperty["CODE"]).'.php';

                if(isset($aMaps)
                    && !empty($aMaps)
                    && isset($aMaps[$arProperty["CODE"]])
                    && !empty($aMaps[$arProperty["CODE"]])
                    && isset($aMaps[$arProperty["CODE"]][$filterValue])
                    && !empty($aMaps[$arProperty["CODE"]][$filterValue])
                ){

                    $arFilter["ID"] = $aMaps[$arProperty["CODE"]][$filterValue];
                    $filtered = true;

                }

            }

            if(!$filtered){
                $arFilter["ID"] = -1;
            }

        }

        public static function ConvertToDB($arProperty, $value) // сохранение в базу
        {
            if(is_array($value)&& array_key_exists("VALUE", $value))
            {

                if(is_array($value["VALUE"])
                    && !empty($value["VALUE"])){
                    $value["VALUE"] = array_filter($value["VALUE"]);
                    $value["VALUE"] = join(',',$value["VALUE"]);
                }

                $return = $value;
            }

            return $return;
        }

        public static function ConvertFromDB($arProperty, $value) //извлечение из БД
        {
            $return = false;

            global $USER;


            if(!is_array($value['VALUE']))
            {
                $value['VALUE'] = explode(',',$value['VALUE']);
                $value['VALUE'] = !empty($value['VALUE']) && !is_array($value['VALUE']) ? array($value['VALUE']) : $value['VALUE'];
                $value['VALUE'] = array_filter($value['VALUE']);
                $return = $value;
            }

            return $return;
        }

        private static function valueToArray($value){

            $avalues = is_array($value["VALUE"]) && sizeof($value["VALUE"]) ? $value["VALUE"] : explode(',',$value["VALUE"]);
            $avalues = !empty($avalues) && !is_array($avalues) ? array($avalues) : $avalues;
            return $avalues;

        }

        public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
        {

            static $cache = array();

            if(isset($strHTMLControlName['MODE']) && $strHTMLControlName["MODE"] == "CSV_EXPORT")
            {
                return is_array($value["VALUE"]) && sizeof($value["VALUE"]) ? join(',',$value["VALUE"]) : $value["VALUE"];
            }
            elseif(is_array($value["VALUE"]) && sizeof($value["VALUE"]))
            {

                $sreturn = array();
                $avalues = $value["VALUE"];

                foreach($avalues as $inum => $ivalue) {

                    if(!isset($cache[$ivalue]))
                    {
                        $db_res = CIBlockElement::GetList(
                            array(),
                            array("ID"=>$ivalue, "SHOW_HISTORY"=>"Y"),
                            false,
                            false,
                            array("ID", "IBLOCK_TYPE_ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL")
                        );
                        $ar_res = $db_res->GetNext();
                        if($ar_res)
                            $cache[$ivalue] = $ar_res;
                        else
                            $cache[$ivalue] = $ivalue;
                    }

                    if (isset($strHTMLControlName['MODE']) && ($strHTMLControlName["MODE"] == "SIMPLE_TEXT" || $strHTMLControlName["MODE"] == 'ELEMENT_TEMPLATE'))
                    {
                        if (is_array($cache[$ivalue]))
                            $sreturn[] = $cache[$ivalue]["~NAME"];
                        else
                            $sreturn[] = $cache[$ivalue];
                    }
                    else
                    {
                        if (is_array($cache[$ivalue]))
                            $sreturn[] = '<a href="'.$cache[$ivalue]["DETAIL_PAGE_URL"].'">'.$cache[$ivalue]["NAME"].'</a>';
                        else
                            $sreturn[] = htmlspecialcharsex($cache[$ivalue]);
                    }

                }

                return json_encode($sreturn);
            }
            else
            {
                return '';
            }
        }

        public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
        {
            static $cache = array();

            if(!empty($value["VALUE"])
                && is_array($value["VALUE"]))
            {
                $avalues = $value["VALUE"];

                $sreturn = '';

                foreach($avalues as $inum => $ivalue) {

                    if(is_array($cache) && !array_key_exists($ivalue, $cache))
                    {
                        $db_res = CIBlockElement::GetList(
                            array(),
                            array("ID"=>$ivalue, "SHOW_HISTORY"=>"Y"),
                            false,
                            false,
                            array("ID", "IBLOCK_TYPE_ID", "IBLOCK_ID", "NAME")
                        );
                        $ar_res = $db_res->GetNext();
                        if($ar_res)
                            $cache[$ivalue] = htmlspecialcharsbx($ar_res['NAME']).
                                ' [<a href="'.
                                '/bitrix/admin/iblock_element_edit.php?'.
                                'type='.urlencode($ar_res['IBLOCK_TYPE_ID']).
                                '&amp;IBLOCK_ID='.$ar_res['IBLOCK_ID'].
                                '&amp;ID='.$ar_res['ID'].
                                '&amp;lang='.LANGUAGE_ID.
                                '" title="Изменить">'.$ar_res['ID'].'</a>]';
                        else
                            $cache[$ivalue] = htmlspecialcharsbx($ivalue);
                    }

                    $sreturn .= $cache[$ivalue];

                }

                return $sreturn;
            }
            else
            {
                return '&nbsp;';
            }
        }

        //PARAMETERS:
        //$arProperty - b_iblock_property.*
        //$value - array("VALUE","DESCRIPTION") -- here comes HTML form value
        //strHTMLControlName - array("VALUE","DESCRIPTION")
        //return:
        //safe html
        public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
        {

            static $cache = array();

            $avalues = static::valueToArray($value);

            for($inum = 0; $inum < 10; $inum++){

                $avalues[sizeof($avalues) + $inum] = 0;

            }

            $sreturn = '';

            foreach($avalues as $inum => $ivalue) {

                if($ivalue > 0){

                    if(!isset($cache[$ivalue])){

                        $ar_res = false;
                        if (mb_strlen($ivalue)) {
                            $db_res = CIBlockElement::GetList(
                                array(),
                                array("ID" => $ivalue, "SHOW_HISTORY" => "Y"),
                                false,
                                false,
                                array("ID", "IBLOCK_ID", "NAME")
                            );
                            $ar_res = $db_res->GetNext();
                        }

                        if (!$ar_res)
                            $ar_res = array("NAME" => "");

                        $cache[$ivalue] = $ar_res;

                    } else {

                        $ar_res = $cache[$ivalue];
                    }

                } else {
                    $ivalue = '';
                    $ar_res = array("NAME" => "");
                }

                $fixIBlock = $arProperty["LINK_IBLOCK_ID"] > 0;
                $windowTableId = 'iblockprop-' . \Bitrix\Iblock\PropertyTable::TYPE_ELEMENT . '-' . $arProperty['ID'] . '-' . $arProperty['LINK_IBLOCK_ID'];

                $sreturn .= '<div><input name="' . htmlspecialcharsbx($strHTMLControlName["VALUE"]) .'['. htmlspecialcharsEx($inum) .']'. '" id="' . htmlspecialcharsbx($strHTMLControlName["VALUE"]) . '['. htmlspecialcharsEx($inum) .']'. '" value="' . htmlspecialcharsEx($ivalue) . '" size="20" type="text">' .
                    '<input type="button" value="..." onClick="jsUtils.OpenWindow(\'' . CUtil::JSEscape('/bitrix/admin/iblock_element_search.php?lang=' . LANGUAGE_ID . '&n=' . urlencode($strHTMLControlName["VALUE"].'['. htmlspecialcharsEx($inum) .']') . '&a=b' . ($fixIBlock ? '&iblockfix=y' : '') . '&tableId=' . $windowTableId) . '\', 900, 700);">' .
                    '&nbsp;<span id="sp_' . htmlspecialcharsbx($strHTMLControlName["VALUE"]) . '['. htmlspecialcharsEx($inum) .']'. '" >' . $ar_res['NAME'] . '</span></div>';
            }

            return $sreturn;

        }

    }

    class CIBlockPropertySMPIDMulti
    {
        public static function GetUserTypeDescription()
        {
            return array(
                "PROPERTY_TYPE" => Bitrix\Iblock\PropertyTable::TYPE_STRING,
                "USER_TYPE" => 'ElementSMPIDMulti',
                "DESCRIPTION" => "Простые множественные строки (тест)",
                "GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
                "GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
                "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
                "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
                "AddFilterFields" => array(__CLASS__,"AddFilterFields"),
                "ConvertToDB" => array(__CLASS__,"ConvertToDB"),
                "ConvertFromDB" => array(__CLASS__,"ConvertFromDB"),
            );
        }

        private static function getFilterValue($control,&$arFilter)
        {
            $filterValue = null;

            $controlName = $control["VALUE"];

            if(isset($arFilter[$controlName]) && !empty($arFilter[$controlName])){
                $filterValue = $arFilter[$controlName];
                unset($arFilter[$controlName]);
            }

            return $filterValue;
        }

        public static function AddFilterFields($arProperty, $control, &$arFilter, &$filtered)
        {
            $filtered = false;
            $filterValue = static::getFilterValue($control,$arFilter);

            if ($filterValue && file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/map_restore_products_'.mb_strtolower($arProperty["CODE"]).'.php'))
            {

                require $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/map_restore_products_'.mb_strtolower($arProperty["CODE"]).'.php';

                if(isset($aMaps)
                    && !empty($aMaps)
                    && isset($aMaps[$arProperty["CODE"]])
                    && !empty($aMaps[$arProperty["CODE"]])
                    && isset($aMaps[$arProperty["CODE"]][$filterValue])
                    && !empty($aMaps[$arProperty["CODE"]][$filterValue])
                ){

                    $arFilter["ID"] = $aMaps[$arProperty["CODE"]][$filterValue];
                    $filtered = true;

                }

            }

            if(!$filtered){
                $arFilter["ID"] = -1;
            }

        }

        public static function ConvertToDB($arProperty, $value) // сохранение в базу
        {

            if(is_array($value)&& array_key_exists("VALUE", $value))
            {

                if(is_array($value["VALUE"])
                    && !empty($value["VALUE"])){

                    $value['VALUE'] = array_filter($value['VALUE'],function($value){
                        if(is_string($value)){                      
                            return trim($value) !== "";
                        } else{
                            return is_array($value);
                        }
                    });

                    $value["VALUE"] = join(',',$value["VALUE"]);
                }

                $return = $value;
            }

            return $return;
        }

        public static function ConvertFromDB($arProperty, $value) //извлечение из БД
        {
            $return = false;

            if(!is_array($value['VALUE']))
            {
                $value['VALUE'] = explode(',',$value['VALUE']);
                $value['VALUE'] = !empty($value['VALUE']) && !is_array($value['VALUE']) ? array($value['VALUE']) : $value['VALUE'];
                $value['VALUE'] = array_filter($value['VALUE'],function($value){
                    if(is_string($value)){
                        return trim($value) !== "";
                    }
                    else{
                        return is_array($value);
                    }
                });

                $return = $value;
            }

            return $return;
        }

        private static function valueToArray($value){

            $avalues = is_array($value["VALUE"]) && sizeof($value["VALUE"]) ? $value["VALUE"] : explode(',',$value["VALUE"]);
            $avalues = !empty($avalues) && !is_array($avalues) ? array($avalues) : $avalues;
            return $avalues;

        }

        public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
        {
            if(isset($strHTMLControlName['MODE']) && $strHTMLControlName["MODE"] == "CSV_EXPORT")
            {
                return is_array($value["VALUE"]) && sizeof($value["VALUE"]) ? join(',',$value["VALUE"]) : $value["VALUE"];
            }
            elseif(is_array($value["VALUE"]) && sizeof($value["VALUE"]))
            {

                $sreturn = array();
                $avalues = $value["VALUE"];

                foreach($avalues as $inum => $ivalue) {

                    if (isset($strHTMLControlName['MODE']) && ($strHTMLControlName["MODE"] == "SIMPLE_TEXT" || $strHTMLControlName["MODE"] == 'ELEMENT_TEMPLATE'))
                    {
                        $sreturn[] = $ivalue;
                    }
                    else
                    {
                        $sreturn[] = htmlspecialcharsex($ivalue);
                    }

                }

                return $sreturn;
            }
            else
            {
                return '';
            }
        }

        public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
        {
            if(!empty($value["VALUE"])
                && is_array($value["VALUE"]))
            {
                $avalues = $value["VALUE"];

                $sreturn = '';

                foreach($avalues as $inum => $ivalue) {

                    $sreturn .= (!empty($sreturn) ? ', ' : '').htmlspecialcharsbx($ivalue);

                }

                return $sreturn;
            }
            else
            {
                return '&nbsp;';
            }
        }

        //PARAMETERS:
        //$arProperty - b_iblock_property.*
        //$value - array("VALUE","DESCRIPTION") -- here comes HTML form value
        //strHTMLControlName - array("VALUE","DESCRIPTION")
        //return:
        //safe html
        public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
        {

            $avalues = static::valueToArray($value);

            for($inum = 0; $inum < 10; $inum++){

                $avalues[sizeof($avalues) + $inum] = '';

            }

            $sreturn = '';

            foreach($avalues as $inum => $ivalue) {

                $fixIBlock = $arProperty["LINK_IBLOCK_ID"] > 0;
                $windowTableId = 'iblockprop-' . \Bitrix\Iblock\PropertyTable::TYPE_ELEMENT . '-' . $arProperty['ID'] . '-' . $arProperty['LINK_IBLOCK_ID'];

                $sreturn .= '<div><input name="' . htmlspecialcharsbx($strHTMLControlName["VALUE"]) .'['. htmlspecialcharsEx($inum) .']'. '" id="' . htmlspecialcharsbx($strHTMLControlName["VALUE"]) . '['. htmlspecialcharsEx($inum) .']'. '" value="' . htmlspecialcharsEx($ivalue) . '" size="20" type="text" />';
            }

            return $sreturn;

        }

    }

}

AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertyORCODE', 'GetUserTypeDescription'));
AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertyRManufacturer', 'GetUserTypeDescription'));

if(!class_exists('CIBlockPropertyMPID')){
    class CIBlockPropertyMPID
    {
        const USER_TYPE = 'ElementMPID';

        public static function GetUserTypeDescription()
        {
            return array(
                "PROPERTY_TYPE" => Bitrix\Iblock\PropertyTable::TYPE_STRING,
                "USER_TYPE" => "ElementMPID",
                "DESCRIPTION" => "Привязка к элементам по множественным Id",
                "GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
                "GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
                "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
                "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
            );
        }

        public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
        {
            static $cache = array();
            if(isset($strHTMLControlName['MODE']) && $strHTMLControlName["MODE"] == "CSV_EXPORT")
            {
                return $value["VALUE"];
            }
            elseif(mb_strlen($value["VALUE"])>0)
            {
                if(!isset($cache[$value["VALUE"]]))
                {
                    $db_res = CIBlockElement::GetList(
                        array(),
                        array("ID"=>$value["VALUE"], "SHOW_HISTORY"=>"Y"),
                        false,
                        false,
                        array("ID", "IBLOCK_TYPE_ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL")
                    );
                    $ar_res = $db_res->GetNext();
                    if($ar_res)
                        $cache[$value["VALUE"]] = $ar_res;
                    else
                        $cache[$value["VALUE"]] = $value["VALUE"];
                }

                if (isset($strHTMLControlName['MODE']) && ($strHTMLControlName["MODE"] == "SIMPLE_TEXT" || $strHTMLControlName["MODE"] == 'ELEMENT_TEMPLATE'))
                {
                    if (is_array($cache[$value["VALUE"]]))
                        return $cache[$value["VALUE"]]["~NAME"];
                    else
                        return $cache[$value["VALUE"]];
                }
                else
                {
                    if (is_array($cache[$value["VALUE"]]))
                        return '<a href="'.$cache[$value["VALUE"]]["DETAIL_PAGE_URL"].'">'.$cache[$value["VALUE"]]["NAME"].'</a>';
                    else
                        return htmlspecialcharsex($cache[$value["VALUE"]]);
                }
            }
            else
            {
                return '';
            }
        }

        public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
        {
            static $cache = array();
            if(mb_strlen($value["VALUE"])>0)
            {
                if(is_array($cache) && !array_key_exists($value["VALUE"], $cache))
                {
                    $db_res = CIBlockElement::GetList(
                        array(),
                        array("ID"=>$value["VALUE"], "SHOW_HISTORY"=>"Y"),
                        false,
                        false,
                        array("ID", "IBLOCK_TYPE_ID", "IBLOCK_ID", "NAME")
                    );
                    $ar_res = $db_res->GetNext();
                    if($ar_res)
                        $cache[$value["VALUE"]] = htmlspecialcharsbx($ar_res['NAME']).
                            ' [<a href="'.
                            '/bitrix/admin/iblock_element_edit.php?'.
                            'type='.urlencode($ar_res['IBLOCK_TYPE_ID']).
                            '&amp;IBLOCK_ID='.$ar_res['IBLOCK_ID'].
                            '&amp;ID='.$ar_res['ID'].
                            '&amp;lang='.LANGUAGE_ID.
                            '" title="Изменить">'.$ar_res['ID'].'</a>]';
                    else
                        $cache[$value["VALUE"]] = htmlspecialcharsbx($value["VALUE"]);
                }
                return $cache[$value["VALUE"]];
            }
            else
            {
                return '&nbsp;';
            }
        }

        //PARAMETERS:
        //$arProperty - b_iblock_property.*
        //$value - array("VALUE","DESCRIPTION") -- here comes HTML form value
        //strHTMLControlName - array("VALUE","DESCRIPTION")
        //return:
        //safe html
        public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
        {
            $ar_res = false;
            if(mb_strlen($value["VALUE"]))
            {
                $db_res = CIBlockElement::GetList(
                    array(),
                    array("ID"=>$value["VALUE"], "SHOW_HISTORY"=>"Y"),
                    false,
                    false,
                    array("ID", "IBLOCK_ID", "NAME")
                );
                $ar_res = $db_res->GetNext();
            }

            if(!$ar_res)
                $ar_res = array("NAME" => "");

            $fixIBlock = $arProperty["LINK_IBLOCK_ID"] > 0;
            $windowTableId = 'iblockprop-'.\Bitrix\Iblock\PropertyTable::TYPE_ELEMENT.'-'.$arProperty['ID'].'-'.$arProperty['LINK_IBLOCK_ID'];

            return  '<input name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" id="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" value="'.htmlspecialcharsEx($value["VALUE"]).'" size="20" type="text">'.
                '<input type="button" value="..." onClick="jsUtils.OpenWindow(\''.CUtil::JSEscape('/bitrix/admin/iblock_element_search.php?lang='.LANGUAGE_ID.'&n='.urlencode($strHTMLControlName["VALUE"]).'&a=b'.($fixIBlock ? '&iblockfix=y' : '').'&tableId='.$windowTableId).'\', 900, 700);">'.
                '&nbsp;<span id="sp_'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" >'.$ar_res['NAME'].'</span>';
        }

        public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
        {
            $arPropertyFields = array(
                "HIDE" => array("ROW_COUNT", "COL_COUNT", "WITH_DESCRIPTION"),
            );
            return '';
        }
    }
}

AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertyMPID', 'GetUserTypeDescription'));
AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertyMPIDMulti', 'GetUserTypeDescription'));
AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertySMPIDMulti', 'GetUserTypeDescription'));

if(!class_exists('twigMainPage')) {

    class twigMainPage
    {

        private $oAsset = null;
        private $oApp = null;
        private $bHasPanel = null;

        public function __construct()
        {
            $this->getObjects();
        }

        private function getObjects()
        {

            global $APPLICATION, $USER;

            $this->oApp = $APPLICATION;
            $this->oAsset = \Bitrix\Main\Page\Asset::getInstance();
            $this->bHasPanel = $USER->IsAuthorized() && $USER->isAdmin() ? true : false;

        }

        public function ShowHeadStrings()
        {

            if (!$this->oAsset->getShowHeadString()) {
                $this->oAsset->setShowHeadString();

                if (!$this->bHasPanel) {
                    $this->oApp->AddBufferContent(array(&$this->oApp, "GetHeadStrings"), 'DEFAULT');
                } else {
                    $this->oApp->AddBufferContent(array(&$this->oApp, "GetHeadStrings"), 'DEFAULT');
                }
            }
        }

        public function ShowHeadScripts()
        {
            $this->oAsset->setShowHeadScript();
            $this->oApp->AddBufferContent(array(&$this->oApp, "GetHeadScripts"), 2);
            $this->oApp->AddBufferContent(array(&$this->oApp, "GetHeadScripts"), 3);

        }

        public function ShowCSS($cMaxStylesCnt = true, $bXhtmlStyle = true)
        {

            $this->oApp->AddBufferContent(array(&$this->oApp, "GetHeadStrings"), 'BEFORE_CSS');

            if (!$this->bHasPanel) {

                $this->oApp->AddBufferContent(array(&$this->oApp, "GetCSS"), $cMaxStylesCnt, $bXhtmlStyle, 2);
                //$this->oApp->AddBufferContent(array(&$this->oApp, "GetCSS"), $cMaxStylesCnt, $bXhtmlStyle, 3);

            } else {

                $this->oApp->AddBufferContent(array(&$this->oApp, "GetCSS"), $cMaxStylesCnt, $bXhtmlStyle);

            }

        }


    }

}

if(!class_exists('twigSmartFilters')){

    abstract class twigSmartFiltersAll{
        abstract static function getSubFilters($sectionId = 0);
        abstract static function hasFilter404Error();
        abstract static function tryToCreateForwardFilterLink($arResult,$arCurrents,$arParams);
        abstract static function getPropertyCodeById($propertyId);
        abstract static function getPropsFromUrl();
        abstract static function set404Error($has404Error);
        abstract static function isCacheble();
        abstract static function filterHasProducts($sPropCode = '',$sPropVal = '');
        abstract static function fixTypeOfProduct();
    }


    class twigSmartFilters extends twigSmartFiltersAll{

        private static $fPropertyId = null;
        private static $hasError404 = null;
        private static $sectionURI = null;
        private static $aSectionURI = null;

        public static function set404Error($has404Error){
            static::$hasError404 = (bool)$has404Error;
        }

        public static function fixTypeOfProduct () {

            global $APPLICATION;

            $currentURL = $APPLICATION->GetCurPage();
            $oldUrl  = $currentURL = trim($currentURL);

            if ((defined('ERROR_404') || CHTTP::GetLastStatus() == 404 || mb_stripos(CHTTP::GetLastStatus(),'Not Found') !== false) && preg_match('~/filter/~isu',$currentURL) && preg_match('~/typeproduct\-~isu',$currentURL)) {


                $currentURL = preg_replace('~/filter/.*?(typeproduct\-[^/\?]+).*~is',"/filter/$1/",$currentURL);
                if ($oldUrl != $currentURL) {
                    LocalRedirect($currentURL);
                }
            }

        }

        public static function filterHasProducts($sPropCode = '',$sPropVal = ''){

            $aSelect = Array("ID");
            $aFilter = Array(
                "IBLOCK_ID" => 11,
                "ACTIVE_DATE" => "Y",
                "ACTIVE" => "Y",
                "PROPERTY_".$sPropCode."" => $sPropVal
            );

            $rEres = CIBlockElement::GetList(Array(), $aFilter, false, ($aNavParams = array('nTopCount' => 1)), $aSelect);

            $aFields = array();

            if($rEres)
                $aFields = $rEres->GetNext();


            return (!empty($aFields) && isset($aFields['ID']) && !empty($aFields['ID'])) ? true : false;
        }

        public static function getPropsFromUrl(){

            if(is_null(static::$fPropertyId)
                || is_null(static::$hasError404)){

                $aFilterEnumCache = array();

                if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/filters_enum_cache.php')){
                    require $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/filters_enum_cache.php';
                }

                $request = \Bitrix\Main\Context::getCurrent()->getRequest();
                $uri = new \Bitrix\Main\Web\Uri($request->getRequestUri());
                $uri->deleteParams(\Bitrix\Main\HttpRequest::getSystemParameters());
                $currentURL = $uri->GetUri();

                $hasError404 = false;
                $currentURL = trim($currentURL);

                $filterURL = preg_replace('~.*filter/(.*)$~isu',"$1",$currentURL);
                $filterURL = trim($filterURL);

                if(!empty($filterURL)){

                    if(mb_stripos($filterURL,'/') !== false){

                        $filterURLs = explode('/',$filterURL);

                    } else {
                        $filterURLs = array($filterURL);
                    }

                    foreach($filterURLs as $smartPart){

                        $smartPart = preg_split("/-(is|or)-/", $smartPart, -1, PREG_SPLIT_DELIM_CAPTURE);
                        $startParts = false;

                        if(is_array($smartPart) && sizeof($smartPart) > 0){

                            $startFrom = $smartPart[0];

                            foreach($smartPart as $smartElement){


                                if($smartElement == 'is'){
                                    $startParts = true;
                                }

                                if($startParts
                                    && $smartElement != 'or'
                                    && $smartElement != 'is'){

                                    $hasError404 = empty($smartElement) ? true : $hasError404;

                                    if(!empty($aFilterEnumCache)){
// var_dump("extendec_classes =>", $aFilterEnumCache, $startFrom, $smartElement);
                                        $hasError404 = !$hasError404 && isset($aFilterEnumCache[$startFrom][$smartElement]) ? false : true;
                                        if(is_array($aFilterEnumCache[$startFrom][$smartElement]))
                                        {
                                            static::$fPropertyId[key($aFilterEnumCache[$startFrom][$smartElement])][current($aFilterEnumCache[$startFrom][$smartElement])] = $smartElement;
                                        }

                                    } else {

                                        if(!$hasError404){

                                            $filterEnumsDB = CIBlockPropertyEnum::GetList(
                                                Array(
                                                    "DEF" => "DESC",
                                                    "SORT" => "ASC"),
                                                Array(
                                                    "CODE" => $startFrom,
                                                    "XML_ID" => $smartElement,
                                                    "IBLOCK_ID" => 11)
                                            );

                                            $hasError404 = true;

                                            if($filterEnumsDB){

                                                if($filterEnumsArr = $filterEnumsDB->GetNext()) {

                                                    if(static::filterHasProducts($startFrom,$filterEnumsArr['ID'])){

                                                        $hasError404 = false;
                                                        static::$fPropertyId[$filterEnumsArr['PROPERTY_ID']][$filterEnumsArr['VALUE']] = $filterEnumsArr['XML_ID'];

                                                    }


                                                }

                                            }

                                        }

                                    }

                                    if($hasError404
                                        && is_null(static::$hasError404)
                                    ){
                                        static::$hasError404 = $hasError404;
                                    }

                                }

                            }



                        }

                        if(!$startParts){
                            $hasError404 = true;
                        }

                    }

                }

                if(is_null(static::$hasError404)){
                    static::$hasError404 = false;
                }

                if(is_null(static::$fPropertyId)){
                    static::$fPropertyId = false;
                }

            }

        }

        public static function getPropertyCodeById($propertyId){

            static $propertyCodes,$aEnumsIdToCode,$aEnums;

            if(is_null($aEnumsIdToCode)){

                $aEnumsIdToCode = $aEnums = array();

                if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/enum_cache.php')){
                    require $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/enum_cache.php';
                    $aEnumsIdToCode = array_flip($aEnumsIdToCode);
                }
            }

            if(!is_array($propertyCodes))
                $propertyCodes = array();

            if(!isset($propertyCodes[$propertyId])){

                if(isset($aEnumsIdToCode[$propertyId])){

                    $propertyCodes[$propertyId] = $aEnumsIdToCode[$propertyId];

                } else {

                    $filterEnumsDB = CIBlockProperty::GetList(
                        Array(
                            "DEF" => "DESC",
                            "SORT" => "ASC"),
                        Array(
                            "ID" => $propertyId)
                    );

                    if($filterEnumsDB){

                        if($filterEnumsArr = $filterEnumsDB->GetNext()) {

                            $propertyCodes[$propertyId] = $filterEnumsArr['CODE'];

                        }

                    }

                }

            }

            return isset($propertyCodes[$propertyId]) ? $propertyCodes[$propertyId] : false;

        }

        public static function isCacheble()
        {
            global $APPLICATION;

            return true;

        }

        public static function tryToCreateForwardFilterLink($arResult,$arCurrents,$arParams){
            global $APPLICATION;

            $obCache = new CPHPCache;

            $fDir = $APPLICATION->GetCurDir();
            $fDir = array_filter(explode('/',$fDir));
            $fDir = array_shift($fDir);
            $fDir = preg_replace('~[^a-z0-9\-\_]+~is','',$fDir);
            $cacheID = $fDir.'_smart_url_rules';

            if(is_null(static::$aSectionURI)){
                static::$aSectionURI = array();
            }

            if(is_null(static::$sectionURI)){
                static::$sectionURI = array();
            }

            if(!isset(static::$aSectionURI[$cacheID])){

                if(
                    static::isCacheble()
                    && file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/'.$cacheID.'.php')
                    && (filesize(dirname(dirname(__DIR__)).'/bitrix/tmp/'.$cacheID.'.php') > 0)
                    && ((filemtime(dirname(dirname(__DIR__)).'/bitrix/tmp/'.$cacheID.'.php') + $arParams['CACHE_TIME']) > time())
                ){

                    require_once dirname(dirname(__DIR__)).'/bitrix/tmp/'.$cacheID.'.php';
                    static::$aSectionURI[$cacheID] = static::$sectionURI = $aSectionURI;

                } else {

                    if($obCache->InitCache($arParams['CACHE_TIME'], $cacheID, "/impel/")){

                        $tmp = array();
                        $tmp = $obCache->GetVars();

                        if(isset($tmp[$cacheID])){
                            static::$sectionURI = $tmp[$cacheID];
                        }


                    } else {

                        $sectionList = CIBlockSection::GetList(array(), array(
                            "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                        ), false, array("ID", "IBLOCK_ID", "SECTION_PAGE_URL"));

                        $sectionList->SetUrlTemplates($arParams["SEF_RULE"]);

                        if($sectionList){
                            while($section = $sectionList->GetNext()){

                                $sUriKey = $section['ID'].':'.$arParams["SEF_RULE"];
                                static::$sectionURI[$sUriKey] = $section;

                            }
                        }

                        if($obCache->StartDataCache()){

                            $obCache->EndDataCache(
                                array(
                                    $cacheID => static::$sectionURI
                                )
                            );

                        };

                    };

                    static::$aSectionURI[$cacheID] = static::$sectionURI;

                    if(static::isCacheble())
                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/'.$cacheID.'.php','<?php $aSectionURI = '.var_export(static::$sectionURI,true).'; ?>');

                }

            } else {

                static::$sectionURI = static::$aSectionURI[$cacheID];

            }

            $request = \Bitrix\Main\Context::getCurrent()->getRequest();
            $uri = new \Bitrix\Main\Web\Uri($request->getRequestUri());
            $uri->deleteParams(\Bitrix\Main\HttpRequest::getSystemParameters());
            $pageURL = $uri->GetUri();

            $JS_FILTER_PARAMS = array();
            if ($arParams["SEF_MODE"] == "Y")
            {
                $section = false;
                if ($arParams['SECTION_ID'] > 0)
                {
                    $sUriKey = $arParams['SECTION_ID'].':'.$arParams["SEF_RULE"];
                    $section = static::$sectionURI[$sUriKey];

                }



                if ($section)
                {
                    $url = $section["DETAIL_PAGE_URL"];
                }
                else
                {
                    $url = CIBlock::ReplaceSectionUrl($arParams["SEF_RULE"], array());
                }

                $CBitrixCatalogSmartFilter = new CBitrixCatalogSmartFilter;

                $CBitrixCatalogSmartFilter->arResult["ITEMS"] = $arCurrents;

                $JS_FILTER_PARAMS["SEF_SET_FILTER_URL"] = $CBitrixCatalogSmartFilter->makeSmartUrl($url, true);

            }

            $paramsToDelete = array("set_filter", "del_filter", "ajax", "bxajaxid", "AJAX_CALL", "mode");

            foreach($arResult["ITEMS"] as $arItem)
            {
                foreach($arItem["VALUES"] as $key => $ar)
                {
                    $paramsToDelete[] = $ar["CONTROL_NAME"];
                    if(isSet($ar["CONTROL_NAME_ALT"])) {
                        $paramsToDelete[] = $ar["CONTROL_NAME_ALT"];
                    }
                }
            }

            $clearURL = \CHTTP::urlDeleteParams($pageURL, $paramsToDelete, array("delete_system_params" => true));

            if ($JS_FILTER_PARAMS["SEF_SET_FILTER_URL"])
            {
                $FILTER_URL = $JS_FILTER_PARAMS["SEF_SET_FILTER_URL"];
            }
            else
            {
                $paramsToAdd = array(
                    "set_filter" => "y",
                );
                foreach($CBitrixCatalogSmartFilter->arResult["ITEMS"] as $arItem)
                {
                    foreach($arItem["VALUES"] as $key => $ar)
                    {
                        foreach($arCurrents["VALUES"] as $arCurrent){

                            if(isset($arCurrent[$ar["CONTROL_NAME"]]))
                            {
                                if($arItem["PROPERTY_TYPE"] == "N" || isset($arItem["PRICE"]))
                                    $paramsToAdd[$ar["CONTROL_NAME"]] = $arCurrent[$ar["CONTROL_NAME"]];
                                elseif($arCurrent[$ar["CONTROL_NAME"]] == $ar["HTML_VALUE"])
                                    $paramsToAdd[$ar["CONTROL_NAME"]] = $arCurrent[$ar["CONTROL_NAME"]];
                            }
                            elseif(isset($arCurrent[$ar["CONTROL_NAME_ALT"]]))
                            {
                                if ($arCurrent[$ar["CONTROL_NAME_ALT"]] == $ar["HTML_VALUE_ALT"])
                                    $paramsToAdd[$ar["CONTROL_NAME_ALT"]] = $arCurrent[$ar["CONTROL_NAME_ALT"]];
                            }

                        }
                    }
                }

                $FILTER_URL = htmlspecialcharsbx(\CHTTP::urlAddParams($clearURL, $paramsToAdd, array(
                    "skip_empty" => true,
                    "encode" => true,
                )));

            }

            $currDir = $APPLICATION->GetCurDir();

            return $FILTER_URL;

        }

        public static function hasFilter404Error(){

            static::getPropsFromUrl();
            $hasError404 = static::$hasError404;

            if($hasError404){
                CHTTP::SetStatus("404 Not Found");
            }

            return $hasError404;

        }

        public static function getSubFilters($sectionId = 0){

            static::getPropsFromUrl();

            $main_parameter = array();

            if(file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/filters_options_cache.php')){
                require dirname(dirname(__DIR__)).'/bitrix/tmp/filters_options_cache.php';
            }

            if(empty($main_parameter)){

                $main_parameter_sizeof = \COption::GetOptionString('my.stat', "main_parameter_sizeof", 0, SITE_ID);

                if($main_parameter_sizeof > 0)
                    for($i = 0; $i < $main_parameter_sizeof; $i ++){

                        $main_parameter['id'][$i] = \COption::GetOptionString('my.stat', "main_parameter_id".$i, "", SITE_ID);
                        $main_parameter['chain'][$i] = \COption::GetOptionString('my.stat', "main_parameter_chain".$i, "", SITE_ID);
                        $main_parameter['value'][$i] = \COption::GetOptionString('my.stat', "main_parameter_value".$i, "", SITE_ID);
                        $main_parameter['section'][$i] = \COption::GetOptionString('my.stat', "main_parameter_section".$i, "", SITE_ID);

                    }

            }

            $fPropertyId = static::$fPropertyId;

            if(isset($main_parameter['value']) && !empty($main_parameter['value'])){

                $cValues = $main_parameter['value'];

                foreach($cValues as $number => $value){
                    if(mb_stripos($value,',') !== false){

                        $values = explode(',',$value);
                        $values = array_map('trim',$values);
                        $current = current($values);
                        $main_parameter['value'][$number] = $current;
                        unset($values[0]);

                        $main_parameter['value'] = array_merge($main_parameter['value'],$values);
                        $main_parameter['id'] = array_merge($main_parameter['id'],array_fill(0,sizeof($values),$main_parameter['id'][$number]));
                        $main_parameter['chain'] = array_merge($main_parameter['chain'],array_fill(0,sizeof($values),$main_parameter['chain'][$number]));
                        $main_parameter['section'] = array_merge($main_parameter['section'],array_fill(0,sizeof($values),$main_parameter['section'][$number]));

                    }


                }


            }

            $addCodes = array();

            if(isset($main_parameter['chain']) && !empty($main_parameter['chain'])) {

                $main_parameter['chain'] = !empty($main_parameter['chain'])
                && !is_array($main_parameter['chain'])
                    ? array($main_parameter['chain'])
                    : $main_parameter['chain'];

                foreach($main_parameter['chain'] as $cnumber => $id) {

                    $pid = $main_parameter['id'][$cnumber];
                    $pvalue = $main_parameter['value'][$cnumber];
                    $psection = $main_parameter['section'][$cnumber];

                    if(!empty($pvalue)) {
                        foreach($fPropertyId as $propertyId => $propertyValues) {
                            if($propertyId == $pid) {
                                foreach($propertyValues as $propertyValue => $propertyCode) {
                                    if(trim($propertyValue) == trim($pvalue) && (!$psection || ($psection == $sectionId))) {
                                        $addCodes[] = static::getPropertyCodeById($main_parameter['chain'][$cnumber]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $addCodes = array_unique($addCodes);
            $addCodes = array_filter($addCodes);
            $addCodes = join(',',$addCodes);

            return $addCodes;

        }

    }

}

if(!class_exists('twigAdminPropsPrint')) {

    abstract class twigAdminPropsPrintAll {

        abstract static function setProps($arResult);
        abstract static function delProps(&$arResult);
        abstract static function getProps($arResult);

    }

    class twigAdminPropsPrint extends twigAdminPropsPrintAll {

        private static $aProps = null;

        public static function setProps($arResult) {

            if(is_null(static::$aProps)) {
                static::$aProps = array();
            }

            if(!isset(static::$aProps[$arResult['ID']])) {

                if($arResult['IBLOCK_ID'] == 16) {

                    $dbBlockResult = CIBlockElement::GetProperty(
                        $arResult['IBLOCK_ID'],
                        $arResult['ID'],
                        Array(),
                        Array(
                            "CODE" => "PROVIDERCODE"
                        )
                    );

                    if ($dbBlockResult) {

                        while ($arBlockResult = $dbBlockResult->Fetch()) {

                            if(!empty($arBlockResult["VALUE"]))
                                static::$aProps[$arResult['ID']][] = array(
                                    'NAME' => $arBlockResult["NAME"],
                                    'VALUE' =>  $arBlockResult["VALUE"]);

                        }
                    }

                    $dbBlockResult = CIBlockElement::GetProperty(
                        $arResult['IBLOCK_ID'],
                        $arResult['ID'],
                        Array(),
                        Array(
                            "CODE" => "PROVIDERPRICE"
                        )
                    );

                    if ($dbBlockResult) {

                        while ($arBlockResult = $dbBlockResult->Fetch()) {

                            if(!empty($arBlockResult["VALUE"]))
                                static::$aProps[$arResult['ID']][] = array(
                                    'NAME' => $arBlockResult["NAME"],
                                    'VALUE' =>  $arBlockResult["VALUE"]);

                        }
                    }

                    $dbBlockResult = CIBlockElement::GetProperty(
                        $arResult['IBLOCK_ID'],
                        $arResult['ID'],
                        Array(),
                        Array(
                            "CODE" => "PROVIDERCODE_RTK"
                        )
                    );

                    if ($dbBlockResult) {

                        while ($arBlockResult = $dbBlockResult->Fetch()) {

                            if(!empty($arBlockResult["VALUE"]))
                                static::$aProps[$arResult['ID']][] = array(
                                    'NAME' => $arBlockResult["NAME"],
                                    'VALUE' =>  $arBlockResult["VALUE"]);

                        }
                    }

                    $dbBlockResult = CIBlockElement::GetProperty(
                        $arResult['IBLOCK_ID'],
                        $arResult['ID'],
                        Array(),
                        Array(
                            "CODE" => "PROVIDERPRICE_RTK"
                        )
                    );

                    if ($dbBlockResult) {

                        while ($arBlockResult = $dbBlockResult->Fetch()) {

                            if(!empty($arBlockResult["VALUE"]))
                                static::$aProps[$arResult['ID']][] = array(
                                    'NAME' => $arBlockResult["NAME"],
                                    'VALUE' =>  $arBlockResult["VALUE"]);

                        }
                    }

                    $dbBlockResult = CIBlockElement::GetProperty(
                        $arResult['IBLOCK_ID'],
                        $arResult['ID'],
                        Array(),
                        Array(
                            "CODE" => "PROVIDERPRICECUR"
                        )
                    );

                    if ($dbBlockResult) {

                        while ($arBlockResult = $dbBlockResult->Fetch()) {

                            if(!empty($arBlockResult["VALUE"]))
                                static::$aProps[$arResult['ID']][] = array(
                                    'NAME' => $arBlockResult["NAME"],
                                    'VALUE' =>  $arBlockResult["VALUE"]);

                        }
                    }


                    $aMonths = array(1,3,12,24);

                    foreach($aMonths as $iMonth) {

                        $dbBlockResult = CIBlockElement::GetProperty(
                            $arResult['IBLOCK_ID'],
                            $arResult['ID'],
                            Array(),
                            Array(
                                "CODE" => "STATISTIC".$iMonth
                            )
                        );

                        if ($dbBlockResult) {

                            while ($arBlockResult = $dbBlockResult->Fetch()) {

                                if(!empty($arBlockResult["VALUE"]))
                                    static::$aProps[$arResult['ID']][] = array(
                                        'NAME' => $arBlockResult["NAME"],
                                        'VALUE' =>  $arBlockResult["VALUE"]);

                            }
                        }

                        $dbBlockResult = CIBlockElement::GetProperty(
                            $arResult['IBLOCK_ID'],
                            $arResult['ID'],
                            Array(),
                            Array(
                                "CODE" => "PREORDER".$iMonth
                            )
                        );

                        if ($dbBlockResult) {

                            while ($arBlockResult = $dbBlockResult->Fetch()) {

                                if(!empty($arBlockResult["VALUE"]))
                                    static::$aProps[$arResult['ID']][] = array(
                                        'NAME' => $arBlockResult["NAME"],
                                        'VALUE' => $arBlockResult["VALUE"]);

                            }
                        }

                    }


                }

                $arResult['STORES'] = array();
                $arResult['MANAGER_STORES'] = array();

                $arResult['BUY_ID'] = getBondsProduct($arResult['ID']);

                if(($arResult["BUY_ID"]	!= $arResult['ID']) || ($arResult['IBLOCK_ID'] == 16)) {

                    $arResult["CAN_BUY"] = canYouBuy($arResult['ID']);

                    if($arResult['CAN_BUY']){

                        $rsStore = CCatalogStoreProduct::GetList(
                            array(),
                            array('PRODUCT_ID' => $arResult['BUY_ID'], "!STORE_ID" => array(3,6,9,10)),
                            false,
                            false
                        );

                        if ($rsStore){

                            while($arStore = $rsStore->Fetch()) {

                                $arResult['STORES'][] = array(
                                    'STORE_NAME' => $arStore['STORE_NAME'],
                                    'AMOUNT' => $arStore['AMOUNT'],
                                    'STORE_ID' => $arStore['STORE_ID']
                                );
                            }
                        }

                        $arResult['MANAGER_STORES'] = $arResult['STORES'];

                        if( isset($arResult['MANAGER_STORES'])
                            && !empty($arResult['MANAGER_STORES'])
                        ) {
                            foreach ($arResult['MANAGER_STORES'] as $store) {

                                static::$aProps[$arResult['ID']][] = array(
                                    'NAME' => (GetMessage('CT_BCE_CATALOG_STORE_' . $store['STORE_ID']) != "" ? GetMessage('CT_BCE_CATALOG_STORE_' . $store['STORE_ID']) : $store['STORE_NAME']).'',
                                    'VALUE' =>  $store['AMOUNT'] . GetMessage('CT_BCE_CATALOG_STORE_AMOUNT'));

                            }
                        }

                    }

                    $bondsArSelect = Array("NAME");
                    $bondsArFilter = Array(
                        "ID"=>(int)($arResult['BUY_ID'])
                    );

                    $bondResDB = CIBlockElement::GetList(Array(), $bondsArFilter, false, false, $bondsArSelect);

                    if($bondResDB && ($bondResArr = $bondResDB->GetNext())) {
                        if(isset($bondResArr['NAME']) && !empty($bondResArr['NAME'])) {
                            static::$aProps[$arResult['ID']][] = array(
                                'NAME' => GetMessage("CRL_BONDS_NAME"),
                                'VALUE' => $bondResArr['NAME']);
                        }

                    }

                }

                $arResult['PRINT_QUANTITY'] = get_quantity_product($arResult['ID']);
                static::$aProps[$arResult['ID']][] = array(
                    'NAME' => GetMessage("CRL_QUANTITY"),
                    'VALUE' => $arResult['PRINT_QUANTITY']);

                if(isset($arResult['DISPLAY_PROPERTIES'])
                    &&isset($arResult['DISPLAY_PROPERTIES']['COM_BLACK'])
                    &&isset($arResult['DISPLAY_PROPERTIES']['COM_BLACK']['VALUE'])
                    &&!empty($arResult['DISPLAY_PROPERTIES']['COM_BLACK']['VALUE'])){

                    static::$aProps[$arResult['ID']][] = array(
                        'NAME' => GetMessage("COMMENT_TO_PRODUCT"),
                        'VALUE' => ('<span class="red">'.$arResult['DISPLAY_PROPERTIES']['COM_BLACK']['VALUE'].'</span>')
                    );

                    unset($arResult['DISPLAY_PROPERTIES']['COM_BLACK'],$arResult['PROPERTIES']['COM_BLACK']);
                }

                if(isset($arResult['PROPERTIES'])
                    &&isset($arResult['PROPERTIES']['QUALITY'])
                    &&isset($arResult['PROPERTIES']['QUALITY']['VALUE'])
                    &&!empty($arResult['PROPERTIES']['QUALITY']['VALUE'])){

                    static::$aProps[$arResult['ID']][] = array(
                        'NAME' => GetMessage("CRL_QUALITY"),
                        'VALUE' => join('/',$arResult["PROPERTIES"]["QUALITY"]["VALUE"])
                    );

                    unset($arResult['DISPLAY_PROPERTIES']['QUALITY'],$arResult['PROPERTIES']['QUALITY']);

                }

            }
        }

        public static function delProps(&$arResult){
            $arResult['PRINT_QUANTITY'] = false;
            unset($arResult['DISPLAY_PROPERTIES']['QUALITY'],$arResult['PROPERTIES']['QUALITY']);
            unset($arResult['DISPLAY_PROPERTIES']['COM_BLACK'],$arResult['PROPERTIES']['COM_BLACK']);
        }

        public static function getProps($arResult) {
            return isset(static::$aProps[$arResult['ID']])
                ? static::$aProps[$arResult['ID']]
                : null;
        }

    }

}

trait twigTemplateServices {

    private static $imageDimensions = null;
    private static $arEmptyPreview= null;

    public static function firstCharToUpper($string){
        return ToUpper(mb_substr($string,0,1)).mb_substr($string,1,mb_strlen($string));
    }

    public static function firstCharToLower($string){
        return ToLower(mb_substr($string,0,1)).mb_substr($string,1,mb_strlen($string));
    }

    public static function getCacheHash($hash){
        return rtrim(strtr(base64_encode(trim(sprintf('%s',$hash))), '+/', '-_'), '=');
    }

    public static function getOldPrice(&$arResult){

        $arResult['OLD_PRICE_PERCENTS'] = 0;
        $arResult['OLD_PRICE'] = 0;

        if(isset($arResult['ITEM_PRICES'])
            && isset($arResult['ITEM_PRICES'][$arResult['ITEM_PRICE_SELECTED']])){

            $minPrice = $arResult['ITEM_PRICES'][$arResult['ITEM_PRICE_SELECTED']];
            $old_price = 0;

            if(     isset($arResult["DISPLAY_PROPERTIES"]["OLD_PRICE"])
                &&  isset($arResult["DISPLAY_PROPERTIES"]["OLD_PRICE"]["~VALUE"])
                &&  isset($arResult["DISPLAY_PROPERTIES"]["OLD_PRICE"]["~VALUE"])
                &&  isset($arResult["DISPLAY_PROPERTIES"]["OLD_PRICE"]["~VALUE"])
                && ($minPrice['PRICE'] < $arResult["DISPLAY_PROPERTIES"]["OLD_PRICE"]["~VALUE"])
            ){

                $old_price = trim($arResult["DISPLAY_PROPERTIES"]["OLD_PRICE"]["~VALUE"]);
                $old_price = str_ireplace(',','.',trim($old_price));

                $arResult['OLD_PRICE'] = $old_price = (float)$old_price;

                if($arResult['OLD_PRICE'] > 0){
                    $arResult['OLD_PRICE'] = CurrencyFormat($arResult['OLD_PRICE'],$minPrice['CURRENCY']);
                }

            };

            unset($arResult["DISPLAY_PROPERTIES"]["OLD_PRICE"],$arResult["DISPLAY_PROPERTIES"]["OLD_PRICE"]);

            if($old_price > 0
                && isset($minPrice['RATIO_PRICE'])
                && !empty($minPrice['RATIO_PRICE'])){

                $discount_val =  str_ireplace(',','.',trim($minPrice['RATIO_PRICE']));
                $discount_val = (float)$discount_val;

                if($old_price > $discount_val){
                    $arResult['OLD_PRICE_PERCENTS'] = 100 - round(( $discount_val / $old_price * 100),0);
                }
            }
        }

    }

    public static function setImageDimensions($dimensions = array()) {

        if(is_null(static::$imageDimensions)){
            static::$imageDimensions = array(
                'thumb' => array(
                    'width' => 74,
                    'height' => 74
                ),
                'main' => array(
                    'width' => 500,
                    'height' => 500
                ),
                'big' => array(
                    'width' => 900,
                    'height' => 900
                ),
                'list' => array(
                    'width' => 500,
                    'height' => 500
                ),
            );
        }

        if(!empty($dimensions)){
            static::$imageDimensions = $dimensions;
        }

    }

    public static function getThumb($src, $dimensions = array('width' => 370,'height' => 370)) {

        $src = rectangleImage(
            $_SERVER['DOCUMENT_ROOT'].$src,
            $dimensions['width'],
            $dimensions['height'],
            $src,
            '#ffffff'
        );

        return array(
            'SRC' => $src,
            'WIDTH' => $dimensions['width'],
            'HEIGHT' => $dimensions['height']
        );

    }

    public static function getDesc($text, $symbCount = 240){

        if(!empty($text)){
            $text = strip_tags($text);
            $text = trim($text);
        }

        if($text
            && (mb_strlen($text) > $symbCount)){
            $text = mb_substr($text,0,$symbCount);
            if(($pos = mb_strrpos($text," ")) !== false){
                $text = mb_substr($text,0,$pos);
            }
        }

        return $text;

    }

    public static function getEmptyPreview(&$arParams){

        if(!(isset($arParams['arEmptyPreview'])
            && isset($arParams['arEmptyPreview']['SRC'])
            &&!empty($arParams['arEmptyPreview']['SRC']))){

            $strEmptyPreview = SITE_TEMPLATE_PATH.'/images/no_photo.png';

            if (file_exists($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview))
            {
                $arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview);
                if (!empty($arSizes))
                {
                    $arEmptyPreview = array(
                        'SRC' => $strEmptyPreview,
                        'WIDTH' => intval($arSizes[0]),
                        'HEIGHT' => intval($arSizes[1])
                    );
                }
                unset($arSizes);
            }

            unset($strEmptyPreview);

            $arNewItemsList = array();
            static::$arEmptyPreview = $arParams['arEmptyPreview'] = $arEmptyPreview;

        }

    }


    public static function getConcentProcessingLink(&$arResult){

        $consent_processing_link = COption::GetOptionString("my.stat", "consent_processing_link", "");
        $consent_processing_text = GetMessage('SOA_CONSENT_PROCESSING_LINK');

        if(!empty($consent_processing_link)){
            $consent_processing_text = str_ireplace('href="#"','href="'.$consent_processing_link.'"',$consent_processing_text);
        } else {
            $consent_processing_text = strip_tags($consent_processing_text);
        }

        $arResult['CONSENT_PROCESSING_TEXT'] = $consent_processing_text;

    }

}

if(!class_exists('twigSeoSections')){

    abstract class twigSeoSectionsAll{
        abstract static function printLinks($arResult = array(), $sSection = '');
        abstract static function getActiveFilters($intSectionID, $arParams, $filter_set, $cacheTime = 604800);

        abstract static function printSeoAndSetTitles($intSectionID = 0, $arParams, $cacheTime = 604800, $currPage = '');
        abstract static function printSeoAndSetTitlesSection($intSectionID = 0, $arParams, $cacheTime = 604800, $currPage = '', $replaces = []);

        abstract static function printSeoAndTitlesAtSectionEpilog(&$arResult,&$arParams);
        abstract static function incScriptsAtSectionEpilog(&$arResult,&$arParams);

        abstract static function printSortAtSectionResultModifier(&$arResult,$arParams,$cacheTime = 604800);
        abstract static function applySectionTemplateModifications(&$arResult,&$arParams);
        abstract static function applySectionModelTemplateModifications(&$arResult,&$arParams);

        abstract static function applyTemplateModifications(&$arResult,&$arParams, $bInSection = false);

        abstract static function getHowSortArray($arParams,$cacheTime = 604800);
        abstract static function getPagerArray();
        abstract static function getPageElementCount($element_count_param);

        abstract static function getConcentProcessingLink(&$arResult);


    }

    class twigSeoSections extends twigSeoSectionsAll{

        use twigTemplateServices;

        public static function applySectionModelTemplateModifications(&$arResult,&$arParams){

            foreach($arResult['ITEMS'] as $arKey => $arItem){

                foreach(array('TYPEPRODUCT','MANUFACTURER') as $sPropCode){

                    if($arItem['ID']
                        && !empty($arItem['ID'])) {

                        $dbBlockResult = CIBlockElement::GetProperty(
                            $arItem['IBLOCK_ID'],
                            $arItem['ID'],
                            Array(),
                            Array(
                                "CODE" => $sPropCode
                            )
                        );

                        if ($dbBlockResult) {

                            while ($arBlockResult = $dbBlockResult->Fetch()) {

                                $arResult['ITEMS'][$arKey]['PROPERTIES'][$sPropCode]['VALUE'] = trim($arBlockResult["VALUE_ENUM"]);


                            }
                        }

                    }

                }

            }

        }

        public static function applyTemplateModifications(&$arResult,&$arParams, $bInSection = false){

            static $bHasRights;

            if(is_null($bHasRights))
                $bHasRights = checkQuantityRigths();

            if(isset($_REQUEST['skip_rights']))
                $bHasRights = false;

            static::setImageDimensions();
            static::getEmptyPreview($arParams);

            $productPictures = CIBlockPriceTools::getDoublePicturesForItem($arResult, $arParams['ADD_PICT_PROP']);
            if (empty($productPictures['PICT']))
                $productPictures['PICT'] = $arParams['arEmptyPreview'];


            if(!(isset($productPictures['PICT']['SRC'])
                && file_exists($_SERVER['DOCUMENT_ROOT'].$productPictures['PICT']['SRC']))){
                $productPictures['PICT'] = $arParams['arEmptyPreview'];
            }

            $productPictures['PICT'] = static::getThumb($productPictures['PICT']['SRC'],static::$imageDimensions['list']);

            if(isset($arResult['PREVIEW_TEXT'])
                && !empty($arResult['PREVIEW_TEXT'])){
                $arResult['PREVIEW_TEXT'] = static::getDesc($arResult['PREVIEW_TEXT'],$arParams['CHARS_COUNT']);
            }

            $arResult['PRODUCT_PREVIEW'] = $arResult['PREVIEW_PICTURE'] = $productPictures['PICT'];

            if(!$bInSection){

                $arResult['BUY_ID'] = getBondsProduct($arResult['ID']);
                if($arResult["BUY_ID"] !=$arResult['ID']){

                    $arResult["CAN_BUY"]	= canYouBuy($arResult['ID']);

                    $rsProducts = CCatalogProduct::GetList(
                        array(),
                        array('ID' => $arResult['BUY_ID']),
                        false,
                        false,
                        array(
                            'ID',
                            'CAN_BUY_ZERO',
                            'QUANTITY_TRACE',
                            'QUANTITY'
                        )
                    );

                    if ($arCatalogProduct = $rsProducts->Fetch())
                    {

                        $arResult['CHECK_QUANTITY'] = ($arCatalogProduct["QUANTITY_TRACE"] == 'Y');

                        $arResultDbRatio = CCatalogMeasureRatio::getList(array(), array("PRODUCT_ID" => $arResult['BUY_ID']), false, false, array("RATIO"));
                        $arResult['CATALOG_MEASURE_RATIO'] = 1;

                        if($arResultRatio = $arResultDbRatio->Fetch()){
                            if(isset($arResultRatio['RATIO']) && !empty($arResultRatio['RATIO'])){
                                $arResult['CATALOG_MEASURE_RATIO'] = $arResultRatio['RATIO'];
                            };
                        };

                        $arResult['CATALOG_QUANTITY'] = (
                        0 < $arCatalogProduct["QUANTITY"] && is_float($arResult['CATALOG_MEASURE_RATIO'])
                            ? floatval($arCatalogProduct["QUANTITY"])
                            : intval($arCatalogProduct["QUANTITY"])
                        );


                    }

                }

                $arResult['PRINT_QUANTITY'] = get_quantity_product($arResult['ID']);
                $arResult['CAN_BUY'] = $arResult['PRINT_QUANTITY'] > 0 ? $arResult['CAN_BUY'] : false;



            }

            static::getOldPrice($arResult);

            if(isset($arResult["DISPLAY_PROPERTIES"])
                && isset($arResult["DISPLAY_PROPERTIES"]["ARTNUMBER"])
                && isset($arResult["DISPLAY_PROPERTIES"]["ARTNUMBER"]["VALUE"])
                && !empty($arResult["DISPLAY_PROPERTIES"]["ARTNUMBER"]["VALUE"])){

                $arResult['ARTNUMBER'] = array(
                    'NAME' => $arResult["DISPLAY_PROPERTIES"]["ARTNUMBER"]["NAME"],
                    'VALUE' => $arResult["DISPLAY_PROPERTIES"]["ARTNUMBER"]["VALUE"]);

                unset($arResult["DISPLAY_PROPERTIES"]["ARTNUMBER"]);

            };


            if($bHasRights){
                twigAdminPropsPrint::setProps($arResult);
                $arResult['ADMIN_MESSAGES'] = twigAdminPropsPrint::getProps($arResult);
            } else {
                twigAdminPropsPrint::delProps($arResult);
            }


        }

        public static function applySectionTemplateModifications(&$arResult,&$arParams){

            global $USER;

            $arResult['COUNT_ITEMS'] = sizeof($arResult['ITEMS']);

            if (!empty($arResult['ITEMS']))
            {
                static::getEmptyPreview($arParams);

                foreach ($arResult['ITEMS'] as $key => $arItem)
                {
                    static::applyTemplateModifications($arItem,$arParams,true);
                    $arNewItemsList[$key] = $arItem;
                }

                $arNewItemsList[$key]['LAST_ELEMENT'] = 'Y';
                $arResult['ITEMS'] = $arNewItemsList;
                $arResult['DEFAULT_PICTURE'] = $arParams['arEmptyPreview'];

            }

            $arResult['PAGER'] = static::getPagerArray();
            $element_count_param = 'PAGE_ELEMENT_COUNT:section:'.$arParams["IBLOCK_ID"].':'.$arParams["SECTION_CODE_PATH"];
            $element_count = static::getPageElementCount($element_count_param);
            $arParams["PAGE_ELEMENT_COUNT"] = $element_count;

            static::getConcentProcessingLink($arResult);

        }

        public static function getPageElementCount($element_count_param){

            global $APPLICATION;

            $element_count = $_REQUEST['PAGE_ELEMENT_COUNT'];
            $pager = static::getPagerArray();

            if(empty($element_count) && (($APPLICATION->get_cookie($element_count_param)))){
                $element_count = $APPLICATION->get_cookie($element_count_param);
            }

            $element_count = (int)$element_count;
            $element_count = !in_array($element_count,$pager) ? $pager[0] : $element_count;
            $element_count = empty($element_count) ? $pager[0] : $element_count;

            return $element_count;
        }

        public static function getPagerArray(){
            return ($pager = array(
                0 =>12,
                1 =>24,
                2 =>72));
        }

        public static function getHowSortArray($arParams,$cacheTime = 604800){

            $howSort = array(
                "propertysort_ONSTOCK:asc" => GetMessage("SORT_ONSTOCK_ASC"),
                //"created_date:desc" => GetMessage("SORT_CREATED_DESC"),
                "show_counter:desc" => GetMessage("SORT_SHOW_COUNTER_DESC"),
                //"sort:asc" => GetMessage("SORT_SORT_ASC"),
                //"sort:desc" => GetMessage("SORT_SORT_DESC"),
                "name:asc" => GetMessage("SORT_NAME_ASC"),
                //"name:desc" => GetMessage("SORT_NAME_DESC"),
                //"catalog_QUANTITY:asc" => GetMessage("SORT_QUANTITY_ASC"),
                //"catalog_QUANTITY:desc" => GetMessage("SORT_QUANTITY_DESC"),
                //"show_counter:asc" => GetMessage("SORT_SHOW_COUNTER_ASC"),
                "created_date:desc" => GetMessage("SORT_CREATED_ASC"),
                //"HAS_PREVIEW_PICTURE:asc" => GetMessage("SORT_PREVIEW_PICTURE_ASC"),
                //"HAS_PREVIEW_PICTURE:desc" => GetMessage("SORT_PREVIEW_PICTURE_DESC")
            );

            if(isset($arParams["PRICE_CODE"])
                && !empty($arParams["PRICE_CODE"])){

                $obCache = new CPHPCache;
                $cacheID = 'catalog_price_code';

                if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

                    $tmp = array();
                    $tmp = $obCache->GetVars();

                    if(isset($tmp[$cacheID])){
                        $catalog_price_code = $tmp[$cacheID];
                    }

                    foreach ($catalog_price_code as $ar_res) {
                        if(!isSet($ar_res['ID']))
                            continue;
                                                
                        $howSort["catalog_PRICE_{$ar_res['ID']}:asc"] = (GetMessage("PRICE_ASC"));
                        $howSort["catalog_PRICE_{$ar_res['ID']}:desc"] = (GetMessage("PRICE_DESC"));
                        
                        // $howSort["catalog_PRICE_".$ar_res["ID"].":asc"] = (GetMessage("PRICE_ASC"));
                        // $howSort["catalog_PRICE_".$ar_res["ID"].":desc"] = (GetMessage("PRICE_DESC"));
                    }

                } else {

                    foreach ($arParams["PRICE_CODE"] as $price_name){

                        $db_res = CCatalogGroup::GetList(
                            array(
                                "SORT" =>"ASC"
                            ),
                            array(
                                "NAME" => $price_name
                            ),
                            false,
                            false,
                            array("ID")
                        );

                        $catalog_price_code = array();

                        if(is_object($db_res)){
                            while ($ar_res = $db_res->Fetch()){
                                $howSort["catalog_PRICE_".$ar_res["ID"].":asc"] = (GetMessage("PRICE_ASC"));
                                $howSort["catalog_PRICE_".$ar_res["ID"].":desc"] = (GetMessage("PRICE_DESC"));
                                $catalog_price_code[] = $ar_res["ID"];
                            }
                        }

                    }


                    if($obCache->StartDataCache()){

                        $obCache->EndDataCache(
                            array(
                                $cacheID => $catalog_price_code
                            )
                        );

                    };

                };

            };

            return $howSort;
        }

        public static function printSortAtSectionResultModifier(&$arResult,$arParams,$cacheTime = 604800){

            $howSort = static::getHowSortArray($arParams);
            $arResult['howSort'] = $howSort;

        }

        public static function incScriptsAtSectionEpilog(&$arResult,&$arParams){
            global $APPLICATION;

            if(!empty($arResult['COUNT_ITEMS'])
                && ($arParams['hasError404'] != 'Y')){

                $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.maskedinput.min.js");
            }

        }

        public static function printSeoAndTitlesAtSectionEpilog(&$arResult,&$arParams){
            global $APPLICATION, $USER;

            if(empty($arResult['COUNT_ITEMS'])
                && mb_stripos($APPLICATION->GetCurDir(),'/filter/') !== false){
                CHTTP::SetStatus("404 Not Found");
            }

            $howSort = $arParams['howSort'] = $arResult['howSort'];

            $sort_values = isset($howSort) ?? array_keys($howSort);

            $sort_code_param = 'sort:section:'.$arParams["IBLOCK_ID"].':'.$arParams["SECTION_CODE_PATH"];

            $sort_default = $arParams["ELEMENT_SORT_FIELD"].":".$arParams["ELEMENT_SORT_ORDER"];
            $_SESSION[$sort_code_param] = !isset($_SESSION[$sort_code_param]) ? $sort_default : $_SESSION[$sort_code_param];

            $sort_code = ((isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])) ? (urldecode($_REQUEST['sort'])) : ($_SESSION[$sort_code_param]));

            if(empty($sort_code) && (($APPLICATION->get_cookie($sort_code_param)))){
                $sort_code = $APPLICATION->get_cookie($sort_code_param);
            }

            if(!(!empty($sort_code) && (is_array($sort_values) && in_array($sort_code,$sort_values)))){
                $sort_code = $sort_default;
            }

            $_SESSION[$sort_code_param] = $sort_code;
            $APPLICATION->set_cookie($sort_code_param,$sort_code);

            if(!empty($sort_code) && is_array($sort_values) && in_array($sort_code,$sort_values)){

                list($arParams["ELEMENT_SORT_FIELD"],$arParams["ELEMENT_SORT_ORDER"]) = explode(":",$sort_code);
                list($arParams["ELEMENT_SORT_FIELD2"],$arParams["ELEMENT_SORT_ORDER2"]) = explode(":",$sort_code);

            }

            $arParams['sort_code'] = $sort_code;

            $strNavQueryString = "";

            if(isset($_REQUEST['q']) && !empty($_REQUEST['q'])){
                $strNavQueryString .= '&q='.$_REQUEST['q'].'';
            }

            $strNavQueryString = !empty($strNavQueryString) ? '?'.$strNavQueryString : $strNavQueryString;

            if(isset($arResult["NAV_STRING"]) && !empty($arResult["NAV_STRING"])){

                $links = array();
                preg_match_all('~<a[^>]*?>(.*?)</a>~isu',$arResult["NAV_STRING"],$links);

                if(!empty($links[1])){
                    $links[1] = array_map('strip_tags',$links[1]);
                    $links[1] = array_map('trim',$links[1]);
                    $links[1] = array_map('intval',$links[1]);
                    $arResult["NavPageCount"] = max($links[1]);
                }

                $links = array();
                preg_match_all('~<li[^>]*?class="active[^"]+?"[^>]*?>(.*?)</li>~isu',$arResult["NAV_STRING"],$links);

                if(!empty($links[1])){
                    $links[1] = array_map('strip_tags',$links[1]);
                    $links[1] = array_map('trim',$links[1]);
                    $links[1] = array_map('intval',$links[1]);
                    $links[1] = current($links[1]);
                    $arResult["NavPageNomer"] = $links[1];
                } else {
                    $arResult["NavPageNomer"] = 1;
                }

                $links = array();
                preg_match_all('~<a[^>]*?href="([^"]+?)"~isu',$arResult["NAV_STRING"],$links);

                if(!(isset($arResult["NavNum"]) && !empty($arResult["NavNum"]))){
                    $arResult["NavNum"] = 2;
                }

                if(!empty($links[1])){
                    $links[1] = array_map('trim',$links[1]);
                    $arResult["sUrlPath"] = end($links[1]);
                    $pages = array();
                    preg_match('#/pages([\d]*?)-([\d]+)#is',$arResult["sUrlPath"],$pages);
                    $arResult["NavNum"] = isset($pages[1]) && !empty($pages[1]) ? (int)trim($pages[1]) : 2;
                }

                if($arResult["NavPageNomer"] > 1){

                    $filter_set = (int)$APPLICATION->GetPageProperty('filter_set', '');

                    //$APPLICATION->SetPageProperty('filter_set', false);

                    $pagenav_title = $APPLICATION->GetPageProperty('pagenav_title');

                    if ($pagenav_title) {

                        $pagenav_title = \COption::GetOptionString('my.stat', (!$filter_set ? 'pagenav_title' : 'pagenav_filter_title'), '', SITE_ID);
                        $pagenav_title = str_ireplace('[pagenum]',$arResult["NavPageNomer"], $pagenav_title);
                        $APPLICATION->SetPageProperty('pagenav_title', $pagenav_title);

                        $pagenav_title_default = \COption::GetOptionString('my.stat', 'pagenav_title_default', '', SITE_ID);
                        $pagenav_title_default = str_ireplace('[pagenum]',$arResult["NavPageNomer"], $pagenav_title_default);

                        $APPLICATION->SetPageProperty('pagenav_title_default', $pagenav_title_default);

                    }

                    $pagenav_description = $APPLICATION->GetPageProperty('pagenav_description');

                    if ($pagenav_description) {

                        $pagenav_description = \COption::GetOptionString('my.stat', (!$filter_set ? 'pagenav_description' : 'pagenav_filter_description'), '', SITE_ID);
                        $pagenav_description = str_ireplace('[pagenum]',$arResult["NavPageNomer"], $pagenav_description);

                        $APPLICATION->SetPageProperty('pagenav_description', $pagenav_description);

                        $pagenav_description_default = \COption::GetOptionString('my.stat', 'pagenav_description_default', '', SITE_ID);
                        $pagenav_description_default = str_ireplace('[pagenum]',$arResult["NavPageNomer"], $pagenav_description_default);

                        $APPLICATION->SetPageProperty('pagenav_description_default', $pagenav_description_default);

                    }

                }

                if(isset($arResult["sUrlPath"])
                    && !empty($arResult["sUrlPath"])
                    && isset($arResult["NavPageNomer"])
                    && !empty($arResult["NavPageNomer"])
                    && isset($arResult["NavPageCount"])
                    && !empty($arResult["NavPageCount"])){

                    $arResult["sUrlPath"] = preg_replace('#(/pages([\d]*?)-([\d]+))#is', '', $arResult["sUrlPath"]);
                    $arResult["sUrlPath"] = preg_replace('~[^/]*?$~','',$arResult["sUrlPath"]);
                    $pageURL = 'pages'.($arResult["NavNum"] != 2 ? $arResult["NavNum"] : '').'-';

                    if ($arResult["NavPageNomer"] > 1) {

                        $link_prev = $arResult["sUrlPath"].(($arResult["NavPageNomer"] > 2) ? ($pageURL.($arResult["NavPageNomer"] - 1).'/') : '').$strNavQueryString;
                        $link_prev = (preg_match('~http(s*?)://~',$link_prev) == 0) ? IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$link_prev : $link_prev;

                        $APPLICATION->AddHeadString('<link rel="prev" href="'.$link_prev.'" />',true);

                    }

                    if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]){

                        $link_next = $arResult["sUrlPath"]. $pageURL . ($arResult["NavPageNomer"] + 1) .'/'.$strNavQueryString;
                        $link_next = (preg_match('~http(s*?)://~',$link_next) == 0) ? IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$link_next : $link_next;

                        $APPLICATION->AddHeadString('<link rel="next" href="'.$link_next.'" />',true);
                    }

                }

            }

            if(
                isset($arResult["NavPageCount"])
                && $arResult["NavPageCount"] > 0
                && isset($_REQUEST['PAGEN_1'])
                && $_REQUEST['PAGEN_1'] > 0
                && ($_REQUEST['PAGEN_1'] > $arResult["NavPageCount"])) {
                CHTTP::SetStatus("404 Not Found");
                define('ERROR_404','Y');
            }

        }

        public static function getPageNum(){

            $pageNum = defined('PAGEN_1') ? PAGEN_1 : 0;
            $pageNum = empty($pageNum)
            && isset($_REQUEST['PAGEN_1'])
            && !empty($_REQUEST['PAGEN_1'])
                ? (int)trim($_REQUEST['PAGEN_1'])
                : $pageNum;

            return $pageNum;

        }

        public static function printLinks($arResult = array(), $sSection = '') {

            global $APPLICATION, $USER;

            $page_num = static::getPageNum();

            if(isset($arResult['VARIABLES'])
                && isset($arResult['VARIABLES']['SECTION_CODE'])
                && !empty($arResult['VARIABLES']['SECTION_CODE'])){

                $filterPath = '';

                if(mb_stripos($APPLICATION->GetCurDir(),'/filter/') !== false){
                    $filterPath = $APPLICATION->GetCurDir();
                    $filterPath = preg_replace('~.*?/filter/~is','/filter/',$filterPath);
                }

                if($page_num){
                    $filterPath .= 'pages-'.$page_num.'/';
                }

                if(!empty($filterPath))
                    $filterPath = ltrim($filterPath,'/');

                //$filterPath = mb_stripos($filterPath,'-or-') === false ? $filterPath : '';

                // $APPLICATION->AddHeadString('<link rel="amphtml" href="'.(IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/amp/sections/' . $arResult['VARIABLES']['SECTION_CODE'] . '/' . $filterPath . '" />'));



            } else {

                $filterPath = '';

                // $APPLICATION->AddHeadString('<link rel="amphtml" href="'.(IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/amp/sections/'. $filterPath .'" />'));

            }

            $currentURL = $APPLICATION->GetCurPage();
            $currentURL = trim($currentURL);

            $request = \Bitrix\Main\Context::getCurrent()->getRequest();
            $values = $request->getQueryList();

            $uri = new \Bitrix\Main\Web\Uri($request->getRequestUri());
            $currentURL = $uri->GetUri();
            $server = \Bitrix\Main\Context::getCurrent()->getServer();
            $bcSet = false;

            if(isset($values['PAGEN_1'])){

                $pageNum = $values['PAGEN_1'];

                if(!empty($pageNum)
                    && $pageNum > 1
                    && mb_stripos($currentURL,'PAGEN_1=') != false
                ){
                    $canonicalURL = preg_replace('~/'.preg_quote('pages-','~').'[0-9]+?/~is','',$currentURL);
                    $canonicalURL = preg_replace('~[&]*?PAGEN_1=[^&]+~is','',$canonicalURL);
                    $canonicalURL = trim($canonicalURL,'?');

                    if ($canonicalURL != $currentURL) {
                        $canonicalURL = IMPEL_PROTOCOL . IMPEL_SERVER_NAME . $canonicalURL;
                        // $APPLICATION->AddHeadString('<link rel="canonical" href="'.$canonicalURL .'" />');
                    }

                }

                if(!empty($pageNum)
                    && (mb_stripos($currentURL,'PAGEN_1='.$values['PAGEN_1']) === false
                        || (mb_stripos($server->get('REDIRECT_QUERY_STRING'),'PAGEN_1=') !== false)
                        && sizeof(explode('PAGEN_1=',$server->get('REDIRECT_QUERY_STRING'))) > 2)
                ){

                    $uri = new \Bitrix\Main\Web\Uri($request->getRequestedPageDirectory());
                    $currentURL = $uri->GetUri();

                    $bOrFilter = mb_stripos($canonicalURL,'-or-') !== false;

                    $canonicalURL = preg_replace('~/'.preg_quote('pages-','~').'[0-9]+?/~is','',$currentURL);
                    $canonicalURL = rtrim($canonicalURL,'/') . '/pages-'.$pageNum.'/';
                    $canonicalURL = preg_replace('~-or-[^/]+~is','',$canonicalURL);

                    $bcSet = true;
                    $canonicalURL = IMPEL_PROTOCOL . IMPEL_SERVER_NAME . $canonicalURL;
                    // $APPLICATION->AddHeadString('<link rel="canonical" href="'.$canonicalURL .'" />');

                    if ($bOrFilter) {
                        LocalRedirect($canonicalURL,false,"301 Moved permanently");
                    }

                }

            }

            if(!$bcSet
                && mb_stripos($APPLICATION->GetCurDir(),'/filter/') !== false
                && mb_stripos($APPLICATION->GetCurDir(),'-or-') !== false){
                $uri = new \Bitrix\Main\Web\Uri($request->getRequestedPageDirectory());
                $currentURL = $uri->GetUri();
                $canonicalURL = preg_replace('~-or-[^/]+~is','',$currentURL);
                $canonicalURL = IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/'.trim($canonicalURL,'/').'/';

                LocalRedirect($canonicalURL,false,"301 Moved permanently");
                $APPLICATION->AddHeadString('<link rel="canonical" href="'.$canonicalURL .'" />');
            }

            if(mb_stripos(CHTTP::GetLastStatus(),'Not Found') !== false){

                $currentURL = preg_replace(array('~/filter/.*?$~is','~/pages-.*?$~is'),'/',$currentURL);

                ?>
                <div class="alert alert-warning alert-dismissible fade in" role="alert">
                    <?  echo sprintf(GetMessage('CT_BCS_TPL_ELEMENTS_NOT_FOUND'),$currentURL); ?>
                </div>
                <?

            } elseif(!empty($sSection)){

                echo $sSection;

            }

            return $APPLICATION->GetPageProperty('SEO_TEXT');
        }

        public static function getActiveFilters($intSectionID, $arParams, $filter_set, $cacheTime = 604800){

            global ${$arParams["FILTER_NAME"]};
            $active_filters = array();

            if($filter_set){


                if((isset(${$arParams["FILTER_NAME"]})
                    && !empty(${$arParams["FILTER_NAME"]}))
                ){

                    foreach(${$arParams["FILTER_NAME"]} as $filter_key => $filter_value){
                        if(mb_stripos($filter_key,'=PROPERTY_') !== false){
                            $active_filters[$filter_key] = $filter_value;
                        };
                    };

                };

                $main_parameter = array();
                $main_parameter_sizeof = \COption::GetOptionString('my.stat', "main_parameter_sizeof", 0, SITE_ID);

                $valueid = array();

                if(file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/filters_options_cache.php')){
                    require dirname(dirname(__DIR__)).'/bitrix/tmp/filters_options_cache.php';
                }

                if(empty($valueid)){

                    $obCache = new CPHPCache;
                    $cacheID = 'chainvalueid';

                    if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

                        $tmp = array();
                        $tmp = $obCache->GetVars();

                        if(isset($tmp[$cacheID])){
                            $valueid = $tmp[$cacheID];
                        }

                    } else {

                        if ($main_parameter_sizeof > 0) {

                            for ($i = 0; $i < $main_parameter_sizeof; $i++) {

                                $main_parameter['id'][$i] = \COption::GetOptionString('my.stat', "main_parameter_id" . $i, "", SITE_ID);
                                $main_parameter['value'][$i] = \COption::GetOptionString('my.stat', "main_parameter_value" . $i, "", SITE_ID);

                                $main_parameter['value'][$i] = mb_stripos($main_parameter['value'][$i], ',') !== false ? explode(',', $main_parameter['value'][$i]) : array($main_parameter['value'][$i]);
                                $main_parameter['value'][$i] = array_map('trim', $main_parameter['value'][$i]);
                                $main_parameter['value'][$i] = array_unique($main_parameter['value'][$i]);
                                $main_parameter['value'][$i] = array_filter($main_parameter['value'][$i]);

                                foreach ($main_parameter['value'][$i] as $enumValue) {

                                    $peDB = CIBlockPropertyEnum::GetList(
                                        array(),
                                        array(
                                            'VALUE' => $enumValue,
                                            'PROPERTY_ID' => $main_parameter['id'][$i]
                                        )
                                    );

                                    if ($peDB && ($peArr = $peDB->GetNext())) {

                                        if (!is_array($valueid[$main_parameter['id'][$i]])) {
                                            $valueid[$main_parameter['id'][$i]] = array();
                                        }

                                        if (!in_array($peArr['ID'], $valueid[$main_parameter['id'][$i]])) {
                                            $valueid[$main_parameter['id'][$i]][] = $peArr['ID'];
                                        }

                                    }

                                }

                            }

                        };

                        if ($obCache->StartDataCache()) {

                            $obCache->EndDataCache(
                                array(
                                    $cacheID => $valueid
                                )
                            );

                        };
                    }

                }

                if($main_parameter_sizeof
                    && !empty($valueid))
                    for($i = 0; $i < $main_parameter_sizeof; $i ++) {


                        if(!isset($main_parameter['id'][$i])
                            || !isset($main_parameter['chain'][$i])){
                            $main_parameter['id'][$i] = \COption::GetOptionString('my.stat', "main_parameter_id" . $i, "", SITE_ID);
                            $main_parameter['chain'][$i] = \COption::GetOptionString('my.stat', "main_parameter_chain" . $i, "", SITE_ID);
                        }
                        if($active_filters['=PROPERTY_'.$main_parameter['id'][$i]] !== null){
                            if((!sizeof(array_intersect($valueid[$main_parameter['id'][$i]], $active_filters['=PROPERTY_'.$main_parameter['id'][$i]]))
                                || !isset($active_filters['=PROPERTY_'.$main_parameter['id'][$i]])
                                || empty($active_filters['=PROPERTY_'.$main_parameter['id'][$i]])
                            )
                            && isset($active_filters['=PROPERTY_'.$main_parameter['chain'][$i]])
                            && !empty($active_filters['=PROPERTY_'.$main_parameter['chain'][$i]])
                            ){

                            $redirectUrl = isset($arParams['SEF_FOLDER'])
                            && !empty($arParams['SEF_FOLDER'])
                                ? trim($arParams['SEF_FOLDER'])
                                : '';

                            if(isset($intSectionID)
                                && !empty($intSectionID)){

                                $sectionIterator = CIBlockSection::GetList(array(), array('ID' => $intSectionID), false, array('SECTION_PAGE_URL'));

                                if($sectionIterator){

                                    //$sectionIterator->SetUrlTemplates('', $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"]);
                                    $sectionResult = $sectionIterator->GetNext();

                                    if(isset($sectionResult['SECTION_PAGE_URL'])
                                        && !empty($sectionResult['SECTION_PAGE_URL'])){

                                        $redirectUrl = $sectionResult['SECTION_PAGE_URL'];
                                    }

                                }
                            }

                            if(!empty($redirectUrl)){

                                CHTTP::SetStatus("404 Not Found");
                                //@define("ERROR_404","Y");
                                //LocalRedirect($redirectUrl);

                            }

                        }
                    }
                }

            }

            return $active_filters;

        }

        public static function getFiltersMinPrice($active_filters,$intSectionID,$arParams){

            $priceMin = '';


            $aFilters = $active_filters;
            if(!empty($intSectionID)) {
                $aFilters['SECTION_ID'] = $intSectionID;
            }

            $aFilters['!CATALOG_PRICE_1'] = false;

            $aFilters['ACTIVE'] = 'Y';
            $aFilters['GLOBAL_ACTIVE'] = 'Y';
            $aSort['catalog_PRICE_1'] = 'ASC';


            $aSelect = Array("CATALOG_GROUP_1");

            $dRes = CIBlockElement::GetList($aSort, $aFilters, false, Array("nTopCount"=>1), $aSelect);


            if($dRes) {
                $aRes = $dRes->GetNext();



                if(isset($aRes['CATALOG_PRICE_1'])
                    && isset($aRes['CATALOG_CURRENCY_1'])){

                    if(isset($arParams['CURRENCY_ID'])
                        && !empty($arParams['CURRENCY_ID'])
                        && $arParams['CURRENCY_ID'] != $aRes['CATALOG_CURRENCY_1']

                    ) {

                        $aRes['CATALOG_PRICE_1'] = CCurrencyRates::ConvertCurrency(
                            $aRes['CATALOG_PRICE_1'],
                            $aRes['CATALOG_CURRENCY_1'],
                            $arParams['CURRENCY_ID']
                        );


                        $aRes['CATALOG_CURRENCY_1'] = $arParams['CURRENCY_ID'];

                    }


                    $priceMin = CurrencyFormat($aRes['CATALOG_PRICE_1'],
                        $aRes['CATALOG_CURRENCY_1']);

                }

            }

            return $priceMin;

        }

        public static function printSeoAndSetTitlesSection($intSectionID = 0, $arParams, $cacheTime = 604800, $pCurrPage = '', $replaces = [])
        {
            global $APPLICATION;
            $hasTest = isset($_REQUEST['test']);

            $currPage = empty($pCurrPage) ? $APPLICATION->GetCurPage() : $pCurrPage;
            $currPage = isset($_SERVER['ORIG_REQUEST_URI']) && !empty($_SERVER['ORIG_REQUEST_URI']) ? trim($_SERVER['ORIG_REQUEST_URI']) : $currPage;
            $currPage = trim($currPage);

            $cacheID = 'infoMetaSection'.static::getCacheHash(IMPEL_SERVER_NAME.$currPage);

            $obCache = new CPHPCache;

            if ($obCache->InitCache($cacheTime, $cacheID, "/impel/")) {

                $tmp = $obCache->GetVars();

                if (isset($tmp[$cacheID])) {
                    $arkFields = $tmp[$cacheID];
                }

            } else {

                $arSelect = array(
                    'ID',
                    'NAME',
                    'PROPERTY_SEO_TITLE',
                    'PROPERTY_SEO_DECRIPTION',
                    'PROPERTY_SEO_KEYWORDS',
                    'PROPERTY_SEO_DECRIPTION_PAGEN',
                    'PROPERTY_SEO_TITLE_PAGEN',
                    'PROPERTY_H1_BOTTOM',
                    'PROPERTY_FOR_UNION_SECTIONS',
                    'PROPERTY_FOR_UNION_SECTIONS_NC',
                    'PREVIEW_TEXT',
                    'PROPERTY_SEO_DECRIPTION_PAGEN_VALUE',
                    'PROPERTY_SEO_TITLE_PAGEN_VALUE',
                );

                $arFilter = array(
                    "ACTIVE_DATE" => "Y",
                    "ACTIVE" => "Y",
                    "PROPERTY_FILTER_URL" => $currPage,
                    "PROPERTY_DOMAIN_VALUE" => IMPEL_SERVER_NAME,
                    "IBLOCK_ID" => 45);

                $res = CIBlockElement::GetList(
                    array(),
                    $arFilter,
                    false,
                    false,
                    $arSelect
                );
                
                if ($res && $ob = $res->GetNextElement()) {
                    $arkFields = $ob->GetFields();
                } else {
                    
                    $aFilterUrlsCache = array();

                    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/filters_titles_cache.php')){
                        require $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/filters_titles_cache.php';
                    }

                    if(isset($aFilterUrlsCache[IMPEL_SERVER_NAME])) {
                        foreach ($aFilterUrlsCache[IMPEL_SERVER_NAME] as $urlTemplate => $urlFields) {
                            if (isset($urlFields['PROPERTY_IS_REGEXP_VALUE'])
                                && $urlFields['PROPERTY_IS_REGEXP_VALUE']
                                && @preg_match('~'.$urlTemplate.'~isu',$currPage)
                            ) {
                                if ($hasTest) {
                                    echo $urlTemplate . ' - '. $currPage .' - '.$arkFields['ID'].'<br />';
                                }

                                $arkFields = $urlFields;
                                break;

                            }

                        }

                    }

                }

                if ($arkFields) {

                    $arkFields['SECTION_NAME'] = '';

                    if ($intSectionID) {

                        $sectionResult = CIBlockSection::GetList(
                            array(
                                "SORT" =>"ASC"
                            ),
                            array(
                                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                "ID" => (int)$intSectionID
                            ),
                            false,
                            $arSelect = array("NAME")
                        );

                        if(is_object($sectionResult)
                            && method_exists($sectionResult,"GetNext")){

                            while ($sectionProp = $sectionResult->GetNext()) {
                                $arkFields['SECTION_NAME'] = static::firstCharToLower($sectionProp['NAME']);
                            }

                        }

                    }

                }

                if ($obCache->StartDataCache()) {

                    $obCache->EndDataCache(
                        array(
                            $cacheID => $arkFields
                        )
                    );

                };

            }
            if (!empty($arkFields)) {
                ob_start();

                if ($hasTest) {
                    print_r($arkFields['ID']);
                }

                $section = $arkFields['PROPERTY_FOR_UNION_SECTIONS_VALUE'];
                $section_nc = $arkFields['PROPERTY_FOR_UNION_SECTIONS_NC_VALUE'] ? $arkFields['PROPERTY_FOR_UNION_SECTIONS_NC_VALUE'] : $arkFields['SECTION_NAME'];

                if (isset($arkFields['PROPERTY_SEO_TITLE_VALUE']) && !empty($arkFields['PROPERTY_SEO_TITLE_VALUE'])) {
                    $filter_title = trim($arkFields['PROPERTY_SEO_TITLE_VALUE']);
                }

                if (isset($arkFields['PROPERTY_H1_BOTTOM_VALUE']) && !empty($arkFields['PROPERTY_H1_BOTTOM_VALUE'])) {
                    $filter_h1 = trim($arkFields['PROPERTY_H1_BOTTOM_VALUE']);
                }

                if (isset($arkFields['PROPERTY_SEO_DECRIPTION_VALUE']) && !empty($arkFields['PROPERTY_SEO_DECRIPTION_VALUE'])) {
                    $filter_description = trim($arkFields['PROPERTY_SEO_DECRIPTION_VALUE']);
                }

                if (isset($arkFields['PROPERTY_SEO_KEYWORDS_VALUE']) && !empty($arkFields['PROPERTY_SEO_KEYWORDS_VALUE'])) {
                    $filter_keywords = trim($arkFields['PROPERTY_SEO_KEYWORDS_VALUE']);
                }

                $page_num = static::getPageNum();

                if ($page_num) {

                    if (isset($arkFields['PROPERTY_SEO_TITLE_PAGEN_VALUE']) && !empty($arkFields['PROPERTY_SEO_TITLE_PAGEN_VALUE'])) {
                        $filter_title = trim($arkFields['PROPERTY_SEO_TITLE_PAGEN_VALUE']);
                    }

                    if (isset($arkFields['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE']) && !empty($arkFields['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE'])) {
                        $filter_description = trim($arkFields['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE']);
                    }

                    $filter_title = str_ireplace('[pagenum]', $page_num, $filter_title);
                    $filter_h1 = str_ireplace('[pagenum]', $page_num, $filter_h1);
                    $filter_description = str_ireplace('[pagenum]', $page_num, $filter_description);
                    $filter_keywords = str_ireplace('[pagenum]', $page_num, $filter_keywords);

                }

                if ((mb_stripos($filter_title, '[min_price]') !== false
                        || mb_stripos($filter_h1, '[min_price]') !== false
                        || mb_stripos($filter_description, '[min_price]') !== false
                        || mb_stripos($filter_keywords, '[min_price]') !== false)
                    && $intSectionID
                ) {

                    $min_price = static::getFiltersMinPrice($active_filters, $intSectionID, $arParams);

                    $filter_title = str_ireplace('[min_price]', $min_price, $filter_title);
                    $filter_h1 = str_ireplace('[min_price]', $min_price, $filter_h1);
                    $filter_description = str_ireplace('[min_price]', $min_price, $filter_description);
                    $filter_keywords = str_ireplace('[min_price]', $min_price, $filter_keywords);

                }

                $filter_title = str_ireplace('[section]', $section, $filter_title);
                $filter_h1 = str_ireplace('[section]', $section, $filter_h1);
                $filter_description = str_ireplace('[section]', $section, $filter_description);
                $filter_keywords = str_ireplace('[section]', $section, $filter_keywords);

                $filter_title = str_ireplace('[section_nc]', $section_nc, $filter_title);
                $filter_h1 = str_ireplace('[section_nc]', $section_nc, $filter_h1);
                $filter_description = str_ireplace('[section_nc]', $section_nc, $filter_description);
                $filter_keywords = str_ireplace('[section_nc]', $section_nc, $filter_keywords);

                if (!empty($replaces)) {

                    $search = array_keys($replaces);
                    $replace = array_values($replaces);

                    $filter_title = str_ireplace($search, $replace, $filter_title);
                    $filter_h1 = str_ireplace($search, $replace, $filter_h1);
                    $filter_description = str_ireplace($search, $replace, $filter_description);
                    $filter_keywords = str_ireplace($search, $replace, $filter_keywords);

                }

                $filter_title = static::firstCharToUpper($filter_title);
                $filter_h1 = static::firstCharToUpper($filter_h1);
                $filter_description = static::firstCharToUpper($filter_description);
                $filter_keywords = static::firstCharToUpper($filter_keywords);

                $filter_keywords = preg_replace('~\s+?\s+~isu', ' ', $filter_keywords);
                $filter_description = preg_replace('~\s+?\s+~isu', ' ', $filter_description);
                $filter_title = preg_replace('~\s+?\s+~isu', ' ', $filter_title);
                $filter_h1 = preg_replace('~\s+?\s+~isu', ' ', $filter_h1);

                if (!empty($filter_keywords)) {
                    $APPLICATION->SetPageProperty('page_keywords', $filter_keywords);
                    $APPLICATION->SetPageProperty("keywords", $filter_keywords);
                }

                if (!empty($filter_description)) {
                    $APPLICATION->SetPageProperty('page_description', $filter_description);
                    $APPLICATION->SetPageProperty("description", $filter_description);
                }

                if (!empty($filter_title))
                    $APPLICATION->SetTitle($filter_title);

                if (!empty($filter_title)) {
                    $APPLICATION->SetPageProperty("title", $filter_title);
                    $APPLICATION->SetPageProperty('page_title', $filter_title);
                }

                if (!empty($filter_h1))
                    $APPLICATION->SetPageProperty("ADDITIONAL_H1", $filter_h1);

                if (!empty($arkFields)) {

                    $APPLICATION->SetPageProperty("ADDITIONAL_TITLE", $arkFields['NAME']);

                    $APPLICATION->IncludeComponent(
                        "bitrix:news.detail",
                        "nsection",
                        array(
                            "DISPLAY_DATE" => "Y",
                            "DISPLAY_NAME" => "Y",
                            "DISPLAY_PICTURE" => "Y",
                            "DISPLAY_PREVIEW_TEXT" => "Y",
                            "USE_SHARE" => "N",
                            "AJAX_MODE" => "N",
                            "IBLOCK_TYPE" => "catalog",
                            "IBLOCK_ID" => $arkFields['IBLOCK_ID'],
                            "ELEMENT_ID" => $arkFields['ID'],
                            "ELEMENT_CODE" => "",
                            "CHECK_DATES" => "Y",
                            "FIELD_CODE" => array(
                                0 => "NAME",
                                1 => "PREVIEW_PICTURE",
                                2 => "PREVIEW_TEXT",
                            ),
                            "PROPERTY_CODE" => array(
                                0 => "ADDITIONAL_LINK",
                                1 => "H1_BOTTOM",
                            ),
                            "IBLOCK_URL" => "",
                            "H1_BOTTOM" => $filter_h1,
                            "META_KEYWORDS" => $filter_keywords,
                            "META_DESCRIPTION" => $filter_description,
                            "BROWSER_TITLE" => $filter_title,
                            "META_PREVIEW_TEXT" => $arkFields['PREVIEW_TEXT'],
                            "SET_TITLE" => "Y",
                            "SET_STATUS_404" => "N",
                            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                            "ADD_SECTIONS_CHAIN" => "N",
                            "ACTIVE_DATE_FORMAT" => "d.m.Y",
                            "USE_PERMISSIONS" => "N",
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "36000000",
                            "CACHE_NOTES" => "",
                            "CACHE_GROUPS" => "Y",
                            "DISPLAY_TOP_PAGER" => "N",
                            "DISPLAY_BOTTOM_PAGER" => "Y",
                            "PAGER_TITLE" => "Страница",
                            "PAGER_TEMPLATE" => "",
                            "PAGER_SHOW_ALL" => "Y",
                            "AJAX_OPTION_JUMP" => "N",
                            "AJAX_OPTION_STYLE" => "Y",
                            "AJAX_OPTION_HISTORY" => "N",
                            "SET_BROWSER_TITLE" => "Y",
                            "SET_META_KEYWORDS" => "Y",
                            "SET_META_DESCRIPTION" => "Y",
                            "ADD_ELEMENT_CHAIN" => "N",
                            "AJAX_OPTION_ADDITIONAL" => "",
                            "COMPONENT_TEMPLATE" => "section",
                            "DETAIL_URL" => "",
                            "SET_CANONICAL_URL" => "N",
                            "SET_LAST_MODIFIED" => "N",
                            "PAGER_BASE_LINK_ENABLE" => "N",
                            "SHOW_404" => "N",
                            "MESSAGE_404" => "",
                            "IMAGE_THUMB_WIDTH" => "870",
                            "IMAGE_THUMB_HEIGHT" => ""
                        ),
                        false
                    );
                }
                // get h1 from $arkFields
                $SEO_TEXT = ob_get_clean();
                $SEO_TEXT = trim($SEO_TEXT);
                $SEO_TEXT = html_entity_decode($SEO_TEXT);
                if (mb_stripos($SEO_TEXT,'<h1') !== false) {
                    list($SEO_TITLE_H1,$SEO_TEXT) = explode('</h1>',$SEO_TEXT);
                    $SEO_TITLE_H1 = strip_tags($SEO_TITLE_H1);
                    $APPLICATION->SetPageProperty('SEO_TITLE_H1', $SEO_TITLE_H1);
                }

                if($page_num){

                    $pagenav_title = $APPLICATION->GetPageProperty('pagenav_title','');

                    if(!empty($pagenav_title)){

                        if(!empty($pag_title)) {

                            if (mb_stripos($pagenav_title, '[pag_title]') !== false) {
                                $pagenav_title = str_ireplace('[pag_title]', $pag_title, $pagenav_title);
                            }

                        } else {

                            $pagenav_title = $APPLICATION->GetPageProperty('pagenav_title_default','');

                            if(mb_stripos($pagenav_title, '[title]') !== false){

                                if(!empty($APPLICATION->GetPageProperty('title',''))){
                                    $pagenav_title = str_ireplace('[title]',$APPLICATION->GetPageProperty('title',''),$pagenav_title);
                                } else {
                                    $pagenav_title = str_ireplace('[title]',$APPLICATION->GetTitle(),$pagenav_title);
                                }

                            }

                        }

                        if(!empty($pagenav_title))
                            $APPLICATION->SetTitle($pagenav_title);

                        if(!empty($pagenav_title))
                            $APPLICATION->SetPageProperty("title", $pagenav_title);

                        $APPLICATION->SetPageProperty('pagenav_title', '');
                        $APPLICATION->SetPageProperty('pagenav_title_default', '');

                    }

                    $pagenav_description = $APPLICATION->GetPageProperty('pagenav_description','');

                    if(!empty($pagenav_description)){

                        if(!empty($pag_description)){

                            if(mb_stripos($pagenav_description, '[pag_description]') !== false){
                                $pagenav_description = str_ireplace('[pag_description]', $pag_description, $pagenav_description);
                            }

                        } else {

                            $pagenav_description = $APPLICATION->GetPageProperty('pagenav_description_default','');

                            if(mb_stripos($pagenav_description, '[description]') !== false) {
                                $pagenav_description = str_ireplace('[description]', $APPLICATION->GetPageProperty('description', ''), $pagenav_description);
                            }

                        }

                        if(!empty($pagenav_description))
                            $APPLICATION->SetPageProperty("description", $pagenav_description);

                        $APPLICATION->SetPageProperty('pagenav_description', '');
                        $APPLICATION->SetPageProperty('pagenav_description_default', '');

                    }

                }

                if(!$page_num)
                    $APPLICATION->SetPageProperty('SEO_TEXT', $SEO_TEXT);

            }
            
        }
        
        public static function printSeoAndSetTitles($intSectionID = 0, $arParams, $cacheTime = 604800, $pCurrPage = '')
        {
            global $APPLICATION;
            $filter_set = $APPLICATION->GetPageProperty('filter_set', false);
            $hasTest = isset($_REQUEST['test']);

            $active_filters = static::getActiveFilters($intSectionID, $arParams, $filter_set);
            $page_num = 0;
            $atDomainLinks = [];

            if (file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/sfilterlinks_domain.php')) {
                require $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/sfilterlinks_domain.php';
            }

            ob_start();

            $arFields  = array();
            $aSkipGlue = array('typeproduct','manufacturer');

            $currPage = empty($pCurrPage) ? $APPLICATION->GetCurPage() : $pCurrPage;
            $currPage = isset($_SERVER['ORIG_REQUEST_URI']) && !empty($_SERVER['ORIG_REQUEST_URI']) ? trim($_SERVER['ORIG_REQUEST_URI']) : $currPage;

            $currPage = trim($currPage);
  
            if($filter_set){
         
                $nofollow_parameter_sizeof = \COption::GetOptionString('my.stat', "nofollow_parameter_sizeof", 0, SITE_ID);
                $b_nofollow = false;

                if($nofollow_parameter_sizeof > 0){

                    for($i = 0; $i < $nofollow_parameter_sizeof; $i ++){

                        $parameter = \COption::GetOptionString('my.stat', "nofollow_parameter_chain".$i, "", SITE_ID);
                        $section = \COption::GetOptionString('my.stat', "nofollow_parameter_section".$i, "", SITE_ID);

                        if((($intSectionID == $section) || (!$section))
                            &&isset($active_filters['=PROPERTY_'.$parameter])
                            &&is_array($active_filters['=PROPERTY_'.$parameter])
                            &&sizeof($active_filters['=PROPERTY_'.$parameter]) > 0) {
                            $APPLICATION->SetPageProperty("robots", "noindex, nofollow");
                        }

                    }

                }

                $obCache = new CPHPCache;
                
                $cacheID = 'infoFilterSection'.static::getCacheHash(IMPEL_SERVER_NAME.$currPage);

                $aFilterUrlsCache = array();

                if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/filters_titles_cache.php')){
                    require $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/filters_titles_cache.php';
                }

                $aFilterEnumCache = array();

                if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/filters_enum_cache.php')){
                    require $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/filters_enum_cache.php';
                }

                if(!(isset($aFilterUrlsCache[IMPEL_SERVER_NAME]) && isset($aFilterUrlsCache[IMPEL_SERVER_NAME][$currPage]))) {
                    

                    if ($obCache->InitCache($cacheTime, $cacheID, "/impel/")) {
                        
                        $tmp = $obCache->GetVars();
                        if (isset($tmp[$cacheID])) {
                            $arkFields = $tmp[$cacheID];
                        }

                    } else {

                        $arSelect = Array(
                            'ID',
                            'NAME',
                            'PROPERTY_SEO_TITLE',
                            'PROPERTY_SEO_DECRIPTION',
                            'PROPERTY_SEO_KEYWORDS',
                            'PROPERTY_SEO_DECRIPTION_PAGEN',
                            'PROPERTY_SEO_TITLE_PAGEN',
                            'PROPERTY_H1_BOTTOM',
                            'PREVIEW_TEXT'
                        );

                        $arFilter = Array(
                            "ACTIVE_DATE" => "Y",
                            "ACTIVE" => "Y",
                            "PROPERTY_FILTER_URL" => $currPage,
                            "PROPERTY_DOMAIN_VALUE" => IMPEL_SERVER_NAME,
                            "IBLOCK_ID" => 45);

                        $res = CIBlockElement::GetList(
                            Array(),
                            $arFilter,
                            false,
                            false,
                            $arSelect
                        );
                    
                        if ($res &&
                            $ob = $res->GetNextElement()) {
                            $arkFields = $ob->GetFields();
                        }
                        if (empty($arkFields)) {

                            if(isset($aFilterUrlsCache[IMPEL_SERVER_NAME])) {
                                foreach ($aFilterUrlsCache[IMPEL_SERVER_NAME] as $urlTemplate => $urlFields) {

                                    if (isset($urlFields['PROPERTY_IS_REGEXP_VALUE'])
                                        && $urlFields['PROPERTY_IS_REGEXP_VALUE']
                                        && @preg_match('~'.$urlTemplate.'~isu',$currPage)
                                    ) {
                                        if ($hasTest) {
                                            echo $urlTemplate . ' - '. $currPage . ' - '.$urlFields['ID'].' <br />';
                                        }

                                        $arkFields = $urlFields;
                                        $isEltId = $urlFields['ID'];
                                        break;

                                    }
                                }
                            }
                        }

                        if (empty($arkFields)) {
                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/sfilterlinks_new.php')
                                && (filesize($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/sfilterlinks_new.php') > 0)
                                && is_readable($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/sfilterlinks_new.php')
                            ) {
                                require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/sfilterlinks_new.php';
                                $hasElt = false;
                                $isEltId = false;
                                $sHelp = '';
                                foreach ($atNewLinks as $iEltId => $aRegExps) {
                                    if (!(isset($atDomainLinks[IMPEL_SERVER_NAME])
                                        && isset($atDomainLinks[IMPEL_SERVER_NAME][$iEltId]))) {
                                        continue;
                                    }
                                    foreach ($aRegExps[0] as $aRregExp) {

                                        $hasElt = false;

                                        foreach ($aRregExp as $sRegExp) {

                                            if (@preg_match('~' . $sRegExp . '~isu', $currPage)) {

                                                if ($hasTest) {
                                                    echo $sRegExp . ' - '. $currPage . ' - '.$iEltId.'<br />';
                                                }

                                                $hasElt = true;
                                                break;
                                            }

                                        }

                                        if (!$hasElt) {
                                            break;
                                        }
                                    }

                                    if ($hasElt) {

                                        foreach ($aRegExps[1] as $aRregExp) {
                                            foreach ($aRregExp as $sRegExp) {
                                                if (@preg_match('~' . $sRegExp . '~isu', $currPage)) {
                                                    $hasElt = false;
                                                    $sHelp = '~' . $sRegExp . '~isu'.$currPage;
                                                }
                                            }

                                        }

                                    }

                                    if ($hasElt) {
                                        $isEltId = $iEltId;
                                        break;
                                    }

                                }
                                
                                if ($isEltId) {

                                    $arFilter = array(
                                        "ACTIVE_DATE" => "Y",
                                        "ACTIVE" => "Y",
                                        "=ID" => $isEltId,
                                        "IBLOCK_ID" => 45);

                                    $res = CIBlockElement::GetList(
                                        array(),
                                        $arFilter,
                                        false,
                                        false,
                                        $arSelect
                                    );

                                    if ($res &&
                                        $ob = $res->GetNextElement()) {
                                        $arkFields = $ob->GetFields();
                                    }
                                }

                            }

                        }

                        $aValues = array();

                        if (!empty($arkFields)) {

                            foreach ($active_filters as $sProp => $aPropVals) {

                                if (sizeof($aPropVals) > 1)
                                    continue;

                                $sPropCode = '';

                                $iPropId = str_ireplace('=PROPERTY_', '', $sProp);
                                $dProps = CIBlockProperty::GetList(
                                    array(),
                                    array(
                                        "IBLOCK_ID" => 11,
                                        "ID" => $iPropId
                                    )
                                );

                                if ($dProps) {

                                    while ($aProps = $dProps->GetNext()) {

                                        $sPropCode = trim(mb_strtolower($aProps['CODE']));

                                        foreach ($aPropVals as $stPropVal) {

                                            $dEnums = CIBlockPropertyEnum::GetList(
                                                array(),
                                                array(
                                                    "PROPERTY_ID" => $aProps["ID"],
                                                    "ID" => $stPropVal
                                                )
                                            );

                                            if ($dEnums) {
                                                while ($aEnums = $dEnums->GetNext()) {
                                                    if (isset($aEnums["VALUE"])) {
                                                        $aValues[$sPropCode] = (trim($aEnums["VALUE"]));
                                                    }
                                                }
                                            }

                                        }

                                    }

                                }

                            }

                            if (!empty($aValues)) {

                                $areplaces = unserialize(\COption::GetOptionString('my.stat', "freplaces_replaces", 0, SITE_ID));
                                $adeclensions = array();

                                foreach ($aValues as $sPropCode => $sPropValue) {
                                    if (isset($areplaces['name']) && !empty($areplaces['name'])) {
                                        foreach ($areplaces['name'] as $snKey => $snValue) {

                                            if (!empty($snValue) && (mb_stripos($sPropValue, $snValue) !== false)) {
                                                $adeclensions[$sPropValue] = str_ireplace($snValue, $areplaces['replace'][$snKey], $sPropValue);
                                            }

                                        }

                                    }

                                }

                                foreach ($arkFields as $sKey => $sValue) {

                                    if (in_array($sKey, array(
                                        'PREVIEW_TEXT',
                                        'PROPERTY_SEO_TITLE_VALUE',
                                        'PROPERTY_SEO_DECRIPTION_VALUE',
                                        'PROPERTY_SEO_KEYWORDS_VALUE',
                                        'PROPERTY_SEO_TITLE_PAGEN_VALUE',
                                        'PROPERTY_SEO_DECRIPTION_PAGEN_VALUE',
                                        'PROPERTY_H1_BOTTOM_VALUE'
                                    ))) {

                                        foreach ($aValues as $sPropCode => $sPropValue) {

                                            preg_match_all('~\[' . $sPropCode . '\:([^\]]+?)\]~isu', $arkFields[$sKey], $aMatches);

                                            foreach ($aMatches[1] as $smKey => $sSearch) {

                                                $sFound = $sSearch;

                                                $sFound = str_ireplace('{value}', $sPropValue, $sFound);

                                                if (mb_stripos($sFound, '{decl}') !== false) {
                                                    if (isset($adeclensions[$sPropValue]))
                                                        $sFound = str_ireplace('{decl}', $adeclensions[$sPropValue], $sFound);
                                                    else
                                                        $sFound = str_ireplace('{decl}', '', $sFound);
                                                }

                                                $arkFields[$sKey] = str_ireplace($aMatches[0][$smKey], $sFound, $arkFields[$sKey]);



                                            }

                                        }

                                        $arkFields['~'.$sKey] = $arkFields[$sKey] = preg_replace('~\[[^\:]+?\:([^\]]+?)\]~isu', "", $arkFields[$sKey]);

                                    }

                                }

                            }

                        }

                        if ($obCache->StartDataCache()) {

                            $obCache->EndDataCache(
                                array(
                                    $cacheID => $arkFields
                                )
                            );

                        };

                    };

                } else {

                    $arkFields = $aFilterUrlsCache[IMPEL_SERVER_NAME][$currPage];
                }

                if ($hasTest) {
                    print_r($isEltId);
                }

                {

                    $skip_tmpl_check = isset($active_filters['=PROPERTY_46'])
                    &&is_array($active_filters['=PROPERTY_46'])
                    &&sizeof($active_filters['=PROPERTY_46']) > 1
                        ? true
                        : false;

                    $skip_tmpl_check = false;

                    $is_only_manufacturer = false;

                    $is_other = $intSectionID == 69 ? true : false;

                    $is_manufacturer = isset($active_filters['=PROPERTY_44'])
                    &&is_array($active_filters['=PROPERTY_44'])
                    &&sizeof($active_filters['=PROPERTY_44']) > 1
                    &&!isset($active_filters['=PROPERTY_46'])
                        ? true
                        : false;

                    $is_only_manufacturer = ((isset($active_filters['=PROPERTY_44'])
                            &&is_array($active_filters['=PROPERTY_44'])
                            &&sizeof($active_filters['=PROPERTY_44']) == 1) || (isset($active_filters['=PROPERTY_243'])
                            &&is_array($active_filters['=PROPERTY_243'])
                            &&sizeof($active_filters['=PROPERTY_243']) == 1))
                    &&!isset($active_filters['=PROPERTY_46'])
                        ? true
                        : false;

                    $filter_parameter = array();
                    $filter_parameter_sizeof = \COption::GetOptionString('my.stat', "filter_parameter_sizeof", 0, SITE_ID);

                    if($filter_parameter_sizeof > 0){
                        for($i = 0; $i < $filter_parameter_sizeof; $i ++){

                            $filter_parameter['code'][$i] = \COption::GetOptionString('my.stat', "filter_parameter_id".$i, "", SITE_ID);
                            $filter_parameter['value'][$i] = \COption::GetOptionString('my.stat', "filter_parameter_value".$i, "", SITE_ID);

                        }
                    }

                    $for_union_sections = '';
                    $for_union_sections_nc = '';

                    $keys = array();
                    $keys_nc = array();
                    $keys_tmpl = array();

                    $arByFilter = array('keys' => '', 'keys_nc' => '',  'section' => '', 'section_nc' => '');

                    $categoryPath = $filterURL = preg_replace('~(.*)filter.*$~isu',"$1",$currPage);

                    $filters_glue_description = \COption::GetOptionString('my.stat', 'filters_glue_description', ' ', SITE_ID);
                    $prevStartFrom = '';

                    if(mb_stripos($currPage,'filter/') !== false){

                        $obCache = new CPHPCache;
                        $cacheID = 'infoFiltersSection'.static::getCacheHash($currPage);

                        if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

                            $tmp = array();
                            $tmp = $obCache->GetVars();

                            if(isset($tmp[$cacheID])){
                                $arByFilter = $tmp[$cacheID];
                            }
                        } else {
                            $filterURL = preg_replace('~.*filter/(.*)$~isu',"$1",$currPage);
                            $filterURL = trim($filterURL);

                            if(!empty($filterURL)){

                                if(mb_stripos($filterURL,'/') !== false){

                                    $filterURLs = explode('/',$filterURL);

                                } else {
                                    $filterURLs = array($filterURL);
                                }

                                foreach($filterURLs as $smartPart){

                                    $smartPart = preg_split("/-(is|or)-/", $smartPart, -1, PREG_SPLIT_DELIM_CAPTURE);
                                    $startParts = false;

                                    if(is_array($smartPart) && sizeof($smartPart) > 0){
                                        $startFrom = $smartPart[0];

                                        foreach($smartPart as $smartElement){

                                            if($smartElement == 'is'){
                                                $startParts = true;
                                            }

                                            if($startParts
                                                && $smartElement != 'or'
                                                && $smartElement != 'is'){

                                                $checkFilter = $categoryPath.'filter/'.$startFrom.'-is-'.$smartElement.'/';

                                                if(($skip_tmpl_check && $startFrom == 'typeproduct')
                                                    || !$skip_tmpl_check){

                                                    $dValue = '';

                                                    $checkFilter = trim($checkFilter);
                                                    $arFields = array();

                                                    if(!isset($keys[$startFrom])){
                                                        $keys[$startFrom] = array();
                                                    }

                                                    if(!isset($keys_nc[$startFrom])){
                                                        $keys_nc[$startFrom] = array();
                                                    }

                                                    if(!isset($keys_tmpl[$startFrom])) {
                                                        $keys_tmpl[$startFrom] = '';
                                                    }

                                                    if(!isset($aFilterUrlsCache[IMPEL_SERVER_NAME][$checkFilter])) {
                                                        $arSelect = Array(
                                                            'PROPERTY_FOR_UNION_FILTERS',
                                                            'PROPERTY_FOR_UNION_FILTERS_NC'
                                                        );

                                                        $arFilter = Array(
                                                            "ACTIVE_DATE" => "Y",
                                                            "ACTIVE" => "Y",
                                                            "PROPERTY_FILTER_URL" => $checkFilter,
                                                            "PROPERTY_DOMAIN_VALUE" => IMPEL_SERVER_NAME,
                                                            "IBLOCK_ID" => 45);

                                                        $res = CIBlockElement::GetList(
                                                            Array(),
                                                            $arFilter,
                                                            false,
                                                            false,
                                                            $arSelect
                                                        );
                                                        if($res) {
                                                            $arFields = $res->GetNext();
                                                        }

                                                    } else {
                                                        $arFields = $aFilterUrlsCache[IMPEL_SERVER_NAME][$checkFilter];

                                                    }

                                                    if (!empty($arFields["PROPERTY_FOR_UNION_FILTERS_VALUE"])) {

                                                        $keys[$startFrom][] = $arFields["PROPERTY_FOR_UNION_FILTERS_VALUE"];
                                                        $keys_tmpl[$startFrom] = (!empty($prevStartFrom) && !in_array($prevStartFrom,$aSkipGlue) && !in_array($startFrom,$aSkipGlue) ? $filters_glue_description : (!empty($prevStartFrom) ? ' ' : '')) . '[values]';

                                                    } else {

                                                        $startFrom = trim($startFrom);
                                                        $smartElement = trim($smartElement);

                                                        if(isset($aFilterEnumCache[$startFrom][$smartElement])){

                                                            $dValue = current($aFilterEnumCache[$startFrom][$smartElement]);


                                                        } else {

                                                            if(empty($dValue)){

                                                                $peDB = CIBlockPropertyEnum::GetList(
                                                                    Array(
                                                                        "DEF" => "DESC",
                                                                        "SORT" => "ASC"),
                                                                    Array(
                                                                        "XML_ID" => ($smartElement),
                                                                        "CODE" => ($startFrom)

                                                                    )
                                                                );

                                                                if($peDB && ($peArr = $peDB->GetNext())){
                                                                    $dValue = isset($peArr["VALUE"]) ? trim($peArr["VALUE"]) : '';
                                                                }

                                                            }

                                                        }

                                                        $keys[$startFrom][] = $dValue;

                                                    }

                                                    if(!empty($arFields["PROPERTY_FOR_UNION_FILTERS_NC_VALUE"])) {

                                                        $keys_nc[$startFrom][] = $arFields["PROPERTY_FOR_UNION_FILTERS_NC_VALUE"];
                                                        $keys_tmpl[$startFrom] = (!empty($prevStartFrom) && !in_array($prevStartFrom,$aSkipGlue) && !in_array($startFrom,$aSkipGlue) ? $filters_glue_description : (!empty($prevStartFrom) ? ' ' : '')).'[values]';

                                                    } else {

                                                        $startFrom = trim($startFrom);
                                                        $smartElement = trim($smartElement);

                                                        if(isset($aFilterEnumCache[$startFrom][$smartElement])){

                                                            $dValue = current($aFilterEnumCache[$startFrom][$smartElement]);

                                                        } else {

                                                            if(empty($dValue)){

                                                                $peDB = CIBlockPropertyEnum::GetList(
                                                                    Array(
                                                                        "DEF" => "DESC",
                                                                        "SORT" => "ASC"),
                                                                    Array(
                                                                        "XML_ID" => ($smartElement),
                                                                        "CODE" => ($startFrom)

                                                                    )
                                                                );

                                                                if($peDB && ($peArr = $peDB->GetNext())){
                                                                    $dValue = isset($peArr["VALUE"]) ? trim($peArr["VALUE"]) : '';
                                                                }

                                                            }

                                                        }

                                                        $keys_nc[$startFrom][] = $dValue;

                                                    }

                                                    if(!$skip_tmpl_check){

                                                        $key_found = is_array($filter_parameter['code']) && array_search($startFrom, $filter_parameter['code']);
                                                        if($key_found !== false){
                                                            $keys_tmpl[$startFrom] = (!empty($prevStartFrom) && !in_array($prevStartFrom,$aSkipGlue) && !in_array($startFrom,$aSkipGlue) ? $filters_glue_description : (!empty($prevStartFrom) ? ' ' : '')).$filter_parameter['value'][$key_found];
                                                        }

                                                    }

                                                }

                                            }

                                        }

                                    }

                                    $prevStartFrom = $startFrom;

                                }

                            }

                            if(!empty($keys)){

                                if (!empty($intSectionID)) {

                                    $sectionResult = CIBlockSection::GetList(
                                        array(
                                            "SORT" =>"ASC"
                                        ),
                                        array(
                                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                            "ID" => (int)$intSectionID
                                        ),
                                        false,
                                        $arSelect = array("UF_DESC_RU")
                                    );

                                    if(is_object($sectionResult)
                                        && method_exists($sectionResult,"GetNext")){

                                        while ($sectionProp = $sectionResult->GetNext()){

                                            $product_id = false;

                                            if(isset($sectionProp["UF_DESC_RU"])
                                                && !empty($sectionProp["UF_DESC_RU"])){
                                                $product_id = (int)$sectionProp["UF_DESC_RU"];
                                            }

                                            if($product_id
                                                && !empty($product_id)){

                                                if(isset($afLinesToId[$product_id])
                                                    && isset($aFilterUrlsCache[IMPEL_SERVER_NAME][$afLinesToId[$product_id]])){

                                                    $for_union_sections = $aFilterUrlsCache[IMPEL_SERVER_NAME][$afLinesToId[$product_id]]['PROPERTY_FOR_UNION_SECTIONS_VALUE'];
                                                    $for_union_sections_nc = $aFilterUrlsCache[IMPEL_SERVER_NAME][$afLinesToId[$product_id]]['PROPERTY_FOR_UNION_SECTIONS_VALUE'];

                                                } else {

                                                    $dbBlockResult = CIBlockElement::GetProperty(
                                                        45,
                                                        $product_id,
                                                        Array(),
                                                        Array(
                                                            "CODE" => "FOR_UNION_SECTIONS"
                                                        )
                                                    );

                                                    if($dbBlockResult){

                                                        while($arBlockResult = $dbBlockResult->Fetch()){

                                                            $for_union_sections = trim($arBlockResult["VALUE"]);

                                                        };

                                                    };

                                                    $dbBlockResult = CIBlockElement::GetProperty(
                                                        45,
                                                        $product_id,
                                                        Array(),
                                                        Array(
                                                            "CODE" => "FOR_UNION_SECTIONS_NC"
                                                        )
                                                    );

                                                    if($dbBlockResult){

                                                        while($arBlockResult = $dbBlockResult->Fetch()){

                                                            $for_union_sections_nc = trim($arBlockResult["VALUE"]);

                                                        };

                                                    };

                                                }

                                            }

                                        }

                                    }

                                }

                                $arByFilter = array('keys' => $keys, 'keys_nc' => $keys_nc, 'section' => $for_union_sections, 'section_nc' => $for_union_sections_nc, 'keys_tmpl' => $keys_tmpl);

                            }


                            if($obCache->StartDataCache()){

                                $obCache->EndDataCache(
                                    array(
                                        $cacheID => $arByFilter
                                    )
                                );

                            };

                        }

                    }

                    if(!empty($arByFilter['keys'])){

                        $filter_title = \COption::GetOptionString('my.stat', 'filter_title', '', SITE_ID);
                        $filter_h1 = \COption::GetOptionString('my.stat', 'filter_h1', '', SITE_ID);
                        $filter_description = \COption::GetOptionString('my.stat', 'filter_description', '', SITE_ID);
                        $filter_keywords = \COption::GetOptionString('my.stat', 'filter_keywords', '', SITE_ID);

                        if($is_manufacturer){

                            $filter_title = \COption::GetOptionString('my.stat', 'filter_manufacturer_title', '', SITE_ID);
                            $filter_h1 = \COption::GetOptionString('my.stat', 'filter_manufacturer_h1', '', SITE_ID);
                            $filter_description = \COption::GetOptionString('my.stat', 'filter_manufacturer_description', '', SITE_ID);
                            $filter_keywords = \COption::GetOptionString('my.stat', 'filter_manufacturer_keywords', '', SITE_ID);

                        }

                        if($is_only_manufacturer){

                            $filter_mtitle = \COption::GetOptionString('my.stat', 'manufacturer_filter_title', '', SITE_ID);
                            $filter_mdesc = \COption::GetOptionString('my.stat', 'manufacturer_filter_description', '', SITE_ID);
                            $filter_mh1 = \COption::GetOptionString('my.stat', 'manufacturer_filter_h1', '', SITE_ID);

                        }

                        if($is_other) {

                            $filter_otitle = \COption::GetOptionString('my.stat', 'other_filter_title', '', SITE_ID);
                            $filter_odesc = \COption::GetOptionString('my.stat', 'other_filter_description', '', SITE_ID);
                            $filter_oh1 = \COption::GetOptionString('my.stat', 'other_filter_h1', '', SITE_ID);

                        }

                        if (isset($arkFields['PROPERTY_SEO_TITLE_VALUE']) && !empty($arkFields['PROPERTY_SEO_TITLE_VALUE'])) {
                            $filter_otitle = $filter_mtitle = $filter_title = trim($arkFields['PROPERTY_SEO_TITLE_VALUE']);
                        }

                        if (isset($arkFields['PROPERTY_H1_BOTTOM_VALUE']) && !empty($arkFields['PROPERTY_H1_BOTTOM_VALUE'])) {
                            $filter_oh1 = $filter_mh1 = $filter_h1 = trim($arkFields['PROPERTY_H1_BOTTOM_VALUE']);
                        }

                        if (isset($arkFields['PROPERTY_SEO_DECRIPTION_VALUE']) && !empty($arkFields['PROPERTY_SEO_DECRIPTION_VALUE'])) {
                            $filter_odesc = $filter_mdesc = $filter_description = trim($arkFields['PROPERTY_SEO_DECRIPTION_VALUE']);
                        }

                        if (isset($arkFields['PROPERTY_SEO_KEYWORDS_VALUE']) && !empty($arkFields['PROPERTY_SEO_KEYWORDS_VALUE'])) {
                            $filter_keywords = trim($arkFields['PROPERTY_SEO_KEYWORDS_VALUE']);
                        }

                        $page_num = static::getPageNum();

                        if($page_num){

                            $filter_title = \COption::GetOptionString('my.stat', 'pagenav_filter_title', '', SITE_ID);

                            if($is_manufacturer){
                                $filter_title = \COption::GetOptionString('my.stat', 'pagenav_filter_manufacturer_title', '', SITE_ID);
                            }

                            if($is_only_manufacturer){
                                $filter_mtitle = \COption::GetOptionString('my.stat', 'manufacturer_pagenav_title_default', '', SITE_ID);
                            }

                            if($is_other){
                                $filter_otitle = \COption::GetOptionString('my.stat', 'other_pagenav_title_default', '', SITE_ID);
                            }

                            if ($is_only_manufacturer) {
                                $filter_mdesc = \COption::GetOptionString('my.stat', 'pagenav_filter_manufacturer_description', '', SITE_ID);
                            }

                            $filter_description = $pagenav_description = \COption::GetOptionString('my.stat', 'pagenav_filter_description', '', SITE_ID);

                            if ($is_manufacturer) {
                                $filter_description = $pagenav_description = \COption::GetOptionString('my.stat', 'pagenav_filter_manufacturer_description', '', SITE_ID);
                            }

                            if (isset($arkFields['PROPERTY_SEO_TITLE_VALUE']) && !empty($arkFields['PROPERTY_SEO_TITLE_VALUE'])) {
                                $filter_otitle = $filter_mtitle = $filter_title = trim($arkFields['PROPERTY_SEO_TITLE_VALUE']);
                            }

                            if (isset($arkFields['PROPERTY_SEO_DECRIPTION_VALUE']) && !empty($arkFields['PROPERTY_SEO_DECRIPTION_VALUE'])) {
                                $filter_mdesc = $filter_odesc = $filter_description = trim($arkFields['PROPERTY_SEO_DECRIPTION_VALUE']);
                            }

                            if (isset($arkFields['PROPERTY_SEO_TITLE_PAGEN_VALUE']) && !empty($arkFields['PROPERTY_SEO_TITLE_PAGEN_VALUE'])) {
                                $filter_otitle = $filter_mtitle = $filter_title = trim($arkFields['PROPERTY_SEO_TITLE_PAGEN_VALUE']);
                            }

                            if (isset($arkFields['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE']) && !empty($arkFields['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE'])) {
                                $filter_mdesc = $filter_odesc = $filter_description = trim($arkFields['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE']);
                            }

                            $filter_description = str_ireplace('[pagenum]',$page_num,$filter_description);

                            $filter_title = str_ireplace('[pagenum]',$page_num,$filter_title);

                            $filter_mtitle = str_ireplace('[pagenum]',$page_num,$filter_mtitle);
                            $filter_mdesc = str_ireplace('[pagenum]',$page_num,$filter_mdesc);

                            $filter_otitle = str_ireplace('[pagenum]',$page_num,$filter_otitle);
                            $filter_odesc = str_ireplace('[pagenum]',$page_num,$filter_odesc);

                        }

                        $keys = '';
                        $keys_nc = '';
                        $manufacturers = '';
                        $manufacturers_nc = '';

                        $keys_tmpl = $arByFilter['keys_tmpl'];

                        if(isset($keys_tmpl['manufacturer'])){

                            $startFrom = 'manufacturer';

                            $keys_value = $keys_tmpl[$startFrom];

                            unset($keys_tmpl[$startFrom]);

                            $arByFilter['keys'][$startFrom] = array_filter($arByFilter['keys'][$startFrom],function($var){
                                return in_array(mb_strtolower($var),array('да','нет')) ? false : true;
                            });

                            $ckeys = join(', ',array_unique($arByFilter['keys'][$startFrom]));
                            $ckeys = str_ireplace('[values]',$ckeys,$keys_value);
                            $manufacturers = $ckeys;

                            $arByFilter['keys_nc'][$startFrom] = empty($arByFilter['keys_nc'][$startFrom]) ? $arByFilter['keys'][$startFrom] : $arByFilter['keys_nc'][$startFrom];

                            $arByFilter['keys_nc'][$startFrom] = array_filter($arByFilter['keys_nc'][$startFrom],function($var){
                                return in_array(mb_strtolower($var),array('да','нет')) ? false : true;
                            });

                            $ckeys_nc = join(', ',array_unique($arByFilter['keys_nc'][$startFrom]));
                            $ckeys_nc = str_ireplace('[values]',$ckeys_nc,$keys_value);
                            $manufacturers_nc = $ckeys_nc;

                        }

                        foreach($keys_tmpl as $startFrom => $keys_value){

                            if($startFrom == 'onstock'){

                                $bOnStock = current($arByFilter['keys'][$startFrom]);

                                if(mb_strtolower($bOnStock) == 'да'){
                                    $keys_value = GetMessage('TMPL_FILTER_IN_STOCK');
                                } else {
                                    $keys_value = GetMessage('TMPL_FILTER_OUT_STOCK');
                                }

                            }

                            $arByFilter['keys'][$startFrom] = array_filter($arByFilter['keys'][$startFrom],function($var){
                                return in_array(mb_strtolower($var),array('да','нет')) ? false : true;
                            });

                            $ckeys = join(', ',array_unique($arByFilter['keys'][$startFrom]));
                            $ckeys = str_ireplace('[values]',$ckeys,$keys_value);
                            $keys .= $ckeys;

                            $arByFilter['keys_nc'][$startFrom] = empty($arByFilter['keys_nc'][$startFrom]) ? $arByFilter['keys'][$startFrom] : $arByFilter['keys_nc'][$startFrom];

                            $arByFilter['keys_nc'][$startFrom] = array_filter($arByFilter['keys_nc'][$startFrom],function($var){
                                return in_array(mb_strtolower($var),array('да','нет')) ? false : true;
                            });


                            $ckeys_nc = join(', ',array_unique($arByFilter['keys_nc'][$startFrom]));
                            $ckeys_nc = str_ireplace('[values]',$ckeys_nc,$keys_value);
                            $keys_nc .= $ckeys_nc;

                        }

                        $keys = mb_strtolower($keys);
                        $keys_nc = mb_strtolower($keys_nc);
                        $manufacturers = mb_strtolower($manufacturers);
                        $manufacturers_nc = mb_strtolower($manufacturers_nc);

                        if(mb_stripos($filter_title,'[min_price]') !== false
                            || mb_stripos($filter_h1,'[min_price]') !== false
                            || mb_stripos($filter_description,'[min_price]') !== false
                            || mb_stripos($filter_keywords,'[min_price]') !== false
                        ) {

                            if(!empty($active_filters)) {

                                $min_price = static::getFiltersMinPrice($active_filters,$intSectionID,$arParams);
                            }

                            $filter_title = str_ireplace('[min_price]', $min_price, $filter_title);
                            $filter_h1 = str_ireplace('[min_price]', $min_price, $filter_h1);
                            $filter_description = str_ireplace('[min_price]', $min_price, $filter_description);
                            $filter_keywords = str_ireplace('[min_price]', $min_price, $filter_keywords);

                        }

                        if(mb_stripos($filter_mtitle,'[min_price]') !== false
                            || mb_stripos($filter_mh1,'[min_price]') !== false
                            || mb_stripos($filter_mdesc,'[min_price]') !== false

                            || mb_stripos($filter_otitle,'[min_price]') !== false
                            || mb_stripos($filter_oh1,'[min_price]') !== false
                            || mb_stripos($filter_odesc,'[min_price]') !== false

                        ) {

                            if(!empty($active_filters)) {

                                $min_price = static::getFiltersMinPrice($active_filters,$intSectionID,$arParams);
                            }

                            $filter_mtitle = str_ireplace('[min_price]', $min_price, $filter_mtitle);
                            $filter_mh1 = str_ireplace('[min_price]', $min_price, $filter_mh1);
                            $filter_mdesc = str_ireplace('[min_price]', $min_price, $filter_mdesc);

                            $filter_otitle = str_ireplace('[min_price]', $min_price, $filter_otitle);
                            $filter_oh1 = str_ireplace('[min_price]', $min_price, $filter_oh1);
                            $filter_odesc = str_ireplace('[min_price]', $min_price, $filter_odesc);

                        }

                        $filter_title = str_ireplace('[manufacturers]', $manufacturers, $filter_title);
                        $filter_h1 = str_ireplace('[manufacturers]', $manufacturers, $filter_h1);

                        $filter_mtitle = str_ireplace('[manufacturers]', $manufacturers, $filter_mtitle);
                        $filter_mh1 = str_ireplace('[manufacturers]', $manufacturers, $filter_mh1);
                        $filter_mdesc = str_ireplace('[manufacturers]', $manufacturers, $filter_mdesc);

                        $filter_otitle = str_ireplace('[manufacturers]', $manufacturers, $filter_otitle);
                        $filter_oh1 = str_ireplace('[manufacturers]', $manufacturers, $filter_oh1);
                        $filter_odesc = str_ireplace('[manufacturers]', $manufacturers, $filter_odesc);


                        $filter_description = str_ireplace('[manufacturers]', $manufacturers, $filter_description);
                        $filter_keywords = str_ireplace('[manufacturers]', $manufacturers, $filter_keywords);

                        $filter_title = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_title);
                        $filter_h1 = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_h1);

                        $filter_mtitle = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_mtitle);
                        $filter_mh1 = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_mh1);
                        $filter_mdesc = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_mdesc);

                        $filter_otitle = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_otitle);
                        $filter_oh1 = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_oh1);
                        $filter_odesc = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_odesc);

                        $filter_description = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_description);
                        $filter_keywords = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_keywords);

                        $filter_title = str_ireplace('[keys]', $keys, $filter_title);
                        $filter_h1 = str_ireplace('[keys]', $keys, $filter_h1);

                        $filter_mtitle = str_ireplace('[keys]', $keys, $filter_mtitle);
                        $filter_mh1 = str_ireplace('[keys]', $keys, $filter_mh1);
                        $filter_mdesc = str_ireplace('[keys]', $keys, $filter_mdesc);

                        $filter_otitle = str_ireplace('[keys]', $keys, $filter_otitle);
                        $filter_oh1 = str_ireplace('[keys]', $keys, $filter_oh1);
                        $filter_odesc = str_ireplace('[keys]', $keys, $filter_odesc);

                        $filter_description = str_ireplace('[keys]', $keys, $filter_description);
                        $filter_keywords = str_ireplace('[keys]', $keys, $filter_keywords);

                        $filter_title = str_ireplace('[keys_nc]', $keys_nc, $filter_title);
                        $filter_h1 = str_ireplace('[keys_nc]', $keys_nc, $filter_h1);

                        $filter_mtitle = str_ireplace('[keys_nc]', $keys_nc, $filter_mtitle);
                        $filter_mh1 = str_ireplace('[keys_nc]', $keys_nc, $filter_mh1);
                        $filter_mdesc = str_ireplace('[keys_nc]', $keys_nc, $filter_mdesc);

                        $filter_otitle = str_ireplace('[keys_nc]', $keys_nc, $filter_otitle);
                        $filter_oh1 = str_ireplace('[keys_nc]', $keys_nc, $filter_oh1);
                        $filter_odesc = str_ireplace('[keys_nc]', $keys_nc, $filter_odesc);

                        $filter_description = str_ireplace('[keys_nc]', $keys_nc, $filter_description);
                        $filter_keywords = str_ireplace('[keys_nc]', $keys_nc, $filter_keywords);

                        $filter_title = str_ireplace('[section]', $arByFilter['section'], $filter_title);
                        $filter_h1 = str_ireplace('[section]', $arByFilter['section'], $filter_h1);

                        $filter_mtitle = str_ireplace('[section]', $arByFilter['section'], $filter_mtitle);
                        $filter_mh1 = str_ireplace('[section]', $arByFilter['section'], $filter_mh1);
                        $filter_mdesc = str_ireplace('[section]', $arByFilter['section'], $filter_mdesc);

                        $filter_otitle = str_ireplace('[section]', $arByFilter['section'], $filter_otitle);
                        $filter_oh1 = str_ireplace('[section]', $arByFilter['section'], $filter_oh1);
                        $filter_odesc = str_ireplace('[section]', $arByFilter['section'], $filter_odesc);

                        $filter_description = str_ireplace('[section]', $arByFilter['section'], $filter_description);
                        $filter_keywords = str_ireplace('[section]', $arByFilter['section'], $filter_keywords);

                        $filter_title = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_title);
                        $filter_h1 = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_h1);

                        $filter_mtitle = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_mtitle);
                        $filter_mh1 = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_mh1);
                        $filter_mdesc = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_mdesc);

                        $filter_otitle = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_otitle);
                        $filter_oh1 = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_oh1);
                        $filter_odesc = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_odesc);

                        $filter_description = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_description);
                        $filter_keywords = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_keywords);

                        $filter_title = static::firstCharToUpper($filter_title);
                        $filter_h1 = static::firstCharToUpper($filter_h1);

                        $filter_mtitle = static::firstCharToUpper($filter_mtitle);
                        $filter_mh1 = static::firstCharToUpper($filter_mh1);
                        $filter_mdesc = static::firstCharToUpper($filter_mdesc);

                        $filter_otitle = static::firstCharToUpper($filter_otitle);
                        $filter_oh1 = static::firstCharToUpper($filter_oh1);
                        $filter_odesc = static::firstCharToUpper($filter_odesc);

                        if($is_only_manufacturer){

                            $btStop = false;
                            $bdStop = false;
                            $bmStop = false;

                            $filter_mstop = \COption::GetOptionString('my.stat', 'manufacturer_stop', '', SITE_ID);
                            $filter_mstop = trim($filter_mstop);

                            if(!empty($filter_mstop)){

                                if(mb_stripos($filter_mstop,',') !== false) {
                                    $filter_mstop = explode(',',$filter_mstop);
                                } else {
                                    $filter_mstop = array($filter_mstop);
                                }

                                $filter_mstop = array_unique($filter_mstop);
                                $filter_mstop = array_filter($filter_mstop);

                                $filter_sdesc = \COption::GetOptionString('my.stat', 'manufacturer_filter_description', '', SITE_ID);
                                $filter_stitle = \COption::GetOptionString('my.stat', 'manufacturer_filter_title', '', SITE_ID);
                                $filter_sh1 = \COption::GetOptionString('my.stat', 'manufacturer_filter_h1', '', SITE_ID);

                                foreach($filter_mstop as $sWord){


                                    $mCount = substr_count($filter_stitle, $sWord);

                                    if(substr_count($filter_mtitle, $sWord) > $mCount) {
                                        $btStop = true;
                                    }

                                    $mCount = substr_count($filter_sdesc, $sWord);

                                    if(substr_count($filter_mdesc, $sWord) > $mCount) {
                                        $bdStop = true;
                                    }

                                    $mCount = substr_count($filter_sh1, $sWord);

                                    if(substr_count($filter_mh1, $sWord) > $mCount) {
                                        $bmStop = true;
                                    }

                                }

                            }

                            if(!$bdStop){
                                $filter_description = $filter_mdesc;
                            }

                            if(!$btStop) {
                                $filter_title = $filter_mtitle;
                            }

                            if(!$bmStop) {
                                $filter_h1 = $filter_mh1;
                            }


                        }

                        if($is_other){

                            $btStop = false;
                            $bdStop = false;
                            $bmStop = false;

                            $filter_ostop = \COption::GetOptionString('my.stat', 'manufacturer_stop', '', SITE_ID);
                            $filter_ostop = trim($filter_ostop);

                            if(!empty($filter_ostop)){

                                if(mb_stripos($filter_ostop,',') !== false) {
                                    $filter_ostop = explode(',',$filter_ostop);
                                } else {
                                    $filter_ostop = array($filter_ostop);
                                }

                                $filter_ostop = array_unique($filter_ostop);
                                $filter_ostop = array_filter($filter_ostop);

                                $filter_sdesc = \COption::GetOptionString('my.stat', 'other_filter_description', '', SITE_ID);
                                $filter_stitle = \COption::GetOptionString('my.stat', 'other_filter_title', '', SITE_ID);
                                $filter_sh1 = \COption::GetOptionString('my.stat', 'other_filter_h1', '', SITE_ID);

                                foreach($filter_ostop as $sWord){

                                    $mCount = substr_count($filter_stitle, $sWord);

                                    if(substr_count($filter_otitle, $sWord) > $mCount) {
                                        $btStop = true;
                                    }

                                    $mCount = substr_count($filter_sdesc, $sWord);

                                    if(substr_count($filter_odesc, $sWord) > $mCount) {
                                        $bdStop = true;
                                    }

                                    $mCount = substr_count($filter_sh1, $sWord);

                                    if(substr_count($filter_oh1, $sWord) > $mCount) {
                                        $bmStop = true;
                                    }

                                }

                            }

                            if(!$bdStop){
                                $filter_description = $filter_odesc;
                            }

                            if(!$btStop) {
                                $filter_title = $filter_otitle;
                            }

                            if(!$bmStop) {
                                $filter_h1 = $filter_oh1;
                            }


                        }


                        $filter_description = static::firstCharToUpper($filter_description);
                        $filter_keywords = static::firstCharToUpper($filter_keywords);

                        $filter_keywords = preg_replace('~\s+?\s+~isu',' ',$filter_keywords);
                        $filter_description = preg_replace('~\s+?\s+~isu',' ',$filter_description);
                        $filter_title = preg_replace('~\s+?\s+~isu',' ',$filter_title);
                        $filter_h1 = preg_replace('~\s+?\s+~isu',' ',$filter_h1);

                        if(!empty($pCurrPage)) {
                            $APPLICATION->SetPageProperty('page_keywords', $filter_keywords);
                            $APPLICATION->SetPageProperty('page_title', $filter_title);
                            $APPLICATION->SetPageProperty('page_description', $filter_description);
                        }

                        if(!empty($filter_keywords))
                            $APPLICATION->SetPageProperty("keywords", $filter_keywords);

                        if(!empty($filter_description))
                            $APPLICATION->SetPageProperty("description", $filter_description);

                        if(!empty($filter_title))
                            $APPLICATION->SetTitle($filter_title);

                        if(!empty($filter_title))
                            $APPLICATION->SetPageProperty("title", $filter_title);

                        if(!empty($filter_h1))
                            $APPLICATION->SetPageProperty("ADDITIONAL_H1", $filter_h1);

                        if(!empty($arkFields)) {

                            $APPLICATION->SetPageProperty("ADDITIONAL_TITLE", $arkFields['NAME']);

                            $APPLICATION->IncludeComponent(
                                "bitrix:news.detail",
                                "nsection",
                                array(
                                    "DISPLAY_DATE" => "Y",
                                    "DISPLAY_NAME" => "Y",
                                    "DISPLAY_PICTURE" => "Y",
                                    "DISPLAY_PREVIEW_TEXT" => "Y",
                                    "USE_SHARE" => "N",
                                    "AJAX_MODE" => "N",
                                    "IBLOCK_TYPE" => "catalog",
                                    "IBLOCK_ID" => $arkFields['IBLOCK_ID'],
                                    "ELEMENT_ID" => $arkFields['ID'],
                                    "ELEMENT_CODE" => "",
                                    "CHECK_DATES" => "Y",
                                    "FIELD_CODE" => array(
                                        0 => "NAME",
                                        1 => "PREVIEW_PICTURE",
                                        2 => "PREVIEW_TEXT",
                                    ),
                                    "PROPERTY_CODE" => array(
                                        0 => "ADDITIONAL_LINK",
                                        1 => "H1_BOTTOM",
                                    ),
                                    "IBLOCK_URL" => "",
                                    "H1_BOTTOM" => $filter_h1,
                                    "META_KEYWORDS" => $filter_keywords,
                                    "META_DESCRIPTION" => $filter_description,
                                    "BROWSER_TITLE" => $filter_title,
                                    "META_PREVIEW_TEXT" => $arkFields['PREVIEW_TEXT'],
                                    "SET_TITLE" => "Y",
                                    "SET_STATUS_404" => "N",
                                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                                    "ADD_SECTIONS_CHAIN" => "N",
                                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                                    "USE_PERMISSIONS" => "N",
                                    "CACHE_TYPE" => "A",
                                    "CACHE_TIME" => "36000000",
                                    "CACHE_NOTES" => "",
                                    "CACHE_GROUPS" => "Y",
                                    "DISPLAY_TOP_PAGER" => "N",
                                    "DISPLAY_BOTTOM_PAGER" => "Y",
                                    "PAGER_TITLE" => "Страница",
                                    "PAGER_TEMPLATE" => "",
                                    "PAGER_SHOW_ALL" => "Y",
                                    "AJAX_OPTION_JUMP" => "N",
                                    "AJAX_OPTION_STYLE" => "Y",
                                    "AJAX_OPTION_HISTORY" => "N",
                                    "SET_BROWSER_TITLE" => "Y",
                                    "SET_META_KEYWORDS" => "Y",
                                    "SET_META_DESCRIPTION" => "Y",
                                    "ADD_ELEMENT_CHAIN" => "N",
                                    "AJAX_OPTION_ADDITIONAL" => "",
                                    "COMPONENT_TEMPLATE" => "section",
                                    "DETAIL_URL" => "",
                                    "SET_CANONICAL_URL" => "N",
                                    "SET_LAST_MODIFIED" => "N",
                                    "PAGER_BASE_LINK_ENABLE" => "N",
                                    "SHOW_404" => "N",
                                    "MESSAGE_404" => "",
                                    "IMAGE_THUMB_WIDTH" => "870",
                                    "IMAGE_THUMB_HEIGHT" => ""
                                ),
                                false
                            );

                        } else {

                            if(!empty($filter_h1)){

                                $APPLICATION->IncludeComponent(
                                    "impel:sectiontitle",
                                    "",
                                    array(
                                        "FILTER_H1" => $filter_h1,
                                    ),
                                    false
                                );

                            }

                        }

                        if(!empty($filter_keywords)
                            || !empty($filter_h1)
                            || !empty($filter_title)
                            || !empty($filter_description)){
                            $arFields['set_filters'] = true;
                        };
                    }
                }
            };
            
            if(empty($arFields) && isset($intSectionID) && !empty($intSectionID)){
                $obCache = new CPageCache;

                $cacheID = 'sectionabout'.$arParams["IBLOCK_ID"].'.'.$intSectionID;

                $sectionabout = array();

                if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){
                    $obCache->Output();

                } else {

                    if($obCache->StartDataCache($cacheTime, $cacheID, "/impel/")){
                        $sectionResult = CIBlockSection::GetList(
                            array(
                                "SORT" =>"ASC"
                            ),
                            array(
                                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                "ID" => (int)$intSectionID
                            ),
                            false,
                            $arSelect = array("UF_DESC_RU")
                        );

                        if(is_object($sectionResult)
                            && method_exists($sectionResult,"GetNext")){

                            while ($sectionProp = $sectionResult->GetNext()){

                                $product_id = false;


                                if(isset($sectionProp["UF_DESC_RU"])
                                    && !empty($sectionProp["UF_DESC_RU"])){
                                    $product_id = (int)$sectionProp["UF_DESC_RU"];
                                }



                                if($product_id && !empty($product_id)){

                                    $infodb = CIBlockElement::GetByID($product_id);

                                    if(is_object($infodb)
                                        && method_exists($infodb,"GetNext")){

                                        while ($arFields = $infodb->GetNext()){

                                            $APPLICATION->IncludeComponent(
                                                "bitrix:news.detail",
                                                "nsection",
                                                array(
                                                    "DISPLAY_DATE" => "Y",
                                                    "DISPLAY_NAME" => "Y",
                                                    "DISPLAY_PICTURE" => "Y",
                                                    "DISPLAY_PREVIEW_TEXT" => "Y",
                                                    "USE_SHARE" => "N",
                                                    "AJAX_MODE" => "N",
                                                    "IBLOCK_TYPE" => $arFields['IBLOCK_TYPE_ID'],
                                                    "IBLOCK_ID" => $arFields['IBLOCK_ID'],
                                                    "ELEMENT_ID" => $arFields['ID'],
                                                    "ELEMENT_CODE" => "",
                                                    "CHECK_DATES" => "Y",
                                                    "FIELD_CODE" => array(
                                                        0 => "NAME",
                                                        1 => "PREVIEW_PICTURE",
                                                        2 => "PREVIEW_TEXT",
                                                    ),
                                                    "PROPERTY_CODE" => array(
                                                        0 => "ADDITIONAL_LINK",
                                                        1 => "H1_BOTTOM",
                                                    ),
                                                    "IBLOCK_URL" => "",
                                                    "META_KEYWORDS" => isset($arFields["PROPERTY_SEO_KEYWORDS_VALUE"]) && !empty($arFields["PROPERTY_SEO_KEYWORDS_VALUE"]) ? ("SEO_KEYWORDS") : '',
                                                    "META_DESCRIPTION" => isset($arFields["PROPERTY_SEO_DECRIPTION_VALUE"]) && !empty($arFields["PROPERTY_SEO_DECRIPTION_VALUE"]) ? ("SEO_DECRIPTION") : '',
                                                    "BROWSER_TITLE" => isset($arFields["PROPERTY_SEO_TITLE_VALUE"]) && !empty($arFields["PROPERTY_SEO_TITLE_VALUE"]) ? ("SEO_TITLE") : '',
                                                    "SET_TITLE" => isset($arFields["PROPERTY_SEO_TITLE_VALUE"]) && !empty($arFields["PROPERTY_SEO_TITLE_VALUE"]) ? "Y" : "N",
                                                    "SET_STATUS_404" => "N",
                                                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                                                    "ADD_SECTIONS_CHAIN" => "N",
                                                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                                                    "USE_PERMISSIONS" => "N",
                                                    "CACHE_TYPE" => "A",
                                                    "CACHE_TIME" => "36000000",
                                                    "CACHE_NOTES" => "",
                                                    "CACHE_GROUPS" => "Y",
                                                    "DISPLAY_TOP_PAGER" => "N",
                                                    "DISPLAY_BOTTOM_PAGER" => "Y",
                                                    "PAGER_TITLE" => "Страница",
                                                    "PAGER_TEMPLATE" => "",
                                                    "PAGER_SHOW_ALL" => "Y",
                                                    "AJAX_OPTION_JUMP" => "N",
                                                    "AJAX_OPTION_STYLE" => "Y",
                                                    "AJAX_OPTION_HISTORY" => "N",
                                                    "SET_BROWSER_TITLE" => isset($arFields["PROPERTY_SEO_TITLE_VALUE"]) && !empty($arFields["PROPERTY_SEO_TITLE_VALUE"]) ? "Y" : "N",
                                                    "SET_META_KEYWORDS" => isset($arFields["PROPERTY_SEO_KEYWORDS_VALUE"]) && !empty($arFields["PROPERTY_SEO_KEYWORDS_VALUE"]) ? "Y" : "N",
                                                    "SET_META_DESCRIPTION" => isset($arFields["PROPERTY_SEO_DECRIPTION_VALUE"]) && !empty($arFields["PROPERTY_SEO_DECRIPTION_VALUE"]) ? "Y" : "N",
                                                    "ADD_ELEMENT_CHAIN" => "N",
                                                    "AJAX_OPTION_ADDITIONAL" => "",
                                                    "COMPONENT_TEMPLATE" => "section",
                                                    "DETAIL_URL" => "",
                                                    "SET_CANONICAL_URL" => "N",
                                                    "SET_LAST_MODIFIED" => "N",
                                                    "PAGER_BASE_LINK_ENABLE" => "N",
                                                    "SHOW_404" => "N",
                                                    "MESSAGE_404" => "",
                                                    "IMAGE_THUMB_WIDTH" => "870",
                                                    "IMAGE_THUMB_HEIGHT" => ""
                                                ),
                                                false
                                            );

                                        }

                                    }

                                }

                            }

                        }

                        $obCache->EndDataCache();
                    };

                };

                $obCache = new CPHPCache;
                $cacheID = 'infoPagenSection'.static::getCacheHash($currPage);
                if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

                    $tmp = array();
                    $tmp = $obCache->GetVars();

                    if(isset($tmp[$cacheID])){
                        $cacheResults = $tmp[$cacheID];

                        $pag_title = $cacheResults['pag_title'];
                        $pag_description = $cacheResults['pag_description'];

                    }

                } else {

                    $sectionResult = CIBlockSection::GetList(
                        array(
                            "SORT" =>"ASC"
                        ),
                        array(
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "ID" => (int)$intSectionID
                        ),
                        false,
                        $arSelect = array("UF_DESC_RU")
                    );

                    if(is_object($sectionResult)
                        && method_exists($sectionResult,"GetNext")) {

                        while ($sectionProp = $sectionResult->GetNext()) {

                            $product_id = false;


                            if (isset($sectionProp["UF_DESC_RU"])
                                && !empty($sectionProp["UF_DESC_RU"])
                            ) {
                                $product_id = (int)$sectionProp["UF_DESC_RU"];
                            }


                            if ($product_id && !empty($product_id)) {

                                $infodb = CIBlockElement::GetList(
                                    array(),
                                    array('ID' => $product_id),
                                    false,
                                    false,
                                    array(
                                        'PROPERTY_SEO_DECRIPTION_PAGEN',
                                        'PROPERTY_SEO_TITLE_PAGEN')
                                );

                                if (is_object($infodb)
                                    && method_exists($infodb, "GetNext")
                                ){

                                    while ($arFields = $infodb->GetNext()) {

                                        $pag_title = $arFields['PROPERTY_SEO_TITLE_PAGEN_VALUE'];
                                        $pag_description = $arFields['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE'];

                                    }

                                }

                            }

                        }

                    }

                    $cacheResults = array(
                        'pag_title' => $pag_title,
                        'pag_description' => $pag_description
                    );

                    if($obCache->StartDataCache()){

                        $obCache->EndDataCache(
                            array(
                                $cacheID => $cacheResults
                            )
                        );

                    };

                }

            };
     
            $SEO_TEXT = ob_get_clean();
            $SEO_TEXT = trim($SEO_TEXT);
            $SEO_TEXT = html_entity_decode($SEO_TEXT);


            if (mb_stripos($SEO_TEXT,'<h1') !== false) {
                list($SEO_TITLE_H1,$SEO_TEXT) = explode('</h1>',$SEO_TEXT);
                $SEO_TITLE_H1 = strip_tags($SEO_TITLE_H1);
            }
            
            $APPLICATION->SetPageProperty('SEO_TITLE_H1', $SEO_TITLE_H1);

            if(!($page_num
                && $filter_set)){

                $pagenav_title = $APPLICATION->GetPageProperty('pagenav_title','');

                if(!empty($pagenav_title)){

                    if(!empty($pag_title)) {

                        if (mb_stripos($pagenav_title, '[pag_title]') !== false) {
                            $pagenav_title = str_ireplace('[pag_title]', $pag_title, $pagenav_title);
                        }

                    } else {

                        $pagenav_title = $APPLICATION->GetPageProperty('pagenav_title_default','');

                        if(mb_stripos($pagenav_title, '[title]') !== false){

                            if(!empty($APPLICATION->GetPageProperty('title',''))){
                                $pagenav_title = str_ireplace('[title]',$APPLICATION->GetPageProperty('title',''),$pagenav_title);
                            } else {
                                $pagenav_title = str_ireplace('[title]',$APPLICATION->GetTitle(),$pagenav_title);
                            }

                        }

                    }

                    if(!empty($pagenav_title))
                        $APPLICATION->SetTitle($pagenav_title);

                    if(!empty($pagenav_title))
                        $APPLICATION->SetPageProperty("title", $pagenav_title);

                    $APPLICATION->SetPageProperty('pagenav_title', '');
                    $APPLICATION->SetPageProperty('pagenav_title_default', '');

                }

                $pagenav_description = $APPLICATION->GetPageProperty('pagenav_description','');


                if(!empty($pagenav_description)){

                    if(!empty($pag_description)){

                        if(mb_stripos($pagenav_description, '[pag_description]') !== false){
                            $pagenav_description = str_ireplace('[pag_description]', $pag_description, $pagenav_description);
                        }

                    } else {

                        $pagenav_description = $APPLICATION->GetPageProperty('pagenav_description_default','');

                        if(mb_stripos($pagenav_description, '[description]') !== false) {
                            $pagenav_description = str_ireplace('[description]', $APPLICATION->GetPageProperty('description', ''), $pagenav_description);
                        }

                    }

                    if(!empty($pagenav_description))
                        $APPLICATION->SetPageProperty("description", $pagenav_description);

                    $APPLICATION->SetPageProperty('pagenav_description', '');
                    $APPLICATION->SetPageProperty('pagenav_description_default', '');

                }

            }

            $page_num = static::getPageNum();

            if(!$page_num)
                $APPLICATION->SetPageProperty('SEO_TEXT', $SEO_TEXT);

        }

    }

}

if(!class_exists('twigBuildSectionFilter')){

    abstract class twigBuildSectionFilterAll{
        abstract static function buildSectionFilter(&$arParams, $cacheTime = 604800);
        abstract static function clearRequestParams();
        abstract static function countFilterParams();
    }

    class twigBuildSectionFilter extends twigBuildSectionFilterAll{

        public static function skipAnalogueFilter(&$arrFilter){

            global $USER, $APPLICATION;

            {
                if (file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/skip_analogue_list.txt')) {
                    $skip = @unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/skip_analogue_list.txt'));
                    if (is_array($skip)
                        && !empty($skip)) {
                        $arrFilter['!ID'] = $skip;
                    }
                }
            }
        }

        public static function countFilterParams(){
            global $USER, $APPLICATION, $arrFilter;

            static::skipAnalogueFilter($arrFilter);

            if(mb_stripos($APPLICATION->GetCurDir(),'/filter/') !== false
                && mb_stripos($APPLICATION->GetCurDir(),'/filter/clear/') === false
            ){

                $filterUrl = preg_replace('~.*?/filter/~','',$APPLICATION->GetCurDir());
                $filterUrl = trim($filterUrl,'/');

                $uCount = sizeof(explode('/',$filterUrl));
                $fCount = 0;

                foreach($arrFilter as $pCode => $pValue){

                    if(mb_stripos($pCode,'=PROPERTY_') !== false) {
                        ++$fCount;
                    }

                }

                if($uCount > $fCount){
                    twigSmartFilters::set404Error(true);
                }

            }
        }

        public static function buildSectionFilter(&$arParams, $cacheTime = 604800){

            global $APPLICATION;

            static::countFilterParams();

            if (isset($arParams['USE_COMMON_SETTINGS_BASKET_POPUP']) && $arParams['USE_COMMON_SETTINGS_BASKET_POPUP'] == 'Y')
                $basketAction = (isset($arParams['COMMON_ADD_TO_BASKET_ACTION']) ? $arParams['COMMON_ADD_TO_BASKET_ACTION'] : '');
            else
                $basketAction = (isset($arParams['SECTION_ADD_TO_BASKET_ACTION']) ? $arParams['SECTION_ADD_TO_BASKET_ACTION'] : '');

            $arParams['ADD_TO_BASKET_ACTION'] = $basketAction;

            $howSort = twigSeoSections::getHowSortArray($arParams);

            $sort_values = array_keys($howSort);

            $sort_code_param = 'sort:section:'.$arParams["IBLOCK_ID"].':'.$arParams["SECTION_CODE_PATH"];

            $sort_default = $arParams["ELEMENT_SORT_FIELD"].":".$arParams["ELEMENT_SORT_ORDER"];
            $_SESSION[$sort_code_param] = !isset($_SESSION[$sort_code_param]) ? $sort_default : $_SESSION[$sort_code_param];

            $sort_code = ((isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])) ? (urldecode($_REQUEST['sort'])) : ($_SESSION[$sort_code_param]));

            if(empty($sort_code) && (($APPLICATION->get_cookie($sort_code_param)))){
                $sort_code = $APPLICATION->get_cookie($sort_code_param);
            }

            if(!(!empty($sort_code) && (in_array($sort_code,$sort_values)))){
                $sort_code = $sort_default;
            }

            $_SESSION[$sort_code_param] = $sort_code;
            $APPLICATION->set_cookie($sort_code_param,$sort_code);

            if(!empty($sort_code) && in_array($sort_code,$sort_values)){

                list($arParams["ELEMENT_SORT_FIELD"],$arParams["ELEMENT_SORT_ORDER"]) = explode(":",$sort_code);
                list($arParams["ELEMENT_SORT_FIELD2"],$arParams["ELEMENT_SORT_ORDER2"]) = explode(":",$sort_code);

            }

            $element_count_param = 'PAGE_ELEMENT_COUNT:section:'.$arParams["IBLOCK_ID"].':'.$arParams["SECTION_CODE_PATH"];
            $element_count = twigSeoSections::getPageElementCount($element_count_param);

            $arParams["PAGE_ELEMENT_COUNT"] = $element_count;

            $tmpls = array('GRID','LIST');

            if(isset($_REQUEST['LIST_TYPE']) && !empty($_REQUEST['LIST_TYPE'])){

                $arParams['LIST_TYPE'] = isset($_REQUEST['LIST_TYPE']) && in_array((string)$_REQUEST['LIST_TYPE'],$tmpls) ? trim($_REQUEST['LIST_TYPE']) : $arParams['LIST_TYPE'];
                $_SESSION['LIST_TYPE'] = $arParams['LIST_TYPE'];
                $APPLICATION->set_cookie('LIST_TYPE',$arParams['LIST_TYPE']);

            } else {

                $arParams['LIST_TYPE'] = $APPLICATION->get_cookie('LIST_TYPE');
                $arParams['LIST_TYPE'] = !empty($arParams['LIST_TYPE']) && in_array((string)$arParams['LIST_TYPE'],$tmpls) ? trim($arParams['LIST_TYPE']) : '';

                if(empty($arParams['LIST_TYPE']) && !empty($_SESSION['LIST_TYPE']) && in_array((string)$_SESSION['LIST_TYPE'],$tmpls)){
                    $arParams['LIST_TYPE'] = $_SESSION['LIST_TYPE'];
                };

                if(empty($arParams['LIST_TYPE'])){
                    $arParams['LIST_TYPE'] = 'LIST';
                };

            };

            if(!in_array($arParams['LIST_TYPE'],$tmpls)){

                $arParams['LIST_TYPE'] = 'LIST';

            }

            $dobj = new \Bitrix\Conversion\Internals\MobileDetect;

            if($dobj->isMobile()){
                $arParams['LIST_TYPE'] = 'GRID';
            };

            $arParams['LIST_TYPE'] = mb_strtolower($arParams['LIST_TYPE']);
            static::clearRequestParams();

        }

        public static function clearRequestParams(){

            unset($_REQUEST['SECTION_CODE_PATH'],$_REQUEST['ELEMENT_CODE'],$_GET['SECTION_CODE_PATH'],$_GET['ELEMENT_CODE'],$_REQUEST['filter'],$_GET['filter']);

            $_REQUEST['SECTION_CODE_PATH'] = null;
            $_REQUEST['ELEMENT_CODE'] = null;
            $_REQUEST['filter'] = null;

            $_GET['SECTION_CODE_PATH'] = null;
            $_GET['ELEMENT_CODE'] = null;
            $_GET['filter'] = null;

        }
    }
}

if(!class_exists('twigElement')){
    abstract class twigElementAll{
        abstract static function applyTemplateModifications(&$arResult,&$arParams, $cacheTime  = 604800);
        abstract static function incScriptsAtEpilog(&$arResult,&$arParams);
        abstract static function incStylesAtEpilog(&$arResult,&$arParams);
        abstract static function printSeoAndTitlesAtEpilog(&$arResult,&$arParams);
        abstract static function getBigData($arResult = []);
        abstract static function getPropertiesHint();
    }

    class twigElement extends twigElementAll{

        use twigTemplateServices;

        public static function incScriptsAtEpilog(&$arResult,&$arParams){
            global $APPLICATION;

            $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/ekko-lightbox.min.js");
            $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.maskedinput.min.js");
            $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/slick.min.js");
            $APPLICATION->AddHeadScript("/bitrix/templates/.default/components/bitrix/catalog.section/kitslider/script.min.js");

        }

        public static function incStylesAtEpilog(&$arResult,&$arParams){
            global $APPLICATION;

            $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/ekko-lightbox.css');
            $APPLICATION->SetAdditionalCSS('/bitrix/templates/.default/components/bitrix/catalog.section/analogue/style.min.css');
            $APPLICATION->SetAdditionalCSS('/bitrix/templates/.default/components/bitrix/catalog.section/kitslider/style.min.css');
            $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/slick.css');

        }

        public static function getBigData($arResult = []) {

            $uri = 'https://analytics.bitrix.info/crecoms/v1_0/recoms.php?uid='.trim($_COOKIE['BX_USER_ID']).'&aid='.trim(\Bitrix\Main\Analytics\Counter::getAccountId()).'&count='.mt_rand(15,20).'&op=recommend&ib=11';
            $ids = [];
            $content = getURIContent($uri);

            if (!empty($content)) {
                $content = json_decode($content,true);

                if (isset($content["items"])
                    && is_array($content["items"])
                    && !empty($content["items"])) {
                    $ids = $content["items"];
                    $ids = array_combine($ids,$ids);

                    foreach ($ids as $key => $product_id) {

                        $can_buy = canYouBuy($product_id);
                        $quantity = get_quantity_product($product_id);
                        $can_buy = $quantity > 0 && $can_buy ? $quantity : 0;
                        if(!($can_buy > 0)) {
                            unset($ids[$key]);
                        }

                    }

                    if (isset($arResult['ID']) && isset($ids[$arResult['ID']])) {
                        unset($ids[$arResult['ID']]);
                    }

                    shuffle($ids);

                }
            }

            return $ids;

        }

        public static function getPropertiesHint() {

            $sectionPropertyGetList = Bitrix\Iblock\SectionPropertyTable::getList(array(
                "filter" => array(
                    "!FILTER_HINT" => false,
                    "SECTION_ID" => 0,
                    "IBLOCK_ID" => 11
                ),
            ));

            $result = array();

            if ($sectionPropertyGetList) {
                while ($property = $sectionPropertyGetList->fetch())
                {

                    $rdb = CIBlockProperty::GetByID($property['PROPERTY_ID']);
                    $sName = '';

                    if ($rdb && $adb = $rdb->Fetch()) {
                        $sName = isset($adb['NAME']) ? trim($adb['NAME']) : '';
                    }

                    $result[$property['PROPERTY_ID']] = ['NAME' => $sName,'HINT' => $property['FILTER_HINT']];
                }
            }

            return $result;
        }

        public static function getPrimaryImages(&$item) {

            $dProps = CIBlockElement::GetProperty(
                11,
                $item['ID'],
                Array(),
                Array(
                    "CODE" => "PRIMARY_IMAGES"
                )
            );

            $images = [];

            if($dProps){

                while ($aProps = $dProps->GetNext()) {

                    $imgSrc = $imgId = $aProps["VALUE"];

                    if ($imgId && is_numeric($imgId)) {
                        $imgSrc = CFile::GetPath($imgId);
                    }

                    if (file_exists($_SERVER['DOCUMENT_ROOT'].$imgSrc)) {

                        $images[] = $imgSrc;
                    }

                }

            }

            if (!empty($images)) {

                $images = array_reverse($images);

                foreach ($images as $imgSrc) {
                    array_unshift($item['MORE_PHOTO'],['SRC' => $imgSrc]);
                }

            }

        }

        public static function applyTemplateModifications(&$arResult,&$arParams, $cacheTime  = 604800) {

            global $USER;

            {

                if (file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/analogue.txt')) {
                    $analogue = @unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/analogue.txt'));
                    if (isset($analogue[$arResult['ID']])){
                        $arResult['ANALOGUE'] = $analogue[$arResult['ID']];
                    }
                }
            }

            if (isset($arResult['PROPERTIES']) && isset($arResult['PROPERTIES']['TYPEPRODUCT'])) {
                $arResult['PRODUCT']['PROPERTIES']['TYPEPRODUCT'] = $arResult['PROPERTIES']['TYPEPRODUCT'];
                $arResult['PRODUCT']['NAME'] = $arResult['NAME'];
                $arResult['PRODUCT']['DETAIL_PAGE_URL'] = $arResult['DETAIL_PAGE_URL'];
            }

            if(isset($arResult['DISPLAY_PROPERTIES'])
                &&isset($arResult['DISPLAY_PROPERTIES']['SEO_TEXT1'])
                &&isset($arResult['DISPLAY_PROPERTIES']['SEO_TEXT1']['VALUE'])
                &&!empty($arResult['DISPLAY_PROPERTIES']['SEO_TEXT1']['VALUE'])){

                $arResult['DETAIL_TEXT'] = $arResult['DISPLAY_PROPERTIES']['SEO_TEXT1']['VALUE'];
            }

            $arResult['HINTS'] = static::getPropertiesHint();

            unset($arResult['DISPLAY_PROPERTIES']['SEO_TEXT1']);

            static::setImageDimensions();
            static::getEmptyPreview($arParams);
            twigSeoSections::applyTemplateModifications($arResult,$arParams);
            static::getConcentProcessingLink($arResult);

            if(isset($arResult['DISPLAY_PROPERTIES'])
                &&isset($arResult['DISPLAY_PROPERTIES']['PRSET'])
                &&isset($arResult['DISPLAY_PROPERTIES']['PRSET']['VALUE'])
                &&!empty($arResult['DISPLAY_PROPERTIES']['PRSET']['VALUE'])){

                $arResult['PRSET'] = $arResult['DISPLAY_PROPERTIES']['PRSET']['VALUE'];
            }

            unset($arResult['DISPLAY_PROPERTIES']['PRSET']);

            $obCache = new CPHPCache;
            $cacheID = 'aboutcartdetail'.md5(IMPEL_SERVER_NAME);
            $lines = array();

            if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

                $tmp = array();
                $tmp = $obCache->GetVars();

                if(isset($tmp[$cacheID])){
                    $lines = $tmp[$cacheID];
                }

            } else {

                $cartArFilter = Array(
                    "CODE" => "buy-panel-info",
                    "IBLOCK_CODE" => "options",
                    'PROPERTY_DOMAIN_VALUE' => IMPEL_SERVER_NAME
                );

                $cartResDB = CIBlockElement::GetList(Array(), $cartArFilter, false, false, array("ID","IBLOCK_ID"));
                $cart_description = "";

                $lines = array("SOCIAL_ICONS_LINKS" => array(), "SOCIAL_TITLES" => array(), "TOOLTIP_TEXT" => array() );

                if($cartResDB){

                    $cartResAr = $cartResDB->GetNext();

                    if(isset($cartResAr["ID"]) && !empty($cartResAr["ID"])){

                        $dbBlockResult  = CIBlockElement::GetProperty(
                            $cartResAr["IBLOCK_ID"],
                            $cartResAr["ID"],
                            Array(),
                            Array(
                                "CODE" => "SOCIAL_ICONS_LINKS"
                            )
                        );



                        if($dbBlockResult){
                            while($dbBlockArr = $dbBlockResult->fetch()){

                                if(isset($dbBlockArr["VALUE"]) && !empty($dbBlockArr["VALUE"])){
                                    if(isset($dbBlockArr["VALUE"]))
                                        $lines["SOCIAL_ICONS_LINKS"][] = $dbBlockArr["VALUE"];
                                }

                            }
                        }


                        $dbBlockResult  = CIBlockElement::GetProperty(
                            $cartResAr["IBLOCK_ID"],
                            $cartResAr["ID"],
                            Array(),
                            Array(
                                "CODE" => "SOCIAL_TITLES"
                            )
                        );

                        if($dbBlockResult){
                            while($dbBlockArr = $dbBlockResult->fetch()){

                                if(isset($dbBlockArr["VALUE"]))
                                    $lines["SOCIAL_TITLES"][] = $dbBlockArr["VALUE"];


                            }
                        }

                        $dbBlockResult  = CIBlockElement::GetProperty(
                            $cartResAr["IBLOCK_ID"],
                            $cartResAr["ID"],
                            Array(),
                            Array(
                                "CODE" => "TOOLTIP_TEXT"
                            )
                        );

                        if($dbBlockResult){
                            while($dbBlockArr = $dbBlockResult->fetch()){
                                if(isset($dbBlockArr["VALUE"]) && isset($dbBlockArr["VALUE"]["TEXT"]))
                                    $lines["TOOLTIP_TEXT"][] = $dbBlockArr["VALUE"]["TEXT"];
                            }
                        }


                    }

                };

                if($obCache->StartDataCache()){

                    $obCache->EndDataCache(
                        array(
                            $cacheID => $lines
                        )
                    );

                };
            };

            $arResult['CART_DESCRIPTION'] = $lines;

            $tabs = array();

            unset($arResult["DISPLAY_PROPERTIES"]["INSTRUCTION"]);

            unset($arResult["DISPLAY_PROPERTIES"]["VIDEO"]);

            $obCache = new CPHPCache;
            $cacheID = 'tabsdetail'.md5(IMPEL_SERVER_NAME);

            $props = ['MANUFACTURER','TYPEPRODUCT','MANUFACTURER_DETAIL'];

            if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

                $tmp = array();
                $tmp = $obCache->GetVars();

                if(isset($tmp[$cacheID])){
                    $tabs = $tmp[$cacheID];
                }

            } else {

                global $USER;

                $arFilter = Array(
                    "SECTION_CODE" => "tabs",
                    "IBLOCK_CODE" => "options",
                    "ACTIVE" => "Y",
                    'PROPERTY_DOMAIN_VALUE' => IMPEL_SERVER_NAME
                );

                foreach ($props as $pCode) {
                    $arFilter["PROPERTY_".$pCode."_VALUE"] = false;
                }

                $tabsResDB = CIBlockElement::GetList(Array(), $arFilter, false, false, array("ID","NAME","PREVIEW_TEXT","PREVIEW_PICTURE"));

                if($tabsResDB){

                    while($tabsResArr = $tabsResDB->Fetch()){

                        if(
                            isset($tabsResArr['ID'])
                            && isset($tabsResArr['NAME'])
                            && isset($tabsResArr['PREVIEW_TEXT'])
                            && !empty($tabsResArr['PREVIEW_TEXT'])
                        ){

                            $tabs['tab_headers'][$tabsResArr['ID']]	= $tabsResArr['NAME'];
                            $tabs['tab_panels'][$tabsResArr['ID']] = $tabsResArr['PREVIEW_TEXT'];

                            if(	isset($tabsResArr['PREVIEW_PICTURE'])
                                && !empty($tabsResArr['PREVIEW_PICTURE'])
                            ){

                                $image_path	= CFile::GetPath($tabsResArr['PREVIEW_PICTURE']);
                                if($image_path){
                                    $tabs['tab_images'][$tabsResArr['ID']] = $image_path;
                                };

                            };
                        };
                    };
                };

                if($obCache->StartDataCache()){

                    $obCache->EndDataCache(
                        array(
                            $cacheID => $tabs
                        )
                    );

                };

            };

            $arResult['TABS'.$arResult['ID']] = ['tab_headers' => [], 'tab_panels' => []];

            {

                //TYPEPRODUCT
                //MANUFACTURER
                //MANUFACTURER_DETAIL

                $bFound = false;

                if (file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/tabs.txt')) {

                    $content = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/tabs.txt');
                    $content = unserialize($content);

                    $tProduct = [];

                    foreach ($props as $pCode) {
                        if (isset($arResult['PROPERTIES'][$pCode])) {
                            $value = isset($arResult['PROPERTIES'][$pCode]['VALUE_ENUM']) && !empty($arResult['PROPERTIES'][$pCode]['VALUE_ENUM']) ? $arResult['PROPERTIES'][$pCode]['VALUE_ENUM'] : $arResult['PROPERTIES'][$pCode]['VALUE'];

                            if (is_array($value)) {
                                $value = array_map(function($value) {
                                    $value = trim(mb_strtolower($value));
                                    return $value;
                                },$value);
                            } else {
                                $value = trim(mb_strtolower($value));
                            }

                            $tProduct[$pCode] = $value;
                        }
                    }

                    $iFind = [];

                    foreach ($content as $tabId => $value) {

                        $cKeys = array_keys($value);
                        $tKeys = array_keys($tProduct);

                        if (empty(array_diff($cKeys,$tKeys))) {

                            $howmany = [];

                            foreach ($tProduct as $key => $check) {

                                $check = !is_array($check) ? (array)$check : $check;
                                $next = !is_array($value[$key]) ? (array)$value[$key] : $value[$key];

                                $intersect = array_intersect($next,$check);

                                if (!empty($intersect)) {
                                    $howmany[] = true;
                                }

                            }

                            if (count($howmany) == count($value)) {
                                $max = count($howmany);

                                if (!isset($iFind[$max])) {
                                    $iFind[$max] = [];
                                }

                                $iFind[$max][$tabId] = $tabId;
                            }

                        }

                    }

                    if ($iFind) {

                        krsort($iFind);
                        $current = current($iFind);

                        $arFilter = Array(
                            "SECTION_CODE" => "tabs",
                            "IBLOCK_CODE" => "options",
                            "ACTIVE" => "Y",
                            "ID" => $current,
                            'PROPERTY_DOMAIN_VALUE' => IMPEL_SERVER_NAME
                        );

                        $tabsResDB = CIBlockElement::GetList(Array(), $arFilter, false, false, array("ID","NAME","PREVIEW_TEXT","PREVIEW_PICTURE","PROPERTY_DOMAIN"));

                        if($tabsResDB){

                            while($tabsResArr = $tabsResDB->Fetch()){

                                if(
                                    isset($tabsResArr['ID'])
                                    && isset($tabsResArr['NAME'])
                                    && isset($tabsResArr['PREVIEW_TEXT'])
                                    && !empty($tabsResArr['PREVIEW_TEXT'])
                                ){

                                    $arResult['TABS'.$arResult['ID']]['tab_headers'][$tabsResArr['ID']]	= $tabsResArr['NAME'];
                                    $arResult['TABS'.$arResult['ID']]['tab_panels'][$tabsResArr['ID']] = $tabsResArr['PREVIEW_TEXT'];

                                    if(	isset($tabsResArr['PREVIEW_PICTURE'])
                                        && !empty($tabsResArr['PREVIEW_PICTURE'])
                                    ){

                                        $image_path	= CFile::GetPath($tabsResArr['PREVIEW_PICTURE']);
                                        if($image_path){
                                            $arResult['TABS'.$arResult['ID']]['tab_images'][$tabsResArr['ID']] = $image_path;
                                        };

                                    };
                                };
                            };
                        };

                    };

                }

                //PROPERTIES
                //MANUFACTURER VALUE_ENUM
                //MANUFACTURER_DETAIL VALUE_ENUM

            }

            $fTabs = array();

            $fTabs['tab_headers']['DETAIL_TEXT'] = GetMessage("CT_ABOUT_PRODUCT");
            $fTabs['tab_panels']['DETAIL_TEXT'] = function_exists('tidy_repair_string')
                ? tidy_repair_string($arResult["DETAIL_TEXT"], array('show-body-only' => true), "utf8")
                : $arResult["DETAIL_TEXT"];

            $fTabs['tab_headers']['REVIEWS'] = GetMessage("TMPL_REVIEWS");
            $fTabs['tab_panels']['REVIEWS'] = '';

            $fTabs['tab_headers']['QUESTIONS'] = GetMessage("TMPL_QUESTIONS");
            $fTabs['tab_panels']['QUESTIONS'] = '';

            if(isset($arResult["DISPLAY_PROPERTIES"]["VIDEO"])
                && !empty($arResult["DISPLAY_PROPERTIES"]["VIDEO"])){

                $fTabs['tab_headers']['VIDEO'] = GetMessage("CT_VIDEO");
                $fTabs['tab_panels']['VIDEO'] = $arResult["DISPLAY_PROPERTIES"]["VIDEO"];

            }

            $tabs['tab_headers'] = array_merge($fTabs['tab_headers'], $tabs['tab_headers'] ? $tabs['tab_headers'] : array());
            $tabs['tab_panels'] = array_merge($fTabs['tab_panels'], $tabs['tab_panels'] ? $tabs['tab_panels'] : array());

            $arResult['TABS'] = $tabs;

            static::getPrimaryImages($arResult);

            $arResult["MORE_PHOTO_BIG"] = array();
            $arResult["MORE_PHOTO_THUMB"] = array();

            if(isset($arResult["MORE_PHOTO"])){

                $photos = $arResult["MORE_PHOTO"];
                unset($arResult["MORE_PHOTO"]);

                if(is_array($photos)
                    && sizeof($photos) > 0){

                    foreach($photos as $number => $photo){
                        if(isset($photo['SRC'])
                            && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$photo['SRC'])){

                            $arResult["MORE_PHOTO"][$number] = static::getThumb($photo['SRC'],static::$imageDimensions['main']);
                            $arResult["MORE_PHOTO_BIG"][$number] = static::getThumb($photo['SRC'],static::$imageDimensions['big']);
                            $arResult["MORE_PHOTO_THUMB"][$number] = static::getThumb($photo['SRC'],static::$imageDimensions['thumb']);

                        }
                    }

                }

                if(empty($arResult["MORE_PHOTO"])){
                    $arParams['arEmptyPreview'] = static::getThumb($arParams['arEmptyPreview']['SRC'],static::$imageDimensions['main']);
                    $arResult["MORE_PHOTO"] = array($arParams['arEmptyPreview']);
                }

                unset($photos);

            }

            $arResult["MORE_PHOTO_COUNT"] = sizeof($arResult["MORE_PHOTO"]);

            $arResult['HAS_WARRANTY'] = false;
            $arResult['STOCK_PRINT_RATIO_PRICE'] = '';

            $dbStockPrice = CPrice::GetList(
                array(),
                array(
                    "PRODUCT_ID" => $arResult['ID'],
                    "CATALOG_GROUP_ID" => 8
                )
            );

            if ($dbStockPrice
                && $arStockPrice = $dbStockPrice->GetNext())
            {

                $currentPrice = $arResult['ITEM_PRICES'][$arResult['ITEM_PRICE_SELECTED']];

                if(    isset($currentPrice['CURRENCY'])
                    && isset($arStockPrice['CURRENCY'])
                    && !empty($currentPrice['CURRENCY'])
                    && !empty($arStockPrice['CURRENCY'])
                    && ($arStockPrice['CURRENCY'] != $currentPrice['CURRENCY'])
                ){

                    $arStockPrice["PRICE"] = CCurrencyRates::ConvertCurrency($arStockPrice["PRICE"], $arStockPrice['CURRENCY'], $currentPrice['CURRENCY']);
                    $arStockPrice["CURRENCY"] = $currentPrice["CURRENCY"];
                }

                $arStockPrice["PRINT_RATIO_PRICE"] = CurrencyFormat($arStockPrice["PRICE"], $arStockPrice["CURRENCY"]);

                if(isset($arStockPrice["PRINT_RATIO_PRICE"])
                    && !empty($arStockPrice["PRINT_RATIO_PRICE"])){

                    $arResult['STOCK_PRINT_RATIO_PRICE'] = $arStockPrice["PRINT_RATIO_PRICE"];

                }

            }

            if(isset($arResult["DISPLAY_PROPERTIES"]["WARRANTY"])
                && isset($arResult["DISPLAY_PROPERTIES"]["WARRANTY"]["~VALUE"])
                && !empty($arResult["DISPLAY_PROPERTIES"]["WARRANTY"]["~VALUE"])){

                $arResult['HAS_WARRANTY'] = true;

            }

            $modelHtml = '';
            $arResult["MODEL_HTML_COUNT"] = 0;

            if(isset($arResult["DISPLAY_PROPERTIES"]["MODEL_HTML"])
                && isset($arResult["DISPLAY_PROPERTIES"]["MODEL_HTML"]["~VALUE"])
                && !empty($arResult["DISPLAY_PROPERTIES"]["MODEL_HTML"]["~VALUE"])){

                $dbMHTMLId = CIBlockElement::GetByID($arResult["DISPLAY_PROPERTIES"]["MODEL_HTML"]["~VALUE"]);

                if($dbMHTMLId
                    && $arMHTMLId = $dbMHTMLId->GetNext()){

                    $modelHtml = trim($arMHTMLId['~DETAIL_TEXT']);
                    $arResult["MODEL_HTML_COUNT"] = sizeof(explode("\n",$modelHtml));

                }

            }

            $arResult["MODEL_HTML_IDS"] = '';
            $arResult["MODEL_HTML"] = $modelHtml;

            if(empty($ids) && !empty(trim($arResult["MODEL_HTML"]))
                && (($arResult["MODEL_HTML_COUNT"] == 1))
                && isset($arResult['PROPERTIES']['LINKED_ELEMETS'])
                && !empty($arResult['PROPERTIES']['LINKED_ELEMETS'])
                && isset($arResult['PROPERTIES']['LINKED_ELEMETS']['VALUE'])
                && !empty($arResult['PROPERTIES']['LINKED_ELEMETS']['VALUE'])
            ){

                $arResult["MODEL_HTML_IDS"] = $arResult['PROPERTIES']['LINKED_ELEMETS']['VALUE'];

            }

            unset($arResult["DISPLAY_PROPERTIES"]["MODEL_HTML"],$arResult['DISPLAY_PROPERTIES']['LINKED_ELEMETS']);

            $arSFilter = array();

            if(isset($arResult['ORIGINAL_PARAMETERS'])){

                if(isset($arResult['ORIGINAL_PARAMETERS']['SECTION_CODE'])){

                    $arSFilter = array(
                        'CODE' => $arResult['ORIGINAL_PARAMETERS']['SECTION_CODE']
                    );

                } else if(isset($arResult['ORIGINAL_PARAMETERS']['SECTION_ID'])){

                    $arSFilter = array(
                        'ID' => $arResult['ORIGINAL_PARAMETERS']['SECTION_ID']
                    );

                }

            }

            $arResult['SECTION_NAME'] = '';

            if(!empty($arSFilter)) {

                $rsSection = CIBlockSection::GetList(Array(), $arSFilter, false, array('NAME'));

                if ($rsSection) {

                    $arSection = $rsSection->GetNext();
                    $arResult['SECTION_NAME'] = $arSection['NAME'];

                }

            }

            $canonical_url = '';
            $canonicalResDB = CIBlockElement::GetList(Array("SORT" => "ASC"),Array('ID' => $arResult['ID'], 'IBLOCK_ID' => $arResult['IBLOCK_ID']), false, false, array('DETAIL_PAGE_URL'));

            if($canonicalResDB
                && $canonicalResArr = $canonicalResDB->getNext()){

                if(isset($canonicalResArr['DETAIL_PAGE_URL']) && !empty($canonicalResArr['DETAIL_PAGE_URL'])){
                    $canonical_url = $canonicalResArr['DETAIL_PAGE_URL'];
                };

            };

            $arResult['CANONICAL_URL'] = $canonical_url;

        }

        public static function replaceDomain($iElt) {

            global $APPLICATION;
            $APPLICATION->SetPageProperty('element_id',$iElt);

        }

        public static function printSeoAndTitlesAtEpilog(&$arResult,&$arParams){
            global $APPLICATION,$USER;

            static::replaceDomain($arResult['ID']);

            if($arResult['PRODUCT'] && $arResult['PRODUCT']['PROPERTIES']['TYPEPRODUCT'])
            {
                $product_name = $arResult['PRODUCT']['NAME'];
                $product_url = $arResult['PRODUCT']['DETAIL_PAGE_URL'];
                $type_product_name = $arResult['PRODUCT']['PROPERTIES']['TYPEPRODUCT']['VALUE'];
                $type_product_xml_id = $arResult['PRODUCT']['PROPERTIES']['TYPEPRODUCT']['VALUE_XML_ID'];

                $arr_url = explode("/", $product_url);
                $arr_url = array_diff($arr_url, array(''));
                array_pop($arr_url);

                $section_url = "";
                foreach ($arr_url as $item)
                {
                    $section_url .=  "/".$item;
                }

                $filter_url = $section_url."/filter/typeproduct-is-".$type_product_xml_id."/";

                $APPLICATION->AddChainItem($type_product_name, $filter_url."#end");
                $APPLICATION->AddChainItem($product_name, "#detail");

            }

            if(isset($_REQUEST['previews']) && isset($_REQUEST['order_id'])
                && !empty($_REQUEST['previews']) && !empty($_REQUEST['order_id'])){
                $APPLICATION->SetPageProperty("robots", "noindex, nofollow");
            }

            // $APPLICATION->AddHeadString('<link rel="amphtml" href="'.IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/amp/catalog/' . $arResult['CODE'] . '/'.'" />');

            if($arResult["MORE_PHOTO_COUNT"] > 0){

                $image = current($arResult["MORE_PHOTO"]);

                if(isset($image['SRC'])
                    && !empty($image['SRC'])){

                    $APPLICATION->SetPageProperty("ogimage", IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$image['SRC']);

                }

            }

            if (isset($_REQUEST['PAGEN_1'])
                && $_REQUEST['PAGEN_1'] > 0) {

                $APPLICATION->SetPageProperty("robots", "noindex, nofollow");

            }

            if(isset($arResult['CANONICAL_URL'])) {

                $canonical_url = $arResult['CANONICAL_URL'];

                if(isset($canonical_url) && !empty($canonical_url)){

                    $canonical_url = (preg_match('~http(s*?)://~',$canonical_url) == 0 ? (IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$canonical_url) : $canonical_url);

                    $SERVER_PAGE_URL = IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$_SERVER['REQUEST_URI'];
                    $SERVER_PAGE_URL = preg_replace('~\?.*?$~isu','',$SERVER_PAGE_URL);
                    $DETAIL_PAGE_URL = preg_replace('~\?.*?$~isu','',$canonical_url);


                    if($DETAIL_PAGE_URL != $SERVER_PAGE_URL || isset($_REQUEST['previews']) || defined('NEED_CANONICAL')){
                        $APPLICATION->AddHeadString('<link rel="canonical" href="'.$canonical_url.'" />');
                        $mobile_url = (preg_match('~http(s*?)://~',$canonical_url) == 0 ? (IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$canonical_url) : $canonical_url);
                        $mobile_url = preg_replace('~://~isu','://m.',$mobile_url);

                        $APPLICATION->SetPageProperty('MOBILE_ALTERATE', $mobile_url);

                    }

                }

            }

            global $USER;

            if (isset($arResult["PRSET"])
                && !empty($arResult["PRSET"])
                && twigSet::canIByProduct($arResult["PRSET"])
            ) {

                $arSet = ['ID' => $arResult["PRSET"]];
                twigSet::createSet($arSet);

                if (isset($arSet['SETLIST_COUNT'])
                    && $arSet['SETLIST_COUNT'] > 0) {

                    $APPLICATION->IncludeComponent(
                        "bitrix:catalog.element",
                        "nalsobuy",
                        array(
                            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                            "IBLOCK_ID" => 11,
                            "PROPERTY_CODE" => array('OLD_PRICE','MORE_PHOTO'),
                            "META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
                            "META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
                            "BROWSER_TITLE" => $arParams["DETAIL_BROWSER_TITLE"],
                            "SET_CANONICAL_URL" => "N",
                            "BASKET_URL" => $arParams["BASKET_URL"],
                            "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                            "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                            "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                            "CHECK_SECTION_ID_VARIABLE" => (isset($arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"]) ? $arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"] : ''),
                            "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                            "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                            "CACHE_TIME" => $arParams["CACHE_TIME"],
                            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                            "SET_TITLE" => "N",
                            "SET_LAST_MODIFIED" => "N",
                            "MESSAGE_404" => "",
                            "SET_STATUS_404" => "N",
                            "SHOW_404" => "N",
                            "FILE_404" => "",
                            "PRICE_CODE" => $arParams["PRICE_CODE"],
                            "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                            "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                            "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                            "PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
                            "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                            "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
                            "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                            "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                            "LINK_IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
                            "LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
                            "LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
                            "LINK_ELEMENTS_URL" => $arParams["LINK_ELEMENTS_URL"],

                            "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                            "OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
                            "OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
                            "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                            "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                            "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                            "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],

                            "ELEMENT_ID" => $arResult["PRSET"],
                            "ELEMENT_CODE" => "",
                            "SECTION_ID" => "",
                            "SECTION_CODE" => "",
                            "SECTION_URL" => "",
                            "DETAIL_URL" => "",
                            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                            'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                            'USE_ELEMENT_COUNTER' => $arParams['USE_ELEMENT_COUNTER'],
                            'SHOW_DEACTIVATED' => 'Y',
                            "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
                            'ADD_PICT_PROP' => 'MORE_PHOTO',
                            'LABEL_PROP' => $arParams['LABEL_PROP'],
                            'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
                            'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
                            'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                            'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                            'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                            'SHOW_MAX_QUANTITY' => $arParams['DETAIL_SHOW_MAX_QUANTITY'],
                            'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
                            'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
                            'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
                            'MESS_BTN_COMPARE' => $arParams['MESS_BTN_COMPARE'],
                            'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
                            'USE_VOTE_RATING' => $arParams['DETAIL_USE_VOTE_RATING'],
                            'VOTE_DISPLAY_AS_RATING' => (isset($arParams['DETAIL_VOTE_DISPLAY_AS_RATING']) ? $arParams['DETAIL_VOTE_DISPLAY_AS_RATING'] : ''),
                            'USE_COMMENTS' => $arParams['DETAIL_USE_COMMENTS'],
                            'BLOG_USE' => (isset($arParams['DETAIL_BLOG_USE']) ? $arParams['DETAIL_BLOG_USE'] : ''),
                            'BLOG_URL' => (isset($arParams['DETAIL_BLOG_URL']) ? $arParams['DETAIL_BLOG_URL'] : ''),
                            'BLOG_EMAIL_NOTIFY' => (isset($arParams['DETAIL_BLOG_EMAIL_NOTIFY']) ? $arParams['DETAIL_BLOG_EMAIL_NOTIFY'] : ''),
                            'VK_USE' => (isset($arParams['DETAIL_VK_USE']) ? $arParams['DETAIL_VK_USE'] : ''),
                            'VK_API_ID' => (isset($arParams['DETAIL_VK_API_ID']) ? $arParams['DETAIL_VK_API_ID'] : 'API_ID'),
                            'FB_USE' => (isset($arParams['DETAIL_FB_USE']) ? $arParams['DETAIL_FB_USE'] : ''),
                            'FB_APP_ID' => (isset($arParams['DETAIL_FB_APP_ID']) ? $arParams['DETAIL_FB_APP_ID'] : ''),
                            'BRAND_USE' => (isset($arParams['DETAIL_BRAND_USE']) ? $arParams['DETAIL_BRAND_USE'] : 'N'),
                            'BRAND_PROP_CODE' => (isset($arParams['DETAIL_BRAND_PROP_CODE']) ? $arParams['DETAIL_BRAND_PROP_CODE'] : ''),
                            'DISPLAY_NAME' => (isset($arParams['DETAIL_DISPLAY_NAME']) ? $arParams['DETAIL_DISPLAY_NAME'] : ''),
                            'ADD_DETAIL_TO_SLIDER' => (isset($arParams['DETAIL_ADD_DETAIL_TO_SLIDER']) ? $arParams['DETAIL_ADD_DETAIL_TO_SLIDER'] : ''),
                            'TEMPLATE_THEME' => '',
                            "ADD_SECTIONS_CHAIN" => 'N',
                            "ADD_ELEMENT_CHAIN" => 'N',
                            "DISPLAY_PREVIEW_TEXT_MODE" => (isset($arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE']) ? $arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE'] : ''),
                            "DETAIL_PICTURE_MODE" => (isset($arParams['DETAIL_DETAIL_PICTURE_MODE']) ? $arParams['DETAIL_DETAIL_PICTURE_MODE'] : ''),
                            'ADD_TO_BASKET_ACTION' => '',
                            'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
                            'DISPLAY_COMPARE' => 'N',
                            'COMPARE_PATH' => "",
                            'SHOW_BASIS_PRICE' => (isset($arParams['DETAIL_SHOW_BASIS_PRICE']) ? $arParams['DETAIL_SHOW_BASIS_PRICE'] : 'Y'),
                            'BACKGROUND_IMAGE' => (isset($arParams['DETAIL_BACKGROUND_IMAGE']) ? $arParams['DETAIL_BACKGROUND_IMAGE'] : ''),
                            'DISABLE_INIT_JS_IN_COMPONENT' => 'Y',
                            'SET_VIEWED_IN_COMPONENT' => 'Y',

                            "USE_GIFTS_DETAIL" => 'N',
                            "USE_GIFTS_MAIN_PR_SECTION_LIST" => 'N',
                            "GIFTS_SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
                            "GIFTS_SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
                            "GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
                            "GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
                            "GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
                            "GIFTS_DETAIL_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_BLOCK_TITLE"],
                            "GIFTS_SHOW_NAME" => $arParams['GIFTS_SHOW_NAME'],
                            "GIFTS_SHOW_IMAGE" => $arParams['GIFTS_SHOW_IMAGE'],
                            "GIFTS_MESS_BTN_BUY" => $arParams['GIFTS_MESS_BTN_BUY'],

                            "GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
                            "GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],
                        )
                    );
                }

            }

        }

    }

}

if (!class_exists('twigModelElement')) {

    class twigModelElement extends twigElement
    {

        public static function applyTemplateModifications(&$arResult, &$arParams, $cacheTime = 604800)
        {
            global $APPLICATION, $USER;
            parent::applyTemplateModifications($arResult, $arParams, $cacheTime);

            unset($arResult['TABS']['tab_headers']['REVIEWS'],$arResult['TABS']['tab_panels']['REVIEWS']);

            $buWrong = true;
            $sDir = $APPLICATION->GetCurDir();
            $sDir = preg_replace('~^/model/~isu', '/', $sDir);
            $sDir = trim($sDir, '/');
            list($sCode, $iElt) = explode('/', $sDir);
            $iElt = (int)trim($iElt);
            $iModelId = 0;           
            
            if ($USER) {

                if (isset($arResult['ANALOGUE']) && !empty($arResult['ANALOGUE'])) {

                    $url = '/model/'.$sCode.'/';

                    foreach($arResult['ANALOGUE'] as $analogue_pkey => $analogue_properties) {

                        foreach ($analogue_properties as $analogue_key => $analogue) {
                            $arResult['ANALOGUE'][$analogue_pkey][$analogue_key]['url'] = $url.$analogue['id'].'/';
                        }
                    }

                }

            }

            if (!empty($sCode) && $iElt > 0) {

                $amFilter = array('CODE' => $sCode, 'IBLOCK_ID' => 17);
                $amSelect = array('ID');

                $rmDb = CIBlockElement::GetList(array(), $amFilter, false, false, $amSelect);

                if ($rmDb) {
                    
            
            
                        
            // file_put_contents(dirname(dirname(__DIR__)).'/info.log', print_r($rmDb, true), FILE_APPEND);
            // file_put_contents(dirname(dirname(__DIR__)).'/info.log', print_r($iElt, true), FILE_APPEND);

                    while ($aModels = $rmDb->GetNext()) {

                        $iModelId = $aModels['ID'];
                        
            // file_put_contents(dirname(dirname(__DIR__)).'/info.log', print_r('modelsid='.$aModels['ID'], true), FILE_APPEND);

                        $rProp = \CIBlockElement::GetProperty(
                            11,
                            $iElt,
                            array(),
                            array("CODE" => 'PRODUCT_MODEL_SHOW'));

                        $bDeny = true;

                        if ($rProp && $aProp = $rProp->GetNext()) {
                            $bDeny = isset($aProp['VALUE_XML_ID']) && $aProp['VALUE_XML_ID'] == 'Y' ? false : true;
                        }

                        $rmpDb = impelCIBlockElement::GetProperty(
                            17,
                            $aModels['ID'],
                            array(),
                            array('CODE' => 'SIMPLEREPLACE_PRODUCTS')
                        );

        // echo "<pre>";
        // var_dump("ModelId =>", $aModels['ID']);
        // var_dump("rmpDb =>", $rmpDb);
        // echo "</pre>";
                        $aProducts = [];
                        if ($rmpDb) {
                            
                            while ($apFields = $rmpDb->GetNext()) {
                                try {
                                    // echo "<pre>";
                                    // var_dump("apFields =>", $apFields);
                                    // echo "</pre>";       
                                    if ((is_string($apFields['VALUE']) || is_int($apFields['VALUE'])) && $apFields['VALUE'] > 0) {
                                        $aProducts[$apFields['VALUE']] = $apFields['VALUE'];
                                    }else if(is_array($apFields['VALUE'])){
                                        foreach ($apFields['VALUE'] as $value) {
                                            $aProducts[$value] = $value;
                                        }
                                    }


                                }
                                catch(Exception $ex) {
                                    
                                }
                                
                            }

                        }

                        // exit(0);

                        if (isset($aProducts[$iElt])) {
                            $buWrong = false;
                        }

                        $buWrong = !empty($bDeny) ? true : $buWrong;

                    }

                }

            }

            if (!$buWrong) {

                $current = twigReplaceModel::getReplaces($iModelId, $iElt, true);
                twigReplaceModel::emptyCache();

                $arResult['chains'] = array($current['replaces']['model'] => "/model/" . $sCode . "/", $current['replaces']['productTitle'] => '');

                $arResult['elt_models_keywords'] = $current['replaces']['elt_models_keywords'];
                $arResult['elt_models_description'] = $current['replaces']['elt_models_description'];
                $arResult['elt_models_title'] = $current['replaces']['elt_models_title'];

                $arResult['models_h1'] = $current['replaces']['elt_models_h1'];
                $arResult['text_for_models'] = $current['replaces']['text_for_elt_models'];


                $arResult['DISPLAY_PROPERTIES']['MANUFACTURER_DETAIL']['~VALUE'] =
                $arResult['DISPLAY_PROPERTIES']['MANUFACTURER_DETAIL']['VALUE'] =
                $arResult['DISPLAY_PROPERTIES']['MANUFACTURER_DETAIL']['DISPLAY_VALUE'] =
                $arResult['DISPLAY_PROPERTIES']['MANUFACTURER_DETAIL']['VALUE_ENUM'] =
                    $current['replaces']['brand'];

                unset($current);

            }

            //$arResult['models_h1'] = $productTitle;
            //$arResult["text_for_models"] = '';

            if ($buWrong) {
                //CHTTP::SetStatus("404 Not Found");
                //@define("ERROR_404","Y");
                LocalRedirect('/model/' . $sCode . '/');
            }

        }

        public static function hasRedirect() {

            global $APPLICATION;

            $buWrong = true;
            $sDir = $APPLICATION->GetCurDir();
            $sDir = preg_replace('~^/model/~isu', '/', $sDir);
            $sDir = trim($sDir, '/');
            list($sCode, $iElt) = explode('/', $sDir);
            $iElt = (int)trim($iElt);
            $iModelId = 0;

            if (!empty($sCode) && $iElt > 0) {

                $amFilter = array('CODE' => $sCode, 'IBLOCK_ID' => 17);
                $amSelect = array('ID');

                $rmDb = CIBlockElement::GetList(array(), $amFilter, false, false, $amSelect);

                if ($rmDb) {

                    while ($aModels = $rmDb->GetNext()) {

                        $iModelId = $aModels['ID'];

                        $rProp = \CIBlockElement::GetProperty(
                            11,
                            $iElt,
                            array(),
                            array("CODE" => 'PRODUCT_MODEL_SHOW'));

                        $bDeny = true;

                        if ($rProp && $aProp = $rProp->GetNext()) {
                            $bDeny = isset($aProp['VALUE_XML_ID']) && $aProp['VALUE_XML_ID'] == 'Y' ? false : true;
                        }

                        $rmpDb = impelCIBlockElement::GetProperty(
                            17,
                            $aModels['ID'],
                            array(),
                            array('CODE' => 'SIMPLEREPLACE_PRODUCTS')
                        );

                        $aProducts = [];

                        if ($rmpDb) {

                            while ($apFields = $rmpDb->GetNext()) {
  
                                if ((is_string($apFields['VALUE']) || is_int($apFields['VALUE'])) && $apFields['VALUE'] > 0) {
                                    $aProducts[$apFields['VALUE']] = $apFields['VALUE'];
                                }else if(is_array($apFields['VALUE'])){
                                    foreach ($apFields['VALUE'] as $value) {
                                        $aProducts[$value] = $value;
                                    }
                                }
                            }

                        }

                        if (isset($aProducts[$iElt])) {
                            $buWrong = false;
                        }

                        $buWrong = !empty($bDeny) ? true : $buWrong;

                    }

                }

            }

            //$arResult['models_h1'] = $productTitle;
            //$arResult["text_for_models"] = '';

            if ($buWrong) {
                //CHTTP::SetStatus("404 Not Found");
                //@define("ERROR_404","Y");
                LocalRedirect('/model/' . $sCode . '/');
            }

            return $buWrong;

        }

        public static function printSeoAndTitlesAtEpilog(&$arResult, &$arParams)
        {
            global $APPLICATION;

            static::replaceDomain($arResult['ID']);

            $sDir = $APPLICATION->GetCurDir();
            $sDir = preg_replace('~^/model/~isu', '/', $sDir);
            $sDir = trim($sDir, '/');
            list($sCode, $iElt) = explode('/', $sDir);
            $iElt = (int)trim($iElt);


            $rDb = CIBlockElement::GetById($iElt);

            if ($rDb && $aElt = $rDb->GetNext()) {
                if (isset($aElt['DETAIL_PAGE_URL']) && !empty($aElt['DETAIL_PAGE_URL'])) {

                    $canonicalURL = $aElt['DETAIL_PAGE_URL'];
                    $canonicalURL = IMPEL_PROTOCOL . IMPEL_SERVER_NAME . $canonicalURL;
                    $APPLICATION->AddHeadString('<link rel="canonical" href="' . $canonicalURL . '" />');

                }
            }


            if (!empty($arResult['elt_models_keywords']))
                $APPLICATION->SetPageProperty("keywords", $arResult['elt_models_keywords']);

            if (!empty($arResult['elt_models_description']))
                $APPLICATION->SetPageProperty("description", $arResult['elt_models_description']);

            if (!empty($arResult['elt_models_title']))
                $APPLICATION->SetTitle($arResult['elt_models_title']);

            if (!empty($arResult['elt_models_title']))
                $APPLICATION->SetPageProperty("title", $arResult['elt_models_title']);


            if (isset($arResult['chains'])
                && !empty($arResult['chains'])
                && (sizeof($arResult['chains']) > 1)) {

                $bfHref = current($arResult['chains']);
                $bfName = key($arResult['chains']);
                next($arResult['chains']);
                $bsName = key($arResult['chains']);

                $APPLICATION->AddChainItem($bfName, $bfHref);
                $APPLICATION->AddChainItem($bsName);

            }

            static::hasRedirect();


        }
    }
}


if(!class_exists('twigViewedList')){
    abstract class twigViewedListAll{
        abstract static function applyViewedListTemplateModifications(&$arResult);
    }

    class twigViewedList extends twigViewedListAll{

        use twigTemplateServices;

        public static function applyViewedListTemplateModifications(&$arResult){

            $emptyPreview = array();
            static::getEmptyPreview($emptyPreview);
            $emptyPreview = $emptyPreview['arEmptyPreview'];
            $emptyPreview = array_change_key_case($emptyPreview, CASE_LOWER);

            foreach($arResult as $key => $val)
            {
                $img = "";
                if ($val["DETAIL_PICTURE"] > 0)
                    $img = $val["DETAIL_PICTURE"];
                elseif ($val["PREVIEW_PICTURE"] > 0)
                    $img = $val["PREVIEW_PICTURE"];

                $file = CFile::ResizeImageGet($img, static::$imageDimensions['thumb'], BX_RESIZE_IMAGE_PROPORTIONAL, true);

                if(!(isset($file['src'])
                    && file_exists($_SERVER['DOCUMENT_ROOT'].$file['src']))){
                    $file = $emptyPreview;
                }

                if(isset($file['src'])
                    && file_exists($_SERVER['DOCUMENT_ROOT'].$file['src'])){


                    $val["PICTURE"] = $file;
                    $val["PICTURE"]	= static::getThumb($val["PICTURE"]["src"], static::$imageDimensions['thumb']);
                    $val["PICTURE"] = array_change_key_case($val["PICTURE"], CASE_LOWER);
                }

                $arResult[$key] = $val;

            }
        }
    }
}


if (!class_exists('twigSet')) {

    abstract class twigSetAll
    {
        abstract static function applySetListTemplateModifications(&$arResult, &$arParams);

        abstract static function createSet(&$arResult);

        abstract static function fillSetList(&$arResult);

        abstract static function getSetPhoto($aSrc);

        abstract static function getPhotoArr($file_id);

        abstract static function canIByProduct($product_id);
    }

    class twigSet extends twigSetAll
    {

        use twigTemplateServices;

        public static function applySetListTemplateModifications(&$arResult, &$arParams)
        {

            static::setImageDimensions();
            static::getEmptyPreview($arParams);

            static::getConcentProcessingLink($arResult);

            static::createSet($arResult);

            static::getOldPrice($arResult);

            $photo = CIBlockPriceTools::getDoublePicturesForItem($arResult, $arParams['ADD_PICT_PROP']);
            if (empty($photo)) {
                $photo['PICT'] = $arParams['arEmptyPreview'];
            }

            $arResult['PHOTO_SRC'] = $photo['PICT']['SRC'];
        }

        public static function createSet(&$arResult)
        {

            $setArray = array();

            $rSetDB = CIBlockElement::GetProperty(
                11,
                $arResult['ID'],
                array(),
                array('CODE' => 'SET')
            );

            if ($rSetDB) {

                while ($aSetFields = $rSetDB->GetNext()) {

                    if (isset($aSetFields['VALUE'])
                        && !empty($aSetFields['VALUE'])
                        && canYouBuy($aSetFields['VALUE'])) {

                        $setArray[] = $aSetFields['VALUE'];

                    }

                }

            }

            $arResult['SET'] = $setArray;
            static::fillSetList($arResult);

        }

        public static function canIByProduct($product_id)
        {
            $quantity = 0;
            $can_buy = null;

            $cacheFile = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/cache/' . md5('on_stock') . '.php';

            if (file_exists($cacheFile)) {

                require $cacheFile;

                if (isset($acOnStock[$product_id])) {
                    $can_buy = $acOnStock[$product_id];
                }
            }

            if (is_null($can_buy)) {
                $can_buy = canYouBuy($product_id);
            }

            if ($can_buy) {

                $buy_id = getBondsProduct($product_id);

                $rsStore = CCatalogStoreProduct::GetList(
                    array(),
                    array('PRODUCT_ID' => $buy_id, "!STORE_ID" => array(3, 6, 10)),
                    false,
                    false
                );

                $in_stock_labels = array();

                if ($rsStore) {

                    while ($arStore = $rsStore->Fetch()) {

                        $amount = (float)$arStore['AMOUNT'];
                        $quantity += $amount;

                    }

                }

                $can_buy = $quantity > 0 ? $can_buy : false;

            }

            return $can_buy;
        }


        public static function fillSetList(&$arResult)
        {

            $arResult['SETLIST'] = array();
            $arResult['SETLIST_COUNT'] = 0;

            if (!empty($arResult['SET'])) {

                $setsArFilter = array('ID' => $arResult['SET']);
                $setsArSelect = array('ID', 'DETAIL_PAGE_URL', 'NAME', 'PREVIEW_PICTURE', 'DETAIL_PICTURE');
                $rSetsDB = CIBlockElement::GetList(array(), $setsArFilter, false, false, $setsArSelect);

                $arResult['DISPLAY_PROPERTIES']['OLD_PRICE']['~VALUE'] = 0;

                if ($rSetsDB) {

                    $arResult['SETLIST'] = array(
                        'NAME' => array(),
                        'LINK' => array(),
                        'SRC' => array(),
                        'PRICES' => array(),
                        'ID' => array(),
                    );

                    while ($aSets = $rSetsDB->GetNext()) {

                        if (!static::canIByProduct($aSets['ID'])) {
                            $arResult['SETLIST'] = array();
                            $arResult['SETLIST_COUNT'] = 0;
                            return ;
                        }

                        $photo = static::getSetPhoto($aSets);

                        $price = CCatalogProduct::GetOptimalPrice($aSets['ID'], 1);

                        $price = (isset($price['PRICE'])
                            && isset($price['PRICE']['PRICE'])
                            && $price['PRICE']['PRICE'] > 0
                            && isset($price['PRICE']['CURRENCY']))
                            ? $price['PRICE']['PRICE'] : 0;

                        $arResult['DISPLAY_PROPERTIES']['OLD_PRICE']['~VALUE'] += $price;

                        $arResult['SETLIST']['ID'][] = $aSets['ID'];
                        $arResult['SETLIST']['NAME'][] = $aSets['NAME'];
                        $arResult['SETLIST']['LINK'][] = $aSets['DETAIL_PAGE_URL'];
                        $arResult['SETLIST']['SRC'][] = $photo['SRC'];

                    }

                    $arResult['SETLIST_COUNT'] = sizeof($arResult['SETLIST']['SRC']);

                }


            }

        }

        public static function getSetPhoto($aSrc)
        {

            $photo = array();

            if (isset($aSrc['PREVIEW_PICTURE'])
                && !empty($aSrc['PREVIEW_PICTURE'])) {

                $photo = static::getPhotoArr($aSrc['PREVIEW_PICTURE']);

            }

            if (empty($photo)
                && isset($aSrc['DETAIL_PICTURE'])
                && !empty($aSrc['DETAIL_PICTURE'])) {

                $photo = static::getPhotoArr($aSrc['DETAIL_PICTURE']);

            }

            if (empty($photo)) {

                $rSetMPDB = CIBlockElement::GetProperty(
                    11,
                    $aSrc['ID'],
                    array(),
                    array('CODE' => 'MORE_PHOTO')
                );

                if ($rSetMPDB) {

                    $aSetMPFields = $rSetMPDB->GetNext();

                    if (isset($aSetMPFields['VALUE'])
                        && !empty($aSetMPFields['VALUE'])) {

                        $photo = static::getPhotoArr($aSetMPFields['VALUE']);

                    }

                }

            }

            if (!(isset($photo['SRC'])
                && file_exists($_SERVER['DOCUMENT_ROOT'] . $photo['SRC']))) {
                $photo = static::$arEmptyPreview;
            }

            if (isset($photo['SRC'])
                && file_exists($_SERVER['DOCUMENT_ROOT'] . $photo['SRC'])) {

                $photo = static::getThumb($photo['SRC'], static::$imageDimensions['main']);

            } else {

                $photo = array();
            }

            return $photo;

        }

        public static function getPhotoArr($file_id)
        {

            $photo = array();
            $photo = CFile::GetFileArray($file_id);

            return $photo;

        }
    }

}

if(!class_exists('twigReviews')){

    abstract class twigReviewsAll{
        abstract static function applyReviewsTemplateModifications(&$arResult,&$arParams);
    }

    class twigReviews extends twigReviewsAll{
        public static function applyReviewsTemplateModifications(&$arResult,&$arParams){

            global $DB;

            $arParams["form_index"] = randString(4);

            $arParams["FORM_ID"] = "REPLIER".$arParams["form_index"];
            $arParams["tabIndex"] = intVal(intval($arParams["TAB_INDEX"]) > 0 ? $arParams["TAB_INDEX"] : 10);

            $ibCommentsId = false;

            $ibRes = CIBlock::GetList(
                Array(),
                Array(
                    'CODE'=>'commentsrating',

                )
            );


            if($ibRes
                && $arIblock = $ibRes->Fetch()){

                $ibCommentsId = $arIblock['ID'];

                if($ibCommentsId){

                    if(isset($arParams['ELEMENT_ID'])
                        && !empty($arParams['ELEMENT_ID'])){
                        $ibERes = CIBlockElement::GetByID((int)$arParams['ELEMENT_ID']);

                        if($ibERes && $ibEAr = $ibERes->GetNext()){
                            $arResult["ELEMENT_NAME"] = $ibEAr["NAME"];
                        }

                    }

                    $dbFres = $DB->Query('SELECT `id`,`review_id`,`topic_id` FROM `b_comment_answer` WHERE `post_id`='.(int)$arParams['ELEMENT_ID']);
                    $arTopics = $arReviews = array();

                    if($dbFres){

                        while($arFres = $dbFres->Fetch()){


                            if(!isset($arTopics[$arFres['topic_id']])){
                                $arTopics[$arFres['topic_id']] = array();
                            }


                            $arReviews[$arFres['review_id']] = $arFres['topic_id'];
                        }
                    }

                    foreach ($arResult["MESSAGES"] as $kmes => $mes){

                        if(isset($arReviews[$mes['ID']])){


                            if(!isset($arResult["MESSAGES"][$arReviews[$mes['ID']]]['REVIEWS'])){

                                $arResult["MESSAGES"][$arReviews[$mes['ID']]]['REVIEWS'] = array();

                            }

                            $arResult["MESSAGES"][$arReviews[$mes['ID']]]['REVIEWS'][$mes['ID']] = $mes;

                            unset($arResult["MESSAGES"][$kmes]);
                            continue;
                        }

                        $dbFRes = CIBlockElement::GetList(
                            Array(),
                            Array(
                                "IBLOCK_ID" => $ibCommentsId,
                                "PROPERTY_forum_topic_id" => $mes["ID"]
                            ),
                            false,
                            false,
                            Array("ID","PROPERTY_vote_count","PROPERTY_vote_sum","PROPERTY_rating")
                        );

                        if($dbFRes && $dbFAr = $dbFRes->GetNext()){

                            $arResult["MESSAGES"][$kmes]["vote_count"] = $dbFAr["PROPERTY_VOTE_COUNT_VALUE"];
                            $arResult["MESSAGES"][$kmes]["vote_sum"] = $dbFAr["PROPERTY_VOTE_SUM_VALUE"];
                            $arResult["MESSAGES"][$kmes]["rating"] = $dbFAr["PROPERTY_RATING_VALUE"];
                        }

                    }

                }

            }

        }
    }
}

if(!class_exists('twigModels')){

    abstract class twigModelsAll{
        abstract static function applyModelsTemplateModifications(&$arResult,&$arParams);
        abstract static function setComIndCodes(&$arResult,&$arParams);
        abstract static function setModelTitle(&$arResult,&$arParams);
        abstract static function setInstruction(&$arResult,&$arParams);
        abstract static function setIndcodeList(&$arResult,&$arParams);
        abstract static function getFilterParams(&$arResult);
        abstract static function replaceFilterTypes($sfilterName,$sfType);
    }

    class twigModels extends twigModelsAll{

        use twigTemplateServices;

        public static function setIndcodeList(&$arResult,&$arParams){

            global $APPLICATION, $USER;


            if(     isset($arResult["DISPLAY_PROPERTIES"])
                &&  isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"])
                &&  isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"])
                && !empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"])){

                $indcode = array();
                $hasCode = array();

                foreach($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"] as $productNum => $productId){
                    if($productId != $arResult["skipIndCodeId"]){
                        $indcode[] = trim(strip_tags($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"][$productNum]));
                    }
                }

                if(!empty($indcode)) {

                    $basePage = $APPLICATION->GetCurPage();

                    $hasFilter = mb_stripos($basePage, '/filter/') !== false ? true : false;

                    if (preg_match('~/indcode/[^/]+?/~', $basePage)) {
                        $basePage = preg_replace('~/indcode/[^/]+?/~', '/', $basePage);
                    }

                    $basePage = '/' . trim($basePage, '/') . '/';

                    if (mb_stripos($basePage, '/filter/') === false && $hasFilter) {
                        $basePage .= 'filter/';
                    }

                    $basePage = str_ireplace('/filter/clear/', '/', $basePage);

                    $trParams = Array(
                        "max_len" => "100",
                        "change_case" => "L",
                        "replace_space" => "_",
                        "replace_other" => "_",
                        "delete_repeat_replace" => "true",
                    );

                    foreach ($indcode as $value){

                        if (in_array($value, $hasCode))
                            continue;

                        $hasCode[] = $value;

                        $code = trim(CUtil::translit(trim(strip_tags($value)), LANGUAGE_ID, $trParams));

                        if (mb_stripos($basePage, '/filter/') !== false) {

                            $parts = explode('/filter/', $basePage, 2);
                            $linkPage = rtrim($parts[0], '/') . '/indcode/' . $code . '/';


                            if (!empty($parts[1])) {
                                $linkPage .= 'filter/' . trim($parts[1], '/') . '/';
                            }

                        } else {

                            $linkPage = '/' . trim($basePage, '/') . '/indcode/' . $code . '/';

                        }

                        $value = trim(strip_tags($value));
                        $arResult['INDCODE'][$value] = $linkPage;

                    }

                }

            }

        }

        public static function setInstruction(&$arResult,&$arParams){
            $instructions = array();

            if(     isset($arResult["DISPLAY_PROPERTIES"])
                &&  isset($arResult["DISPLAY_PROPERTIES"]["instruction"])
                &&  isset($arResult["DISPLAY_PROPERTIES"]["instruction"]["FILE_VALUE"])
                &&  isset($arResult["DISPLAY_PROPERTIES"]["instruction"]["FILE_VALUE"]["SRC"])
                && !empty($arResult["DISPLAY_PROPERTIES"]["instruction"]["FILE_VALUE"]["SRC"])) {

                $arResult['file_src'] = $arResult["DISPLAY_PROPERTIES"]["instruction"]["FILE_VALUE"]["SRC"];
                $arResult['file_extension'] = mb_strtoupper(pathinfo($arResult['file_src'], PATHINFO_EXTENSION));
                $arResult['file_basename'] = mb_strtoupper(pathinfo($arResult['file_src'], PATHINFO_BASENAME));

            }
        }

        public static function setModelTitle(&$arResult,&$arParams){

            global $APPLICATION;

            $isVersion       = isset($arResult["DISPLAY_PROPERTIES"])
            &&isset($arResult["DISPLAY_PROPERTIES"]["VERSION"])
            &&isset($arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"])
            &&!empty($arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"])
            && $arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"] == 'Да'
                ? true
                : false;

            $default_title  = isset($arParams['DEFAULT_TITLE'])
            &&!empty($arParams['DEFAULT_TITLE'])
                ? trim($arParams['DEFAULT_TITLE'])
                : '';

            if(!$isVersion){

                $declension_models = unserialize(\COption::GetOptionString('my.stat', 'declension_models', array(), SITE_ID) || "");

            } else {

                $declension_models = unserialize(\COption::GetOptionString('my.stat', 'declension_series_models', array(), SITE_ID) || "");

            }

            $dreplaces = array(
                '[product_type_dec]' => '',
                '[product_type]' => '',
                '[brand]' => '',
                '[model]' => '',
                '[indcode]' => ''
            );

            $curPage = $APPLICATION->GetCurPage();

            if(preg_match('~/indcode/([^/]+?)/~isu',$curPage,$matches)){

                if(isset($matches[1]) && !empty($matches[1])){
                    $curIndcode = trim($matches[1]);

                    foreach($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"]
                            as $productNum => $productName){

                        $productName = trim(strip_tags($productName));

                        $trParams = Array(
                            "max_len" => "100",
                            "change_case" => "L",
                            "replace_space" => "_",
                            "replace_other" => "_",
                            "delete_repeat_replace" => "true",
                        );

                        $productCode = trim(CUtil::translit(trim(strip_tags($productName)), LANGUAGE_ID, $trParams));

                        if($productCode == $curIndcode){
                            $dreplaces['[indcode]'] = trim(strip_tags($productName));
                            break;
                        }

                    }

                }

            }

            if(isset($arResult["DISPLAY_PROPERTIES"])
                &&isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"])
                &&isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE_ENUM_ID"])
                &&!empty($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE_ENUM_ID"])
                &&isset($declension_models["declension"])
                &&is_array($declension_models["declension"])
                &&sizeof($declension_models["declension"])){

                foreach($declension_models["type_of_product"] as $dnumber => $typeID){

                    if($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE_ENUM_ID"] == $typeID
                        &&isset($declension_models["declension"][$dnumber])
                        &&trim($declension_models["declension"][$dnumber]) != ""
                    ){
                        $dreplaces['[product_type_dec]'] = trim($declension_models["declension"][$dnumber]);
                    }
                }

            }

            if(isset($arResult["DISPLAY_PROPERTIES"])
                &&isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"])
                &&isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"])
                &&!empty($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"])){

                $dreplaces['[product_type]'] = $arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"];

            }

            if(isset($arResult["DISPLAY_PROPERTIES"])
                &&isset($arResult["DISPLAY_PROPERTIES"]["manufacturer"])
                &&isset($arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"])
                &&!empty($arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"])){

                $dreplaces['[brand]'] = $arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"];

            }

            if(isset($arResult["DISPLAY_PROPERTIES"])
                &&isset($arResult["DISPLAY_PROPERTIES"]["model_new"])
                &&isset($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"])
                &&!empty($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"])){

                $dreplaces['[model]'] = ($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"]);

            }

            $manufacturer   = isset($arResult["DISPLAY_PROPERTIES"])
            &&isset($arResult["DISPLAY_PROPERTIES"]["manufacturer"])
            &&isset($arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"])
            &&!empty($arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"])
                ? trim($arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"])
                : '';

            $model_new      = isset($arResult["DISPLAY_PROPERTIES"])
            &&isset($arResult["DISPLAY_PROPERTIES"]["model_new"])
            &&isset($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"])
            &&!empty($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"])
                ? trim($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"])
                : '';

            if(!$isVersion) {

                $text_for_models = \COption::GetOptionString('my.stat', 'text_for_models', '', SITE_ID);
                $models_h1 = \COption::GetOptionString('my.stat', 'models_h1', '', SITE_ID);

            } else {

                $text_for_models = \COption::GetOptionString('my.stat', 'text_for_models_version', '', SITE_ID);
                $models_h1 = \COption::GetOptionString('my.stat', 'models_version_h1', '', SITE_ID);

            }

            $text_for_models = str_ireplace(array_keys($dreplaces),array_values($dreplaces),$text_for_models);
            $models_h1 = str_ireplace(array_keys($dreplaces),array_values($dreplaces),$models_h1);
            $default_title = sprintf($default_title,' '.$manufacturer.' '.$model_new.'');

            $arResult['models_h1'] = $models_h1;
            $arResult['default_title'] = $default_title;
            $arResult['text_for_models'] = $text_for_models;

        }

        public static function setComIndCodes(&$arResult,&$arParams){

            $skipProdId = 0;

            $arProdFilter = Array(
                "CODE" => "bez_tovara",
                "IBLOCK_ID" => 11
            );

            $arProdSelect = Array("ID");

            $resProdDB = CIBlockElement::GetList(Array(), $arProdFilter, false, false, $arProdSelect);

            $resProdArr = Array();

            if($resProdDB) {
                $resProdArr = $resProdDB->GetNext();

                if(isset($resProdArr['ID'])
                    && !empty($resProdArr['ID'])){

                    $skipProdId = $resProdArr['ID'];
                }
            }

            $arResult['skipProdId'] = $skipProdId;

            $skipViewId = 0;

            $arViewFilter = Array(
                "CODE" => "bez_vida",
                "IBLOCK_ID" => 34
            );

            $arViewSelect = Array("ID");

            $resViewDB = CIBlockElement::GetList(Array(), $arViewFilter, false, false, $arViewSelect);

            $resViewArr = Array();

            if($resViewDB) {
                $resViewArr = $resViewDB->GetNext();

                if(isset($resViewArr['ID'])
                    && !empty($resViewArr['ID'])){

                    $skipViewId = $resViewArr['ID'];
                }
            }

            $arResult['skipViewId'] = $skipViewId;

            $skipIndCodeId = 0;

            $arCodeFilter = Array(
                "CODE" => "bez_ind_koda",
                "IBLOCK_ID" => 35
            );

            $arCodeSelect = Array("ID");

            $resCodeDB = CIBlockElement::GetList(Array(), $arCodeFilter, false, false, $arCodeSelect);

            $resCodeArr = Array();

            if($resCodeDB) {
                $resCodeArr = $resCodeDB->GetNext();

                if(isset($resCodeArr['ID'])
                    && !empty($resCodeArr['ID'])){

                    $skipIndCodeId = $resCodeArr['ID'];

                }
            }

            $arResult['skipIndCodeId'] = $skipIndCodeId;

            $skipComCodeId = 0;

            $arCodeFilter = Array(
                "CODE" => "bez_com_koda",
                "IBLOCK_ID" => 36
            );

            $arCodeSelect = Array("ID");

            $resCodeDB = CIBlockElement::GetList(Array(), $arCodeFilter, false, false, $arCodeSelect);

            $resCodeArr = Array();

            if($resCodeDB) {
                $resCodeArr = $resCodeDB->GetNext();

                if(isset($resCodeArr['ID'])
                    && !empty($resCodeArr['ID'])){

                    $skipComCodeId = $resCodeArr['ID'];

                }
            }

            $arResult['skipComCodeId'] = $skipComCodeId;

            if(
                isset($arResult["DISPLAY_PROPERTIES"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"])){

                $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"];

                $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"];
            }

            if(
                isset($arResult["DISPLAY_PROPERTIES"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["VALUE"])){

                $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["DISPLAY_VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["DISPLAY_VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["DISPLAY_VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["DISPLAY_VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["DISPLAY_VALUE"];

                $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["VALUE"];
            }

            if(
                isset($arResult["DISPLAY_PROPERTIES"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]["DISPLAY_VALUE"])){

                $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]["DISPLAY_VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]["DISPLAY_VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]["DISPLAY_VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]["DISPLAY_VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]["DISPLAY_VALUE"];


                $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]["VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]["VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]["VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]["VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]["VALUE"];
            }

            if(
                isset($arResult["DISPLAY_PROPERTIES"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"])){

                $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"];

                $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"];
            }

            if(
                isset($arResult["DISPLAY_PROPERTIES"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["VIEW"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"])){

                $arResult["DISPLAY_PROPERTIES"]["VIEW"]["DISPLAY_VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["VIEW"]["DISPLAY_VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["VIEW"]["DISPLAY_VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["VIEW"]["DISPLAY_VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["VIEW"]["DISPLAY_VALUE"];

                $arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"];
            }

            if(
                isset($arResult["DISPLAY_PROPERTIES"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["POSITION"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["POSITION"]["DISPLAY_VALUE"])){

                $arResult["DISPLAY_PROPERTIES"]["POSITION"]["DISPLAY_VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["POSITION"]["DISPLAY_VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["POSITION"]["DISPLAY_VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["POSITION"]["DISPLAY_VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["POSITION"]["DISPLAY_VALUE"];


                $arResult["DISPLAY_PROPERTIES"]["POSITION"]["VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["POSITION"]["VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["POSITION"]["VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["POSITION"]["VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["POSITION"]["VALUE"];
            }

            if(
                isset($arResult["DISPLAY_PROPERTIES"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]) &&
                isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["DISPLAY_VALUE"])){

                $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["DISPLAY_VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["DISPLAY_VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["DISPLAY_VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["DISPLAY_VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["DISPLAY_VALUE"];

                $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["VALUE"] =
                    !empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["VALUE"])
                    && is_string($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["VALUE"])
                        ? array($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["VALUE"])
                        :  $arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["VALUE"];
            }

        }

        public static function getFilterParams(&$arResult){

            global $APPLICATION;

            $arResult['ftypes'] = '';
            $basePage = $APPLICATION->GetCurPage();
            $hasFilter = mb_stripos($basePage,'/filter/') !== false ? true : false;

            if($hasFilter){

                $aEnumsIdToCode = $aEnums = array();

                if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/enum_cache.php')){
                    require $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/enum_cache.php';
                }

                $atypes = array();
                $stypes = '';

                $filterUrl = preg_replace('~/clear/.*~isu','',$basePage);
                $filterUrl = preg_replace('~.*/filter/~isu','',$filterUrl);
                $filterUrl = trim($filterUrl);

                if(!empty($filterUrl)){
                    $aFilterParts = explode('/',$filterUrl);

                    $aFilterParts = array_map(function($value){
                        $value = trim($value,'/');
                        $value = trim($value);
                        return $value;
                    },$aFilterParts);

                    $aFilterParts = array_filter($aFilterParts);

                    foreach($aFilterParts as $sPart){

                        list($sPropCode,$sPropVal) = explode('-is-',$sPart,2);

                        if(!empty($sPropCode)
                            && !empty($sPropVal)){

                            if(mb_stripos($sPropVal,'-or-') !== false){
                                $aPropVals = explode('-or-',$sPropVal);
                            } else {
                                $aPropVals = array($sPropVal);
                            }

                            $aPropVals = array_map('trim',$aPropVals);
                            $aPropVals = array_filter($aPropVals);

                            if(!empty($aPropVals)){

                                if(isset($aEnumsIdToCode[mb_strtoupper($sPropCode)])){

                                    $propertyId = $aEnumsIdToCode[mb_strtoupper($sPropCode)];

                                    foreach($aEnums[$propertyId] as $aPropValues){

                                        if(in_array($aPropValues['XML_ID'],$aPropVals)){

                                            $atypes[] = (trim($aPropValues["VALUE"]));

                                        }

                                    }


                                } else {

                                    $dProps = CIBlockProperty::GetList(
                                        Array(),
                                        Array(
                                            "IBLOCK_ID" => 11,
                                            "CODE" => $sPropCode
                                        )
                                    );

                                    if($dProps){

                                        while ($aProps = $dProps->GetNext()) {

                                            foreach($aPropVals as $stPropVal){

                                                $dEnums = CIBlockPropertyEnum::GetList(
                                                    Array(),
                                                    Array(
                                                        "PROPERTY_ID" => $aProps["ID"],
                                                        "XML_ID" => $stPropVal
                                                    )
                                                );

                                                if($dEnums){
                                                    while($aEnums = $dEnums->GetNext()){


                                                        if(isset($aEnums["VALUE"])){
                                                            $atypes[] = (trim($aEnums["VALUE"]));
                                                        }
                                                    }
                                                }

                                            }

                                        }

                                    }

                                }

                            }

                        }

                    }

                }

                if(!empty($atypes)) {

                    $stypes = join(', ', $atypes);
                    $arResult['ftypes'] = $stypes;

                }

            }

        }

        public static function replaceFilterTypes($sfilterName,$sfType){
            preg_match('~\[ftypes\:(.*)\]~isu',$sfilterName,$aMatches);
            $sDefault = '';

            if(isset($aMatches[0])){
                $sDefault = trim($aMatches[1]);
            }

            $sfType = empty($sfType) ? $sDefault : $sfType;
            $sfilterName = preg_replace('~\[ftypes\:(.*)\]~isu', $sfType, $sfilterName);

            $sfilterName = preg_replace('~\s*?\(\s*?\)~isu','',$sfilterName);
            $sfilterName = trim($sfilterName);

            return $sfilterName;
        }

        public static function fixSimpleProperties(&$arResult){

            global $USER;

            foreach ($arResult["DISPLAY_PROPERTIES"] as $key => $array) {

                if (stripos($key,'SIMPLEREPLACE_') !== false) {

                    $arResult["DISPLAY_PROPERTIES"][$key]["DISPLAY_VALUE"] = is_array($arResult["DISPLAY_PROPERTIES"][$key]["DISPLAY_VALUE"]) ? current($arResult["DISPLAY_PROPERTIES"][$key]["DISPLAY_VALUE"]) : $arResult["DISPLAY_PROPERTIES"][$key]["DISPLAY_VALUE"];
                    $arResult["DISPLAY_PROPERTIES"][$key]["DISPLAY_VALUE"] = json_decode($arResult["DISPLAY_PROPERTIES"][$key]["DISPLAY_VALUE"],true);
                    if(is_array($arResult["DISPLAY_PROPERTIES"][$key]["DISPLAY_VALUE"])) {
                        $arResult["DISPLAY_PROPERTIES"][$key]["DISPLAY_VALUE"] = array_map('trim',$arResult["DISPLAY_PROPERTIES"][$key]["DISPLAY_VALUE"]);
                    }

                }

            }

        }

        public static function applyModelsTemplateModifications(&$arResult,&$arParams){

            static::fixSimpleProperties($arResult);

            static::setImageDimensions();
            static::getEmptyPreview($arParams);

            static::setComIndCodes($arResult,$arParams);



            if(isset($arResult["DISPLAY_PROPERTIES"])
                &&isset($arResult["DISPLAY_PROPERTIES"]["model_new_link"])
                &&isset($arResult["DISPLAY_PROPERTIES"]["model_new_link"]["VALUE"])
                &&!empty($arResult["DISPLAY_PROPERTIES"]["model_new_link"]["VALUE"])) {

                $model_new_link = trim($arResult["DISPLAY_PROPERTIES"]["model_new_link"]["VALUE"]);
                $arModelLinkFilter = Array(
                    "ID" => $model_new_link,
                    "IBLOCK_ID" => 27,
                );

                $arModelLinkSelect = Array("NAME");

                $resModelLinkDB = CIBlockElement::GetList(Array("PROPERTY_MODEL_NEW_VALUE" => "ASC"), $arModelLinkFilter, false, false, $arModelLinkSelect);

                $resModelLinkArr = Array();
                if($resModelLinkDB){
                    $resModelLinkArr = $resModelLinkDB->GetNext();

                    if(isset($resModelLinkArr["NAME"])
                        && !empty($resModelLinkArr["NAME"])){

                        $arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"] = $resModelLinkArr["NAME"];

                    }

                }

            }

            static::setModelTitle($arResult,$arParams);
            static::setInstruction($arResult,$arParams);
            static::setIndcodeList($arResult,$arParams);

            if(!(isset($arResult["PREVIEW_PICTURE"])
                && !empty($arResult["PREVIEW_PICTURE"]['SRC'])
                && file_exists($_SERVER['DOCUMENT_ROOT'].$arResult["PREVIEW_PICTURE"]['SRC']))){
                $arResult["PREVIEW_PICTURE"] = $arParams['arEmptyPreview'];
            }

            $arResult["PREVIEW_PICTURE"] = static::getThumb($arResult["PREVIEW_PICTURE"]['SRC'],static::$imageDimensions['list']);


        }
    }
}

class TwigSenderEventHandler
{
    public static function onTriggerList($data)
    {

        $data['TRIGGER'] = 'SenderTriggerBuyOrders';

        return $data;
    }
}

AddEventHandler("sender", "OnTriggerList", array("TwigSenderEventHandler","onTriggerList"));

abstract class twigTMPLHFCacheAll{
    abstract static function skipTmplHFCache($arFields);
    abstract static function hasTmplHFCache();
    abstract static function setTmplHFCache($shBlock,$suString);
    abstract static function returnTmplHFBlock($suString);
    abstract static function finalizeTmplHFBlock();
}

class twigTMPLHFCache extends twigTMPLHFCacheAll{

    use twigTemplateServices;
    private static $sAllCached = true;

    public static function skipTmplHFCache($arFields){

        if($arFields['IBLOCK_ID'] == 18
            && $arFields["ID"] > 0
            && file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/template/nmain.txt')
        ) {

            unlink(dirname(dirname(__DIR__)).'/bitrix/tmp/template/nmain.txt');

        }
    }

    public static function hasTmplHFCache(){

        return (file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/template/nmain.txt')) ? true : false;

    }

    public static function setTmplHFCache($shBlock,$suString){

        if(empty($shBlock)) return;

        $sBlockFile = (dirname(dirname(__DIR__)).'/bitrix/tmp/template/'.$suString.'.php'); 
        
        $shBlock = '<?php if(!defined(\'CATALOG_INCLUDED\')) die(); ?>'.$shBlock;

        file_put_contents($sBlockFile,$shBlock);

        echo $shBlockstr;

        static::$sAllCached = (file_exists($sBlockFile)
            && filesize($sBlockFile) > 0)
            ? static::$sAllCached
            : false;


    }

    public static function returnTmplHFBlock($suString){


        $suString = static::getCacheHash($suString);

        $sBlockFile = (dirname(dirname(__DIR__)).'/bitrix/tmp/template/'.$suString.'.php');

        if(file_exists($sBlockFile)
            && file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/template/nmain.txt')){

            require_once($sBlockFile);
        

            return true;

        } else {
            if(file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/template/nmain.txt')){
                unlink(file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/template/nmain.txt'));
            }
            return $suString;

        }

    }

    public static function finalizeTmplHFBlock(){

        if(static::$sAllCached
            && !file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/template/nmain.txt')){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/template/nmain.txt','');
        }

    }

}

if (!class_exists('twigInfoDomain')) {
    class twigInfoDomain {

        public static function getListFilter() {
            return ["PROPERTY_DOMAIN_VALUE" => IMPEL_SERVER_NAME];
        }

        public static function getElementFilter() {

            $code = trim($_REQUEST["CODE"]);
            $id = 0;

            $arSelect = ['ID'];
            $arFilter = [
                'CODE' => $code,
                'IBLOCK_ID' => 13,
                "PROPERTY_DOMAIN_VALUE" => IMPEL_SERVER_NAME,
            ];

            $rsElement = CIBlockElement::GetList(($aOrder = []), $arFilter, false, ($aNavParams = ['nTopCount' => 1]), $arSelect);

            if ($rsElement) {
                if($arElement = $rsElement->GetNext()) {
                    $id = $arElement['ID'];
                }
            }

            return $id;
        }
    }
}

if (!class_exists('twigReplaceModel')) {
    class twigReplaceModel
    {
        use twigTemplateServices;

        const TM_CTIME = 24 * 3600;
        const TM_CFILE = '/bitrix/tmp/mp_cache.php';

        private static bool $bChanged = false;

        private static array $models = [];
        private static array $products = [];
        private static array $apmodels = [];

        public static function emptyCache() {
            static::$models = static::$products = static::$apmodels = [];
        }

        public static function setModelEltTitle(&$arResult)
        {
            static $declension_models_default,
            $text_for_elt_models_default,
            $elt_models_h1_default,
            $elt_models_title_default,
            $elt_models_keywords_default,
            $elt_models_description_default;

            if (!is_string($text_for_elt_models_default)) {
                $declension_models_default = unserialize(\COption::GetOptionString('my.stat', 'declension_models', array(), SITE_ID) || "");
                $text_for_elt_models_default = \COption::GetOptionString('my.stat', 'text_for_elt_models', '', SITE_ID);
                $elt_models_h1_default = \COption::GetOptionString('my.stat', 'elt_models_h1', '', SITE_ID);
                $elt_models_title_default = \COption::GetOptionString('my.stat', 'elt_models_title', '', SITE_ID);
                $elt_models_keywords_default = \COption::GetOptionString('my.stat', 'elt_models_keywords', '', SITE_ID);
                $elt_models_description_default = \COption::GetOptionString('my.stat', 'elt_models_description', '', SITE_ID);
            }

            $declension_models = $declension_models_default;
            $text_for_elt_models = $text_for_elt_models_default;
            $elt_models_h1 = $elt_models_h1_default;
            $elt_models_title = $elt_models_title_default;
            $elt_models_keywords = $elt_models_keywords_default;
            $elt_models_description = $elt_models_description_default;

            $dreplaces = array();

            foreach ($arResult["replaces"] as $key => $value) {
                $dreplaces['[' . $key . ']'] = $value;
            }

            if (isset($arResult["replaces"])
                && isset($arResult["replaces"]["product_type_model_id"])
                && !empty($arResult["replaces"]["product_type_model_id"])
                && isset($declension_models["declension"])
                && is_array($declension_models["declension"])
                && sizeof($declension_models["declension"])) {

                foreach ($declension_models["type_of_product"] as $dnumber => $typeID) {

                    if ($arResult["replaces"]["product_type_model_id"] == $typeID
                        && isset($declension_models["declension"][$dnumber])
                        && trim($declension_models["declension"][$dnumber]) != ""
                    ) {
                        $arResult['replaces']['product_type_model_dec'] = $dreplaces['[product_type_model_dec]'] = mb_strtolower(trim($declension_models["declension"][$dnumber]));
                    }
                }

            }

            $productTitle = $arResult['replaces']['product_type'];
            $productTitle .= (!empty($productTitle) ? ' ' . GetMessage('CATALOG_FOR') . ' ' : '') . $arResult['replaces']['product_type_model_dec'];
            $productTitle .= ' ' . $arResult['replaces']['brand_model'];
            $productTitle .= ' ' . $arResult['replaces']['model'];

            $text_for_elt_models = str_ireplace(array_keys($dreplaces), array_values($dreplaces), $text_for_elt_models);
            $elt_models_h1 = str_ireplace(array_keys($dreplaces), array_values($dreplaces), $elt_models_h1);
            $elt_models_title = str_ireplace(array_keys($dreplaces), array_values($dreplaces), $elt_models_title);
            $elt_models_keywords = str_ireplace(array_keys($dreplaces), array_values($dreplaces), $elt_models_keywords);
            $elt_models_description = str_ireplace(array_keys($dreplaces), array_values($dreplaces), $elt_models_description);

            $arResult['replaces']['productTitle'] = static::firstCharToUpper($productTitle);
            $arResult['replaces']['text_for_elt_models'] = static::firstCharToUpper($text_for_elt_models);
            $arResult['replaces']['elt_models_h1'] = static::firstCharToUpper($elt_models_h1);
            $arResult['replaces']['elt_models_title'] = static::firstCharToUpper($elt_models_title);
            $arResult['replaces']['elt_models_keywords'] = static::firstCharToUpper($elt_models_keywords);
            $arResult['replaces']['elt_models_description'] = static::firstCharToUpper($elt_models_description);

        }

        private static function getProductReplaces($iElt, &$arResult)
        {

            if (isset(static::$products[$iElt])) {
                $replaces = static::$products[$iElt];
            } else {

                $replaces = [];

                $rProp = \CIBlockElement::GetProperty(
                    11,
                    $iElt,
                    array(),
                    array("CODE" => 'TYPEPRODUCT'));

                if ($rProp && $aProp = $rProp->GetNext()) {
                    $replaces['product_type'] = mb_strtolower($aProp['VALUE_ENUM']);
                }

                $rProp = \CIBlockElement::GetProperty(
                    11,
                    $iElt,
                    array(),
                    array("CODE" => 'MANUFACTURER_DETAIL'));

                $replaces['brand'] = '';

                if ($rProp) {
                    while ($aProp = $rProp->GetNext()) {
                        $replaces['brand'] .= (!empty($replaces['brand']) ? ', ' : '') . $aProp['VALUE_ENUM'];
                    }
                }

                $rProp = \CIBlockElement::GetProperty(
                    11,
                    $iElt,
                    array(),
                    array("CODE" => 'ARTNUMBER'));

                $replaces['artnumber'] = '';

                if ($rProp) {
                    while ($aProp = $rProp->GetNext()) {
                        $replaces['artnumber'] = $aProp['VALUE'];
                    }
                }

                static::$products[$iElt] = $replaces;

            }

            $arResult['replaces'] = array_merge($arResult['replaces'], $replaces);

        }

        private static function getModelReplaces($iModelId, &$arResult)
        {

            if (isset(static::$models[$iModelId])) {
                $replaces = static::$models[$iModelId];
            } else {

                $replaces = [];

                $rModel = CIBlockElement::GetById($iModelId);

                if ($rModel) {
                    $aModel = $rModel->GetNext();

                    if (isset($aModel['DETAIL_PAGE_URL'])
                        && !empty($aModel['DETAIL_PAGE_URL'])) {
                        $replaces['DETAIL_PAGE_URL'] = $aModel['DETAIL_PAGE_URL'];
                    }

                }

                $rProp = \CIBlockElement::GetProperty(
                    17,
                    $iModelId,
                    array(),
                    array("CODE" => 'manufacturer'));

                if ($rProp && $aProp = $rProp->GetNext()) {
                    $manufacturer = $aProp['VALUE_ENUM'];
                }

                if (!empty($manufacturer)) {
                    $replaces['brand_model'] = $manufacturer;
                }

                $rProp = \CIBlockElement::GetProperty(
                    17,
                    $iModelId,
                    array(),
                    array("CODE" => 'type_of_product'));

                if ($rProp && $aProp = $rProp->GetNext()) {
                    $replaces['product_type_model'] = mb_strtolower($aProp['VALUE_ENUM']);
                    $replaces['product_type_model_id'] = $aProp['VALUE'];
                }

                $rmpDb = impelCIBlockElement::GetProperty(
                    17,
                    $iModelId,
                    array(),
                    array('CODE' => 'model_new_link')
                );

                if ($rmpDb) {

                    while ($apFields = $rmpDb->GetNext()) {

                        if ($apFields['VALUE']) {

                            $imId = $apFields['VALUE'];
                            if ($imId > 0) {
                                $rmnDb = CIBlockElement::getById($imId);
                                if ($rmnDb && $amnArr = $rmnDb->GetNext()) {
                                    if (isset($amnArr['NAME'])) {
                                        $replaces['model'] = $amnArr['NAME'];
                                    }
                                }
                            }
                        }

                    }

                }

                static::$models[$iModelId] = $replaces;

            }

            $arResult['replaces'] = array_merge($arResult['replaces'], $replaces);

        }

        public static function hasCache($iModelId, $iElt, $bTime = true, $skip = false)
        {

            return false;

        }

        public static function writeCache() {

        }

        public static function getReplaces($iModelId, $iElt, $skip = false)
        {

            $arResult = static::hasCache($iModelId, $iElt, false, $skip);

            if (!$arResult) {

                $arResult['replaces'] = array(
                    'product_type_model_dec' => '',
                    'product_type_model' => '',
                    'product_type' => '',
                    'brand' => '',
                    'brand_model' => '',
                    'model' => '',
                    'artnumber' => '',
                );

                static::getModelReplaces($iModelId, $arResult);
                static::getProductReplaces($iElt, $arResult);
                static::setModelEltTitle($arResult);
                static::$bChanged = true;
                static::$apmodels[$iElt.'_'.$iModelId] = $arResult;

            }

            if (empty($arResult['replaces']['artnumber'])
                || is_null($arResult['replaces']['artnumber'])) {

                $rProp = \CIBlockElement::GetProperty(
                    11,
                    $iElt,
                    array(),
                    array("CODE" => 'ARTNUMBER'));


                if ($rProp) {
                    while ($aProp = $rProp->GetNext()) {
                        $arResult['replaces']['artnumber'] = $aProp['VALUE'];
                    }
                }

            }

            return $arResult;

        }

    }

}