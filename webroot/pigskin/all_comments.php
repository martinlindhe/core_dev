<?
	require_once('config.php');
	$session->requireLoggedIn();

	require('design_head.php');

	echo '<h1>Visar alla kommentarer, nyast först</h1>';



	require('design_foot.php');
?>