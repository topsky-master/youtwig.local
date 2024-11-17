<?php

$rfp = fopen('https://youtwig.ru/upload/export_file_ZHMB7iJ9LY7cqWjD.csv','r');
$bFirst = true;
$aStr = [];

while($afp = fgetcsv($rfp,0,';')) {
	
	if ($bFirst) {
		$bFirst = false;
		continue;
	}
	
	$aStr[$afp[1]] = trim($afp[0]); 	
		
}

fclose($rfp);

$rfp = fopen('https://youtwig.ru/upload/export_file_WPysIIjo0YZN2eLo.csv','r');
$rfp1 = fopen($_SERVER['DOCUMENT_ROOT'].'/upload/export_models.csv','w+');
$bFirst = true;

while($afp = fgetcsv($rfp,0,';')) {
	
	if ($bFirst) {
		$bFirst = false;
		fputcsv($rfp1,$afp,';');
		continue;
	}
		
	if (isset($aStr[$afp[4]]) 
		&& !empty($aStr[$afp[4]])) {	
		$afp[4] = $aStr[$afp[4]];
		fputcsv($rfp1,$afp,';');
	}
		
}

fclose($rfp1);

fclose($rfp);
