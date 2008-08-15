<?php
/**
 * $Id$
 *
 * Set of functions to implement comments, used by various modules
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

define('COMMENT_NEWS',				1);
define('COMMENT_BLOG',				2);		//anonymous or registered users comments on a blog
define('COMMENT_FILE',				3);		//anonymous or registered users comments on a image
define('COMMENT_TODOLIST',			4);		//todolist item comments
define('COMMENT_GENERIC',			5);		//generic comment type
define('COMMENT_PASTEBIN',			6);		//"pastebin" text. anonymous submissions are allowed
define('COMMENT_SCRIBBLE',			7);		//scribble board
define('COMMENT_CUSTOMER',			8);		//customer comments
define('COMMENT_ADMIN_IP',			10);	//a comment on a specific IP number, written by an admin (only shown to admins), ownerId=geoip number

/* Comment types only meant for the admin's eyes */
define('COMMENT_MODERATION',		30);	//owner = tblModeration.queueId
define('COMMENT_USER',				31);	//owner = tblUsers.userId, admin comments for a user

$comment_constants[COMMENT_NEWS]				= 'News';
$comment_constants[COMMENT_BLOG]				= 'Blog';
$comment_constants[COMMENT_FILE]				= 'File';
$comment_constants[COMMENT_TODOLIST]			= 'Todolist';
$comment_constants[COMMENT_GENERIC]				= 'Generic';
$comment_constants[COMMENT_PASTEBIN]			= 'Pastebin';
$comment_constants[COMMENT_SCRIBBLE]			= 'Scribble';
$comment_constants[COMMENT_ADMIN_IP]			= 'Admin IP';
$comment_constants[COMMENT_MODERATION]			= 'Moderation';
$comment_constants[COMMENT_USER]				= 'User';
$comment_constants[COMMENT_CUSTOMER]			= 'Customer';


/**
 * Add a comment
 */
function addComment($_type, $ownerId, $commentText, $privateComment = false)
{
	global $db, $session;
	if (!is_numeric($_type) || !is_numeric($ownerId) || !is_bool($privateComment)) return false;

	if ($_type != COMMENT_FILE && $_type != COMMENT_PASTEBIN && !$session->id) return false;

	$commentText = $db->escape(htmlspecialchars($commentText));

	if ($privateComment) $private = 1;
	else $private = 0;

	$q = 'INSERT INTO tblComments SET ownerId='.$ownerId.', userId='.$session->id.', userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).', commentType='.$_type.', commentText="'.$commentText.'", commentPrivate='.$private.', timeCreated=NOW()';
	return $db->insert($q);
}

/**
 * Update a comment
 */
function updateComment($commentType, $ownerId, $commentId, $commentText)
{
	global $db, $session;
	if (!$session->id || !is_numeric($commentType) || !is_numeric($ownerId) || !is_numeric($commentId)) return false;

	$commentText = $db->escape(htmlspecialchars($commentText));

	$q  = 'UPDATE tblComments SET commentText="'.$commentText.'",timeCreated=NOW(),userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).' ';
	$q .= 'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND userId='.$session->id;
	$db->update($q);
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
	$db->update('UPDATE tblComments SET deletedBy='.$session->id.',timeDeleted=NOW() WHERE commentId='.$commentId);
}

/**
 * Deletes all comments for this commentType & ownerId. returns the number of rows deleted
 */
function deleteComments($commentType, $ownerId)
{
	global $db, $session;
	if (!$session->id || !is_numeric($commentType) || !is_numeric($ownerId)) return false;

	$q = 'UPDATE tblComments SET deletedBy='.$session->id.',timeDeleted=NOW() WHERE commentType='.$commentType.' AND ownerId='.$ownerId;
	return $db->update($q);
}

/**
 * Returns the number of items in the comment search result
 */
function getCommentFreeTextSearchCount($text)
{
	global $db;

	$text = $db->escape($text);

	$q  = 'SELECT count(*) FROM tblComments ';
	$q .= 'WHERE commentText ';
	$q .= 'LIKE "%'.$text.'%"';
	return $db->getOneItem($q);
}

/**
 * Returns the comment search result
 */
function getCommentFreeTextSearch($text, $_limit_sql = '')
{
	global $db;

	$text = $db->escape($text);

	$q  = 'SELECT t.*, u1.userName AS authorName FROM tblComments t, ';
	$q .= 'tblUsers u1 WHERE t.userId = u1.userId AND t.commentText ';
	$q .= 'LIKE "%'.$text.'%" ORDER BY t.timeCreated DESC'.$_limit_sql;

	return $db->getArray($q);
}

/**
 * Returns comments of specified type and/or owner
 */
