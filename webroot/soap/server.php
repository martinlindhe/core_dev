<?
	/*
		exempel med login:
		http://www.suraski.net/blog/index.php?/archives/5-PHP-5s-SOAP-extension-and-SalesForce.html
	*/

	class SOAP_ProcessService
	{
		private $quotes = array('ibm' => 98.42);  

		function getQuote($symbol)
		{
			if (isset($this->quotes[$symbol])) return $this->quotes[$symbol];
	
			throw new SoapFault('Server', 'Unknown Symbol '.$symbol);
		}

		function login($params)
		{
			return 'you wanted to log in with user '.$params['username'].', password '.$params['password'];
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
	  foreach($functions as $func) {
	   echo $func.'<br/>';
	  }
	}

?>