<?
	require('config.include.php');
	require('main.fnc.php');
	execSt();
	if(!defined('NO_SQL')) { require('sql.class.php'); $sql = &new sql(); }
	require('user.class.php');
	if(!defined('NO_SQL')) { $user = &new user($sql); }
?>