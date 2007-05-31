<?
	require_once('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$wordId = $_GET['id'];

	require('design_head.php');

	$word = getWord($wordId);

	print_r($word);

	require('design_foot.php');
?>