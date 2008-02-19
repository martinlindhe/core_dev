<?
/*
	i - file id
	x1, y2, x2, y2 - coordinates to cut the image in
*/

	require_once('find_config.php');

	if (!$session->id || empty($_GET['i']) || empty($_GET['x1']) || empty($_GET['y1']) || empty($_GET['x2']) || empty($_GET['y2'])) die;

	$files->imageCrop($_GET['i'], $_GET['x1'], $_GET['y1'], $_GET['x2'], $_GET['y2']);
?>