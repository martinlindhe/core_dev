<?
	//this script is intended to be called regularry. every 30-60 seconds or so
	set_time_limit(60*10);	//10 minute max, for long video recodings

	require_once('config.php');

	$list = getProcessQueue($config['process']['process_limit']);

	foreach ($list as $job) {
		d($job);
		switch ($job['orderType'])
		{
			case PROCESSQUEUE_AUDIO_RECODE:
				//Recodes source audio file into orderParams destination format

				$dst_audio_ok = array('ogg', 'wma', 'mp3');	//fixme: config item or $files->var
				if (!in_array($job['orderParams'], $dst_audio_ok)) {
					echo 'error: invalid mime type<br/>';
					$session->log('Process queue error - audio conversion destination mimetype not supported: '.$job['orderParams'], LOGLEVEL_ERROR);
					break;
				}

				$file = $files->getFileInfo($job['fileId']);
				if (!$file) {
					echo 'Error: no fileentry existed for fileId '.$job['fileId'];
					break;
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

				echo 'Executing: '.$c.'<br/>';
				$exec_time = exectime($c);

				echo 'Execution time: '.shortTimePeriod($exec_time).'<br/>';

				if (!file_exists($dst_file)) {
					echo '<b>FAILED - dst file '.$dst_file.' dont exist!<br/>';
					continue;
				}

				//skapa nytt tblFiles entry. länka det till orginal-filen
				$newId = $files->cloneEntry($job['fileId']);

				//renama $dst_file till fileId för nya file entry
				rename($dst_file, $files->upload_dir.$newId);

				//update cloned entry with new file size and such
				$files->updateClone($newId, $dst_mime);
				break;

			case PROCESSQUEUE_VIDEO_RECODE:
				echo 'VIDEO RECODE<br/>';
				$file = $files->getFileInfo($job['fileId']);
				if (!$file) {
					echo 'Error: no fileentry existed for fileId '.$job['fileId'];
					break;
				}

				//fixme: kolla om filen finns på disk innan vi fortsätter
				echo 'Recoding source video of "'.$file['fileName'].'" ('.$file['fileMime'].') to format "'.$job['orderParams'].'" ...<br/>';

				switch ($job['orderParams']) {
					case 'video/avi':
						//default profile: mpeg4 video (DivX 3) + mp3 audio. should play on any windows/linux/mac without codecs
						$dst_file = 'tmpfile.avi';
						$dst_mime = 'video/avi';
						$c = 'e:/devel/mencoder/mencoder.exe '.$files->upload_dir.$job['fileId'].' -o '.$dst_file.' -ovc lavc -oac mp3lame -ffourcc DX50 -lavcopts vcodec=msmpeg4';
						break;

					case 'video/mpeg':
						//mpeg2 video, should be playable anywhere
						$dst_file = 'tmpfile.mpg';
						$dst_mime = 'video/mpeg';
						$c = 'e:/devel/mencoder/mencoder.exe '.$files->upload_dir.$job['fileId'].' -o '.$dst_file.' -ovc lavc -oac mp3lame -lavcopts vcodec=mpeg2video -ofps 25';
						break;

					default:
						die('unknown destination video format: '.$job['orderParams']);
				}

				echo 'Executing: '.$c.'<br/>';
				$exec_time = exectime($c);
				echo 'Execution time: '.shortTimePeriod($exec_time).'<br/>';
				//todo: store execution time

				if (!file_exists($dst_file)) {
					echo '<b>FAILED - dst file '.$dst_file.' dont exist!<br/>';
					continue;
				}

				//skapa nytt tblFiles entry. länka det till orginal-filen
				$newId = $files->cloneEntry($job['fileId']);

				//renama $dst_file till fileId för nya file entry
				rename($dst_file, $files->upload_dir.$newId);

				//update cloned entry with new file size and such
				$files->updateClone($newId, $dst_mime);
				break;

			case PROCESSQUEUE_IMAGE_RECODE:
				echo 'IMAGE RECODE<br/>';
				if (!in_array($job['orderParams'], $files->image_mime_types)) {
					echo 'error: invalid mime type<br/>';
					$session->log('Process queue error - image conversion destination mimetype not supported: '.$job['orderParams'], LOGLEVEL_ERROR);
					break;
				}
				$newId = $files->cloneEntry($job['fileId']);

				$exec_start = microtime(true);
				$check = $files->convertImage($files->upload_dir.$job['fileId'], $files->upload_dir.$newId, $job['orderParams']);
				$exec_time = microtime(true) - $exec_start;
				echo 'Execution time: '.shortTimePeriod($exec_time).'<br/>';

				if (!$check) {
					$session->log('#'.$job['entryId'].': IMAGE CONVERT failed! format='.$job['orderParams'], LOGLEVEL_ERROR);
					echo 'Error: Image convert failed!<br/>';
					die;
				}

				//update cloned entry with new file size and such
				$files->updateClone($newId, $job['orderParams']);
				break;

			case PROCESSFETCH_FORM:
				echo 'FETCH RESOURCE FROM URL:<br/>';
				echo $job['orderParams'].'<br/>';

				$fileName = basename($job['orderParams']); //extract filename part of url
				echo 'Using filename '.$fileName.'<br/>';
				
				$exec_start = microtime(true); //count download time
				//fixme: isURL() check
				$data = file_get_contents($job['orderParams']);
				
				//echo $data;
	
				$fileMime = 'xxx';	//fixme! parse server headers from the HTTP GET
				echo 'Using mimetype '.$fileMime.'<br/>';
				
				$exec_time = microtime(true) - $time_start;
				$newFileId = $files->addFileEntry(FILETYPE_PROCESS, 0, $session->id, $fileName, $fileMime, $data);
				break;

			default:
				echo 'unknown ordertype: '.$job['orderType'].'<br/>';
				die;
		}

		//marks queue item as completed
		$q = 'UPDATE tblProcessQueue SET orderCompleted=1,timeCompleted=NOW(),timeExec="'.$exec_time.'" WHERE entryId='.$job['entryId'];
		$db->update($q);
	}
	
	//include('design_head.php'); $db->showProfile();

?>