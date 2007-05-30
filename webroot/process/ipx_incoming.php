<?
	/*
		This script is called by IPX for incoming SMS

		The following parameters are set as GET parameters:
			OriginatorAddress			- number the SMS came from, in the format 46702297439
			DestinationAddress		- number the SMS was sent to, in the format 71160
			Message								- message text: ""Pog 123 TEST"
			MessageId							- unique message ID, "1-797950504"
			Operator							- name of the consumers mobile operator, "Telia"
			TimeStamp							- timestamp in CET / CEST time zone format, "20070529 13:11:41"

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


	//All incoming data is set as GET parameters
	$params = '';
	if (!empty($_GET)) $params = $db->escape(serialize($_GET));
	//if (!empty($_POST)) $params = $db->escape(serialize($_POST));

	$q = 'INSERT INTO tblIncomingSMS SET params="'.$params.'",IP='.$session->ip.',timeReceived=NOW()';
	$db->insert($q);

	//Acknowledgment - Tell IPX that the SMS received
	header('HTTP/1.1 200 OK');
	header('Content-Type: text/plain');
	echo '<DeliveryResponse ack="true"/>';
?>