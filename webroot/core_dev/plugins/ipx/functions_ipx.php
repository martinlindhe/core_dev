<?
	/*
	extension=php_soap.dll (required)
	extension=php_openssl.dll (required for https support)
	*/

	set_time_limit(600);
	ini_set('default_socket_timeout', '600');	//10 minute timeout for SOAP requests

														//level 1=normal user
	define('VIP_LEVEL1',	2);	//Normal VIP
	define('VIP_LEVEL2',	3);	//VIP delux

	//todo: rename to $config['ipx']
	$config['sms']['originating_number'] = '123';
	$config['sms']['auth_username'] = '';
	$config['sms']['auth_password'] = '';

	//set $tariff & $reference to charge a previous MT-SMS. requires the originating_number to be configured for MT billing
	function sendSMS($dest_number, $msg, $from_number = '', $tariff = 'SEK0', $reference = '#NULL#')
	{
		//$msg must be a UTF-8 encoded string for swedish characters to work
		global $db, $config, $session;

		//använder det nummret som inkommande sms kom på för att skicka utgående, för mt billing
		//detta nummer ska bara användas för utgående sms (MO)
		if (!$from_number) $from_number = $config['sms']['originating_number'];

		$client = new SoapClient('https://europe.ipx.com/api/services/SmsApi50?wsdl');//, array('trace' => 1));

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
			return $response->responseCode.' ('.$response->responseMessage.')';

		} catch (Exception $e) {
			echo 'Exception: '.$e.'<br/><br/>';
			echo 'Request header: '.htmlspecialchars($client->__getLastRequestHeaders()).'<br/>';
			echo 'Request: '.htmlspecialchars($client->__getLastRequest()).'<br/>';
			echo 'Response: '.htmlspecialchars($client->__getLastResponse()).'<br/>';

			$session->log('Exception in sendSMS(): '.$e, LOGLEVEL_ERROR);
			return false;
		}
	}

	function ipxOutgoingLog()
	{
		global $db;

		$q = 'SELECT * FROM tblSentSMS ORDER BY timeSent DESC';
		$list = $db->getArray($q);

		foreach ($list as $row) {
			echo $row['timeSent'].' to <b>'.$row['dest'].'</b><br/>';
			echo 'Message: '.mb_convert_encoding($row['msg'], 'ISO-8859-1', 'utf8').'<br/><br/>';	//fixme: unicode probs. strängen ska va unicode men visas inte korrekt :(

			$q = 'SELECT * FROM tblSendResponses WHERE correlationId='.$row['correlationId'];
			$response = $db->getOneRow($q);

			echo 'Sent message parameters:<br/>';
			d(unserialize($response['params']));

			echo 'IPX status response: ';
			unset($response['params']);
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

			echo 'SMS from <b>'.$msg['OriginatorAddress'].'</b> operator <b>'.$msg['Operator'].'</b> (to <b>'.$msg['DestinationAddress'].'</b>)<br/>';
			echo 'Message: <b>'.$msg['Message'].'</b> (id <b>'.$msg['MessageId'].'</b>)<br/>';

			$ts = sql_datetime(strtotime($msg['TimeStamp']));
			echo 'Message sent: '.$ts.' (IPX time)<br/>';

			echo 'Incoming message parameters:<br/>';
			d(unserialize($row['params']));

			echo '<hr/>';
		}
	}

	//fixme: move all/most of this function out of here!	
	function ipxHandleIncoming()
	{
		global $db, $user_db, $session, $config;

		//All incoming data is set as GET parameters
		$params = '';
		if (!empty($_GET)) $params = $_GET;
		if (!$params) die('nothing to do');

		//Log the incoming SMS
		$q = 'INSERT INTO tblIncomingSMS SET params="'.$db->escape(serialize($params)).'",IP='.$session->ip.',timeReceived=NOW()';
		$db->insert($q);

		//Acknowledgment - Tell IPX that the SMS was received so they drop the connection
		header('HTTP/1.1 200 OK');
		header('Content-Type: text/plain');
		echo '<DeliveryResponse ack="true"/>';

		$ipx = prepareIPX_MT_bill_CS($params['Message']);	//todo: could be a class... :O

		//2. skicka ett nytt sms till avsändaren, med TARIFF satt samt med messageid från incoming sms satt som "reference id"
		//	använder samma avsändar-nummer som det inkommande SMS:et skickades till

		//"Testa att sätta referenceID-parametern till messageID:t utan det inledande "1-" delen. Det bör fungera då."
		$referenceId = $params['MessageId'];
		//if (substr($referenceId, 0, 2) == '1-') $referenceId = substr($referenceId, 2);

		$sms_err = sendSMS($params['OriginatorAddress'], $ipx['msg'], $params['DestinationAddress'], $ipx['tariff'], $referenceId);
		if ($sms_err === true) {
			$l = 'Charge to '.$ipx['username'].' of '.$ipx['tariff'].' succeeded';
			$session->log($l);

			//fixme: move this function call out of here
			addVIP($ipx['user_id'], $ipx['vip_level'], $ipx['days']);

			//Leave a confirmation message in the users inbox
			//fixme: move this sql query out of the general ipx implementation
			$internal_title = 'VIP-bekräftelse';
			$q = 'INSERT INTO s_usermail SET sender_id=0, user_id='.$ipx['user_id'].',sent_ttl="'.$internal_title.'",sent_cmt="'.$ipx['internal_msg'].'",sent_date=NOW()';
			$user_db->insert($q);
		} else {
			$l = 'Charge to '.$ipx['username'].' of '.$ipx['tariff'].' failed with error '.$sms_err;
			$session->log($l, LOGLEVEL_ERROR);
		}

		//fixme: gör dest-mail konfigurerbar
		mail('martin@unicorn.tv', '[IPX] billing report', $l);

		if ($sms_err === true) {
			return true;
		}
		return false;
	}

?>
