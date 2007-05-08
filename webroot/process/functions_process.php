<?
	/*
		for image conversions I am using: ImageMagick-6.3.4-0-Q16-windows-dll.exe
				in ubuntu: sudo apt-get install imagemagick  (v6.2.4 in ubuntu 7.4)

		configuration:
			this module requires:
			- php_soap.dll extension (included in windows php dist, disabled by default)
			- allow_url_fopen = On
			- always_populate_raw_post_data = On (required by php_soap.dll)

		todo:
			* SOAP interface
			* locking. enbart en performWorkOrders() åt gången
			* kolla om php_soap.dll extension är laddad
			* kolla om soap config är ok. cache = ON, temp path ska va korrekt, ttl ska va minst 24 timmar
	*/

	define('ORDER_RESIZE_IMG',		1);		//orderParams håller önskad bredd & höjd som serialized array.. ?
	define('ORDER_CONVERT_IMG',		2);		//orderParams håller mimetype på önskad output type. t.ex "image/jpeg" eller "image/png"
	define('ORDER_CONVERT_VIDEO',	3);		//orderParams håller mimetype på önskad output type. NOT YET IMPLEMENTED

	$WORK_OPRDER_TYPES = array(
		ORDER_RESIZE_IMG => 'IMAGE RESIZE',
		ORDER_CONVERT_IMG => 'IMAGE CONVERT',
		ORDER_CONVERT_VIDEO => 'VIDEO CONVERT'
	);

	function addWorkOrder($_type, $_params)
	{
		global $db, $session, $WORK_OPRDER_TYPES;

		if (!is_numeric($_type)) return false;

		$_params = $db->escape(serialize($_params));

		$q = 'INSERT INTO tblOrders SET orderType='.$_type.', orderParams="'.$_params.'", ownerId='.$session->id.', timeCreated=NOW()';
		$db->query($q);
		
		$session->log('#'.$db->insert_id.': Added work order: '.$WORK_OPRDER_TYPES[$_type]);
	}

	/* Returns the oldest 10 work orders still active for processing */
	function getWorkOrders($_limit = 10)
	{
		global $db;

		if (!is_numeric($_limit)) return false;
		
		$q = 'SELECT * FROM tblOrders ORDER BY timeCreated ASC LIMIT '.$_limit;
		return $db->getArray($q);
	}


	function performWorkOrders($_limit = 10)
	{
		global $db, $session, $files;

		$ini_check = ini_get('allow_url_fopen');
		if ($ini_check != 1) die('FATAL: allow_url_fopen disabled!');

		$work_list = getWorkOrders($_limit);

		if (!$work_list) {
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
	
				case ORDER_CONVERT_IMG:
					echo 'Performing task: Image convert<br/>';
					echo ' &nbsp; params: format='.$params['format'].'<br/>';
	
					//2. Perform convert
					$check = $files->convertImage($src_temp_file, $dst_temp_file, $params['format']);
					if (!$check) {
						$session->log('#'.$work['entryId'].': IMAGE CONVERT failed! format='.$params['format'], LOGLEVEL_ERROR);
						echo 'Error: Image convert failed!<br/>';
						continue;
					}
					$session->log('#'.$work['entryId'].': IMAGE CONVERT performed successfully');
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
	
			unlink($src_temp_file);
			unlink($dst_temp_file);
	
			echo '<br/>';
		}

		$session->log('WORK ORDER QUEUE - Completed');
	}
?>