<?
	//messaging module settings:
	$config['messages']['enabled'] = false;		//module enabled?
	$config['messages']['items_per_page']	= 5;
	$config['messages']['maxsize_body'] = 1000;		//max length of instant messages
	$config['messages']['system_id']		= 0;			//userId to display as sender for system messages, use 0 for "System message"
	$config['messages']['folder_inbox']		= 'Inkorgen';
	$config['messages']['folder_outbox']	= 'Utkorgen';


	/* Instant-message folders */
	define('MESSAGE_FOLDER_STATIC',		1);
	define('MESSAGE_FOLDER_USERMADE',	2);
	
	define('MESSAGE_UNREAD',			10);
	define('MESSAGE_READ',				11);
	define('MESSAGE_REPLIED',			12);
	
	/* Type of the message stored in mailbox */
	define('MESSAGETYPE_INSTANT',		1);
	define('MESSAGETYPE_SMS',			2);

	define('FOLDER_MOVECONTENT',		1);
	define('FOLDER_DELETECONTENT',		2);


	/* Returns the parent id of $folderId, 0 is the top level */
	function getMessageFolderParent(&$db, $userId, $folderId)
	{
		if (!is_numeric($userId) || !is_numeric($folderId)) return false;

		$sql = 'SELECT parentFolder FROM tblMessageFolders WHERE ownerId='.$userId.' AND folderId='.$folderId;
		return dbOneResultitem($db, $sql);
	}


	/* Sends a instant message from $fromId to $toId */
	function sendMessageInstant(&$db, $fromId, $toId, $subject, $message, $attachment = '')
	{
		global $config;
		
		if (!is_numeric($fromId) || !is_numeric($toId)) return false;

		$subject = dbAddSlashes($db, strip_tags($subject));
		$message = dbAddSlashes($db, strip_tags($message));
		if ($attachment) {
			echo 'No support for attachment!';
			die;
		}
		
		addMessageToOutbox($db, $fromId, $toId, $subject, $message, MESSAGETYPE_INSTANT);

		if ($toId == $config['messages']['system_id']) {

			$message .= "\n\nFrån ".getUserName($db,$fromId).", personlig sida:\n\n";
			$message .= $config['site_url']."/user_show.php?id=".$fromId."\n";

			sendMail($config['site_admin'], 'Community feedback', $message);
		} else {
			addMessageToInbox($db, $fromId, $toId, $subject, $message, MESSAGETYPE_INSTANT);
		}
	}

	/* Sends a instant message to a group of users */
	function sendMessageInstantToGroup(&$db, $fromId, $groupId, $subject, $message)
	{
		$list = getUserFriends($db, $fromId, $groupId);

		for ($i=0; $i<count($list); $i++) {
			sendMessageInstant($db, $fromId, $list[$i]['friendId'], $subject, $message);
		}
	}

	/* Sends a instant message to all $fromId's friends */
	function sendMessageInstantToFriends(&$db, $fromId, $subject, $message)
	{
		$list = getUserFriends($db, $fromId);

		for ($i=0; $i<count($list); $i++) {
			sendMessageInstant($db, $fromId, $list[$i]['friendId'], $subject, $message);
		}
	}

	function sendMessageInstantToFriendsOnline(&$db, $fromId, $subject, $message)
	{
		$list = getFriendsOnline($db, $fromId);

		for ($i=0; $i<count($list); $i++) {
			sendMessageInstant($db, $fromId, $list[$i]['friendId'], $subject, $message);
		}
	}




	function sendMessageSMS(&$db, $fromId, $phoneId, $message)
	{
		$number = getPhonenumber($db, $fromId, $phoneId);

		sendSMS($number, $message);

		addMessageToOutbox($db, $fromId, $phoneId, '', $message, MESSAGETYPE_SMS);
	}

	/* Used for system functionality, like reminders, password retrival etc */
	function sendSMS($phoneNumber, $message)
	{
		return wip_sendSMS('senderNAME', $phoneNumber, $message);
	}

	/* Used for system functionality, like reminders, password retrival etc */
	function sendMail($address, $subject, $message, $frommail = '')
	{
		if (!$address || !$subject || !$message) {
			return false;
		}
		
		if ($frommail == '') {
			$frommail = MAIL_NOTIFICATION_SENDER;
		}

		/* Verify that $address is a valid email address */
		if (!ValidEmail($address)) {
			return false;
		}

		$head = "From: ".$frommail."\r\n";
		return @mail($address, $subject, $message, $head);
	}


	/* Lägg till till meddelande-history */
	function addMessageToOutbox(&$db, $fromId, $toId, $subject, $message, $message_type)
	{
		global $config;
		
		if (!is_numeric($fromId) || !is_numeric($toId) || !is_numeric($message_type)) return false;
		$subject = dbAddSlashes($db, $subject);
		$message = dbAddSlashes($db, $message);

		$folderId = getMessageFolderId($db, $fromId, $config['messages']['folder_outbox']);
		$sql = 'INSERT INTO tblMessages SET messageOwner='.$fromId.',messageFolder='.$folderId.',messageSender='.$fromId.',messageReceiver='.$toId.',messageSubject="'.$subject.'",messageBody="'.$message.'",messageStatus='.MESSAGE_UNREAD.',timestamp='.time().',messageType='.$message_type;

		dbQuery($db, $sql);
	}

	function addMessageToInbox(&$db, $fromId, $toId, $subject, $message, $message_type)
	{
		global $config;

		if (!is_numeric($fromId) || !is_numeric($toId) || !is_numeric($message_type)) return false;
		$subject = dbAddSlashes($db, $subject);
		$message = dbAddSlashes($db, $message);

		$folderId = getMessageFolderId($db, $toId, $config['messages']['folder_inbox']);
		$sql = 'INSERT INTO tblMessages SET messageOwner='.$toId.',messageFolder='.$folderId.',messageSender='.$fromId.',messageReceiver='.$toId.',messageSubject="'.$subject.'",messageBody="'.$message.'",messageStatus='.MESSAGE_UNREAD.',timestamp='.time().',messageType='.$message_type;

		dbQuery($db, $sql);
	}

	function getMessageFolderId(&$db, $userId, $folderName)
	{
		if (!is_numeric($userId)) return false;
		$folderName = dbAddSlashes($db, $folderName);

		$sql = 'SELECT folderId FROM tblMessageFolders WHERE folderName="'.$folderName.'" AND ownerId='.$userId;
		return dbOneResultItem($db, $sql);
	}

	function getMessageFolderName(&$db, $userId, $folderId)
	{
		if (!is_numeric($userId) || !is_numeric($folderId)) return false;

		$sql = 'SELECT folderName FROM tblMessageFolders WHERE folderId='.$folderId.' AND ownerId='.$userId;
		return dbOneResultItem($db, $sql);
	}

	/* Creates a instant message-folder, if parentId is ommited, it's created under the root level */
	function addUserMessageFolder(&$db, $userId, $folderName, $folderType, $parentId = 0)
	{
		if (!is_numeric($userId) || !is_numeric($folderType) || !is_numeric($parentId)) return false;
		$folderName = dbAddSlashes($db, $folderName);

		$sql = 'SELECT folderId FROM tblMessageFolders WHERE folderName="'.$folderName.'" AND ownerId='.$userId;
		$check = dbQuery($db, $sql);
		if (dbNumRows($check)) return false;

		$sql = 'INSERT INTO tblMessageFolders SET folderName="'.$folderName.'",ownerId='.$userId.',folderType='.$folderType.',parentFolder='.$parentId.',timestamp='.time();
		dbQuery($db, $sql);
		return true;
	}

	/* Returns an array with all the users folders + number of messages in each */
	function getUserMessageFolders(&$db, $userId, $parentId = 0)
	{
		if (!is_numeric($userId) || !is_numeric($parentId)) return false;

		$sql  = 'SELECT tblMessageFolders.*, COUNT(tblMessages.messageId) AS messageCount ';
		$sql .= 'FROM tblMessageFolders ';
		$sql .= 'LEFT OUTER JOIN tblMessages ON (tblMessageFolders.folderId = tblMessages.messageFolder) ';
		$sql .= 'WHERE tblMessageFolders.ownerId='.$userId.' ';
		$sql .= 'AND (tblMessages.messageOwner='.$userId.' OR tblMessages.messageOwner IS NULL) ';
		$sql .= 'AND (tblMessages.messageDeleted=0 OR tblMessages.messageDeleted IS NULL) ';
		$sql .= 'AND tblMessageFolders.parentFolder='.$parentId.' ';
		$sql .= 'GROUP BY tblMessageFolders.folderId ';
		$sql .= 'ORDER BY tblMessageFolders.folderType ASC, tblMessageFolders.folderName ASC';

		return dbArray($db, $sql);
	}

	/* Raderar en ANVÄNDARSKAPAD, systemfoldrar måste finnas där folder */
	function removeUserMessageFolder(&$db, $userId, $folderId, $mode = FOLDER_MOVECONTENT)
	{
		if (!is_numeric($userId) || !is_numeric($folderId) || !is_numeric($mode)) return false;

		$sql = 'SELECT folderType FROM tblMessageFolders WHERE ownerId='.$userId.' AND folderId='.$folderId;
		$row = dbOneResult($db, $sql);

		if ($row['folderType'] != MESSAGE_FOLDER_USERMADE) return false;

		$sql = 'DELETE FROM tblMessageFolders WHERE ownerId='.$userId.' AND folderId='.$folderId.' AND folderType='.MESSAGE_FOLDER_USERMADE;
		dbQuery($db, $sql);

		if ($mode == FOLDER_MOVECONTENT) {
			dbQuery($db, 'UPDATE tblMessages SET messageFolder=0 WHERE messageOwner='.$userId.' AND messageFolder='.$folderId);
		} else {
			dbQuery($db, 'UPDATE tblMessages SET messageDeleted=1 WHERE messageOwner='.$userId.' AND messageFolder='.$folderId);
		}
		return true;
	}

	/* Marks a message as deleted */
	function removeUserMessage(&$db, $userId, $messageId)
	{
		if (!is_numeric($userId) || !is_numeric($messageId)) return false;

		$sql = 'UPDATE tblMessages SET messageDeleted=1 WHERE messageId='.$messageId.' AND messageOwner='.$userId;
		dbQuery($db, $sql);

		return true;
	}

	/* Renames a *USERMADE* folder */
	function renameMessageFolder(&$db, $userId, $folderId, $newname)
	{
		if (!is_numeric($userId) || !is_numeric($folderId)) return false;
		$newname = dbAddSlashes($db, $newname);

		$sql = 'SELECT folderType FROM tblMessageFolders WHERE ownerId='.$userId.' AND folderId='.$folderId;
		$row = dbOneResult($db, $sql);

		if ($row['folderType'] == MESSAGE_FOLDER_USERMADE) {
			dbQuery($db, 'UPDATE tblMessageFolders SET folderName="'.$newname.'" WHERE ownerId='.$userId.' AND folderId='.$folderId );
			return true;
		}
		return false;
	}

	/* Flyttar ett meddelande till en ny folder */
	function moveMessageToFolder(&$db, $userId, $folderId, $messageId)
	{
		if (!is_numeric($userId) || !is_numeric($folderId) || !is_numeric($messageId)) return false;

		$sql = 'UPDATE tblMessages SET messageFolder='.$folderId.' WHERE messageOwner='.$userId.' AND messageId='.$messageId;
		dbQuery($db, $sql);
	}

	/* Returns the number of messages in $folderId */
	function getFolderMessageCount(&$db, $userId, $folderId)
	{
		if (!is_numeric($userId) || !is_numeric($folderId)) return false;

		$sql  = 'SELECT COUNT(timestamp) FROM tblMessages ';
		$sql .= 'WHERE messageOwner='.$userId.' ';
		$sql .= 'AND messageFolder='.$folderId.' ';
		$sql .= 'AND messageDeleted=0 ORDER BY timestamp DESC';

		return dbOneResultItem($db, $sql);
	}

	/* Returns all messages in the folder folderId */
	function getFolderMessages(&$db, $userId, $folderId, $page = '')
	{
		global $config;

		if (!is_numeric($userId) || !is_numeric($folderId)) return false;

		$sql  = 'SELECT tblMessages.*, tblUsers.userName AS otherName, tblQuicklists.data AS isFriend ';
		$sql .= 'FROM tblMessages ';
		$sql .= 'LEFT OUTER JOIN tblUsers      ON ( IF(tblMessages.messageSender=tblMessages.messageOwner,tblMessages.messageReceiver,tblMessages.messageSender) = tblUsers.userId) ';
		$sql .= 'LEFT OUTER JOIN tblQuicklists ON ( IF(tblMessages.messageSender=tblMessages.messageOwner,tblMessages.messageReceiver,tblMessages.messageSender) = tblQuicklists.data) ';
 		$sql .= 'WHERE tblMessages.messageOwner='.$userId.' ';
		$sql .= 'AND tblMessages.messageFolder='.$folderId.' ';
		$sql .= 'AND tblMessages.messageDeleted=0 ';
		$sql .= 'GROUP BY tblMessages.messageId ';
		$sql .= 'ORDER BY tblMessages.timestamp DESC';
		if (is_numeric($page)) {
			$sql .= ' LIMIT ' . ($config['messages']['items_per_page'] * ($page-1)). ','. $config['messages']['items_per_page'];
		}

		return dbArray($db, $sql);
	}

	/* Returns an array with the message history between $userId and $otherId, from $userId's messagebox(es) */
	function getMessageHistory(&$db, $userId, $otherId, $page = '')
	{
		global $config;

		if (!is_numeric($userId) || !is_numeric($otherId)) return false;

		$sql  = 'SELECT t1.*,t2.userName AS senderName, t3.userName AS receiverName ';
		$sql .= 'FROM tblMessages AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblUsers AS t2 ON (t1.messageSender=t2.userId) ';
		$sql .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t1.messageReceiver=t3.userId) ';
		$sql .= 'WHERE t1.messageOwner='.$userId.' AND (t1.messageSender='.$otherId.' OR t1.messageReceiver='.$otherId.') ';
		$sql .= 'AND messageDeleted=0 ';
		$sql .= 'ORDER BY t1.timestamp DESC';
		
		if (is_numeric($page)) {
			$sql .= ' LIMIT ' . ($config['messages']['items_per_page'] * ($page-1)). ','. $config['messages']['items_per_page'];
		}

		return dbArray($db, $sql);
	}

	/* Return the number of messages in history between $userId and $otherId */
	function getMessageHistoryCount(&$db, $userId, $otherId)
	{
		if (!is_numeric($userId) || !is_numeric($otherId)) return false;

		$sql  = 'SELECT COUNT(timestamp) FROM tblMessages ';
		$sql .= 'WHERE messageOwner='.$userId.' ';
		$sql .= 'AND (messageSender='.$otherId.' OR messageReceiver='.$otherId.') ';
		$sql .= 'AND messageDeleted=0 ORDER BY timestamp DESC';

		return dbOneResultItem($db, $sql);
	}

	/* Returns an array with all unread messages */
	function getUserNewMessages(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql  = 'SELECT tblMessages.*, tblUsers.userName AS otherName ';
		$sql .= 'FROM tblMessages ';
		$sql .= 'LEFT OUTER JOIN tblUsers ON (tblMessages.messageSender = tblUsers.userId) ';
		$sql .= 'WHERE tblMessages.messageOwner='.$userId.' ';
		$sql .= 'AND tblMessages.messageReceiver='.$userId.' ';
		$sql .= 'AND tblMessages.messageDeleted=0 ';
		$sql .= 'AND tblMessages.messageStatus='.MESSAGE_UNREAD.' ';
		$sql .= 'ORDER BY tblMessages.timestamp DESC';

		return dbArray($db, $sql);
	}

	function userHasNewMessages(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql = 'SELECT messageId FROM tblMessages WHERE messageOwner='.$userId.' AND messageReceiver='.$userId.' AND messageDeleted=0 AND messageStatus='.MESSAGE_UNREAD;
		$check = dbQuery($db, $sql);

		if (dbNumRows($check)) return true;
		return false;
	}

	function markMessageAsRead(&$db, $userId, $messageId)
	{
		if (!is_numeric($userId) || !is_numeric($messageId)) return false;

		$sql = 'UPDATE tblMessages SET messageStatus='.MESSAGE_READ.' WHERE messageOwner='.$userId.' AND messageId='.$messageId;
		dbQuery($db, $sql);
	}

	/* Returns the $limit most active instant message senders */
	function getMostActiveMessagers(&$db, $limit = '')
	{
		$sql  = 'SELECT COUNT(tblMessages.messageId) AS cnt, tblMessages.messageOwner AS userId, tblUsers.userName AS userName ';
		$sql .= 'FROM tblMessages ';
		$sql .= 'INNER JOIN tblUsers ON (tblUsers.userId = tblMessages.messageOwner) ';
		$sql .= 'GROUP BY tblMessages.messageOwner ';
		$sql .= 'ORDER BY cnt DESC';
		if (is_numeric($limit)) {
			$sql .= ' LIMIT 0,'.$limit;
		}

		return dbArray($db, $sql);
	}

	/* Returnerar hur många nya meddelanden $userId har från $otherId */
	function getNewMessagesFromUserCount(&$db, $userId, $otherId)
	{
		if (!is_numeric($userId) || !is_numeric($otherId)) return false;

		$sql  = 'SELECT COUNT(messageId) FROM tblMessages ';
		$sql .= 'WHERE messageOwner='.$userId.' AND messageReceiver='.$userId.' ';
		$sql .= 'AND messageSender='.$otherId.' AND messageDeleted=0 ';
		$sql .= 'AND messageStatus='.MESSAGE_UNREAD;

		return dbOneResultItem($db, $sql);
	}

	/* Returnerar hur många nya meddelanden som finns */
	function getNewMessagesCount(&$db, $userId, $multiple_text = '', $one_text = '')
	{
		if (!is_numeric($userId)) return false;

		$sql  = 'SELECT COUNT(messageId) FROM tblMessages ';
		$sql .= 'WHERE messageOwner='.$userId.' AND messageReceiver='.$userId.' ';
		$sql .= 'AND messageDeleted=0 ';
		$sql .= 'AND messageStatus='.MESSAGE_UNREAD;

		$cnt = dbOneResultItem($db, $sql);
		
		if ($multiple_text && $one_text) {
			if ($cnt == 1) return $cnt.' '.$one_text;
			return $cnt.' '.$multiple_text;
		}

		return $cnt;
	}
	
	/* Returnerar id & namn på alla användare som jag har nya meddelanden från men som INTE e på min kompislista */
	function getNewMessagesFromNonFriends(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql  = 'SELECT t1.messageSender AS userId, t3.userName ';
		$sql .= 'FROM tblMessages AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblQuicklists AS t2 ON (t2.userId='.$userId.' AND t2.data = t1.messageSender) ';
		$sql .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t3.userId = t1.messageSender) ';
		$sql .= 'WHERE messageOwner='.$userId.' AND messageReceiver='.$userId.' ';
		$sql .= 'AND messageDeleted=0 AND messageStatus='.MESSAGE_UNREAD.' AND t2.data IS NULL ';
		$sql .= 'GROUP BY userId';

		return dbArray($db, $sql);
	}
	
?>