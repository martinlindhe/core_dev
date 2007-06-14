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


	function getForumItem($itemId)
	{
		global $db;
		if (!is_numeric($itemId)) return false;

		$q  = 'SELECT t1.*,t2.userName AS authorName ';
		$q .= 'FROM tblForums AS t1 ';
		$q .= 'LEFT OUTER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$q .= 'WHERE t1.itemId='.$itemId;

		return $db->getOneRow($q);
	}

	/* Returns all items inside $itemId */
	function getForumItems($itemId = 0, $asc_order = true)
	{
		global $db;
		if (!is_numeric($itemId) ||!is_bool($asc_order)) return false;

		$q  = 'SELECT t1.*,t2.userName AS authorName ';
		$q .= 'FROM tblForums AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$q .= 'WHERE t1.parentId='.$itemId.' ';
		$q .= 'ORDER BY t1.itemType ASC,t1.sticky DESC,';
		if ($asc_order) $q .= 't1.timeCreated ASC';
		else $q .= 't1.timeCreated DESC';
		$q .= ',t1.itemSubject ASC';

		return $db->getArray($q);
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

	function forumItemIsFolder($itemId)
	{
		global $db;
		if (!is_numeric($itemId)) return false;

		if ($itemId == 0) return true; //root folder

		$q = 'SELECT itemType FROM tblForums WHERE itemId='.$itemId;
		$itemType = $db->getOneItem($q);

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

	function forumItemIsDiscussion($itemId)
	{
		global $db;
		/* If the parentId is a folder and itemId is a message, then it is a discussion! */

		if (!is_numeric($itemId)) return false;

		$q = 'SELECT itemType, parentId FROM tblForums WHERE itemId='.$itemId;
		$row = $db->getOneRow($q);

		if ($row['itemType'] == FORUM_MESSAGE) {
			if (forumItemIsFolder($row['parentId'])) return true;
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

	function setForumItemParent($itemId, $parentId)
	{
		global $db;
		if (!is_numeric($itemId) || !is_numeric($parentId)) return false;

		$q = 'UPDATE tblForums SET parentId='.$parentId.' WHERE itemId='.$itemId;
		$db->update($q);
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

		$sql = 'INSERT INTO tblForums SET itemType='.FORUM_FOLDER.',authorId='.$_SESSION['userId'].',parentId='.$parentId.',itemSubject="'.$folderName.'",itemBody="'.$folderDesc.'",timeCreated=NOW()';
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

		$sql = 'INSERT INTO tblForums SET itemType='.FORUM_MESSAGE.',authorId='.$_SESSION['userId'].',parentId='.$parentId.',itemSubject="'.$subject.'",itemBody="'.$body.'",timeCreated=NOW()';
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

		$sql = 'SELECT * FROM tblForums WHERE authorId='.$authorId.' AND itemType='.FORUM_MESSAGE.' ORDER BY timeCreated DESC';
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
	function getForumItemDepthHTML($itemId)
	{
		global $db, $config;
		if (!is_numeric($itemId)) return false;

		if ($itemId != 0) {

			$q = 'SELECT itemSubject,parentId FROM tblForums WHERE itemId='.$itemId;
			$row = $db->getOneRow($q);
			$subject = $row['itemSubject'];
			if ($subject) {
				if (mb_strlen($subject) > 35) {
					$subject = mb_substr($subject, 0, 35).'...';
				}
				$result = ' - <a href="forum.php?id='.$itemId.'">'.($subject != '' ? $subject : '(No name)').'</a>';
			} else {
				$result = '';
			}
			$result = getForumItemDepthHTML($row['parentId']).$result;
			return $result;
		}

		$result = '<a href="forum.php">'.$config['forum']['rootname'].'</a>';
		return $result;
	}

	/* Returns the $count last posts */
	function getLastForumPosts($count)
	{
		global $db;
		if (!is_numeric($count)) return false;

		$q  = 'SELECT t1.*,t2.userName AS authorName,t3.itemSubject AS parentSubject ';
		$q .= 'FROM tblForums AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$q .= 'LEFT OUTER JOIN tblForums AS t3 ON (t1.itemSubject="" AND t1.parentId=t3.itemId) ';
		$q .= 'WHERE t1.itemType='.FORUM_MESSAGE.' ';
		$q .= 'ORDER BY t1.timeCreated DESC ';
		$q .= 'LIMIT 0,'.$count;

		return $db->getArray($q);
	}

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

	function forumLockItem($itemId)
	{
		global $db, $session;
		if (!$session->isAdmin || !is_numeric($itemId)) return false;
		
		$q = 'UPDATE tblForums SET locked=1 WHERE itemId='.$itemId;
		$db->query($q);
	}

	function forumUnlockItem($itemId)
	{
		global $db, $session;
		if (!$session->isAdmin || !is_numeric($itemId)) return false;
		
		$q = 'UPDATE tblForums SET locked=0 WHERE itemId='.$itemId;
		$db->query($q);
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
	function getForumStructure($parentId=0, $arr='', $pre='')
	{
		global $db;
		if (!is_numeric($parentId)) return false;

		$q = 'SELECT itemSubject,itemId FROM tblForums WHERE parentId='.$parentId.' ORDER BY itemSubject';
		$list = $db->getArray($q);

		/* Lägg först till allt på samma nivå */
		for ($i=0; $i<count($list); $i++) {	//fixme: foreach
			if ($pre != '') {
				$arr[] = array('name' => $pre.' - '.$list[$i]['itemSubject'], 'itemId' => $list[$i]['itemId']);
			} else {
				$arr[] = array('name' => $list[$i]['itemSubject'], 'itemId' => $list[$i]['itemId']);
			}
		}

		/* Sen rekursiva */
		for ($i=0; $i<count($list); $i++) {	//fixme: foreach
			if ($pre != '') {
				$pre = $pre.' - '.$list[$i]['itemSubject'];
			} else {
				$pre = $list[$i]['itemSubject'];
			}

			$arr = getForumStructure($list[$i]['itemId'], $arr, $pre);
			$pre='';
		}

		return $arr;
	}


	function getForumFolderDepthHTML($itemId)
	{
		global $db, $config;
		if (!is_numeric($itemId)) return false;

		if ($itemId != 0) {

			$result = '';

			$q = 'SELECT itemSubject,parentId FROM tblForums WHERE itemId='.$itemId;
			$row = $db->getOneRow($q);
			if ($row['itemSubject']) {
				$result = $config['forum']['path_separator'].'<a href="forum.php?id='.$itemId.'">'.$row['itemSubject'].'</a>';
			}
			$result = getForumFolderDepthHTML($row['parentId']).$result;
			return $result;
		}
		$result = '<a href="forum.php">'.$config['forum']['rootname'].'</a>';
		return $result;
	}

	function displayCurrentForumContent(&$db, $itemId)
	{
		echo 'displayCurrentForumContent() deprecated ! dont use';
	}


	function displayRootForumContent()
	{
		$list = getForumItems();

		if (!count($list)) return;
		
		$str = '';

		for ($i=0; $i<count($list); $i++) {

			$subject = $list[$i]["itemSubject"];
			if (strlen($subject)>35) $subject = substr($subject,0,35).'..';

			if (!$subject) {
				$subject = '(Inget navn)';
			}

			$str .= '<div class="forum_header"><a href="forum.php?id='.$list[$i]['itemId'].'">'.$subject.'</a></div>';
			$str .= displaySubfolders($list[$i]['itemId']).'<br>';
		}

		return $str;
	}
	
	function displaySubfolders($itemId)
	{
		global $config;
		
		if (!is_numeric($itemId)) return false;

		$data = getForumItem($itemId);
		$list = getForumItems($itemId);

		$str  = '<table width="100%" cellpadding=0 cellspacing=0 border=1 class="forum_borders">';
		$str .= '<tr class="forum_subheader">';
		$str .= '<th width=30></th>';
		$str .= '<th>&nbsp;Forum</th>';


		if ($data['parentId'] == 0) {
			$str .= '<th width=200 align="center">Last thread</th>';
			$str .= '<th width=70 align="center">Threads</th>';
			$str .= '<th width=70 align="center">Posts</th>';
		} else {
			$str .= '<th width=200 align="center">Last post</th>';
			$str .= '<th width=70 align="center">Threads</th>';
			$str .= '<th width=70 align="center">Posts</th>';
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

			$data = getForumThreadContentLastPost($list[$i]['itemId']);
			$str .= '<td class="forum_item_text" width=200>';
			if ($data) {
				if ($data['itemSubject']) {
					$str .= '<a href="forum.php?id='.$data['itemId'].'">'.$data['itemSubject'].'</a><br>';
				} else {
					$str .= '<a href="forum.php?id='.$data['parentId'].'#post'.$data['itemId'].'">'.$data['parentSubject'].'</a><br>';
				}
				$str .= $config['forum']['text']['by'].' '.nameLink($data['authorId'], $data['authorName']).'<br>';
				$str .= $data['timeCreated'];
			} else {
				$str .= 'Never';
			}
			$str .= '</td>';
			$str .= '<td align="center">'.formatNumber(getForumItemCountFlat($list[$i]['itemId'])).'</td>';
			$str .= '<td align="center">'.formatNumber(getForumThreadContentCount($list[$i]['itemId'])).'</td>';
			$str .= '</tr>';
		}
		$str .= '</table>';

		return $str;
	}
	
	/* Returns item data for the last post in any of the threads with parentId=$itemId */
	function getForumThreadContentLastPost($itemId)
	{
		global $db;
		if (!is_numeric($itemId)) return false;
		
		$q = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId;
		$list = $db->getArray($q);

		$newest_time = 0;

		for ($i=0; $i<count($list); $i++) {
			$q =	'SELECT itemId, timeCreated FROM tblForums '.
						'WHERE parentId='.$list[$i]['itemId'].' '.
						'ORDER BY timeCreated DESC LIMIT 0,1';
			$data = $db->getOneRow($q);

			if ($data['timeCreated'] > $newest_time) {
				$newest_time = $data['timeCreated'];
				$newest_id = $data['itemId'];
			}
		}
		
		if ($newest_time) {
			$data = getForumItem($newest_id);
			if (!$data['itemSubject']) {
				//fills in parent's subject if subject is missing
				$parent_data = getForumItem($data['parentId']);
				$data['parentSubject'] = $parent_data['itemSubject'];
			}
			return $data;
		}
		
		return false;
	}

	/* Returns the number of items with $itemId as parent, non-recursive */
	function getForumItemCountFlat($itemId)
	{
		global $db;
		if (!is_numeric($itemId)) return false;

		$q = 'SELECT COUNT(itemId) FROM tblForums WHERE parentId='.$itemId;
		return $db->getOneItem($q);
	}
	
	/* Returns the total number of posts contained in all the threads with parentId=$itemId */
	function getForumThreadContentCount($itemId)
	{
		global $db;
		//fixme: kanske byta namn på funktionen
		if (!is_numeric($itemId)) return false;
		
		$q = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId;
		$list = $db->getArray($q);

		$cnt = 0;
		for ($i=0; $i<count($list); $i++) {
			$q = 'SELECT COUNT(itemId) FROM tblForums WHERE parentId='.$list[$i]['itemId'];
			$cnt += $db->getOneItem($q);
		}

		return $cnt;
	}

	function displayForumContentFlat($itemId)
	{
		global $db, $config;
		if (!is_numeric($itemId)) return false;

		$result = '';
		
		$data = getForumItem($itemId);
		$list = getForumItems($itemId, false);
		
		if ($data['parentId'] == 0) {
			$result .= '<div class="forum_header">'.$data['itemSubject'].'</div>';
		} else {
			$result .= '<div class="forum_header">Threads in forum: '.$data['itemSubject'].'</div>';
		}

		$result .= '<table width="100%" cellpadding=0 cellspacing=0 border=1 class="forum_borders">';
		$result .= '<tr class="forum_subheader">';
		$result .= '<th width=30></th>';
		if ($data['parentId'] == 0) {
			$result .= '<th>&nbsp;Forum</th>';			
			$result .= '<th width=200 align="center">Last thread</th>';
			$result .= '<th width=70 align="center">Threads</th>';	
		} else {
			$result .= '<th>&nbsp;Thread</th>';
			$result .= '<th width=200 align="center">Last post</th>';
			$result .= '<th width=70 align="center">Replies</th>';
		}
		$result .= '<th width=70 align="center">Views</th>';
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
				if ($list[$i]['sticky'] == 2) $result .= '<b>Announcement: </b>';
				$result .= '<a href="forum.php?id='.$list[$i]['itemId'].'">'.$list[$i]['itemSubject'].'</a><br>';
				$result .= $list[$i]['timeCreated'].'<br>';
				$result .= 'by '.nameLink($list[$i]['authorId'], $list[$i]['authorName']);
			$result .= '</td>';

			$lastreply = getForumLastReply($list[$i]['itemId']);
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
				$result .= $lastreply['timeCreated'].'<br>by '.nameLink($lastreply['userId'], $lastreply['userName']);
			} else {
				if ($data['parentId']) $result .= '<a href="forum.php?id='.$list[$i]['itemId'].'"><img src="icons/forum_lastpost.png" title="Go to post" width=15 height=15></a> ';
				$subject = $list[$i]['itemSubject'];
				if (mb_strlen($subject) > 25) $subject = mb_substr($subject, 0, 25).'...';
				$result .= '<a href="forum.php?id='.$list[$i]['itemId'].'">'.$subject.'</a><br>';
				$result .= $list[$i]['timeCreated'].'<br>by '.nameLink($list[$i]['authorId'], $list[$i]['authorName']);
			}
			$result .= '</td>';

			$result .= '<td align="center">'.formatNumber(getForumMessageCount($list[$i]['itemId'], false)).'</td>';
			$result .= '<td align="center">'.formatNumber($list[$i]['itemRead']).'</td>';
			$result .= '</tr>';
		}
		
		$result .= '</table>';
		return $result;
	}
	
	function getForumLastReply($itemId)
	{
		global $db;
		if (!is_numeric($itemId)) return false;

		//returns timestamp of last reply to $itemId
		$q  = 'SELECT t1.*,t2.userId,t2.userName FROM tblForums AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$q .= 'WHERE t1.parentId='.$itemId.' ';
		$q .= 'ORDER BY t1.timeCreated DESC LIMIT 0,1';
		return $db->getOneRow($q);
	}
	
	//todo: ta en optional parameter $highlight för sökresultat
	//bugg: $highlight ändrar på enkodade htmltaggar vilket resulterar i massa html-leakage i resultatet
	function showForumPost($item, $headertext = '', $show_links = true, $highlight = '')
	{
		global $session, $config;
		
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
		$content .= '<th width=160>'.$item['timeCreated'].'</th>';
		if ($headertext) {
			$content .= '<th align="right">'.$headertext.'</th>';
		} else {
			$content .= '<th></th>';
		}
		$content .= '</tr>';

		$content .= '<tr class="forum_item">';
		$content .= '<td width=160 valign="top" class="forum_item_text">';
			$content .= nameLink($item['authorId'], $item['authorName']).'<br><br>';

			/*
			$userpic = getThumbnail($item['authorId'], 'Egen avatar', $config['thumbnail_width'], $config['thumbnail_height'], false);
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
			*/

		$content .= '</td>';

		$content .= '<td valign="top" class="forum_item_text">';
			if ($subject) $content .= '<b>'.$subject.'</b><hr>';
			$content .= $body;

			$signature = loadUserdataSetting($session->id, $config['settings']['default_signature']);
			
			if ($signature) $content .= '<hr>'.$signature;

		$content .= '</td>';
		$content .= '</tr>';
			
		$content .= '<tr class="forum_item">';
		$content .= '<td></td>';
		$content .= '<td align="right">';
			if ($session->id && $show_links) {
				$content .= '<a href="forum_tipsa.php?id='.$item['itemId'].'">Tell a friend</a> ';

				if (!$item['locked']) {
					if (forumItemIsDiscussion($item['itemId'])) {
						$content .= '<a href="forum_new.php?id='.$item['itemId'].'&q='.$item['itemId'].'">Reply</a> ';
					} else {
						$content .= '<a href="forum_new.php?id='.$item['parentId'].'&q='.$item['itemId'].'">Reply</a> ';
					}

					if ($item['authorId'] == $session->id || $session->isAdmin) {
						$content .= '<a href="forum_edit.php?id='.$item['itemId'].'">Edit</a> ';
					}
				}

				if ($session->isAdmin) {
					$content .= '<a href="forum_delete.php?id='.$item['itemId'].'">Remove</a> ';
				}
				
				if ($session->isAdmin && forumItemIsDiscussion($item['itemId'])) {
					if (!$item['locked']) {
						$content .= '<a href="forum_lock.php?id='.$item['itemId'].'">L&aring;s</a> ';
					} else {
						$content .= '<a href="forum_lock.php?id='.$item['itemId'].'&unlock">L&aring;s upp</a> ';
					}
					$content .= '<a href="forum_move.php?id='.$item['itemId'].'">Move</a> ';
				}

				if ($session->id != $item['authorId']) {
					$content .= '<a href="forum_report.php?id='.$item['itemId'].'">Report</a> ';
				}
			}

		$content .= '</td></tr>';
		$content .= '</table><br>';

		return $content;
	}
	
	
	function displayDiscussionContentFlat($itemId)
	{
		global $db, $config;
		if (!is_numeric($itemId)) return false;
		
		$content = '';
		
		$item = getForumItem($itemId);
		$content .= showForumPost($item, 'First post');

		$list = getForumItems($itemId);

		for ($i=0; $i<count($list); $i++) {
			$content .= showForumPost($list[$i], 'Svar #'.($i+1));
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
				$str .= '<td valign="top">'.$list[$i]['timeCreated'].'</td>';
				$str .= '</tr>';
			}
			$str .= '</table>';
		} else {
			$str = 'The user has not written any posts';
		}
		
		return $str;
	}	

	/* Returns a list of search results with forum items */
	function getForumSearchResults($criteria, $method, $page, $limit)
	{
		global $db;
		$criteria = $db->escape($criteria);
		if (!is_numeric($page) || !is_numeric($limit)) return false;

		if (!$criteria || !$method || !$page || !$limit) return false;

		$list = explode(' ', $criteria);

		$q  = 'SELECT t1.*,t2.userName AS authorName FROM tblForums AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$q .= 'WHERE ';
		$q .= getForumSearchQuery($list);

		switch ($method) {
			case 'mostread': //mest läst
				$q .= 'ORDER BY t1.itemRead DESC '; break;

			case 'oldfirst': //älst först
				$q .= 'ORDER BY t1.timeCreated ASC '; break;

			case 'newfirst': default: //nyast först, default
				$q .= 'ORDER BY t1.timeCreated DESC '; break;
		}

		$q .= 'LIMIT '.(($page-1) * $limit).','.$limit;

		return $db->getArray($q);
	}

	function getForumSearchResultsCount($criteria)
	{
		global $db;
		$criteria = $db->escape($criteria);
		if (!$criteria) return false;

		$list = explode(' ', $criteria);

		$q  = 'SELECT COUNT(t1.itemId) FROM tblForums AS t1 ';
		$q .= 'WHERE '.getForumSearchQuery($list);

		return $db->getOneItem($q);
	}

	/* $list är en array med ord att söka på */
	function getForumSearchQuery($list)
	{
		$sql = '';
		for ($i=0; $i<count($list); $i++) {

			$curr = $list[$i];
			if (substr($curr,0,1) == '+') {
				//kräv detta

				$curr = substr($curr,1);
				if ($i>0) {
					$sql .= 'AND ';
				}
				$sql .= '(t1.itemSubject LIKE "%'.$curr.'%" OR t1.itemBody LIKE "%'.$curr.'%") ';

			} else if (substr($curr,0,1) == '-') {
				//INTE detta

				if (count($list)==1) { //tillåt inte sökning på allt UTAN ett ord..
					return;
				}

				$curr = substr($curr,1);
				if ($i>0) {
					$sql .= 'AND ';
				}
				$sql .= 'NOT (t1.itemSubject LIKE "%'.$curr.'%" OR t1.itemBody LIKE "%'.$curr.'%") ';

			} else {
				//frivilligt (typ detta ELLER nåt annat)

				if ($i>0) {
					$sql .= 'OR ';
				}
				$sql .= '(t1.itemSubject LIKE "%'.$curr.'%" OR t1.itemBody LIKE "%'.$curr.'%") ';
			}
		}

		$sql .= 'AND t1.itemDeleted=0 ';
		return $sql;
	}
?>