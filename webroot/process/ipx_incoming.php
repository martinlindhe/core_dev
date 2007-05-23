<?
	/*
		This script is called by IPX for incoming SMS

		The following parameters are set:
			DestinationAddress		- number the SMS was sent to
			OriginatorAddress			- number the SMS came from
			Message								- message text
			MessageId							- unique message ID
			TimeStamp							- timestamp in CET / CEST time zone format
			Operator							- name of the consumers mobile operator
			
			All parameters are set, however some may have a value with length 0
	*/
	
	require_once('config.php');

	$allowed_ips = array(
		'127.0.0.1',
		'213.80.11.162',	//Unicorn kontor oxtorgsgränd 3
		'87.227.76.225',	//Martin glocalnet hem-ip
		'ipx.com'					//Ericsson IPX - fixme: rätt ip/hostname
	);
	
	if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
		$session->log('ipx_incoming.php accessed by unlisted IP', LOGLEVEL_ERROR);
		//fixme: ska stoppa här vid okänt ip, gör det ej nu för debuggande
		//die('ip not allowed');
	}
	

	$get = '';
	$post = '';
	if (!empty($_GET)) $get = $db->escape(serialize($_GET));
	if (!empty($_POST)) $get = $db->escape(serialize($_POST));

	$q = 'INSERT INTO tblIncomingSMS SET get="'.$get.'",post="'.$post.'",IP='.$session->ip.',timeReceived=NOW()';
	$db->insert($q);

	//Acknowledgment - Tell IPX that the SMS received
	header('HTTP/1.1 200 OK');
	header('Content-Type: text/plain');
	echo '<DeliveryResponse ack="true"/>';
?>