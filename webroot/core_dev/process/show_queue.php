<?
	require_once('config.php');

	require('design_head.php');

	wiki('ProcessShowQueue');

	$list = getProcessQueue(50);
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

				case PROCESSFETCH_FORM:
					echo 'Fetch remote media from <b>'.$row['orderParams'].'</b><br/>';
					break;

				default:
					die('unknown processqueue type: '.$row['orderType']);
			}

			if ($row['fileId']) {
				echo '<a href="show_file_status.php?id='.$row['fileId'].'"><img src="'.$config['core_web_root'].'gfx/ajax_loading.gif"> Show file status</a>';
			}
			echo $row['timeCreated'].' added by '.nameLink($row['ownerId']).'<br/><br/>';

			$file = $files->getFileInfo($row['fileId']);
			if ($file) {
				echo 'fileName: '.$file['fileName'].'<br/>';
				echo 'fileMime: '.$file['fileMime'].'<br/>';
				echo 'fileSize: '.$file['fileSize'].'<br/>';
				echo 'sha1: '.$files->sha1($row['fileId']);
			}

			echo '</div>';
		}
	} else {
		echo 'Queue is empty';
	}

	require('design_foot.php');
?>