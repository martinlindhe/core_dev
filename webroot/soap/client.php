<?
  echo '<pre>';

	$client = new SoapClient("http://localhost/soap/calls.wsdl", array('trace' => 1));

	try {
		//echo $client->getQuote("ibm");
		echo $client->login('martin', 'test');

		echo '<br/>';
		echo 'Request :'.htmlspecialchars($client->__getLastRequest()).'<br/>';
  	echo 'Response:'.htmlspecialchars($client->__getLastResponse()).'<br/>';
	} catch (Exception $e) {
		echo 'exception: '.$e.'<br/><br/>';
		echo 'last request header: '.htmlspecialchars($client->__getLastRequestHeaders()).'<br/>';
		echo 'last request body: '.htmlspecialchars($client->__getLastRequest()).'<br/>';
		echo 'last response: '.htmlspecialchars($client->__getLastResponse()).'<br/>';
	}
?>