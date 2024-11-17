<?php

if(isset($argv) && isset($argc) 
	&& !empty($argc) && !empty($argv)){

	ini_set('ERROR_REPORTING','E_ERROR');

	$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));

	file_put_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/tmp/cpimglog.txt","start: ".date("Y:m:d H:i:s"));

	function rsearch($folder, $pattern_array) {
		$return = array();
		$iti = new RecursiveDirectoryIterator($folder);
		foreach(new RecursiveIteratorIterator($iti) as $file){
			if (in_array(mb_strtolower(array_pop(explode('.', $file))), $pattern_array)){
				$return[] = (string)$file;
			}
		}
		return $return;
	}

	$aPaths = rsearch($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/pdf/', ($aExts = array('pdf')));

	foreach($aPaths as $sPdf){

		$sLog = $sPdf."\n";
		
		try{
		
			$fpdf = fopen($sPdf, 'rb');
			$oimg = new imagick(); // [0] can be used to set page number`
			$oimg->setResolution(300,300);
			$oimg->readImageFile($fpdf);
			$oimg->setImageFormat( "jpg" );
			$oimg->setImageCompression(imagick::COMPRESSION_JPEG); 
			$oimg->setImageCompressionQuality(90); 
			$oimg->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
			
			$sName = pathinfo($sPdf,PATHINFO_FILENAME);
			$sDir = dirname(pathinfo($sPdf,PATHINFO_DIRNAME));
			
			if($oimg->valid()){
			
				file_put_contents($sDir.'/images/'.$sName.'.jpg',$oimg);
				$sLog .= "Convert ".$sPdf." to: ".$sDir.'/images/'.$sName.'.jpg'."\n";
				
				if(file_exists($sPdf) && is_file($sPdf)){
					unlink($sPdf);
				};
				
			} else {
				$sLog .= "Error: ".$sPdf." to: ".$sDir.'/images/'.$sName.'.jpg'."\n";
			}
		
		} catch(Exception $e){
			
			$sLog .= "Error: ".$e->getMessage()."\n";
			
		}
		
		file_put_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/tmp/cpimglog.txt",$sLog,FILE_APPEND);
	} 
	
	
	file_put_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/tmp/cpimglog.txt","end: ".date("Y:m:d H:i:s"),FILE_APPEND);
	
}

//https://youtwig.ru/local/crontab/pdftoimage.php