<?
	ini_set('soap.wsdl_cache_enabled', '0');

  echo '<pre>';

	$client = new SoapClient("http://localhost/process/process.wsdl", array('trace' => 1));

	try {
		echo $client->login('martin', 'test');

		echo '<br/>';
		echo 'Request :'.htmlspecialchars($client->__getLastRequest()).'<br/>';
  	echo 'Response:'.htmlspecialchars($client->__getLastResponse()).'<br/>';
	} catch (Exception $e) {
		echo 'exception: '.$e.'<br/><br/>';
		echo 'Request header: '.htmlspecialchars($client->__getLastRequestHeaders()).'<br/>';
		echo 'Request: '.htmlspecialchars($client->__getLastRequest()).'<br/>';
		echo 'Response: '.htmlspecialchars($client->__getLastResponse()).'<br/>';
	}
?>