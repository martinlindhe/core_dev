<?
	require_once('config.php');

	$wordId = 0;
	if ($_GET['id'] && is_numeric($_GET['id'])) $wordId = $_GET['id'];
	if (!$wordId) {
		header('Location: '.$config['start_page']);
		die;
	}

	require('design_head.php');

	$word = getWord($wordId);

	print_r($word);

	require('design_foot.php');
?>