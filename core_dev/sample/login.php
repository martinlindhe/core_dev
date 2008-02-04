<?
	require_once('config.php');
	$session->requireLoggedOut();
	require_core('class.Auth_Standard.php');

	require('design_head.php');

	$auth->showLoginForm();

	require('design_foot.php');
?>