<?
/**
 * IMPORTANT: for this to work, the sql user need to have LOCK & UNLOCK privilegies
 *
 * mencoder, ffmpeg (recent svn) and imagemagick needs to be available
 *
 * uses MySQL table locking to ensure atomic operation
 *
 * this module requires:
 * - php_soap.dll extension (included in windows php dist, disabled by default)
 * - allow_url_fopen = On
 * - always_populate_raw_post_data = On (required by php_soap.dll)
 *
 * suggested config:
 * soap.wsdl_cache_enabled=1
 * soap.wsdl_cache_ttl=172800
 */

require_once('functions_image.php');
require_once('functions_fileareas.php');


//how many enqued items to process at max each time the process_queue.php script is called
//WARNING: keep this a low number unless you are sure what the consequences are (between 5 and 10 is fine)
$config['process']['process_limit'] = 5;

$config['process']['default']['video'] = 'video/x-flv';		//convert video to flv
$config['process']['default']['audio'] = 'audio/x-mpeg';	//convert audio to mp3


/*
	NOTE: we exploit the tblFiles.categoryId to store the type of upload PROCESSUPLOAD_*
		this is hardcoded here, no sophisticated dynamic solution should be needed

	these are "event classes"
*/
define('PROCESSUPLOAD_FORM',	1);	//Upload thru HTTP POST form
define('PROCESSUPLOAD_SOAP',	2);	//fixme: use
define('PROCESSUPLOAD_GET',		3);	//fixme: use
define('PROCESSQUEUE_AUDIO_RECODE', 10);	//Enqueue this
define('PROCESSQUEUE_VIDEO_RECODE', 11);	//fixme: use
define('PROCESSQUEUE_IMAGE_RECODE', 12);	//Enqueue this file for recoding/converting to another image format

define('PROCESSFETCH',							20);	//Ask the server to download remote media. Parameter is URL
define('PROCESSPARSE_AND_FETCH',		21);	//Parse the content of the file for further resources (extract media links from html, or download torrent files from .torrent)
define('PROCESS_CONVERT_TO_DEFAULT',22);	//Convert media to default format


define('PROCESSMONITOR_SERVER',			30);	//Monitors server uptime

