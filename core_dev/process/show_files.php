<?
	require_once('config.php');
	$session->requireLoggedIn();

	require('design_head.php');

	wiki('ProcessShowFiles');

	echo '<input type="checkbox" checked="checked="/> Show all ';
	echo '<input type="checkbox"/> Images ';
	echo '<input type="checkbox"/> Videos ';
	echo '<input type="checkbox"/> Music ';
	echo '<input type="checkbox"/> Documents ';
	echo '<input type="checkbox"/> Other ';

	echo '<h1>Files</h1>';
/*
	$list = $files->getFileList(FILETYPE_PROCESS);

	foreach ($list as $row) {
		echo '<a href="show_file_status.php?id='.$row['fileId'].'">'.$row['fileName'].'</a>';
		echo ', mime='.$row['fileMime'].' uploaded '.$row['timeUploaded'].' by '.Users::link($row['uploaderId']).'<br/>';
	}
*/
	
	showFiles(FILETYPE_PROCESS);

	echo '<h1>Converted files:</h1>';
/*
	$list = $files->getFileList(FILETYPE_CLONE_CONVERTED);
//	d($list);

	foreach ($list as $row) {
		echo '<a href="show_file_status.php?id='.$row['fileId'].'">Details</a>';
		echo ', mime='.$row['fileMime'].' created '.$row['timeUploaded'].' by '.Users::link($row['uploaderId']).'<br/>';
	}
*/

	showFiles(FILETYPE_CLONE_CONVERTED);

	require('design_foot.php');
?>