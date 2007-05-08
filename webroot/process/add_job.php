<?
	//add_job.php - adds a new work to the work queue
	
	require_once('config.php');

	require_once('design_head.php');

/*
	$params = array (
		'src' => 'http://localhost/process/in/sample.gif',
		'dst' => 'D:/devel/webroot/process/crap/x.gif',
		'width' => 60,
		'height' => 40
	);
	addWorkOrder(ORDER_RESIZE_IMG, $params);
	
	$params = array (
		'src' => 'http://localhost/process/in/sample.bmp',
		'dst' => 'D:/devel/webroot/process/crap/converted.jpg',

		'format' => 'image/jpeg'
	);
	addWorkOrder(ORDER_CONVERT_IMG, $params);

	$params = array (
		'src' => 'http://localhost/process/in/sample.bmp',
		'dst' => 'D:/devel/webroot/process/crap/converted.png',
		
		'format' => 'image/png'
	);
	addWorkOrder(ORDER_CONVERT_IMG, $params);
*/

	$params = array (
		'src' => 'http://localhost/process/in/sample.svg',
		'dst' => 'D:/devel/webroot/process/crap/converted.png',
		
		'format' => 'image/png'
	);
	addWorkOrder(ORDER_CONVERT_IMG, $params);

	require_once('design_foot.php');
?>