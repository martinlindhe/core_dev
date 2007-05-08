<?
	$quotes = array(
		"ibm" => 98.42
	);

	function getQuote($symbol) {
		global $quotes;
		return $quotes[$symbol];
	}

	ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
	$server = new SoapServer("calls.wsdl");
	$server->addFunction("getQuote");
	$server->handle();
?> 