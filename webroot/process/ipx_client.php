<?
	/*
	extension=php_soap.dll (required)
	extension=php_openssl.dll (required for https support)
	*/	

	require_once('config.php');	//för d() debug dump

	$config['sms']['originating_number'] = '72777';
	$config['sms']['auth_username'] = 'lwcg';
	$config['sms']['auth_password'] = '3koA4enpE';

	set_time_limit(600);
	ini_set('default_socket_timeout', '600');	//10 minute timeout for SOAP requests

	sendSMS('46707308763', 'tjenna åäö test ÅÄÖ lalalaa');
	
	function sendSMS($dest_number, $msg)
	{
		//$msg must be a UTF-8 encoded string for swedish characters to work
		global $db, $config;

		$client = new SoapClient("https://europe.ipx.com/api/services/SmsApi50?wsdl", array('trace' => 1));
		//d($client->__getTypes());

		try {
			
			$q = 'INSERT INTO tblSentSMS SET dest="'.$db->escape($dest_number).'",msg="'.$db->escape($msg).'",timeSent=NOW()';
			$corrId = $db->insert($q);
			
			if (!$corrId) die('FAILED TO INSERT tblSentSMS');

			$params = array(
				//element										value					data type
				'correlationId'					=>	$corrId,			//string	- id som klienten sätter för att hålla reda på requesten, returneras tillsammans med soap-response från IPX
				'originatingAddress'		=>	$config['sms']['originating_number'],	//string	- orginating number for SMS sent by us
				'destinationAddress'		=>	$dest_number,	//string	- mottagare till sms:et, med landskod, format: 46707308763
				'originatorAlpha'				=>	'0',					//bool		- ?
				'userData'							=>	$msg,					//string	- meddelandetexten
				'userDataHeader'				=>	'#NULL#',			//string	- ?
				'dcs'										=>	'-1',					//int			- data coding scheme, how the userData text are encoded
				'pid'										=>	'-1',					//int			- reserved
				'relativeValidityTime'	=>	'-1',					//int			- relative validity time in seconds, from the time of submiussion to IPX
				'deliveryTime'					=>	'#NULL#',			//string	- used for delayed delivery of sms
				'statusReportFlags'			=>	'0',					//int			- 0 = no delivery report, 1 = delivery report requested
				'accountName'						=>	'#NULL#',			//string	- ?
				'blocking'							=>	'1',					//bool		- reserved
				'tariffClass'						=>	'SEK0',				//string	- price of the premium message
				'referenceId'						=>	'#NULL#',			//string	- refenrence order of premium message
				'contentCategory'				=>	'#NULL#',			//string	- reserved
				'username'							=>	$config['sms']['auth_username'],	//string
				'password'							=>	$config['sms']['auth_password']		//string
			);

			$response = $client->send($params);
			d($response);
			
			$q = 'INSERT INTO tblSendResponses SET correlationId='.$corrId.',messageId="'.$db->escape($response->messageId).'",responseCode='.$response->responseCode.',responseMessage="'.$db->escape($response->responseMessage).'",temporaryError='.intval($response->temporaryError).',timeCreated=NOW()';
			if ($response->responseCode) {
				$q .= ',params="'.$db->escape(serialize($params)).'"';
			}
			$db->insert($q);

		} catch (Exception $e) {
			echo 'Exception: '.$e.'<br/><br/>';
			echo 'Request header: '.htmlspecialchars($client->__getLastRequestHeaders()).'<br/>';
			echo 'Request: '.htmlspecialchars($client->__getLastRequest()).'<br/>';
			echo 'Response: '.htmlspecialchars($client->__getLastResponse()).'<br/>';
		}
	}
?>