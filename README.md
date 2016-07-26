# PHP_Compare_Website_Files
Compare similar website files. Shows missing files, different files (modified date / size), and files modified in the last 7 days.

Last Updated = 7/25/16
Created By: Blastroid

Instructions:

Copy the file_list.php file to the root directory of 2 websites you want to compare.

Use the following code:
	$compare = new compareFolders('http://URL1/file_list.php', 'http://URL2/file_list.php');
	
	Where it says URL1 change to the website #1 URL
	Where it says URL2 change to the website #1 URL
	
	Optional paremeters are naming the 2 website paths (Defaults are URL1, and URL2). Example:
		$compare = new compareFolders('http://URL1/file_list.php', 'http://URL2/file_list.php', 'Website 1', 'Website 2');
	
	Run the class where you want the 3 HTML tables to be written.

Note: In the compare_file.php you can add to the array $ignoreFiles to ignore files.
