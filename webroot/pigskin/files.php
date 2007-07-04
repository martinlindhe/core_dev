<?
	require_once('config.php');

	$session->requireLoggedIn();

	require('design_head.php');

	$files->showFiles(FILETYPE_USERFILE);
?>

<br/>
<a href="index.php">Tillbaka till framsidan</a>

<?
	require('design_foot.php');
?>