<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?

if (!class_exists('impelDeliveryIntervalExceed')) {

    class impelDeliveryIntervalExceed extends impelDeliveryInterval
    {
        private static $aTimeIntervals = [];

        public static function getIntervalsExceed()
        {

            $aTimeInterval = unserialize(\Bitrix\Main\Config\Option::get("my.stat", "references_timeintervals", array()));
            $aTimeIntervalMax = [];
            $iMaxCount = 0;

            if(isset($aTimeInterval['counts'])) {
                foreach ($aTimeInterval['counts'] as $iTime => $iValue) {
                    if (isset($aTimeInterval['times'][$iTime])) {
                        $aTimeIntervalMax[$aTimeInterval['times'][$iTime]] = $iValue;
                    }
                    $iMaxCount += $iValue;
                }
            }

            $days = parent::deliveryDaysInterval();
            $timeInterval = parent::getTimeInterval();

            if($iMaxCount > 0) {
                foreach ($days as $cNum => $cDay) {
                    $iCount = 0;

                    foreach ($timeInterval as $tText => $tValue) {
                        $iCurrent = static::getCountByDay($cDay, $tValue);

                        if($iCurrent < $aTimeIntervalMax[$tValue]) {
                            static::$aTimeIntervals[$cDay] .=
                                (isset(static::$aTimeIntervals[$cDay]) && !empty(static::$aTimeIntervals[$cDay])
                                    ? ',': '') .$tValue;
                        }

                        $iCount += $iCurrent;
                    }

                    if ($iMaxCount <= $iCount) {
                        unset($days[$cNum]);
                    }
                }
            }

            return $days;
        }

        public static function getcTimeIntervals() {
            return static::$aTimeIntervals;
        }

        public static function getCountByDay($cDay, $cTime)
        {
            global $DB;

            $sSql = 'SELECT COUNT(id) as count FROM b_intervals WHERE date = \'' . $DB->ForSql($cDay) . '\' and intervaluse	= \'' . $DB->ForSql($cTime) . '\'';

            $rDb = $DB->Query($sSql);

            $iCount = 0;

            if ($rDb && $aDb = $rDb->fetch()) {
                $iCount = $aDb['count'];
            }

            return $iCount;

        }


    }

    if (!function_exists('properties_usort')) {
        function properties_usort($a, $b)
        {
            if ($a['SORT'] == $b['SORT']) {
                return 0;
            }
            return ($a['SORT'] < $b['SORT']) ? -1 : 1;
        }
    }

    if (!function_exists("showFilePropertyField")) {
        function showFilePropertyField($name, $property_fields, $values, $max_file_size_show = 50000)
        {
            $res = "";

            if (!is_array($values) || empty($values))
                $values = array(
                    "n0" => 0,
                );

            if ($property_fields["MULTIPLE"] == "N") {
                $res = "<label for=\"\"><input type=\"file\" size=\"" . $max_file_size_show . "\" value=\"" . $property_fields["VALUE"] . "\" name=\"" . $name . "[0]\" id=\"" . $name . "[0]\" class=\"has_tooltip\"></label>";
            } else {
                $res = '
                <script type="text/javascript">
                    function addControl(item)
                    {
                        var current_name = item.id.mb_split("[")[0],
                            current_id = item.id.mb_split("[")[1].replace("[", "").replace("]", ""),
                            next_id = parseInt(current_id) + 1;

                        var newInput = document.createElement("input");
                        newInput.type = "file";
                        newInput.name = current_name + "[" + next_id + "]";
                        newInput.id = current_name + "[" + next_id + "]";
                        newInput.onchange = function() { addControl(this); };
                        var br = document.createElement("br");
                        var br2 = document.createElement("br");

                        BX(item.id).parentNode.appendChild(br);
                        BX(item.id).parentNode.appendChild(br2);
                        BX(item.id).parentNode.appendChild(newInput);
                    }
                </script>
                ';

                $res .= "<label for=\"\"><input type=\"file\" size=\"" . $max_file_size_show . "\" value=\"" . $property_fields["VALUE"] . "\" name=\"" . $name . "[0]\" id=\"" . $name . "[0]\" class=\"has_tooltip\" /></label>";
                $res .= "<br/><br/>";
                $res .= "<label for=\"\"><input type=\"file\" size=\"" . $max_file_size_show . "\" value=\"" . $property_fields["VALUE"] . "\" name=\"" . $name . "[1]\" id=\"" . $name . "[1]\" onChange=\"javascript:addControl(this);\"></label>";
            }

            return $res;
        }
    }

}

if (!function_exists('properties_usort')) {
    function properties_usort($a, $b)
    {
        if ($a['SORT'] == $b['SORT']) {
            return 0;
        }
        return ($a['SORT'] < $b['SORT']) ? -1 : 1;
    }
}

