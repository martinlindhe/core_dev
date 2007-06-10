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

	function showPoll($data)
	{
		global $session;

		echo '<div class="item">';

		echo $data['itemText'].'<br/>';
		$list = getCategories(CATEGORY_POLL, $data['pollId']);

		echo '<div id="poll'.$data['pollId'].'">';
		if ($session->isAdmin) echo 'Starts: '.$data['timeStart'].', ends '.$data['timeEnd'].'<br/>';

		if (!hasAnsweredPoll($data['pollId'])) {
			foreach ($list as $row) {
				echo '<span onclick="submit_poll('.$data['pollId'].','.$row['categoryId'].')">';
				echo $row['categoryName'];
				echo '</span><br/>';
			}
		} else {
			echo 'You already voted, showing current standings:<br/>';

			$votes = getPollStats($data['pollId']);
			d($votes);
		}
		echo '</div>';

		echo '<div id="poll_voted'.$data['pollId'].'" style="display:none">';
			echo 'Your vote has been registered!';
		echo '</div>';

		echo '</div>';	//class="item"
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

	function addPollVote($_id, $voteId)
	{
		global $db, $session;
		if (!is_numeric($_id) || !is_numeric($voteId)) return false;

		$check = $db->getOneItem('SELECT userId FROM tblPollVotes WHERE pollId='.$_id.' AND userId='.$session->id);
		if ($check) return false;
		$db->insert('INSERT INTO tblPollVotes SET userId='.$session->id.',pollId='.$_id.',voteId='.$voteId);
		return true;
	}

	function hasAnsweredPoll($_id)
	{
		global $db, $session;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT pollId FROM tblPollVotes WHERE userId='.$session->id.' AND pollId='.$_id;
		if ($db->getOneItem($q)) return true;
		return false;
	}
	
	function getPollStats($_id)
	{
		global $db;

		$q = 'SELECT COUNT(voteId) AS cnt FROM tblPollVotes WHERE pollId='.$_id.' GROUP BY voteId';
		return $db->getArray($q);
	}

/*
	function removePoll($ownerId, $_id)
	{
		global $db;
		if (!is_numeric($ownerId) || !is_numeric($_id)) return false;

		$db->delete('DELETE FROM tblPolls WHERE ownerId='.$ownerId.' AND pollID='.$_id);
	}
*/
?>