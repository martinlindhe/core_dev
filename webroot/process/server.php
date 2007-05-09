<?
	ini_set('soap.wsdl_cache_enabled', '0');

	class SOAP_ProcessService
	{
		/* Returns a unique ID for this session used to encode the password in the login() step */
		function getSID()
		{
			/* Todo: use this function from the client to get a unique ID to encode the password with. 
				currently I dont know a good solution since the password is already encoded using several sha1-sums
				in the database, it can not easily be confirmed to be correct this way.

				For the moment, we solve this by hiding the Process service behind SSL
			*/
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