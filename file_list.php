<?php
//Prevent page caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

//Gets list of file paths in all folders except for ignored folders.
function globRecursive($pattern){
	$files = Array();
	$files = glob($pattern, 0);
	foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR) as $dir){
		$fileok = true;
		if ($fileok == true){
			$files = array_merge($files, globRecursive($dir.'/'.basename($pattern), 0));
		}
	}
	return $files;
}

//Convert bytes to friendly size description.
function humanFileSize($bytes, $decimals = 0){
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).' '.@$sz[$factor];
}

/*Create array $details:
	[#]["filename"] = filename
	[#]["date"] = modified date
	[#]["size"] = size in bytes
	[#]["old"] = 1 if older than 7 days and 0 if not */
$files = globRecursive("*");
$details = Array();
$aCount = -1;
for($i = 0; $i < count($files);$i++){
	$aCount++;
	$details[$aCount]['filename'] = str_replace('../../', '', $files[$i]);
	$details[$aCount]['date'] = date ('F d Y (H:i:s)', filemtime($files[$i]));
	$details[$aCount]['size'] = humanFileSize(filesize($files[$i]));
	if (filemtime($files[$i]) < (time() - (7 * 24 * 60 * 60 ))){
		$details[$aCount]['old'] = '1'; //Older than 7 days
	} else {
		$details[$aCount]['old'] = '0';
	}
}

//Draws a JSON string of the $details array.
echo json_encode($details);
?>