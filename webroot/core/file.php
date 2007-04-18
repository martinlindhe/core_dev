<?
	/* file.php - takes a file id, returns the file */

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	require_once('find_config.php');

	require_once($project.'config.php');

	$files->sendFile($_GET['id']);
?>