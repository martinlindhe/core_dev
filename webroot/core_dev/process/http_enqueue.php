<?
	require_once('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('no id');
	$fileId = $_GET['id'];

	require('design_head.php');

	$added = false;
	if (!empty($_POST['dst_audio_fmt'])) {
		$added = processEvent(PROCESSQUEUE_AUDIO_RECODE, $fileId, $_POST['dst_audio_fmt']);
	} else if (!empty($_POST['dst_image_fmt'])) {
		$added = processEvent(PROCESSQUEUE_IMAGE_RECODE, $fileId, $_POST['dst_image_fmt']);
	} else if (!empty($_POST['dst_video_fmt'])) {
		$added = processEvent(PROCESSQUEUE_VIDEO_RECODE, $fileId, $_POST['dst_video_fmt']);
	}

	if ($added) {
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

	$dst_image = array(
		'image/png' => 'PNG image',
		'image/jpeg' => 'JPEG image',
		'image/gif' => 'GIF image'
	);

	$dst_video = array(
		'video/mpeg'			=>	'.mpg file',
		'video/avi'				=>	'.avi xvid file',
		'video/x-ms-wmv'	=>	'.wmv Windows Media Video',
		'video/3gpp'			=>	'.3gp video file'
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
	} else if (in_array($data['fileMime'], $files->image_mime_types)) {

		echo '<h1>convert image</h1>';

		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$fileId.'">';
		echo 'Select output format: ';

		echo '<select name="dst_image_fmt">';
		foreach ($dst_image as $key => $val) {
			echo '<option value="'.$key.'">'.$val.'</option>';
		}
		echo '</select>';

		echo '<input type="submit" value="Continue"/>';
		echo '</form><br/>';

		echo 'Image view:<br/>';
		echo makeThumbLink($fileId);

	} else if (in_array($data['fileMime'], $files->video_mime_types)) {

		echo '<h1>convert video</h1>';

		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$fileId.'">';
		echo 'Select output format: ';

		echo '<select name="dst_video_fmt">';
		foreach ($dst_video as $key => $val) {
			echo '<option value="'.$key.'">'.$val.'</option>';
		}
		echo '</select>';

		echo '<input type="submit" value="Continue"/>';
		echo '</form><br/>';

	} else {
		echo 'Dont know how to handle mimetype: '.$data['fileMime'];
	}

	require('design_foot.php');
?>