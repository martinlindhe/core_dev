<?php

require_once('config.php');
$h->session->requireLoggedIn();

//TODO: ability to force recalculation of checksums. verify that the file is on disk

if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('no id');
$fileId = $_GET['id'];

require('design_head.php');

$data = $files->getFileInfo($fileId);
if (!$data) {
	echo '<h1>File dont exist</h1>';
	die;
}

$list = getQueuedEvents($fileId);

if (!empty($list)) {
	echo '<h1>'.count($list).' queued actions</h1>';
	foreach ($list as $row) {
		echo '<h3>Was enqueued '.ago($row['timeCreated']).' by '.Users::link($row['creatorId']);
		echo ' type='.$row['orderType'].', params='.$row['orderParams'];
		echo '</h3>';
	}
} else {
	echo '<h1>No queued action</h1>';
}

echo 'Process log:<br/>';
$list = getProcessLog($fileId);

echo '<table border="1">';
echo '<tr>';
echo '<th>Added</th>';
echo '<th>Completed</th>';
echo '<th>Exec time</th>';
echo '<th>Type</th>';
echo '<th>Created by</th>';
echo '</tr>';
foreach ($list as $row) {
	echo '<tr>';
	echo '<td>'.$row['timeCreated'].'</td>';
	if ($row['orderStatus'] == ORDER_COMPLETED) {
		echo '<td>'.$row['timeCompleted'].'</td>';
		echo '<td>'.round($row['timeExec'], 3).'s</td>';
	} else {
		echo '<td>not done</td>';
		echo '<td>?</td>';
	}
	echo '<td>'.$row['orderType'].'</td>';
	echo '<td>'.Users::link($row['creatorId']).'</td>';
	//echo $row['orderParams'];
	echo '</tr>';
}
echo '</table>';

showFileInfo($fileId);

$file = $files->getFileInfo($fileId);
if ($file['fileType'] == FILETYPE_CLONE_CONVERTED) {
	echo 'This file is a converted version of the orginal file <a href="'.$_SERVER['PHP_SELF'].'?id='.$file['ownerId'].'">'.$file['ownerId'].'</a><br/>';
}

$list = $files->getFileList(FILETYPE_CLONE_CONVERTED, $fileId);
if ($list) echo '<h1>Conversions based on this file</h1>';
foreach ($list as $row) {
	echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$row['fileId'].'">'.$row['fileId'].'</a> '.formatDataSize($row['fileSize']).' '.$row['fileMime'].'<br/>';
}
echo '<br/>';

$files->updateFile($fileId);

echo '<a href="http_enqueue.php?id='.$fileId.'">Create process (media conversion, or further processing)</a>';

require('design_foot.php');
?>
