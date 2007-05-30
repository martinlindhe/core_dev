<?
	$project = '../';	//Defaults to a config.php in the directory below this one
	if (!empty($_GET['pr'])) {
		if (strpbrk($_GET['pr'], '"\'/\\%&?;:.,')) die;				//checks _pr for " ' \ / % & ? ; : . ,
		$project = preg_replace( "/[^\w\.-]+/", "_", $_GET['pr']); //bra regexp för att ta bort farliga tecken från filnamn
		if ($project != $_GET['pr']) die;	//invalid chars in path
		$project = '../../'.$project.'/';
	}

	require_once($project.'config.php');
?>