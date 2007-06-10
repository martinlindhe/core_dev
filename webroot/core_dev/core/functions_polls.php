<?
	/* functions_polls.php - implements a couple of different types of polling functionality
		by Martin Lindhe, 2006-2007
	*/

	define('POLL_SITE',		1);	//"Question of the week"-style polls on the site's front page (for example)
	//define('POLL_USER',		2);
	//define('POLL_GROUP',	3);

	function addPoll($itemType, $ownerId, $text, $duration_mode)
	{
		global $db;

		if (!is_numeric($itemType) || !is_numeric($ownerId)) return false;

		$text = $db->escape(trim($text));
		
		switch ($duration_mode) {
			case 'day';
				$length = 1;
				break;

			case 'week':
				//week start at monday 00:00
				$length = 7;
				break;

			default: die('eep addpoll');
		}
		
		$q = 'SELECT timeEnd FROM tblPolls ORDER BY timeStart ASC LIMIT 1';
		$data = $db->getOneRow($q);
		if ($data) {
			$timeStart = ',timeStart="'.$data['timeEnd'].'"';
		} else {
			$timeStart = ',timeStart=NOW()';
		}
		$timeEnd = ',timeEnd=DATE_ADD(timeStart, INTERVAL '.$length.' DAY)';

		$q = 'INSERT INTO tblPolls SET ownerId='.$ownerId.',itemType='.$itemType.',itemText="'.$text.'"'.$timeStart.$timeEnd;
		return $db->insert($q);
	}


	/* get all polls */
	function getPolls($_type, $ownerId = 0)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($ownerId)) return false;		

		$q = 'SELECT * FROM tblPolls WHERE itemType='.$_type.' AND ownerId='.$ownerId.' ORDER BY timeStart ASC,itemText ASC';
		$list = $db->getArray($q);

		return $list;
	}

	/* Get active polls */
	function getActivePolls($_type, $ownerId = 0)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($ownerId)) return false;		

		$q = 'SELECT * FROM tblPolls WHERE itemType='.$_type.' AND ownerId='.$ownerId.' AND NOW() BETWEEN timeStart AND timeEnd ORDER BY timeStart ASC,itemText ASC';
		$list = $db->getArray($q);

		return $list;
	}

	function showPoll($row)
	{
		echo '<div class="item">';
		echo $row['itemText'].'<br/>';
		echo 'Starts: '.$row['timeStart'].', ends '.$row['timeEnd'].' '.getCategoriesSelect(CATEGORY_POLL, $row['pollId']);
		echo '</div>';
	}

	function showPolls($_type)
	{
		global $db;
		if (!is_numeric($_type)) return false;

		$list = getActivePolls($_type);
		
		foreach ($list as $row) {
			showPoll($row);
		}
	}

/*
	function removePoll($ownerId, $itemId)
	{
		global $db;
		if (!is_numeric($ownerId) || !is_numeric($itemId)) return false;

		$db->delete('DELETE FROM tblPolls WHERE ownerId='.$ownerId.' AND itemId='.$itemId);
	}

	function addSitePollVote($userId, $itemId, $voteId)
	{
		global $db;
		if (!is_numeric($itemId) || !is_numeric($voteId)) return false;

		if ($userId && is_numeric($userId)) {
			$check = $db->getOneItem($db, 'SELECT userId FROM tblPollVotes WHERE itemId='.$itemId.' AND userId='.$userId);
			if ($check) return false;
			$db->insert('INSERT INTO tblPollVotes SET userId='.$userId.',itemId='.$itemId.',voteId='.$voteId);
		}
		$db->query('UPDATE tblPolls SET voteCnt=voteCnt+1 WHERE itemId='.$voteId);
		return true;
	}

	function hasUserVoted($userId, $voteId)
	{
		global $db;
		if (!is_numeric($userId) || !is_numeric($voteId)) return false;

		$q = 'SELECT itemId FROM tblPollVotes WHERE userId='.$userId.' AND itemId='.$voteId;
		$check = $db->getOneItem($q);

		if ($check) return true;
		return false;
	}
*/
?>