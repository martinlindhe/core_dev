<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');

	echo '<div class="h_friends"></div>';

	echo '<a href="friends.php">VÄNNER</a> ('.relationsOnlineCount().' online)<br/>';
	echo '<a href="blocked.php">BLOCKADE</a><br/>';

	require('design_foot.php');
?>