<?
	/*
	extension=php_soap.dll (required)
	extension=php_openssl.dll (required for https support)
	*/

	set_time_limit(600);
	ini_set('default_socket_timeout', '600');	//10 minute timeout for SOAP requests

	$config['sms']['originating_number'] = '71160';
	$config['sms']['auth_username'] = 'lwcg';
	$config['sms']['auth_password'] = '3koA4enpE';

	//set $tariff & $reference to charge a previous MT-SMS. requires the originating_number to be configured for MT billing
	function sendSMS($dest_number, $msg, $tariff = 'SEK0', $reference = '#NULL#')
	{
		//$msg must be a UTF-8 encoded string for swedish characters to work
		global $db, $config;

		$client = new SoapClient('https://europe.ipx.com/api/services/SmsApi50?wsdl');//, array('trace' => 1));

		//try {
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
				'tariffClass'						=>	$tariff,			//string	- price of the premium message in the format "SEK0"
				'referenceId'						=>	$reference,		//string	- reference order of premium message
				'contentCategory'				=>	'#NULL#',			//string	- reserved
				'username'							=>	$config['sms']['auth_username'],	//string
				'password'							=>	$config['sms']['auth_password']		//string
			);

			$response = $client->send($params);
			
			$q = 'INSERT INTO tblSendResponses SET correlationId='.$corrId.',messageId="'.$db->escape($response->messageId).'",responseCode='.$response->responseCode.',responseMessage="'.$db->escape($response->responseMessage).'",temporaryError='.intval($response->temporaryError).',timeCreated=NOW()';
			$q .= ',params="'.$db->escape(serialize($params)).'"';
			$db->insert($q);

			if ($response->responseCode == 0) return true;
			return $response->responseCode.' ('.$response->responseMessage.')';		//fixme: returnerar tom sträng??

		/*} catch (Exception $e) {
			echo 'Exception: '.$e.'<br/><br/>';
			echo 'Request header: '.htmlspecialchars($client->__getLastRequestHeaders()).'<br/>';
			echo 'Request: '.htmlspecialchars($client->__getLastRequest()).'<br/>';
			echo 'Response: '.htmlspecialchars($client->__getLastResponse()).'<br/>';
		}*/
	}

	function ipxOutgoingLog()
	{
		global $db;

		$q = 'SELECT * FROM tblSentSMS ORDER BY timeSent DESC';
		$list = $db->getArray($q);

		foreach ($list as $row) {
			echo $row['timeSent'].' to '.$row['dest'].'<br/>';
			echo 'Message: '.$row['msg'].'<br/>';

			$q = 'SELECT * FROM tblSendResponses WHERE correlationId='.$row['correlationId'];
			$response = $db->getOneRow($q);

			echo 'IPX status response: ';
			d($response);
			echo '<hr/>';
		}
	}

	function ipxIncomingLog()
	{
		global $db, $config;

		$q = 'SELECT * FROM tblIncomingSMS ORDER BY timeReceived DESC';
		$list = $db->getArray($q);

		foreach ($list as $row) {
			$ipv4 = GeoIP_to_IPv4($row['IP']);
			echo $row['timeReceived'].' (local time) incoming data from <a href="'.$config['core_web_root'].'admin/admin_ip.php?ip='.$ipv4.getProjectPath().'">'.$ipv4.'</a><br/>';
			$msg = unserialize($row['params']);

			echo 'SMS from '.$msg['OriginatorAddress'].' operator '.$msg['Operator'].' (to '.$msg['DestinationAddress'].')<br/>';
			echo 'Message: '.$msg['Message'].' (id '.$msg['MessageId'].')<br/>';

			$ts = sql_datetime(strtotime($msg['TimeStamp']));
			echo 'Message sent: '.$ts.' (IPX time)<br/>';
			echo '<hr/>';
		}
	}
?>