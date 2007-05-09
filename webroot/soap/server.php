<?
	ini_set('soap.wsdl_cache_enabled', '0');

	class SOAP_ProcessService
	{
		/* Returns a unique ID for this session used to encode the password in the login() step */
		function getSID()
		{
			return 'abc123';
		}

		function login($username, $password)
		{
			return 'you wanted to log in with user '.$username.', password '.$password;
		}

	}

	$server = new SoapServer('process.wsdl', array('trace' => 1));
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