<?
	ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

	
	$params = array(
							'trace' => 1,
							'exceptions' => 1 );
	$client = new SoapClient("calls.wsdl", $params);
	try {
		print($client->getQuote("ibm"));

  	echo '<pre>';
  	echo 'Request:<br/>'.htmlspecialchars($client->__getLastRequest()).'<br/>';
  	echo 'Response:<br/>'.htmlspecialchars($client->__getLastResponse()).'<br/>';
  	echo '</pre>';

	} catch (SoapFault $e) {
  	echo $e->getMessage();
	}
?> 