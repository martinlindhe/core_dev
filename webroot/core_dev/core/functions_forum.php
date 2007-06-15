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

	function getForumName($itemId)
	{
		global $db;
		if (!is_numeric($itemId)) return false;

		$q  = 'SELECT itemSubject FROM tblForums WHERE itemId='.$itemId;
		return $db->getOneItem($q);
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

	/* Return the number of messages inside $itemId, recursive (default) */
	function getForumMessageCount($itemId, $recursive = true, $mecnt = 0)	//fixme: skicka med itemType param & rename function
	{
		global $db;
		if (!is_numeric($itemId) || !is_numeric($mecnt)) return false;

		$q = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId.' AND itemType='.FORUM_MESSAGE;
		$arr = $db->getArray($q);

		foreach ($arr as $row) {
			$mecnt++;
			if ($recursive === true) {
				$mecnt = getForumMessageCount($row['itemId'], $recursive, $mecnt);
			}
		}
		return $mecnt;
	}

	/* Return the number of items (folders & messages & discussions) inside $itemId, recursive */
	function getForumItemCount($itemId, $mecnt = 0)
	{
		global $db;
		if (!is_numeric($itemId) || !is_numeric($mecnt)) return false;

		$q = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId;

		$arr = $db->getArray($q);

		foreach ($arr as $row) {
			$mecnt++;
			$mecnt = getForumItemCount($row['itemId'], $mecnt);
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

	/* Returns false if item is a message but parent is a folder (item is a discussion then) */
	function forumItemIsMessage($itemId)
	{
		global $db;
		if (!is_numeric($itemId)) return false;

		$q = 'SELECT itemType, parentId FROM tblForums WHERE itemId='.$itemId;
		$row = $db->getOneRow($q);

		if ($row['itemType'] == FORUM_MESSAGE) {
			if (forumItemIsFolder($row['parentId'])) return false;
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

	function setForumItemParent($itemId, $parentId)
	{
		global $db;
		if (!is_numeric($itemId) || !is_numeric($parentId)) return false;

		$q = 'UPDATE tblForums SET parentId='.$parentId.' WHERE itemId='.$itemId;
		$db->update($q);
	}

	function addForumFolder($parentId, $folderName, $folderDesc = '')
	{
		global $db, $session;
		if (!$session->id || !is_numeric($parentId)) return false;

		$folderDesc = strip_tags($folderDesc);
		$folderName = $db->escape(strip_tags($folderName));
		$folderDesc = $db->escape($folderDesc);

		$q = 'INSERT INTO tblForums SET itemType='.FORUM_FOLDER.',authorId='.$session->id.',parentId='.$parentId.',itemSubject="'.$folderName.'",itemBody="'.$folderDesc.'",timeCreated=NOW()';
		return $db->insert($q);
	}

	function addForumMessage($parentId, $subject, $body, $sticky = 0)
	{
		global $db, $session, $config;

		if (!$session->id || !is_numeric($parentId) || !is_numeric($sticky)) return false;

		$body = strip_tags($body);
		$subject = $db->escape(strip_tags($subject));

		$body = substr($body, 0, $config['forum']['maxsize_body']);
		$body = $db->escape($body);

		$q = 'INSERT INTO tblForums SET itemType='.FORUM_MESSAGE.',authorId='.$session->id.',parentId='.$parentId.',itemSubject="'.$subject.'",itemBody="'.$body.'",timeCreated=NOW()';
		if ($sticky) $q .= ',sticky='.$sticky;
		$itemId = $db->insert($q);

		/* Auto-moderate */
		if (isObjectionable($subject) || isObjectionable($body)) addToModerationQueue($itemId, MODERATION_OBJECTIONABLE_POST);
		if (isSensitive($subject) || isSensitive($body)) addToModerationQueue($itemId, MODERATION_SENSITIVE_POST);

		/* Check if there is any users who should be notified about this new message */
		//notifySubscribers($parentId, $itemId, SUBSCRIBE_MAIL);
		return $itemId;
	}

	function getForumDepthHTML($type, $itemId)
	{
		global $db, $config;
		if (!is_numeric($type) || !is_numeric($itemId)) return false;

		if (!$itemId) {
			$result = '<a href="forum.php">'.$config['forum']['rootname'].'</a>';
			return $result;
		}

		$q = 'SELECT itemSubject,parentId FROM tblForums WHERE itemId='.$itemId;
		$row = $db->getOneRow($q);

		switch ($type) {
			case FORUM_MESSAGE:
				$subject = $row['itemSubject'];
				if ($subject) {
					if (mb_strlen($subject) > 35) $subject = mb_substr($subject, 0, 35).'...';
					$result = ' - <a href="forum.php?id='.$itemId.'">'.($subject != '' ? $subject : '(No name)').'</a>';
				} else {
					$result = '';
				}
				break;

			case FORUM_FOLDER:
				$result = '';
				if ($row['itemSubject']) $result = $config['forum']['path_separator'].'<a href="forum.php?id='.$itemId.'">'.$row['itemSubject'].'</a>';
				break;

			default: die('aouuu');
		}

		return getForumDepthHTML($type, $row['parentId']).$result;
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

	/* Sparar ändringar i ett inlägg/folder/whatever */
	function forumUpdateItem($itemId, $subject, $body, $sticky = 0)
	{
		global $db;
		if (!is_numeric($itemId) || !is_numeric($sticky)) return false;
		$subject = $db->escape($subject);
		$body = $db->escape($body);

		$q = 'UPDATE tblForums SET itemSubject="'.$subject.'",itemBody="'.$body.'",sticky='.$sticky.' WHERE itemId='.$itemId;
		$db->update($q);
	}

	/* Returns a list of all folder paths, ie folder1 - folder_in_folder1 etc... + folderid, used for now in accessgroup admin */
	function getForumStructure($parentId = 0, $arr = '', $pre = '')
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

	function displayRootForumContent()
	{
		global $config;
		$list = getForumItems();

		if (!count($list)) return;

		foreach ($list as $row) {

			$subject = $row["itemSubject"];
			if (strlen($subject)>35) $subject = substr($subject,0,35).'..';

			if (!$subject) $subject = '(No name)';

			echo '<div class="forum_overview_group">';
			echo '<a href="forum.php?id='.$row['itemId'].'">'.$subject.'</a><br/><br/>';
			$itemId = $row['itemId'];

			$data = getForumItem($itemId);
			$list = getForumItems($itemId);

			echo '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="forum_overview_table">';
			echo '<tr>';
			echo '<th width="40"></th>';	//for icons
			echo '<th>Forum</th>';
			echo '<th width="200" align="center">Last post</th>';
			echo '<th width="70" align="center">Topics</th>';
			echo '<th width="70" align="center">Posts</th>';
			echo '</tr>';

			$i = 0;
			foreach ($list as $row) {
				$i++;

				$subject = $row["itemSubject"];
				if (strlen($subject) > 50) $subject = substr($subject, 0, 50).'..';
				if (!$subject) {
					$subject = '(Inget navn)';
				}

				echo '<tr class="forum_overview_item_'.($i%2?'even':'odd').'" >';
				echo '<td align="center"><img src="'.$config['core_web_root'].'gfx/icon_forum_folder.png" alt="Folder"/></td>';
				echo '<td class="forum_item_text">';
					echo '<a href="forum.php?id='.$row['itemId'].'">'.$subject.'</a><br/>';
					echo $row['itemBody'];
				echo '</td>';

				$data = getForumThreadContentLastPost($row['itemId']);
				echo '<td class="forum_item_text" width=200>';
				if ($data) {
					if ($data['itemSubject']) {
						echo '<a href="forum.php?id='.$data['itemId'].'">'.$data['itemSubject'].'</a><br/>';
					} else {
						echo '<a href="forum.php?id='.$data['parentId'].'#post'.$data['itemId'].'">'.$data['parentSubject'].'</a><br/>';
					}
					echo 'by '.nameLink($data['authorId'], $data['authorName']).'<br/>';
					echo $data['timeCreated'];
				} else {
					echo 'Never';
				}
				echo '</td>';
				echo '<td align="center">'.formatNumber(getForumItemCountFlat($row['itemId'])).'</td>';
				echo '<td align="center">'.formatNumber(getForumThreadContentCount($row['itemId'])).'</td>';
				echo '</tr>';
			}
			echo '</table>';
			echo '</div><br/>';	//class="forum_overview_group"
		}
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

	//Displays the different forums of one root level category + activity details
	function displayForumContentFlat($itemId)
	{
		global $db, $config;
		if (!is_numeric($itemId)) return false;

		echo '<div class="forum_overview_group">';

		$data = getForumItem($itemId);
		$list = getForumItems($itemId, false);

		echo '<div class="forum_header">'.getForumDepthHTML(FORUM_FOLDER, $itemId).'</div>';
		/*
		if ($data['parentId'] == 0) {
			echo '<div class="forum_header">'.$data['itemSubject'].'</div>';
		} else {
			echo '<div class="forum_header">Topics under: '.$data['itemSubject'].'</div>';
		}
		*/
		echo '<br/>';

		echo '<table width="100%" class="forum_overview_table">';
		echo '<tr class="forum_subheader">';
		echo '<th width=30></th>';
		if ($data['parentId'] == 0) {
			echo '<th>&nbsp;Forum</th>';
			echo '<th width=200 align="center">Last topic</th>';
			echo '<th width=70 align="center">Topics</th>';
		} else {
			echo '<th>&nbsp;Topic</th>';
			echo '<th width=200 align="center">Last post</th>';
			echo '<th width=70 align="center">Replies</th>';
		}
		echo '<th width=70 align="center">Views</th>';
		echo '</tr>';

		$i = 0;
		foreach ($list as $row) {
			$i++;
			echo '<tr class="forum_overview_item_'.($i%2?'even':'odd').'" >';

			echo '<td align="center">';

			if ($row['sticky'] == 1) {
				echo '<img src="'.$config['core_web_root'].'gfx/icon_forum_sticky.png" alt="Sticky"/>';
			} else if ($row['sticky'] == 2) {
				echo '<img src="'.$config['core_web_root'].'gfx/icon_forum_announcement.png" alt="Announcement"/>';
			} else if ($data['parentId'] == 0) {
				echo '<img src="'.$config['core_web_root'].'gfx/icon_forum_folder.png" alt="Folder"/>';
			} else {
				if ($row['locked']) {
					echo '<img src="'.$config['core_web_root'].'gfx/icon_forum_locked.png" alt="Locked"/><br/>';
				} else {
					echo '<img src="'.$config['core_web_root'].'gfx/icon_forum_topic.png" alt="Message"/>';
				}
			}
			echo '</td>';

			echo '<td class="forum_item_text">';
				if ($row['sticky'] == 1) echo '<b>Sticky: </b>';
				if ($row['sticky'] == 2) echo '<b>Announcement: </b>';
				echo '<a href="forum.php?id='.$row['itemId'].'">'.$row['itemSubject'].'</a><br/>';
				echo $row['timeCreated'].'<br/>';
				echo 'by '.nameLink($row['authorId'], $row['authorName']);
			echo '</td>';

			$lastreply = getForumLastReply($row['itemId']);
			echo '<td class="forum_item_text">';
			if ($lastreply) {
				if ($data['parentId'] == 0) {
					$subject = $lastreply['itemSubject'];
					if (mb_strlen($subject) > 25) $subject = mb_substr($subject, 0, 25).'...';
					echo '<a href="forum.php?id='.$lastreply['itemId'].'#post'.$lastreply['itemId'].'">'.$subject.'</a><br/>';
				} else {
					$subject = $row['itemSubject'];
					if (mb_strlen($subject) > 25) $subject = mb_substr($subject, 0, 25).'...';
					echo '<a href="forum.php?id='.$row['itemId'].'#post'.$lastreply['itemId'].'">'.$subject.'</a><br/>';
				}
				echo $lastreply['timeCreated'].'<br/>by '.nameLink($lastreply['userId'], $lastreply['userName']);
			} else {
				$subject = $row['itemSubject'];
				if (mb_strlen($subject) > 25) $subject = mb_substr($subject, 0, 25).'...';
				echo '<a href="forum.php?id='.$row['itemId'].'">'.$subject.'</a><br/>';
				echo $row['timeCreated'].'<br/>by '.nameLink($row['authorId'], $row['authorName']);
			}
			echo '</td>';

			echo '<td align="center">'.formatNumber(getForumMessageCount($row['itemId'], false)).'</td>';
			echo '<td align="center">'.formatNumber($row['itemRead']).'</td>';
			echo '</tr>';
		}

		echo '</table>';
		echo '</div>';

		if ($data['parentId'] == 0) {
			echo '<br/>';
			echo '<div class="forum_overview_group">';
			echo '<b>Active topics</b><br/><br/>';
			echo 'fixme - list a few of the active topics within the above forums here';
			echo '</div>';
		}
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
	function showForumPost($item, $highlight = '')
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
				$body = eregi_replace($regexp, $replace, $body);
			}
		}

		echo '<a name="post'.$item['itemId'].'" id="post'.$item['itemId'].'"></a>';

		echo '<table width="100%" class="forum_post_table">';

		echo '<tr class="forum_post_heading forum_post_item">';

		echo '<td valign="top" class="forum_item_text">';
		if ($subject) echo '<b>'.$subject.'</b><hr/>';
		
		echo '<div class="forum_post_details">';
		echo '<img src="'.$config['core_web_root'].'gfx/icon_forum_post.png" alt="Post"/> ';
		echo 'by '.nameLink($item['authorId'], $item['authorName']).' on '.$item['timeCreated'];
		echo '</div><br/>';

		echo $body;
		$signature = loadUserdataSetting($session->id, $config['settings']['default_signature']);
		if ($signature) echo '<hr/>'.$signature;
		echo '</td>';

		echo '<td width="120" valign="top" class="forum_item_text">';
		echo nameThumbLink($item['authorId'], $item['authorName']).'<br/><br/>';
		//echo 'Status: '.getUserStatus($item['authorId']).'<br/>';
		//echo 'Join date: '.getUserCreated($item['authorId']).'<br/>';
		echo 'Posts: '.getForumPostsCount($item['authorId']);
		echo '</td>';

		echo '</tr>';

		if (!$session->id) {
			echo '</table><br/>';
			return;
		}

		echo '<tr class="forum_item">';
		
		echo '<td colspan="2" align="right">';
		
		echo '<a href="forum_tipsa.php?id='.$item['itemId'].'">Tell a friend</a> ';

		if (!$item['locked']) {
			if (forumItemIsDiscussion($item['itemId'])) {
				echo '<a href="forum_new.php?id='.$item['itemId'].'&amp;q='.$item['itemId'].'">Reply</a> ';
			} else {
				echo '<a href="forum_new.php?id='.$item['parentId'].'&amp;q='.$item['itemId'].'">Reply</a> ';
			}

			if ($item['authorId'] == $session->id || $session->isAdmin) {
				echo '<a href="forum_edit.php?id='.$item['itemId'].'">Edit</a> ';
			}
		}

		if ($session->isAdmin) {
			echo '<a href="forum_delete.php?id='.$item['itemId'].'">Remove</a> ';
		}

		if ($session->isAdmin && forumItemIsDiscussion($item['itemId'])) {
			if (!$item['locked']) {
				echo '<a href="forum_lock.php?id='.$item['itemId'].'">Lock</a> ';
			} else {
				echo '<a href="forum_lock.php?id='.$item['itemId'].'&unlock">Unlock</a> ';
			}
			echo '<a href="forum_move.php?id='.$item['itemId'].'">Move</a> ';
		}

		if ($session->id != $item['authorId']) {
			echo '<a href="forum_report.php?id='.$item['itemId'].'">Report</a> ';
		}

		echo '</td></tr>';
		echo '</table><br/>';
	}


	function displayDiscussionContentFlat($itemId)
	{
		global $db, $config;
		if (!is_numeric($itemId)) return false;

		echo '<div class="forum_overview_group">';
		echo getForumDepthHTML(FORUM_MESSAGE, $itemId).'<br/><br/>';

		$item = getForumItem($itemId);
		showForumPost($item);

		$list = getForumItems($itemId);

		for ($i=0; $i<count($list); $i++) {
			showForumPost($list[$i]);
		}
		echo '</div>';
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

	function deleteForumItem($itemId)
	{
		global $db;
		if (!is_numeric($itemId)) return false;

		$q = 'DELETE FROM tblForums WHERE itemId='.$itemId;
		$db->delete($q);
	}

	/* Deletes itemId and everything below it. also deletes associated moderation queue entries */
	function deleteForumItemRecursive($itemId, $loop = false)
	{
		global $db;
		if (!is_numeric($itemId)) return false;

		$q = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId;
		$arr = $db->getArray($q);

		foreach ($arr as $row) {
			$q = 'DELETE FROM tblForums WHERE itemId='.$row['itemId'];
			$db->delete($q);

			//removeFromModerationQueueByItemId($row['itemId'], MODERATION_REPORTED_POST);
			deleteForumItemRecursive($row['itemId'], true);
		}

		if ($loop != true) {
			$q = 'DELETE FROM tblForums WHERE itemId='.$itemId;
			$db->delete($q);

			//removeFromModerationQueueByItemId($itemId, MODERATION_REPORTED_POST);
		}
	}

	/* Returns the number of messages that $userId has written in the forums */
	function getForumPostsCount($userId)
	{
		global $db;
		if (!is_numeric($userId)) return false;

		$q = 'SELECT COUNT(itemId) FROM tblForums WHERE authorId='.$userId.' AND itemType='.FORUM_MESSAGE;
		return $db->getOneItem($q);
	}


	function displayForum($_id)
	{
		global $session;
		if (!is_numeric($_id)) return false;

/*
		// Starta/avsluta bevakning
		if ($session->id) {
			if (isset($_GET['subscribe'])) {
				addSubscription($_GET['subscribe'], SUBSCRIBE_MAIL);
			} else if (isset($_GET['unsubscribe'])) {
				removeSubscription($_GET['unsubscribe'], SUBSCRIBE_MAIL);
			}
		}

		if ($config['forum']['allow_votes'] && !empty($_POST['vote']) && !empty($_POST['voteId'])) {
			addForumVote($_POST['voteId'], $_POST['vote']);
		}*/

		if (!$_id) {
			//display root level
			echo displayRootForumContent();

			if ($session->isAdmin) echo '<a href="forum_new.php?id=0">New root level category</a>';
			return;
		}

		if (forumItemIsFolder($_id)) {
			//display content of a folder (parent = root)
			echo displayForumContentFlat($_id);

			echo '<a href="forum_new.php?id='.$_id.'">New discussion</a><br/>';

			if ($session->isAdmin) {
				echo '<a href="forum_edit.php?id='.$_id.'">Edit forum name</a><br/>';
				echo '<a href="forum_delete.php?id='.$_id.'">Delete forum</a><br/>';
			}
		} else {
			//display flat discussion overview
			echo displayDiscussionContentFlat($_id);
		}
	}
?>