<?php

//https://youtwig.ru/local/crontab/check_m404codes.php?intestwetrust=1

//if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class ShortRedirectsCheck{

	public static function check404Code($url){

        $http_code = '';

        $tuCurl = curl_init();

        if($tuCurl && is_resource($tuCurl)) {


            $opts = array(
                CURLOPT_HTTPGET => 1,
                CURLOPT_CONNECTTIMEOUT => 12,
                CURLOPT_RETURNTRANSFER => 1
            );


            $opts[CURLOPT_HEADER] = 0;

            $opts[CURLOPT_URL] = $url;

            $opts[CURLOPT_FOLLOWLOCATION] = 0;
            $opts[CURLOPT_AUTOREFERER] = 0;

            $opts[CURLOPT_COOKIESESSION] = 1;
            $opts[CURLOPT_VERBOSE] = 0;

            $opts[CURLOPT_SSL_VERIFYHOST] = 0;
            $opts[CURLOPT_SSL_VERIFYPEER] = 0;

            $opts[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)';
            $opts[CURLOPT_ENCODING] = "";


            foreach ($opts as $key => $value) {
                curl_setopt($tuCurl, $key, $value);
            }


            $tuData = curl_exec($tuCurl);
            $http_code = curl_getinfo($tuCurl, CURLINFO_HTTP_CODE);
            curl_close($tuCurl);

        }

        return $http_code;
    }

    public static function checkCycleRedirect(){

		$aUnique = array();

		$rfp1 = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/ddoubles.csv','w+');
		$rfp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/mdoubles.csv','r+');

		if($rfp){
			while($aMod = fgetcsv($rfp,0,';')){

				$aMod = array_map('trim',$aMod);


				if(isset($aMod[5]) 
					&& !empty($aMod[5]) 
					&& $aMod[5] > 0
					&& !isset($aUnique[$aMod[5]])
					){

					$aUnique[$aMod[5]] = 0;
					$reDb = CIBlockElement::GetById($aMod[5]);	

					if($reDb 
						&& ($aModel = $reDb->GetNext())){

						$sLink = 'https://youtwig.ru/model/'.$aModel['CODE'].'/';
						$sCode = static::check404Code($sLink);
						$aMod[] = $sCode; 
						fputcsv($rfp1,$aMod,";");		

					}

				}

			}
		}

		fclose($rfp);
		fclose($rfp1);

    }


}

ShortRedirectsCheck::checkCycleRedirect();