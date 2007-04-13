<?
	/* file.php - takes a file id, returns the file */

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$fileId = $_GET['id'];

	$download = false;
	if (isset($_GET['dl'])) $download = true;

	$project = '../';	//Defaults to a config.php in the directory below this one
	if (!empty($_GET['pr']) && !strpbrk($_GET['pr'], '"\'/\\%&?;:.,')) {		//checks _pr for " ' \ / % & ? ; : . ,
		$project = '../'.$_GET['pr'].'/';
	}

	require_once($project.'config.php');

	$files->sendFile($fileId, $download);
?>