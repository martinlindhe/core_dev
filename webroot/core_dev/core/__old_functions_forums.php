<?

	function getForumItemParent(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;

		$sql = 'SELECT parentId FROM tblForums WHERE itemId='.$itemId;
		return dbOneResultItem($db, $sql);
	}


	//Recursive, returns the nearest folderId above itemId (which is a message)
	function getForumFolderParent(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;
		$parentId = getForumItemParent($db, $itemId);
		if ($parentId == 0) return $parentId;

		$sql = 'SELECT itemType FROM tblForums WHERE itemId='.$parentId;
		$itemType = dbOneResultItem($db, $sql);

		if ($itemType == FORUM_FOLDER) return $parentId;
		return getForumFolderParent($db, $parentId);
	}


	//Returns the root message id of $itemId

	function getForumMessageRoot(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;
		$parentId = getForumItemParent($db, $itemId);
		if ($parentId == 0) return $itemId;

		$sql = 'SELECT itemType FROM tblForums WHERE itemId='.$parentId;
		$itemType = dbOneResultItem($db, $sql);

		if ($itemType == FORUM_FOLDER) return $itemId;
		return getForumMessageRoot($db, $parentId);
	}


	/* Returns the $count last posts by $userId, or all if $count is skipped */
	function getUserLastForumPosts(&$db, $authorId, $count = '')
	{
		if (!is_numeric($authorId)) return false;

		$sql = 'SELECT * FROM tblForums WHERE authorId='.$authorId.' AND itemType='.FORUM_MESSAGE.' ORDER BY timeCreated DESC';
		if (is_numeric($count)) {
			$sql .= ' LIMIT 0,'.$count;
		}

		return dbArray($db, $sql);
	}


	/* Returns the timestamp of the newest forum entry inside $itemId, recursive */
	function getForumNewestItem(&$db, $itemId, $currtop = '')
	{
		if (!$currtop) $currtop=0;
		if (!is_numeric($itemId) || !is_numeric($currtop)) return false;

		$sql = 'SELECT itemId, timeCreated FROM tblForums WHERE parentId='.$itemId;
		$list = dbArray($db, $sql);
		for ($i=0; $i<count($list); $i++) {
			if ($list[$i]['timeCreated'] > $currtop) {
				$currtop = $list[$i]['timeCreated'];
			}
			$currtop = getForumNewestItem($db, $list[$i]['itemId'], $currtop);
		}

		return $currtop;
	}


	/* Returns a list of all folder paths, ie folder1 - folder_in_folder1 etc... + folderid, used for now in accessgroup admin */
	function getForumFolderStructure(&$db, $parentId, $arr = '', $pre = '')
	{
		if (!is_numeric($parentId)) return false;

		$sql = 'SELECT itemSubject, itemId FROM tblForums WHERE itemType='.FORUM_FOLDER.' AND parentId='.$parentId.' ORDER BY itemSubject';
		$list = dbArray($db, $sql);

		/* Lägg först till allt på samma nivå */
		for ($i=0; $i<count($list); $i++) {
			if ($pre != '') {
				$arr[] = array('name' => $pre.' - '.$list[$i]['itemSubject'], 'itemId' => $list[$i]['itemId']);
			} else {
				$arr[] = array('name' => $list[$i]['itemSubject'], 'itemId' => $list[$i]['itemId']);
			}
		}

		/* Sen rekursiva */
		for ($i=0; $i<count($list); $i++) {
			if ($pre != '') {
				$pre = $pre.' - '.$list[$i]['itemSubject'];
			} else {
				$pre = $list[$i]['itemSubject'];
			}

			$arr = getForumFolderStructure($db, $list[$i]['itemId'], $arr, $pre);
			$pre = '';
		}

		return $arr;
	}

	function updateForumReadCounter(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;

		$sql = 'UPDATE tblForums SET itemRead=itemRead+1 WHERE itemId='.$itemId;
		dbQuery($db, $sql);
	}

	function addForumVote(&$db, $itemId, $value)
	{
		if (!is_numeric($itemId) || !is_numeric($value)) return false;

		$sql  = 'UPDATE tblForums ';
		$sql .= 'SET itemVote=itemVote+'.$value.',itemVoteCnt=itemVoteCnt+1 ';
		$sql .= 'WHERE itemId='.$itemId;
		dbQuery($db, $sql);
	}

	function getMostActivePosters(&$db, $limit='')
	{
		$sql  = 'SELECT COUNT(t1.authorId) AS cnt, t1.authorId AS userId, t2.userName ';
		$sql .= 'FROM tblForums AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$sql .= 'GROUP BY t1.authorId ';
		$sql .= 'ORDER BY cnt DESC';

		if (is_numeric($limit)) {
			$sql .= ' LIMIT 0,'.$limit;
		}

		return dbArray($db, $sql);
	}



	/* item is a forum or folder or whatever! */
	/*
	function getForumItemDepthHTML($itemId)
	{
		global $db, $config;
		if (!is_numeric($itemId)) return false;

		if (!$itemId) {
			$result = '<a href="forum.php">'.$config['forum']['rootname'].'</a>';
			return $result;
		}

		$q = 'SELECT itemSubject,parentId FROM tblForums WHERE itemId='.$itemId;
		$row = $db->getOneRow($q);
		$subject = $row['itemSubject'];
		if ($subject) {
			if (mb_strlen($subject) > 35) $subject = mb_substr($subject, 0, 35).'...';
			$result = ' - <a href="forum.php?id='.$itemId.'">'.($subject != '' ? $subject : '(No name)').'</a>';
		} else {
			$result = '';
		}
		return getForumItemDepthHTML($row['parentId']).$result;
	}

	function getForumFolderDepthHTML($itemId)
	{
		global $db, $config;
		if (!is_numeric($itemId)) return false;

		if (!$itemId) {
			$result = '<a href="forum.php">'.$config['forum']['rootname'].'</a>';
			return $result;
		}

		$q = 'SELECT itemSubject,parentId FROM tblForums WHERE itemId='.$itemId;
		$row = $db->getOneRow($q);

		$result = '';
		if ($row['itemSubject']) $result = $config['forum']['path_separator'].'<a href="forum.php?id='.$itemId.'">'.$row['itemSubject'].'</a>';

		return getForumFolderDepthHTML($row['parentId']).$result;
	}
*/



	/* Returns the $count most read posts (on whole forum) */
	function getForumMostReadMessages(&$db, $count)
	{
		if (!is_numeric($count)) return false;

		$sql  = 'SELECT t1.itemId,t1.authorId,t1.itemSubject,t1.itemBody,t1.timeCreated,t2.userName AS authorName ';
		$sql .= 'FROM tblForums AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$sql .= 'WHERE itemType='.FORUM_MESSAGE.' ';
		$sql .= 'ORDER BY itemRead DESC ';
		$sql .= 'LIMIT 0,'.$count;

		return dbArray($db, $sql);
	}

	/* Returns the $count most read posts in a specific part of forum */
	function getForumMostReadMessagesHere(&$db, $itemId, $count)
	{
		if (!is_numeric($itemId) || !is_numeric($count)) return false;

		$sql  = 'SELECT tblForums.*,tblUsers.userName AS authorName ';
		$sql .= 'FROM tblForums ';
		$sql .= 'INNER JOIN tblUsers ON (tblForums.authorId=tblUsers.userId) ';
		$sql .= 'WHERE tblForums.parentId='.$itemId.' AND tblForums.itemType='.FORUM_MESSAGE.' ';
		$sql .= 'ORDER BY tblForums.itemRead DESC ';
		$sql .= 'LIMIT 0,'.$count;

		return dbArray($db, $sql);
	}

	/* Returnerar TRUE om ett oläst inlägg påträffas */
	function forumPathContainsUnread(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;

		$sql = 'SELECT itemId,timeCreated FROM tblForums WHERE parentId='.$itemId;
		$list = dbArray($db, $sql);
		for ($i=0; $i<count($list); $i++) {

			if (!isset($_SESSION['forum'.$list[$i]['itemId']])) $_SESSION['forum'.$list[$i]['itemId']]=false;

			if (($list[$i]['timeCreated'] > $_SESSION['prevLoginTime']) && ($_SESSION['forum'.$list[$i]['itemId']] === false)) {
				return true;
			}
			if (forumPathContainsUnread($db, $list[$i]['itemId']) === true) {
				return true;
			}
		}
		return false;
	}


	/* Returns true/false */
	function forumItemExists(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;

		$sql = 'SELECT itemId FROM tblForums WHERE itemId='.$itemId;
		$check = dbQuery($db, $sql);
		if (dbNumRows($check)) return true;
		return false;
	}


	/* Returns TRUE if $itemParent is parent to $itemChild */
	function forumIsItemParent(&$db, $itemParent, $itemChild)
	{
		if (!is_numeric($itemParent) || !is_numeric($itemChild)) return false;

		while (1) {

			$sql = 'SELECT parentId FROM tblForums WHERE itemId='.$itemChild;
			$parentId = dbOneResultItem($db, $sql);

			if ($parentId == $itemParent) return true;
			if ($parentId == 0) return false;

			$itemChild = $parentId;
		}
	}

	function displayCurrentForumContent(&$db, $itemId)
	{
		echo 'displayCurrentForumContent() deprecated ! dont use';
	}

?>