<?
	/*
		for image conversions I am using: ImageMagick-6.3.4-0-Q16-windows-dll.exe
				in ubuntu: sudo apt-get install imagemagick  (v6.2.4 in ubuntu 7.4)
	
	*/

	define('ORDER_RESIZE_IMG',		1);		//orderParams håller önskad bredd & höjd som serialized array.. ?
	define('ORDER_CONVERT_IMG',		2);		//orderParams håller mimetype på önskad output type. t.ex "image/jpeg" eller "image/png"
	define('ORDER_CONVERT_VIDEO',	3);		//orderParams håller mimetype på önskad output type. NOT YET IMPLEMENTED

	function addWorkOrder($_type, $_params)
	{
		global $db, $session;

		if (!is_numeric($_type)) return false;

		$_params = $db->escape(serialize($_params));

		$q = 'INSERT INTO tblOrders SET orderType='.$_type.', orderParams="'.$_params.'", ownerId='.$session->id.', timeCreated=NOW()';
		$db->query($q);
	}

	/* Returns the oldest 10 work orders still active for processing */
	function getWorkOrders($_limit = 10)
	{
		global $db;

		if (!is_numeric($_limit)) return false;
		
		$q = 'SELECT * FROM tblOrders ORDER BY timeCreated ASC LIMIT '.$_limit;
		return $db->getArray($q);
	}
?>