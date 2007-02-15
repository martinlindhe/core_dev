<?
	/*
		functions_polls.php - Funktioner för omröstningar
	*/

	define('POLL_USER',		0);
	define('POLL_SITE',		1);
	define('POLL_GROUP',	2);


	function addSitePoll(&$db, $parentId, $text, $starttime = '', $length = '')
	{
		if ($parentId) { //only use these for root item
			$starttime = 0;
			$length    = 0;
		}
		return addPoll($db, POLL_SITE, 0, $parentId, $text, $starttime, $length);
	}
	
	function addPoll(&$db, $itemType, $ownerId, $parentId, $text, $starttime = 0, $length = 0)
	{
		if (!is_numeric($itemType) || !is_numeric($ownerId) || !is_numeric($parentId) || !is_numeric($starttime) || !is_numeric($length)) return false;

		$text = dbAddSlashes($db, trim($text));

		$sql = 'INSERT INTO tblPolls SET parentId='.$parentId.',ownerId='.$ownerId.',itemType='.$itemType.',itemText="'.$text.'",timeStart='.$starttime.',timeLength='.$length;
		$query = dbQuery($db, $sql);
		return $db['insert_id'];
	}
	
	function getSitePolls(&$db)
	{
		return getPolls($db, POLL_SITE, 0);
	}
	
	function getPolls(&$db, $itemType, $ownerId)
	{
		if (!is_numeric($itemType) || !is_numeric($ownerId)) return false;		

		/* Get all questions */
		$sql = 'SELECT * FROM tblPolls WHERE itemType='.$itemType.' AND ownerId='.$ownerId.' AND parentId=0 ORDER BY timeStart ASC,itemText ASC';
		$list = dbArray($db, $sql);
		
		/* Get all response alternatives */
		for ($i=0; $i<count($list); $i++) {
			$sql = 'SELECT * FROM tblPolls WHERE parentId='.$list[$i]['itemId'];
			$list[$i]['alt'] = dbArray($db, $sql);
		}

		return $list;
	}
	
	function removeSitePoll(&$db, $itemId)
	{
		removePoll($db, 0, $itemId);
	}
	
	function removePoll(&$db, $ownerId, $itemId)
	{
		if (!is_numeric($ownerId) || !is_numeric($itemId)) return false;

		dbQuery($db, 'DELETE FROM tblPolls WHERE ownerId='.$ownerId.' AND (itemId='.$itemId.' OR parentId='.$itemId.')');
	}

	/* Returnerar alla sitepolls som är aktiva för tillfället och som $userId (om inloggad) inte har röstat i */
	function getActiveSitePolls(&$db, $userId)
	{
		//if (!is_numeric($userId)) return false; //ignorerad

		$sql  = 'SELECT itemId,itemText,timeStart,timeLength FROM tblPolls ';
		$sql .= 'WHERE itemType='.POLL_SITE.' AND parentId=0 ';
		$sql .= 'AND '.mktime(0,0,0,date('m'),date('d'),date('Y')).'>=timeStart ';
		$sql .= 'AND '.time().' < (timeStart + ((timeLength*60)*60))';
		$list = dbArray($db, $sql);
		
		/* Get all response alternatives */
		for ($i=0; $i<count($list); $i++) {
			$sql = 'SELECT * FROM tblPolls WHERE parentId='.$list[$i]['itemId'];
			$list[$i]['alt'] = dbArray($db, $sql);
		}
		
		return $list;
	}
	
	/* Returnerar alla sitepolls som har expirat */
	function getOldSitePolls(&$db)
	{
		$sql  = 'SELECT itemId,itemText,timeStart,timeLength FROM tblPolls ';
		$sql .= 'WHERE itemType=1 AND parentId=0 ';
		$sql .= 'AND timeStart+((timeLength*60)*60) < '.time().' ';
		$sql .= 'ORDER BY timeStart DESC';
		$list = dbArray($db, $sql);
		
		/* Get all response alternatives */
		for ($i=0; $i<count($list); $i++) {
			$sql = 'SELECT * FROM tblPolls WHERE parentId='.$list[$i]['itemId'];
			$list[$i]['alt'] = dbArray($db, $sql);
		}
		
		return $list;
	}

	/* Returns the results from the poll so far */
	function getPollResults(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;

		$sql = 'SELECT itemText,voteCnt FROM tblPolls WHERE parentId='.$itemId;
		return dbArray($db, $sql);
	}

	function addSitePollVote(&$db, $userId, $itemId, $voteId)
	{
		if (!is_numeric($itemId) || !is_numeric($voteId)) return false;

		if ($userId && is_numeric($userId)) {
			//todo: går att ersätta med replace ?
			$check = dbQuery($db, 'SELECT userId FROM tblPollVotes WHERE itemId='.$itemId.' AND userId='.$userId );
			if (dbNumRows($check)) return false;
			
			dbQuery($db, 'INSERT INTO tblPollVotes SET userId='.$userId.',itemId='.$itemId.',voteId='.$voteId );
		}
		
		dbQuery($db, 'UPDATE tblPolls SET voteCnt=voteCnt+1 WHERE itemId='.$voteId );
		return true;
	}
	
	/* Returns true if user has already voted here */
	function hasUserVoted(&$db, $userId, $voteId)
	{
		if (!is_numeric($userId) || !is_numeric($voteId)) return false;

		$sql = 'SELECT itemId FROM tblPollVotes WHERE userId='.$userId.' AND itemId='.$voteId;
		$check = dbQuery($db, $sql);

		if (dbNumRows($check)) return true;
		return false;
	}
	
?>