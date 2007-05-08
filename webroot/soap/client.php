<?
	ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

	$client = new SoapClient("calls.wsdl");
	try {
		print($client->getQuote("ibm"));
	} catch (SoapFault $e) {
  	echo $e->getMessage();
	}
?> 