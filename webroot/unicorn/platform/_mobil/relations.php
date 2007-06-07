<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');

	echo 'DINA RELATIONER<br/>';
	echo '<br/>';
	echo '<a href="friends.php">VÄNNER</a> ('.relationsOnlineCount().' online)<br/>';
	echo '<a href="blocked.php">BLOCKERADE</a><br/>';

	require('design_foot.php');
?>