<?
	if (empty($_GET['id']) || empty($_GET['code'])) die;

	require_once('config.php');
	require('design_head.php');

	$auth->verifyActivationMail($_GET['id'], $_GET['code']);

	require('design_foot.php');
?>
