<?
	function addChatEntry(&$db, $roomId, $text)
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($roomId)) return false;
		
		//Sanitize text
		$text = str_replace(']]>', '', $text);	//Guard against XML feed poisoning
		$text = dbAddSlashes($db, $text);

		$sql = 'INSERT INTO tblChat SET userId='.$_SESSION['userId'].',timeCreated='.time().',roomId='.$roomId.',msg="'.$text.'"';

		dbQuery($db, $sql);
	}

	/* Returns the last $limit chat entries from chat room $roomId */
	function getChatEntries(&$db, $roomId, &$lastEntryId, $limit = 10)
	{
		if (!is_numeric($roomId) || !is_numeric($limit)) return false;
		
		$sql = 'SELECT * FROM tblChat WHERE roomId='.$roomId.' ORDER BY entryId DESC LIMIT 0,1';
		$lastEntryId = dbOneResultItem($db, $sql);

		$sql = 'SELECT t1.timeCreated,t1.msg,t1.userId,t2.userName FROM tblChat AS t1 '.
					'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) '.
					'WHERE t1.roomId='.$roomId.' '.
					'ORDER BY t1.timeCreated DESC '.
					'LIMIT 0,'.$limit;

		return dbArray($db, $sql);
	}
	
	/* Returns all chat entries from $fromTime and newer */
	function getNewChatEntries(&$db, $roomId, &$lastEntryId, $fromEntryId)
	{
		if (!is_numeric($roomId) || !is_numeric($fromEntryId)) return false;

		$sql = 'SELECT * FROM tblChat WHERE roomId='.$roomId.' ORDER BY entryId DESC LIMIT 0,1';
		$lastEntryId = dbOneResultItem($db, $sql);

		$sql = 'SELECT t1.timeCreated,t1.msg,t1.userId,t2.userName FROM tblChat AS t1 '.
					'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) '.
					'WHERE t1.roomId='.$roomId.' AND t1.entryId > '.$fromEntryId.
					' AND t1.userId != '.$_SESSION['userId'];
		$sql .= ' ORDER BY t1.timeCreated DESC';

		return dbArray($db, $sql);
	}

	function newChatRoom(&$db, $roomName)
	{
		$roomName = trim($roomName);
		if (!$roomName) return false;
		$roomName = dbAddSlashes($db, $roomName);
		
		$sql = 'INSERT INTO tblChatRooms SET roomName="'.$roomName.'",timeCreated='.time().',createdBy='.$_SESSION['userId'];
		dbQuery($db, $sql);
		
		return $db['insert_id'];
	}
	
	function getChatRooms(&$db)
	{
		$sql = 'SELECT * FROM tblChatRooms ORDER BY roomName ASC';
		return dbArray($db, $sql);
	}
	
	function getChatRoom(&$db, $roomId)
	{
		if (!is_numeric($roomId)) return false;
		
		$sql = 'SELECT * FROM tblChatRooms WHERE roomId='.$roomId;
		return dbOneResult($db, $sql);
	}
	
	function setChatRoomName(&$db, $roomId, $roomName)
	{
		if (!is_numeric($roomId)) return false;
		
		$roomName = trim($roomName);
		if (!$roomName) return false;
		$roomName = dbAddSlashes($db, $roomName);

		$sql = 'UPDATE tblChatRooms SET roomName="'.$roomName.'" WHERE roomId='.$roomId;
		dbQuery($db, $sql);
	}

	function deleteChatRoom(&$db, $roomId)
	{
		if (!is_numeric($roomId)) return false;

		emptyChatRoomBuffer($db, $roomId);

		$sql = 'DELETE FROM tblChatRooms WHERE roomId='.$roomId;
		dbQuery($db, $sql);
	}

	function emptyChatRoomBuffer(&$db, $roomId)
	{
		if (!is_numeric($roomId)) return false;

		$sql = 'DELETE FROM tblChat WHERE roomId='.$roomId;
		dbQuery($db, $sql);
	}

	/* Updates tblChatUsers with current information of chat room members */
	function setChatRoomUser(&$db, $roomId, $userId)
	{
		//todo: detta fragmenterar säkerligen tabellen som fan eftersom det uppdateras så ofta, vore nog bättre att återanvända entryId's
		if (!is_numeric($roomId) || !is_numeric($userId)) return false;
		
		$sql = 'DELETE FROM tblChatUsers WHERE roomId='.$roomId.' AND userId='.$userId;
		dbQuery($db, $sql);
		
		$sql = 'INSERT INTO tblChatUsers SET roomId='.$roomId.',userId='.$userId.',lastSeen='.time();
		dbQuery($db, $sql);
	}

	function getCurrentChatUsers(&$db, $roomId)
	{
		global $config;

		if (!is_numeric($roomId)) return false;

		$sql =	'SELECT t1.userId, t2.userName FROM tblChatUsers AS t1 '.
						'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) '.
						'WHERE t1.roomId='.$roomId.' AND t1.lastSeen >= '.(time()-$config['chat']['idle_timeout']);
		if ($_SESSION['loggedIn']) $sql .= ' AND t1.userId!='.$_SESSION['userId'];

		return dbArray($db, $sql);
	}
?>