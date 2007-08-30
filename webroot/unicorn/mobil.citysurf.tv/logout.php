<?
	require_once('config.php');
	
	$user_auth->logout(1, true);
	header('Location: index.php');
?>
