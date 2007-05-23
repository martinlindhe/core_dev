<?
	/*
		atom_comments.php - set of functions to implement comments, used by various modules

		By Martin Lindhe, 2007
	*/

	define('COMMENT_NEWS',					1);
	define('COMMENT_BLOG',					2);	//anonymous or registered users comments on a blog

	define('COMMENT_ADBLOCKRULE',		20);

	/* Comment types only meant for the admin's eyes */
	define('COMMENT_MODERATION_QUEUE',	30);	//owner = tblModerationQueue.queueId

	function addComment($commentType, $ownerId, $commentText, $privateComment = false)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($commentType) || !is_numeric($ownerId) || !is_bool($privateComment)) return false;
		$commentText = $db->escape(htmlspecialchars($commentText));

		if ($privateComment) $private = 1;
		else $private = 0;

		$q = 'INSERT INTO tblComments SET ownerId='.$ownerId.', userId='.$session->id.', userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).', commentType='.$commentType.', commentText="'.$commentText.'", commentPrivate='.$private.', timeCreated=NOW()';
		return $db->insert($q);
	}
	
	function updateComment($commentType, $ownerId, $commentId, $commentText)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($commentType) || !is_numeric($ownerId) || !is_numeric($commentId)) return false;
		$commentText = $db->escape(htmlspecialchars($commentText));

		$q  = 'UPDATE tblComments SET commentText="'.$commentText.'",timeCreated=NOW(),userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).' ';
		$q .= 'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND userId='.$session->id;

		$db->query($q);
	}
	
	function deleteComment($commentId)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($commentId)) return false;

		$db->query('UPDATE tblComments SET deletedBy='.$session->id.',timeDeleted=NOW() WHERE commentId='.$commentId);
	}
	
	/* Deletes all comments for this commentType & ownerId. returns the number of rows deleted */
	function deleteComments($commentType, $ownerId)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($commentType) || !is_numeric($ownerId)) return false;
		
		$q = 'UPDATE tblComments SET deletedBy='.$session->id.',timeDeleted=NOW() WHERE commentType='.$commentType.' AND ownerId='.$ownerId;
		return $db->delete($q);
	}

	function getComments($commentType, $ownerId, $privateComments = false)
	{
		global $db;

		if (!is_numeric($commentType) || !is_numeric($ownerId) || !is_bool($privateComments)) return array();

		$q  = 'SELECT t1.*,t2.userName FROM tblComments AS t1 '.
					'LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId) '.
					'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND deletedBy=0';

		if ($privateComments === false) $q .= ' AND commentPrivate=0';

		$q .=	' ORDER BY timeCreated DESC';
		return $db->getArray($q);
	}

	/* returns the last comment posted for $ownerId object. useful to retrieve COMMENT_FILE_DESC where max 1 comment is posted per object */
	function getLastComment($commentType, $ownerId, $privateComments = false)
	{
		global $db;

		if (!is_numeric($commentType) || !is_numeric($ownerId) || !is_bool($privateComments)) return false;

		$q  = 'SELECT * FROM tblComments '.
					'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND deletedBy=0';

		if ($privateComments === false) $q .= ' AND commentPrivate=0';

		$q .=	' ORDER BY timeCreated DESC';
		$q .= ' LIMIT 0,1';

		return $db->getOneRow($q);
	}
	
	function getCommentsCount($commentType, $ownerId)
	{
		global $db;

		if (!is_numeric($commentType) || !is_numeric($ownerId)) return false;

		$q =	'SELECT COUNT(commentId) FROM tblComments '.
					'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND deletedBy=0';
		return $db->getOneItem($q);
	}

	/* Helper function, standard "show comments" to be used by another module
		Used by these modules: functions_news.php
	*/
	function showComments($commentType, $ownerId)
	{
		if (!is_numeric($commentType) || !is_numeric($ownerId)) return false;

		if (!empty($_POST['cmt'])) {
			addComment($commentType, $ownerId, $_POST['cmt']);
		}

		/* Visar kommentarer till artikeln */
		$list = getComments($commentType, $ownerId);

		echo '<div class="comment_header" onclick="toggle_element_by_name(\'comments_holder\')">'.count($list).' Comments</div>';

		echo '<div id="comments_holder">';
		foreach ($list as $row) {
			echo '<div class="comment_text">'.$row['commentText'].'</div>';
			echo '<div class="comment_details">'.'By '.$row['userName'].' at '.$row['timeCreated'].'</div>';
		}

		echo '<form method="post" action="">';
		echo '<textarea name="cmt" cols="30" rows="6"></textarea><br/>';
		echo '<input type="submit" class="button" value="Add comment"/>';
		echo '</form>';
		
		echo '</div>';	//id="comments"
	}
?>