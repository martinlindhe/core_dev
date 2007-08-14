<?
	session_start();
	require("./set_c.php");

	$vimmel = &new vimmel();
	$vimmel->vimmelRefresh();
?>