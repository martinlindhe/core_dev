<?
	require_once('../config.php');

	if (!$user->isAdmin) die('bye');
	
	//fixme: true för admins. false för webmasters
	$isCrew = true; //$_SESSION['u_c'];

	require_once('set_c.php');
	require_once('set_fnc.php');
	require_once('set_formatadm.php');
?>
