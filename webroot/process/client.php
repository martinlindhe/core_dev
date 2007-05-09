<?
	require_once('functions_process.php');

	ini_set('soap.wsdl_cache_enabled', '0');

  echo '<pre>';

	$client = new SoapClient("http://localhost/process/process.wsdl", array('trace' => 1));

	try {
		$sid = $client->login('martin', 'nutana');
		if (!$sid) {
			echo 'login failed';
			die;
		}
		
		list($sess_name, $sess_val) = explode('=', $sid);
		echo 'sessname: '.$sess_name.'<br>';
		echo 'sess val: '.$sess_val.'<br>';
		//$client->__setCookie($sess_name, $sess_val);

		$params = array (
			'src' => 'http://localhost/process/in/sample.gif',
			'dst' => 'D:/devel/webroot/process/crap/x.gif',
			'width' => 60,
			'height' => 40
		);

		$client->newOrder(ORDER_RESIZE_IMG, serialize($params));

		echo '<br/>';
		echo 'Request header: '.htmlspecialchars($client->__getLastRequestHeaders()).'<br/>';
		echo 'Request :'.htmlspecialchars($client->__getLastRequest()).'<br/>';
  	echo 'Response:'.htmlspecialchars($client->__getLastResponse()).'<br/>';
	} catch (Exception $e) {
		echo 'exception: '.$e.'<br/><br/>';
		echo 'Request header: '.htmlspecialchars($client->__getLastRequestHeaders()).'<br/>';
		echo 'Request: '.htmlspecialchars($client->__getLastRequest()).'<br/>';
		echo 'Response: '.htmlspecialchars($client->__getLastResponse()).'<br/>';
	}
?>