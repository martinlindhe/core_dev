<?
	require_once('config.php');
	
	if (!$session->id) die;

	require('design_head.php');

	$files->showFiles(FILETYPE_FILEAREA_UPLOAD);
?>

<br/>
<a href="index.php">Tillbaka till framsidan</a>

<?
	require('design_foot.php');
?>