function getComments($commentType, $ownerId, $privateComments = false, $limit = '')
{
	global $db;
	if (!is_numeric($commentType) || !is_numeric($ownerId) || !is_bool($privateComments)) return array();

	$q  = 'SELECT t1.*,t2.userName FROM tblComments AS t1 ';
	$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
	$q .= 'WHERE t1.ownerId='.$ownerId.' AND t1.commentType='.$commentType.' AND t1.deletedBy=0';

	if ($privateComments === false) $q .= ' AND t1.commentPrivate=0';

	$q .= ' ORDER BY t1.timeCreated DESC '.$limit;
	return $db->getArray($q);
}

/**
 * Return comment by commentId
 */
function getComment($commentId)
{
	global $db;
	if (!is_numeric($commentId)) return false;

	$q  = 'SELECT * FROM tblComments ';
	$q .= 'WHERE commentId='.$commentId.' AND deletedBy=0 LIMIT 1';

	return $db->getOneItem($q);
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
		if ($_type == COMMENT_FILE && $files->getUploader($row['ownerId']) == $ownerId) {
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

	$q = 'SELECT * FROM tblComments';
	$q .= ' WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND deletedBy=0';
	if ($privateComments === false) $q .= ' AND commentPrivate=0';
	$q .= ' ORDER BY timeCreated DESC';
	$q .= ' LIMIT 0,1';
	return $db->getOneRow($q);
}

/**
 * Get number of comments
 */
function getCommentsCount($commentType, $ownerId, $privateComments = false)
{
	global $db;
	if (!is_numeric($commentType) || !is_numeric($ownerId)) return false;

	$q = 'SELECT COUNT(commentId) FROM tblComments';
	$q .= ' WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND deletedBy=0';
	if ($privateComments === false) $q .= ' AND commentPrivate=0';
	return $db->getOneItem($q);
}

/**
 * Helper function, standard "show comments" to be used by other modules
 * col_w sets the column width of the textarea
 */
function showComments($_type, $ownerId = 0, $col_w = 30, $col_h = 6, $limit = 15)
{
	global $session, $config;
	if (!is_numeric($_type) || !is_numeric($ownerId) || !is_numeric($col_w) || !is_numeric($col_h)) return false;

	if (!empty($_POST['cmt_'.$_type])) {
		addComment($_type, $ownerId, $_POST['cmt_'.$_type]);
		unset($_POST['cmt_'.$_type]);
	}

	if (!empty($_GET['delete']) && is_numeric($_GET['delete'])) {
		//let users delete comments belonging to their files
		if ($session->isAdmin ||
			($_type == COMMENT_FILE && Files::getOwner($ownerId) == $session->id)
		) {				
			deleteComment($_GET['delete']);	//FIXME: comment typ!
			unset($_GET['delete']);
		}
	}

	//Gets all comments for this item
	$cnt = getCommentsCount($_type, $ownerId);

	echo '<div class="comment_header" onclick="toggle_element_by_name(\'comments_holder\')">'.$cnt.' '.($cnt == 1 ? t('comment'):t('comments')).'</div>';

	echo '<div id="comments_holder">';
	echo '<div id="comments_only">';
	$pager = makePager($cnt, $limit);

	$list = getComments($_type, $ownerId, false, $pager['limit']);
	echo $pager['head'];
	foreach ($list as $row) {
		showComment($row);
	}
	if ($cnt >= 5) echo $pager['head'];
	echo '</div>'; //id="comments_only"

	if ( ($session->id && $_type != COMMENT_MODERATION) ||
			($_type == COMMENT_FILE || $_type == COMMENT_PASTEBIN)
	) {
		echo '<form method="post" action="">';
		echo xhtmlTextarea('cmt_'.$_type, '', $col_w, $col_h).'<br/>';
		echo xhtmlSubmit('Add comment');
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
		showComment($row);
	}
}

/**
 * Default function for displaying comments
 * FIXME: make it possible to override this function
 */
function showComment($row)
{
	global $config, $session;
	echo '<div class="comment_details">';
	//echo makeThumbLink($row['ownerId']);
	echo Users::link($row['userId'], $row['userName']).'<br/>';
	echo $row['timeCreated'];
	echo '</div>';
	echo '<div class="comment_text">'.nl2br($row['commentText']);
	if ($session->isAdmin ||
		//allow users to delete their own comments
		$session->id == $row['userId'] ||
		//allow users to delete comments on their files
		($row['commentType'] == COMMENT_FILE && Files::getOwner($row['ownerId']) == $session->id)
	) {
		echo ' | ';
		echo coreButton('Delete', URLadd('delete', $row['commentId']) );
		
	}
	echo '</div>';
}
?>
