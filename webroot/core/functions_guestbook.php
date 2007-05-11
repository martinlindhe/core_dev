<?
	function addGuestbookEntry($ownerId, $subject, $body)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($ownerId)) return false;

		/* Strip all html */
		$subject = $db->escape(strip_tags($subject));
		$body    = $db->escape(strip_tags($body));

		if (!$body) return false;

		$q = 'INSERT INTO tblGuestbooks SET userId='.$ownerId.',authorId='.$session->id.',subject="'.$subject.'",body="'.$body.'",timeCreated=NOW()';
		$db->query($q);
		$entryId = $db->insert_id;

		/* Add entry to moderation queue */
		if (isSensitive($subject) || isSensitive($body)) {
			addToModerationQueue($entryId, MODERATION_SENSITIVE_GUESTBOOK);
		}
	}
	
	function removeGuestbookEntry($entryId)
	{
		global $db;

		if (!is_numeric($entryId)) return false;

		$q = 'UPDATE tblGuestbooks SET entryDeleted=1,timeDeleted=NOW() WHERE entryId='.$entryId;
		$db->query($q);
	}

	/* Return $userId's guestbook entries, if $page is omitted the full guestbook is returned */
	function getGuestbook($userId, $page = '')
	{
		global $db;

		if (!is_numeric($userId)) return false;

		$q  = 'SELECT t1.*,t2.userName AS authorName ';
		$q .= 'FROM tblGuestbooks AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$q .= 'WHERE t1.userId='.$userId.' ';
		$q .= 'AND t1.entryDeleted=0 ';
		$q .= 'ORDER BY t1.timeCreated DESC';
		/*
		if (is_numeric($page)) {
			$sql .= ' LIMIT ' . ($config['guestbook']['items_per_page'] * ($page-1)). ','. $config['guestbook']['items_per_page'];
		}*/

		return $db->getArray($q);
	}
	
	/* Returns $count last entries from $userId's guestbook */
	function getGuestbookItems($userId, $count = 5)
	{
		global $db;

		if (!is_numeric($userId) || !is_numeric($count)) return false;

		$q  = 'SELECT t1.*,t2.userName AS authorName ';
		$q .= 'FROM tblGuestbooks AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$q .= 'WHERE t1.userId='.$userId.' ';
		$q .= 'AND t1.entryDeleted=0 ';
		$q .= 'ORDER BY t1.timeCreated DESC';
		$q .= ' LIMIT 0,'.$count;

		return $db->getArray($q);
	}

	/* Returns one specific guestbook entry */
	function getGuestbookItem($entryId)
	{
		global $db;

		if (!is_numeric($entryId)) return false;
		
		$q  = 'SELECT t1.*,t2.userName AS authorName,t3.userName FROM tblGuestbooks AS t1 ';
		$q .= 'LEFT OUTER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$q .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t1.userId=t3.userId) ';
		$q .= 'WHERE entryId='.$entryId;

		return $db->getOneRow($q);
	}

	
	/* Returns the number of items in the guestbook */
	function getGuestbookSize($userId)
	{
		global $db;

		if (!is_numeric($userId)) return false;

		$q = 'SELECT COUNT(entryId) FROM tblGuestbooks WHERE userId='.$userId.' AND entryDeleted=0';
		return $db->getOneItem($q);
	}
	
	/* Markerar alla inlägg i gästboken som lästa */
	function markGuestbookRead()
	{	
		global $db, $session;

		if (!$session->id) return false;

		$q = 'UPDATE tblGuestbooks SET entryRead=1,timeRead=NOW() WHERE entryRead=0 AND userId='.$session->id;
		$db->query($q);
	}
	
	function getNewGuestbookCount($userId)
	{
		global $db;

		if (!is_numeric($userId)) return false;
		
		$q = 'SELECT COUNT(entryId) FROM tblGuestbooks WHERE userId='.$userId.' AND entryRead=0';
		return $db->getOneItem($q);
	}
?>