<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

//XXX DEPRECATE drop this and use a blog with category "News" instead

require_once('atom_comments.php');		//for news comment support
require_once('atom_categories.php');	//for news categories support
require_once('atom_rating.php');			//for news rating support
require_once('atom_polls.php');				//for support of polls attached to news article
require_once('functions_fileareas.php');	//for showFiles()

$config['news']['allowed_tabs'] = array('News', 'NewsEdit', 'NewsDelete', 'NewsCategories', 'NewsComment', 'NewsFiles', 'NewsPolls');
$config['news']['allow_rating'] = true;	//allow users to rate articles
$config['news']['allow_polls'] = true;	//allow polls to be attached to articles

function addNews($title, $body, $topublish, $rss_enabled, $category_id = 0)
{
	global $h, $db;
	if (!$h->session->id || !is_numeric($rss_enabled) || !is_numeric($category_id)) return false;

	$q = 'SELECT newsId FROM tblNews WHERE title="'.$db->escape($title).'" AND body="'.$db->escape($body).'"';
	if ($db->getOneItem($q)) return false;

	$topublish_time = strtotime($topublish);
	if ($topublish_time < time()) $topublish_time = time();

	$q = 'INSERT INTO tblNews SET title="'.$db->escape($title).'",body="'.$db->escape($body).'",rss_enabled='.$rss_enabled.',creatorId='.$h->session->id.',timeToPublish="'.sql_datetime($topublish_time).'",timeCreated=NOW(),categoryId='.$category_id;
	$db->insert($q);

	return true;
}

function updateNews($newsId, $categoryId, $title, $body, $topublish, $rss_enabled)
{
	global $h, $db;
	if (!$h->session->isAdmin || !is_numeric($newsId) || !is_numeric($categoryId) || !is_numeric($rss_enabled)) return false;

	$topublish = sql_datetime(strtotime($topublish));

	$q = 'UPDATE tblNews SET categoryId='.$categoryId.',title="'.$db->escape($title).'",body="'.$db->escape($body).'",timeToPublish="'.$topublish.'",rss_enabled='.$rss_enabled.',timeEdited=NOW(),editorId='.$h->session->id.' WHERE newsId='.$newsId;
	$db->update($q);
	return true;
}

function removeNews($_id)
{
	global $h, $db;
	if (!$h->session->isAdmin || !is_numeric($_id)) return false;

	$db->update('UPDATE tblNews SET deletedBy='.$h->session->id.',timeDeleted=NOW() WHERE newsId='.$_id);
	return true;
}

function getAllNews()
{
	global $db;

	$q  = 'SELECT t1.*,t2.userName AS creatorName,t3.userName AS editorName FROM tblNews AS t1 ';
	$q .= 'INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
	$q .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t1.editorId=t3.userId) ';
	$q .= 'WHERE t1.deletedBy=0 ';
	$q .= 'ORDER BY timeCreated DESC';
	return $db->getArray($q);
}

/**
 * Used by showNews() and core/rss_news.php
 */
function getPublishedNews($_categoryId = 0, $limit = '')
{
	global $db;
	if (!is_numeric($_categoryId)) return false;

	$q  = 'SELECT t1.*,t2.userName AS creatorName, t3.userName AS editorName FROM tblNews AS t1 ';
	$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
	$q .= 'LEFT JOIN tblUsers AS t3 ON (t1.editorId=t3.userId) ';
	$q .= 'WHERE timeToPublish<NOW() AND deletedBy=0 ';
	if  ($_categoryId) $q .= ' AND categoryId='.$_categoryId.' ';
	$q .= 'ORDER BY timeToPublish DESC';
	if ($limit) $q .= ' LIMIT 0,'.$limit;

	return $db->getArray($q);
}

function getUnpublishedNews($_categoryId = 0)
{
	global $db;
	if (!is_numeric($_categoryId)) return false;

	$q  = 'SELECT t1.*,t2.userName AS creatorName, t3.userName AS editorName FROM tblNews AS t1 ';
	$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
	$q .= 'LEFT JOIN tblUsers AS t3 ON (t1.editorId=t3.userId) ';
	$q .= 'WHERE timeToPublish>NOW() AND deletedBy=0 ';
	if  ($_categoryId) $q .= ' AND categoryId='.$_categoryId.' ';
	$q .= 'ORDER BY timeToPublish DESC';

	return $db->getArray($q);
}

