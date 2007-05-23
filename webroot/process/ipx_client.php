<?
	require_once('config.php');

	$client = new SoapClient("http://europe.ipx.com/api/services/SmsApi50?wsdl", array('trace' => 1));

	try {
		$params = array(
			//element										value					data type
			'correlationId'					=>	'corrID2',			//string	- id som klienten sätter för att hålla reda på requesten, returneras tillsammans med soap-response från IPX
			'originatingAddress'		=>	'72777',			//string	- orginating number for SMS sent by us
			'destinationAddress'		=>	'46707308763',//string	- mottagare till sms:et, med landskod, format: 46707308763
			'originatorAlpha'				=>	'0',					//bool		- ?
			'userData'							=>	'hej lalala',	//string	- meddelandetexten
			'userDataHeader'				=>	'#NULL#',			//string	- ?
			'dcs'										=>	'-1',					//int			- ? data coding scheme
			'pid'										=>	'-1',					//int			- reserved
			'relativeValidityTime'	=>	'-1',					//int			- relative validity time in seconds, from the time of submiussion to IPX
			'deliveryTime'					=>	'#NULL#',			//string	- used for delayed delivery of sms
			'statusReportFlags'			=>	'0',					//int			- 0 = no delivery report, 1 = delivery report requested
			'accountName'						=>	'#NULL#',			//string	- ?
			'blocking'							=>	'1',					//bool		- reserved
			'tariffClass'						=>	'SEK0',				//string	- price of the premium message
			'referenceId'						=>	'#NULL#',			//string	- refenrence order of premium message
			'contentCategory'				=>	'#NULL#',			//string	- reserved
			'username'							=>	'lwcg',				//string
			'password'							=>	'3koA4enpE'		//string
		);

		$result = $client->send( $params );
		d($result);

	} catch (Exception $e) {
		echo 'Exception: '.$e.'<br/><br/>';

		echo 'Request header: '.htmlspecialchars($client->__getLastRequestHeaders()).'<br/>';
		echo 'Request: '.htmlspecialchars($client->__getLastRequest()).'<br/>';
		echo 'Response: '.htmlspecialchars($client->__getLastResponse()).'<br/>';
	}
?>