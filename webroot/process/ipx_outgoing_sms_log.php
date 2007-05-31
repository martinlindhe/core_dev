<?
	require_once('config.php');
	$session->requireLoggedIn();

	require_once('design_head.php');

	echo '<h1>IPX outgoing SMS log</h1>';
	
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

	require_once('design_foot.php');
?>