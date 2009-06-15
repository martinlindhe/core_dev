<?php
/**
 * $Id$
 *
 * Set of functions to implement comments, used by various modules
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

//FIXME cleanup API and take userId as parameter, no tblUsers assumtions & usage

define('COMMENT_NEWS',				1);
define('COMMENT_BLOG',				2);		//anonymous or registered users comments on a blog
define('COMMENT_FILE',				3);		//anonymous or registered users comments on a image
define('COMMENT_TODOLIST',			4);		//todolist item comments
define('COMMENT_GENERIC',			5);		//generic comment type
define('COMMENT_PASTEBIN',			6);		//"pastebin" text. anonymous submissions are allowed
define('COMMENT_SCRIBBLE',			7);		//scribble board
define('COMMENT_CUSTOMER',			8);		//customer comments
define('COMMENT_FILEDESC',			9);		//this is a file description, only one per file can exist
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
	global $db, $h;
	if (!is_numeric($_type) || !is_numeric($ownerId) || !is_bool($privateComment)) return false;

	$commentText = $db->escape(htmlspecialchars($commentText));

	if ($privateComment) $private = 1;
	else $private = 0;

	$userId = !empty($h->session->id) ? $h->session->id : 0;

	$q = 'INSERT INTO tblComments SET ownerId='.$ownerId.', userId='.$userId.', userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).', commentType='.$_type.', commentText="'.$commentText.'", commentPrivate='.$private.', timeCreated=NOW()';
	return $db->insert($q);
}

/**
 * Update a comment
 */
function updateComment($commentType, $ownerId, $commentId, $commentText)
{	//FIXME commentId och ownerId parametrarna borde byta plats
	global $db, $h;
	if (!is_numeric($commentType) || !is_numeric($ownerId) || !is_numeric($commentId)) return false;

	$commentText = $db->escape(htmlspecialchars($commentText));

	$q  = 'UPDATE tblComments SET commentText="'.$commentText.'",timeCreated=NOW(),userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).' ';
	if (empty($h->session->id)) {
		$q .= 'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND userId=0';
	} else {
		$q .= 'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND userId='.$h->session->id;
	}
	$db->update($q);
}

/**
 * Marks a comment as deleted
 *
 * @param $commentId comment to delete
 */
function deleteComment($commentId)
{
	global $db, $h;
	if (!$h->session->id || !is_numeric($commentId)) return false;
	$db->update('UPDATE tblComments SET deletedBy='.$h->session->id.',timeDeleted=NOW() WHERE commentId='.$commentId);
}

/**
 * Deletes all comments for this commentType & ownerId. returns the number of rows deleted
 */
function deleteComments($commentType, $ownerId)
{
	global $db, $h;
	if (!$h->session->id || !is_numeric($commentType) || !is_numeric($ownerId)) return false;

	$q = 'UPDATE tblComments SET deletedBy='.$h->session->id.',timeDeleted=NOW() WHERE commentType='.$commentType.' AND ownerId='.$ownerId;
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
function getComments($commentType, $ownerId = 0, $privateComments = false, $limit = '')
{
	global $db, $h;
	if (!is_numeric($commentType) || !is_numeric($ownerId) || !is_bool($privateComments)) return array();

	$q  = 'SELECT t1.*';
	if (!empty($h->session->id)) $q .= ',t2.userName';
	$q .= ' FROM tblComments AS t1 ';
	if (!empty($h->session->id)) $q .= 'LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
	$q .= 'WHERE ';
	if ($ownerId) $q .= 't1.ownerId='.$ownerId.' AND ';
	$q .= 't1.commentType='.$commentType.' AND t1.deletedBy=0';

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
{	//FIXME remove, this is already implemented with getComments()
	global $db, $h;
	if (!is_numeric($_type) || !is_numeric($ownerId)) return false;

	$q  = 'SELECT t1.*,t2.userName FROM tblComments AS t1 ';
	$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
	$q .= 'WHERE t1.commentType='.$_type.' AND t1.deletedBy=0 ORDER BY t1.timeCreated DESC';
	$list = $db->getArray($q);

	$result = array();
	foreach ($list as $row) {
		if ($_type == COMMENT_FILE && $h->files->getUploader($row['ownerId']) == $ownerId) {
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
 * Get the number of comments during the specified time period
 */
function getCommentsCountPeriod($type, $dateStart, $dateStop)
{
	global $db;

	if (!is_numeric($type)) return false;

	$q = 'SELECT count(commentId) AS cnt FROM tblComments WHERE commentType = '.$type.' AND timeCreated BETWEEN "'.$db->escape($dateStart).'" AND "'.$db->escape($dateStop).'"';
	return $db->getOneItem($q);
}


/**
 * Helper function, standard "show comments" to be used by other modules
 * col_w sets the column width of the textarea
 */
function showComments($_type, $ownerId = 0, $col_w = 30, $col_h = 6, $limit = 15)
{
	global $h, $config;
	if (!is_numeric($_type) || !is_numeric($ownerId) || !is_numeric($col_w) || !is_numeric($col_h)) return false;

	if (!empty($_POST['cmt_'.$_type])) {
		addComment($_type, $ownerId, $_POST['cmt_'.$_type]);
		unset($_POST['cmt_'.$_type]);
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
		echo showComment($row);
	}
	if ($cnt >= 5) echo $pager['head'];
	echo '</div>'; //id="comments_only"

	if ( ($h->session->id && $_type != COMMENT_MODERATION) ||
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
 * Shows all comments to objects of $_type owned by $h->session->id, typically to be used by site admins
 */
function showAllComments($_type)
{
	global $h, $config;
	if (!$h->session->id || !is_numeric($_type)) return false;

	if (!empty($_GET['delete']) && is_numeric($_GET['delete'])) {
		deleteComment($_GET['delete']);
	}

	switch ($_type) {
		case COMMENT_TODO_LIST:
			$list = getComments($_type, 0, true);
			break;

		default:
			$list = getCommentsByOwner($_type, $h->session->id);
	}

	foreach ($list as $row) {
		echo showComment($row);
	}
}

/**
 * Default function for displaying comments
 * FIXME: make it possible to override this function
 */
function showComment($row)
{
	global $config, $h;

	if (!empty($_GET['cmt_delete']) && is_numeric($_GET['cmt_delete']) && ($_GET['cmt_delete'] == $row['commentId']) ) {
		//let users delete comments belonging to their files
		if ($h->session->isAdmin ||
			($_type == COMMENT_FILE && $h->files->getOwner($ownerId) == $h->session->id)
		) {
			deleteComment($_GET['cmt_delete']);	//FIXME: comment typ!
			unset($_GET['cmt_delete']);
			return false;
		}
	}

	$res = '<div class="comment_details">';
	//echo makeThumbLink($row['ownerId']);
	if ($row['userId']) {
		$res .= Users::link($row['userId']).'<br/>';
	} else {
		$res .= t('Anonymous').'<br/>';
	}
	$res .= '<font size="1">'.formatTime($row['timeCreated']).'</font>';
	$res .= '</div>';
	$res .= '<div class="comment_text">'.nl2br($row['commentText']);
	if ($h->session->id && ($h->session->isAdmin ||
		//allow users to delete their own comments
		$h->session->id == $row['userId'] ||
		//allow users to delete comments on their files
		($row['commentType'] == COMMENT_FILE && $h->files->getOwner($row['ownerId']) == $h->session->id)
		)
	) {
		$res .= ' | ';
		$res .= coreButton('Delete', URLadd('cmt_delete', $row['commentId']) );

	}
	$res .= '</div>';
	return $res;
}
?>
