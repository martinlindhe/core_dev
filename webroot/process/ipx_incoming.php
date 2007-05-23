<?
	/*
		This script is called by IPX for incoming SMS

		The following varibles are set:
			DestinationAddress		- number the SMS was sent to
			OriginatorAddress			- number the SMS came from
			Message								- message text
			MessageId							- unique message ID
			TimeStamp							- timestamp in CET / CEST time zone format
			Operator							- name of the consumers mobile operator
	*/
	
	require_once('config.php');

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