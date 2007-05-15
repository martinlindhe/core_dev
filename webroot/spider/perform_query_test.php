<?
	require_once('config.php');

	require('design_head.php');

	$list = unserialize(file_get_contents('dump.txt'));

	d($list);



	require('design_foot.php');
?>