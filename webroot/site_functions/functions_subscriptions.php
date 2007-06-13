<?
	/*
		functions_subscriptions.php - Funktioner för övervakningar
	*/


	/* Subscription types */
	define('SUBSCRIBE_MAIL',			1);		// $recipient is a email
	define('SUBSCRIBE_SMS',				2); 	// $recipient is a cellphone number
	define('SUBSCRIBE_IM',				3);		// $recipient is the userId to send a Instant Message to
	define('SUBSCRIBE_TRACKSITE',	10);	// used to store subscriptions for a track site, $recipient is not used

	/* Skapar en subscription av $type, på itemId med mottagare $recipient (userId, email eller mobilnummer) */
	function addSubscription(&$db, $type, $ownerId, $recipient = '')
	{
		if (!is_numeric($type) || !is_numeric($ownerId)) return false;
		
		$recipient = dbAddSlashes($db, $recipient);

		$sql = 'INSERT INTO tblSubscriptions SET creatorId='.$_SESSION['userId'].', ownerId='.$ownerId.', subscriptionType='.$type.', recipient="'.$recipient.'", timeCreated=NOW()';
		dbQuery($db, $sql);
		return $db['insert_id'];
	}

	/* Raderar en subscription */
	function removeSubscription(&$db, $type, $ownerId, $subscriptionId)
	{
		if (!is_numeric($type) || !is_numeric($ownerId) || !is_numeric($subscriptionId)) return false;

		$sql = 'DELETE FROM tblSubscriptions WHERE subscriptionId='.$subscriptionId.' AND subscriptionType='.$type.' AND ownerId='.$ownerId;
		dbQuery($db, $sql);
	}
	
	/* Raderar alla subscriptions av $type och $ownerId */
	function removeAllSubscriptions(&$db, $type, $ownerId)
	{
		if (!is_numeric($type) || !is_numeric($ownerId)) return false;
		
		$sql = 'DELETE FROM tblSubscriptions WHERE subscriptionType='.$type.' AND ownerId='.$ownerId;
		dbQuery($db, $sql);
	}

	/* Returns all subscribers for $ownerId, only of type $type if specified */
	function getSubscribers(&$db, $type, $ownerId)
	{
		if (!is_numeric($type) || !is_numeric($ownerId)) return false;

		$sql = 'SELECT * FROM tblSubscriptions WHERE ownerId='.$ownerId.' AND subscriptionType='.$type;
		return dbArray($db, $sql);
	}
	
	/* Returns an array with all stored settings belonging to this subscription from tblSettings */
	function getSubscriptionSettings(&$db, $type, $ownerId)
	{
		if (!is_numeric($type) || !is_numeric($ownerId)) return false;

		$sql  = 'SELECT t2.settingId,t2.settingName,t2.settingValue FROM tblSubscriptions AS t1 ';
		$sql .= 'INNER JOIN tblSettings AS t2 ON (t1.subscriptionId=t2.ownerId) ';
		$sql .= 'WHERE t1.subscriptionId='.$ownerId.' AND t1.subscriptionType='.$type;

		return dbArray($db, $sql);
	}

	/* Returnerar ett row för angiven subscription */
	function getSubscription(&$db, $type, $subscriptionId)
	{
		if (!is_numeric($type) || !is_numeric($subscriptionId)) return false;
		
		$sql = 'SELECT * FROM tblSubscriptions WHERE subscriptionType='.$type.' AND subscriptionId='.$subscriptionId;
		
		return dbOneResult($db, $sql);
	}
	
	/* Returnerar alla subscriptions av en viss typ */
	function getSubscriptions(&$db, $type)
	{
		if (!is_numeric($type)) return false;
		
		$sql = 'SELECT * FROM tblSubscriptions WHERE subscriptionType='.$type;
		return dbArray($db, $sql);		
	}
	
	/* Helper function, returns a comma separated text string with mail addresses */
	function getEmailSubscribers(&$db, $subscriptionId)
	{
		if (!is_numeric($subscriptionId)) return false;
		
		$list = getSubscribers($db, SUBSCRIBE_MAIL, $subscriptionId);
		$mails = array();
		for ($i=0; $i<count($list); $i++) {
			$mails[] = $list[$i]['recipient'];
		}
		
		return $mails;
	}

	function addSubscriptionHistory(&$db, $subscriptionId, $time_from, $time_to, $mail_to, $text)
	{
		if (!is_numeric($subscriptionId) || !is_numeric($time_from) || !is_numeric($time_to)) return false;

		$mail_to = dbAddSlashes($db, $mail_to);
		$text = dbAddSlashes($db, $text);

		$date_from = date('Y-m-d H:i', $time_from); // YYYY-MM-DD HH:SS format (MySQL DATETIME)
		$date_to   = date('Y-m-d H:i', $time_to);

		$sql = 'INSERT INTO tblSubscriptionsHistory SET subscriptionId='.$subscriptionId.',timeCreated=NOW(),periodStart="'.$date_from.'",periodEnd="'.$date_to.'",recipients="'.$mail_to.'",message="'.$text.'"';
		dbQuery($db, $sql);
	}
	
	/* Returns true if this period has already been covered */
	function checkSubscriptionHistoryPeriod(&$db, $subscriptionId, $time_from, $time_to)
	{
		if (!is_numeric($subscriptionId) || !is_numeric($time_from) || !is_numeric($time_to)) return false;
		
		$date_from = date('Y-m-d H:i', $time_from); // YYYY-MM-DD HH:SS format (MySQL DATETIME)
		$date_to   = date('Y-m-d H:i', $time_to);

		$sql = 'SELECT historyId FROM tblSubscriptionsHistory WHERE subscriptionId='.$subscriptionId.' AND periodStart="'.$date_from.'" AND periodEnd="'.$date_to.'"';
		$check = dbQuery($db, $sql);
		
		if (dbNumRows($check)) return true;

		return false;
	}
	
	/* Returns array of all history entries for specified subscription */
	function getSubscriptionHistory(&$db, $subscriptionType, $subscriptionId)
	{
		if (!is_numeric($subscriptionType) || !is_numeric($subscriptionId)) return false;
		
		$sql  = 'SELECT t1.* FROM tblSubscriptionsHistory AS t1 ';
		$sql .= 'INNER JOIN tblSubscriptions AS t2 ON (t1.subscriptionId=t2.subscriptionId) ';
		$sql .= 'WHERE t2.subscriptionType='.$subscriptionType.' AND t1.subscriptionId='.$subscriptionId.' ORDER BY t1.timeCreated ASC';
		
		return dbArray($db, $sql);		
	}
	
	
	//Helper function: calls class.phpmailer.php functions
	function smtp_auth_send_multiple($mails, $subject, $body)
	{
		global $config;
		
		$mail = new PHPMailer();
		
		$mail->IsSMTP();                                // send via SMTP
		$mail->Host     = $config['smtp']['host']; 			// SMTP servers
		$mail->SMTPAuth = true;    											// turn on SMTP authentication
		$mail->Username = $config['smtp']['username'];	// SMTP username
		$mail->Password = $config['smtp']['password'];	// SMTP password
		$mail->CharSet  = 'utf-8';

		$mail->From     = $config['smtp']['sender'];
		$mail->FromName = $config['smtp']['sender_name'];

		foreach ($mails as $adr) {
			$mail->AddAddress($adr);
		}

		$mail->IsHTML(true);                   					// send as HTML

		//Embed graphics
		$mail->AddEmbeddedImage($config['smtp']['mail_footer'], 'ai_logo', '', 'base64', 'image/png');

		$mail->Subject  = $subject;
		$mail->Body     = $body;

		if (!$mail->Send()) {
			echo 'Failed to send mail, error:'.$mail->ErrorInfo;
			return false;
		}

		return true;
	}
?>