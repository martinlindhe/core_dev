<?
	require_once('../config.php');

	if (!$user->isAdmin) die('bye');
	
	require_once('set_c.php');
	require_once('set_fnc.php');
?>
