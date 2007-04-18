<?
	/* ajax_fileinfo.php - returns details about a certain file, in HTML format for inclusion in a div element */

	$project = '../';	//Defaults to a config.php in the directory below this one
	if (!empty($_GET['pr']) && !strpbrk($_GET['pr'], '"\'/\\%&?;:.,')) {		//checks _pr for " ' \ / % & ? ; : . ,
		$project = preg_replace( "/[^\w\.-]+/", "_", $_GET['pr']); //bra regexp för att ta bort farliga tecken från filnamn
		$project = '../'.$project.'/';
	}

	require_once($project.'config.php');

	if ((!$session->id && !$files->anon_uploads) || empty($_GET['i']) || !is_numeric($_GET['i'])) die('bad');

	$info = $files->getFileInfo($_GET['i']);
	if (!$info) die('bad');

	echo $info;
?>