if (!function_exists("showFilePropertyField")) {
    function showFilePropertyField($name, $property_fields, $values, $max_file_size_show = 50000)
    {
        $res = "";

        if (!is_array($values) || empty($values))
            $values = array(
                "n0" => 0,
            );

        if ($property_fields["MULTIPLE"] == "N") {
            $res = "<label for=\"\"><input type=\"file\" size=\"" . $max_file_size_show . "\" value=\"" . $property_fields["VALUE"] . "\" name=\"" . $name . "[0]\" id=\"" . $name . "[0]\" class=\"has_tooltip\"></label>";
        } else {
            $res = '
                <script type="text/javascript">
                    function addControl(item)
                    {
                        var current_name = item.id.mb_split("[")[0],
                            current_id = item.id.mb_split("[")[1].replace("[", "").replace("]", ""),
                            next_id = parseInt(current_id) + 1;

                        var newInput = document.createElement("input");
                        newInput.type = "file";
                        newInput.name = current_name + "[" + next_id + "]";
                        newInput.id = current_name + "[" + next_id + "]";
                        newInput.onchange = function() { addControl(this); };
                        var br = document.createElement("br");
                        var br2 = document.createElement("br");

                        BX(item.id).parentNode.appendChild(br);
                        BX(item.id).parentNode.appendChild(br2);
                        BX(item.id).parentNode.appendChild(newInput);
                    }
                </script>
                ';

            $res .= "<label for=\"\"><input type=\"file\" size=\"" . $max_file_size_show . "\" value=\"" . $property_fields["VALUE"] . "\" name=\"" . $name . "[0]\" id=\"" . $name . "[0]\" class=\"has_tooltip\" /></label>";
            $res .= "<br/><br/>";
            $res .= "<label for=\"\"><input type=\"file\" size=\"" . $max_file_size_show . "\" value=\"" . $property_fields["VALUE"] . "\" name=\"" . $name . "[1]\" id=\"" . $name . "[1]\" onChange=\"javascript:addControl(this);\"></label>";
        }

        return $res;
    }
}

