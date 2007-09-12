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
					
				default:
					die('unknown processqueue type: '.$row['orderType']);
			}

			echo '<a href="show_file_status.php?id='.$row['resourceId'].'"><img src="'.$config['core_web_root'].'gfx/ajax_loading.gif"> Show file status</a>';
			echo $row['timeCreated'].' added by '.nameLink($row['ownerId']).'<br/><br/>';

			$file = $files->getFileInfo($row['resourceId']);
			echo 'fileName: '.$file['fileName'].'<br/>';
			echo 'fileMime: '.$file['fileMime'].'<br/>';
			echo 'fileSize: '.$file['fileSize'].'<br/>';
			echo 'sha1: '.$files->sha1($row['resourceId']);

			echo '</div>';
		}
	} else {
		echo 'Queue is empty';
	}

	require('design_foot.php');
?>