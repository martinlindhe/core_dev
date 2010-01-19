<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

//STATUS: deprecate, use class.Blog !

require_once('atom_moderation.php');		//for automatic moderation of new blogs, and for "report blog" feature
require_once('atom_comments.php');			//for comment support for blogs
require_once('atom_rating.php');			//for rating support for blogs
require_once('design_blog.php');

$config['blog']['moderation'] = true;		//enables automatic moderation of new blogs
$config['blog']['allowed_tabs'] = array('Blog', 'BlogEdit', 'BlogDelete', 'BlogReport', 'BlogComment', 'BlogFiles');
$config['blog']['allow_rating'] = true;		//allow users to rate blogs

/**
 * XXX
 */
function addBlog($categoryId, $title, $body, $isPrivate = 0)
{
	global $h, $db, $config;
	if (!$h->session->id || !is_numeric($categoryId) || !is_numeric($isPrivate)) return false;

	$title = $db->escape($title);
	$body = $db->escape($body);

	$q = 'INSERT INTO tblBlogs SET categoryId='.$categoryId.',userId='.$h->session->id;
	$q .= ',subject="'.$title.'",body="'.$body.'",timeCreated=NOW(),isPrivate='.$isPrivate;
	$blogId = $db->insert($q);

	//Add entry to moderation queue
	if ($config['blog']['moderation'] && (isSensitive($title) || isSensitive($body))) {
		addToModerationQueue(MODERATION_BLOG, $blogId, true);
	}
	//notify subscribers
	if ($config['subscriptions']['notify']) {
		notifySubscribers(SUBSCRIPTION_BLOG, $h->session->id, $blogId);
	}

	return $blogId;
}

/**
 * Marks a blog as deleted
 *
 * @param $_id blog id
 */
function deleteBlog($_id)
{
	global $h, $db;
	if (!$h->session->id || !is_numeric($_id)) return false;

	$q = 'UPDATE tblBlogs SET timeDeleted=NOW(),deletedBy='.$h->session->id.' WHERE blogId='.$_id;
	$db->update($q);
}

/**
 * XXX
 *
 * @param $_id blog id
 */
function updateBlogReadCount($_id)
{
	global $db;
	if (!is_numeric($_id)) return false;

	$q = 'UPDATE tblBlogs SET readCnt=readCnt+1 WHERE blogId='.$_id.' LIMIT 1';
	$db->update($q);
}

/**
 * XXX
 */
function updateBlog($blogId, $categoryId, $title, $body, $isPrivate = 0)
{
	global $h, $db, $config;
	if (!$h->session->id || !is_numeric($blogId) || !is_numeric($categoryId) || !is_numeric($isPrivate)) return false;

	$title = $db->escape($title);
	$body = $db->escape($body);

	$q = 'UPDATE tblBlogs SET categoryId='.$categoryId.',subject="'.$title.'",body="'.$body.'",timeUpdated=NOW(),isPrivate='.$isPrivate.' WHERE blogId='.$blogId;
	$db->update($q);

	//Add entry to moderation queue
	if ($config['blog']['moderation'] && (isSensitive($title) || isSensitive($body))) {
		addToModerationQueue(MODERATION_BLOG, $blogId, true);
	}
}

/**
 * Sorts results by category for pretty printing
 */
function getBlogsByCategory($userId, $limit = 0)
{
	global $db;
	if (!is_numeric($userId) || !is_numeric($limit)) return false;

	$q  = 'SELECT t1.*,t2.categoryName,t2.categoryPermissions FROM tblBlogs AS t1';
	$q .= ' LEFT JOIN tblCategories AS t2 ON (t1.categoryId=t2.categoryId AND t2.categoryType='.CATEGORY_BLOG.')';
	$q .= ' WHERE t1.userId='.$userId.' AND t1.deletedBy=0';

	//Return order: First blogs categorized in global categories, then blogs categorized in user's categories, then uncategorized blogs
	$q .= ' ORDER BY t2.categoryPermissions DESC, t1.categoryId ASC, t1.timeCreated DESC';
	if ($limit) $q .= ' LIMIT 0,'.$limit;

	return $db->getArray($q);
}

