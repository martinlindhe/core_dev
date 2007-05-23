<?
	require_once('functions_process.php');

	ini_set('soap.wsdl_cache_enabled', '0');

	$client = new SoapClient("http://europe.ipx.com/api/services/SmsApi50?wsdl", array('trace' => 1));

	try {
		$params = array(
			'correlationId' => 'x',
			'orginatingAddress' => 'x',
			'destinationAddress' => 'x',
			'orginatorAlpha' => '0',		//bool
			'userData' => 'hej världen lalal',
			'userDataHeader' => '#NULL#',
			'dcs' => '-1',
			'pid' => '-1',
			'relativeValidityTime' => '-1',
			'deliveryTime' => '#NULL#',
			'statusReportFlags' => '-1',
			'accountName' => '#NULL#',
			'blocking' => '1'	//bool
			'tariffClass' => 'SEK0',
			'referenceId' => '#NULL#',
			'contentCategory' => '#NULL#',
			'username' => 'lwcg',
			'password' => '3koA4enpE'
		);

		$result = $client->send( array('request' => $params) );
		d($result);

	} catch (Exception $e) {
		echo 'Exception: '.$e.'<br/><br/>';

		echo 'Request header: '.htmlspecialchars($client->__getLastRequestHeaders()).'<br/>';
		echo 'Request: '.htmlspecialchars($client->__getLastRequest()).'<br/>';
		echo 'Response: '.htmlspecialchars($client->__getLastResponse()).'<br/>';
	}
?>