<?
	require_once('config.php');

	require('design_head.php');

	wiki('ProcessShowFiles');

	echo '<input type="checkbox" checked="checked="/> Show all ';
	echo '<input type="checkbox"/> Images ';
	echo '<input type="checkbox"/> Videos ';
	echo '<input type="checkbox"/> Music ';
	echo '<input type="checkbox"/> Documents ';
	echo '<input type="checkbox"/> Other ';

	echo '<h1>Files</h1>';
	$list = $files->getFileList(FILETYPE_PROCESS);

	foreach ($list as $row) {
		echo '<a href="show_file_status.php?id='.$row['fileId'].'">'.$row['fileName'].'</a>';
		echo ', mime='.$row['fileMime'].' uploaded '.$row['timeUploaded'].' by '.Users::link($row['uploaderId']).'<br/>';
	}

	echo '<h1>Clones:</h1>';
	$list = $files->getFileList(FILETYPE_PROCESS_CLONE);
//	d($list);

	foreach ($list as $row) {
		echo '<a href="show_file_status.php?id='.$row['fileId'].'">Orginal file</a>';
		echo ', mime='.$row['fileMime'].' created '.$row['timeUploaded'].' by '.Users::link($row['uploaderId']).'<br/>';
	}


	require('design_foot.php');
?>