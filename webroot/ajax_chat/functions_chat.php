<?
	function addChatEntry($roomId, $text)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($roomId)) return false;
		
		//Sanitize text
		$text = str_replace(']]>', '', $text);	//Guard against XML feed poisoning
		$text = $db->escape($text);

		$q = 'INSERT INTO tblChat SET userId='.$session->id.',timeCreated=NOW(),roomId='.$roomId.',msg="'.$text.'"';
		$db->query($q);
	}

	/* Returns the last $limit chat entries from chat room $roomId */
	function getChatEntries($roomId, &$lastEntryId, $limit = 10)
	{
		global $db;

		if (!is_numeric($roomId) || !is_numeric($limit)) return false;
		
		$q = 'SELECT * FROM tblChat WHERE roomId='.$roomId.' ORDER BY entryId DESC LIMIT 0,1';
		$lastEntryId = $db->getOneItem($q);

		$q =	'SELECT t1.timeCreated,t1.msg,t1.userId,t2.userName FROM tblChat AS t1 '.
					'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) '.
					'WHERE t1.roomId='.$roomId.' '.
					'ORDER BY t1.timeCreated DESC '.
					'LIMIT 0,'.$limit;

		return $db->getArray($q);
	}
	
	/* Returns all chat entries from $fromTime and newer */
	function getNewChatEntries($roomId, &$lastEntryId, $fromEntryId)
	{
		global $db, $session;

		if (!is_numeric($roomId) || !is_numeric($fromEntryId)) return false;

		$q = 'SELECT * FROM tblChat WHERE roomId='.$roomId.' ORDER BY entryId DESC LIMIT 0,1';
		$lastEntryId = $db->getOneItem($q);

		$q =	'SELECT t1.timeCreated,t1.msg,t1.userId,t2.userName FROM tblChat AS t1 '.
					'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) '.
					'WHERE t1.roomId='.$roomId.' AND t1.entryId > '.$fromEntryId.
					' AND t1.userId != '.$session->id.
					' ORDER BY t1.timeCreated DESC';

		return $db->getArray($q);
	}

	function newChatRoom($roomName)
	{
		global $db, $session;

		$roomName = trim($roomName);
		if (!$roomName) return false;

		$q = 'INSERT INTO tblChatRooms SET roomName="'.$db->escape($roomName).'",timeCreated=NOW(),createdBy='.$session->id;

		return $db->insert($q);
	}
	
	function getChatRooms()
	{
		global $db;

		$q = 'SELECT * FROM tblChatRooms ORDER BY roomName ASC';
		return $db->getArray($q);
	}
	
	function getChatRoom($roomId)
	{
		global $db;

		if (!is_numeric($roomId)) return false;
		
		$q = 'SELECT * FROM tblChatRooms WHERE roomId='.$roomId;
		return $db->getOneRow($q);
	}

	function setChatRoomName($roomId, $roomName)
	{
		global $db;

		if (!is_numeric($roomId)) return false;
		
		$roomName = trim($roomName);
		if (!$roomName) return false;

		$q = 'UPDATE tblChatRooms SET roomName="'.$db->escape($roomName).'" WHERE roomId='.$roomId;
		$db->query($q);
	}

	function deleteChatRoom($roomId)
	{
		global $db;

		if (!is_numeric($roomId)) return false;

		emptyChatRoomBuffer($roomId);

		$q = 'DELETE FROM tblChatRooms WHERE roomId='.$roomId;
		$db->query($q);
	}

	function emptyChatRoomBuffer($roomId)
	{
		global $db;

		if (!is_numeric($roomId)) return false;

		$q = 'DELETE FROM tblChat WHERE roomId='.$roomId;
		$db->query($q);
	}

	/* Updates tblChatUsers with current information of chat room members */
	function setChatRoomUser($roomId)
	{
		global $db, $session;
		
		//todo: detta fragmenterar säkerligen tabellen som fan eftersom det uppdateras så ofta, vore nog bättre att återanvända entryId's
		if (!$session->id || !is_numeric($roomId)) return false;
		
		$q = 'DELETE FROM tblChatUsers WHERE roomId='.$roomId.' AND userId='.$session->id;
		$db->query($q);
		
		$q = 'INSERT INTO tblChatUsers SET roomId='.$roomId.',userId='.$session->id.',lastSeen=NOW()';
		$db->query($q);
	}

	function getCurrentChatUsers($roomId)
	{
		global $db, $session, $config;

		if (!is_numeric($roomId)) return false;

		$q =	'SELECT t1.userId, t2.userName FROM tblChatUsers AS t1 '.
					'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) '.
					'WHERE t1.roomId='.$roomId.' AND t1.lastSeen >= NOW()-'.$config['chat']['idle_timeout'];
		if ($session->id) $q .= ' AND t1.userId!='.$session->id;

		return $db->getArray($q);
	}
?>