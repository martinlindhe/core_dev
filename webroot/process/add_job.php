<?
	//add_job.php - adds a new work to the work queue
	
	require_once('config.php');

	require_once('design_head.php');
	
	$params = array (
		'width' => 60,
		'height' => 40,
		'src' => 'http://img.aftonbladet.se/vinj/v1/logo/aftonbladet375x56.gif',
		'dst' => 'D:/devel/webroot/process/crap/x.gif'
	);
	addWorkOrder(ORDER_RESIZE_IMG, $params);


	$params = array (
		'src' => 'http://img.aftonbladet.se/vinj/v1/logo/aftonbladet375x56.gif',
		'dst' => 'D:/devel/webroot/process/crap/converted.jpg',
		'format' => 'image/jpeg'
	);
	addWorkOrder(ORDER_CONVERT_IMG, $params);
		
	require_once('design_foot.php');
?>