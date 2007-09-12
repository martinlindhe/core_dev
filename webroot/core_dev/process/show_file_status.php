<?
	require_once('config.php');

	//todo: ability to force recalculation of checksums. verify that the file is on disk

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('no id');
	$fileId = $_GET['id'];

	require('design_head.php');

	wiki('ProcessShowfileStatus');

	$files->showFileInfo($fileId);

	$data = $files->getFileInfo($fileId);

	if (in_array($data['fileMime'], $files->audio_mime_types)) {
		echo '<h1>convert audio</h1>';
		echo 'Input format: xxx<br/>';

		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$fileId.'">';
		echo 'Select output format: ';

		echo '<select name="dst_audio_fmt">';
		foreach ($dst_audio as $key => $val) {
			echo '<option value="'.$key.'">'.$val.'</option>';
		}
		echo '</select>';

		echo '<input type="submit" value="Continue"/>';
		echo '</form>';
	} else {
		echo 'Dont know how to handle mimetype: '.$data['fileMime'];
	}
	require('design_foot.php');
?>