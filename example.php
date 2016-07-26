<!--
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
-->

<style>
body{
	font-family:Arial;
}

table#missingTableData, table#fileDifferencesData, table#filesModifiedData{
	border-collapse:collapse;
	border:2px solid black;
}

table#missingTableData th, table#fileDifferencesData th, table#filesModifiedData th{
	background-color:black;
	color:white;
	border:1px solid lightgray;
	padding:3px;
	vertical-align:bottom;
	font-size:16px;
	text-align:center;
}

table#missingTableData td, table#fileDifferencesData td, table#filesModifiedData td{
	color:black;
	border:1px solid black;
	padding:3px;
	vertical-align:top;
	font-size:14px;
	color:black;
}

.ac{text-align:center;}
.spacer{border:0px;background-color:black;width:10px;}
.url1{background-color:#ccffff;}
.url2{background-color:#b3ffd9;}
.mismatch{background-color:#ffb3b3;}
</style>

<?php
include_once('compare_files.php');

$test = new compareFolders('bwat.info/other/class_compare_sites/A/file_list.php', 'bwat.info/other/class_compare_sites/B/file_list.php');
?>