<?
	//forum module settings:
	$config['forum']['rootname'] = 'Forum';
	$config['forum']['path_separator'] = ' - ';
	$config['forum']['allow_votes'] = false;
	$config['forum']['maxsize_body'] = 5000;	//max number of characters in a forum post
	$config['forum']['search_results_per_page'] = 5;


	/*
		functions_forums.php - Funktioner för forum
		
		//todo: släng bort massa funktioner som inte används
		//ta bort fileId från tblForums
		//eventuellt: omprogrammering och använd itemType ordentligt! type skulle kunna vara: category, folder, thread, post, sticky, announcement		
	*/


	/* Forum-itemtypes */
	define('FORUM_FOLDER',				1);
	define('FORUM_MESSAGE',				2);


	function getForumItem(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;

		$sql  = 'SELECT t1.*,t2.userName AS authorName ';
		$sql .= 'FROM tblForums AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$sql .= 'WHERE t1.itemId='.$itemId;

		return dbOneResult($db, $sql);
	}

	/* Returns all items inside $itemId */
	function getForumItems(&$db, $itemId, $asc_order = true)
	{
		if (!is_numeric($itemId) ||!is_bool($asc_order)) return false;

		$sql  = 'SELECT t1.*,t2.userName AS authorName ';
		$sql .= 'FROM tblForums AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$sql .= 'WHERE t1.parentId='.$itemId.' ';
		$sql .= 'ORDER BY t1.itemType ASC,t1.sticky DESC,';
		if ($asc_order) $sql .= 't1.timestamp ASC';
		else $sql .= 't1.timestamp DESC';
		$sql .= ',t1.itemSubject ASC';

		return dbArray($db, $sql);
	}

	function deleteForumItem(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;

		$sql = 'DELETE FROM tblForums WHERE itemId='.$itemId;
		dbQuery($db, $sql);
	}

	/* Deletes itemId and everything below it. also deletes associated moderation queue entries */
	function deleteForumItemRecursive(&$db, $itemId, $loop = false)
	{
		if (!is_numeric($itemId)) return false;

		$sql = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId;
		$check = dbQuery($db, $sql);
		$cnt = dbNumRows($check);

		for ($i=0; $i<$cnt; $i++) {
			$row = dbFetchArray($check);

			$sql = 'DELETE FROM tblForums WHERE itemId='.$row['itemId'];
			dbQuery($db, $sql);

			removeFromModerationQueueByItemId($db, $row['itemId'], MODERATION_REPORTED_POST);
			deleteForumItemRecursive($db, $row['itemId'], true);
		}

		if ($loop != true) {
			$sql = 'DELETE FROM tblForums WHERE itemId='.$itemId;
			dbQuery($db, $sql);

			removeFromModerationQueueByItemId($db, $itemId, MODERATION_REPORTED_POST);
		}
	}


	/* Return the number of messages inside $itemId, recursive (default) */
	function getForumMessageCount(&$db, $itemId, $recursive = true, $mecnt = 0)
	{
		if (!is_numeric($itemId) || !is_numeric($mecnt)) return false;

		$sql = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId.' AND itemType='.FORUM_MESSAGE;
		$check = dbQuery($db, $sql);
		$cnt = dbNumRows($check);

		for ($i=0; $i<$cnt; $i++) {
			$row = dbFetchArray($check);
			$mecnt++;
			if ($recursive === true) {
				$mecnt = getForumMessageCount($db, $row['itemId'], $recursive, $mecnt);
			}
		}
		return $mecnt;
	}

	/* Returns the number of folders inside $itemId, recursive */
	function getForumFolderCount(&$db, $itemId, $mecnt = 0)
	{
		if (!is_numeric($itemId) || !is_numeric($mecnt)) return false;

		$sql = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId.' AND itemType='.FORUM_FOLDER;

		$check = dbQuery($db, $sql);
		$cnt = dbNumRows($check);

		for ($i=0; $i<$cnt; $i++) {
			$row = dbFetchArray($check);
			$mecnt++;
			$mecnt = getForumFolderCount($db, $row['itemId'], $mecnt);
		}
		return $mecnt;
	}

	/* Return the number of items (folders & messages & discussions) inside $itemId, recursive */
	function getForumItemCount(&$db, $itemId, $mecnt = 0)
	{
		if (!is_numeric($itemId) || !is_numeric($mecnt)) return false;

		$sql = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId;

		$check = dbQuery($db, $sql);
		$cnt = dbNumRows($check);

		for ($i=0; $i<$cnt; $i++) {
			$row = dbFetchArray($check);
			$mecnt++;
			$mecnt = getForumItemCount($db, $row['itemId'], $mecnt);
		}
		return $mecnt;
	}

	function forumItemIsFolder(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;

		if ($itemId == 0) return true; //root folder

		$sql = 'SELECT itemType FROM tblForums WHERE itemId='.$itemId;
		$itemType = dbOneResultItem($db, $sql);

		if ($itemType == FORUM_FOLDER) return true;
		return false;
	}

	function forumItemIsMessage(&$db, $itemId)
	{
		/* Returns false if item is a message but parent is a folder (item is a discussion then) */

		if (!is_numeric($itemId)) return false;

		$sql = 'SELECT itemType, parentId FROM tblForums WHERE itemId='.$itemId;
		$row = dbOneResult($db, $sql);

		if ($row['itemType'] == FORUM_MESSAGE) {
			if (forumItemIsFolder($db, $row['parentId'])) return false;
			return true;
		}
		return false;
	}

	function forumItemIsDiscussion(&$db, $itemId)
	{
		/* If the parentId is a folder and itemId is a message, then it is a discussion! */

		if (!is_numeric($itemId)) return false;

		$sql = 'SELECT itemType, parentId FROM tblForums WHERE itemId='.$itemId;
		$row = dbOneResult($db, $sql);

		if ($row['itemType'] == FORUM_MESSAGE) {
			if (forumItemIsFolder($db, $row['parentId'])) return true;
			return false;
		}
		return false;
	}

	function getForumItemParent(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;

		$sql = 'SELECT parentId FROM tblForums WHERE itemId='.$itemId;
		return dbOneResultItem($db, $sql);
	}

	function setForumItemParent(&$db, $itemId, $parentId)
	{
		if (!is_numeric($itemId) || !is_numeric($parentId)) return false;

		$sql = 'UPDATE tblForums SET parentId='.$parentId.' WHERE itemId='.$itemId;
		dbQuery($db, $sql);
	}


	/* Recursive, returns the nearest folderId above itemId (which is a message) */
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

	/* Returns the root message id of $itemId */
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

	function addForumFolder(&$db, $parentId, $folderName, $folderDesc = '')
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($parentId)) return false;

		$folderDesc = strip_tags($folderDesc);
		$folderName = dbAddSlashes($db, strip_tags($folderName));
		$folderDesc = dbAddSlashes($db, $folderDesc);

		$sql = 'INSERT INTO tblForums SET itemType='.FORUM_FOLDER.',authorId='.$_SESSION['userId'].',parentId='.$parentId.',itemSubject="'.$folderName.'",itemBody="'.$folderDesc.'",timestamp='.time();
		$query = dbQuery($db, $sql);
		return $db['insert_id'];
	}

	function addForumMessage(&$db, $parentId, $subject, $body, $sticky = 0)
	{
		global $config;
		
		if (!$_SESSION['loggedIn'] || !is_numeric($parentId) || !is_numeric($sticky)) return false;

		$body = strip_tags($body);
		$subject = dbAddSlashes($db, strip_tags($subject));

		$body = substr($body, 0, $config['forum']['maxsize_body']);
		$body = dbAddSlashes($db, $body);

		$sql = 'INSERT INTO tblForums SET itemType='.FORUM_MESSAGE.',authorId='.$_SESSION['userId'].',parentId='.$parentId.',itemSubject="'.$subject.'",itemBody="'.$body.'",timestamp='.time();
		if ($sticky) $sql .= ',sticky='.$sticky;
		$query = dbQuery($db, $sql);
		$itemId = $db['insert_id'];
		
		/* Check if message contains any objectionable words */
		/*
		if (isObjectionable($db, $subject) || isObjectionable($db, $body)) {
			addToModerationQueue($db, $itemId, MODERATION_OBJECTIONABLE_POST);
		}*/

		if (isSensitive($db, $subject) || isSensitive($db, $body)) {
			addToModerationQueue($db, $itemId, MODERATION_SENSITIVE_POST);
		}

		/* Check if there is any users who should be notified about this new message */
		notifySubscribers($db, $parentId, $itemId, SUBSCRIBE_MAIL);
		return $itemId;
	}

	/* Returns the $count last posts by $userId, or all if $count is skipped */
	function getUserLastForumPosts(&$db, $authorId, $count = '')
	{
		if (!is_numeric($authorId)) return false;

		$sql = 'SELECT * FROM tblForums WHERE authorId='.$authorId.' AND itemType='.FORUM_MESSAGE.' ORDER BY timestamp DESC';
		if (is_numeric($count)) {
			$sql .= ' LIMIT 0,'.$count;
		}

		return dbArray($db, $sql);
	}

	/* Returns the number of messages that $userId has written in the forums */
	function getForumPostsCount(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql = 'SELECT COUNT(itemId) FROM tblForums WHERE authorId='.$userId.' AND itemType='.FORUM_MESSAGE;
		return dbOneResultItem($db, $sql);
	}

	/* Returns the timestamp of the newest forum entry inside $itemId, recursive */
	function getForumNewestItem(&$db, $itemId, $currtop = '')
	{
		if (!$currtop) $currtop=0;
		if (!is_numeric($itemId) || !is_numeric($currtop)) return false;

		$sql = 'SELECT itemId, timestamp FROM tblForums WHERE parentId='.$itemId;
		$list = dbArray($db, $sql);
		for ($i=0; $i<count($list); $i++) {
			if ($list[$i]['timestamp'] > $currtop) {
				$currtop = $list[$i]['timestamp'];
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
	function getForumItemDepthHTML(&$db, $itemId)
	{
		global $config;

		if (!is_numeric($itemId)) return false;

		if ($itemId != 0) {

			$sql = 'SELECT itemSubject,parentId FROM tblForums WHERE itemId='.$itemId;
			$row = dbOneResult($db, $sql);
			$subject = $row['itemSubject'];
			if ($subject) {
				if (mb_strlen($subject) > 35) {
					$subject = mb_substr($subject, 0, 35).'...';
				}
				$result = ' - <a href="forum.php?id='.$itemId.'">'.($subject != '' ? $subject : '(Inget navn)').'</a>';
			} else {
				$result = '';
			}
			$result = getForumItemDepthHTML($db, $row['parentId']).$result;
			return $result;
		}

		$result = '<a href="forum.php">'.$config['forum']['rootname'].'</a>';
		return $result;
	}

	/* Returns the $count last posts */
	function getLastForumPosts(&$db, $count)
	{
		if (!is_numeric($count)) return false;

		$sql  = 'SELECT t1.*,t2.userName AS authorName,t3.itemSubject AS parentSubject ';
		$sql .= 'FROM tblForums AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$sql .= 'LEFT OUTER JOIN tblForums AS t3 ON (t1.itemSubject="" AND t1.parentId=t3.itemId) ';
		$sql .= 'WHERE t1.itemType='.FORUM_MESSAGE.' ';
		$sql .= 'ORDER BY t1.timestamp DESC ';
		$sql .= 'LIMIT 0,'.$count;

		return dbArray($db, $sql);
	}

	/* Returns the $count most read posts (on whole forum) */
	function getForumMostReadMessages(&$db, $count)
	{
		if (!is_numeric($count)) return false;

		$sql  = 'SELECT t1.itemId,t1.authorId,t1.itemSubject,t1.itemBody,t1.timestamp,t2.userName AS authorName ';
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

		$sql = 'SELECT itemId,timestamp FROM tblForums WHERE parentId='.$itemId;
		$list = dbArray($db, $sql);
		for ($i=0; $i<count($list); $i++) {

			if (!isset($_SESSION['forum'.$list[$i]['itemId']])) $_SESSION['forum'.$list[$i]['itemId']]=false;

			if (($list[$i]['timestamp'] > $_SESSION['prevLoginTime']) && ($_SESSION['forum'.$list[$i]['itemId']] === false)) {
				return true;
			}
			if (forumPathContainsUnread($db, $list[$i]['itemId']) === true) {
				return true;
			}
		}
		return false;
	}

	function forumLockItem(&$db, $itemId)
	{
		if (!$_SESSION['isAdmin'] || !is_numeric($itemId)) return false;
		
		$sql = 'UPDATE tblForums SET locked=1 WHERE itemId='.$itemId;
		dbQuery($db, $sql);
	}

	function forumUnlockItem(&$db, $itemId)
	{
		if (!$_SESSION['isAdmin'] || !is_numeric($itemId)) return false;
		
		$sql = 'UPDATE tblForums SET locked=0 WHERE itemId='.$itemId;
		dbQuery($db, $sql);
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

	/* Sparar ändringar i ett inlägg/folder/whatever */
	function forumUpdateItem(&$db, $itemId, $subject, $body, $sticky = 0)
	{
		if (!is_numeric($itemId) || !is_numeric($sticky)) return false;
		$subject = dbAddSlashes($db, $subject);
		$body = dbAddSlashes($db, $body);

		$sql = 'UPDATE tblForums SET itemSubject="'.$subject.'",itemBody="'.$body.'",sticky='.$sticky.' WHERE itemId='.$itemId;
		dbQuery($db, $sql);
	}

	/* Returns a list of all folder paths, ie folder1 - folder_in_folder1 etc... + folderid, used for now in accessgroup admin */
	function getForumStructure(&$db, $parentId=0, $arr='', $pre='')
	{
		$parentId = dbAddSlashes($db, $parentId);
		if (!is_numeric($parentId)) return false;

		$sql = 'SELECT itemSubject,itemId FROM tblForums WHERE parentId='.$parentId.' ORDER BY itemSubject';
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

			$arr = getForumStructure($db, $list[$i]['itemId'], $arr, $pre);
			$pre='';
		}

		return $arr;
	}


	function getForumFolderDepthHTML(&$db, $itemId)
	{
		global $config;
		
		if (!is_numeric($itemId)) return false;

		if ($itemId != 0) {

			$result = '';

			$sql = 'SELECT itemSubject,parentId FROM tblForums WHERE itemId='.$itemId;
			$row = dbOneResult($db, $sql);
			if ($row['itemSubject']) {
				$result = $config['forum']['path_separator'].'<a href="forum.php?id='.$itemId.'">'.$row['itemSubject'].'</a>';
			}
			$result = getForumFolderDepthHTML($db, $row['parentId']).$result;
			return $result;
		}
		$result = '<a href="forum.php">'.$config['forum']['rootname'].'</a>';
		return $result;
	}

	function displayCurrentForumContent(&$db, $itemId)
	{
		echo 'displayCurrentForumContent() deprecated ! dont use';
	}


	function displayRootForumContent(&$db)
	{
		$list = getForumItems($db, 0);

		if (!count($list)) return;
		
		$str = '';

		for ($i=0; $i<count($list); $i++) {

			$subject = $list[$i]["itemSubject"];
			if (strlen($subject)>35) $subject = substr($subject,0,35).'..';

			if (!$subject) {
				$subject = '(Inget navn)';
			}

			$str .= '<div class="forum_header"><a href="forum.php?id='.$list[$i]['itemId'].'">'.$subject.'</a></div>';
			$str .= displaySubfolders($db, $list[$i]['itemId']).'<br>';
		}

		return $str;
	}
	
	function displaySubfolders(&$db, $itemId)
	{
		global $config;
		
		if (!is_numeric($itemId)) return false;

		$data = getForumItem($db, $itemId);
		$list = getForumItems($db, $itemId);

		$str  = '<table width="100%" cellpadding=0 cellspacing=0 border=1 class="forum_borders">';
		$str .= '<tr class="forum_subheader">';
		$str .= '<th width=30></th>';
		$str .= '<th>&nbsp;'.$config['forum']['text']['forum'].'</th>';


		if ($data['parentId'] == 0) {
			$str .= '<th width=200 align="center">'.$config['forum']['text']['last_thread'].'</th>';
			$str .= '<th width=70 align="center">'.$config['forum']['text']['threads'].'</th>';
			$str .= '<th width=70 align="center">'.$config['forum']['text']['posts'].'</th>';
		} else {
			$str .= '<th width=200 align="center">'.$config['forum']['text']['last_post'].'</th>';
			$str .= '<th width=70 align="center">'.$config['forum']['text']['threads'].'</th>';
			$str .= '<th width=70 align="center">'.$config['forum']['text']['posts'].'</th>';
		}

		$str .= '</tr>';

		for ($i=0; $i<count($list); $i++) {

			$subject = $list[$i]["itemSubject"];
			if (strlen($subject) > 50) $subject = substr($subject, 0, 50).'..';
			if (!$subject) {
				$subject = '(Inget navn)';
			}

			$str .= '<tr class="forum_item">';
			$str .= '<td align="center"><img src="gfx/icon_folder.png"></td>';
			$str .= '<td class="forum_item_text">'.
								'<a href="forum.php?id='.$list[$i]['itemId'].'">'.$subject.'</a><br>'.
								$list[$i]['itemBody'];
							'</td>';

			$data = getForumThreadContentLastPost($db, $list[$i]['itemId']);
			$str .= '<td class="forum_item_text" width=200>';
			if ($data) {
				if ($data['itemSubject']) {
					$str .= '<a href="forum.php?id='.$data['itemId'].'">'.$data['itemSubject'].'</a><br>';
				} else {
					$str .= '<a href="forum.php?id='.$data['parentId'].'#post'.$data['itemId'].'">'.$data['parentSubject'].'</a><br>';
				}
				$str .= $config['forum']['text']['by'].' '.nameLink($data['authorId'], $data['authorName']).'<br>';
				$str .= getRelativeTimeLong($data['timestamp']);
			} else {
				$str .= 'Never';
			}
			$str .= '</td>';
			$str .= '<td align="center">'.formatNumber(getForumItemCountFlat($db, $list[$i]['itemId'])).'</td>';
			$str .= '<td align="center">'.formatNumber(getForumThreadContentCount($db, $list[$i]['itemId'])).'</td>';
			$str .= '</tr>';
		}
		$str .= '</table>';

		return $str;
	}
	
	/* Returns item data for the last post in any of the threads with parentId=$itemId */
	function getForumThreadContentLastPost(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;
		
		$sql = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId;
		$list = dbArray($db, $sql);
		
		$newest_time = 0;

		for ($i=0; $i<count($list); $i++) {
			$sql =	'SELECT itemId, timestamp FROM tblForums '.
							'WHERE parentId='.$list[$i]['itemId'].' '.
							'ORDER BY timestamp DESC LIMIT 0,1';
			$data = dbOneResult($db, $sql);
			
			if ($data['timestamp'] > $newest_time) {
				$newest_time = $data['timestamp'];
				$newest_id = $data['itemId'];
			}
		}
		
		if ($newest_time) {
			$data = getForumItem($db, $newest_id);
			if (!$data['itemSubject']) {
				//fills in parent's subject if subject is missing
				$parent_data = getForumItem($db, $data['parentId']);
				$data['parentSubject'] = $parent_data['itemSubject'];
			}
			return $data;
		}
		
		return false;
	}

	/* Returns the number of items with $itemId as parent, non-recursive */
	function getForumItemCountFlat(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;

		$sql = 'SELECT COUNT(itemId) FROM tblForums WHERE parentId='.$itemId;
		
		return dbOneResultItem($db, $sql);
	}
	
	/* Returns the total number of posts contained in all the threads with parentId=$itemId */
	function getForumThreadContentCount(&$db, $itemId)
	{
		//fixme: kanske byta namn på funktionen
		if (!is_numeric($itemId)) return false;
		
		$sql = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId;
		$list = dbArray($db, $sql);
		
		$cnt = 0;
		for ($i=0; $i<count($list); $i++) {
			$sql = 'SELECT COUNT(itemId) FROM tblForums WHERE parentId='.$list[$i]['itemId'];
			$cnt += dbOneResultItem($db, $sql);
		}

		return $cnt;
	}

	function displayForumContentFlat(&$db, $itemId)
	{
		global $config;

		if (!is_numeric($itemId)) return false;

		$result = '';
		
		$data = getForumItem($db, $itemId);
		$list = getForumItems($db, $itemId, false);
		
		if ($data['parentId'] == 0) {
			$result .= '<div class="forum_header">'.$data['itemSubject'].'</div>';
		} else {
			$result .= '<div class="forum_header">'.$config['forum']['text']['threads_in_forum'].': '.$data['itemSubject'].'</div>';
		}

		$result .= '<table width="100%" cellpadding=0 cellspacing=0 border=1 class="forum_borders">';
		$result .= '<tr class="forum_subheader">';
		$result .= '<th width=30></th>';
		if ($data['parentId'] == 0) {
			$result .= '<th>&nbsp;'.$config['forum']['text']['forum'].'</th>';			
			$result .= '<th width=200 align="center">'.$config['forum']['text']['last_thread'].'</th>';
			$result .= '<th width=70 align="center">'.$config['forum']['text']['threads'].'</th>';	
		} else {
			$result .= '<th>&nbsp;'.$config['forum']['text']['thread'].'</th>';
			$result .= '<th width=200 align="center">'.$config['forum']['text']['last_post'].'</th>';
			$result .= '<th width=70 align="center">'.$config['forum']['text']['replies'].'</th>';
		}
		$result .= '<th width=70 align="center">'.$config['forum']['text']['views'].'</th>';
		$result .= '</tr>';
		
		for ($i=0; $i<count($list); $i++) {
			$result .= '<tr class="forum_item">';

			$result .= '<td align="center">';
			if ($list[$i]['locked']) {
				$result .= '<img src="icons/forum_lock.png"><br>';
			}
			if ($list[$i]['sticky'] == 1) {
				$result .= '<img src="icons/forum_sticky.gif">';
			} else if ($list[$i]['sticky'] == 2) {
				$result .= '<img src="icons/forum_announcement.png">';
			} else if ($data['parentId'] == 0) {
				$result .= '<img src="gfx/icon_folder.png">';
			} else {
				$result .= '<img src="gfx/icon_message.png">';
			}
			$result .= '</td>';

			$result .= '<td class="forum_item_text">';
				if ($list[$i]['sticky'] == 1) $result .= '<b>Sticky: </b>';
				//if ($list[$i]['sticky'] == 2) $result .= '<b>Announcement: </b>';
				if ($list[$i]['sticky'] == 2) $result .= '<b>Kunngj&oslash;ring: </b>';
				$result .= '<a href="forum.php?id='.$list[$i]['itemId'].'">'.$list[$i]['itemSubject'].'</a><br>';
				$result .= getRelativeTimeLong($list[$i]['timestamp']).'<br>';
				$result .= $config['forum']['text']['by'].' '.nameLink($list[$i]['authorId'], $list[$i]['authorName']);
			$result .= '</td>';

			$lastreply = getForumLastReply($db, $list[$i]['itemId']);
			$result .= '<td class="forum_item_text">';
			if ($lastreply) {
				if ($data['parentId'] == 0) {
					$result .= '<a href="forum.php?id='.$lastreply['itemId'].'#post'.$lastreply['itemId'].'"><img src="icons/forum_lastpost.png" title="Go to post" width=15 height=15></a> ';
					$subject = $lastreply['itemSubject'];
					if (mb_strlen($subject) > 25) $subject = mb_substr($subject, 0, 25).'...';
					$result .= '<a href="forum.php?id='.$lastreply['itemId'].'#post'.$lastreply['itemId'].'">'.$subject.'</a><br>';
				} else {
					//visa rubriken från parent-inlägg:
					$result .= '<a href="forum.php?id='.$list[$i]['itemId'].'#post'.$lastreply['itemId'].'"><img src="icons/forum_lastpost.png" title="Go to post" width=15 height=15></a> ';
					$subject = $list[$i]['itemSubject'];
					if (mb_strlen($subject) > 25) $subject = mb_substr($subject, 0, 25).'...';
					$result .= '<a href="forum.php?id='.$list[$i]['itemId'].'#post'.$lastreply['itemId'].'">'.$subject.'</a><br>';
				}
				$result .= getRelativeTimeLong($lastreply['timestamp']).'<br>'.$config['forum']['text']['by'].' '.nameLink($lastreply['userId'], $lastreply['userName']);
			} else {
				if ($data['parentId']) $result .= '<a href="forum.php?id='.$list[$i]['itemId'].'"><img src="icons/forum_lastpost.png" title="Go to post" width=15 height=15></a> ';
				$subject = $list[$i]['itemSubject'];
				if (mb_strlen($subject) > 25) $subject = mb_substr($subject, 0, 25).'...';
				$result .= '<a href="forum.php?id='.$list[$i]['itemId'].'">'.$subject.'</a><br>';
				$result .= getRelativeTimeLong($list[$i]['timestamp']).'<br>'.$config['forum']['text']['by'].' '.nameLink($list[$i]['authorId'], $list[$i]['authorName']);
			}
			$result .= '</td>';

			$result .= '<td align="center">'.formatNumber(getForumMessageCount($db, $list[$i]['itemId'], false)).'</td>';
			$result .= '<td align="center">'.formatNumber($list[$i]['itemRead']).'</td>';
			$result .= '</tr>';
		}
		
		$result .= '</table>';
		return $result;
	}
	
	function getForumLastReply(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;
		
		//returns timestamp of last reply to $itemId
		$sql  = 'SELECT t1.*,t2.userId,t2.userName FROM tblForums AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$sql .= 'WHERE t1.parentId='.$itemId.' ';
		$sql .= 'ORDER BY t1.timestamp DESC LIMIT 0,1';
		return dbOneResult($db, $sql);
	}
	
	//todo: ta en optional parameter $highlight för sökresultat
	//bugg: $highlight ändrar på enkodade htmltaggar vilket resulterar i massa html-leakage i resultatet
	function showForumPost(&$db, $item, $headertext = '', $show_links = true, $highlight = '')
	{
		global $config;
		
		$subject = formatUserInputText($item['itemSubject']);
		$body = formatUserInputText($item['itemBody']);

		if ($highlight) {
			$criterialist = explode(" ", $highlight);
			$replace = '<span class="forum_search_highlight">\\0</span>';
			for ($i=0; $i<count($criterialist); $i++) {
				$regexp = "(\t | \n | ' ')*".$criterialist[$i]."(\t | \n | ' ')*";
				$subject = eregi_replace($regexp, $replace, $subject);
				$body    = eregi_replace($regexp, $replace, $body);
			}
		}


		$content = '<a name="post'.$item['itemId'].'" id="post'.$item['itemId'].'"></a>';
		$content .= '<table width="100%" cellpadding=0 cellspacing=0 border=1 class="forum_borders">';
		$content .= '<tr class="forum_subheader">';
		$content .= '<th width=160>'.getRelativeTimeLong($item['timestamp']).'</th>';
		if ($headertext) {
			$content .= '<th align="right">'.$headertext.'</th>';
		} else {
			$content .= '<th></th>';
		}
		$content .= '</tr>';

		$content .= '<tr class="forum_item">';
		$content .= '<td width=160 valign="top" class="forum_item_text">';
			$content .= nameLink($item['authorId'], $item['authorName']).'<br><br>';

			$userpic = getThumbnail($db, $item['authorId'], 'Egen avatar', $config['thumbnail_width'], $config['thumbnail_height'], false);
			if ($userpic) {
				$content .= $userpic.'<br>';
			} else {
				$choosen_avatar = getUserSetting($db, $item['authorId'], 'avatar');
				if ($choosen_avatar && is_numeric($choosen_avatar)) {
					$content .= '<img src="avatars/'.$config['avatars'][$choosen_avatar].'" width=80 height=80><br>';
				}
			}

			$content .= 'Status: '.getUserStatus($db, $item['authorId']).'<br>';
			//$content .= 'Join date: '.formatDate(getUserCreated($db, $item['authorId'])).'<br>';
			$content .= 'Inlegg: '.getForumPostsCount($db, $item['authorId']);
		$content .= '</td>';

		$content .= '<td valign="top" class="forum_item_text">';
			if ($subject) $content .= '<b>'.$subject.'</b><hr>';
			$content .= $body;

			$signature = getUserdataByFieldname($db, $item['authorId'], 'Forumsignatur');
			if ($signature) $content .= '<hr>'.$signature;

		$content .= '</td>';
		$content .= '</tr>';
			
		$content .= '<tr class="forum_item">';
		$content .= '<td></td>';
		$content .= '<td align="right">';
			if ($_SESSION['loggedIn'] && $show_links) {
				//$content .= '<a href="forum_tipsa.php?id='.$item['itemId'].'">Tell a friend</a> ';

				if (!$item['locked']) {
					if (forumItemIsDiscussion($db, $item['itemId'])) {
						$content .= '<a href="forum_new.php?id='.$item['itemId'].'&q='.$item['itemId'].'">'.$config['text']['link_reply'].'</a> ';
					} else {
						$content .= '<a href="forum_new.php?id='.$item['parentId'].'&q='.$item['itemId'].'">'.$config['text']['link_reply'].'</a> ';
					}

					if ($item['authorId'] == $_SESSION['userId'] || $_SESSION['isAdmin']) {
						$content .= '<a href="forum_edit.php?id='.$item['itemId'].'">'.$config['text']['link_edit'].'</a> ';
					}
				}

				if ($_SESSION['isAdmin']) {
					$content .= '<a href="forum_delete.php?id='.$item['itemId'].'">'.$config['text']['link_remove'].'</a> ';
				}
				
				if ($_SESSION['isAdmin'] && forumItemIsDiscussion($db, $item['itemId'])) {
					if (!$item['locked']) {
						$content .= '<a href="forum_lock.php?id='.$item['itemId'].'">L&aring;s</a> ';
					} else {
						$content .= '<a href="forum_lock.php?id='.$item['itemId'].'&unlock">L&aring;s upp</a> ';
					}
					$content .= '<a href="forum_move.php?id='.$item['itemId'].'">'.$config['text']['link_move'].'</a> ';
				}

				if ($_SESSION['userId'] != $item['authorId']) {
					$content .= '<a href="forum_report.php?id='.$item['itemId'].'">'.$config['text']['link_report'].'</a> ';
				}
			}

		$content .= '</td></tr>';
		$content .= '</table><br>';

		return $content;
	}
	
	
	function displayDiscussionContentFlat(&$db, $itemId)
	{
		global $config;

		if (!is_numeric($itemId)) return false;
		
		$content = '';
		
		$item = getForumItem($db, $itemId);
		$content .= showForumPost($db, $item, $config['forum']['text']['first_post']);

		$list = getForumItems($db, $itemId);

		for ($i=0; $i<count($list); $i++) {
			$content .= showForumPost($db, $list[$i], 'Svar #'.($i+1));
		}
		return $content;
	}

	function displayUsersLatestPosts(&$db, $userId, $limit = 5)
	{
		$list = getUserLastForumPosts($db, $userId, $limit);

		if (count($list)) {

			$str = '<table width="100%" cellpadding=0 cellspacing=0 border=0>';
			$str .= '<tr><td>Tr&aring;d</td><td width=70>Tid</td></tr>';
			for ($i=0; $i<count($list); $i++) {
				$str .= '<tr>';
				$subject = $list[$i]['itemSubject'];
				if (!$subject) {
					$data = getForumItem($db, $list[$i]['parentId']);
					$subject = $data['itemSubject'];
				}
				if (strlen($subject)>30) $subject = substr($subject,0,30);

				$str .= '<td>';
					if (forumItemIsMessage($db, $list[$i]['itemId'])) {
						$str .= '<a href="forum.php?id='.$list[$i]['parentId'].'#post'.$list[$i]['itemId'].'">'.$subject.'</a>';
					} else {
						$str .= '<a href="forum.php?id='.$list[$i]['itemId'].'">'.$subject.'</a>';
					}
					if (strlen($list[$i]['itemSubject']) > strlen($subject)) $str .= '..';
				$str .= '</td>';
				$str .= '<td valign="top">'.getRelativeTimeLong($list[$i]['timestamp']).'</td>';
				$str .= '</tr>';
			}
			$str .= '</table>';
		} else {
			//$str = 'Anv&auml;ndaren har inte skrivit n&aring;gra inl&auml;gg';
			$str = 'Brukeren har ikke skrevet noen innlegg.';
		}
		
		return $str;
	}	
?>