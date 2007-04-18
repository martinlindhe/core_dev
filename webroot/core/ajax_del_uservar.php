<?
	/* ajax_del_uservar.php - deletes a user variable */

	$project = '../';	//Defaults to a config.php in the directory below this one
	if (!empty($_GET['pr']) && !strpbrk($_GET['pr'], '"\'/\\%&?;:.,')) {		//checks _pr for " ' \ / % & ? ; : . ,
		$project = preg_replace( "/[^\w\.-]+/", "_", $_GET['pr']); //bra regexp för att ta bort farliga tecken från filnamn
		$project = '../'.$project.'/';
	}

	require_once($project.'config.php');

	header('Content-type: text/xml');
	echo '<?xml version="1.0" ?>';

	if (!$session->id || empty($_GET['i']) || !is_numeric($_GET['i'])) die('<bad/>');

	$db->query('DELETE FROM tblSettings WHERE ownerId='.$session->id.' AND settingId='.$_GET['i']);

	echo '<ok/>';
?>