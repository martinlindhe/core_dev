<?php

ini_set('soap.wsdl_cache_enabled', '0');

$config['no_session'] = true;	//force session "last active" update to be skipped
require_once('config.php');

class SOAP_ProcessService
{
	function fetchAndConvert($username, $password, $uri, $callback, $watermark_uri)
	{
		$customerId = getCustomerId($username, $password);
		if (!$customerId) return false;

		$id = addProcessEvent(PROCESS_FETCH, $customerId, $uri);
		$params['callback'] = $callback;
		$params['watermark'] = $watermark_uri;

		$endId = addProcessEvent(PROCESS_CONVERT_TO_DEFAULT, $customerId, $id, $params);

		return $endId;
	}
}

$server = new SoapServer($config['app']['full_url'].'process.wsdl.php'); //, array('trace' => 1));
$server->setClass('SOAP_ProcessService');

$server->handle();

/*
  echo 'This SOAP server has the following functions:<br/>';
  $functions = $server->getFunctions();
  foreach($functions as $name) {
   echo $name.'<br/>';
  }
*/

?>