if (!function_exists("PrintPropsForm")) {
    function PrintPropsForm($arSource = array(), $locationTemplate = ".default", &$counter, $arResult, $relatedProperties = array(), $delivery_name = '', $paysystem_name = '')
    {
		
		global $USER;
		
		static $sCity, $iBitrixId, $sCountry;
		
		if (sizeof($relatedProperties)) {

            $db_props = CSaleOrderProps::GetList(
                array("SORT" => "ASC"),
                array(
                    "@CODE" => array('delivery', 'paysystem', 'comments'),
                ),
                false,
                false
            );


            while ($props = $db_props->Fetch()) {

                $arSource[] = $props;
            }

            usort($arSource, 'properties_usort');

        };

		if (empty($sCountry)) {
			$iBitrixId = 0;
			$sCity = '';
			$sCountry = 'Россия';
			
			foreach ($arSource as $arProperties) {

                if ((
                        $arProperties["CODE"] == 'paysystem'
                        || $arProperties["CODE"] == 'delivery'
                        || $arProperties["CODE"] == 'comments'
                    )
                    && sizeof($relatedProperties)

                ):

                else:
				
					if ($arProperties["TYPE"] == "LOCATION") {

						if (is_array($arProperties["VARIANTS"]) && count($arProperties["VARIANTS"]) > 0) {

							foreach ($arProperties["VARIANTS"] as $arVariant) {

								if ($arVariant["SELECTED"] == "Y") {

									$iBitrixId = trim($arVariant["CITY_ID"]);
                                    $sCity = trim($arVariant["CITY_NAME"]);
                                    $sCountry = trim($arVariant["COUNTRY_NAME"]);
                                    break;

                                }
                            }
                        }

                    }
				
				endif;
			}
		
		}

        if (!empty($arSource)) {
            
			
			
            foreach ($arSource as $arProperties) {

                $is_filled = false;
                $error_find = false;

                foreach ($arResult["ERROR"] as $v) {
                    if (mb_stripos($v, '' . trim($arProperties["NAME"]) . '') !== false) {

                        $error_find = $v;
                        $error_find = str_ireplace('"', '', $error_find);
                        $error_find = str_ireplace('\'', '', $error_find);
                        $error_find = htmlspecialchars($error_find, ENT_HTML5, LANG_CHARSET);

                        break;
                    };
                };

                if ((
                        $arProperties["CODE"] == 'paysystem'
                        || $arProperties["CODE"] == 'delivery'
                        || $arProperties["CODE"] == 'comments'
                    )
                    && sizeof($relatedProperties)

                ):

                    echo $relatedProperties[$arProperties["CODE"]];

                else:
				
                    if (!$error_find) {

                        if ($arProperties["TYPE"] == "CHECKBOX") {

                            if ($arProperties["CHECKED"] == "Y") {
                                $is_filled = true;
                            }

                        } elseif ($arProperties["TYPE"] == "TEXT") {

                            if (!empty($arProperties["VALUE"])) {
                                $is_filled = true;
                            }

                        } elseif ($arProperties["TYPE"] == "SELECT") {

                            if (is_array($arProperties["VARIANTS"])) {

                                foreach ($arProperties["VARIANTS"] as $arVariants):

                                    if ($arVariants["SELECTED"] == "Y") {
                                        $is_filled = true;
                                        break;

                                    };

                                endforeach;

                            }

                        } elseif ($arProperties["TYPE"] == "MULTISELECT") {
                            if (is_array($arProperties["VARIANTS"])) {
                                foreach ($arProperties["VARIANTS"] as $arVariants):
                                    if ($arVariants["SELECTED"] == "Y") {

                                        $is_filled = true;
                                        break;

                                    };
                                endforeach;
                            };

                        } elseif ($arProperties["TYPE"] == "TEXTAREA") {
                            if (!empty($arProperties["VALUE"])) {
                                $is_filled = true;
                            }

                        } elseif ($arProperties["TYPE"] == "LOCATION") {

                            if (is_array($arProperties["VARIANTS"]) && count($arProperties["VARIANTS"]) > 0) {

                                foreach ($arProperties["VARIANTS"] as $arVariant) {

                                    if ($arVariant["SELECTED"] == "Y") {

						               $is_filled = true;
                                        break;

                                    }
                                }
                            }

                        } elseif ($arProperties["TYPE"] == "RADIO") {

                            if (is_array($arProperties["VARIANTS"])) {
                                foreach ($arProperties["VARIANTS"] as $arVariants):

                                    if ($arVariants["CHECKED"] == "Y") {
                                        $is_filled = true;
                                        break;
                                    }

                                endforeach;
                            }

                        } elseif ($arProperties["TYPE"] == "FILE") {
                            if (!empty($arProperties["VALUE"])) {
                                $is_filled = true;
                            }

                        }

                    }

                    ?>
                    <div class="row order-properties <?php if(in_array(trim($arProperties["CODE"]),['YD_DAYS','YD_TIME'])): ?> hidden hidden-address<?php endif; ?> <?php echo $delivery_name == '' ? 'not-selected-delivery' : $delivery_name; ?> <?php echo $paysystem_name; ?> <?= $arProperties["FIELD_NAME"] ?> <?= $arProperties["CODE"] ?> <? if ($arProperties["REQUIED_FORMATED"] == "Y"):?>required<?php endif; ?> <?php if ($is_filled && !$error_find): ?>has-success<?php endif; ?> <?php if ($error_find): ?>has_order_error<?php endif; ?>">
                        <div class="col-xs-12 col-sm-12 col-lg-10 col-md-12 <?= $arProperties["FIELD_NAME"] ?> <?= $arProperties["CODE"] ?> <? if ($arProperties["REQUIED_FORMATED"] == "Y"):?>required<?php endif; ?> <?php if ($is_filled && !$error_find): ?>has-success<?php endif; ?> <?php if ($error_find): ?>has_order_error<?php endif; ?>" <?php if ($error_find): ?> data:error="<?php echo $error_find; ?>"<?php endif; ?>>
                            <?php
                            if ($arProperties["TYPE"] == "LOCATION") {
                                ?>
                                <input type="hidden" id="countryName" value="<?php echo trim($sCountry); ?>"/>
                                <?php
                            }

                            if (CSaleLocation::isLocationProMigrated()) {
                                $propertyAttributes = array(
                                    'type' => $arProperties["TYPE"],
                                    'valueSource' => $arProperties['SOURCE'] == 'DEFAULT' ? 'default' : 'form'
                                );

                                if (intval($arProperties['IS_ALTERNATE_LOCATION_FOR']))
                                    $propertyAttributes['isAltLocationFor'] = intval($arProperties['IS_ALTERNATE_LOCATION_FOR']);

                                if (intval($arProperties['INPUT_FIELD_LOCATION']))
                                    $propertyAttributes['altLocationPropId'] = intval($arProperties['INPUT_FIELD_LOCATION']);

                                if ($arProperties['IS_ZIP'] == 'Y')
                                    $propertyAttributes['isZip'] = true;
                            }
                            ?>
                            <div class="cart-fields" data-property-id-row="<?= intval($arProperties["ID"]) ?>">
                                <?
                                if ($arProperties["TYPE"] == "CHECKBOX") {
                                    ?>
                                    <label for="<?= $arProperties["FIELD_NAME"] ?>" class="col-lg-3 col-xs-4 col-sm-12">
                                        <?= $arProperties["NAME"] ?>
                                        <? if ($arProperties["REQUIED_FORMATED"] == "Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif; ?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <input type="hidden" name="<?= $arProperties["FIELD_NAME"] ?>" value="">
                                        <input type="checkbox" name="<?= $arProperties["FIELD_NAME"] ?>"
                                               id="<?= $arProperties["FIELD_NAME"] ?>"
                                               value="Y"<? if ($arProperties["CHECKED"] == "Y") {
                                            echo " checked";
                                            $is_filled = true;
                                        }; ?> class="has_tooltip"/>
                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?= $arProperties["DESCRIPTION"] ?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>


                                    <?
                                } elseif ($arProperties["TYPE"] == "TEXT") {

                                    if (!empty($arProperties["VALUE"])) {
                                        $is_filled = true;
                                    }

                                    ?>
                                    <label for="<?= $arProperties["FIELD_NAME"] ?>" class="col-lg-3 col-xs-4 col-sm-12">
                                        <?= $arProperties["NAME"] ?>
                                        <? if ($arProperties["REQUIED_FORMATED"] == "Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif; ?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <?

										$classes = [];

										if ($arProperties["CODE"] == 'STATION') {

                                            $sDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/undeground/';

                                            $ar = [];

                                            $files = [];
                                            $files = glob($sDir."*.csv");
                                            $stations = [];

                                            if (!empty($files)) {

                                                $times = array_map('filemtime', ($files));

                                                array_multisort($times, SORT_DESC, SORT_REGULAR, $files);

                                                $filename = current($files);
                                                $csvContent = file_get_contents($filename);
                                                $csvContent = iconv('windows-1251','utf-8//IGNORE',$csvContent);
                                                $aCsvContent = explode("\n",$csvContent);
												
												 $params = Array(
													"max_len" => "200",
													"change_case" => "L",
													"replace_space" => "_",
													"replace_other" => "_",
													"delete_repeat_replace" => "true"
												);

												if ($aCsvContent) {

                                                    $bFirst = true;

                                                    foreach($aCsvContent as $sStation) {

                                                        $aStation = str_getcsv(trim($sStation),';');
                                                        $aStation = array_map('trim',$aStation);

                                                        if ($bFirst) {
                                                            $bFirst = false;
                                                            continue;
                                                        }

                                                        if(isset($aStation[5]) && !empty($aStation[5])) {
															
															$c_class = CUtil::translit($aStation[1], LANGUAGE_ID, $params);
															$classes[$c_class] = $c_class;
                                                            $stations[] = array('м. '.$aStation[0].', '.$aStation[1] . '',$c_class);
															
                                                        }
                                                    }

                                                }

                                            }
											
                                            if (!empty($stations)) {
                                            ?>
                                                <select class="selectpicker form-control has_tooltip"
                                                        name="<?= $arProperties["FIELD_NAME"] ?>"
                                                        id="<?= $arProperties["FIELD_NAME"] ?>" data-live-search="true"
                                                        data-container="body">
                                                    <option value=""
                                                            datavalue=""
                                                            datacode=""><?= GetMessage('SOA_STATIONDELIVERY'); ?></option>
                                                    <?php foreach ($stations as $sCurrent) { 
														$sValue = current($sCurrent);
														$sClass = next($sCurrent);
													
													?>
                                                    <option<?php if (isset($arProperties["VALUE"]) && !empty($arProperties["VALUE"]) && $arProperties["VALUE"] == $sValue) { ?> selected="selected"<? }; ?>
                                                                value="<?= htmlspecialchars($sValue, ENT_QUOTES, LANG_CHARSET); ?>"
                                                                datavalue="<?= htmlspecialchars($sSdekHelp, ENT_QUOTES, LANG_CHARSET); ?>"
                                                                datacode="<?= htmlspecialchars($sSdekCode, ENT_QUOTES, LANG_CHARSET); ?>"
																class="<?= $sClass; ?>"
                                                        ><?= $sValue; ?></option>

                                                    <? } ?>

                                                </select>
                                                    <?
                                            }

                                        
                                        } elseif ($arProperties["CODE"] == 'SDEK_PVZ'
                                            && $iBitrixId) {

                                            $aSdekPvzFilter = [
                                                'PROPERTY_BITRIX_ID' => $iBitrixId,
                                                'ACTIVE' => 'Y',
                                                'IBLOCK_ID' => 44
                                            ];

                                            $aSdekPvzSelect = [
                                                'ID',
                                                'PROPERTY_CODE',
                                                'PROPERTY_ADDRESS',
                                                'PROPERTY_WORKTIME',
                                                'PROPERTY_ADDRESSCOMMENT',
                                                'PROPERTY_PHONE',
                                                'PROPERTY_BITRIX_ID'
                                            ];

                                            //Address WorkTime AddressComment Phone Email
                                            //ADDRESS WORKTIME ADDRESSCOMMENT PHONE EMAIL

                                            $aSdekPvzs = [];
                                            $rdSdekPvz = impelCIBlockElement::GetList(array(), $aSdekPvzFilter, false, false, $aSdekPvzSelect);

                                            if ($rdSdekPvz) {
                                                $iCount = 0;

                                                while ($aSdekPvz = $rdSdekPvz->GetNext()) {

                                                    if (empty($aSdekPvz['PROPERTY_ADDRESS_VALUE'])) {
                                                        continue;
                                                    }

                                                    ++$iCount;

                                                    $iHelp = $aSdekPvz['ID'];

                                                    $aSdekPvzs[$aSdekPvz['PROPERTY_CODE_VALUE']] = [
                                                        $aSdekPvz['PROPERTY_ADDRESS_VALUE'],
                                                        $iHelp,
                                                        $aSdekPvz['PROPERTY_CODE_VALUE'],
                                                    ];
                                                    //$oPvzElt->Delete($aSdekPvz["ID"]);
                                                }
                                            }

                                            //echo $iCount;

                                            if (!empty($aSdekPvzs)) {
                                                $sсSdekHelp = '';
                                                ?>
                                                <select class="selectpicker form-control has_tooltip"
                                                        name="<?= $arProperties["FIELD_NAME"] ?>"
                                                        id="<?= $arProperties["FIELD_NAME"] ?>" data-live-search="true"
                                                        data-container="body">
                                                    <option value=""
                                                            datavalue=""
                                                            datacode=""><?= GetMessage('SOA_SDEKDELIVERY'); ?></option>
                                                    <?php foreach ($aSdekPvzs as $aValue):
                                                        $sValue = current($aValue);
                                                        $sSdekHelp = next($aValue);
                                                        $sSdekCode = next($aValue);
                                                        if (isset($arProperties["VALUE"]) && !empty($arProperties["VALUE"]) && $arProperties["VALUE"] == $sValue) {
                                                            $sсSdekHelp = $sSdekHelp;
                                                        }
                                                        ?>
                                                        <option<?php if (isset($arProperties["VALUE"]) && !empty($arProperties["VALUE"]) && $arProperties["VALUE"] == $sValue) { ?> selected="selected"<? }; ?>
                                                            value="<?= htmlspecialchars($sValue, ENT_QUOTES, LANG_CHARSET); ?>"
                                                            datavalue="<?= htmlspecialchars($sSdekHelp, ENT_QUOTES, LANG_CHARSET); ?>"
                                                            datacode="<?= htmlspecialchars($sSdekCode, ENT_QUOTES, LANG_CHARSET); ?>"
                                                        ><?= $sValue; ?></option>
                                                    <?php endforeach; ?>
                                                </select><div id="sdekHelp"
                                                     class="<?= $sсSdekHelp == '' ? 'hidden ' : ''; ?>alert alert-info">
                                                </div>
                                                <input type="hidden" id="SDEK_HELP" name="SDEK_HELP"
                                                       value="<?= isset($_REQUEST['SDEK_HELP']) ? trim(htmlspecialchars($_REQUEST['SDEK_HELP'], ENT_QUOTES, LANG_CHARSET)) : ''; ?>"/>
                                                <?php

                                            }

										
                                        } elseif ($arProperties["CODE"] == 'BOXBERRY_PVZ'
                                            && !empty($sCountry)
                                            && !empty($sCity)
                                        ) {

                                            $svCountry = str_ireplace('ё', 'е', $sCountry);
                                            $svCity = str_ireplace('ё', 'е', $sCity);

                                            $aBoxberryPvzFilter = [
                                                'ACTIVE' => 'Y',
                                                'IBLOCK_ID' => 43,
                                                'PROPERTY_CITYNAME' => $svCity,
                                                'PROPERTY_COUNTRY' => $svCountry,
                                            ];

                                            $aBoxberryPvzSelect = [
                                                'ID',
                                                'PROPERTY_CODE',
                                                'PROPERTY_ADDRESSREDUCE',
                                                'PROPERTY_WORKSHEDULE',
                                                'PROPERTY_TRIPDESCRIPTION',
                                                'PROPERTY_PHONE',
                                                'PROPERTY_CITYNAME',
                                                'PROPERTY_COUNTRY',
                                            ];

                                            //Address WorkTime AddressComment Phone Email
                                            //ADDRESS WORKTIME ADDRESSCOMMENT PHONE EMAIL

                                            $aBoxberryPvzs = [];
                                            $rdBoxberryPvz = impelCIBlockElement::GetList(array(), $aBoxberryPvzFilter, false, false, $aBoxberryPvzSelect);
                                            $iCount = 0;
                                            if ($rdBoxberryPvz) {
                                                while ($aBoxberryPvz = $rdBoxberryPvz->GetNext()) {
                                                    if (empty($aBoxberryPvz['PROPERTY_ADDRESSREDUCE_VALUE'])) {
                                                        continue;
                                                    }

                                                    ++$iCount;

                                                    $iHelp = $aBoxberryPvz['ID'];

                                                    $aBoxberryPvzs[$aBoxberryPvz['PROPERTY_CODE_VALUE']] = [
                                                        $aBoxberryPvz['PROPERTY_ADDRESSREDUCE_VALUE'],
                                                        $iHelp,
                                                        $aBoxberryPvz['PROPERTY_CODE_VALUE']
                                                    ];
                                                    //$oPvzElt->Delete($aBoxberryPvz["ID"]);
                                                }
                                            }

                                            //echo $iCount;

                                            if (!empty($aBoxberryPvzs)) {
                                                $sсBoxberryHelp = '';
                                                ?>
                                                <select class="selectpicker form-control has_tooltip"
                                                        name="<?= $arProperties["FIELD_NAME"] ?>"
                                                        id="<?= $arProperties["FIELD_NAME"] ?>" data-live-search="true"
                                                        data-container="body">
                                                    <option value=""
                                                            datavalue=""
                                                            datacode=""><?= GetMessage('SOA_BOXBERRYDELIVERY'); ?></option>
                                                    <?php foreach ($aBoxberryPvzs as $aValue):

                                                        $sValue = current($aValue);
                                                        $sBoxberryHelp = next($aValue);
                                                        $sBoxberryCode = next($aValue);

                                                        if (isset($arProperties["VALUE"]) && !empty($arProperties["VALUE"]) && $arProperties["VALUE"] == $sValue) {
                                                            $sсBoxberryHelp = $sBoxberryHelp;
                                                        }

                                                        ?>
                                                        <option<?php if (isset($arProperties["VALUE"]) && !empty($arProperties["VALUE"]) && $arProperties["VALUE"] == $sValue): ?> selected="selected"<? endif; ?>
                                                            value="<?= htmlspecialchars($sValue, ENT_QUOTES, LANG_CHARSET); ?>"
                                                            datavalue="<?= htmlspecialchars($sBoxberryHelp, ENT_QUOTES, LANG_CHARSET); ?>"
                                                            datacode="<?= htmlspecialchars($sBoxberryCode, ENT_QUOTES, LANG_CHARSET); ?>"
                                                        ><?= $sValue; ?></option>
                                                    <?php endforeach; ?>
                                                </select><div id="boxberryHelp"
                                                     class="<?= $sсBoxberryHelp == '' ? 'hidden ' : ''; ?>alert alert-info">
                                                </div>
                                                <input type="hidden" id="BOXBERRY_HELP" name="BOXBERRY_HELP"
                                                       value="<?= isset($_REQUEST['BOXBERRY_HELP']) ? trim(htmlspecialchars($_REQUEST['BOXBERRY_HELP'], ENT_QUOTES, LANG_CHARSET)) : ''; ?>"/>
                                                <?php

                                            }


                                        } elseif ($arProperties["CODE"] == 'YD_PVZ'
                                            && !empty($sCity)
                                        ) {

                                            $svCity = str_ireplace('ё', 'е', $sCity);

                                            $aYandexPvzFilter = [
                                                'ACTIVE' => 'Y',
                                                'IBLOCK_ID' => 46,
                                                'PROPERTY_CITYNAME' => $svCity,
                                            ];

                                            $aYandexPvzSelect = [
                                                'ID',
                                                'PROPERTY_CODE',
                                                'PROPERTY_ADDRESSREDUCE',
                                                'PROPERTY_WORKSHEDULE',
                                                'PROPERTY_TRIPDESCRIPTION',
                                                'PROPERTY_PHONE',
                                                'PROPERTY_CITYNAME',
                                                'PROPERTY_COUNTRY',
												'PROPERTY_POINTID',
												'PROPERTY_FULL_ADDRESS',
												'PROPERTY_LONGITUDE',
												'PROPERTY_LATITUDE'
                                            ];

                                            //Address WorkTime AddressComment Phone Email
                                            //ADDRESS WORKTIME ADDRESSCOMMENT PHONE EMAIL

                                            $aYandexPvzs = [];
                                            $rdYandexPvz = impelCIBlockElement::GetList(['PROPERTY_ADDRESSREDUCE' => 'ASC'], $aYandexPvzFilter, false, false, $aYandexPvzSelect);
                                            $iCount = 0;
                                            if ($rdYandexPvz) {
                                                while ($aYandexPvz = $rdYandexPvz->GetNext()) {
                                                    if (empty($aYandexPvz['PROPERTY_ADDRESSREDUCE_VALUE'])) {
                                                        continue;
                                                    }

                                                    ++$iCount;

                                                    $iHelp = $aYandexPvz['ID'];

                                                    $aYandexPvzs[$aYandexPvz['PROPERTY_CODE_VALUE']] = [
                                                        $aYandexPvz['PROPERTY_ADDRESSREDUCE_VALUE'],
                                                        $iHelp,
                                                        $aYandexPvz['PROPERTY_CODE_VALUE'],
														
														$aYandexPvz['PROPERTY_POINTID_VALUE'],
													    $aYandexPvz['PROPERTY_FULL_ADDRESS_VALUE'],
													    $aYandexPvz['PROPERTY_LONGITUDE_VALUE'],
													    $aYandexPvz['PROPERTY_LATITUDE_VALUE'],
														
                                                    ];
                                                    //$oPvzElt->Delete($aYandexPvz["ID"]);
                                                }
                                            }

                                            //echo $iCount;

                                            if (!empty($aYandexPvzs)) {
                                                $sсYandexHelp = '';
                                                ?>
                                                <select  class="selectpicker form-control has_tooltip"
                                                        name="<?= $arProperties["FIELD_NAME"] ?>"
                                                        id="<?= $arProperties["FIELD_NAME"] ?>" data-live-search="true"
                                                        data-container="body">
                                                    <option value=""
                                                            data-point=""
															data-long=""
															data-lat=""
															datavalue=""
                                                            datacode=""><?= GetMessage('SOA_YANDEXDELIVERY'); ?></option>
                                                    <?php foreach ($aYandexPvzs as $aValue):

                                                        $sValue = current($aValue);
                                                        $sYandexHelp = next($aValue);
                                                        $sYandexCode = next($aValue);
														
														$sPoint = next($aValue);
														$sFullAddress = next($aValue);
														$sLong = next($aValue);
														$sLat = next($aValue);
														
                                                        if (isset($arProperties["VALUE"]) && !empty($arProperties["VALUE"]) && $arProperties["VALUE"] == $sValue) {
                                                            $sсYandexHelp = $sYandexHelp;
                                                        }

                                                        ?>
                                                        <option<?php if (isset($arProperties["VALUE"]) && !empty($arProperties["VALUE"]) && $arProperties["VALUE"] == $sValue): ?> selected="selected"<? endif; ?>
                                                            value="<?= htmlspecialchars($sValue, ENT_QUOTES, LANG_CHARSET); ?>"
                                                            data-point="<?= htmlspecialchars($sPoint, ENT_QUOTES, LANG_CHARSET); ?>"
															data-long="<?= htmlspecialchars($sLong, ENT_QUOTES, LANG_CHARSET); ?>"
															data-lat="<?= htmlspecialchars($sLat, ENT_QUOTES, LANG_CHARSET); ?>"
															datavalue="<?= htmlspecialchars($sYandexHelp, ENT_QUOTES, LANG_CHARSET); ?>"
                                                            datacode="<?= htmlspecialchars($sYandexCode, ENT_QUOTES, LANG_CHARSET); ?>"
                                                        ><?= $sValue; ?></option>
                                                    <?php endforeach; ?>
                                                </select><div id="YANDEXHELP"
                                                     class="<?= $sсYandexHelp == '' ? 'hidden ' : ''; ?>alert alert-info">
													 <?= isset($_REQUEST['YANDEX_HELP']) ? trim($_REQUEST['YANDEX_HELP']) : ''; ?>
                                                </div>
                                                <input type="hidden" id="YANDEX_HELP" name="YANDEX_HELP"
                                                       value="<?= isset($_REQUEST['YANDEX_HELP']) ? trim(htmlspecialchars($_REQUEST['YANDEX_HELP'], ENT_QUOTES, LANG_CHARSET)) : ''; ?>"/>
                                                <?php

                                            }


                                        } elseif ($arProperties["CODE"] == 'DAYOFDELIVERY') {

                                            $days = impelDeliveryIntervalExceed::getIntervalsExceed();
                                            $daysInterval = impelDeliveryIntervalExceed::getcTimeIntervals();

                                            ?>
                                            <select class="selectpicker form-control has_tooltip123"
                                                    name="<?= $arProperties["FIELD_NAME"] ?>"
                                                    id="<?= $arProperties["FIELD_NAME"] ?>">
                                                <option value=""><?= GetMessage('SOA_DAYOFDELIVERY'); ?></option>
                                                <?php foreach ($days as $dayvalue): ?>
                                                    <option<?php if (isset($arProperties["VALUE"]) && $arProperties["VALUE"] == $dayvalue): ?> selected="selected"<? endif; ?>
                                                        value="<?= $dayvalue; ?>" class="<?= isset($daysInterval[$dayvalue]) ? $daysInterval[$dayvalue] : ''; ?>"><?= $dayvalue; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?

                                        } else if ($arProperties["CODE"] == 'TIMEOFDELIVERY') {

                                            $timeInterval = impelDeliveryInterval::getTimeInterval();
                                            ?>
                                            <select class="selectpicker form-control has_tooltip"
                                                    name="<?= $arProperties["FIELD_NAME"] ?>"
                                                    id="<?= $arProperties["FIELD_NAME"] ?>">
                                                <option value=""><?= GetMessage('SOA_TIMEOFDELIVERY'); ?></option>
                                                <?php foreach ($timeInterval as $timetext => $timevalue): ?>
                                                    <option<?php if (isset($arProperties["VALUE"]) && $arProperties["VALUE"] == $timevalue): ?> selected="selected"<? endif; ?>
                                                        value="<?= $timevalue; ?>"><?= $timetext; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?


                                        } else {
											
											?>

                                                <input type="text" maxlength="250" value="<?=$arProperties["VALUE"]; ?>"
                                                       name="<?= $arProperties["FIELD_NAME"] ?>"
                                                       id="<?= $arProperties["FIELD_NAME"] ?>"
                                                       class="form-control has_tooltip"/>

                                                <?

                                        
                                        }

                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?= $arProperties["DESCRIPTION"] ?>
                                            </div>
                                        <?
                                        endif;


                                        ?>
                                    </div>

                                    <?
                                } elseif ($arProperties["TYPE"] == "SELECT") {

                                    $sHour = trim(strftime('%k'));
                                    ++$sHour;
                                    $sMinute = trim(ltrim(strftime('%M'), 0));

                                    switch ($arProperties["CODE"]) {

                                        case 'DELIVERY_REQUIRED_START_TIME':

                                            if ($sMinute > 1 && $sMinute < 31) {
                                                $sMinute = 30;
                                            } else if ($sMinute > 30 && $sMinute < 59) {
                                                $sMinute = '00';
                                                ++$sHour;
                                            } else {
                                                $sMinute = '00';
                                            }

                                            $sDefault = $sHour . ':' . $sMinute;

                                            foreach ($arProperties["VARIANTS"] as $sKey => $arVariants) {

                                                if ($arVariants["VALUE"] != $sDefault) {
                                                    unset($arProperties["VARIANTS"][$sKey]);
                                                }
                                            }

                                            break;

                                        case 'DELIVERY_REQUIRED_TIME':

                                            ++$sHour;

                                            if ($sMinute > 1 && $sMinute < 31) {
                                                $sMinute = 30;
                                            } else if ($sMinute > 30 && $sMinute < 59) {
                                                $sMinute = '00';
                                                ++$sHour;
                                            } else {
                                                $sMinute = '00';
                                            }

                                            $sDefault = $sHour . ':' . $sMinute;

                                            foreach ($arProperties["VARIANTS"] as $sKey => $arVariants) {
                                                if ($arVariants["VALUE"] != $sDefault) {
                                                    unset($arProperties["VARIANTS"][$sKey]);
                                                }
                                            }

                                            break;
                                    }


                                    ?>
                                    <label for="<?= $arProperties["FIELD_NAME"] ?>" class="col-lg-3 col-xs-4 col-sm-12">
                                        <?= $arProperties["NAME"] ?>
                                        <? if ($arProperties["REQUIED_FORMATED"] == "Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif; ?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <select name="<?= $arProperties["FIELD_NAME"] ?>"
                                                id="<?= $arProperties["FIELD_NAME"] ?>"
                                                size="<?= $arProperties["SIZE1"] ?>"
                                                class="selectpicker form-control has_tooltip">
                                            <?
                                            foreach ($arProperties["VARIANTS"] as $arVariants):
                                                ?>
                                                <option value="<?= $arVariants["VALUE"] ?>"<? if ($arVariants["SELECTED"] == "Y") {
                                                    echo " selected";
                                                    $is_filled = true;
                                                }; ?>><?= $arVariants["NAME"] ?></option>
                                            <?
                                            endforeach;
                                            ?>
                                        </select>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?= $arProperties["DESCRIPTION"] ?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                } elseif ($arProperties["TYPE"] == "MULTISELECT") {
                                    ?>
                                    <label for="<?= $arProperties["FIELD_NAME"] ?>" class="col-lg-3 col-xs-4 col-sm-12">
                                        <?= $arProperties["NAME"] ?>
                                        <? if ($arProperties["REQUIED_FORMATED"] == "Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif; ?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <select multiple="multiple" name="<?= $arProperties["FIELD_NAME"] ?>"
                                                id="<?= $arProperties["FIELD_NAME"] ?>"
                                                size="<?= $arProperties["SIZE1"] ?>"
                                                class="selectpicker form-control has_tooltip">
                                            <?
                                            foreach ($arProperties["VARIANTS"] as $arVariants):
                                                ?>
                                                <option value="<?= $arVariants["VALUE"] ?>"<? if ($arVariants["SELECTED"] == "Y") {
                                                    echo " selected";
                                                    $is_filled = true;
                                                }; ?>><?= $arVariants["NAME"] ?></option>
                                            <?
                                            endforeach;
                                            ?>
                                        </select>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?= $arProperties["DESCRIPTION"] ?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                } elseif ($arProperties["TYPE"] == "TEXTAREA") {
                                    $rows = ($arProperties["SIZE2"] > 10) ? 4 : $arProperties["SIZE2"];

                                    if (!empty($arProperties["VALUE"])) {
                                        $is_filled = true;
                                    }

                                    ?>
                                    <label for="<?= $arProperties["FIELD_NAME"] ?>" class="col-lg-3 col-xs-4 col-sm-12">
                                        <?= $arProperties["NAME"] ?>
                                        <? if ($arProperties["REQUIED_FORMATED"] == "Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif; ?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <textarea class="form-control has_tooltip"
                                                  name="<?= $arProperties["FIELD_NAME"] ?>"
                                                  id="<?= $arProperties["FIELD_NAME"] ?>"><?= $arProperties["VALUE"] ?></textarea>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description clearfix clear">
                                                <?= $arProperties["DESCRIPTION"] ?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                } elseif ($arProperties["TYPE"] == "LOCATION") {
                                    ?>
                                    <label class="col-lg-3 col-xs-4 col-sm-12">
                                        <?= $arProperties["NAME"] ?>
                                        <? if ($arProperties["REQUIED_FORMATED"] == "Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif; ?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">

                                        <?
                                        $value = 0;
                                        if (is_array($arProperties["VARIANTS"]) && count($arProperties["VARIANTS"]) > 0) {
                                            foreach ($arProperties["VARIANTS"] as $arVariant) {
                                                if ($arVariant["SELECTED"] == "Y") {

                                                    $is_filled = true;
                                                    $value = $arVariant["ID"];
                                                    break;
                                                }
                                            }
                                        }

                                        ?>

                                        <? CSaleLocation::proxySaleAjaxLocationsComponent(array(
                                            "AJAX_CALL" => "N",
                                            "COUNTRY_INPUT_NAME" => "COUNTRY",
                                            "REGION_INPUT_NAME" => "REGION",
                                            "CITY_INPUT_NAME" => $arProperties["FIELD_NAME"],
                                            "CITY_OUT_LOCATION" => "Y",
                                            "LOCATION_VALUE" => $value,
                                            "ORDER_PROPS_ID" => $arProperties["ID"],
                                            "ONCITYCHANGE" => ($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitForm()" : "",
                                            "SIZE1" => $arProperties["SIZE1"],
                                        ),
                                            array(
                                                "ID" => $arProperties["VALUE"],
                                                "CODE" => "",
                                                "SHOW_DEFAULT_LOCATIONS" => "Y",

                                                // function called on each location change caused by user or by program
                                                // it may be replaced with global component dispatch mechanism coming soon
                                                "JS_CALLBACK" => "submitFormProxy", //($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitFormProxy" : "",

                                                // function window.BX.locationsDeferred['X'] will be created and lately called on each form re-draw.
                                                // it may be removed when sale.order.ajax will use real ajax form posting with BX.ProcessHTML() and other stuff instead of just simple iframe transfer
                                                "JS_CONTROL_DEFERRED_INIT" => intval($arProperties["ID"]),

                                                // an instance of this control will be placed to window.BX.locationSelectors['X'] and lately will be available from everywhere
                                                // it may be replaced with global component dispatch mechanism coming soon
                                                "JS_CONTROL_GLOBAL_ID" => intval($arProperties["ID"]),

                                                "DISABLE_KEYBOARD_INPUT" => 'Y'
                                            ),
                                            $_REQUEST['PERMANENT_MODE_STEPS'] == 1 ? 'steps' : $locationTemplate,
                                            true,
                                            'location-block-wrapper has_tooltip'
                                        ) ?>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?= $arProperties["DESCRIPTION"] ?>
                                            </div>
                                        <?
                                        endif;
                                        ?>

                                    </div>

                                    <?
                                } elseif ($arProperties["TYPE"] == "RADIO") {
                                    ?>
                                    <label class="col-lg-3 col-xs-4 col-sm-12">
                                        <?= $arProperties["NAME"] ?>
                                        <? if ($arProperties["REQUIED_FORMATED"] == "Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif; ?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <?
                                        if (is_array($arProperties["VARIANTS"])) {
                                            foreach ($arProperties["VARIANTS"] as $arVariants):
                                                ?>
                                                <input
                                                        type="radio"
                                                        name="<?= $arProperties["FIELD_NAME"] ?>"
                                                        id="<?= $arProperties["FIELD_NAME"] ?>_<?= $arVariants["VALUE"] ?>"
                                                        value="<?= $arVariants["VALUE"] ?>" <? if ($arVariants["CHECKED"] == "Y") {
                                                    echo " checked";
                                                    $is_filled = true;
                                                }; ?>
                                                        class="has_tooltip"/>

                                                <label for="<?= $arProperties["FIELD_NAME"] ?>_<?= $arVariants["VALUE"] ?>"><?= $arVariants["NAME"] ?></label></br>
                                            <?
                                            endforeach;
                                        }
                                        ?>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?= $arProperties["DESCRIPTION"] ?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                } elseif ($arProperties["TYPE"] == "FILE") {
                                    if (!empty($arProperties["VALUE"])) {
                                        $is_filled = true;
                                    }

                                    ?>
                                    <label for="<?php echo "ORDER_PROP_" . $arProperties["ID"]; ?>"
                                           class="col-lg-3 col-xs-4 col-sm-12">
                                        <?= $arProperties["NAME"] ?>
                                        <? if ($arProperties["REQUIED_FORMATED"] == "Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif; ?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <?= showFilePropertyField("ORDER_PROP_" . $arProperties["ID"], $arProperties, $arProperties["VALUE"], $arProperties["SIZE1"]) ?>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?= $arProperties["DESCRIPTION"] ?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>


                                    <?
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <? endif; ?>
                <? if (CSaleLocation::isLocationProEnabled()):?>
                    <script type="text/javascript">
                        //<!--
                        (window.top.BX || BX).saleOrderAjax.addPropertyDesc(<?=CUtil::PhpToJSObject(array(
                            'id' => intval($arProperties["ID"]),
                            'attributes' => $propertyAttributes
                        ))?>);
                        //-->
                    </script>
                <?endif ?>
                <?

                ++$counter;
            }
            ?>

            <?
        }
    }
}