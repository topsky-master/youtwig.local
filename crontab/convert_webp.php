<?php

if(!isset($argv)){
	die();
}


function rsearch($folder, $pattern_array) {
    $return = array();
    $iti = new RecursiveDirectoryIterator($folder);
    foreach(new RecursiveIteratorIterator($iti) as $file){
        if (@in_array(strtolower(array_pop(explode('.', $file))), $pattern_array)){
            $file = (string)$file;
			$return[$file] = $file;
        }
    }
    return $return;
}

$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));

$aPaths = rsearch($_SERVER['DOCUMENT_ROOT'].'/upload/', ($aExts = array('jpeg','jpg','gif','png')));
$aPaths[] = $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/nmain/images/no_photo.png'; 
 
foreach($aPaths as $sPath) {
	
	$aPath = pathinfo($sPath);

	if(isset($aPath['filename'])) {

		$cache = $_SERVER['DOCUMENT_ROOT'].'/upload/webp/'.trim(str_ireplace( $_SERVER['DOCUMENT_ROOT'],'',$aPath['dirname']),'/').'/';
		
		if(!file_exists($cache)) {
			@mkdir($cache,0775,true);
		}
		
		$base_ext = mb_strtolower(trim($aPath['extension']));
		$base_name = $aPath['filename'];
		$time = filemtime($sPath);
		$base_name .= '_'.$time; 
		
		if(!file_exists($cache . $base_name . '.webp') && $base_ext != 'webp') {
			passthru('cwebp -q 90 '.escapeshellarg($sPath).' -o '.escapeshellarg($cache . $base_name) . '.webp -quiet');
			
			if(file_exists($cache . $base_name . '.webp')) {
									 
				$imagesize = getimagesize($cache . $base_name . '.webp');
				if (!(isset($imagesize[0]) && !empty($imagesize[0])
					&& isset($imagesize[1]) && !empty($imagesize[1]) 
					&& filesize($cache . $base_name . '.webp') > 0)
				) {
										
					unlink($cache . $base_name . '.webp');
										
				} else {
					echo $cache . $base_name . '.webp'."\n";
				}					

			}
			
		}

	}
	
} 
