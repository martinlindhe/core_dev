<?
	/* perform_work.php - körs regelbundet
	
		detta script plockar fram de 10 äldsta arbetsuppgifterna från databasen och utför dessa en i taget
	*/

	require_once('config.php');

	$ini_check = ini_get('allow_url_fopen');
	if ($ini_check != 1) die('FATAL: allow_url_fopen disabled!');

	$work_list = getWorkOrders(10);

	if ($work_list) {
		$session->log('WORK OERDER QUEUE - Processing '.count($work_list).' work orders');
	}

	foreach ($work_list as $work)
	{
		$params = unserialize($work['orderParams']);

		$src_temp_file = 'D:/work_temp.dat';
		$dst_temp_file = 'D:/work_temp2.dat';
		@unlink($src_temp_file);
		@unlink($dst_temp_file);

		//1. Läs in src
		echo 'Reading src from '.$params['src'].' ...<br/>';
		$src_data = file_get_contents($params['src']);
		if (!$src_data) {
			echo 'Error: Failed to fetch src file!<br/>';
			continue;
		}
		file_put_contents($src_temp_file, $src_data);

		switch ($work['orderType'])
		{
			case ORDER_RESIZE_IMG:
				echo 'Performing task: Resize image<br/>';
				echo ' &nbsp; order params: width='.$params['width'].', height='.$params['height'].'<br/>';

				//2. Perform resize
				$check = $files->resizeImage($src_temp_file, $dst_temp_file, $params['width'], $params['height']);
				if (!$check) {
					$session->log('#'.$work['entryId'].': Image resize failed! w='.$params['width'].', h='.$params['height']);
					echo 'Error: Image resize failed!<br/>';
					continue;
				}
				$session->log('#'.$work['entryId'].': Image resize performed successfully');
				break;

			case ORDER_CONVERT_IMG:
				echo 'Performing task: Convert image<br/>';
				echo ' &nbsp; order params: format='.$params['format'].'<br/>';

				//2. Perform convert. todo gör detta till en funktion i class.Files.php, återanvänd i class.Files.php:handleGeneralUpload()
				$c = 'convert -quality 75 GIF:'.$src_temp_file.' JPG:'.$dst_temp_file;
				exec($c);

				if (!file_exists($dst_temp_file)) {
					$session->log('#'.$work['entryId'].': Image convert failed! cmd: '.$db->escape($c));
					echo 'Error: Convert failed!<br/>';
					continue;
				}
				$session->log('#'.$work['entryId'].': Image convert performed successfully');
				break;

			default:
				echo 'UNKNOWN WORK ORDER TYPE: '.$work['orderType'].'<br/>';
				continue;
		}
		
		//3. Write result to destination file
		echo 'Writing result to dst '.$params['dst'].' ...<br/>';
		copy($dst_temp_file, $params['dst']);

		//4. Ta bort utfört arbete från loggen
		$q = 'DELETE FROM tblOrders WHERE entryId='.$work['entryId'];
		$db->query($q);

		echo '<br/>';
	}

	if ($work_list) {
		$session->log('WORK OERDER QUEUE - Completed');
	}

?>