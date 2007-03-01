<?
	/* 
		file.php - tar emot file id, returnerar filen
	*/

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$fileId = $_GET['id'];

	$download = false;
	if (isset($_GET['dl'])) $download = true;

	include('config.php');

	$files->sendFile($fileId, $download);
?>