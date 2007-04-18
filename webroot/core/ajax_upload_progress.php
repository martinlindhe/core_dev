<?
	/* ajax_upload_progress.php - reports back progress of current file upload to browser, using php_apc.dll extension */

	if (empty($_GET['s']) || !is_numeric($_GET['s'])) die;

	$project = '../';	//Defaults to a config.php in the directory below this one
	if (!empty($_GET['pr']) && !strpbrk($_GET['pr'], '"\'/\\%&?;:.,')) {		//checks _pr for " ' \ / % & ? ; : . ,
		$project = preg_replace( "/[^\w\.-]+/", "_", $_GET['pr']); //bra regexp för att ta bort farliga tecken från filnamn
		$project = '../'.$project.'/';
	}

	require_once($project.'config.php');

  $status = apc_fetch('upload_'.$_GET['s']);
  print_r($status);
?>