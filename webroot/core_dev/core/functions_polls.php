<?
	/* functions_polls.php - implements a couple of different types of polling functionality
		by Martin Lindhe, 2006-2007
	*/

	define('POLL_SITE',		1);	//"Question of the week"-style polls on the site's front page (for example)
	//define('POLL_USER',		2);
	//define('POLL_GROUP',	3);


	function addPoll($itemType, $ownerId, $parentId, $text, $starttime = '', $length = 0)
	{
		global $db;

		if (!is_numeric($itemType) || !is_numeric($ownerId) || !is_numeric($parentId) || !is_numeric($starttime) || !is_numeric($length)) return false;

		$text = $db->escape(trim($text));
		
		$starttime = sql_datetime(strtotime($starttime));

		$q = 'INSERT INTO tblPolls SET parentId='.$parentId.',ownerId='.$ownerId.',itemType='.$itemType.',itemText="'.$text.'",timeStart="'.$starttime.'",timeLength='.$length;
		return $db->insert($q);
	}
	
	function getPolls($itemType, $ownerId)
	{
		global $db;

		if (!is_numeric($itemType) || !is_numeric($ownerId)) return false;		

		/* Get all questions */
		$q = 'SELECT * FROM tblPolls WHERE itemType='.$itemType.' AND ownerId='.$ownerId.' AND parentId=0 ORDER BY timeStart ASC,itemText ASC';
		$list = $db->getArray($q);
		
		/* Get all response alternatives */
		for ($i=0; $i<count($list); $i++) {
			$q = 'SELECT * FROM tblPolls WHERE parentId='.$list[$i]['itemId'];
			$list[$i]['alt'] = $db->getArray($q);
		}

		return $list;
	}
	
	function removePoll($ownerId, $itemId)
	{
		global $db;
		if (!is_numeric($ownerId) || !is_numeric($itemId)) return false;

		$db->delete('DELETE FROM tblPolls WHERE ownerId='.$ownerId.' AND (itemId='.$itemId.' OR parentId='.$itemId.')');
	}

	/* Returns the results from the poll so far */
	function getPollResults($itemId)
	{
		global $db;
		if (!is_numeric($itemId)) return false;

		$q = 'SELECT itemText,voteCnt FROM tblPolls WHERE parentId='.$itemId;
		return $db->getArray($q);
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
	
	/* Returns true if user has already voted here */
	function hasUserVoted($userId, $voteId)
	{
		global $db;
		if (!is_numeric($userId) || !is_numeric($voteId)) return false;

		$q = 'SELECT itemId FROM tblPollVotes WHERE userId='.$userId.' AND itemId='.$voteId;
		$check = $db->getOneItem($q);

		if ($check) return true;
		return false;
	}
	
?>