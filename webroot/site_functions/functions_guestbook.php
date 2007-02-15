<?
	//guestbook module settings:
	$config['guestbook']['items_per_page'] = 4;
	

	function addGuestbookEntry(&$db, $ownerId, $subject, $body)
	{
		if (!is_numeric($ownerId)) return false;

		/* Strip all html */
		$subject = dbAddSlashes($db, strip_tags($subject));
		$body    = dbAddSlashes($db, strip_tags($body));

		if (!$body) return false;

		$sql = 'INSERT INTO tblGuestbooks SET userId='.$ownerId.',authorId='.$_SESSION['userId'].',subject="'.$subject.'",body="'.$body.'",timestamp='.time().',entryDeleted=0';
		dbQuery($db, $sql);

		$entryId = $db['insert_id'];

		/* Add entry to moderation queue */
		if (isSensitive($db, $subject) || isSensitive($db, $body)) {
			addToModerationQueue($db, $entryId, MODERATION_SENSITIVE_GUESTBOOK);
		}
	}
	
	function removeGuestbookEntry(&$db, $entryId)
	{
		if (!is_numeric($entryId)) return false;

		$sql = 'UPDATE tblGuestbooks SET entryDeleted=1 WHERE entryId='.$entryId;
		dbQuery($db, $sql);
	}

	/* Return $userId's guestbook entries, if $page is omitted the full guestbook is returned */
	function getGuestbook(&$db, $userId, $page = '')
	{
		global $config;

		if (!is_numeric($userId)) return false;

		$sql  = 'SELECT t1.*,t2.userName AS authorName ';
		$sql .= 'FROM tblGuestbooks AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$sql .= 'WHERE t1.userId='.$userId.' ';
		$sql .= 'AND t1.entryDeleted=0 ';
		$sql .= 'ORDER BY t1.timestamp DESC';
		if (is_numeric($page)) {
			$sql .= ' LIMIT ' . ($config['guestbook']['items_per_page'] * ($page-1)). ','. $config['guestbook']['items_per_page'];
		}

		return dbArray($db, $sql);
	}
	
	/* Returns $count last entries from $userId's guestbook */
	function getGuestbookItems(&$db, $userId, $count = 5)
	{
		if (!is_numeric($userId) || !is_numeric($count)) return false;

		$sql  = 'SELECT t1.*,t2.userName AS authorName ';
		$sql .= 'FROM tblGuestbooks AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$sql .= 'WHERE t1.userId='.$userId.' ';
		$sql .= 'AND t1.entryDeleted=0 ';
		$sql .= 'ORDER BY t1.timestamp DESC';
		$sql .= ' LIMIT 0,'.$count;

		return dbArray($db, $sql);
	}

	/* Returns one specific guestbook entry */
	function getGuestbookItem(&$db, $entryId)
	{
		if (!is_numeric($entryId)) return false;
		
		$sql  = 'SELECT t1.*,t2.userName AS authorName,t3.userName FROM tblGuestbooks AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$sql .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t1.userId=t3.userId) ';
		$sql .= 'WHERE entryId='.$entryId;

		return dbOneResult($db, $sql);
	}

	
	/* Returns the number of items in the guestbook */
	function getGuestbookSize(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql = 'SELECT COUNT(entryId) FROM tblGuestbooks WHERE userId='.$userId.' AND entryDeleted=0';
		return dbOneResultItem($db, $sql);
	}
	
	/* Markerar alla inlägg i gästboken som lästa */
	function markGuestbookRead(&$db)
	{	
		if (!$_SESSION['userId']) return false;

		$sql = 'UPDATE tblGuestbooks SET entryRead=1 WHERE userId='.$_SESSION['userId'];
		dbQuery($db, $sql);
	}
	
	function getNewGuestbookCount(&$db, $userId, $multiple_text='', $one_text='')
	{
		if (!is_numeric($userId)) return false;
		
		$sql = 'SELECT COUNT(entryId) FROM tblGuestbooks WHERE userId='.$userId.' AND entryRead=0';
		$cnt = dbOneResultItem($db, $sql);

		if ($multiple_text && $one_text) {
			if ($cnt == 1) {
				return $cnt." ".$one_text;
			}
			return $cnt." ".$multiple_text;
		}
		
		return $cnt;
	}

?>