<?
	/* ajax_del_uservar.php - deletes a user variable */

	require_once('find_config.php');

	header('Content-type: text/xml');
	echo '<?xml version="1.0" ?>';

	if (!$session->id || empty($_GET['i']) || !is_numeric($_GET['i'])) die('<bad/>');

	$db->query('DELETE FROM tblSettings WHERE ownerId='.$session->id.' AND settingId='.$_GET['i']);

	echo '<ok/>';
?>