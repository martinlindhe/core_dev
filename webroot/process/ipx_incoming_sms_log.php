<?
	require_once('config.php');
	$session->requireLoggedIn();

	require_once('design_head.php');

	echo '<h1>IPX incoming SMS log</h1>';
	
	$q = 'SELECT * FROM tblIncomingSMS ORDER BY timeReceived DESC';
	$list = $db->getArray($q);
	
	foreach ($list as $row) {
		$ipv4 = GeoIP_to_IPv4($row['IP']);
		echo $row['timeReceived'].' (local time) incoming data from <a href="admin/admin_ip.php?ip='.$ipv4.getProjectPath().'">'.$ipv4.'</a><br/>';
		$msg = unserialize($row['params']);

		echo 'SMS from '.$msg['OriginatorAddress'].' operator '.$msg['Operator'].' (to '.$msg['DestinationAddress'].')<br/>';
		echo 'Message: '.$msg['Message'].' (id '.$msg['MessageId'].')<br/>';
		
		$ts = sql_datetime(strtotime($msg['TimeStamp']));
		echo 'Message sent: '.$ts.' (IPX time)<br/>';
		echo '<hr/>';
	}

	require_once('design_foot.php');
?>