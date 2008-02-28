<?
	ini_set('soap.wsdl_cache_enabled', '0');

	require_once('config.php');

	$client = new SoapClient("http://coredev.localhost/process/process.wsdl.php"); //, array('trace' => 1));

	try {
		$uri = 'http://localhost/sample.3gp';
		$callback = 'http://localhost/process_callback.php?ref=123';	//will tell client app that file "123" finished processing
		$result = $client->fetchAndConvert($uri, $callback);

		if (!$result) {
			echo 'Failed to add order!';
		} else {
			echo 'Order added successfully. Order ID is '.$result;
		}

	} catch (Exception $e) {
		echo 'Exception: '.$e.'<br/><br/>';

		echo 'Request header: '.htmlspecialchars($client->__getLastRequestHeaders()).'<br/>';
		echo 'Request: '.htmlspecialchars($client->__getLastRequest()).'<br/>';
		echo 'Response: '.htmlspecialchars($client->__getLastResponse()).'<br/>';
	}
?>