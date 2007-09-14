<?
	/*
		IMPORTANT: for this to work, the sql user need to have LOCK & UNLOCK privilegies
		
		for image conversions I am using: ImageMagick-6.3.4-0-Q16-windows-dll.exe
				in ubuntu: sudo apt-get install imagemagick  (v6.2.4 in ubuntu 7.4)

				uses MySQL table locking to ensure atomic operation

		configuration:
			this module requires:
			- php_soap.dll extension (included in windows php dist, disabled by default)
			- allow_url_fopen = On
			- always_populate_raw_post_data = On (required by php_soap.dll)

			suggested config:
			soap.wsdl_cache_enabled=1
			soap.wsdl_cache_ttl=172800
	*/


	//how many enqued items to process at max each time the process_queue.php script is called
	//WARNING: keep this a low number unless you are sure what the consequences are (between 5 and 10 is fine)
	$config['process']['process_limit'] = 5;




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

	define('PROCESSFETCH_FORM',					20);	//Ask the server to download remote media. Parameter is URL

	define('PROCESSPARSE_AND_FETCH',		21);	//Parse the content of the file for further resources (extract media links from html, or download torrent files from .torrent)

	//event types
	define('EVENT_PROCESS',	1);	//event from the process server

	function processEvent($_type, $param, $param2 = '')
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
				$db->insert($q);

				return $newFileId;

			case PROCESSQUEUE_AUDIO_RECODE:
			case PROCESSQUEUE_IMAGE_RECODE:
			case PROCESSQUEUE_VIDEO_RECODE:
				//enque file for recoding.
				//	$param = fileId
				//	$param2 = destination format (by extension)
				//fixme: kolla om dest format finns i $dst_audio
				if (!is_numeric($param)) die;

				/*
				$q = 'INSERT INTO tblEvents SET eventType='.EVENT_PROCESS.',eventClass='.$_type.',param="'.$db->escape($param.'_'.$param2).'",createdBy='.$session->id.',timeCreated=NOW()';
				$db->insert($q);
				*/

				$q = 'INSERT INTO tblProcessQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',fileId='.$param.',orderCompleted=0,orderParams="'.$db->escape($param2).'"';
				$db->insert($q);
				break;

			case PROCESSFETCH_FORM:
				//enqueue url for download and processing
				//	$param = url
				// downloads media files, torrents & youtube links

				$q = 'INSERT INTO tblProcessQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',fileId=0,orderCompleted=0,orderParams="'.$db->escape($param).'"';
				$db->insert($q);
				break;

			case PROCESSPARSE_AND_FETCH:
				//parse this resource for further media resources and fetches them
				// $param = fileId
				// use to process a uploaded .torrent file & download it's content
				// or to process a webpage and extract video files from it (including youtube) and download them to the server

			default: die('processEvent unknown type');
		}

		return true;
	}

	function getEvents()
	{
		global $db;

		$q = 'SELECT * FROM tblEvents ORDER BY timeCreated DESC';

		return $db->getArray($q);
	}

	/* Returns the oldest work orders still active for processing */
	function getProcessQueue($_limit = 10)
	{
		global $db;
		if (!is_numeric($_limit)) return false;

		$q = 'SELECT * FROM tblProcessQueue WHERE orderCompleted=0 ORDER BY timeCreated ASC';
		if ($_limit) $q .= ' LIMIT '.$_limit;
		return $db->getArray($q);
	}

	/* Returns a list of currently enqueued actions to do for fileId $_id */
	function getQueuedEvents($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT * FROM tblProcessQueue WHERE fileId='.$_id.' AND orderCompleted=0 ORDER BY timeCreated ASC';
		return $db->getArray($q);
	}

	/* displays the enqueued actions in the process queue for the fileId $_id */
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
				echo '<h3>Was enqueued '.ago($row['timeCreated']).' by '.nameLink($row['creatorId']);
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
				echo '<td>'.$row['timeExec'].'</td>';
			} else {
				echo '<td>not done</td>';
				echo '<td>?</td>';
			}
			echo '<td>'.$row['orderType'].'</td>';
			echo '<td>'.nameLink($row['creatorId']).'</td>';
			//echo $row['orderParams'];
			echo '</tr>';
		}
		echo '</table>';

		$files->showFileInfo($_id);
	}






