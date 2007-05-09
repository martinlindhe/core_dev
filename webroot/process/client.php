<?
	require_once('functions_process.php');

	ini_set('soap.wsdl_cache_enabled', '0');

	$client = new SoapClient("http://localhost/process/process.wsdl"); //, array('trace' => 1));

	try {
		if (!$client->login('martin', 'nutana')) {
			echo 'login failed';
			die;
		}

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
			'src' => 'http://localhost/process/in/sample.gif',
			'dst' => 'D:/devel/webroot/process/crap/x.gif',
			'width' => 60,
			'height' => 40
		);

		$result = $client->newOrder(ORDER_RESIZE_IMG, serialize($params));
		if ($result) {
			echo 'Order added successfully';
		} else {
			echo 'Failed to add order!';
		}

	} catch (Exception $e) {
		echo 'Exception: '.$e.'<br/><br/>';

		echo 'Request header: '.htmlspecialchars($client->__getLastRequestHeaders()).'<br/>';
		echo 'Request: '.htmlspecialchars($client->__getLastRequest()).'<br/>';
		echo 'Response: '.htmlspecialchars($client->__getLastResponse()).'<br/>';
	}
?>