function getNewsItem($newsId)
{
	global $db;
	if (!is_numeric($newsId)) return false;

	$q  = 'SELECT t1.*,t2.userName AS creatorName,t3.userName AS editorName FROM tblNews AS t1 ';
	$q .= 'INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
	$q .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t1.editorId=t3.userId) ';
	$q .= 'WHERE t1.newsId='.$newsId.' AND deletedBy=0';
	return $db->getOneRow($q);
}

/**
 * Returns the latest news items
 */
function getLatestNews()
{
	global $db;

	$q  = 'SELECT t1.*,t2.userName AS creatorName,t3.userName AS editorName FROM tblNews AS t1 ';
	$q .= 'INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
	$q .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t1.editorId=t3.userId) ';
	$q .= 'WHERE deletedBy=0 ORDER BY timeToPublish DESC LIMIT 1';
	$row = $db->getOneRow($q);

	return parseArticle($row['title'], $row['body'], $row['timeToPublish']);
}

function showNewsArticle($_id = 0)
{
	global $h, $db, $config, $project;
	if (!is_numeric($_id)) return false;

	//Looks for formatted news section commands, like: News:ID, NewsEdit:ID, NewsDelete:ID, NewsComment:ID, NewsFiles:ID
	$cmd = fetchSpecialParams($config['news']['allowed_tabs']);
	if ($cmd) list($current_tab, $_id) = $cmd;
	if (empty($_id) || !is_numeric($_id)) return false;

	$news = getNewsItem($_id);
	if (!$news) return false;

	if (!$h->session->isAdmin && !datetime_less($news['timeToPublish'], now())) return false;

	$res = '<div class="news">';
	$res .= '<div class="news_top">';
	if ($news['rss_enabled']) $res .= '<div class="news_title_rss">';
	else $res .= '<div class="news_title">';
	$res .= $news['title'].'</div>';

	if ($news['categoryId']) {
		$res .= '<a href="news.php?cat='.$news['categoryId'].'">'.getCategoryName(CATEGORY_NEWS, $news['categoryId']).'</a><br/>';
	}
	$res .= 'By '.Users::link($news['creatorId'], $news['creatorName']);
	if (datetime_less($news['timeToPublish'], now())) {
		$res .= ', '.t('published').' '.formatTime($news['timeToPublish']).'<br/>';
	} else {
		$res .= ', <b>'.t('will be published').' '.formatTime($news['timeToPublish']).'</b><br/>';
	}

	if ($news['editorId']) $res .= '<i>'.t('Updated').' '.formatTime($news['timeEdited']).' '.t('by').' '.$news['editorName'].'</i><br/>';
	$res .= '</div>'; //class="news_top"
	$res .= '<br/>';

	if ($h->session->isAdmin) {
		$menu = array(
			$_SERVER['PHP_SELF'].'?News:'.$_id => t('Show news'),
			$_SERVER['PHP_SELF'].'?NewsEdit:'.$_id => t('Edit'),
			$_SERVER['PHP_SELF'].'?NewsPolls:'.$_id => t('Polls'),
			$_SERVER['PHP_SELF'].'?NewsFiles:'.$_id => t('Attachments').' ('.$h->files->getFileCount(FILETYPE_NEWS, $_id).')',
			$_SERVER['PHP_SELF'].'?NewsDelete:'.$_id => t('Delete'),
			$_SERVER['PHP_SELF'].'?NewsCategories:'.$_id => t('Categories'),
			$_SERVER['PHP_SELF'].'?NewsComment:'.$_id => t('Comments').' ('.getCommentsCount(COMMENT_NEWS, $_id).')'
			);
	} else {
		$menu = array(
			$_SERVER['PHP_SELF'].'?News:'.$_id => t('Show article'),
			$_SERVER['PHP_SELF'].'?NewsComment:'.$_id => t('Comments').' ('.getCommentsCount(COMMENT_NEWS, $_id).')'
			);
	}

	$res .= createMenu($menu, 'blog_menu');

	if ($current_tab == 'NewsEdit' && $h->session->isAdmin) {

		if (!empty($_POST['news_title'])) {
			updateNews($_id, $_POST['news_cat'], $_POST['news_title'], $_POST['news_body'], $_POST['news_publish'], $_POST['news_rss']);
		}

		$item = getNewsItem($_id);

		$res .= '<h1>'.t('Edit news article').'</h1>';
		$res .= '<form method="post" action="'.'?NewsEdit:'.$_id.'">';
		$res .= '<input type="hidden" name="news_rss" value="0"/>';
		$res .= t('Title').': '.xhtmlInput('news_title', $item['title'], 50).'<br/>';
		$res .= t('Text').':<br/>';
		$res .= xhtmlTextarea('news_body', $item['body'], 60, 16).'<br/>';
		$res .= '<input name="news_rss" id="rss_check" type="checkbox" class="checkbox" value="1"'.($item['rss_enabled']?' checked="checked"':'').'/>';
		$res .= ' <label for="rss_check">';
		$res .= '<img src="'.$config['core']['web_root'].'gfx/icon_rss.png" width="16" height="16" alt="RSS enabled" title="RSS enabled"/> ';
		$res .= t('Include this news in the RSS feed').'</label><br/><br/>';
		$res .= t('Category').': '.xhtmlSelectCategory(CATEGORY_NEWS, 0, 'news_cat', $item['categoryId']).'<br/><br/>';
		$res .= t('Time for publication').': ';
		$res .= xhtmlInput('news_publish', $item['timeToPublish']).'<br/>';
		$res .= xhtmlSubmit('Save changes');
		$res .= '</form>';

	} else if ($current_tab == 'NewsPolls' && $h->session->isAdmin) {
		managePolls(POLL_NEWS, $_id);

	} else if ($current_tab == 'NewsDelete' && $h->session->isAdmin) {

		if (confirmed(t('Are you sure you wish to delete this news entry?'), 'NewsDelete:'.$_id)) {
			removeNews($_id);

			require_once($project.'design_head.php');
			echo t('News article successfully deleted!').'<br/><br/>';
			require_once($project.'design_foot.php');
			die;
		}

	} else if ($current_tab == 'NewsCategories' && $h->session->isAdmin) {

		manageCategoriesDialog(CATEGORY_NEWS);

	} else if ($current_tab == 'NewsFiles' && $h->session->isAdmin) {

		$res .= showFiles(FILETYPE_NEWS, $_id);

	} else if ($current_tab == 'NewsComment') {
		$res .= showComments(COMMENT_NEWS, $_id);
	} else {

		$art = parseArticle($news['title'], $news['body']);
		if ($art['head']) $res .= '<div class="news_head">'.$art['head'].'</div>';
		$res .= '<div class="news_body">'.$art['body'].'</div>';

		if ($config['news']['allow_rating']) {
			$res .= '<br/>';
			$res .= '<div class="news_rate">';
			$res .= ratingGadget(RATE_NEWS, $_id);
			$res .= '</div>';
		}
	}

	$res .= '</div>'; //class="news"

	return $res;
}

