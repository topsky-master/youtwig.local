#!/usr/bin/php -q
<?php

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

ini_set('default_charset','utf-8');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if (isset($argc) 
	&& $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$sTable = '';
$sDet = '';

$sTable = '{
                                                    "content": [
                                                        {
                                                            "widgetName": "raTextBlock",
                                                            "title": {
                                                                "content": [
                                                                    "Описание товара"
                                                                ],
                                                                "size": "size5",
                                                                "color": "color1"
                                                            },
                                                            "theme": "tertiary",
                                                            "padding": "type2",
                                                            "gapSize": "m",
                                                            "text": {
                                                                "size": "size2",
                                                                "align": "left",
                                                                "color": "color1",
                                                                "content": [
                                                                    "Текст Описания"
                                                                ]
                                                            }
                                                        },
                                                        {
                                                            "widgetName": "raTable",
                                                            "title": {
                                                                "content": [
                                                                    "Характеристики"
                                                                ],
                                                                "size": "size4",
                                                                "align": "left",
                                                                "color": "color1"
                                                            },
                                                            "table": {
                                                                "head": [
                                                                    {
                                                                        "img": {
                                                                            "src": "",
                                                                            "srcMobile": "",
                                                                            "alt": ""
                                                                        },
                                                                        "text": [
                                                                            "Название колонки"
                                                                        ],
                                                                        "contentAlign": "left"
                                                                    },
                                                                    {
                                                                        "img": {
                                                                            "src": "",
                                                                            "srcMobile": "",
                                                                            "alt": ""
                                                                        },
                                                                        "text": [
                                                                            "Название колонки"
                                                                        ],
                                                                        "contentAlign": "left"
                                                                    }
                                                                ],
                                                                "body": [
                                                                    [
                                                                        [
                                                                            "Характеристика"
                                                                        ],
                                                                        [
                                                                            "Описание"
                                                                        ]
                                                                    ],
                                                                    [
                                                                        [
                                                                            "Характеристика1"
                                                                        ],
                                                                        [
                                                                            "Описание1"
                                                                        ]
                                                                    ],
                                                                    [
                                                                        [
                                                                            "Характеристика2"
                                                                        ],
                                                                        [
                                                                            "Описание2"
                                                                        ]
                                                                    ],
                                                                    [
                                                                        [
                                                                            "Характеристика3"
                                                                        ],
                                                                        [
                                                                            "Описание3"
                                                                        ]
                                                                    ]
                                                                ],
                                                                "hideHead": true
                                                            }
                                                        },
                                                         {
      "widgetName": "list",
      "theme": "bullet",
      "blocks": [
        {
          "text": {
            "size": "size2",
            "align": "left",
            "color": "color1",
            "content": [
              "Пожалуйста, замените этот текст Вашим собственным. Просто кликните по тексту, чтобы добавить свой текст. Настройте стиль текста в левой колонке."
            ]
          },
          "title": {
            "content": [
              "Заголовок"
            ],
            "size": "size4",
            "align": "left",
            "color": "color1"
          }
        },
        {
          "text": {
            "size": "size2",
            "align": "left",
            "color": "color1",
            "content": [
              "Пожалуйста, замените этот текст Вашим собственным. Просто кликните по тексту, чтобы добавить свой текст. Настройте стиль текста в левой колонке."
            ]
          },
          "title": {
            "content": [
              "Заголовок"
            ],
            "size": "size4",
            "align": "left",
            "color": "color1"
          }
        },
        {
          "text": {
            "size": "size2",
            "align": "left",
            "color": "color1",
            "content": [
              "Пожалуйста, замените этот текст Вашим собственным. Просто кликните по тексту, чтобы добавить свой текст. Настройте стиль текста в левой колонке."
            ]
          },
          "title": {
            "content": [
              "Заголовок"
            ],
            "size": "size4",
            "align": "left",
            "color": "color1"
          }
        }
      ]
    }
                                                    ],
                                                    "version": 0.3
                                                }';

$sTable = json_decode($sTable,true);

