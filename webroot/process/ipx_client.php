<?
	/*
	extension=php_soap.dll (required)
	extension=php_openssl.dll (required for https support)
	*/

	require_once('config.php');	//för d() debug dump

	set_time_limit(600);
	ini_set('default_socket_timeout', '600');	//10 minute timeout for SOAP requests

	sendSMS('46707308763', 'tjenna åäö test ÅÄÖ lalalaa');

?>