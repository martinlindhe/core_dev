<?
	/*
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

		if (!$session->id) {
			$session->log('Un-authenticated user attempted to add work order');
			return false;
		}

		$_params = $db->escape(serialize($_params));

		$q = 'INSERT INTO tblOrders SET orderType='.$_type.', orderParams="'.$_params.'", ownerId='.$session->id.', timeCreated=NOW()';
		$order_id = $db->insert($q);

		$session->log('#'.$order_id.': Added work order: '.$WORK_OPRDER_TYPES[$_type]);
		return $order_id;
	}

	/* Returns the oldest 10 work orders still active for processing */
	function getWorkOrders($_limit = 10)
	{
		global $db;

		if (!is_numeric($_limit)) return false;

		$q = 'SELECT * FROM tblOrders WHERE orderCompleted=0 ORDER BY timeCreated ASC LIMIT '.$_limit;
		return $db->getArray($q);
	}

	function getWorkOrderStatus($_id)
	{
		global $db;

		if (!is_numeric($_id)) return false;

		$q = 'SELECT orderCompleted FROM tblOrders WHERE entryId='.$_id;
		return $db->getOneItem($q);
	}

	function performWorkOrders($_limit = 10)
	{
		global $db, $session, $files;

		$ini_check = ini_get('allow_url_fopen');
		if ($ini_check != 1) die('FATAL: allow_url_fopen disabled!');

		//Aquire table lock
		$db->query('LOCK TABLES tblOrders WRITE, tblLogs WRITE');

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

			//4. Markera utförd order
			$q = 'UPDATE tblOrders SET orderCompleted=1 WHERE entryId='.$work['entryId'];
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