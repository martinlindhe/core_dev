<?
/**
 * IMPORTANT: for this to work, the sql user need to have LOCK & UNLOCK privilegies
 *
 * mencoder, ffmpeg (recent svn), ffprobe and imagemagick needs to be available
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
//WARNING: keep this a low number unless you are sure what the consequences are
$config['process']['process_limit'] = 3;

define('PROCESSQUEUE_AUDIO_RECODE', 10);	///< Enqueue this
define('PROCESSQUEUE_VIDEO_RECODE', 11);	///< fixme: use
define('PROCESSQUEUE_IMAGE_RECODE', 12);	///< Enqueue this file for recoding/converting to another image format

define('PROCESS_UPLOAD',			19);	///< HTTP Post upload
define('PROCESS_FETCH',				20);	///< Ask the server to download remote media. Parameter is URL

define('PROCESSPARSE_AND_FETCH',	21);	///< Parse the content of the file for further resources (extract media links from html, or download torrent files from .torrent)
define('PROCESS_CONVERT_TO_DEFAULT',22);	///< Convert media to default format


define('PROCESSMONITOR_SERVER',		30);	///< Monitors server uptime

//event types
define('EVENT_PROCESS',	1);	///< event from the process server


//process order status modes
define('ORDER_NEW',			0);
define('ORDER_EXECUTING',	1);
define('ORDER_COMPLETED',	2);
define('ORDER_FAILED',		3);

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
			case PROCESS_UPLOAD:
				//handle HTTP post file upload. is not enqueued
				//	$param is the $_FILES[idx] array

				$exec_start = microtime(true);	//dont count the actual upload time, just the process time
				$newFileId = $files->handleUpload($param, FILETYPE_PROCESS);

				$files->checksums($newFileId);	//force generation of file checksums

				$exec_time = microtime(true) - $exec_start;
				$q = 'INSERT INTO tblProcessQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',referId='.$newFileId.',orderStatus='.ORDER_COMPLETED.',orderParams="'.$db->escape(serialize($param)).'", timeExec="'.$exec_time.'",timeCompleted=NOW()';
				return $db->insert($q);

			case PROCESSQUEUE_AUDIO_RECODE:
			case PROCESSQUEUE_IMAGE_RECODE:
			case PROCESSQUEUE_VIDEO_RECODE:
				//enque file for recoding.
				//	$param = fileId
				//	$param2 = destination format (by extension)
				if (!is_numeric($param)) die;
				$q = 'INSERT INTO tblProcessQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',referId='.$param.',orderStatus='.ORDER_NEW.',orderParams="'.$db->escape($param2).'"';
				return $db->insert($q);

			case PROCESS_FETCH:
				//enqueue url for download and processing
				//	$param = url
				// downloads media files, torrents & youtube links

				$q = 'INSERT INTO tblProcessQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',referId=0,orderStatus='.ORDER_NEW.',orderParams="'.$db->escape($param).'"';
				return $db->insert($q);

			case PROCESS_CONVERT_TO_DEFAULT:
				if (!is_numeric($param)) return false;
				//convert some media to the default media type, can be used to enqueue a conversion of a PROCESSFETCH before the server
				//has fetched it & cant know the media type
				//  $param = eventId we refer to. from this we can extract the future fileId to process
				//	$param2 = callback URL on process completion (optional)
				$q = 'INSERT INTO tblProcessQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',referId='.$param.',orderStatus='.ORDER_NEW.',orderParams="'.$db->escape($param2).'"';
				return $db->insert($q);

			case PROCESSMONITOR_SERVER:
				//enqueues a server to be monitored
				// $param = serialized params
				$q = 'INSERT INTO tblProcessQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',referId=0,orderStatus='.ORDER_NEW.',orderParams="'.$db->escape($param).'"';
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
	 * FIXME remove??
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
	 * \param $_id entryId or 0 to return the oldest marked as ORDER_NEW
	 */
	function getProcessQueueEntry($_id = 0)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		if ($_id) {
			$q = 'SELECT * FROM tblProcessQueue WHERE entryId='.$_id;
		} else {
			//XXX: this little delay is needed because huge uploaded files (100mb+)
			//     is'nt ready for process yet on test box. even after move_uploaded_file().
			//     hopefully it can be removed later on
			$q = 'SELECT * FROM tblProcessQueue WHERE orderStatus='.ORDER_NEW;
			$q .= ' AND timeCreated <= DATE_SUB(NOW(), INTERVAL 10 SECOND)';
			$q .= ' ORDER BY timeCreated ASC,entryId ASC LIMIT 1';
		}
		return $db->getOneRow($q);
	}

	/**
	 * Returns the oldest work orders still active for processing
	 */
	function getProcessQueue($_limit = 10, $completed = false)
	{
		global $db;
		if (!is_numeric($_limit) || !is_bool($completed)) return false;

		if ($completed) $cnd = 'orderStatus='.ORDER_COMPLETED;
		else $cnd = 'orderStatus='.ORDER_NEW;

		$q = 'SELECT * FROM tblProcessQueue WHERE '.$cnd.' ORDER BY timeCreated ASC, entryId ASC';
		if ($_limit) $q .= ' LIMIT '.$_limit;
		return $db->getArray($q);
	}

	/**
	 * Returns a list of currently enqueued actions to do for referId $_id (can be tblFiles.fileId or tblProcessQueue.eventId)
	 */
	function getQueuedEvents($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT * FROM tblProcessQueue WHERE referId='.$_id.' AND orderStatus='.ORDER_NEW.' ORDER BY timeCreated ASC';
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
		$q = 'SELECT * FROM tblProcessQueue WHERE referId='.$_id.' AND orderType != '.PROCESS_CONVERT_TO_DEFAULT;
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
			if ($row['orderStatus'] == ORDER_COMPLETED) {
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
	 * Returns the number of items in process queue of specified status
	 * Particulary useful to see how many orders are currently being processed (ORDER_EXECUTING)
	 * 
	 * \param $_order_status status code
	 * \return number of entries of specified status
	 */
	function getProcessesQueueStatusCnt($_order_status)
	{
		global $db;
		if (!is_numeric($_order_status)) return false;

		$q = 'SELECT COUNT(*) FROM tblProcessQueue WHERE orderStatus='.$_order_status;
		return $db->getOneItem($q);
	}

	/**
	 * Takes some work orders from the process queue and performs them
	 */
	function processQueue()
	{
		global $config, $files;

		//Only allows a few work orders being executed at once, so we can call this function very often
		if (getProcessesQueueStatusCnt(ORDER_EXECUTING) > $config['process']['process_limit']) {
			echo "TOO MUCH ACTIVE WORK, ABORTING\n";
			return;
		}

		$job = getProcessQueueEntry();
		if (!$job) {
			return;
		}

		//mark current job as "IN PROGRESS" so another process won't start on it aswell
		markQueue($job['entryId'], ORDER_EXECUTING);

		echo "\n\n-------------\n";
		switch ($job['orderType'])
		{
			case PROCESSQUEUE_IMAGE_RECODE:
				echo 'IMAGE RECODE<br/>';
				if (!in_array($job['orderParams'], $files->image_mime_types)) {
					echo 'error: invalid mime type<br/>';
					$session->log('Process queue error - image conversion destination mimetype not supported: '.$job['orderParams'], LOGLEVEL_ERROR);
					break;
				}
				$newId = $files->cloneFile($job['referId'], FILETYPE_CLONE_CONVERTED);

				$exec_start = microtime(true);
				$check = convertImage($files->findUploadPath($job['referId']), $files->findUploadPath($newId), $job['orderParams']);
				$exec_time = microtime(true) - $exec_start;
				echo 'Execution time: '.shortTimePeriod($exec_time).'<br/>';

				if (!$check) {
					$session->log('#'.$job['entryId'].': IMAGE CONVERT failed! format='.$job['orderParams'], LOGLEVEL_ERROR);
					echo 'Error: Image convert failed!<br/>';
					die;
				}

				$files->updateFile($newId, $job['orderParams']);
				markQueueCompleted($job['entryId'], $exec_time);
				break;

			case PROCESSQUEUE_AUDIO_RECODE:
				//Recodes source audio file into orderParams destination format

				$dst_audio_ok = array('ogg', 'wma', 'mp3');	//FIXME: config item or $files->var
				if (!in_array($job['orderParams'], $dst_audio_ok)) {
					echo 'error: invalid mime type<br/>';
					$session->log('Process queue error - audio conversion destination mimetype not supported: '.$job['orderParams'], LOGLEVEL_ERROR);
					break;
				}

				$file = $files->getFileInfo($job['referId']);
				if (!$file) {
					echo 'Error: no fileentry existed for fileId '.$job['referId'];
					break;
				}
				$newId = $files->cloneFile($job['referId'], FILETYPE_CLONE_CONVERTED);

				echo 'Recoding source audio of "'.$file['fileName'].'" ('.$file['fileMime'].') to format '.$job['orderParams']." ...\n";

				switch ($job['orderParams']) {
					case 'application/x-ogg':
						//FIXME hur anger ja dst-format utan filändelse? tvingas göra det i 2 steg nu
						$dst_file = 'tmpfile.ogg';
						$c = 'ffmpeg -i "'.$files->findUploadPath($job['referId']).'" '.$dst_file;
						break;

					case 'audio/x-ms-wma':
						$dst_file = 'tmpfile.wma';
						$c = 'ffmpeg -i "'.$files->findUploadPath($job['referId']).'" '.$dst_file;
						break;

					case 'audio/mpeg':
					case 'audio/x-mpeg':
						//fixme: source & destination should not be able to be the same!
						$dst_file = 'tmpfile.mp3';
						$c = 'ffmpeg -i "'.$files->findUploadPath($job['referId']).'" '.$dst_file;
						break;

					default:
						die('unknown destination audio format: '.$job['orderParams']);
				}

				echo 'Executing: '.$c."\n";
				$exec_time = exectime($c);

				echo 'Execution time: '.shortTimePeriod($exec_time)."\n";

				if (!file_exists($dst_file)) {
					echo '<b>FAILED - dst file '.$dst_file." dont exist!\n";
					continue;
				}

				//renama $dst_file till fileId för nya file entry
				//fixme: behöver inget rename-steg. kan göra det i ett steg!
				rename($dst_file, $files->upload_dir.$newId);

				$files->updateFile($newId);
				markQueueCompleted($job['entryId'], $exec_time);
				break;

			case PROCESSQUEUE_VIDEO_RECODE:
				echo "VIDEO RECODE:\n";

				$exec_start = microtime(true);
				if (convertVideo($job['referId'], $job['orderParams']) === false) {
					markQueue($job['entryId'], ORDER_FAILED);
				} else {
					markQueueCompleted($job['entryId'], microtime(true) - $exec_start);
				}
				break;

			case PROCESS_FETCH:
				echo "FETCH CONTENT\n";

				$fileName = basename($job['orderParams']); //extract filename part of url, used as "filename" in database
				$newFileId = $files->addFileEntry(FILETYPE_PROCESS, 0, 0, $fileName);

				//FIXME: isURL() check
				$c = 'wget '.escapeshellarg($job['orderParams']).' -O '.$files->findUploadPath($newFileId);
				echo "$ ".$c."\n";
				$exec_time = exectime($c);

				//TODO: process html document for media links if it is a html document

				markQueueCompleted($job['entryId'], $exec_time, $newFileId);
				$files->updateFile($newFileId);
				break;

			case PROCESS_CONVERT_TO_DEFAULT:
				echo "CONVERT TO DEFAULT\n";
				//referId is entryId of previous proccess queue order
				$prev_job = getProcessQueueEntry($job['referId']);
				$file = $files->getFileInfo($prev_job['referId']);

				if (in_array($file['fileMime'], $files->video_mime_types)) {
					$exec_start = microtime(true);
					$newId = convertVideo($prev_job['referId'], $files->default_video, ($job['orderParams'] ? false : true));
					if ($newId === false) {
						markQueue($job['entryId'], ORDER_FAILED);
					} else {
						markQueueCompleted($job['entryId'], microtime(true) - $exec_start);

						if ($job['orderParams']) {
							//execute callback
							$uri = $config['core']['full_url'].'api/file.php?id='.$newId;
							$data = file_get_contents($job['orderParams'].'&uri='.urlencode($uri));

							echo "Performing callback: ".$job['orderParams'].'&uri='.urlencode($uri). "\n\n";

							echo "Client callback script returned:\n".$data;

							//delete files after callback processing
							$files->deleteFile($prev_job['referId']);
							$files->deleteFile($newId);
						}
					}
				} else if (in_array($file['fileMime'], $files->audio_mime_types)) {
					die('CONVERT TO MP3!!!!');
				} else {
					echo "UNKNOWN MIME TYPE ".$file['fileMime'].", CANNOT CONVERT MEDIA!!!\n";
					markQueue($job['entryId'], ORDER_FAILED);
				}
				break;

			case PROCESSMONITOR_SERVER:
				echo "MONITOR SERVER\n";
				$d = unserialize($job['orderParams']);
				switch ($d['type']) {
					case 'ping':
						echo 'Pinging '.$d['adr'].' ... TODO<br/>';
						break;
					default:
						die('unknown server type '.$d['type']);
				}
				break;

			default:
				echo "Unknown ordertype: ".$job['orderType']."\n";
				d($job);
				die;
		}
	}

	/**
	 * Converts a video to another video format
	 *
	 * \return file id of the newly converted video, or false on error
	 */
	function convertVideo($fileId, $mime, $thumbs = true)
	{
		global $files, $config;
		if (!is_numeric($fileId)) return false;

		//FIXME dont convert uploaded flashvideo TO flashvideo
		$newId = $files->cloneFile($fileId, FILETYPE_CLONE_CONVERTED);

		switch ($mime) {
			case 'video/x-flv':
				//Flash video. Confirmed working
				$c = 'ffmpeg -i '.$files->findUploadPath($fileId).' -f flv -ac 2 -ar 22050 ';
				if (!empty($config['process']['video_watermark'])) $c .= '-vhook "/usr/lib/vhook/watermark.so -m 1 -f '.$config['process']['video_watermark'].'" ';
				$c .= $files->findUploadPath($newId);
				break;

			case 'video/avi':
				//default profile: mpeg4 video (DivX 3) + mp3 audio. should play on any windows/linux/mac without codecs
				$c = 'mencoder '.$files->findUploadPath($fileId).' -o '.$files->findUploadPath($newId).' -ovc lavc -oac mp3lame -ffourcc DX50 -lavcopts vcodec=msmpeg4';
				die('verify to video/avi');							
				break;

			case 'video/mpeg':
				//mpeg2 video, should be playable anywhere
				$c = 'mencoder '.$files->findUploadPath($fileId).' -o '.$files->findUploadPath($newId).' -ovc lavc -oac mp3lame -lavcopts vcodec=mpeg2video -ofps 25';
				die('verify to video/mpeg');
				break;

			case 'video/x-ms-wmv':
				//Windows Media Video, version 2 (AKA WMV8)
				$c = 'mencoder '.$files->findUploadPath($fileId).' -o '.$files->findUploadPath($newId).' -ovc lavc -oac mp3lame -lavcopts vcodec=wmv2';
				die('verify to video/x-ms-wmv');
				break;

			case 'video/3gpp':
				//3gp video
				die('add to video/3gpp');
				break;

			default:
				die('unknown destination video format: '.$mime);
		}

		echo "$ ".$c."\n";
		exec($c);

		if (!file_exists($files->findUploadPath($newId)) || !filesize($files->findUploadPath($newId))) {
			echo "convertVideo() FAILED - dst file ".$files->findUploadPath($newId)." dont exist!\n";
			$files->deleteFile($newId);
			return false;
		}

		if ($thumbs) {
			generateVideoThumbs($newId);
		}

		$files->updateFile($newId);
		return $newId;
	}

	/**
	 * Marks an object in the process queue as completed
	 *
	 * \param $entryId entry id
	 * \param $exec_time time it took to execute this task
	 * \param $referId optional, specify if we now refer to a file, used when the process event was to fetch a file
	 */
	function markQueueCompleted($entryId, $exec_time, $referId = 0)
	{
		global $db;
		if (!is_numeric($entryId) || !is_float($exec_time) || !is_numeric($referId)) return false;

		$q = 'UPDATE tblProcessQueue SET orderStatus='.ORDER_COMPLETED.',timeCompleted=NOW(),timeExec="'.$exec_time.'"';
		if ($referId) $q .= ',referId='.$referId;
		$q .= ' WHERE entryId='.$entryId;
		$db->update($q);
	}

	/**
	 * Marks an object in the process queue with specified status code
	 *
	 * \param $entryId entry id
	 * \param $_status status code
	 */
	function markQueue($entryId, $_status)
	{
		global $db;
		if (!is_numeric($entryId) || !is_numeric($_status)) return false;

		$q = 'UPDATE tblProcessQueue SET orderStatus='.$_status.' WHERE entryId='.$entryId;
		$db->update($q);
	}

	/**
	 * Generates image thumbnails from specified video file
	 */
	function generateVideoThumbs($fileId)
	{
		global $files;
		if (!is_numeric($fileId)) return false;

		$c = 'ffprobe -show_files '.$files->findUploadPath($fileId).' 2> /dev/null | grep duration | cut -d= -f2';
		//echo "Executing: ".$c."\n";
		$duration = exec($c);

		$pos10 = $duration * 0.10;
		$pos25 = $duration * 0.25;
		$pos50 = $duration * 0.50;
		$pos75 = $duration * 0.75;
		$pos90 = $duration * 0.90;

		$newId = $files->cloneFile($fileId, FILETYPE_CLONE_VIDEOTHUMB10);

		$c = 'ffmpeg -i '.$files->findUploadPath($fileId).' -ss '.$pos10.' -vframes 1 -f image2 '.$files->findUploadPath($newId).' 2> /dev/null';
		echo "$ ".$c."\n";
		exec($c);

		$files->updateFile($newId);
	}

	/**
	 * Used with a client app to execute process server orders through the SOAP interface
	 */
	function process_client_fetchAndConvert($uri, $callback = '')
	{
		global $config;
		ini_set('soap.wsdl_cache_enabled', '0');

		$client = new SoapClient($config['process']['server_soap']); //, array('trace' => 1));

		try {
			$result = $client->fetchAndConvert($uri, $callback);
			return $result;

		} catch (Exception $e) {
			echo 'Exception: '.$e.'<br/><br/>';

			echo 'Request header: '.htmlspecialchars($client->__getLastRequestHeaders()).'<br/>';
			echo 'Request: '.htmlspecialchars($client->__getLastRequest()).'<br/>';
			echo 'Response: '.htmlspecialchars($client->__getLastResponse()).'<br/>';
		}
	}
?>
