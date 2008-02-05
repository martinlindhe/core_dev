<?
/**
 * $Id$
 *
 * Set of functions to implement comments, used by various modules
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	define('COMMENT_NEWS',					1);
	define('COMMENT_BLOG',					2);		//anonymous or registered users comments on a blog
	define('COMMENT_IMAGE',					3);		//anonymous or registered users comments on a image
	define('COMMENT_TODOLIST',			4);		//todolist item comments
	define('COMMENT_GENERIC',				5);		//generic comment type
	define('COMMENT_PASTEBIN',			6);		//"pastebin" text. anonymous submissions are allowed
	define('COMMENT_SCRIBBLE',			7);		//scribble board

	define('COMMENT_ADMIN_IP',			10);	//a comment on a specific IP number, written by an admin (only shown to admins), ownerId=geoip number

	/* Comment types only meant for the admin's eyes */
	define('COMMENT_MODERATION',		30);	//owner = tblModeration.queueId

	/**
	 *
	 */
	function addComment($_type, $ownerId, $commentText, $privateComment = false)
	{
		global $db, $session;
		if (!is_numeric($_type) || !is_numeric($ownerId) || !is_bool($privateComment)) return false;

		if ($_type != COMMENT_IMAGE && $_type != COMMENT_PASTEBIN && !$session->id) return false;

		$commentText = $db->escape(htmlspecialchars($commentText));

		if ($privateComment) $private = 1;
		else $private = 0;

		$q = 'INSERT INTO tblComments SET ownerId='.$ownerId.', userId='.$session->id.', userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).', commentType='.$_type.', commentText="'.$commentText.'", commentPrivate='.$private.', timeCreated=NOW()';
		return $db->insert($q);
	}

	/**
	 *
	 */
	function updateComment($commentType, $ownerId, $commentId, $commentText)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($commentType) || !is_numeric($ownerId) || !is_numeric($commentId)) return false;

		$commentText = $db->escape(htmlspecialchars($commentText));

		$q  = 'UPDATE tblComments SET commentText="'.$commentText.'",timeCreated=NOW(),userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).' ';
		$q .= 'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND userId='.$session->id;

		$db->query($q);
	}

	/**
	 * Marks a comment as deleted
	 *
	 * \param $commentId comment to delete
	 */
	function deleteComment($commentId)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($commentId)) return false;
		$db->query('UPDATE tblComments SET deletedBy='.$session->id.',timeDeleted=NOW() WHERE commentId='.$commentId);
	}

	/**
	 * Deletes all comments for this commentType & ownerId. returns the number of rows deleted
	 */
	function deleteComments($commentType, $ownerId)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($commentType) || !is_numeric($ownerId)) return false;

		$q = 'UPDATE tblComments SET deletedBy='.$session->id.',timeDeleted=NOW() WHERE commentType='.$commentType.' AND ownerId='.$ownerId;
		return $db->delete($q);
	}

	/**
	 *
	 */
	function getComments($commentType, $ownerId, $privateComments = false)
	{
		global $db;
		if (!is_numeric($commentType) || !is_numeric($ownerId) || !is_bool($privateComments)) return array();

		$q  = 'SELECT t1.*,t2.userName FROM tblComments AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$q .= 'WHERE t1.ownerId='.$ownerId.' AND t1.commentType='.$commentType.' AND t1.deletedBy=0';

		if ($privateComments === false) $q .= ' AND t1.commentPrivate=0';

		$q .=	' ORDER BY t1.timeCreated DESC';
		return $db->getArray($q);
	}

	/**
	 * Returns all comments to all objects owned by $ownerId, newest first
	 */
	function getCommentsByOwner($_type, $ownerId)
	{
		global $db, $files;
		if (!is_numeric($_type) || !is_numeric($ownerId)) return false;

		$q  = 'SELECT t1.*,t2.userName FROM tblComments AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$q .= 'WHERE t1.commentType='.$_type.' AND t1.deletedBy=0 ORDER BY t1.timeCreated DESC';
		$list = $db->getArray($q);

		$result = array();
		foreach ($list as $row) {
			if ($_type == COMMENT_IMAGE && $files->getUploader($row['ownerId']) == $ownerId) {
				$result[] = $row;
			}
		}
		return $result;
	}

	/**
	 * Returns the last comment posted for $ownerId object.
	 * Useful to retrieve COMMENT_FILE_DESC where max 1 comment is posted per object
	 */
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

	/**
	 *
	 */
	function getCommentsCount($commentType, $ownerId)
	{
		global $db;
		if (!is_numeric($commentType) || !is_numeric($ownerId)) return false;

		$q =	'SELECT COUNT(commentId) FROM tblComments '.
					'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND deletedBy=0';
		return $db->getOneItem($q);
	}

	/**
	 * Helper function, standard "show comments" to be used by other modules
	 * col_w sets the column width of the textarea
	 */
	function showComments($_type, $ownerId = 0, $col_w = 30, $col_h = 6)
	{
		global $session, $config;
		if (!is_numeric($_type) || !is_numeric($ownerId) || !is_numeric($col_w) || !is_numeric($col_h)) return false;

		if (!empty($_POST['cmt'])) {
			addComment($_type, $ownerId, $_POST['cmt']);
		}

		if ($session->isAdmin && !empty($_GET['delete']) && is_numeric($_GET['delete'])) {
			deleteComment($_GET['delete']);
		}

		/* Shows all comments for this item */
		$list = getComments($_type, $ownerId);

		echo '<div class="comment_header" onclick="toggle_element_by_name(\'comments_holder\')">'.count($list).' comment'.(count($list) == 1 ? '':'s').'</div>';

		echo '<div id="comments_holder">';
		echo '<div id="comments_only">';
		foreach ($list as $row) {
			echo '<div class="comment_details">';
			echo Users::link($row['userId'], $row['userName']).'<br/>';
			echo $row['timeCreated'];
			echo '</div>';
			echo '<div class="comment_text">'.$row['commentText'];
			if ($session->isAdmin || $session->id == $row['userId']) {
				echo ' | <a href="'.URLadd('delete', $row['commentId']).'"><img src="'.$config['core_web_root'].'gfx/icon_delete.png"/></a>';
			}
			echo '</div>';
		}
		echo '</div>'; //id="comments_only"

		if ( ($session->id && $_type != COMMENT_MODERATION) ||
				($_type == COMMENT_IMAGE || $_type == COMMENT_PASTEBIN)
		) {
			echo '<form method="post" action="">';
			echo '<textarea name="cmt" cols="'.$col_w.'" rows="'.$col_h.'"></textarea><br/>';
			echo '<input type="submit" class="button" value="Add comment"/>';
			echo '</form>';
		}

		echo '</div>';	//id="comments_holder"

		return count($list);
	}

	/**
	 * Shows all comments to objects of $_type owned by $session->id, typically to be used by site admins
	 */
	function showAllComments($_type)
	{
		global $session, $config;

		if (!$session->id || !is_numeric($_type)) return false;

		if (!empty($_GET['delete']) && is_numeric($_GET['delete'])) {
			deleteComment($_GET['delete']);
		}

		switch ($_type) {
			case COMMENT_TODO_LIST:
				$list = getComments($_type, 0, true);
				break;

			default:
				$list = getCommentsByOwner($_type, $session->id);
		}

		foreach ($list as $row) {
			echo '<div class="comment_details">';
			echo makeThumbLink($row['ownerId']);
			echo Users::link($row['userId'], $row['userName']).'<br/>';
			echo $row['timeCreated'];
			echo '</div>';
			echo '<div class="comment_text">';
			echo '<a href="'.URLadd('delete', $row['commentId']).'"><img src="'.$config['core_web_root'].'gfx/icon_delete.png"/></a> ';
			echo $row['commentText'];
			echo '</div>';
		}
	}
?>
