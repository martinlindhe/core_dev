<?
	require_once('config.php');

	require('design_head.php');

	if (!$session->id) {
		echo '<a href="login.php">Admin login</a><br>';
	}

	require('design_foot.php');
?>