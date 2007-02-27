<?
	header('Content-type: text/xml');
	echo '<?xml version="1.0" ?>';

	if (empty($_GET['i'])) die('<bad/>');
	$id = $_GET['i'];
	
	//todo: this path is not good!	
	include('../oop_test/config.php');

	$db->query('DELETE FROM tblSettings WHERE ownerId='.$session->id.' AND settingId='.$id);
	
	echo '<ok/>';
?>