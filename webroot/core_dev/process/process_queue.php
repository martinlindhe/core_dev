<?
	//this script is intended to be called regularry. every 30-60 seconds or so
	set_time_limit(60);

	require_once('config.php');

	$list = getProcessQueue($config['process']['process_limit']);

	foreach ($list as $job) {
		d($job);
		switch ($job['orderType'])
		{
			case PROCESSQUEUE_AUDIO_RECODE:
				//Recodes source audio file into orderParams destination format

				$file = $files->getFileInfo($job['fileId']);
				if (!$file) {
					echo 'Error: no fileentry existed for fileId '.$job['fileId'];
					continue;
				}

				//fixme: kolla om filen finns på disk innan vi fortsätter
				echo 'Recoding source audio of "'.$file['fileName'].'" ('.$file['fileMime'].') to format "'.$job['orderParams'].'" ...<br/>';

				switch ($job['orderParams']) {
					case 'ogg':
						//detta borde funka, men det gör det inte. funkar om outifle har extension .ogg. -acodec ogg/vorbis ignoreras
						//$c = 'ffmpeg -i "'.$files->upload_dir.$job['resourceId'].'" outfile  -acodec ogg';

						//så istället tvingas vi göra det i 2 steg:
						$dst_file = 'tmpfile.ogg';
						$dst_mime = 'application/x-ogg';
						$c = 'ffmpeg -i "'.$files->upload_dir.$job['fileId'].'" '.$dst_file;
						break;
					case 'wma':
						$dst_file = 'tmpfile.wma';
						$dst_mime = 'audio/x-ms-wma';
						$c = 'ffmpeg -i "'.$files->upload_dir.$job['fileId'].'" '.$dst_file;
						break;
					case 'mp3':
						//fixme: source & destination should not be able to be the same!
						$dst_file = 'tmpfile.mp3';
						$dst_mime = 'audio/x-mpeg';
						$c = 'ffmpeg -i "'.$files->upload_dir.$job['fileId'].'" '.$dst_file;
						break;
					default:
						die('unknown destination audio format: '.$job['orderParams']);
				}

				$exec_start = microtime(true);
				exec($c);
				$exec_end = microtime(true);
				echo 'Execution time: '.shortTimePeriod($exec_end - $exec_start).'<br/>';

				if (!file_exists($dst_file)) {
					echo '<b>FAILED - dst file '.$dst_file.' dont exist!<br/>';
					continue;
				}

				//skapa nytt tblFiles entry. länka det till orginal-filen
				$newId = $files->cloneEntry($job['fileId']);

				//renama $dst_file till fileId för nya file entry
				rename($dst_file, $files->upload_dir.$newId);

				$size = filesize($files->upload_dir.$newId);
				$q = 'UPDATE tblFiles SET fileMime="'.$dst_mime.'",fileSize='.$size.' WHERE fileId='.$newId.' AND fileType='.FILETYPE_PROCESS_CLONE;
				$db->update($q);
				break;

			case PROCESSQUEUE_VIDEO_RECODE:
				echo 'IMAGE RECODE! todo - implement<br/>';
				die;

			case PROCESSQUEUE_IMAGE_RECODE:
				echo 'IMAGE RECODE! todo - implement<br/>';
				die;

			default:
				echo 'unknown ordertype: '.$job['orderType'].'<br/>';
				die;
		}

		//marks queue item as completed
		$q = 'UPDATE tblProcessQueue SET orderCompleted=1 WHERE entryId='.$job['entryId'];
		$db->update($q);
	}

?>