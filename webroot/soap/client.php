<?
  echo '<pre>';

	$client = new SoapClient("http://localhost/soap/calls.wsdl", array('trace' => 1));

	try {
		//echo 'getQuote: '. $client->getQuote("ibm").'<br/>';
		echo $client->login('martin'); //, 'test');

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