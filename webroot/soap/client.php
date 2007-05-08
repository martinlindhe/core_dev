<?
	ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

	$client = new SoapClient("calls.wsdl");

	print($client->getQuote("ibm"));
?> 