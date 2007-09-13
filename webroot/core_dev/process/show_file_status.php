<?
	require_once('config.php');

	//todo: ability to force recalculation of checksums. verify that the file is on disk

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('no id');
	$fileId = $_GET['id'];

	require('design_head.php');

	wiki('ProcessShowfileStatus');

	showFileQueueStatus($fileId);

	$file = $files->getFileInfo($fileId);
	if ($file['fileType'] == FILETYPE_PROCESS_CLONE) {
		echo 'This file is a clone of the orginal file <a href="'.$_SERVER['PHP_SELF'].'?id='.$file['ownerId'].'">'.$file['ownerId'].'</a><br/>';
	}

	$list = $files->getClonesList($fileId);
	//d($list);
	if ($list) echo '<h1>Conversions based on this file</h1>';
	foreach ($list as $row) {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$row['fileId'].'">'.$row['fileId'].'</a> '.formatDataSize($row['fileSize']).' '.$row['fileMime'].'<br/>';
	}
	echo '<br/>';

	echo '<a href="http_enqueue.php?id='.$fileId.'">Convert media</a>';

	require('design_foot.php');
?>