<?
	header('Content-type: text/xml');
	echo '<?xml version="1.0" ?>';

	if (empty($_GET['i']) || !is_numeric($_GET['i'])) die('<bad/>');
	$id = $_GET['i'];
	
	//todo: this path is not good!	
	include('../adblock/config.php');
	if (!$session->id) die('<bad/>');

	$db->query('DELETE FROM tblSettings WHERE ownerId='.$session->id.' AND settingId='.$id);
	
	echo '<ok/>';
?>