<?

	function sendNewsletter($db, $subject, $body) {
		$list = getNewsLetterSubscribers($db);
		$bcc_mails = "";
		for ($i=0; $i<count($list); $i++) {
			$bcc_mails .= $list[$i]["userMail"].", ";
		}
		$bcc_mails = trim($bcc_mails);
		$bcc_mails = substr($bcc_mails, 0, -1); //remove last comma from $bcc_mails
		
		$sender_name = "Online Newsletter";
		$sender_email = "support@inthc.net";

		$headers  = "From: \"".addslashes($sender_name)."\" <".$sender_email.">\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/plain; charset=iso-8859-1\r\n";
		$headers .= "X-Priority: 1\r\n";
		$headers .= "Bcc: ".$bcc_mails."\r\n";

		mail("", $subject, $body, $headers); 

		$subject = addslashes($subject);
		$body = addslashes($body);
		$headers = addslashes($headers);
		dbQuery($db, "INSERT INTO tblNewsletters SET subject='".$subject."',body='".$body."',headers='".$headers."',timestamp=".time().",recievers=".count($list));
	}
	
	/* Returns a list with all people who is subscribed for the newsletter */
	function getNewsLetterSubscribers($db) {
		$sql = "SELECT userMail FROM tblUserAddress WHERE newsletter=1";
		return dbArray($db, $sql);
	}


	function getArchivedNewsletters($db) {
		$sql = "SELECT * FROM tblNewsletters ORDER BY timestamp ASC";
		return dbArray($db, $sql);
	}
	
	function getArchivedNewsletter($db, $itemId) {
		if (!is_numeric($itemId)) return false;
		$sql = "SELECT * FROM tblNewsletters WHERE itemId=".$itemId;
		$check = dbQuery($db, $sql);
		return dbFetchArray($check);
	}
	
?>