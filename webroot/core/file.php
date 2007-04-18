<?
	/* file.php - takes a file id, returns the file */

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$fileId = $_GET['id'];

	$project = '../';	//Defaults to a config.php in the directory below this one
	if (!empty($_GET['pr']) && !strpbrk($_GET['pr'], '"\'/\\%&?;:.,')) {		//checks _pr for " ' \ / % & ? ; : . ,
		$project = preg_replace( "/[^\w\.-]+/", "_", $_GET['pr']); //bra regexp för att ta bort farliga tecken från filnamn
		$project = '../'.$project.'/';
	}

	require_once($project.'config.php');

	$files->sendFile($fileId);
?>