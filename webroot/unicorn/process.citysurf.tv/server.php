<?
	ini_set('soap.wsdl_cache_enabled', '0');

	require_once('config.php');

	class SOAP_ProcessService
	{
		function newOrder($_type, $serialized_params)
		{
			global $session;

			$params = unserialize($serialized_params);

			if (!$session->logIn($params['username'], $params['password'])) {
				return false;
			}

			return addWorkOrder($_type, $params);
		}
	}

	$server = new SoapServer('process.wsdl'); //, array('trace' => 1));
	$server->setClass('SOAP_ProcessService');

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		/*
			fixme: this is currently required to fetch the post data, else will return in:
				"Bad Request. Can't find HTTP_RAW_POST_DATA"
			this occurs with php 5.2.2, hopefully will not be needed in the future.
			This works wether always_populate_raw_post_data is on or off
		*/
	  $data = file_get_contents('php://input');
	  $server->handle($data);
	} else {
	  echo 'This SOAP server can handle following functions: <br/>';
	  $functions = $server->getFunctions();
	  foreach($functions as $name) {
	   echo $name.'<br/>';
	  }
	}

?>