/**
 * Shows a list with the latest news headlines for all categories
 */
function showNews($limit = 0)
{
	global $h, $db, $config;

	//Displays one news article - returns if successful
	$res = showNewsArticle();
	if ($res) return $res;

	$_cat_id = 0;
	if (!empty($_GET['cat']) && is_numeric($_GET['cat'])) $_cat_id = $_GET['cat'];

	$list = getPublishedNews($_cat_id, $limit);

	foreach ($list as $row) {
		$res .= showNewsOverview($row);
	}
	if ($h->session->isAdmin) {
		$res .= '<a href="'.$config['core']['web_root'].'admin/admin_news_add.php">'.t('Add news').'</a><br/>';
		$res .= '<a href="'.$config['core']['web_root'].'admin/admin_news.php">'.t('Manage news').'</a><br/>';
	}
	return $res;
}

function showNewsOverview($row)
{
	global $config;

	$res = '<div class="news_item_overview">';

	$res .= '<h1>'.$row['title'].'</h1>';
	$res .= '<div>';
	//$res .= '<div class="news_item_picl">'.Users::linkThumb($row['creatorId'], $row['creatorName']).'</div>';
		$res .= '<a href="?News:'.$row['newsId'].'">'.$row['title'].'</a> '.formatTime($row['timeToPublish']).'<br/>';	//fixme: show optional link title instead
		$art = parseArticle($row['title'], $row['body']);
		$res .= $art['head'];
	$res .= '</div>';
	/*
	$res .= '<a href="?News:'.$row['newsId'].'">'.$row['title'].'</a>, published '.$row['timeToPublish'];
	if ($row['categoryId']) $res .= ' - <a href="news.php?cat='.$row['categoryId'].'">'.getCategoryName(CATEGORY_NEWS, $row['categoryId']).'</a>';
	$art = parseArticle($row['title'], $row['body']);
	$res .= '<br/>'.$art['head'].'<br/>';
	*/
	$res .= '</div><br class="clr"/>';

	if ($config['news']['allow_polls']) {
		//show news polls
		$res .= showAttachedPolls(POLL_NEWS, $row['newsId']);
		$res .= '<br/>';
	}
	return $res;
}
?>
