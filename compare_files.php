<?php
/*
Last Updated = 7/25/16
Created By: Blastroid
Compare website files.

Instructions:
1. Copy the file_list.php file to the root directory of 2 websites you want to compare.
2. Use the following code:
		$compare = new compareFolders('http://URL1/file_list.php', 'http://URL2/file_list.php');
	
		Where it says URL1 change to the website #1 URL
		Where it says URL2 change to the website #1 URL
	
	Optional paremeters are naming the 2 website paths (Defaults are URL1, and URL2). Example:
		$compare = new compareFolders('http://URL1/file_list.php', 'http://URL2/file_list.php', 'Website 1', 'Website 2');
		
	Run the class where you want the 3 HTML tables to be written.
*/
class compareFolders{
	
	public function __construct($URL1, $URL2, $URL1Title = 'URL1', $URL2Title = 'URL2'){
		$errors = '';
		
		//ADD FILENAMES TO IGNORE HERE:
		$ignoreFiles = array(
			'file_list.php'
		);
		
		//Check if fiename is found in the $ignoreFiles array.
		function ignoreFile($filename){
			global $ignoreFiles;
			for($i = 0; $i < count($ignoreFiles); $i++){
				if (stripos($filename, $ignoreFiles[$i]) !== false){
					return true;
				}
			}
			return false;
		}
		
		//Get file contents.
		function url_get_contents ($URL) {
			if (!function_exists('curl_init')){ 
				die('CURL is not installed!');
			}
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $URL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);
			curl_close($ch);
			return $output;
		}
		
		//Check if URL exists.
		function fileFound($url){
			$handle = curl_init($url);
			curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
			$response = curl_exec($handle);
			$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
			if($httpCode == 404){
				curl_close($handle);
				return false;
			} else {
				curl_close($handle);
				return true;
			}
		}
		
		//Check to see if the URL's are pointing to the file_list.php file.
		if (stripos($URL1, 'file_list.php') === false){
			$errors.= '<br/>URL #1 not pointing to the file file_list.php: '.$URL1.PHP_EOL;
		}
		if (stripos($URL2, 'file_list.php') === false){
			$errors.= '<br/>URL #2 not pointing to the file file_list.php: '.$URL2.PHP_EOL;
		}

		//Check to see if the URL's can be found.
		if ($errors == ''){
			if (fileFound($URL1) == false){
				$errors.= '<br/>URL #1 was not found: '.$URL1.PHP_EOL;
			}
			if (fileFound($URL2) == false){
				$errors.= '<br/>URL #2 was not found.'.$URL2;
			}
		}
		
