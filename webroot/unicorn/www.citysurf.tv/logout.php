<?
	require_once('config.php');

	$user_auth->logout();
	header('Location: index.php');
	die;
?>