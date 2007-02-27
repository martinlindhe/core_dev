<?
	/* 
		file.php - tar emot file id, returnerar filen
	
	*/
	
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$fileId = $_GET['id'];

	$download = 0;
	if (isset($_GET['dl'])) $download = 1;
	
	include('config.php');

	$files->outputFile($fileId, $download);
?>