		if ($errors == ''){
			//No errors, continue to compare URL's.
					
			$URL1Files = json_decode(url_get_contents($URL1), true);
			$URL2Files = json_decode(url_get_contents($URL2), true);
			
			$acount = -1;
			$acount2 = -1;
			$acount3 = -1;
			$missing_files = Array();
			$modified_recently = Array();
			$date_differences = Array();
			
			for($i = 0; $i < count($URL1Files);$i++){ //Loop through URL1 file list
				if (stripos($URL1Files[$i]["filename"], '.') !== false && ignoreFile($URL1Files[$i]["filename"]) == false){
					$filefound = false;
					for($j = 0; $j < count($URL2Files);$j++){ //Loop through URL2 file list
						if ($URL1Files[$i]["filename"] == $URL2Files[$j]["filename"] && ignoreFile($URL2Files[$j]["filename"]) == false){
							$filefound = true;
							if (($URL1Files[$i]["date"] != $URL2Files[$j]["date"]) || ($URL1Files[$i]["size"] != $URL2Files[$j]["size"])){
								$acount2++;
								$date_differences[$acount2]["filename"] = $URL1Files[$i]["filename"];
								$date_differences[$acount2]["s1_date"] = $URL1Files[$i]["date"];
								$date_differences[$acount2]["s2_date"] = $URL2Files[$j]["date"];
								$date_differences[$acount2]["dev_size"] = $URL1Files[$i]["size"];
								$date_differences[$acount2]["prod_size"] = $URL2Files[$j]["size"];
								$difference = "";
								if ($URL1Files[$i]["date"] != $URL2Files[$j]["date"]){
									$difference.= "date";
								} else {
									$difference.= "size";
								}
								$date_differences[$acount2]["difference"] = $difference;
							}
							if ($URL1Files[$i]["old"] == "0"){
								$acount3++;
								$modified_recently[$acount3]["filename"] = $URL1Files[$i]["filename"];
								$modified_recently[$acount3]["date"] = $URL1Files[$i]["date"];
								$modified_recently[$acount3]["size"] = $URL1Files[$i]["size"];
								$modified_recently[$acount3]["level"] = $URL1Title;
							}
							if ($URL2Files[$j]["old"] == "0"){
								$acount3++;
								$modified_recently[$acount3]["filename"] = $URL2Files[$j]["filename"];
								$modified_recently[$acount3]["date"] = $URL2Files[$j]["date"];
								$modified_recently[$acount3]["size"] = $URL2Files[$j]["size"];
								$modified_recently[$acount3]["level"] = $URL2Title;
							}
						}
					}
					if ($filefound == false){
						if ($URL1Files[$i]["filename"] != "./~resources/exe/default.php"){
							$acount++;
							$missing_files[$acount]["level"] = $URL1Title;
							$missing_files[$acount]["filename"] = $URL1Files[$i]["filename"];
							$missing_files[$acount]["date"] = $URL1Files[$i]["date"];
							$missing_files[$acount]["size"] = $URL1Files[$i]["size"];
						}
					}
				}
			}

			for($i = 0; $i < count($URL2Files);$i++){
				$filefound = false;
				for($j = 0; $j < count($URL1Files);$j++){
					if ($URL2Files[$i]["filename"] == $URL1Files[$j]["filename"]){
						$filefound = true;
					}
				}
				if ($filefound == false){
					if ($URL2Files[$i]["filename"] != "./~resources/exe/default.php"){
						$acount++;
						$missing_files[$acount]["level"] = "URL2";
						$missing_files[$acount]["filename"] = $URL2Files[$i]["filename"];
						$missing_files[$acount]["date"] = $URL2Files[$i]["date"];
						$missing_files[$acount]["size"] = $URL2Files[$i]["size"];
					}
				}
			}
						
			//MISSING FILES
			$tmpS = '';
			$tmpS = "<h3 style='display:inline;'>MISSING FILES:</h3><br/>".PHP_EOL;
			$tmpS.= "<table id='missingTableData'>".PHP_EOL;
			if (count($missing_files) == 0){
				$tmpS.= "<tr><td style='background-color:lightgreen;'>No missing files</td></tr>".PHP_EOL;
			} else {
				$tmpS.= "<tr>".PHP_EOL;
				$tmpS.= "	<th>Found In</th>".PHP_EOL;
				$tmpS.= "	<th>Path / Filename</th>".PHP_EOL;
				$tmpS.= "	<th>Date</th>".PHP_EOL;
				$tmpS.= "	<th>Size</th>".PHP_EOL;
				$tmpS.= "</tr>".PHP_EOL;
				for($i = 0; $i < count($missing_files); $i++){
					if (ignoreFile($missing_files[$i]["filename"]) == false){
						if ($missing_files[$i]["level"] == 'URL2'){
							$class = 'url2';
						} else {
							$class = 'url1';
						}
						$tmpS.= "<tr>".PHP_EOL;
						$tmpS.= "	<td class='".$class."'>".$missing_files[$i]["level"]."</td>".PHP_EOL;
						$tmpS.= "	<td class='".$class."'>".$missing_files[$i]["filename"]."</td>".PHP_EOL;
						$tmpS.= "	<td class='".$class."'>".$missing_files[$i]["date"]."</td>".PHP_EOL;
						$tmpS.= "	<td class='ac ".$class."'>".$missing_files[$i]["size"]."</td>".PHP_EOL;
						$tmpS.= "</tr>".PHP_EOL;
					}
				}
			}
			$tmpS.= "</table>".PHP_EOL;
			echo $tmpS;
			
			//FILE DIFFERENCES:
			$tmpS = '';
			$tmpS = "<h3 style='display:inline;'><br/>FILE DIFFERENCES:</h3><br/>".PHP_EOL;
			$tmpS.= "<b>Filter:</b> ".PHP_EOL;
			$tmpS.= "<span onclick=\\\"$('.diffrow').show();\\\"><input type='radio' name='difffilter' value='diffrow' checked />Both</span>".PHP_EOL;
			$tmpS.= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span onclick=\\\"$('.diffrow').hide(); $('.datediff').show();\\\"><input type='radio' name='difffilter' value='datediff' />Date</span>".PHP_EOL;
			$tmpS.= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span onclick=\\\"$('.diffrow').hide(); $('.sizediff').show();\\\"><input type='radio' name='difffilter' value='sizediff' />Size</span>".PHP_EOL;
			$tmpS.= "<table id='fileDifferencesData' border='1'>".PHP_EOL;
			if (count($date_differences) == 0){
				$tmpS.= "<tr><td style='background-color:lightgreen;'>No file differences</td></tr>".PHP_EOL;
			} else {
				$tmpS.= "<tr>".PHP_EOL;
				$tmpS.= "	<th style='border-bottom:0px;'></th>".PHP_EOL;
				$tmpS.= "	<th class='spacer'></th>".PHP_EOL;
				$tmpS.= "	<th colspan='2' style='border-bottom-0px;text-align:center;'>".$URL1Title."</th>".PHP_EOL;
				$tmpS.= "	<th class='spacer'></th>".PHP_EOL;
				$tmpS.= "	<th colspan='2' style='border-bottom-0px;text-align:center;'>".$URL2Title."</th>".PHP_EOL;
				$tmpS.= "</tr>".PHP_EOL;
				$tmpS.= "<tr>".PHP_EOL;
				$tmpS.= "	<th style='border-top:0px;'>Path / Filename</th>".PHP_EOL;
				$tmpS.= "	<th class='spacer'></th>".PHP_EOL;
				$tmpS.= "	<th>Size</th>".PHP_EOL;
				$tmpS.= "	<th>Date</th>".PHP_EOL;
				$tmpS.= "	<th class='spacer'></th>".PHP_EOL;
				$tmpS.= "	<th>Size</th>".PHP_EOL;
				$tmpS.= "	<th>Date</th>".PHP_EOL;
				$tmpS.= "</tr>".PHP_EOL;
				for($i = 0; $i < count($date_differences); $i++){
					$dateclass = '';
					$sizeclass = '';
					$filterClass = '';
					if ($date_differences[$i]["s1_date"] != $date_differences[$i]["s2_date"]){
						$dateclass = " mismatch";
						$filterClass.= " datediff";
					}
					if ($date_differences[$i]["dev_size"] != $date_differences[$i]["prod_size"]){
						$sizeclass = " mismatch";
						$filterClass.= " sizediff";
					}
					$tmpS.= "<tr class='diffrow".$filterClass."'>".PHP_EOL;
					$tmpS.= "	<td>".$date_differences[$i]["filename"]."</td>".PHP_EOL;
					$tmpS.= "	<th class='spacer'></th>".PHP_EOL;
					$tmpS.= "	<td class='".$dateclass."'>".$date_differences[$i]["s1_date"]."</td>".PHP_EOL;
					$tmpS.= "	<td class='ac".$sizeclass."'>".$date_differences[$i]["dev_size"]."</td>".PHP_EOL;
					$tmpS.= "	<th class='spacer'></th>".PHP_EOL;
					$tmpS.= "	<td class='".$dateclass."'>".$date_differences[$i]["s2_date"]."</td>".PHP_EOL;
					$tmpS.= "	<td class='ac".$sizeclass."'>".$date_differences[$i]["prod_size"]."</td>".PHP_EOL;
					$tmpS.= "</tr>".PHP_EOL;
				}
			}
			$tmpS.= "</table>".PHP_EOL;
			echo $tmpS;
			
			
			//FILES MODIFIED IN THE LAST 7 DAYS
			$tmpS = '';
			$tmpS = "<h3 style='display:inline;'><br/>FILES MODIFIED IN THE LAST 7 DAYS:</h3><br/>".PHP_EOL;
			$tmpS.= "<table id='filesModifiedData' border='1'>".PHP_EOL;
			if (count($modified_recently) == 0){
				$tmpS.= "<tr><td style='background-color:lightgreen;'>No modified files found</td></tr>".PHP_EOL;
			} else {
				$tmpS.= "<tr>".PHP_EOL;
				$tmpS.= "<th>Level</th>".PHP_EOL;
				$tmpS.= "<th>Path / Filename</th>".PHP_EOL;
				$tmpS.= "<th>Date</th>".PHP_EOL;
				$tmpS.= "<th>Size</th>".PHP_EOL;
				$tmpS.= "</tr>".PHP_EOL;
				for($i = 0; $i < count($modified_recently);$i++){
					$class = '';
					if ($modified_recently[$i]["level"] == $URL1Title){
						$class = 'url1';
					} else {
						$class = 'url2';
					}
					$tmpS.= "<tr>".PHP_EOL;
					$tmpS.= "<td class='".$class."'>".$modified_recently[$i]["level"]."</td>".PHP_EOL;
					$tmpS.= "<td class='".$class."'>".$modified_recently[$i]["filename"]."</td>".PHP_EOL;
					$tmpS.= "<td class='".$class."'>".$modified_recently[$i]["date"]."</td>".PHP_EOL;
					$tmpS.= "<td class='ac ".$class."'>".$modified_recently[$i]["size"]."</td>".PHP_EOL;
					$tmpS.= "</tr>".PHP_EOL;
					
				}
			}
			$tmpS.= "</table>".PHP_EOL;
			echo $tmpS;
				
		} else {
			//Errors found
			echo 'ERROR/S:'.$errors;
		}
		
	}
	
}
?>