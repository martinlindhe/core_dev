<?php

require_once('config.php');
$h->session->requireLoggedIn();

if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('no id');
$eventId = $_GET['id'];

$event = getProcessQueueEntry($eventId);
$fileId = $event['referId'];

require('design_head.php');

$added = false;
if (!empty($_POST['dst_audio_fmt'])) {
	$added = addProcessEvent(PROCESSQUEUE_AUDIO_RECODE, $h->session->id, $fileId, $_POST['dst_audio_fmt']);
} else if (!empty($_POST['dst_image_fmt'])) {
	$added = addProcessEvent(PROCESSQUEUE_IMAGE_RECODE, $h->session->id, $fileId, $_POST['dst_image_fmt']);
} else if (!empty($_POST['dst_video_fmt'])) {
	$added = addProcessEvent(PROCESSQUEUE_VIDEO_RECODE, $h->session->id, $fileId, $_POST['dst_video_fmt']);
} else if (isset($_GET['process'])) {
	$added = addProcessEvent(PROCESSPARSE_AND_FETCH, $h->session->id, $fileId);
} else if (!empty($_POST['unfetched_process']) && $_POST['unfetched_process'] == 'convert') {
	$added = addProcessEvent(PROCESS_CONVERT_TO_DEFAULT, $h->session->id, $eventId);
}

if ($added) {
	echo 'Work order has been enqueued.<br/><br/>';

	echo '<a href="show_file_status.php?id='.$fileId.'">Show file status</a><br/><br/>';
	echo '<a href="show_queue.php">Show active queue</a>';

	require('design_foot.php');
	die;
}

if ($event['orderType'] == PROCESS_FETCH) {
	echo '<h1>convert unfetched media</h1>';

	echo 'The following order has not yet been processed and media type cannot be determined.<br/><br/>';

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$eventId.'">';
	echo 'Select preferred action: ';

	echo '<select name="unfetched_process">';
	echo '<option value="convert">Convert to default media type</option>';
	echo '</select>';

	echo '<input type="submit" value="Continue"/>';
	echo '</form>';
} else {
	showFileInfo($fileId);

	$data = $h->files->getFileInfo($fileId);

	if (in_array($data['fileMime'], $h->files->audio_mime_types)) {
		echo '<h1>convert audio</h1>';

		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$eventId.'">';
		echo 'Select output format: ';

		echo '<select name="dst_audio_fmt">';
		foreach ($h->files->audio_mime_types as $key => $val) {
			echo '<option value="'.$key.'">'.$val.'</option>';
		}
		echo '</select>';

		echo '<input type="submit" value="Continue"/>';
		echo '</form>';
	} else if (in_array($data['fileMime'], $h->files->image_mime_types)) {

		echo '<h1>convert image</h1>';

		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$eventId.'">';
		echo 'Select output format: ';

		echo '<select name="dst_image_fmt">';
		foreach ($h->files->image_mime_types as $key => $val) {
			echo '<option value="'.$key.'">'.$val.'</option>';
		}
		echo '</select>';

		echo '<input type="submit" value="Continue"/>';
		echo '</form><br/>';

		echo 'Image view:<br/>';
		echo makeThumbLink($fileId);

	} else if (in_array($data['fileMime'], $h->files->video_mime_types)) {

		echo '<h1>convert video</h1>';

		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$eventId.'">';
		echo 'Select output format: ';

		echo '<select name="dst_video_fmt">';
		foreach ($h->files->video_mime_types as $key => $val) {
			echo '<option value="'.$key.'">'.$val.'</option>';
		}
		echo '</select>';

		echo '<input type="submit" value="Continue"/>';
		echo '</form><br/>';

	} else if ($data['fileMime'] == 'application/x-bittorrent') {
		//bittorrent download!
		echo '<h1>bittorent download</h1>';

		//todo: only allow this once. if torrent file already has been downloaded show its content instead
		echo 'Download and store the content of this torrent file?<br/><br/>';
		echo '<a href="?id='.$fileId.'&process">Yes</a><br/><br/>';

		echo '<a href="">No</a>';
	} else if ($data['fileMime'] == 'text/html') {
		//extract video links from the html
		echo '<h1>extract videos from html</h1>';

		echo 'todo: show found video links from html and allow user to choose which ones to queue for download';

		$arr = extract_filenames(file_get_contents($h->files->getFileInfo($fileId)));
		d($arr);
	} else {
		echo 'Dont know how to handle mimetype: '.$data['fileMime'];
	}
}

require('design_foot.php');
?>