/**
 * Returns the latest blogs posted on the site
 */
function getLatestBlogs($_cnt = 5)
{
	global $db;
	if (!is_numeric($_cnt)) return false;

	$q  = 'SELECT t1.*,t2.userName FROM tblBlogs AS t1';
	$q .= ' INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId)';
	$q .= ' WHERE t1.deletedBy=0';
	$q .= ' ORDER BY t1.timeCreated DESC';
	if ($_cnt) $q .= ' LIMIT '.$_cnt;
	return $db->getArray($q);
}

/**
 * XXX
 */
function getBlog($blogId)
{
	global $db;
	if (!is_numeric($blogId)) return false;

	$q  = 'SELECT t1.*,t2.categoryName,t3.userName FROM tblBlogs AS t1';
	$q .= ' LEFT OUTER JOIN tblCategories AS t2 ON (t1.categoryId=t2.categoryId AND t2.categoryType='.CATEGORY_BLOG.')';
	$q .= ' INNER JOIN tblUsers AS t3 ON (t1.userId=t3.userId)';
	$q .= ' WHERE t1.blogId='.$blogId.' AND t1.deletedBy=0';
	return $db->getOneRow($q);
}

/**
 * Returns blogs for a user
 *
 * @param $_id userid
 */
function getBlogs($_id = 0, $_limit_sql = '')
{
	global $h, $db;
	if (!is_numeric($_id)) return false;

	$q  = 'SELECT * FROM tblBlogs';
	$q .= ' WHERE deletedBy=0';
	if ($_id) $q .= ' AND userId='.$_id;
	if (!$h->session->isAdmin && ($h->session->id != $_id || !isFriends($_id))) {
		$q .= ' AND isPrivate=0';
	}
	$q .= ' ORDER BY timeCreated DESC'.$_limit_sql;
	return $db->getArray($q);
}

/**
 * Returns number of blogs
 *
 * @param $_id userid
 */
function getBlogCount($_id = 0)
{
	global $h, $db;
	if (!is_numeric($_id)) return false;

	$q  = 'SELECT COUNT(blogId) FROM tblBlogs';
	$q .= ' WHERE deletedBy=0';
	if ($_id) $q .= ' AND userId='.$_id;
	if (!$h->session->isAdmin && ($h->session->id != $_id || !isFriends($_id))) {
		$q .= ' AND isPrivate=0';
	}
	return $db->getOneItem($q);
}

/**
 * Get the number of new blog entries written during the specified time period
 */
function getBlogsCountPeriod($dateStart, $dateStop)
{
	global $db;

	$q = 'SELECT count(blogId) AS cnt FROM tblBlogs WHERE timeCreated BETWEEN "'.$dateStart.'" AND "'.$dateStop.'"';
	return $db->getOneItem($q);
}

/**
 * Returns all blogs from $userId for the specified month
 */
function getBlogsByMonth($userId, $month, $year, $order_desc = true)
{
	global $db;
	if (!is_numeric($userId) || !is_numeric($year) || !is_numeric($month) || !is_bool($order_desc)) return false;

	$time_start = mktime(0, 0, 0, $month, 1, $year);			//00:00 at first day of month
	$time_end   = mktime(23, 59, 59, $month+1, 0, $year);	//23:59 at last day of month

	$q  = 'SELECT * FROM tblBlogs';
	$q .= ' WHERE userId='.$userId.' AND deletedBy=0';
	$q .= ' AND timeCreated BETWEEN "'.sql_datetime($time_start).'" AND "'.sql_datetime($time_end).'"';
	if ($order_desc === true) {
		$q .= ' ORDER BY timeCreated DESC';
	} else {
		$q .= ' ORDER BY timeCreated ASC';
	}
	return $db->getArray($q);
}

?>
