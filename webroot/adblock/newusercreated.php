<?
	require_once('config.php');

	require('design_head.php');

	echo getInfoField('page_newuser_created');

	if ($_SESSION['isAdmin']) {
		echo 'User created - <b>you are still logged in as '.$_SESSION['userName'].'</b>';
	}

	require('design_foot.php');
?>