//event types
define('EVENT_PROCESS',	1);	//event from the process server


	/**
	 * Adds something to the process queue
	 *
	 * \return process event id
	 */
	function addProcessEvent($_type, $param, $param2 = '')
	{
		global $db, $session, $files;
		if (!is_numeric($_type)) return false;

		switch ($_type) {
			case PROCESSUPLOAD_FORM:
				//handle HTTP post file upload. is not enqueued
				//	$param is the $_FILES[idx] array

				$exec_start = microtime(true);	//dont count the actual upload time, just the process time
				$newFileId = $files->handleUpload($param, FILETYPE_PROCESS, 0, PROCESSUPLOAD_FORM);

				/*
				$q = 'INSERT INTO tblEvents SET eventType='.EVENT_PROCESS.',eventClass='.$_type.',param="'.$newFileId.'",createdBy='.$session->id.',timeCreated=NOW()';
				$db->insert($q);
				*/

				$files->checksums($newFileId);	//force generation of file checksums

				$exec_time = microtime(true) - $exec_start;
				$q = 'INSERT INTO tblProcessQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',fileId='.$newFileId.',orderCompleted=1,orderParams="'.$db->escape(serialize($param)).'", timeExec="'.$exec_time.'",timeCompleted=NOW()';
				return $db->insert($q);

			case PROCESSQUEUE_AUDIO_RECODE:
			case PROCESSQUEUE_IMAGE_RECODE:
			case PROCESSQUEUE_VIDEO_RECODE:
				//enque file for recoding.
				//	$param = fileId
				//	$param2 = destination format (by extension)
				if (!is_numeric($param)) die;
				$q = 'INSERT INTO tblProcessQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',fileId='.$param.',orderCompleted=0,orderParams="'.$db->escape($param2).'"';
				return $db->insert($q);

			case PROCESSFETCH:
				//enqueue url for download and processing
				//	$param = url
				// downloads media files, torrents & youtube links

				$q = 'INSERT INTO tblProcessQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',fileId=0,orderCompleted=0,orderParams="'.$db->escape($param).'"';
				return $db->insert($q);

			case PROCESS_CONVERT_TO_DEFAULT:
				//convert some media to the default media type, can be used to enqueue a conversion of a PROCESSFETCH before the server
				//has fetched it & cant know the media type
				//  $param = eventId we refer to. from this we can extract the future fileId to process
				$q = 'INSERT INTO tblProcessQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',fileId=0,orderCompleted=0,orderParams="'.$db->escape($param).'"';
				return $db->insert($q);

			case PROCESSMONITOR_SERVER:
				//enqueues a server to be monitored
				// $param = serialized params
				$q = 'INSERT INTO tblProcessQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',fileId=0,orderCompleted=0,orderParams="'.$db->escape($param).'"';
				return $db->insert($q);

			case PROCESSPARSE_AND_FETCH:
				//parse this resource for further media resources and fetches them
				// $param = fileId
				// use to process a uploaded .torrent file & download it's content
				// or to process a webpage and extract video files from it (including youtube) and download them to the server
				die('not implemented PROCESSPARSE_AND_FETCH');
				break;

			default:
				die('unknown processqueue type');
				return false;
		}
	}

	/**
	 *
	 */
	function getEvents()
	{
		global $db;

		$q = 'SELECT * FROM tblEvents ORDER BY timeCreated DESC';

		return $db->getArray($q);
	}

	/**
	 * Returns a process queue entry
	 *
	 */
	function getProcessQueueEntry($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT * FROM tblProcessQueue WHERE entryId='.$_id;
		return $db->getOneRow($q);
	}


	/**
	 * Returns the oldest work orders still active for processing
	 */
	function getProcessQueue($_limit = 10, $completed = false)
	{
		global $db;
		if (!is_numeric($_limit) || !is_bool($completed)) return false;

		if ($completed) $cnd = 'orderCompleted=1';
		else $cnd = 'orderCompleted=0';

		$q = 'SELECT * FROM tblProcessQueue WHERE '.$cnd.' ORDER BY timeCreated ASC';
		if ($_limit) $q .= ' LIMIT '.$_limit;
		return $db->getArray($q);
	}

	/**
	 * Returns a list of currently enqueued actions to do for fileId $_id
	 */
	function getQueuedEvents($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT * FROM tblProcessQueue WHERE fileId='.$_id.' AND orderCompleted=0 ORDER BY timeCreated ASC';
		return $db->getArray($q);
	}

	/**
	 * Displays the enqueued actions in the process queue for the fileId $_id
	 */
	function showFileQueueStatus($_id)
	{
		global $db, $files;
		if (!is_numeric($_id)) return false;

		$data = $files->getFileInfo($_id);
		if (!$data) {
			echo '<h1>File dont exist</h1>';
			return;
		}

		$list = getQueuedEvents($_id);

		if (!empty($list)) {
			echo '<h1>'.count($list).' queued actions</h1>';
			foreach ($list as $row) {
				echo '<h3>Was enqueued '.ago($row['timeCreated']).' by '.Users::link($row['creatorId']);
				echo ' type='.$row['orderType'].', params='.$row['orderParams'];
				echo '</h3>';
			}
		} else {
			echo '<h1>No queued action</h1>';
		}

		echo 'Process log:<br/>';
		$q = 'SELECT * FROM tblProcessQueue WHERE fileId='.$_id;
		$list = $db->getArray($q);
		echo '<table border="1">';
		echo '<tr>';
		echo '<th>Added</th>';
		echo '<th>Completed</th>';
		echo '<th>Exec time</th>';
		echo '<th>Type</th>';
		echo '<th>Created by</th>';
		echo '</tr>';
		foreach ($list as $row) {
			echo '<tr>';
			echo '<td>'.$row['timeCreated'].'</td>';
			if ($row['orderCompleted']) {
				echo '<td>'.$row['timeCompleted'].'</td>';
				echo '<td>'.round($row['timeExec'], 3).'s</td>';
			} else {
				echo '<td>not done</td>';
				echo '<td>?</td>';
			}
			echo '<td>'.$row['orderType'].'</td>';
			echo '<td>'.Users::link($row['creatorId']).'</td>';
			//echo $row['orderParams'];
			echo '</tr>';
		}
		echo '</table>';

		showFileInfo($_id);
	}

	/**
	 *
	 */
	function processQueue()
	{
		global $config, $files;

		$list = getProcessQueue($config['process']['process_limit']);

		foreach ($list as $job) {
			d($job);
			switch ($job['orderType'])
			{
				case PROCESSQUEUE_AUDIO_RECODE:
					//Recodes source audio file into orderParams destination format

					$dst_audio_ok = array('ogg', 'wma', 'mp3');	//FIXME: config item or $files->var
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

					$newId = $files->cloneFile($job['fileId'], FILETYPE_CLONE_CONVERTED);

					echo 'Recoding source audio of "'.$file['fileName'].'" ('.$file['fileMime'].') to format "'.$job['orderParams'].'" ...<br/>';

					switch ($job['orderParams']) {
						case 'application/x-ogg':
							//FIXME hur anger ja dst-format utan filändelse? tvingas göra det i 2 steg nu
							$dst_file = 'tmpfile.ogg';
							$c = 'ffmpeg -i "'.$files->findUploadPath($job['fileId']).'" '.$dst_file;
							break;

						case 'audio/x-ms-wma':
							$dst_file = 'tmpfile.wma';
							$c = 'ffmpeg -i "'.$files->findUploadPath($job['fileId']).'" '.$dst_file;
							break;

						case 'audio/mpeg':
						case 'audio/x-mpeg':
							//fixme: source & destination should not be able to be the same!
							$dst_file = 'tmpfile.mp3';
							$c = 'ffmpeg -i "'.$files->findUploadPath($job['fileId']).'" '.$dst_file;
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

					//renama $dst_file till fileId för nya file entry
					//fixme: behöver inget rename-steg. kan göra det i ett steg!
					rename($dst_file, $files->upload_dir.$newId);

					$files->updateFile($newId);
					markQueueCompleted($job['entryId'], $exec_time);
					break;

				case PROCESSQUEUE_VIDEO_RECODE:
					echo 'VIDEO RECODE<br/>';
					$file = $files->getFileInfo($job['fileId']);
					if (!$file) {
						echo 'Error: no fileentry existed for fileId '.$job['fileId'];
						break;
					}

					echo 'Recoding source video of "'.$file['fileName'].'" ('.$file['fileMime'].') to format "'.$job['orderParams'].'" ...<br/>';

					$newId = $files->cloneFile($job['fileId'], FILETYPE_CLONE_CONVERTED);

					switch ($job['orderParams']) {
						case 'video/x-flv':
							//Flash video. Confirmed working
							$c = 'ffmpeg -i '.$files->findUploadPath($job['fileId']).' -f flv -ar 22050 '.$files->findUploadPath($newId);
							break;

						case 'video/avi':
							//default profile: mpeg4 video (DivX 3) + mp3 audio. should play on any windows/linux/mac without codecs
							$c = 'mencoder '.$files->findUploadPath($job['fileId']).' -o '.$files->findUploadPath($newId).' -ovc lavc -oac mp3lame -ffourcc DX50 -lavcopts vcodec=msmpeg4';
							die('verify to video/avi');							
							break;

						case 'video/mpeg':
							//mpeg2 video, should be playable anywhere
							$c = 'mencoder '.$files->findUploadPath($job['fileId']).' -o '.$files->findUploadPath($newId).' -ovc lavc -oac mp3lame -lavcopts vcodec=mpeg2video -ofps 25';
							die('verify to video/mpeg');
							break;

						case 'video/x-ms-wmv':
							//Windows Media Video, version 2 (AKA WMV8)
							$c = 'mencoder '.$files->findUploadPath($job['fileId']).' -o '.$files->findUploadPath($newId).' -ovc lavc -oac mp3lame -lavcopts vcodec=wmv2';
							die('verify to video/x-ms-wmv');
							break;

						case 'video/3gpp':
							//3gp video
							die('add to video/3gpp');
							break;

						default:
							die('unknown destination video format: '.$job['orderParams']);
					}

					echo 'Executing: '.$c.'<br/>';
					$exec_time = exectime($c);
					echo 'Execution time: '.shortTimePeriod($exec_time).'<br/>';

					if (!file_exists($files->findUploadPath($newId))) {
						echo '<b>FAILED - dst file '.$files->findUploadPath($newId).' dont exist!<br/>';
						continue;
					}

					$files->updateFile($newId);
					markQueueCompleted($job['entryId'], $exec_time);
					break;

				case PROCESSQUEUE_IMAGE_RECODE:
					echo 'IMAGE RECODE<br/>';
					if (!in_array($job['orderParams'], $files->image_mime_types)) {
						echo 'error: invalid mime type<br/>';
						$session->log('Process queue error - image conversion destination mimetype not supported: '.$job['orderParams'], LOGLEVEL_ERROR);
						break;
					}
					$newId = $files->cloneFile($job['fileId'], FILETYPE_CLONE_CONVERTED);

					$exec_start = microtime(true);
					$check = convertImage($files->findUploadPath($job['fileId']), $files->findUploadPath($newId), $job['orderParams']);
					$exec_time = microtime(true) - $exec_start;
					echo 'Execution time: '.shortTimePeriod($exec_time).'<br/>';

					if (!$check) {
						$session->log('#'.$job['entryId'].': IMAGE CONVERT failed! format='.$job['orderParams'], LOGLEVEL_ERROR);
						echo 'Error: Image convert failed!<br/>';
						die;
					}

					//update cloned entry with new file size and such
					$files->updateFile($newId, $job['orderParams']);
					markQueueCompleted($job['entryId'], $exec_time);
					break;

				case PROCESSFETCH:
					echo 'FETCH CONTENT FROM '.$job['orderParams'].'<br/>';

					$fileName = basename($job['orderParams']); //extract filename part of url, used as "filename" in database

					//$exec_start = microtime(true); //count download time
					//$data = file_get_contents($job['orderParams']);

					$newFileId = $files->addFileEntry(FILETYPE_PROCESS, 0, 0, $fileName);

					//fixme: isURL() check
					$c = 'wget '.$job['orderParams'].' -O '.$files->findUploadPath($newFileId);
					echo 'Executing: '.$c.'<br/>';
					$exec_time = exectime($c);
					//$exec_time = microtime(true) - $time_start;

					//todo: process html document for media links if it is a html document

					markQueueCompleted($job['entryId'], $exec_time, $newFileId);
					$files->updateFile($newFileId);
					break;

				case PROCESSMONITOR_SERVER:
					echo 'MONITOR SERVER<br/>';
					$d = unserialize($job['orderParams']);
					switch ($d['type']) {
						case 'ping':
							echo 'Pinging '.$d['adr'].' ... TODO<br/>';
							break;
						default:
							die('unknown server type '.$d['type']);
					}
					break;

				case PROCESS_CONVERT_TO_DEFAULT:
					echo 'CONVERT TO DEFAULT<br/>';
					//$param is entryId of previous proccess queue order, fetch fileId from it
					$prev_job = getProcessQueueEntry($job['orderParams']);

					$file = $files->getFileInfo($prev_job['fileId']);
d($file);
					if (in_array($file['fileMime'], $files->video_mime_types)) {

						$newId = $files->cloneFile($file['fileId'], FILETYPE_CLONE_CONVERTED);
						$c = 'ffmpeg -i '.$files->findUploadPath($file['fileId']).' -f flv -ar 22050 '.$files->findUploadPath($newId);

						echo 'Executing: '.$c.'<br/>';
						$exec_time = exectime($c);
						echo 'Execution time: '.shortTimePeriod($exec_time).'<br/>';

						if (!file_exists($files->findUploadPath($newId))) {
							echo '<b>FAILED - dst file '.$files->findUploadPath($newId).' dont exist!<br/>';
							break;
						}

						//update cloned entry with new file size and such
						$files->updateFile($newId);
						markQueueCompleted($job['entryId'], $exec_time);

					} else if (in_array($file['fileMime'], $files->audio_mime_types)) {
						die('CONVERT TO MP3!!!!');
					} else {
						echo 'CANNOT CONVERT MEDIA!!!';
					}
					break;

				default:
					echo 'unknown ordertype: '.$job['orderType'].'<br/>';
					die;
			}

		}
	}

	/**
	 * Marks an object in the process queue as completed
	 *
	 * \param $entryId entry id
	 * \param $exec_time time it took to execute this task
	 * \param $fileId optional, specify if we now refer to a file, used when the process event was to fetch a file
	 */
	function markQueueCompleted($entryId, $exec_time, $fileId = 0)
	{
		global $db;
		if (!is_numeric($entryId) || !is_float($exec_time)) return false;

		$q = 'UPDATE tblProcessQueue SET orderCompleted=1,timeCompleted=NOW(),timeExec="'.$exec_time.'"';
		if ($fileId) $q .= ',fileId='.$fileId;
		$q .= ' WHERE entryId='.$entryId;
		$db->update($q);
	}
?>