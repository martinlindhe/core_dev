<?
	include('include_all.php');

	$wordId = 0;
	if ($_GET['id'] && is_numeric($_GET['id'])) $wordId = $_GET['id'];
	if (!$wordId) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');

	$word = getWord($db, $wordId);
	
	print_r($word);


	include('design_foot.php');
?>