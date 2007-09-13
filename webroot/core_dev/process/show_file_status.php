<?
	require_once('config.php');

	//todo: ability to force recalculation of checksums. verify that the file is on disk

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('no id');
	$fileId = $_GET['id'];

	require('design_head.php');

	wiki('ProcessShowfileStatus');

	showFileQueueStatus($fileId);

	echo '<h1>Conversions based on this file</h1>';
	$list = $files->getClonesList($fileId);
	d($list);

	echo '<a href="http_enqueue.php?id='.$fileId.'">Convert media</a>';

	require('design_foot.php');
?>