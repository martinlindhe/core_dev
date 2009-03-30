<?php

require_once('config.php');
$h->session->requireLoggedIn();

require('design_head.php');

//FIXME show failed & in progress aswell
$tot_cnt = getProcessQueueCount(0, isset($_GET['completed']) ? ORDER_COMPLETED : ORDER_NEW);

$pager = makePager($tot_cnt, 10);
echo $pager['head'];

$list = getProcessQueue(0, $pager['limit'], isset($_GET['completed']) ? ORDER_COMPLETED : ORDER_NEW);

if (!empty($list)) {
	foreach ($list as $row) {
		echo '<div class="item">';
		echo '<h2>#'.$row['entryId'].': ';

		switch ($row['orderType']) {
			case PROCESSQUEUE_AUDIO_RECODE:
				echo 'Audio recode to <b>"'.$row['orderParams'].'"</b></h2>';
				break;

			case PROCESSQUEUE_IMAGE_RECODE:
				echo 'Image recode to <b>"'.$row['orderParams'].'"</b></h2>';
				break;

			case PROCESSQUEUE_VIDEO_RECODE:
				echo 'Video recode to <b>"'.$row['orderParams'].'"</b></h2>';
				break;

			case PROCESS_FETCH:
				echo 'Fetch remote media</h2>';
				echo 'from <b>'.$row['orderParams'].'</b><br/>';
				break;

			case PROCESS_UPLOAD:
				echo 'Uploaded remote media from client</h2>';
				break;

			case PROCESS_CONVERT_TO_DEFAULT:
				echo 'Convert media to default type for entry <b>#'.$row['referId'].'</b></h2>';
				if ($row['orderParams']) {
					$params = unserialize($row['orderParams']);
					if (!empty($params['callback']))  echo 'Callback: <b>'. urldecode($params['callback']). '</b><br/><br/>';
					if (!empty($params['watermark'])) echo 'Watermark: <b>'.urldecode($params['watermark']).'</b><br/><br/>';
				}
				if ($row['callback_log']) {
					echo 'Callback returned:<br/>';
					echo '<b>'.$row['callback_log'].'</b><br/><br/>';
				}
				break;

			default:
				die('unknown processqueue type: '.$row['orderType']);
		}
		echo $row['timeCreated'].' added by '.getCustomerName($row['creatorId']).'<br/><br/>';
		echo 'Attempts: '.$row['attempts'].'<br/><br/>';

		if ($row['orderType'] != PROCESS_CONVERT_TO_DEFAULT) {
			if ($row['referId']) {
				echo '<a href="show_file_status.php?id='.$row['referId'].'">Show file status</a><br/>';
			}

			$file = $h->files->getFileInfo($row['referId']);
			if ($file) {
				echo '<h3>Source file:</h3>';
				echo 'Mime: '.$file['fileMime'].'<br/>';
				echo 'Size: '.formatDataSize($file['fileSize']).'<br/>';
				echo 'SHA1: '.$h->files->sha1($row['referId']).'<br/>';
			}
		}

		if ($row['orderStatus'] == ORDER_COMPLETED) {
			echo '<b>Order completed</b><br/>';
			echo 'Exec time: '.round($row['timeExec'], 3).'s<br/>';
		}

		echo '</div>';
	}
} else {
	echo 'Queue is empty.<br/>';
}

if (!isset($_GET['completed'])) {
	echo '<a href="?completed">Show completed queue items</a>';
} else {
	echo '<a href="?">Show pending queue items</a>';
}

require('design_foot.php');
?>