///// CODE BELOW NOT CLEANED UP!!!!11



/*
	$WORK_OPRDER_TYPES = array(
		ORDER_RESIZE_IMG => 'IMAGE RESIZE',
		ORDER_CONVERT_IMG => 'IMAGE CONVERT',
		ORDER_CONVERT_VIDEO => 'VIDEO CONVERT'
	);
*/

	//adds a item on the "todo" queue of the process server
	function addWorkOrder($_type, $_params)
	{
		global $db, $session, $WORK_OPRDER_TYPES;
		if (!$session->id || !is_numeric($_type)) return false;

		$_params = $db->escape(serialize($_params));

		$q = 'INSERT INTO tblProcessQueue SET orderType='.$_type.', orderParams="'.$_params.'", creatorId='.$session->id.', timeCreated=NOW()';
		$order_id = $db->insert($q);

		$session->log('#'.$order_id.': Added work order: '.$WORK_OPRDER_TYPES[$_type]);
		return $order_id;
	}

	function getWorkOrderStatus($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT orderCompleted FROM tblProcessQueue WHERE entryId='.$_id;
		return $db->getOneItem($q);
	}

	function performWorkOrders($_limit = 10)
	{
		global $db, $session, $files;

		$ini_check = ini_get('allow_url_fopen');
		if ($ini_check != 1) die('FATAL: allow_url_fopen disabled!');

		//Aquire table lock
		$db->query('LOCK TABLES tblProcessQueue WRITE, tblLogs WRITE');

		$work_list = getWorkOrders($_limit);

		if (!$work_list) {
			//Release table lock
			$db->query('UNLOCK TABLES');
			echo 'Nothing to do!';
			return false;
		}

		$session->log('WORK ORDER QUEUE - Processing '.count($work_list).' work orders');

		$src_temp_file = 'D:/work_temp.dat';
		$dst_temp_file = 'D:/work_temp2.dat';

		if (file_exists($src_temp_file)) unlink($src_temp_file);
		if (file_exists($dst_temp_file)) unlink($dst_temp_file);

		foreach ($work_list as $work)
		{
			$params = unserialize($work['orderParams']);

			//1. Läs in src
			echo 'Reading src from '.$params['src'].' ...<br/>';
			$src_data = file_get_contents($params['src']);
			if (!$src_data) {
				$session->log('#'.$work['entryId'].': Failed to fetch src file: '.$params['src'], LOGLEVEL_ERROR);
				echo 'Error: Failed to fetch src file!<br/>';
				continue;
			}
			file_put_contents($src_temp_file, $src_data);

			switch ($work['orderType'])
			{
				case ORDER_RESIZE_IMG:
					echo 'Performing task: Image resize<br/>';
					echo ' &nbsp; params: width='.$params['width'].', height='.$params['height'].'<br/>';

					//2. Perform resize
					$check = $files->resizeImage($src_temp_file, $dst_temp_file, $params['width'], $params['height']);
					if (!$check) {
						$session->log('#'.$work['entryId'].': IMAGE RESIZE failed! w='.$params['width'].', h='.$params['height'], LOGLEVEL_ERROR);
						echo 'Error: Image resize failed!<br/>';
						continue;
					}
					$session->log('#'.$work['entryId'].': IMAGE RESIZE performed successfully');
					break;

				default:
					echo 'UNKNOWN WORK ORDER TYPE: '.$work['orderType'].'<br/>';
					continue;
			}

			//3. Write result to destination file
			echo 'Writing result to dst '.$params['dst'].' ...<br/>';
			copy($dst_temp_file, $params['dst']);

			//4. Markera utförd order
			$q = 'UPDATE tblProcessQueue SET orderCompleted=1 WHERE entryId='.$work['entryId'];
			$db->query($q);

			unlink($src_temp_file);
			unlink($dst_temp_file);

			echo '<br/>';
		}

		$session->log('WORK ORDER QUEUE - Completed');

		//Release table lock
		$db->query('UNLOCK TABLES');
	}
?>