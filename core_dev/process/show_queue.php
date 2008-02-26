<?
	require_once('config.php');
	$session->requireLoggedIn();

	require('design_head.php');

	wiki('ProcessShowQueue');

	$list = getProcessQueue(50, isset($_GET['completed']));
	if (!empty($list)) {
		foreach ($list as $row) {
			echo '<div class="item">';

			switch ($row['orderType']) {
				case PROCESSQUEUE_AUDIO_RECODE:
					echo 'Audio recode to <b>"'.$row['orderParams'].'"</b><br/>';
					break;
					
				case PROCESSQUEUE_IMAGE_RECODE:
					echo 'Image recode to <b>"'.$row['orderParams'].'"</b><br/>';
					break;

				case PROCESSQUEUE_VIDEO_RECODE:
					echo 'Video recode to <b>"'.$row['orderParams'].'"</b><br/>';
					break;

				case PROCESS_FETCH:
					echo 'Fetch remote media from <b>'.$row['orderParams'].'</b><br/>';
					break;

				case PROCESSMONITOR_SERVER:
					$d = unserialize($row['orderParams']);
					echo 'Monitor remote server <b>'.$d['adr'].'</b> for '.$d['type'].' uptime<br/>';
					break;

				case PROCESS_CONVERT_TO_DEFAULT:
					echo 'Convert media to default type<br/>';
					break;

				default:
					die('unknown processqueue type: '.$row['orderType']);
			}

			if ($row['referId']) {
				echo '<a href="show_file_status.php?id='.$row['referId'].'"><img src="'.$config['core_web_root'].'gfx/ajax_loading.gif"> Show file status</a>';
			}
			echo $row['timeCreated'].' added by '.Users::link($row['creatorId']).'<br/><br/>';

			$file = $files->getFileInfo($row['referId']);
			if ($file) {
				echo '<b>Source file:</b><br/>';
				echo $file['fileName'].' ('.$file['fileMime'].')<br/>';
				echo 'size: '.formatDataSize($file['fileSize']).'<br/>';
				echo 'sha1: '.$files->sha1($row['referId']).'<br/>';
			}

			if ($row['orderStatus'] == ORDER_COMPLETED) {
				echo '<b>Order completed</b><br/>';
				echo 'Exec time: '.$row['timeExec'].'<br/>';
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