$sProps = 'TYPEPRODUCT, COUNTRY, COLOR, DIAMETR, VNESHNIY_DIAMETR, VISOTA, DLINNA, SHIRINA, KOLICHESTVO_ZUBEV, TYPE_OF_PROFILE, POWER, TYPE_OF_MOUNT, PLACE_OF_CONTACTS, TYPE_OF_FABRIC, VNUNTRENNIY_DIAMETR, NUMBER_OF_CONTACTS, VOLUME, COVERING, FEATURES, ARTNUMBER, RESISTANCE, HOLE, WHEEL_DIAMETR, ANGLE, TYPE_OF_BORE, WARRANTY, TYPE_OF_BELT, MATERIAL, KOMPLEKT, OBSHIY_RAZMER, VNUTRENNIY_KVADRAT, TOLSHCINA, PURPOSE_OF_NOZZLE, VOLTAGE, AMPERAGE, TURNS, DIAMETR_VTULKI, DIAMETR_RABOCHEY_CHASTI, DLINA_RABOCHEY_CHASTI, CAPACITANCE, DIAMETR_VTULKI_POD_SALNIK, DIAMETR_POD_PODShIPNIK, DLINA_VALA, RASSTOJaNIE, RASSTOYANIE_KREPEJA, CONNECT, TYPE_OF_PUMP, OBSCHAYA_TOLSHCINA, filter_type, temp, COKOL, NOMINAL_VOLTAGE, MAX_TEMP, SHIRINA_FLANCA, SHIRINA_REZINI, BURTIK_NA_REZINE, DIAMETR_TRUBKI, FORMA, VNUTRENNIY_PROGIB, DIAMETR_PO_REZBE, VYSOT_POSADKI, RAZMER_POSADKI, DIAMETR_POSADKI, MODEL_HTML';
$aProps = explode(',',$sProps);
$aProps = array_map('trim',$aProps);

$rdb = CIBlockElement::GetList(
    [],
    ['IBLOCK_ID' => 11, 'ACTIVE' => 'Y'],
    false,
    false,
    ['DETAIL_TEXT','ID','NAME']
);

if ($rdb) {

	while ($aDb = $rdb->getNext()) {

		$sDet = trim(str_ireplace("\n",", ",trim(strip_tags($aDb['DETAIL_TEXT']))));
		$sName = trim($aDb['NAME']);

		if (isset($sTable['content'])
			&& isset($sTable['content'][0])
			&& isset($sTable['content'][0]['title'])
			&& isset($sTable['content'][0]['title']['content'])) {

			$sTable['content'][0]['title']['content'] = [$sName];
		}

		if (isset($sTable['content'])
			&& isset($sTable['content'][0])
			&& isset($sTable['content'][0]['text'])
			&& isset($sTable['content'][0]['text']['content'])) {

			$sTable['content'][0]['text']['content'] = [$sDet];
		}

		$sTable['content'][1]['table']['body'] = [];
		$sTable['content'][2]['blocks'] = [];
		$iCount = 0;

		foreach ($aProps as $iProp => $sProp) {

			$dProps = CIBlockElement::GetProperty(11, $aDb['ID'], [],['CODE' => $sProp]);

			if($dProps) {

				$values = '';
				$name = '';

				while ($aProp = $dProps->Fetch()) {

					if (empty($name)) {
						$name = $aProp['NAME'];
					}

					$aProp['VALUE'] = (isset($aProp['VALUE_ENUM']) && !empty($aProp['VALUE_ENUM']))
						? trim($aProp['VALUE_ENUM'])
						: $aProp['VALUE'];

					$aProp['VALUE'] = is_array($aProp['VALUE']) ? join(', ',$aProp['VALUE']) : $aProp['VALUE'];
					$aProp['VALUE'] = trim($aProp['VALUE']);

					$values .= (!empty($values) ? ', ' : '') . $aProp['VALUE'];
				}

				if (!empty($values)) {

					$values = explode(',',$values);
					$values = array_map('trim',$values);
					$values = array_unique($values);

					if ($sProp == 'MODEL_HTML') {

						$newValues = [];

						foreach ($values as $strId) {

							$rmedb = CIBlockElement::GetList(
								[],
								['IBLOCK_ID' => 37, 'ID' => trim($strId)],
								false,
								false,
								['DETAIL_TEXT']
							);

							if ($rmedb && $ameDb = $rmedb->getNext()) {
								$newValues[] = trim(str_ireplace("\n",", ",trim(strip_tags($ameDb['DETAIL_TEXT']))));
							}
						}

						$values = $newValues;

					}

					$values = join(',',$values);

					if (isset($name) && !empty($name) && !empty($values)) {

						$sTable['content'][1]['table']['body'][$iCount] = [
							0 => [0 => $name],
							1 => [0 => $values]
						];

						$sTable['content'][2]['blocks'][$iCount] = array (
							'text' =>
								array (
									'size' => 'size2',
									'align' => 'left',
									'color' => 'color1',
									'content' =>
										array (
											0 => $values,
										),
								),
							'title' =>
								array (
									'content' =>
										array (
											0 => $name,
										),
									'size' => 'size3',
									'align' => 'left',
									'color' => 'color1',
								),
						);

						++$iCount;

					}


				}


			}

		}

		$json = json_encode($sTable, JSON_UNESCAPED_UNICODE);
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/json/'.$aDb['ID'].'.txt',$json);
	}
}