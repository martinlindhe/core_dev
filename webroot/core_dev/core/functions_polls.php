<?
	/* functions_polls.php - implements a couple of different types of polling functionality
		by Martin Lindhe, 2006-2007
	*/

	define('POLL_SITE',		1);	//"Question of the week"-style polls on the site's front page (for example)
	//define('POLL_USER',		2);
	//define('POLL_GROUP',	3);

	function addPoll($_type, $ownerId, $text, $duration_mode, $start_mode)
	{
		global $db, $session;

		if (!is_numeric($_type) || !is_numeric($ownerId)) return false;

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
		
		switch ($start_mode) {
			case 'thismonday':
				$dayofweek = date('N');
				$mon = date('n');
				$day = date('j');
				$thismonday = mktime(6, 0, 0, $mon, $day - $dayofweek + 1);	//06:00 Monday current week
				$timeStart = ',timeStart="'.sql_datetime($thismonday).'"';
				break;

			case 'nextfree':
				$q = 'SELECT timeEnd FROM tblPolls ORDER BY timeStart ASC LIMIT 1';
				$data = $db->getOneRow($q);
				if ($data) {
					$timeStart = ',timeStart="'.$data['timeEnd'].'"';
				} else {
					$timeStart = ',timeStart=NOW()';
				}
				break;
			
			default: die('eexp');
		}

		$timeEnd = ',timeEnd=DATE_ADD(timeStart, INTERVAL '.$length.' DAY)';

		$q = 'INSERT INTO tblPolls SET ownerId='.$ownerId.',pollType='.$_type.',pollText="'.$text.'",createdBy='.$session->id.',timeCreated=NOW()'.$timeStart.$timeEnd;
		return $db->insert($q);
	}

	function updatePoll($_type, $_id, $_text)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($_id)) return false;

		$q = 'UPDATE tblPolls SET pollText="'.$db->escape($_text).'" WHERE pollType='.$_type.' AND pollId='.$_id;
		$db->update($q);	
	}

	/* get all polls */
	function getPolls($_type, $ownerId = 0)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($ownerId)) return false;		

		$q = 'SELECT * FROM tblPolls WHERE pollType='.$_type.' AND ownerId='.$ownerId.' ORDER BY timeStart ASC,pollText ASC';
		return $db->getArray($q);
	}

	/* get all polls */
	function getPoll($_type, $_id)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($_id)) return false;		

		$q = 'SELECT * FROM tblPolls WHERE pollType='.$_type.' AND pollId='.$_id;
		return $db->getOneRow($q);
	}

	/* Get active polls */
	function getActivePolls($_type, $ownerId = 0)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($ownerId)) return false;		

		$q = 'SELECT * FROM tblPolls WHERE pollType='.$_type.' AND ownerId='.$ownerId.' AND NOW() BETWEEN timeStart AND timeEnd ORDER BY timeStart ASC,pollText ASC';
		return $db->getArray($q);
	}

	function showPoll($data)
	{
		global $session;
		
		$active = false;

		if (time() >= datetime_to_timestamp($data['timeStart']) && time() <= datetime_to_timestamp($data['timeEnd'])) {
			$active = true;
		}

		echo '<div class="item">';
		if ($active) echo 'ACTIVE POLL: ';
		echo $data['pollText'].'<br/><br/>';
		$list = getCategories(CATEGORY_POLL, $data['pollId']);

		echo '<div id="poll'.$data['pollId'].'">';
		if ($session->isAdmin) echo 'Starts: '.$data['timeStart'].', ends '.$data['timeEnd'].'<br/>';

		if ($active && !hasAnsweredPoll($data['pollId'])) {
			foreach ($list as $row) {
				echo '<span onclick="submit_poll('.$data['pollId'].','.$row['categoryId'].')">';
				echo $row['categoryName'];
				echo '</span><br/>';
			}
		} else {
			if ($active) {
				echo 'You already voted, showing current standings:<br/>';
			} else {
				echo 'The poll closed, final result:You already voted, showing current standings:<br/>';
			}

			$votes = getPollStats($data['pollId']);
			$tot_votes = 0;
			foreach ($votes as $row) $tot_votes += $row['cnt'];

			foreach ($votes as $row) {
				echo $row['categoryName'].' got '.$row['cnt'].' ('.(($row['cnt'] / $tot_votes)*100).'%) votes<br/>';
			}
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
		if (!is_numeric($_id)) return false;

		$q  = 'SELECT t1.categoryName, ';
		$q .= '(SELECT COUNT(*) FROM tblPollVotes WHERE voteId=t1.categoryId) AS cnt ';
 		$q .= 'FROM tblCategories AS t1 ';
		$q .= 'WHERE t1.ownerId='.$_id.' AND t1.categoryType='.CATEGORY_POLL;
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