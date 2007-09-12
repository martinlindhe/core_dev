<?
	require_once('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('no id');
	$fileId = $_GET['id'];

	require('design_head.php');

	if (!empty($_POST['dst_audio_fmt'])) {

		processEvent(PROCESSQUEUE_AUDIO_RECODE, $fileId, $_POST['dst_audio_fmt']);

		echo 'Work order has been enqueued.<br/><br/>';
		echo '<a href="show_file_status.php?id='.$fileId.'">Show file status</a><br/><br/>';
		echo '<a href="show_queue.php">Show active queue</a>';

		require('design_foot.php');
		die;
	}

	$dst_audio = array(
		'ogg' => 'OGG audio',
		'wma' => 'WMA audio',
		'mp3' => 'mp3 audio'
	);

	wiki('ProcessFile');

	$files->showFileInfo($fileId);

	$data = $files->getFileInfo($fileId);

	if (in_array($data['fileMime'], $files->audio_mime_types)) {
		echo '<h1>convert audio</h1>';

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